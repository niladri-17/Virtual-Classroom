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

function asgn_id($asgn_id)
{
    global $connection;
    $asgn_id_sql = "SELECT asgn_id FROM assignments WHERE md5(asgn_id) = '$asgn_id'";
    $asgn_id_result = mysqli_query($connection, $asgn_id_sql);
    $row = mysqli_fetch_assoc($asgn_id_result);
    return $row["asgn_id"];

}

function fetch_my_submissions()
{
    global $connection;

    $asgn_id = asgn_id($_GET['a']);

    $submissions_materials_query = "SELECT * FROM submissions WHERE sub_asgn_id = '$asgn_id' AND sub_student_id = '{$_SESSION['user_id']}'";
    $submissions_materials_result = mysqli_query($connection, $submissions_materials_query);

    // assignment materials
    $html = "";

    if (mysqli_num_rows($submissions_materials_result) > 0) {
        $html .= "<div style='margin-bottom:1rem;' class='sub_box_heading'><h3>Your work</h3><span>Turned in</span></div>";
        $html .= "<ul class='file-list'>";

        while ($materials = mysqli_fetch_assoc($submissions_materials_result)) {
            $material_names = $materials['sub_file'];
            $file_name = substr($material_names, 11);
            $html .=
                "
                <a href='./storage/files/$material_names' target='_blank'>
                    <li class='file-list-item'>
                    $file_name
                    </li>
                </a>
            ";

        }
        $html .= "</ul>";
        $html .=
            "
            <form id='asgn-unsubmit-form'>
                <button type='submit' class='btn-submit'>Unsubmit</button>
            </form>
        ";
    } else {
        $html .= "<div><h3>Your work</h3></div>";
        $html .=
            "
            <p id='fileCount1' class='file-count'></p>
            <ul id='fileList-1' class='file-list'></ul>
            <form id='asgn-submit-form' enctype='multipart/form-data'>
                <label for='submit-file' class='btn-add-create' style='text-align:center;'>+ Add or create
                </label>
                <input id='submit-file' style='display:none;' type='file' name='files[]' multiple>
                <button type='submit' class='btn-submit'>Turn in</button>
            </form>
        ";
    }
    echo $html;
}

function fetch_all_submissions()
{
    global $connection;

    $asgn_id = asgn_id($_GET['a']);

    $html = "";

    $fetch_submissions_students_query =
        "SELECT DISTINCT sub_student_id 
            FROM submissions 
            WHERE sub_asgn_id = '$asgn_id';
        ";
    $fetch_submissions_students_result = mysqli_query($connection, $fetch_submissions_students_query);

    if (mysqli_num_rows($fetch_submissions_students_result) > 0) {
        while ($student = mysqli_fetch_assoc($fetch_submissions_students_result)) {

            $fetch_student_details_query = "SELECT * FROM submissions WHERE sub_asgn_id = '$asgn_id' AND sub_student_id={$student['sub_student_id']}";
            $fetch_student_details_result = mysqli_query($connection, $fetch_student_details_query);

            $student_details = mysqli_fetch_assoc($fetch_student_details_result);

            $sql = "SELECT * FROM users where user_id = '{$student['sub_student_id']}'";
            $result = mysqli_query($connection, $sql);
            $user = mysqli_fetch_assoc($result);
            if ($user["user_image_url"] == NULL)
                $user_image = APP_URL . "storage/profile/defaultAvatar.jpg";
            else
                $user_image = $user["user_image_url"];

            $fetch_grade_query = "SELECT * FROM grades WHERE grade_asgn_id = '$asgn_id' AND grade_student_id= '{$student['sub_student_id']}'";
            $fetch_grade_result = mysqli_query($connection, $fetch_grade_query);
            $grade = "";
            if (mysqli_num_rows($fetch_grade_result) > 0) {
                $fetch_grade = mysqli_fetch_assoc($fetch_grade_result);
                $grade = "Grade: " . $fetch_grade['grade_value'];
            } else {
                $grade = "Ungraded";
            }


            $html .=
                "
                <div STU-ID='{$student['sub_student_id']}'>
                    <div class='sub-header'>
                        <div class='sub-header-1'>
                            <img src='$user_image' alt='' title='{$user['user_email']}'>
                            <span>{$user['user_name']}</span>
                            <form class='give-grade-form'>
                                <input type='text' name='grade'>
                                <button type='submit'>Mark</button>
                            </form>
                        </div>
                        <div class='sub-header-2'>
                            <span>Submitted on: {$student_details['sub_date']}</span>
                            <span>$grade</span>
                        </div>
                    </div>
                    <ul class='sub-list'>
                ";

            $fetch_all_submissions_query = "SELECT * FROM submissions WHERE sub_asgn_id = '$asgn_id' AND sub_student_id={$student['sub_student_id']}";
            $fetch_all_submissions_result = mysqli_query($connection, $fetch_all_submissions_query);

            while ($materials = mysqli_fetch_assoc($fetch_all_submissions_result)) {
                $material_names = $materials['sub_file'];
                $file_name = substr($material_names, 11);
                $html .=
                    "
                    <li class='sub-list-items'>
                        <a href='./storage/files/$material_names' target='_blank'>
                            $file_name
                        </a>
                    </li>
                    ";
            }
            $html .=
                "
                    </ul>
                </div>
                <hr class='h-div'>
                ";
        }
    }

    echo $html;
}

function asgn_accept_status()
{
    global $connection;
    $asgn_id = asgn_id($_GET['a']);
    $accept_query = "SELECT * FROM assignments WHERE asgn_id = '$asgn_id' AND asgn_accept_status = 1";
    $accept_result = mysqli_query($connection, $accept_query);
    if (mysqli_num_rows($accept_result) > 0) {
        echo
            "
            <div class='switch'>
                <div class='toggle-hider'></div>
                <input type='checkbox' class='toggle'>
                <label for='toggle' id='toggle' class='label plus'></label>
                <span class='text'>Accepting </span>
            </div> 

        ";
    } else {
        echo
            "
            <div class='switch'>
                <div class='toggle-hider'></div>
                <input type='checkbox' class='toggle'>
                <label for='toggle' id='toggle' class='label minus'></label>
                <span class='text'>Not accepting</span>
            </div>
        ";
    }
}