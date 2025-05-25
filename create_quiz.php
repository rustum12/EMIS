<?php
include 'db.php';
//session_start();

// Allow only Admin and Teacher
if (!isset($_SESSION['userid']) || !in_array($_SESSION['urole'], ['Admin', 'Teacher'])) {
    header("Location: login.php");
    exit();
}

include 'header.php';
include 'navigation.php';
?>

<div class="container mt-4">
    <div class="card p-4">
        <h3><i class="fas fa-question-circle text-danger me-2"></i>Create Quiz</h3>
        <form method="post" action="save_quiz.php" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label>Select Class</label>
                <select name="CID" class="form-control" required>
                    <option value="">-- Select Class --</option>
                    <?php
                    $class_query = mysqli_query($conn, "SELECT * FROM classes WHERE class_status = 'active'");
                    while ($row = mysqli_fetch_assoc($class_query)) {
                        echo "<option value='{$row['CID']}'>{$row['class_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Select Session</label>
                <select name="session_id" class="form-control" required>
                    <option value="">-- Select Session --</option>
                    <?php
                    $session_query = mysqli_query($conn, "SELECT * FROM sessions WHERE status = 'active'");
                    while ($row = mysqli_fetch_assoc($session_query)) {
                        echo "<option value='{$row['session_id']}'>{$row['session_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Quiz Title</label>
                <input type="text" name="quiz_title" class="form-control" placeholder="e.g. Chapter 1 Quiz" required>
            </div>

            <div class="form-group mb-3">
                <label>Quiz File (optional - PDF/Word)</label>
                <input type="file" name="quiz_file" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Create Quiz</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
