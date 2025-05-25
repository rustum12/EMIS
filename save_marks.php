<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Teacher') {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id']);
    $subject = trim($_POST['subject']);
    $exam_type = $_POST['exam_type'];
    $student_ids = $_POST['student_ids'] ?? [];
    $marks_arr = $_POST['marks'] ?? [];

    if (!$class_id || !$subject || !$exam_type || empty($student_ids) || count($student_ids) !== count($marks_arr)) {
        die("Invalid input");
    }

    $stmt = $conn->prepare("INSERT INTO marks (student_id, class_id, subject, exam_type, marks) VALUES (?, ?, ?, ?, ?)");

    foreach ($student_ids as $index => $student_id) {
        $marks = $marks_arr[$index];

        if (!is_numeric($marks)) continue; // Skip invalid marks

        $stmt->bind_param("iissi", $student_id, $class_id, $subject, $exam_type, $marks);
        $stmt->execute();
    }

    echo "<script>alert('Marks saved successfully!'); window.location.href='enter_marks.php';</script>";
}
?>
