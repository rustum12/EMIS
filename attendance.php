<?php
include 'db.php';
session_start();

// Check access
if (!isset($_SESSION['userid']) || ($_SESSION['urole'] !== 'Admin' && $_SESSION['urole'] !== 'Teacher')) {
    header("Location: login.php");
    exit();
}

include 'header.php';
include 'navigation.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3 bg-light p-3">
            <?php include 'leftbar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="card p-4">
                <h4>Mark Attendance</h4>
                <form method="post" action="save_attendance.php">
                    <div class="form-group">
                        <label>Select Class:</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            <?php
                            $classes = mysqli_query($conn, "SELECT * FROM classes WHERE class_status = 'active'");
                            while ($class = mysqli_fetch_assoc($classes)) {
                                echo "<option value='{$class['CID']}'>{$class['class_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Session:</label>
                        <select name="session_id" class="form-control" required>
                            <option value="">-- Select Session --</option>
                            <?php
                            $sessions = mysqli_query($conn, "SELECT * FROM sessions WHERE status = 'active'");
                            while ($session = mysqli_fetch_assoc($sessions)) {
                                echo "<option value='{$session['session_id']}'>{$session['session_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Date:</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div id="student-list-area">
   						 <h5 class="mt-4">Student List:</h5>
   						 <p class="text-muted">Please select Class and Session to load students.</p>
					</div>


                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!---AJAX for fetching Students Dynamically--->
<script>
document.querySelector('select[name="class_id"]').addEventListener('change', fetchStudents);
document.querySelector('select[name="session_id"]').addEventListener('change', fetchStudents);

function fetchStudents() {
    const classId = document.querySelector('select[name="class_id"]').value;
    const sessionId = document.querySelector('select[name="session_id"]').value;

    if (classId && sessionId) {
        fetch(`fetch_students.php?class_id=${classId}&session_id=${sessionId}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('student-list-area').innerHTML = html;
            });
    } else {
        document.getElementById('student-list-area').innerHTML = "<p class='text-muted'>Please select Class and Session to load students.</p>";
    }
}
</script>

<?php include 'footer.php'; ?>
