<?php
session_start();
require 'db.php';

// Secure session check for student
if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$logged_in_uid = $_SESSION['userid'];

// Step 1: Get CNIC of the logged-in student
$cnic = '';
$cnic_stmt = $conn->prepare("SELECT CNIC FROM users WHERE uid = ?");
$cnic_stmt->bind_param("i", $logged_in_uid);
$cnic_stmt->execute();
$cnic_result = $cnic_stmt->get_result();

if ($cnic_result->num_rows > 0) {
    $row = $cnic_result->fetch_assoc();
    $cnic = $row['CNIC'];
} else {
    die("Student CNIC not found.");
}
$cnic_stmt->close();

// Filters
$status_filter = $_GET['status'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Step 2: Main query using CNIC
$query = "
SELECT 
    a.date, 
    a.status, 
    c.class_name, 
    s.session_name, 
    t.uname AS marked_by,
    stu.CNIC AS student_cnic
FROM attendance a
JOIN classes c ON a.class_id = c.CID
JOIN sessions s ON a.session_id = s.session_id
JOIN users t ON a.teacher_id = t.uid -- teacher info
JOIN students st ON a.student_id = st.student_id
JOIN users stu ON st.student_cnic = stu.CNIC -- student info (you)
WHERE stu.CNIC = ?
";

$params = [$cnic];
$types = "s"; // CNIC is a string

// Append filters
if ($status_filter !== '') {
    $query .= " AND a.status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

if ($from_date !== '') {
    $query .= " AND a.date >= ?";
    $types .= "s";
    $params[] = $from_date;
}

if ($to_date !== '') {
    $query .= " AND a.date <= ?";
    $types .= "s";
    $params[] = $to_date;
}

$query .= " ORDER BY a.date DESC";

// Execute query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Stats for chart
$stats = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0];
$attendance_data = [];

while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
    $stats[$row['status']] = ($stats[$row['status']] ?? 0) + 1;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>My Attendance</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h2>📅 My Attendance Record</h2>

    <!-- Filter Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <label>Status:
            <select name="status">
                <option value="">All</option>
                <option value="Present" <?= $status_filter == 'Present' ? 'selected' : '' ?>>Present</option>
                <option value="Absent" <?= $status_filter == 'Absent' ? 'selected' : '' ?>>Absent</option>
                <option value="Late" <?= $status_filter == 'Late' ? 'selected' : '' ?>>Late</option>
                <option value="Excused" <?= $status_filter == 'Excused' ? 'selected' : '' ?>>Excused</option>
            </select>
        </label>
        <label>From:
            <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
        </label>
        <label>To:
            <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
        </label>
        <button type="submit">Filter</button>
        <a href="view_attendance.php">Reset</a>
    </form>

    <!-- Attendance Table -->
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

    <!-- Chart -->
    <div style="max-width: 400px; margin: 40px auto;">
    <canvas id="attendanceChart"></canvas>
</div>

    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent', 'Late', 'Excused'],
                datasets: [{
                    label: 'Attendance Summary',
                    data: [
                        <?= $stats['Present'] ?>,
                        <?= $stats['Absent'] ?>,
                        <?= $stats['Late'] ?>,
                        <?= $stats['Excused'] ?>
                    ],
                    backgroundColor: ['#4caf50', '#f44336', '#ff9800', '#2196f3']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
