<?php

function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function isUniqueId($connection, $class_code)
{

    $query = "SELECT COUNT(*) as count FROM classes WHERE class_code= '$class_code'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

function generateUniqueTicketId($connection)
{
    do {
        $class_code = generateRandomString();
    } while (!isUniqueId($connection, $class_code));
    return $class_code;
}
function teacher_or_student($class_code)
{
    global $connection;

    $user_id = $_SESSION['user_id'];

    // Check if the current user is the teacher of the class
    $check_teacher_sql = "SELECT * FROM classes WHERE md5(class_code) = '$class_code'";
    $check_teacher_result = mysqli_query($connection, $check_teacher_sql);

    $user_role = 0; // user is neither teacher nor student

    $class = mysqli_fetch_assoc($check_teacher_result);

    if ($class && $class['class_teacher_id'] == $user_id) {
        $user_role = 1; // 1 denotes teacher
    } else {
        // Check if the current user is a student in the class

        $class_id = $class['class_id'];

        $check_student_sql = "SELECT student_id FROM enrollments WHERE enrollment_class_id = '$class_id' AND student_id = '$user_id'";
        $check_student_result = mysqli_query($connection, $check_student_sql);
        if (mysqli_num_rows($check_student_result) > 0) {
            $user_role = 2; // 2 denotes student
        }
    }
    return $user_role;
}

function class_id($class_code)
{
    global $connection;
    $class_id_sql = "SELECT class_id FROM classes WHERE md5(class_code) = '$class_code'";
    $class_id_result = mysqli_query($connection, $class_id_sql);
    $row = mysqli_fetch_assoc($class_id_result);
    return $row["class_id"];

}
function post_date($date)
{

    // Create a DateTime object from the retrieved date
    $dateTime = new DateTime($date);

    // Get day, month, and year separately
    $day = $dateTime->format('j');
    $month = $dateTime->format('F');
    $year = $dateTime->format('Y');

    // Determine the day suffix
    if ($day == 1 || $day == 21 || $day == 31) {
        $suffix = 'st';
    } elseif ($day == 2 || $day == 22) {
        $suffix = 'nd';
    } elseif ($day == 3 || $day == 23) {
        $suffix = 'rd';
    } else {
        $suffix = 'th';
    }

    // Print the formatted date
    return $day . "<sup>$suffix</sup>" . ' ' . $month . ' ' . $year;
}