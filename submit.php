<?php require 'includes/db.php' ?>
<?php require 'includes/functions.php' ?>
<?php

$class_code = $_GET['c'];
$class_id = class_id($class_code);

$asgn_id = asgn_id($_GET['a']);

$user_role = teacher_or_student($class_code);

if ((!isset($_SESSION['user_id'])) || $user_role == 0) {
    header("Location: index.php");
    die();
}

$enrollment_count_query = "SELECT COUNT(*) FROM enrollments WHERE enrollment_class_id = '$class_id'";
$enrollment_count_result = mysqli_query($connection, $enrollment_count_query);
$assigned_count = mysqli_fetch_column($enrollment_count_result);

$turned_in_count_query = "SELECT COUNT(DISTINCT sub_student_id) FROM submissions WHERE sub_asgn_id = '$asgn_id'";
$turned_in_count_result = mysqli_query($connection, $turned_in_count_query);
$turned_in_count = mysqli_fetch_column($turned_in_count_result);

$select_asgn_query = "SELECT * FROM assignments WHERE asgn_id = '$asgn_id'";
$select_asgn_result = mysqli_query($connection, $select_asgn_query);
$asgn = mysqli_fetch_assoc($select_asgn_result);

$asgn_teacher_id = $asgn['asgn_teacher_id'];

$asgn_teacher_query = "SELECT * FROM users WHERE user_id = $asgn_teacher_id";
$asgn_teacher_result = mysqli_query($connection, $asgn_teacher_query);
$asgn_teacher = mysqli_fetch_assoc($asgn_teacher_result);


$asgn_materials_query = "SELECT * FROM materials WHERE material_asgn_id = '{$asgn['asgn_id']}'";
$asgn_materials_result = mysqli_query($connection, $asgn_materials_query);

// assignment materials
$files = "";

if (mysqli_num_rows($asgn_materials_result) > 0) {
    $files = "<br>";
    while ($materials = mysqli_fetch_assoc($asgn_materials_result)) {
        $material_names = $materials['material_file'];
        $file_name = substr($material_names, 11);
        $files .= "<p><a href='./storage/files/$material_names' target='_blank' download>$file_name</a>
        </p>";
    }
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
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="./assets/css/mini.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/submit.css">
    <link rel="stylesheet" href="./assets/css/loader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="header-left">
                <i id="hamburger" class="fas fa-bars icon" title="Menu"></i>
                <h1>Virtual Classroom</h1>
            </div>
            <div class="header-right">
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
                <div class="assignment-detail">
                    <h2><i class='fa-regular fa-file-lines'></i> <?= $asgn['asgn_title'] ?></h2>
                    <p style="margin-bottom:1rem;font-size:0.9rem">
                        <?=
                            $asgn_teacher['user_name'] . " • " . post_date($asgn['asgn_created_at']) .
                            ($asgn['asgn_edited_at'] != NULL ? " (Edited: " . post_date($asgn['asgn_edited_at']) . ")" : "")
                            ?>
                    </p>
                    <div style="display:flex;justify-content: space-between;">
                        <span><?= "Points: " . $asgn['asgn_points'] ?></span>
                        <span>
                            <?=
                                $asgn['asgn_due_date'] == "No due date" ? $asgn['asgn_due_date'] : "Due " . post_date($asgn['asgn_due_date']) . ", 11:59 PM";
                            ?>
                        </span>
                    </div>

                    <?php
                    if ($user_role == 2):
                        $grade_query = "SELECT * FROM grades WHERE grade_asgn_id = '$asgn_id' AND grade_student_id = '{$_SESSION['user_id']}'";
                        $grade_result = mysqli_query($connection, $grade_query);
                        $grade = mysqli_fetch_assoc($grade_result);
                        if (mysqli_num_rows($grade_result) > 0):
                            ?>
                            <div>
                                <span> Grade: <?= $grade['grade_value'] ?></span>
                            </div>
                            <?php
                        endif;
                    endif;
                    ?>

                    <hr>
                    <div class="asgn_desc">
                        <?= $asgn['asgn_description'] ?>
                    </div>
                    <div>
                        <?= $files ?>
                    </div>
                    <?php if ($user_role == 1): ?>
                        <div class="submissions-section">
                            <h3>Class Submissions</h3>
                            <div id="all-submissions">

                                <?php fetch_all_submissions() ?>

                            </div>
                        </div>
                    <?php endif ?>
                    <!-- <div class="comments-section">
                        <h3>Class comments</h3>
                        <a href="#">Add a class comment</a>
                        <textarea placeholder="Add a comment..." id="editor1"></textarea>
                    </div> -->
                </div>
                <div class="right-sidebar">

                    <?php if ($user_role == 1): ?>

                        <div>
                            <div class="sub_info">
                                <div class="sub_info_header">
                                    <div class="count">
                                        <div class="number"><?= $turned_in_count ?></div>
                                        <div>Turned in</div>
                                    </div>
                                    <hr class="v-div">
                                    <div class="count">
                                        <div class="number"><?= $assigned_count ?></div>
                                        <div>Assigned</div>
                                    </div>
                                </div>
                                <div class="sub_info_footer">

                                    <?php asgn_accept_status(); ?>

                                </div>
                            </div>
                        </div>

                    <?php else: ?>

                        <div id="submission-box" class="submission-box">
                            <?php fetch_my_submissions() ?>
                        </div>

                    <?php endif ?>

                    <!-- <div class="private-comments">
                        <h3>Private comments</h3>
                        <a href="#">Add comment to Rahul Mahato</a>
                        <textarea id="editor2"></textarea>
                    </div> -->
                </div>
            </main>
        </div>
    </div>

    <!-- Include the Quill library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="<?= APP_URL ?>/assets/js/mini.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>

</body>

</html>

<script>

    var classCode = <?= json_encode($class_code) ?>;
    var asgnId = <?= json_encode($asgn_id) ?>;

    fetchEnrolledClasses();
    fetchTeachingClasses();

    setupFileInput('submit-file', 'fileList-1', 'fileCount1');


    const clearSubForm = function () {

        // Clear file input
        $('#submit-file').val('');
        $('#fileList-1').empty();  // Clear file list

        // Update file count displays
        $('#fileCount1').text('');
    }

    $(document).on('submit', '#asgn-submit-form', function (e) {
        e.preventDefault(); // Prevent the default form submission

        let asgnFormData = new FormData(this);

        asgnFormData.append('submit_asgn', 1)
        asgnFormData.append('classCode', classCode);
        asgnFormData.append('asgn_id', asgnId);

        $.ajax({
            url: 'controllers/submit',
            type: 'POST',
            data: asgnFormData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.error == 0) {
                    $("#submission-box").load(location.href + " #submission-box");
                    notifier.success(res.message)
                } else {
                    notifier.warning(res.message)
                }
            },
        });

    });

    function unSubmit(callback) {
        notifier.confirm(
            "Your submissions will be deleted",
            () => {
                callback(true);
            },
            () => {
                callback(false);
            },
            {
                labels: {
                    confirm: "Unsubmit?",
                    confirmOk: "OK",
                    confirmCancel: "Cancel",
                },
            }
        );
    }

    $(document).on('submit', '#asgn-unsubmit-form', function (e) {
        e.preventDefault(); // Prevent the default form submission

        unSubmit(function (status) {
            console.log(status); // This logs the user's response (true or false)
            if (status) {
                $.ajax({
                    url: 'controllers/submit',
                    type: 'POST',
                    data: {
                        unsubmit_asgn: 1,
                        classCode: classCode,
                        asgn_id: asgnId
                    },
                    success: function (response) {
                        const res = jQuery.parseJSON(response);
                        if (res.error == 0) {
                            $("#submission-box").load(location.href + " #submission-box", function () {
                                notifier.success(res.message);
                                // Re-initialize the file input setup after the content is loaded
                                setupFileInput('submit-file', 'fileList-1', 'fileCount1');
                            });
                        } else {
                            notifier.warning(res.message);
                        }
                    },
                });
            }
        });
    });

    // Event Delegation: By using $(document).on('click', '.label', function (e) {...});, the event listener is attached to the document and will handle clicks on .label elements, even if they are dynamically loaded.

    $(document).on('click', '.label', function (e) {
        e.preventDefault();

        var $this = $(this);
        $('.text').text("saving...");
        $('.label').addClass('inactive');
        $('.toggle-hider').show();

        if ($this.hasClass("plus")) {
            setTimeout(() => {
                $.ajax({
                    url: "controllers/submit",
                    type: "POST",
                    data: {
                        asgn_not_accept: 1,
                        asgn_id: asgnId
                    },
                    success: function (response) {
                        res = jQuery.parseJSON(response);
                        if (res.error == 0) {
                            $(".sub_info_footer").load(location.href + " .sub_info_footer");
                        }
                    },
                });
            }, 1000);
        } else if ($(this).hasClass("minus")) {
            setTimeout(() => {
                $.ajax({
                    url: "controllers/submit",
                    type: "POST",
                    data: {
                        asgn_accept: 1,
                        asgn_id: asgnId
                    },
                    success: function (response) {
                        res = jQuery.parseJSON(response);
                        if (res.error == 0) {
                            $(".sub_info_footer").load(location.href + " .sub_info_footer");
                        }
                    },
                });
            }, 1000);
        }
    });


    $(document).on('submit', '.give-grade-form', function (event) {
        event.preventDefault(); // Prevent the form from actually submitting

        let stuDiv = $(this).closest('div[stu-id]');
        let stuId = stuDiv.length ? stuDiv.attr('stu-id') : 'No stu-id found';

        let gradeValue = $(this).find('input[name="grade"]').val();

        console.log('stu-id:', stuId);
        console.log('Input value:', gradeValue);


        if (gradeValue == "") {
            notifier.warning("Enter a grade");
        } else {
            $.ajax({
                url: "controllers/submit",
                type: "POST",
                data: {
                    give_grade: 1,
                    classCode: classCode,
                    asgn_id: asgnId,
                    stu_id: stuId,
                    grade_value: gradeValue
                },
                success: function (response) {
                    res = jQuery.parseJSON(response);
                    if (res.error == 0) {
                        notifier.success(res.message);
                        $("#all-submissions").load(location.href + " #all-submissions")
                    } else {
                        notifier.warning(res.message);
                    }
                },
            });
        }
    });

</script>