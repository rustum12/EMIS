<?php 
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

 

// === Handle AJAX request to fetch classes ===
if (isset($_POST['fetch_classes'])) {
    $session_id = (int)$_POST['fetch_classes'];

    $classes = $conn->query("SELECT CID, class_name FROM classes WHERE session_id = $session_id AND class_status='active'");

    echo '<select name="class_id[]" class="form-control" required>';
    echo '<option value="">Select Class</option>';
    while($row = $classes->fetch_assoc()) {
        echo '<option value="'.$row['CID'].'">'.$row['class_name'].'</option>';
    }
    echo '</select>';
    exit;
}

// === Handle Form Submission ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_classes'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $session_id = (int)$_POST['session_id'];
    $class_ids = $_POST['class_id'];

    foreach($class_ids as $class_id) {
        $class_id = (int)$class_id;

        // Check duplicate
        $check = $conn->query("SELECT * FROM teacher_classes WHERE teacher_id = $teacher_id AND class_id = $class_id AND session_id = $session_id");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO teacher_classes (class_id, teacher_id, session_id) VALUES ($class_id, $teacher_id, $session_id)");
			echo "<script>alert('Classes assigned successfully!'); window.location.href='".$_SERVER['PHP_SELF']."';</script>";
        }
		else{
				echo "<script>alert('This record alread exists!'); window.location.href='".$_SERVER['PHP_SELF']."';</script>";
		}
    }

    exit;
}

// === Fetch Teachers & Sessions for dropdowns ===
$teachers = $conn->query("SELECT teacher_id, teacher_name FROM teachers WHERE status=0");
$sessions = $conn->query("SELECT session_id, session_name FROM sessions WHERE status='active'");
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
                <a href="view_teacher_class.php" class="btn btn-success" >View All Assignments</a>
<div class="container">
    <h2 class="mb-4">Assign Classes to Teacher</h2>

    <form method="post">
        <input type="hidden" name="assign_classes" value="1">

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Teacher</label>
                <select name="teacher_id" class="form-control" required>
                    <option value="">Select Teacher</option>
                    <?php while($row = $teachers->fetch_assoc()): ?>
                        <option value="<?= $row['teacher_id'] ?>"><?= htmlspecialchars($row['teacher_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label>Batch</label>
                <select name="session_id" id="session_id" class="form-control" required>
                    <option value="">Select Session</option>
                    <?php while($row = $sessions->fetch_assoc()): ?>
                        <option value="<?= $row['session_id'] ?>"><?= htmlspecialchars($row['session_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div id="classes_table" style="display:none;">
            <h4>Assign Classes</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th><button type="button" id="add_row" class="btn btn-success btn-sm">Add Class</button></th>
                    </tr>
                </thead>
                <tbody id="class_rows">
                    <!-- Class dropdowns will appear here -->
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary">Assign Classes</button>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#session_id').change(function() {
        var session_id = $(this).val();
        if(session_id) {
            $.ajax({
                url: '',
                method: 'POST',
                data: {fetch_classes: session_id},
                success: function(data) {
                    $('#classes_table').show();
                    $('#class_rows').html('<tr><td>'+data+'</td><td></td></tr>');

                    $('#add_row').off('click').on('click', function() {
                        $('#class_rows').append('<tr><td>'+data+'</td><td><button type="button" class="btn btn-danger btn-sm remove_row">Remove</button></td></tr>');
                    });

                    $(document).on('click', '.remove_row', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });
        } else {
            $('#classes_table').hide();
            $('#class_rows').empty();
        }
    });
});
</script>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
