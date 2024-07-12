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
    <link rel="stylesheet" href="./assets/css/loader.css">
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
                <div class="profile-icon" title=" <?= $user_email ?> ">
                    <img src="<?= $user_image ?>" alt="Profile" />
                </div>
                <i id="logout" title="Logout" class="fa-solid fa-power-off logout"></i>
            </div>
        </header>

        <div class="loader-container">
            <div class="loader-bar bar1"></div>
            <div class="loader-bar bar2"></div>
            <div class="loader-bar bar3"></div>
        </div>

        <div class="content">

            <?php require ("./includes/sidebar.php") ?>

            <div>
                <ul class="tabs">
                    <li id="stream-btn" style="border-bottom-left-radius: 5px;" class="tab active"
                        onclick="activateTab(event, 'stream')">Stream</li>
                    <li id="classwork-btn" class="tab" onclick="activateTab(event, 'classwork')">Classwork</li>
                    <li id="people-btn" style="border-bottom-right-radius: 5px;" class="tab"
                        onclick="activateTab(event, 'people')">People</li>
                </ul>
            </div>

            <main>

                <div id="stream" class="tab-content active">
                    <div class="announcement">
                        <div class="profile-icon" title=" <?= $user_email ?> ">
                            <img src="<?= $user_image ?>" alt="Profile" />
                        </div>
                        <form id="announcement-form" enctype="multipart/form-data">
                            <div style="display:flex;gap:1rem;flex-direction:column;">
                                <div id="classEditor1"></div>
                                <div style="display:flex">
                                    <div style="width:15rem">
                                        <label style="display: inline-block; cursor:pointer" for="add-file"
                                            class="btn-add-create">
                                            <i class="fa-solid fa-paperclip"></i>
                                            <span>Add file</span>
                                        </label>
                                        <span id="fileCount1" class="file-count">0 files</span>
                                    </div>
                                    <div>
                                        <input id="add-file" style="display:none;" type="file" name="files[]" multiple>
                                    </div>
                                    <button type="submit"><i class="fa-regular fa-paper-plane"></i></i></button>
                                </div>
                                <ul id="fileList-1" class="file-list"></ul>
                            </div>
                        </form>
                    </div>
                    <div class="post-list">
                        <!-- Posts -->

                    </div>
                </div>

                <div id="classwork" class="tab-content">
                    <div style="max-width: 700px;margin:0 auto;">

                        <?php if ($user_role == 1): ?>
                            <div class='dropdown'>
                                <div style='margin-bottom: 1rem;' id='create-classwork' class='btn-rounded'>+ Create</div>
                                <div style='left:0' id='create-classwork-dropdown' class='dropdown-content'>
                                    <a href='#' class='open-cw-modal' id='asgn'><i class='fa-regular fa-file-lines'></i>
                                        Assignment</a>
                                    <!-- <a href='#' class='open-cw-modal' id='quiz-asgn'><i
                                            class='fa-regular fa-file-lines'></i> Quiz Assignment</a>
                                    <a href='#' class='open-cw-modal' id='material'><i class='fa-solid fa-book'></i>
                                        Material</a> -->
                                </div>
                            </div>
                        <?php endif ?>

                        <div class="cw-list">

                        </div>

                    </div>
                </div>

                <div id="people" class="tab-content">
                    <div class="container-teacher">
                        <h2>Teacher</h2>
                        <div>
                            <ul id="all-teachers">

                            </ul>
                        </div>
                    </div>
                    <div>
                        <h2>Students</h2>
                        <div>
                            <ul id="all-students">

                            </ul>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <div id="editAnModal" class="modal">
        <div style="max-width:800px" class="modal-content">
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

    <div id="cw-modal">
        <form id="cw-form" enctype="multipart/form-data">
            <nav class="cw-nav">
                <div class="cw-nav-left">
                    <i class="fa-solid fa-xmark close-cw-modal"></i>
                    <i class="fa-regular fa-file-lines"></i>
                    <span>Assignment</span>
                </div>
                <div class="cw-nav-right">
                    <button class="btn-rounded" type="submit">Assign</button>
                </div>
            </nav>
            <main class="cw-main">
                <div class="form-div">
                    <div class="form-group">
                        <label for="cw-title">Title:</label>
                        <input id="cw-title" name="cw_title" type="text">
                    </div>
                    <div class="form-group">
                        <label for="cw-editor">Description:</label>
                        <div id="cw-editor"></div>
                    </div>
                    <div class="form-group">
                        <label style="display: inline-block;">
                            <span>Attachments:</span>
                            <span id="fileCount3" class="file-count"></span>
                        </label>
                        <input type="file" id="cw-file" name="cwFiles[]" multiple>
                        <ul id="fileList-3" class="file-list"></ul>
                    </div>
                    <div class="form-sub-group">
                        <div class="form-group">
                            <label for="cw-points">Points:</label>
                            <input type="number" id="cw-points" placeholder="ungraded" min="0">
                        </div>
                        <div class="form-group">
                            <label for="due-date">Due:</label>
                            <div class="due-date-dropdown">
                                <div class="dropdown-toggle" id="due-date-toggle">No due date</div>
                                <div class="dropdown-menu" id="due-date-menu">
                                    <div class="dropdown-item" id="no-due-date">No due date</div>
                                    <input type="date" id="due-date-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </form>
    </div>

    <!-- Include the Quill library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="<?= APP_URL ?>/assets/js/mini.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>

</html>

<script>

    var classCode = <?= json_encode($class_code) ?>;

    const dueDateToggle = document.getElementById('due-date-toggle');
    const dueDateMenu = document.getElementById('due-date-menu');
    const noDueDateItem = document.getElementById('no-due-date');
    const dueDateInput = document.getElementById('due-date-input');

    dueDateToggle.addEventListener('click', function () {
        dueDateMenu.style.display = dueDateMenu.style.display === 'none' || dueDateMenu.style.display === '' ? 'block' : 'none';
    });

    noDueDateItem.addEventListener('click', function () {
        dueDateToggle.textContent = 'No due date';
        dueDateMenu.style.display = 'none';
    });

    dueDateInput.addEventListener('change', function () {
        if (dueDateInput.value) {
            dueDateToggle.textContent = dueDateInput.value;
        }
        dueDateMenu.style.display = 'none';
    });

    // Hide dropdown if clicked outside
    document.addEventListener('click', function (event) {
        if (!dueDateToggle.contains(event.target) && !dueDateMenu.contains(event.target)) {
            dueDateMenu.style.display = 'none';
        }
    });

    // Get today's date in YYYY-MM-DD format
    var today = new Date().toISOString().split('T')[0];

    // Set the min attribute to today's date
    document.getElementById('due-date-input').setAttribute('min', today);


    fetchEnrolledClasses(); // declared in app.js
    fetchTeachingClasses(); // declared in app.js

    // class tab switches

    function activateTab(event, tabId) {

        const tabs = document.querySelectorAll(".tab");
        const contents = document.querySelectorAll(".tab-content");

        // Remove 'active' class from all tabs and contents
        tabs.forEach((tab) => {
            tab.classList.remove("active");
        });
        contents.forEach((content) => {
            content.classList.remove("active");
        });

        // Set timeout to switch tabs after 3 seconds
        // setTimeout(() => {
        // Add 'active' class to the clicked tab and corresponding content
        event.target.classList.add("active");
        document.getElementById(tabId).classList.add("active");

        // }, 2000); // 2000 milliseconds = 2 seconds
    }

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
        // placeholder: "Announce something to your class",
        theme: "snow", // or 'bubble'
    });

    const quill3 = new Quill("#cw-editor", {
        modules: {
            toolbar: [
                ["bold", "italic", "underline"],
                [{ 'list': 'ordered' }, { 'list': "bullet" }], // Specify the list type as 'bullet' for <ul>
                ['link'],
            ],
        },
        placeholder: "Instructions (optional)",
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

        // Remove any previously attached event listener before attaching a new one
        // Prevent multiple AJAX requests to be sent on subsequent form submissions by using off
        $('#editAnnouncement-form').off('submit').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = new FormData(this);
            var editorContent = quill2.getSemanticHTML(); // Assuming quill2 is your editor instance

            formData.append('edit_announcement', 1);
            formData.append('an_id', postId);
            formData.append('classCode', classCode);
            formData.append('editorContent', editorContent);

            $.ajax({
                url: 'controllers/class',
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
            url: "controllers/class",
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
        $(".loader-container").show();
        $.ajax({
            type: "POST",
            url: "controllers/class",
            data: {
                'fetch_announcements': 1,
                'classCode': classCode,
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.status == 1) {
                    // console.log(res.message)
                    $(".loader-container").hide();
                    $(".post-list").html(res.message);

                    const editAnBtn = document.querySelectorAll(".editAn");
                    editAnBtn.forEach((editAnElem) => {
                        $(editAnElem).click(function (e) {
                            e.preventDefault();
                            editAn(editAnElem);
                        });
                    });

                    const deleteAnBtn = document.querySelectorAll(".deleteAn");
                    deleteAnBtn.forEach((deleteAnElem) => {
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

    const clearAnForm = function () {

        // Clear Quill editor content
        quill1.setText('');  // Clear Quill editor content

        // Clear file input
        $('#add-file').val('');
        $('#fileList-1').empty();  // Clear file list

        // Update file count displays
        $('#fileCount1').text('');
    }

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
            url: 'controllers/class',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.error == 0) {
                    clearAnForm()
                    fetch_announcements()
                    notifier.success(res.message)
                } else {
                    notifier.warning(res.message)
                }
            },
        });
    });

    var peopleClicked = false;
    $("#people-btn").click(function (e) {
        e.preventDefault();
        if (!peopleClicked) {
            $(".loader-container").show();
            peopleClicked = true;

            $.ajax({
                type: "POST",
                url: "controllers/class",
                data: {
                    'fetch_people': 1,
                    'classCode': classCode
                },
                success: function (response) {
                    const res = jQuery.parseJSON(response);
                    setTimeout(() => {
                        $(".loader-container").hide();
                        $("#all-teachers").html(res.teacher);
                        $("#all-students").html(res.student);
                    }, 2000);
                }
            });
        }
    });

    var classworkClicked = false;
    $("#classwork-btn").click(function (e) {
        e.preventDefault();
        if (!classworkClicked) {
            $(".loader-container").show();
            classworkClicked = true;

            setTimeout(() => {
                $(".loader-container").hide();
                fetch_classworks()
            }, 2000);
        }
    });

    $(".open-cw-modal").click(function (e) {
        e.preventDefault();
        $("#cw-modal").show();
    });

    $(".close-cw-modal").click(function (e) {
        e.preventDefault();
        $("#cw-modal").hide();
        clearCwForm();
    });

    // Setup file inputs
    setupFileInput('add-file', 'fileList-1', 'fileCount1');
    setupFileInput('cw-file', 'fileList-3', 'fileCount3');

    const clearCwForm = function () {
        // Clear text input
        $('#cw-title').val('');

        // Clear Quill editor content
        quill3.setText('');  // Clear Quill editor content

        // Clear file input
        $('#cw-file').val('');
        $('#fileList-3').empty();  // Clear file list

        // Clear number input
        $('#cw-points').val('');

        // Clear date input
        $("#due-date-toggle").text("No Due Date");

        // Update file count displays
        $('#fileCount3').text('');
    }

    $('#cw-form').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        let cwType = "assignment";

        let cwformData = new FormData(this);
        let cwContent = quill3.getSemanticHTML(); // ol and ul error solution

        let cwDueDate = $("#due-date-toggle").text();
        let cwPoints = $("#cw-points").val();
        if (cwPoints == "") cwPoints = "ungraded"

        cwformData.append('create_cw', 1)
        cwformData.append('classCode', classCode);
        cwformData.append('cw_DueDate', cwDueDate);
        cwformData.append('cw_type', cwType);
        cwformData.append('cw_points', cwPoints);
        cwformData.append('cw_Content', cwContent);

        // console.log(formData);

        $.ajax({
            url: 'controllers/class',
            type: 'POST',
            data: cwformData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.error == 0) {
                    clearCwForm()
                    $("#cw-modal").hide();
                    // fetch_announcements()
                    fetch_classworks();
                    notifier.success(res.message)
                } else {
                    notifier.warning(res.message)
                }
            },
        });

    });


    function confirmDeleteCw(callback) {
        notifier.confirm(
            'All submissions will also be deleted',
            () => {
                callback(true);
            },
            () => {
                callback(false);
            },
            {
                labels: {
                    confirm: 'Delete classwork?',
                    confirmOk: 'OK',
                    confirmCancel: 'Cancel'
                }
            }
        );
    }

    function deleteCw(elem) {
        const card = elem.closest('.cw');
        const cwType = card.getAttribute('cw-type');
        const cwId = card.getAttribute('cw-id');
        // console.log('Cw Type:', cwType);
        // console.log('Cw ID:', cwId);
        $.ajax({
            type: "POST",
            url: "controllers/class",
            data: {
                delete_cw: 1,
                cw_type: cwType,
                cw_id: cwId
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.status == 1) {
                    fetch_classworks();
                    notifier.success(res.message)
                } else {
                    notifier.warning(res.message)
                }
            }
        });
    }


    function fetch_classworks() {
        $(".loader-container").show();
        $.ajax({
            type: "POST",
            url: "controllers/class",
            data: {
                'fetch_cw': 1,
                'classCode': classCode,
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                if (res.status == 1) {
                    // console.log(res.message)
                    $(".loader-container").hide();
                    $(".cw-list").html(res.message);

                    // const editCwBtn = document.querySelectorAll(".editAn");
                    // editAnBtn.forEach((editAnElem) => {
                    //     $(editAnElem).click(function (e) {
                    //         e.preventDefault();
                    //         editAn(editAnElem);
                    //     });
                    // });

                    const deleteCwBtn = document.querySelectorAll(".deleteCw");
                    deleteCwBtn.forEach((deleteCwElem) => {
                        $(deleteCwElem).click(function (e) {
                            e.preventDefault();

                            confirmDeleteCw(function (status) {
                                console.log(status); // This logs the user's response (true or false)
                                if (status) {
                                    // console.log("Deleting announcement...");
                                    deleteCw(deleteCwElem);
                                }
                            });
                        });
                    });
                }
            }
        });
    }


</script>