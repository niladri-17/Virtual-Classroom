<?php require 'includes/db.php' ?>
<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die();
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
    <link rel="stylesheet" href="<?= APP_URL ?>assets/css/submit.css">
    <link rel="stylesheet" href="<?= APP_URL ?>assets/css/style.css">
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
                <i class="fas fa-magnifying-glass icon" title="Search"></i>
                <div class="profile-icon" title=" <?= $user_email ?> ">
                    <img src="<?= $user_image ?>" alt="Profile" />
                </div>
                <i id="logout" title="Logout" class="fa-solid fa-power-off logout"></i>
            </div>
        </header>
        <div class="content">
            <aside id="sidebar">
                <nav>
                    <ul>
                        <li><i class="fa-solid fa-house"></i><a href="home.php">Home</a></li>
                        <li><i class="fa-solid fa-list"></i><a href="home.php">To-do</a></li>
                        <li style="display:flex;justify-content:space-between;">
                            <div><i class="fa-solid fa-list-check"></i><a href="home.php">Enrolled</a></div>
                            <i class="fa-solid fa-caret-down"></i>
                        </li>
                    </ul>
                </nav>
            </aside>
            <main>
                <div class="assignment-detail">
                    <h2><i class="fas fa-clipboard"></i> Python Assignment-8</h2>
                    <p>Rahul Mahato • May 16 (Edited May 18)</p>
                    <p>20 points</p>
                    <p>Due May 18, 11:59 PM</p>
                    <hr>
                    <p>Please write the solution for each assignment question in a separate .py file. Once completed,
                        consolidate all these .py files into a zip archive and submit it here.</p>
                    <ul>
                        <li>Please ensure submissions are made before Saturday, May 18, 2024.</li>
                        <li>Students submitting after the deadline will receive reduced marks.</li>
                        <li>Ensure your code is original and free from plagiarism.</li>
                        <li>Each assignment is worth a total of 20 marks</li>
                    </ul>
                    <div class="assignment-file">
                        <i class="fas fa-file-pdf"></i>
                        <a href="#">TIU Assignment-8.pdf</a>
                    </div>
                    <div class="comments-section">
                        <h3>Class comments</h3>
                        <a href="#">Add a class comment</a>
                        <textarea placeholder="Add a comment..." id="editor1"></textarea>
                    </div>
                </div>
                <div class="submission-box">
                    <h3>Your work</h3>
                    <label for="add-file" class="btn-add-create" style="text-align:center;">+ Add or create </label>
                    <input id="add-file" style="display:none;" type="file">
                    <button class="btn-resubmit">Resubmit</button>
                    <div class="private-comments">
                        <h3>Private comments</h3>
                        <a href="#">Add comment to Rahul Mahato</a>
                        <textarea id="editor2"></textarea>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Include the Quill library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="./assets/js/app.js"></script>
</body>

</html>