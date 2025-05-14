<?php
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$class_name = $class_short = $class_status = "";
$update = false;

// Fetch for update
if (isset($_GET['id'])) {
    $CID = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM classes WHERE CID = ?");
    $stmt->bind_param("i", $CID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $class_name = $row['class_name'];
    $class_short = $row['class_short'];
    $class_status = $row['class_status'];
    $class_session_id = $row['session_id'];
    $update = true;
}

// Handle insert or update
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $CID = $_POST['CID'];
    $class_name = $_POST['class_name'];
    $class_short = $_POST['class_short'];
    $class_status = $_POST['class_status'];
    $class_session_id = $_POST['session_id'];

    if ($CID == "") {
        $stmt = $conn->prepare("INSERT INTO classes (class_name, class_short, class_status,session_id) VALUES (?, ?, ?,?)");
        $stmt->bind_param("sssi", $class_name, $class_short, $class_status,$class_session_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("UPDATE classes SET class_name=?, class_short=?, class_status=?, session_id=? WHERE CID=?");
        $stmt->bind_param("ssssi", $class_name, $class_short, $class_status, $class_session_id, $CID);
        $stmt->execute();
    }

    header("Location: classes.php");
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
                <h2 class="text-center">
 	<?= $update ? 'Update Class' : 'Add Class' ?></h2>
    <form method="POST" action="">
        <input type="hidden" name="CID" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">

        <div class="mb-3">
            <label for="class_name" class="form-label">Class Name</label>
            <input type="text" class="form-control" id="class_name" name="class_name" value="<?= htmlspecialchars($class_name) ?>" required>
        </div>

        <div class="mb-3">
            <label for="class_short" class="form-label">Class Short</label>
            <input type="text" class="form-control" id="class_short" name="class_short" value="<?= htmlspecialchars($class_short) ?>" required>
        </div>
 <div class="mb-3">
            <label for="session_id" class="form-label">Batch</label>
							
							
							<select name="session_id" id="session_id" required class="form-select" >
    <option value="">Select Session</option>
    <?php while($row = mysqli_fetch_assoc($sessions_result)) {
				$selected = '';
				if($class_session_id == $row['session_id'])
						$selected = 'selected="selected"';
			?>
        <option value="<?php echo $row['session_id']; ?>"  <?php echo $selected; ?>>
            <?php echo $row['session_name']; ?>
        </option>
    <?php } ?>
</select>
	 </div>
        <div class="mb-3">
            <label for="class_status" class="form-label">Status</label>
            <select class="form-select" id="class_status" name="class_status">
                <option value="active" <?= $class_status == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $class_status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
             </select>
        </div>

        <button type="submit" class="btn btn-primary"><?= $update ? 'Update' : 'Add' ?> Class</button>
    </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
