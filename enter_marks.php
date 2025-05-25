<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Teacher') {
    die("Unauthorized");
}

$teacher_id = $_SESSION['userid'];

// Get session/class from query
$session_id = $_GET['session_id'] ?? '';
$class_id = $_GET['class_id'] ?? '';

// Get all sessions
$sessions = $conn->query("SELECT session_id, session_name FROM sessions ORDER BY session_id DESC")->fetch_all(MYSQLI_ASSOC);

// Get classes assigned to teacher in selected session
$classes = [];
if ($session_id) {
    $stmt = $conn->prepare("SELECT tc.class_id, c.class_name 
                            FROM teacher_classes tc 
                            JOIN classes c ON tc.class_id = c.CID 
                            WHERE tc.teacher_id = ? AND c.session_id = ?");
    $stmt->bind_param("ii", $teacher_id, $session_id);
    $stmt->execute();
    $classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get students
$students = [];
if ($class_id && $session_id) {
    $stmt = $conn->prepare("SELECT student_id, student_name FROM students 
                            WHERE class = ? AND session_id = ?");
    $stmt->bind_param("ii", $class_id, $session_id);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Enter Marks</h2>

    <form method="get" action="">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Select Session</label>
                <select name="session_id" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Select Session --</option>
                    <?php foreach ($sessions as $s): ?>
                        <option value="<?= $s['session_id'] ?>" <?= ($s['session_id'] == $session_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['session_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Select Class</label>
                <select name="class_id" class="form-select" onchange="this.form.submit()" <?= $session_id ? '' : 'disabled' ?> required>
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?= $cls['class_id'] ?>" <?= ($cls['class_id'] == $class_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cls['class_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <?php if ($class_id && $session_id && $students): ?>
        <form method="post" action="save_marks.php">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="session_id" value="<?= $session_id ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Exam Type</label>
                    <select name="exam_type" class="form-select" required>
                        <option value="Midterm">Midterm</option>
                        <option value="Final">Final</option>
                        <option value="Monthly">Monthly</option>
                    </select>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['student_name']) ?></td>
                        <td>
                            <input type="hidden" name="student_ids[]" value="<?= $student['student_id'] ?>">
                            <input type="number" name="marks[]" min="0" max="100" class="form-control" required>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary mt-3">Save Marks</button>
        </form>
    <?php elseif ($class_id && $session_id): ?>
        <div class="alert alert-warning">No students found in this class for selected session.</div>
    <?php endif; ?>
</div>
</body>
</html>
