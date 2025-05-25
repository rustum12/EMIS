<?php
session_start();
include 'db.php';


// Allow only Admin and Teacher
if (!isset($_SESSION['userid']) || !in_array($_SESSION['urole'], ['Admin', 'Teacher'])) {
    header("Location: login.php");
    exit();
}

// Sanitize and collect form data
$class_id = intval($_POST['CID']);
$session_id = intval($_POST['session_id']);
$quiz_title = mysqli_real_escape_string($conn, $_POST['quiz_title']);
$teacher_id = $_SESSION['userid']; // Assuming userid refers to teacher/admin

$upload_path = '';
$allowed_ext = ['pdf', 'doc', 'docx'];

if (isset($_FILES['quiz_file']) && $_FILES['quiz_file']['error'] === 0) {
    $file_name = $_FILES['quiz_file']['name'];
    $file_tmp = $_FILES['quiz_file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_ext)) {
        $new_name = 'quiz_' . time() . '.' . $file_ext;
        $upload_path = 'uploads/quizzes/' . $new_name;
        if (!is_dir('uploads/quizzes')) {
            mkdir('uploads/quizzes', 0777, true);
        }
        move_uploaded_file($file_tmp, $upload_path);
    } else {
        $_SESSION['error'] = "Invalid file type. Only PDF, DOC, DOCX allowed.";
        header("Location: create_quiz.php");
        exit();
    }
}

// Insert into database
$insert = mysqli_query($conn, "INSERT INTO quizzes (CID, session_id, quiz_title, file_path, created_by, created_at)
VALUES ($class_id, $session_id, '$quiz_title', '$upload_path', $teacher_id, NOW())");

if ($insert) {
    $_SESSION['success'] = "Quiz created successfully.";
} else {
    $_SESSION['error'] = "Failed to save quiz: " . mysqli_error($conn);
}

header("Location: create_quiz.php");
exit();
?>
