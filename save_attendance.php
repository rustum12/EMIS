<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid']) || ($_SESSION['urole'] !== 'Admin' && $_SESSION['urole'] !== 'Teacher')) {
    header("Location: login.php");
    exit();
}
$tid = $_SESSION['userid'];
$check = $conn->query("SELECT * FROM teachers WHERE teacher_id = $tid");
if ($check->num_rows === 0) {
    die("Teacher ID not found in teachers table.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $session_id = $_POST['session_id'];
    $date = $_POST['date'];
    $statuses = $_POST['status'];
    $teacher_id = $_SESSION['userid'];

    foreach ($statuses as $student_id => $status) {
        // Prevent duplicate entries
        $check = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = $student_id AND date = '$date'");
        if (mysqli_num_rows($check) == 0) {
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, teacher_id, class_id, session_id, date, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiss", $student_id, $teacher_id, $class_id, $session_id, $date, $status);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: attendance.php?success=1");
    exit();
} else {
    echo "Invalid request.";
}
