<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['userid'];

// Fetch classes and sessions for dropdowns (optional)
$classes = $conn->query("SELECT * FROM classes");
$sessions = $conn->query("SELECT * FROM sessions");

// Handle submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment'])) {
    $class_id = $_POST['class_id'];
    $session_id = $_POST['session_id'];
    $title = $_POST['title'];
    $file = $_FILES['assignment'];

    $uploadDir = 'uploads/assignments/';
    $filename = uniqid() . "_" . basename($file['name']);
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO submissions (student_id, class_id, session_id, title, filename, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiss", $student_id, $class_id, $session_id, $title, $filename);
        $stmt->execute();
        $msg = "✅ Assignment submitted successfully!";
    } else {
        $msg = "❌ Upload failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Assignment</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">
    <h2>📝 Submit Assignment</h2>
    <?php if (isset($msg)) echo "<p>$msg</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Class:</label><br>
        <select name="class_id" required>
            <?php while ($c = $classes->fetch_assoc()): ?>
                <option value="<?= $c['CID'] ?>"><?= $c['class_name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Session:</label><br>
        <select name="session_id" required>
            <?php while ($s = $sessions->fetch_assoc()): ?>
                <option value="<?= $s['session_id'] ?>"><?= $s['session_name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Upload File:</label><br>
        <input type="file" name="assignment" required><br><br>

        <button type="submit">📤 Submit</button>
    </form>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
