<?php
session_start();
require 'db.php';

// Check if parent is logged in
//if (!isset($_SESSION['userid']) ) {
    //header("Location: login.php");
  //  exit();
//}

$parent_cnic = getParentCNIC($_SESSION['userid']); // Get parent's CNIC

// Fetch children (students linked via father_cnic)
$childQuery = $conn->prepare("SELECT student_id, student_name FROM students WHERE father_cnic = ?");
$childQuery->bind_param("s", $parent_cnic);
$childQuery->execute();
$childResult = $childQuery->get_result();

$children = [];
while ($row = $childResult->fetch_assoc()) {
    $children[] = $row;
}

// If child is selected
$selected_student_id = $_GET['student_id'] ?? '';
$attendance_data = [];

if ($selected_student_id) {
    $query = "
        SELECT 
            a.date, a.status, c.class_name, s.session_name, u.uname AS marked_by
        FROM attendance a
        JOIN classes c ON a.class_id = c.CID
        JOIN sessions s ON a.session_id = s.session_id
        JOIN users u ON a.teacher_id = u.uid
        WHERE a.student_id = ?
        ORDER BY a.date DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selected_student_id);
    $stmt->execute();
    $attendance_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Helper: get parent's CNIC
function getParentCNIC($uid) {
    global $conn;
    $stmt = $conn->prepare("SELECT CNIC FROM users WHERE uid = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['CNIC'] ?? '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📊 Child Attendance</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">
    <h2>📋 View Child Attendance</h2>

    <!-- Child Selection -->
    <form method="GET">
        <label>Select Child:
            <select name="student_id" onchange="this.form.submit()">
                <option value="">-- Select --</option>
                <?php foreach ($children as $child): ?>
                    <option value="<?= $child['student_id'] ?>" <?= $child['student_id'] == $selected_student_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($child['student_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <!-- Attendance Table -->
    <?php if ($selected_student_id): ?>
        <h3>Attendance Record</h3>
        <table border="1" cellpadding="10">
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Class</th>
                <th>Session</th>
                <th>Marked By</th>
            </tr>
            <?php if (count($attendance_data) > 0): ?>
                <?php foreach ($attendance_data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                        <td><?= htmlspecialchars($row['session_name']) ?></td>
                        <td><?= htmlspecialchars($row['marked_by']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No attendance records found.</td></tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
