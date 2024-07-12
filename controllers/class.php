<?php

// sleep(3);

require ("../includes/db.php");
require ("../includes/functions.php");

global $connection;

if (isset($_POST['fetch_announcements'])) {
    $html = "";
    $class_id = class_id($_POST['classCode']);
    $user_id = $_SESSION['user_id'];

    // Check if the user is a teacher of the class
    $check_teacher_query = "SELECT * FROM classes WHERE class_id = '$class_id' AND class_teacher_id = '$user_id'";
    $check_teacher_result = mysqli_query($connection, $check_teacher_query);
    $is_teacher = mysqli_num_rows($check_teacher_result) > 0;

    $select_announcements_query = "SELECT * FROM announcements WHERE an_class_id = '$class_id' ORDER BY an_id DESC";
    $select_announcements = mysqli_query($connection, $select_announcements_query);

    while ($row = mysqli_fetch_assoc($select_announcements)) {
        $an_date = post_date($row['an_created_at']);

        $an_user_details = "SELECT * FROM users WHERE user_id={$row['an_user_id']}";
        $an_user_result = mysqli_query($connection, $an_user_details);
        $user = mysqli_fetch_assoc($an_user_result);

        $an_materials_query = "SELECT * FROM materials WHERE material_an_id = '{$row['an_id']}'";
        $an_materials_result = mysqli_query($connection, $an_materials_query);

        // announcement and assignment materials
        $files = "";

        if (mysqli_num_rows($an_materials_result) > 0) {
            $files = "<br>";
            while ($materials = mysqli_fetch_assoc($an_materials_result)) {
                $material_names = $materials['material_file'];
                $file_name = substr($material_names, 11);
                $files .= "<p><a href='./storage/files/$material_names' target='_blank' download>$file_name</a></p>";
            }
        }

        // Show delete button 
        if ($is_teacher || $row['an_user_id'] == $user_id) {
            $show_delete_btn = "<a class='deleteAn'>Delete</a>";
            $show_three_dots = "<span class='post-show-more'>&#8942;</span>";
        } else {
            $show_delete_btn = "";
            $show_three_dots = "";
        }

        // Show edit button
        if ($row['an_user_id'] == $user_id) {
            $show_edit_btn = "<a class='editAn'>Edit</a>";
            $show_three_dots = "<span class='post-show-more'>&#8942;</span>";
        } else {
            $show_edit_btn = "";
        }

        // Announcement user image URL
        if ($user['user_image_url'] == NULL) {
            $user_image = APP_URL . "storage/profile/defaultAvatar.jpg";
        } else {
            $user_image = $user["user_image_url"];
        }

        $html .= "
            <div class='post' post-id='{$row['an_id']}'>
                <div class='post-header'>
                    $show_three_dots
                    <div id='post-actions' class='post-actions'>
                        $show_edit_btn
                        $show_delete_btn
                    </div>
                    <div class='profile-icon' title='{$user['user_email']}'>
                        <img src='$user_image' alt='Profile' />
                    </div>
                    <div class='post-info'>
                        <div class='post-details'>
                            <p><strong>{$user['user_name']}</strong></p>
                            <p style='font-size:13px;margin-top:3px'>$an_date</p>
                        </div>
                        <div class='an-text'>{$row['an_text']}</div>
                        <div class='an_materials'>$files</div>
                    </div>
                </div>
            </div>
        ";
    }
    $res = [
        'status' => 1,
        'message' => $html
    ];
    echo json_encode($res);
}

if (isset($_POST["make_announcement"])) {
    $html = "";
    $uploaded_files = []; // Initialize the array

    $error = 0;

    if ($_POST['editorContent'] !== "<p></p>") {

        $an_text = $_POST['editorContent'];
        $class_code = $_POST['classCode'];

        if (empty($_FILES['files']['name'][0])) {
            $files = NULL;
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
                            // $html .= "File uploaded successfully: $new_file_name<br>";
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
            // Insert announcement into announcements table
            $class_id = class_id($class_code);
            $announcement_query = "INSERT INTO announcements(an_class_id, an_user_id, an_text) VALUES ('$class_id', '{$_SESSION['user_id']}', '$an_text')";
            $announcement_result = mysqli_query($connection, $announcement_query);
            $new_announcement_id = mysqli_insert_id($connection);

            // Insert each uploaded file into materials table
            if (!empty($uploaded_files)) {
                foreach ($uploaded_files as $file_name) {
                    $announcement_file_query = "INSERT INTO materials(material_class_id, material_user_id, material_an_id, material_file) VALUES ('$class_id', '{$_SESSION['user_id']}',  '$new_announcement_id', '$file_name')";
                    $announcement_file_result = mysqli_query($connection, $announcement_file_query);
                    if (!$announcement_file_result) {
                        $html .= "Failed to insert file record for: $file_name<br>";
                        $error = 1;
                        break;
                    }
                }
            }
            $html .= "Announcement added successfully!";
        }
    } else {
        $html .= "Announcement can't be empty";
        $error = 1;
    }

    $res = [
        'error' => $error,
        'message' => $html
    ];
    echo json_encode($res);
}

if (isset($_POST["edit_announcement"])) {
    $html = "";
    $uploaded_files = []; // Initialize uploaded files array
    $error = 0;

    // Retrieve data from POST
    $an_id = $_POST['an_id'];
    $an_text = $_POST['editorContent'];
    $class_code = $_POST['classCode'];

    // Check if both announcement text and files are empty
    if ($an_text == "<p></p>" && empty($_FILES['updateFiles']['name'][0])) {
        $html .= "Both announcement text and files are empty. Please provide content or files to update.<br>";
        $error = 1;
    }

    // Handle file uploads
    if ($error == 0 && !empty($_FILES['updateFiles']['name'][0])) {
        $files = $_FILES['updateFiles'];
        $time = time();

        foreach ($files['name'] as $key => $file_name) {
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            // Exclude certain file types
            $unallowed_files = ['exe', 'bat', 'sh'];
            if (!in_array($file_extension, $unallowed_files)) {
                // Check file size
                if ($files['size'][$key] < 2000000) {
                    $new_file_name = $time . '_' . $file_name;
                    $file_destination_path = "../storage/files/" . $new_file_name;

                    // Upload file
                    if (move_uploaded_file($files['tmp_name'][$key], $file_destination_path)) {
                        $uploaded_files[] = $new_file_name;
                    } else {
                        $html .= "Failed to upload file: $file_name<br>";
                        $error = 1;
                    }
                } else {
                    $html .= "File size too big for $file_name. Should be less than 2MB.<br>";
                    $error = 1;
                }
            } else {
                $html .= "File type not allowed for $file_name. Cannot be 'exe', 'bat', 'sh'.<br>";
                $error = 1;
            }
        }
    }

    // Update announcement text if provided
    if ($error == 0 && isset($an_text)) {

        $an_text = mysqli_real_escape_string($connection, $an_text);
        $announcement_query = "UPDATE announcements SET an_text = '$an_text', an_updated_at = NOW() WHERE an_id = '$an_id'";
        $announcement_result = mysqli_query($connection, $announcement_query);

        if (!$announcement_result) {
            $html .= "Failed to update announcement.<br>";
            $error = 1;
        }
    }

    // Handle uploaded files (insert/update in materials table)
    if ($error == 0 && !empty($uploaded_files)) {
        foreach ($uploaded_files as $file_name) {
            $file = substr($file_name, strpos($file_name, '_') + 1); // Extract original file name from timestamped name

            // Check if material exists
            $check_material_query = "SELECT material_id FROM materials WHERE material_class_id = '$class_code' AND material_an_id = '$an_id' AND SUBSTRING_INDEX(material_file, '_', -1) = '$file'";
            $check_material_result = mysqli_query($connection, $check_material_query);

            if (mysqli_num_rows($check_material_result) > 0) {
                // Update existing material
                $material_data = mysqli_fetch_assoc($check_material_result);
                $material_id = $material_data['material_id'];
                $update_material_query = "UPDATE materials SET material_file = '$file_name' WHERE material_id = '$material_id'";
                $update_material_result = mysqli_query($connection, $update_material_query);

                if (!$update_material_result) {
                    $html .= "Failed to update file record for: $file_name<br>";
                    $error = 1;
                    break; // Exit loop on first error
                }
            } else {
                // Insert new material
                $insert_material_query = "INSERT INTO materials (material_class_id, material_an_id, material_file) VALUES ('$class_code', '$an_id', '$file_name')";
                $insert_material_result = mysqli_query($connection, $insert_material_query);

                if (!$insert_material_result) {
                    $html .= "Failed to insert file record for: $file_name<br>";
                    $error = 1;
                    break; // Exit loop on first error
                }
            }
        }
    }

    // Prepare response
    if ($error == 0) {
        $response = [
            'error' => 0,
            'message' => 'Announcement updated successfully!',
        ];
    } else {
        $response = [
            'error' => 1,
            'message' => $html,
        ];
    }

    echo json_encode($response);
    exit; // Ensure script stops execution after echoing response
}

if (isset($_POST['delete_announcement'])) {

    $an_id = $_POST['an_id'];
    $html = "";

    $delete_an_query = "DELETE FROM announcements WHERE an_id = '$an_id'";
    $delete_an_result = mysqli_query($connection, $delete_an_query);

    $image_query = "SELECT material_file FROM materials WHERE material_an_id = '$an_id'";
    $image_result = mysqli_query($connection, $image_query);
    if (mysqli_num_rows($image_result) > 0) {
        while ($image = mysqli_fetch_assoc($image_result)) {
            unlink('../storage/files/' . $image['material_file']);
        }
    }

    $delete_material_query = "DELETE FROM materials WHERE material_an_id = '$an_id'";
    $delete_material_result = mysqli_query($connection, $delete_material_query);

    if ($delete_an_result && $delete_material_result) {
        $html .= "Announcement deleted!";
        $status = 1;
    } else {
        $html .= "Something went wrong!";
        $status = 0;
    }

    $res = [
        "status" => $status,
        "message" => $html
    ];
    echo json_encode($res);
}

if (isset($_POST["fetch_people"])) {

    $teacher_details = "";
    $student_details = "";
    $class_id = class_id($_POST['classCode']);
    $user_id = $_SESSION['user_id'];

    $fetch_teacher_id_query = "SELECT * FROM classes WHERE class_id = '$class_id'";
    $fetch_teacher_id_result = mysqli_query($connection, $fetch_teacher_id_query);
    $fetched_teacher = mysqli_fetch_assoc($fetch_teacher_id_result);
    $teacher_id = $fetched_teacher["class_teacher_id"];

    $fetch_teacher_query = "SELECT * FROM users where user_id = '$teacher_id'";
    $fetch_teacher_result = mysqli_query($connection, $fetch_teacher_query);
    $teacher = mysqli_fetch_assoc($fetch_teacher_result);

    if ($teacher["user_image_url"] == NULL)
        $teacher_user_image = APP_URL . "storage/profile/defaultAvatar.jpg";
    else
        $teacher_user_image = $teacher["user_image_url"];

    $teacher_details .=
        "    
            <li class='teacher-list'>
                <div class='profile-icon' title='{$teacher['user_email']}'>
                    <img src='$teacher_user_image' alt='Profile' />
                </div>
                <p>{$teacher['user_name']}</p>
            </li>
        ";


    $fetch_enrollments_id_query = "SELECT * FROM enrollments where enrollment_class_id = '$class_id'";
    $fetch_enrollments_id_result = mysqli_query($connection, $fetch_enrollments_id_query);
    if (mysqli_num_rows($fetch_enrollments_id_result) > 0) {
        while ($fetched_students = mysqli_fetch_assoc($fetch_enrollments_id_result)) {
            $student_id = $fetched_students["student_id"];

            $fetch_student_query = "SELECT * FROM users where user_id = '$student_id'";
            $fetch_student_result = mysqli_query($connection, $fetch_student_query);
            $student = mysqli_fetch_assoc($fetch_student_result);

            if ($student["user_image_url"] == NULL)
                $student_user_image = APP_URL . "storage/profile/defaultAvatar.jpg";
            else
                $student_user_image = $student["user_image_url"];

            $student_details .=
                "    
                    <li class='teacher-list'>
                        <div class='profile-icon' title='{$student['user_email']}'>
                            <img src='$student_user_image' alt='Profile' />
                        </div>
                        <p>{$student['user_name']}</p>
                    </li>
                ";

        }
    } else {
        $student_details .= "<li class='teacher-list'>There is no student in this class</li>";
    }

    $res = [
        'teacher' => $teacher_details,
        'student' => $student_details,
        'status' => 1
    ];

    echo json_encode($res);
}

if (isset($_POST['fetch_cw'])) {
    $html = "";
    $class_id = class_id($_POST['classCode']);
    $user_id = $_SESSION['user_id'];

    // Check if the user is a teacher of the class
    $check_teacher_query = "SELECT * FROM classes WHERE class_id = '$class_id' AND class_teacher_id = '$user_id'";
    $check_teacher_result = mysqli_query($connection, $check_teacher_query);
    $is_teacher = mysqli_num_rows($check_teacher_result) > 0;

    $select_assignments_query = "SELECT * FROM assignments WHERE asgn_class_id = '$class_id' ORDER BY asgn_id DESC";
    $select_assignments = mysqli_query($connection, $select_assignments_query);

    while ($row = mysqli_fetch_assoc($select_assignments)) {
        $asgn_date = post_date($row['asgn_created_at']);

        // Show edit and delete button 
        if ($is_teacher) {
            $show_three_dots =
                "
                <span class='cw-show-more'>&#8942;</span>
                <div class='cw-actions'>
                    <a class='editCw'>Edit</a>
                    <a class='deleteCw'>Delete</a>
                </div>
            ";
        } else {
            $show_three_dots = "";
        }

        $html .=
            "       
           
            <div class='cw' cw-type='asgn' cw-id='{$row['asgn_id']}'>
                <div class='cw-header'>
                    $show_three_dots
                    <span>
                    <i class='fa-regular fa-file-lines'></i>
                        <a href='submit.php?c=" . $_POST['classCode'] . "&" . "a=" . md5($row['asgn_id']) . "'>
                            <span>{$row['asgn_title']}</span>
                        </a> 
                    </span>
                    <span style='font-size:12px'>Posted $asgn_date</span>
                </div>
            </div>
                                
        ";
    }
    $res = [
        'status' => 1,
        'message' => $html
    ];
    echo json_encode($res);
}


if (isset($_POST["create_cw"])) {
    $html = "";
    $uploaded_files = []; // Initialize the array

    $error = 0;

    if ($_POST['cw_title'] !== "") {

        $cw_title = $_POST['cw_title'];

        $cw_description = $_POST['cw_Content'];
        if ($cw_description == '<p></p>')
            $cw_description = "";

        $cw_points = $_POST['cw_points'];
        $cw_due_date = $_POST['cw_DueDate'];
        $class_code = $_POST['classCode'];

        if (empty($_FILES['cwFiles']['name'][0])) {
            $files = NULL;
        } else {
            $files = $_FILES['cwFiles']['name'];
            $time = time();
            $file_temp = $_FILES['cwFiles']['tmp_name'];
            $file_size = $_FILES['cwFiles']['size'];

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
                            // $html .= "File uploaded successfully: $new_file_name<br>";
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
            // Insert assignment into assignment table
            $class_id = class_id($class_code);
            $asgn_query = "INSERT INTO assignments(asgn_class_id, asgn_teacher_id, asgn_title, asgn_description, asgn_points, asgn_due_date) VALUES ('$class_id', '{$_SESSION['user_id']}', '$cw_title', '$cw_description', '$cw_points', '$cw_due_date')";
            $asgn_result = mysqli_query($connection, $asgn_query);
            $new_asgn_id = mysqli_insert_id($connection);

            // Insert each uploaded file into materials table
            if (!empty($uploaded_files)) {
                foreach ($uploaded_files as $file_name) {
                    $asgn_file_query = "INSERT INTO materials(material_class_id, material_user_id, material_asgn_id, material_file) VALUES ('$class_id', '{$_SESSION['user_id']}', '$new_asgn_id', '$file_name')";
                    $asgn_file_result = mysqli_query($connection, $asgn_file_query);
                    if (!$asgn_file_result) {
                        $html .= "Failed to insert file record for: $file_name<br>";
                        $error = 1;
                        break;
                    }
                }
            }
            $html .= "Assignment added successfully!";
        }
    } else {
        $html .= "Assignment Title can't be empty";
        $error = 1;
    }

    $res = [
        'error' => $error,
        'message' => $html
    ];
    echo json_encode($res);
}

if (isset($_POST['delete_cw'])) {

    if ($_POST['cw_type'] == 'asgn') {
        $asgn_id = $_POST['cw_id'];
        $html = "";

        $delete_asgn_query = "DELETE FROM assignments WHERE asgn_id = '$asgn_id'";
        $delete_asgn_result = mysqli_query($connection, $delete_asgn_query);

        $image_query1 = "SELECT material_file FROM materials WHERE material_asgn_id = '$asgn_id'";
        $image_result1 = mysqli_query($connection, $image_query1);
        if (mysqli_num_rows($image_result1) > 0) {
            while ($image = mysqli_fetch_assoc($image_result1)) {
                unlink('../storage/files/' . $image['material_file']);
            }
        }

        $image_query2 = "SELECT sub_file FROM submissions WHERE sub_asgn_id = '$asgn_id'";
        $image_result2 = mysqli_query($connection, $image_query2);
        if (mysqli_num_rows($image_result2) > 0) {
            while ($image = mysqli_fetch_assoc($image_result2)) {
                unlink('../storage/files/' . $image['material_file']);
            }
        }

        $delete_material_query = "DELETE FROM materials WHERE material_asgn_id = '$asgn_id'";
        $delete_material_result = mysqli_query($connection, $delete_material_query);

        $delete_submissions_query = "DELETE FROM submissions WHERE sub_asgn_id = '$asgn_id'";
        $delete_submissions_result = mysqli_query($connection, $delete_submissions_query);

        $delete_grades_query = "DELETE FROM grades WHERE grade_asgn_id = '$asgn_id'";
        $delete_grades_result = mysqli_query($connection, $delete_grades_query);


        if ($delete_asgn_result && $image_result1 && $image_result2 && $delete_material_result && $delete_submissions_result && $delete_grades_result) {
            $html .= "Announcement deleted!";
            $status = 1;
        } else {
            $html .= "Something went wrong!";
            $status = 0;
        }

        $res = [
            "status" => $status,
            "message" => $html
        ];
        echo json_encode($res);
    }

    // if ($_POST['cw_type'] == 'quiz'){}
    // if ($_POST['cw_type'] == 'material'){}
}