<?php

require ("../includes/db.php");
require ("../includes/functions.php");

global $connection;

if (isset($_POST['show_all_class'])) {
    $html = "";

    // Validate that user_id is set in session
    if (!isset($_SESSION['user_id'])) {
        $res = [
            'status' => 400,
            'message' => "User is not authenticated."
        ];
        echo json_encode($res);
        return;
    }

    $sql = "
        SELECT DISTINCT classes.*
        FROM classes 
        LEFT JOIN enrollments ON classes.class_id = enrollments.enrollment_class_id
        WHERE 
            classes.class_teacher_id = '{$_SESSION['user_id']}'
            OR 
            enrollments.student_id = '{$_SESSION['user_id']}'
        ORDER BY classes.class_id DESC
    ";

    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $teacher_id = $row['class_teacher_id'];
            $show = $teacher_id == $_SESSION['user_id'] ? 'Delete Class' : 'Unenroll';

            $user_query = "SELECT * FROM users WHERE user_id = '$teacher_id'";
            $user_result = mysqli_query($connection, $user_query);

            // Initialize the default user
            $user_image = APP_URL . "storage/profile/defaultAvatar.jpg";

            if ($user_result && mysqli_num_rows($user_result) > 0) {
                $user = mysqli_fetch_assoc($user_result);
                if ($user["user_image_url"] != NULL) {
                    $user_image = $user["user_image_url"];
                }
            }

            $class_id = md5($row['class_code']);

            $html .= "
                <div data-id='$class_id' data-code='{$row['class_code']}' class='card'>
                    <span id='show-more' class='show-more'>&#8942;</span>
                    <div id='class-actions' class='class-actions'>
                        <a class='viewCode'>View Code</a>
                        <a class='deleteClass'>{$show}</a>
                    </div>
                    <h3>{$row['class_name']}</h3>  
                    <div style='display:flex;justify-content:space-between;margin:4px 0;font-size:14px'><span>{$row['class_subject']}</span><span>{$row['class_section']}</span></div>
                    <p style='margin-top:1.2rem'>{$user['user_name']}</p>
                    <div class='class-hr'><img class='class-teacher-image' src='$user_image' /></div>
                    <a class='view-btn' href='class.php?c=" . md5($row['class_code']) . "' >View Details</a>
                </div>
            ";
        }
    } else {
        $html .= "<h2 style='font-weight:500;'>You don't have any classes</h2>";
    }

    $res = [
        'status' => 200,
        'message' => $html
    ];
    echo json_encode($res);
    return;
}

if (isset($_POST['create_class'])) {

    $html = "";

    $class_name = filter_var($_POST['className'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $class_section = filter_var($_POST['classSection'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $class_subject = filter_var($_POST['classSubject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $class_code = generateUniqueTicketId($connection);

    $sql = "INSERT INTO classes (class_name, class_section, class_subject, class_code, class_teacher_id) ";
    $sql .= "VALUES ('$class_name', '$class_section', '$class_subject', '$class_code', '{$_SESSION['user_id']}')";
    $result = mysqli_query($connection, $sql);
    if ($result) {
        $html = "<p>Code to join this class is <b>{$class_code}</b></p>";
        $res = [
            'status' => 200,
            'message' => $html
        ];
        echo json_encode($res);
        return;
    }

}

if (isset($_POST['join_class'])) {

    $html = "";
    $join = 0;

    $class_code = $_POST['classCode'];
    $user_id = $_SESSION['user_id'];

    if ($class_code && $user_id) {

        // Check if the class code is valid
        $check_class_sql = "SELECT * FROM classes WHERE class_code = '$class_code'";
        $check_class_result = mysqli_query($connection, $check_class_sql);

        $class = mysqli_fetch_assoc($check_class_result);
        if ($class) {
            $class_id = $class['class_id'];

            // Check if the current user is the teacher of the class
            if ($class['class_teacher_id'] == $user_id) {
                $html .= "You are the teacher of this class. You cannot enroll as a student.";
            } else {
                // Check if the current user is already enrolled in the class
                $check_enrollment_sql = "SELECT * FROM enrollments WHERE enrollment_class_id = '$class_id' AND student_id = '$user_id'";
                $check_enrollment_result = mysqli_query($connection, $check_enrollment_sql);

                if (mysqli_num_rows($check_enrollment_result) > 0) {
                    $html .= "You are already enrolled in this class.";
                } else {
                    // Enroll the user in the class
                    $enroll_sql = "INSERT INTO enrollments (enrollment_class_id, student_id) VALUES ('$class_id', '$user_id')";
                    $enroll_result = mysqli_query($connection, $enroll_sql);

                    $html .= "Successfully enrolled in the class.";
                    $join = 1;
                }
            }
        } else {
            $html .= "Invalid class code.";
        }

    } else {
        $html .= "No class code provided or user not logged in.";
    }

    $res = [
        'join' => $join,
        'message' => $html
    ];
    echo json_encode($res);
    return;

}

if (isset($_POST['delete_class'])) {

    $html = "";

    $class_code = filter_var($_POST['classId'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $class_id = class_id($class_code);
    $user_id = $_SESSION['user_id'];

    if ($class_id && $user_id) {

        // Check if the current user is the teacher of the class
        $check_teacher_sql = "SELECT class_teacher_id FROM classes WHERE class_id = '$class_id'";
        $check_teacher_result = mysqli_query($connection, $check_teacher_sql);

        $is_teacher = false; // therefore student

        $class = mysqli_fetch_assoc($check_teacher_result);
        if ($class && $class['class_teacher_id'] == $user_id) {
            $is_teacher = true; // therefore teacher
        }

        if ($is_teacher) {

            // Delete from announcements table
            $delete_announcements_sql = "DELETE FROM announcements WHERE an_class_id = '$class_id' AND an_user_id = '$user_id'";
            $delete_announcements_result = mysqli_query($connection, $delete_announcements_sql);

            // Delete from assignments table
            $delete_assignments_sql = "DELETE FROM assignments WHERE asgn_class_id = '$class_id'";
            $delete_assignments_result = mysqli_query($connection, $delete_assignments_sql);

            // Delete from enrollments table
            $delete_enrollments_sql = "DELETE FROM enrollments WHERE enrollment_class_id = '$class_id'";
            $delete_enrollments_result = mysqli_query($connection, $delete_enrollments_sql);

            // Delete from classes table
            $delete_class_sql = "DELETE FROM classes WHERE class_id = '$class_id'";
            $delete_class_result = mysqli_query($connection, $delete_class_sql);

            // Delete from grades table
            $delete_grades_sql = "DELETE FROM grades WHERE grade_class_id = '$class_id'";
            $delete_grades_result = mysqli_query($connection, $delete_grades_sql);

            // Delete from materials table
            $delete_sub_sql = "DELETE FROM materials WHERE material_class_id = '$class_id'";
            $delete_sub_result = mysqli_query($connection, $delete_sub_sql);

            // Delete submitted files
            $image_query = "SELECT material_file FROM materials WHERE material_class_id = '$class_id'";
            $image_result = mysqli_query($connection, $image_query);
            if (mysqli_num_rows($image_result) > 0) {
                while ($image = mysqli_fetch_assoc($image_result)) {
                    unlink('../storage/files/' . $image['material_file']);
                }
            }

            // Delete from submissions table
            $delete_sub_sql = "DELETE FROM submissions WHERE sub_class_id = '$class_id'";
            $delete_sub_result = mysqli_query($connection, $delete_sub_sql);

            // Delete submitted files
            $image_query2 = "SELECT sub_file FROM submissions WHERE sub_class_id = '$class_id' AND sub_student_id = '$user_id'";
            $image_result2 = mysqli_query($connection, $image_query2);
            if (mysqli_num_rows($image_result2) > 0) {
                while ($image = mysqli_fetch_assoc($image_result2)) {
                    unlink('../storage/files/' . $image['material_file']);
                }
            }

            $html .= "Class and related enrollments deleted successfully.";
        } else {

            // Delete from enrollments table
            $delete_enrollments_sql = "DELETE FROM enrollments WHERE enrollment_class_id = '$class_id' AND student_id = '$user_id'";
            $delete_enrollments_result = mysqli_query($connection, $delete_enrollments_sql);

            // Delete from grades table
            $delete_grades_sql = "DELETE FROM grades WHERE grade_class_id = '$class_id' AND grade_student_id = '$user_id'";
            $delete_grades_result = mysqli_query($connection, $delete_grades_sql);

            // Delete from submissions table
            $delete_sub_sql = "DELETE FROM submissions WHERE sub_class_id = '$class_id' AND sub_student_id = '$user_id'";
            $delete_sub_result = mysqli_query($connection, $delete_sub_sql);

            // Delete submitted files
            $image_query1 = "SELECT sub_file FROM submissions WHERE sub_class_id = '$class_id' AND sub_student_id = '$user_id'";
            $image_result1 = mysqli_query($connection, $image_query1);
            if (mysqli_num_rows($image_result1) > 0) {
                while ($image = mysqli_fetch_assoc($image_result1)) {
                    unlink('../storage/files/' . $image['material_file']);
                }
            }

            // Delete from announcements table
            $delete_announcements_sql = "DELETE FROM announcements WHERE an_class_id = '$class_id' AND an_user_id = '$user_id'";
            $delete_announcements_result = mysqli_query($connection, $delete_announcements_sql);

            $image_query2 = "SELECT material_file FROM materials WHERE material_class_id = '$class_id' AND material_user_id = '$user_id'";
            $image_result2 = mysqli_query($connection, $image_query2);
            if (mysqli_num_rows($image_result2) > 0) {
                while ($image = mysqli_fetch_assoc($image_result2)) {
                    unlink('../storage/files/' . $image['material_file']);
                }
            }

            // Delete from classes table -- user cant because student

            $html .= "Unenrolled successfully";
        }

    } else {
        $html .= "No class_id provided or user not logged in.";
    }

    $res = [
        'delete' => 1,
        'message' => $html
    ];
    echo json_encode($res);
    return;
}




