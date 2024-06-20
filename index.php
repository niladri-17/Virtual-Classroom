<?php require 'includes/db.php' ?>
<?php
if (isset($_SESSION['user_id']))
    header('Location: home.php');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Classroom</title>
    <link rel="shortcut icon" href="<?= APP_URL . 'assets/images/favicon.jpg' ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= APP_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="header-left">
                <h1><a href="home.php">Virtual Classroom</a></h1>
            </div>
            <div class="header-right">
                <i id="themeToggle" class="fas fa-moon icon" title="Toggle Theme"></i>
                <i style="display: none;" id="signinBtn" class="fas fa-arrow-right-to-bracket icon" title="Sign In"></i>
            </div>
        </header>
        <div class="index-content">
            <div class="index-content-left">
                <h2 class="index-left-heading">Where teaching and learning come together</h2>
                <p>Virtual Classroom helps educators create engaging learning experiences they can personalize, manage, and measure. It empowers educators to enhance their impact and prepare students for the future.</p>
                <div><a href="">Sign in to Classroom</a></div>
            </div>
            <div class="index-content-right">
            <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Sign In</h2>
            <form id="signin-form">
                <div id="signup-success" style="color:green"></div>
                <div id="signin-response" style="color:red"></div>
                <label for="signinEmail">Email:</label>
                <input type="email" id="signinEmail" required>
                <label for="signinPassword">Password:</label>
                <input type="password" id="signinPassword" required>
                <button type="submit">Sign In</button>
                <div><a href='<?= $client->createAuthUrl(); ?>'
                        style="display:block;width:max-content;margin:0 auto;text-decoration:none;"><i
                            class="fa-brands fa-google"></i>
                        Sign in with Google</a>
                </div>
                <p class="forgot-password"><a href="#" id="forgotPasswordLink">Forgot Password?</a></p>
                <p class="toggle-modal">Don't have an account? <a href="#" id="showSignup">Sign Up</a></p>
            </form>
        </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div id="signinModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Sign In</h2>
            <form id="signin-form">
                <div id="signup-success" style="color:green"></div>
                <div id="signin-response" style="color:red"></div>
                <label for="signinEmail">Email:</label>
                <input type="email" id="signinEmail" required>
                <label for="signinPassword">Password:</label>
                <input type="password" id="signinPassword" required>
                <button type="submit">Sign In</button>
                <div><a href='<?= $client->createAuthUrl(); ?>'
                        style="display:block;width:max-content;margin:0 auto;text-decoration:none;"><i
                            class="fa-brands fa-google"></i>
                        Sign in with Google</a>
                </div>
                <p class="forgot-password"><a href="#" id="forgotPasswordLink">Forgot Password?</a></p>
                <p class="toggle-modal">Don't have an account? <a href="#" id="showSignup">Sign Up</a></p>
            </form>
        </div>
    </div>

    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Sign Up</h2>
            <form id="signup-form">
                <div id="signup-response" style="color:red"></div>
                <label for="signupName">Name:</label>
                <input type="text" id="signupName" required>
                <label for="signupEmail">Email:</label>
                <input type="email" id="signupEmail" required>
                <label for="signupPassword">Password:</label>
                <input type="password" id="signupPassword" required>
                <label for="ConfirmPassword">Confirm Password:</label>
                <input type="password" id="ConfirmPassword" required>
                <button type="submit">Sign Up</button>
                <p class="toggle-modal">Already have an account? <a href="#" id="showSignin">Sign In</a></p>
            </form>
        </div>
    </div>

    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Forgot Password</h2>
            <form>
                <label for="forgotPasswordEmail">Email:</label>
                <input type="email" id="forgotPasswordEmail" name="email" required>
                <button type="submit">Reset Password</button>
                <p class="toggle-modal">Already have an account? <a href="#" id="backToSignin">Sign In</a></p>
            </form>
        </div>
    </div>

    <script src="<?= APP_URL ?>/assets/js/mini.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>

</html>

<script>

    function emptySignupEntry() {
        $("#signupName").val("");
        $("#signupEmail").val("");
        $("#signupPassword").val("");
        $("#ConfirmPassword").val("");
    }
    
    function emptySigninEntry() {
        $("#signinEmail").val("");
        $("#signinPassword").val("");
    }

    $("#signin-form").submit(function (e) {
        e.preventDefault();
        const signinEmail = $("#signinEmail").val();
        const signinPassword = $("#signinPassword").val();
        $.ajax({
            type: "POST",
            url: "controllers/auth.php",
            data: {
                signin: 1,
                signinEmail: signinEmail,
                signinPassword: signinPassword
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                const message = res.message;
                if (res.signin == 0) {
                    $("#signup-success").text("");
                    $("#signup-success").hide();
                    $("#signin-response").show();
                    $("#signin-response").text(message);
                } else {
                    window.location.href = "home.php";
                }
            }
        });
    });

    $("#signup-form").submit(function (e) {
        e.preventDefault();
        const signupName = $("#signupName").val();
        const signupEmail = $("#signupEmail").val();
        const signupPassword = $("#signupPassword").val();
        const confirmPassword = $("#ConfirmPassword").val();
        $.ajax({
            type: "POST",
            url: "controllers/auth.php",
            data: {
                signup: 1,
                signupName: signupName,
                signupEmail: signupEmail,
                signupPassword: signupPassword,
                confirmPassword: confirmPassword,
            },
            success: function (response) {
                const res = jQuery.parseJSON(response);
                const message = res.message;
                if (res.signup == 0) {
                    $("#signup-response").show();
                    $("#signup-response").text(message);
                } else {
                    // toggleModal(signupModal, signinModal);
                    $(signupModal).hide();
                    emptySigninEntry();
                    $("#signup-success").show();
                    $("#signup-success").text(message);
                }

            }
        });
    });

    $("#signup-success").hide();
    $("#signin-response").hide();
    $("#signup-response").hide();
</script>