<?php require 'includes/db.php' ?>

<?php

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    } else {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        // code tampering into url
        if (!isset($token['access_token']))
            header('Location: ' . APP_URL);
        $client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];
    }
    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $userinfo = [
        'email' => $google_account_info['email'],
        'first_name' => $google_account_info['givenName'],
        'last_name' => $google_account_info['familyName'],
        'gender' => $google_account_info['gender'],
        'full_name' => $google_account_info['name'],
        'picture' => $google_account_info['picture'],
        'verifiedEmail' => $google_account_info['verifiedEmail'],
        'token' => $google_account_info['id'],
    ];

    // checking if user is already exists in database
    $sql = "SELECT * FROM users WHERE user_email ='{$userinfo['email']}'";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        // user exists
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];
    } else {
        // user does not exist
        $name = $userinfo['first_name'] . ' ' . $userinfo['last_name'];
        $sql = "INSERT INTO users (user_name, user_email, user_image_url, user_token) VALUES ('{$name}', '{$userinfo['email']}',  '{$userinfo['picture']}', '{$userinfo['token']}')";
        $result = mysqli_query($connection, $sql);

        $fetch_sql = "SELECT * FROM users WHERE user_email ='{$userinfo['email']}'";
        $fetch_result = mysqli_query($connection, $fetch_sql);
        $row = mysqli_fetch_assoc($fetch_result);

        if ($result) {
            $user_id = $row['user_id'];
        } else {
            echo "User is not created";
            die();
        }
    }

    $_SESSION['user_id'] = $user_id;

} else {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        die();
    }
    // checking if user is already exists in database
    // $sql = "SELECT * FROM users WHERE user_token ='{$_SESSION['user_token']}'";
    // $result = mysqli_query($connection, $sql);
    // if (mysqli_num_rows($result) > 0) {
    //     // user exists
    //     $userinfo = mysqli_fetch_assoc($result);
    //     $_SESSION['user_token'] = $userinfo['user_token'];
    // }

}

?>

<?php
$sql = "SELECT * FROM users where user_id = '{$_SESSION['user_id']}'";
$result = mysqli_query($connection, $sql);
$row = mysqli_fetch_assoc($result);
$user_email = $row["user_email"];
if ($row["user_image_url"] == NULL)
    $user_image = APP_URL . "storage/profile/defaultAvatar.jpg";
else
    $user_image = $row["user_image_url"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Classroom</title>
    <link rel="shortcut icon" href="<?= APP_URL . 'assets/images/favicon.jpg' ?>" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/mini.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="header-left">
                <i id="hamburger" class="fas fa-bars icon" title="Menu"></i>
                <h1><a href="home">Virtual Classroom</a></h1>
            </div>
            <div class="header-right">
                <!-- <i class="fas fa-magnifying-glass icon" title="Search"></i> -->
                <div class="dropdown">
                    <i id="plusIcon" class="fas fa-plus icon" title="Create or Join Class"></i>
                    <div id="dropdownContent" class="dropdown-content">
                        <a href="#" id="createClass">Create Class</a>
                        <a href="#" id="joinClass">Join Class</a>
                    </div>
                </div>

                <i id="themeToggle" class="fas fa-moon icon" title="Toggle Theme"></i>
                <div class="profile-icon" title=" <?= $user_email ?> ">
                    <img src="<?= $user_image ?>" alt="Profile" />
                </div>
                <i id="logout" title="Logout" class="fa-solid fa-power-off logout"></i>
            </div>
        </header>
        <div class="content">

            <?php require ("./includes/sidebar.php") ?>

            <main>
                <div id="classes" class="card-grid">

                </div>
            </main>
        </div>
    </div>

    <div id="joinClassModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Join Class</h2>
            <form id="joinClass-form">
                <div id="join-response" style="color:red;display:none;"></div>
                <label for="classCode">Class Code:</label>
                <input type="text" id="classCode" minlength="6" maxlength="6" required>
                <button type="submit">Join</button>
            </form>
        </div>
    </div>

    <div id="createClassModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Create Class</h2>
            <form id="createClass-form">
                <label for="className">Class Name:</label>
                <input type="text" id="className" required>
                <label for="classSection">Section:</label>
                <input type="text" id="classSection" required>
                <label for="classSubject">Subject:</label>
                <input type="text" id="classSubject" required>
                <button type="submit">Create</button>
            </form>
        </div>
    </div>

    <div id="classCodeModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div id="class-code">Class Code</div>
            <div class="copy-text">Code copied!</div>
        </div>
    </div>

    <script src="<?= APP_URL ?>/assets/js/mini.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>

</html>

<script>

    fetchEnrolledClasses(); // declared in app.js
    fetchTeachingClasses(); // declared in app.js

    function emptyCreateClassEntry() {
        $("#className").val("");
        $("#classSection").val("");
        $("#classSubject").val("");
    }

    function viewCode(elem) {
        const card = elem.closest('.card');
        const dataCode = card.getAttribute('data-code');
        console.log('Data code:', dataCode);
        $("#classCodeModal").show();
        $("#class-code").html(`<p>Code to join this class is <b>${dataCode}</b><i onclick="copyCode('${dataCode}')" class="fa-regular fa-copy copy-code"></i></p>`);
    }

    function copyCode(dataCode) {
        // Create a temporary input element
        const copyText = document.createElement('input');
        copyText.value = dataCode;
        document.body.appendChild(copyText);

        // Select the text and copy it
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        navigator.clipboard.writeText(copyText.value).then(() => {
            console.log('Code copied to clipboard:', dataCode);
            $('.copy-text').show();
            setTimeout(function () {
                $('.copy-text').hide();
            }, 2000); // 3000 milliseconds = 3 seconds
        }).catch(err => {
            console.error('Failed to copy code:', err);
        });

        // Remove the temporary input element
        document.body.removeChild(copyText);
    }


    function deleteClass(elem) {
        const card = elem.closest('.card');
        const dataId = card.getAttribute('data-id');
        console.log('Data ID:', dataId);
        $.ajax({
            type: "POST",
            url: "controllers/home",
            data: {
                delete_class: 1,
                classId: dataId
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.delete == 1) {
                    show_all_class();
                    fetchTeachingClasses();
                    fetchEnrolledClasses();
                    notifier.success(res.message)
                }
            }
        });
    }

    function show_all_class() {
        $.ajax({
            type: "POST",
            url: "controllers/home",
            data: {
                show_all_class: 1,
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                const message = res.message;
                if (res.status == 200) {
                    $("#classes").html(message);
                }

                const viewCodeBtn = document.querySelectorAll(".viewCode");
                viewCodeBtn.forEach(viewCodeElem => {
                    $(viewCodeElem).click(function (e) {
                        e.preventDefault();
                        viewCode(viewCodeElem);
                    });
                });

                const deleteClassBtn = document.querySelectorAll(".deleteClass");
                deleteClassBtn.forEach(deleteClassElem => {
                    $(deleteClassElem).click(function (e) {
                        e.preventDefault();
                        if (confirm("Do you want to delete this class?")) {
                            deleteClass(deleteClassElem);
                        }
                    });
                });

            }
        });
    }
    show_all_class();

    $("#createClass-form").submit(function (e) {
        e.preventDefault();
        const className = $("#className").val();
        const classSection = $("#classSection").val();
        const classSubject = $("#classSubject").val();

        $.ajax({
            type: "POST",
            url: "controllers/home",
            data: {
                create_class: 1,
                className: className,
                classSection: classSection,
                classSubject: classSubject
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                const message = res.message;
                if (res.status == 200) {
                    $("#createClassModal").hide();
                    show_all_class();
                    fetchTeachingClasses();
                    TeachingClassDropdownClose();
                    emptyCreateClassEntry();
                    $("#classCodeModal").show();
                    $("#class-code").html(message);
                }
            }
        });
    });

    $("#joinClass-form").submit(function (e) {
        e.preventDefault();
        const classCode = $("#classCode").val();

        $.ajax({
            type: "POST",
            url: "controllers/home",
            data: {
                join_class: 1,
                classCode: classCode
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                const message = res.message;
                if (res.join == 1) {
                    console.log(res.message);
                    show_all_class();
                    fetchEnrolledClasses();
                    // TeachingClassDropdownClose();
                    $("#joinClassModal").hide();
                    $("#classCode").val("");
                    $("#join-response").text("").hide();
                }
                else {
                    console.log(res.message);
                    $("#join-response").text(message).show();
                }
            }
        });
    });


</script>