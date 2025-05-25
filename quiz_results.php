<?php
include 'db.php';
session_start();

// Access Control
if (!isset($_SESSION['userid']) || !in_array($_SESSION['urole'], ['Admin', 'Teacher'])) {
    header("Location: login.php");
    exit();
}

include 'header.php';
include 'navigation.php';

// Fetch filter values
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : '';
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : '';
?>

<div class="container mt-4">
    <div class="card p-4">
        <h3><i class="fas fa-chart-bar text-info me-2"></i>Quiz Performance</h3>

        <form method="get" class="row mb-3">
            <div class="col-md-4">
                <label>Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">-- Select Class --</option>
                    <?php
                    $class_q = mysqli_query($conn, "SELECT * FROM classes WHERE class_status = 'active'");
                    while ($row = mysqli_fetch_assoc($class_q)) {
                        $selected = ($row['CID'] == $class_id) ? 'selected' : '';
                        echo "<option value='{$row['CID']}' $selected>{$row['class_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Session</label>
                <select name="session_id" class="form-control" required>
                    <option value="">-- Select Session --</option>
                    <?php
                    $sess_q = mysqli_query($conn, "SELECT * FROM sessions WHERE status = 'active'");
                    while ($row = mysqli_fetch_assoc($sess_q)) {
                        $selected = ($row['session_id'] == $session_id) ? 'selected' : '';
                        echo "<option value='{$row['session_id']}' $selected>{$row['session_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">View Results</button>
            </div>
        </form>

        <?php if ($class_id && $session_id): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Student Name</th>
                    <th>Score</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $results = mysqli_query($conn, "
                    SELECT q.quiz_title, st.student_name, r.score, r.submitted_at
                    FROM quiz_results r
                    INNER JOIN quizzes q ON r.quiz_id = q.quiz_id
                    INNER JOIN students st ON r.student_id = st.student_id
                    WHERE q.class_id = $class_id AND q.session_id = $session_id
                ");

                if (mysqli_num_rows($results) > 0) {
                    while ($row = mysqli_fetch_assoc($results)) {
                        echo "<tr>
                                <td>{$row['quiz_title']}</td>
                                <td>{$row['student_name']}</td>
                                <td>{$row['score']}</td>
                                <td>{$row['submitted_at']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No results found for selected class and session.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
