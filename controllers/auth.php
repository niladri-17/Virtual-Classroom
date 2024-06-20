<?php

require ("../includes/db.php");

global $connection;

if (isset($_POST["signin"])) {
    $email = filter_var($_POST['signinEmail'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($_POST['signinPassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $html = '';
    $signin = 0;
    if (!$email)
        $html = "* Username or Email required";
    else if (!$password)
        $html = "* Password required";
    else {
        $fetch_user_query = "SELECT * FROM users WHERE user_email='$email'";
        $fetch_user_result = mysqli_query($connection, $fetch_user_query);

        if (mysqli_num_rows($fetch_user_result) == 1) {
            $user_record = mysqli_fetch_assoc($fetch_user_result);
            $db_password = $user_record['user_password'];
            //compare form password with database password
            if (empty($db_password)) {
                $html = "* Signin to this account using Google";
            } else if (password_encrypt($password) === $db_password) {
                // set session for access control
                $_SESSION['user_id'] = $user_record['user_id'];
                // log user in
                $signin = 1;
            } else {
                $html = "* Wrong password";
            }
        } else {
            $html = "* User not found";
        }
    }

    $res = [
        'status' => 200,
        'signin' => $signin,
        'message' => $html
    ];
    echo json_encode($res);
    return;
}


if (isset($_POST['signup'])) {

    $name = filter_var($_POST['signupName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['signupEmail'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $create_password = filter_var($_POST['signupPassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_password = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $signup = 0;
    $html = '';
    if ($create_password !== $confirm_password) {
        // check if passwords match
        $html = "Passwords do not match!";
    } else {
        // hash password
        $password_hash = password_encrypt($confirm_password);

        $user_check_query = "SELECT * FROM users WHERE user_email = '$email'";
        $user_check_result = mysqli_query($connection, $user_check_query);

        if (mysqli_num_rows($user_check_result) > 0) {
            $html = "* Email already exists!";
        }
    }

    if (empty($html)) {
        $user_signup_query = "INSERT INTO users(user_name, user_email, user_password) ";
        $user_signup_query .= "VALUES ('$name', '$email', '$password_hash')";
        $user_signup_result = mysqli_query($connection, $user_signup_query);
        if ($user_signup_result) {
            $html = 'Signup successful. Please Sign in';
            $signup = 1;
        }
    }
    $res = [
        'status' => 200,
        'signup' => $signup,
        'message' => $html
    ];
    echo json_encode($res);
    return;
}


function password_encrypt($password)
{
    $hashFormat = "$2y$10$";
    $salt = "69hellomotherfucker696";
    $hash_form_and_salt = $hashFormat . $salt;
    $password_hash = crypt($password, $hash_form_and_salt);
    return $password_hash;
}
