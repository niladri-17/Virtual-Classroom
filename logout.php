<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die();
}

unset($_SESSION);
session_destroy();
header("Location: index.php");