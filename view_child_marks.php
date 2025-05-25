<?php
session_start();
require 'db.php';

// Secure session check for parent
if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Parents') {
    header("Location: login.php");
    exit();
}

$parent_cnic = getParentCNIC($_SESSION['userid']); // Get parent's CNIC

// Fetch children (students where father_cnic = parent CNIC)
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
$marks_data = [];

if ($selected_student_id) {
    $query = "
        SELECT 
            m.subject_name, 
            m.marks_obtained, 
            m.total_marks, 
            m.exam_type, 
            m.exam_date, 
            u.uname AS evaluated_by
        FROM marks m
        JOIN users u ON m.teacher_id = u.uid
        WHERE m.student_id = ?
        ORDER BY m.exam_date DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selected_student_id);
    $stmt->execute();
    $marks_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Helper: Get parent's CNIC
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
    <title>📚 Child Marks</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">
    <h2>📈 View Child Marks</h2>

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

    <!-- Marks Table -->
    <?php if ($selected_student_id): ?>
        <h3>Marks Record</h3>
        <table border="1" cellpadding="10">
            <tr>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Total Marks</th>
                <th>Exam Type</th>
                <th>Date</th>
                <th>Evaluated By</th>
            </tr>
            <?php if (count($marks_data) > 0): ?>
                <?php foreach ($marks_data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
                        <td><?= htmlspecialchars($row['total_marks']) ?></td>
                        <td><?= htmlspecialchars($row['exam_type']) ?></td>
                        <td><?= htmlspecialchars($row['exam_date']) ?></td>
                        <td><?= htmlspecialchars($row['evaluated_by']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No marks found for this student.</td></tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
