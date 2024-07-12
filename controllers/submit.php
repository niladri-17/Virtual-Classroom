<?php

// sleep(3);

require ("../includes/db.php");
require ("../includes/functions.php");

global $connection;


if (isset($_POST["submit_asgn"])) {
    $html = "";
    $uploaded_files = []; // Initialize the array

    $error = 0;

    $class_code = $_POST['classCode'];
    $asgn_id = $_POST['asgn_id'];

    $check_accept_status_query = "SELECT * FROM assignments WHERE asgn_id = '$asgn_id' AND asgn_accept_status = 1";
    $check_accept_status_result = mysqli_query($connection, $check_accept_status_query);
    $accept_status = mysqli_num_rows($check_accept_status_result) > 0 ? true : false;

    if ($accept_status) {
        if (empty($_FILES['files']['name'][0])) {
            $html .= "Choose a file to submit";
            $error = 1;
        } else {
            $files = $_FILES['files']['name'];
            $time = time();
            $file_temp = $_FILES['files']['tmp_name'];
            $file_size = $_FILES['files']['size'];

            foreach ($files as $key => $file_name) {
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                // Exclude certain file types
                $unallowed_files = ['exe', 'bat', 'sh'];
                if (!in_array($file_extension, $unallowed_files)) {
                    // Make sure file size is less than 2MB
                    if ($file_size[$key] < 2000000) {
                        $new_file_name = $time . '_' . $file_name;
                        $file_destination_path = "../storage/files/" . $new_file_name;

                        // Upload file
                        if (move_uploaded_file($file_temp[$key], $file_destination_path)) {
                            $uploaded_files[] = $new_file_name;
                        } else {
                            $html .= "Failed to upload file: $file_name<br>";
                            $error = 1;
                            break;
                        }
                    } else {
                        $html .= "File size too big for $file_name. Should be less than 2MB.<br>";
                        $error = 1;
                        break;
                    }
                } else {
                    $html .= "File type not allowed for $file_name. Cannot be 'exe', 'bat', 'sh'.<br>";
                    $error = 1;
                    break;
                }
            }
        }

        if ($error == 0) {
            $class_id = class_id($class_code);
            // Insert each uploaded file into submissions table
            if (!empty($uploaded_files)) {
                foreach ($uploaded_files as $file_name) {
                    $submission_file_query = "INSERT INTO submissions(sub_class_id, sub_asgn_id, sub_student_id, sub_file) VALUES ('$class_id', '$asgn_id', '{$_SESSION['user_id']}', '$file_name')";
                    $submission_file_result = mysqli_query($connection, $submission_file_query);
                    if (!$submission_file_result) {
                        $html .= "Failed to insert file record for: $file_name<br>";
                        $error = 1;
                        break;
                    }
                }
            }
            $html .= "Submission done!";
        }
    } else {
        $error = 1;
        $html .= "Submissions are closed";
    }

    $res = [
        'error' => $error,
        'message' => $html
    ];
    echo json_encode($res);
}

if (isset($_POST["unsubmit_asgn"])) {
    $html = "";

    $error = 0;

    $class_code = $_POST['classCode'];
    $asgn_id = $_POST['asgn_id'];

    $class_id = class_id($class_code);

    $check_accept_status_query = "SELECT * FROM assignments WHERE asgn_id = '$asgn_id' AND asgn_accept_status = 1";
    $check_accept_status_result = mysqli_query($connection, $check_accept_status_query);
    $accept_status = mysqli_num_rows($check_accept_status_result) > 0 ? true : false;

    if ($accept_status) {

        $image_query = "SELECT sub_file FROM submissions WHERE sub_asgn_id = '$asgn_id' AND sub_student_id = '{$_SESSION['user_id']}'";
        $image_result = mysqli_query($connection, $image_query);
        if (mysqli_num_rows($image_result) > 0) {
            while ($image = mysqli_fetch_assoc($image_result)) {
                unlink('../storage/files/' . $image['material_file']);
            }
        }

        $delete_sub_query = "DELETE FROM submissions WHERE sub_asgn_id = '$asgn_id' AND sub_student_id = '{$_SESSION['user_id']}'";
        $delete_sub_result = mysqli_query($connection, $delete_sub_query);
        if (!$delete_sub_result) {
            $html .= "Failed to remove submissions";
            $error = 1;
        }
        $html .= "Submissions removed!";
    } else {
        $error = 1;
        $html .= "Submission is closed. You can't remove submissions.";
    }

    $res = [
        'error' => $error,
        'message' => $html
    ];
    echo json_encode($res);
}

if (isset($_POST["asgn_not_accept"])) {

    $error = 0;

    $asgn_id = $_POST['asgn_id'];

    $asgn_not_accept_query = "UPDATE assignments SET asgn_accept_status = 0 WHERE asgn_id = '$asgn_id'";
    $asgn_not_accept_result = mysqli_query($connection, $asgn_not_accept_query);
    if (!$asgn_not_accept_result) {
        $html .= "Failed to remove submissions";
        $error = 1;
    }

    $res = [
        'error' => $error,
    ];
    echo json_encode($res);
}

if (isset($_POST["asgn_accept"])) {

    $error = 0;

    $asgn_id = $_POST['asgn_id'];

    $asgn_not_accept_query = "UPDATE assignments SET asgn_accept_status = 1 WHERE asgn_id = '$asgn_id'";
    $asgn_not_accept_result = mysqli_query($connection, $asgn_not_accept_query);
    if (!$asgn_not_accept_result) {
        $html .= "Failed to remove submissions";
        $error = 1;
    }

    $res = [
        'error' => $error,
    ];
    echo json_encode($res);
}

if (isset($_POST['give_grade']) && $_POST['give_grade'] == 1) {

    $class_code = $_POST['classCode'];
    $class_id = class_id($class_code);
    $asgnId = $_POST['asgn_id'];
    $stuId = $_POST['stu_id'];
    $gradeValue = $_POST['grade_value'];

    // Prepare the SELECT query
    $selectQuery = "SELECT * FROM grades WHERE grade_asgn_id = $asgnId AND grade_student_id = $stuId";
    $result = mysqli_query($connection, $selectQuery);

    if (mysqli_num_rows($result) > 0) {
        // Record exists, so update it
        $updateQuery = "UPDATE grades SET grade_value = $gradeValue WHERE grade_class_id = '$class_id' AND grade_asgn_id = $asgnId AND grade_student_id = $stuId";
        if (mysqli_query($connection, $updateQuery)) {
            echo json_encode(["error" => 0, "message" => "Grade updated successfully"]);
        } else {
            echo json_encode(["error" => 1, "message" => "Failed to update grade"]);
        }
    } else {
        // Record does not exist, so insert a new one
        $insertQuery = "INSERT INTO grades (grade_class_id, grade_asgn_id, grade_student_id, grade_value) VALUES ($class_id, $asgnId, $stuId, $gradeValue)";
        if (mysqli_query($connection, $insertQuery)) {
            echo json_encode(["error" => 0, "message" => "Grade inserted successfully"]);
        } else {
            echo json_encode(["error" => 1, "message" => "Failed to insert grade"]);
        }
    }
}
