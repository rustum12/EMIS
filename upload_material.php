<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid']) || (($_SESSION['urole'] !== 'Teacher') && ($_SESSION['urole'] !== 'Admin'))) {
    die("Unauthorized access");
}

$teacher_id = $_SESSION['userid'];

// Fetch sessions
$sessions = $conn->query("SELECT session_id, session_name FROM sessions ORDER BY session_id DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch classes assigned to this teacher
$classes = $conn->prepare("SELECT DISTINCT c.CID, c.class_name, c.session_id 
                           FROM teacher_classes tc 
                           JOIN classes c ON tc.class_id = c.CID 
                           WHERE tc.teacher_id = ?");
$classes->bind_param("i", $teacher_id);
$classes->execute();
$class_result = $classes->get_result();
$all_classes = $class_result->fetch_all(MYSQLI_ASSOC);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $subject = $_POST['subject'];
    $session_id = $_POST['session_id'];
    $class_id = $_POST['class_id'];

    $uploadDir = 'uploads/materials/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($_FILES['material_file']['error'] === 0) {
        $fileName = basename($_FILES['material_file']['name']);
        $targetFilePath = $uploadDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES['material_file']['tmp_name'], $targetFilePath);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO study_materials (teacher_id, session_id, class_id, subject, title, description, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissss", $teacher_id, $session_id, $class_id, $subject, $title, $description, $targetFilePath);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Study material uploaded successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to upload material.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>File upload error.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Study Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Upload Study Material</h2>

    <?= $message ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Batch</label>
            <select name="session_id" class="form-select" required>
                <option value="">-- Select Batch --</option>
                <?php foreach ($sessions as $s): ?>
                    <option value="<?= $s['session_id'] ?>"><?= htmlspecialchars($s['session_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Class</label>
            <select name="class_id" class="form-select" required>
                <option value="">-- Select Class --</option>
                <?php foreach ($all_classes as $cls): ?>
                    <option value="<?= $cls['CID'] ?>"><?= htmlspecialchars($cls['class_name']) ?> (Session <?= $cls['session_id'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Select File</label>
            <input type="file" name="material_file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar" required>
        </div>

        <button type="submit" class="btn btn-success">Upload</button>
    </form>
</div>
</body>
</html>
