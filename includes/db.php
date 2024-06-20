<?php
ob_start();
require (__DIR__ . "/config.php");
session_start();

$connection = mysqli_connect(DB_HOST , DB_USER , DB_PASS , DB_NAME) or die;

mysqli_set_charset($connection, 'utf8mb4');
