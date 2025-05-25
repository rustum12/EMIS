<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['userid'];

// Get class/session materials (you can filter by class/session later)
$query = "SELECT m.id, m.title, m.subject, m.description, m.file_path, m.uploaded_at, 
                 c.class_name, s.session_name 
          FROM study_materials m
          JOIN classes c ON m.class_id = c.CID
          JOIN sessions s ON m.session_id = s.session_id
          ORDER BY m.uploaded_at DESC";


$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Materials</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">
    <h2>📚 Study Materials</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Title</th>
            <th>Class</th>
            <th>Session</th>
            <th>File</th>
            <th>Uploaded On</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['class_name']) ?></td>
                <td><?= htmlspecialchars($row['session_name']) ?></td>
                <td><a href="uploads/materials/<?= $row['filename'] ?>" target="_blank">Download</a></td>
                <td><?= htmlspecialchars($row['upload_date']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
