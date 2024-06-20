<?php require 'includes/db.php' ?>
<?php require 'includes/functions.php' ?>
<?php

$class_code = $_GET['c'];
$user_role = teacher_or_student($class_code);

if ((!isset($_SESSION['user_id'])) || $user_role == 0) {
    header("Location: index.php");
    die();
}

?>

<?php

$select_class_query = "SELECT * FROM classes WHERE md5(class_code) = '$class_code'";
$select_class_result = mysqli_query($connection, $select_class_query);
while ($class = mysqli_fetch_assoc($select_class_result)) {
    $class_name = $class["class_name"];
    $class_section = $class["class_section"];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="header-left">
                <i id="hamburger" class="fas fa-bars icon" title="Menu"></i>
                <h1><a href="home.php">Virtual Classroom</a></h1>
                <i class="fa-solid fa-angle-right"></i>
                <div>
                    <div><?= $class_name ?></div>
                    <div style="font-size:12px;margin-top:3px"><?= $class_section ?></div>
                </div>
            </div>
            <div class="header-right">
                <i id="themeToggle" class="fas fa-moon icon" title="Toggle Theme"></i>
                <i class="fas fa-magnifying-glass icon" title="Search"></i>
                <div class="profile-icon" title=" <?= $user_email ?> ">
                    <img src="<?= $user_image ?>" alt="Profile" />
                </div>
                <i id="logout" title="Logout" class="fa-solid fa-power-off logout"></i>
            </div>
        </header>
        <div class="content">

            <?php require ("./includes/sidebar.php") ?>

            <main>
                <div class="announcement">
                    <div class="profile-icon" title=" <?= $user_email ?> ">
                        <img src="<?= $user_image ?>" alt="Profile" />
                    </div>
                    <form id="announcement-form" enctype="multipart/form-data">
                        <div style="display:flex;gap:1rem;flex-direction:column;">
                            <div id="classEditor1"></div>
                            <div style="display:flex">
                                <div style="width:6rem">
                                    <label for="add-file" class="btn-add-create"><i class="fa-solid fa-paperclip"></i>
                                        Add
                                        file
                                    </label>
                                </div>
                                <div>
                                    <input id="add-file" style="display:none;" type="file" name="files[]" multiple>
                                </div>
                                <button type="submit"><i class="fa-regular fa-paper-plane"></i></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="post-list">
                    <!-- Posts -->

                </div>
            </main>
        </div>
    </div>

    <div id="editAnModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit announcement</h2>
            <form id="editAnnouncement-form" enctype="multipart/form-data">
                <div style="display:flex;gap:1rem;flex-direction:column;">
                    <div id="classEditor2"></div>
                    <div style="display:flex;justify-content:space-between;">
                        <div style="width:6rem">
                            <label for="update-file" class="btn-add-create"><i class="fa-solid fa-paperclip"></i>
                                Add
                                file
                            </label>
                        </div>
                        <div>
                            <input id="update-file" style="display:none;" type="file" name="updateFiles[]" multiple>
                        </div>
                        <button type="submit"><i class="fa-regular fa-paper-plane"></i></i></button>
                    </div>
                </div>
            </form>
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

    fetchEnrolledClasses(); // declared in app.js
    fetchTeachingClasses(); // declared in app.js

    const quill1 = new Quill("#classEditor1", {
        modules: {
            toolbar: [
                ["bold", "italic", "underline"],
                [{ 'list': 'ordered' }, { list: "bullet" }], // Specify the list type as 'bullet' for <ul>
            ],
        },
        placeholder: "Announce something to your class",
        theme: "snow", // or 'bubble'
    });

    const quill2 = new Quill("#classEditor2", {
        modules: {
            toolbar: [
                ["bold", "italic", "underline"],
                [{ 'list': 'ordered' }, { 'list': "bullet" }], // Specify the list type as 'bullet' for <ul>
            ],
        },
        placeholder: "Announce something to your class",
        theme: "snow", // or 'bubble'
    });

    function confirmAction(callback) {
        notifier.confirm(
            'Comments will also be deleted',
            () => {
                callback(true);
            },
            () => {
                callback(false);
            },
            {
                labels: {
                    confirm: 'Delete announcement?',
                    confirmOk: 'OK',
                    confirmCancel: 'Cancel'
                }
            }
        );
    }

    function editAn(elem) {
        // Assuming `elem` is the clicked element, wrapped in a jQuery object
        const post = $(elem).closest('.post');
        const postId = post.attr('post-id');
        const editContent = post.find('.an-text').html();

        quill2.clipboard.dangerouslyPasteHTML(editContent);
        $('#editAnModal').show();

        console.log('Edit Content:', editContent);
        console.log('Post Id:', postId);

        $('#editAnnouncement-form').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = new FormData(this);
            var editorContent = quill2.getSemanticHTML(); // Assuming quill2 is your editor instance

            formData.append('edit_announcement', 1);
            formData.append('an_id', postId);
            formData.append('classCode', classCode);
            formData.append('editorContent', editorContent);

            $.ajax({
                url: 'controllers/class.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    const res = jQuery.parseJSON(response);
                    if (res.error == 0) {
                        $('.ql-editor').html('');
                        fetch_announcements();
                        $('#editAnModal').hide();
                        notifier.success(res.message);
                    } else {
                        notifier.warning(res.message);
                        // Reset file input (if needed)
                        $('#update-file').val('');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    notifier.error('Failed to update announcement.');
                }
            });
        });
    }

    function deleteAn(elem) {
        const card = elem.closest('.post');
        const postId = card.getAttribute('post-id');
        console.log('Post ID:', postId);
        $.ajax({
            type: "POST",
            url: "controllers/class.php",
            data: {
                delete_announcement: 1,
                an_id: postId
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.status == 1) {
                    fetch_announcements();
                    notifier.success(res.message)
                } else {
                    notifier.warning(res.message)
                }
            }
        });
    }

    function fetch_announcements() {
        $.ajax({
            type: "POST",
            url: "controllers/class.php",
            data: {
                'fetch_announcements': 1,
                'classCode': classCode,
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.status == 1) {
                    // console.log(res.message)
                    $(".post-list").html(res.message);
                }

                const editAnBtn = document.querySelectorAll(".editAn");
                editAnBtn.forEach(editAnElem => {
                    $(editAnElem).click(function (e) {
                        e.preventDefault();
                        editAn(editAnElem);
                    });
                });

                const deleteAnBtn = document.querySelectorAll(".deleteAn");
                deleteAnBtn.forEach(deleteAnElem => {
                    $(deleteAnElem).click(function (e) {
                        e.preventDefault();

                        confirmAction(function (status) {
                            console.log(status); // This logs the user's response (true or false)
                            if (status) {
                                // console.log("Deleting announcement...");
                                deleteAn(deleteAnElem);
                            }
                        });
                    });
                });

            }
        });
    }

    fetch_announcements();

    // Listen for input events on the classEditor1 div
    $('#classEditor1').on('keydown', function () {
        // checkEditorContent();
        var editorContent = quill1.getSemanticHTML()
        // console.log(editorContent)
    });


    $('#announcement-form').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this);

        var editorContent = quill1.getSemanticHTML() // ol and ul error solution

        // console.log(editorContent);
        formData.append('make_announcement', 1)
        formData.append('classCode', classCode);
        formData.append('editorContent', editorContent);

        // console.log(formData);

        $.ajax({
            url: 'controllers/class.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.error == 0) {
                    $('.ql-editor').html("")
                    fetch_announcements()
                    notifier.success(res.message)
                } else {
                    notifier.warning(res.message)
                    document.getElementById('#add-file').value = '';
                    document.getElementById('.btn-add-create').value = '';
                }
            },
        });
    });


</script>