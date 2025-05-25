<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid']) || ($_SESSION['urole'] !== 'Admin' && $_SESSION['urole'] !== 'Teacher')) {
    die("Unauthorized");
}
$tid = $_SESSION['userid'];
$check = $conn->query("SELECT * FROM teachers WHERE teacher_id = $tid");
if ($check->num_rows === 0) {
    die("Teacher ID not found in teachers table.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $date = $_POST['date'];

    $query = "SELECT a.*, s.student_name, s.father_name, s.email, s.phone, s.address
              FROM attendance a
              JOIN students s ON a.student_id = s.student_id
              WHERE a.student_id = ? AND a.date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $student_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || empty($row['email'])) {
        die("Invalid record or missing parent email.");
    }

    $to = $row['email'];
    $subject = 'Attendance Update for ' . $row['student_name'];
    $message = "
Dear Parent of {$row['student_name']},

Attendance record for {$date}:
Status: {$row['status']}
Remarks: {$row['remarks']}

Regards,
EMIS System
";

    $headers = "From: emis@yourdomain.com\r\n"; // Change this to your actual domain email if available

    if (mail($to, $subject, $message, $headers)) {
        echo "<script>alert('Email sent successfully using mail()'); window.history.back();</script>";
    } else {
        echo "<script>alert('Email sending failed'); window.history.back();</script>";
    }
}
?>
