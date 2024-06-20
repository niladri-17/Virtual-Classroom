<?php

require ("../includes/db.php");

global $connection;

if (isset($_POST['enrolled_list'])) {

    $html = "";

    $select_enrolled_query =
        "
            SELECT * 
            FROM classes 
            LEFT JOIN enrollments ON classes.class_id = enrollments.enrollment_class_id
            WHERE 
            enrollments.student_id = '{$_SESSION['user_id']}'
            ORDER BY class_id DESC
        ";

    $select_enrolled_result = mysqli_query($connection, $select_enrolled_query);
    if (mysqli_num_rows($select_enrolled_result) > 0) {
        while ($class = mysqli_fetch_assoc($select_enrolled_result)) {
            $html .=
                "
                    <li>
                        <a href='class.php?c=" . md5($class['class_code']) . "'>
                        <p>{$class['class_name']}<p>
                        <p style='font-size:.8rem'>{$class['class_section']}</p>
                        </a>
                    </li>
                ";
        }
    } else {
        $html .= "<li>You are not enrolled to any class</li>";
    }

    $res = [
        'status' => 1,
        'message' => $html
    ];
    echo json_encode($res);
}

if (isset($_POST['teaching_list'])) {

    $html = "";

    $select_teaching_query =
        "
            SELECT * 
            FROM classes 
            WHERE 
            class_teacher_id = '{$_SESSION['user_id']}'
            ORDER BY class_id DESC
        ";

    $select_teaching_result = mysqli_query($connection, $select_teaching_query);
    if (mysqli_num_rows($select_teaching_result) > 0) {
        while ($class = mysqli_fetch_assoc($select_teaching_result)) {
            $html .=
                "
                <a href='class.php?c=" . md5($class['class_code']) . "'>   
                    <li>        
                        <p>{$class['class_name']}<p>
                        <p style='font-size:.8rem'>{$class['class_section']}</p>
                    </li>
                </a>
                ";
        }
    } else {
        $html .= "<li>You don't have any class.</li>";
    }

    $res = [
        'status' => 1,
        'message' => $html
    ];
    echo json_encode($res);
}

