<?php
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}
 // Initialize variables
$session_name = "";
$starting_date = "";
$status = "active";
$remarks = "";
$id = 0;
$error = "";

// If form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $session_name = trim($_POST['session_name']);
    $starting_date = $_POST['starting_date'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];
    $id = intval($_POST['id']);

    // Check for duplicate session_name (excluding current record if updating)
    $stmt = $conn->prepare("SELECT session_id FROM sessions WHERE session_name = ? AND session_id != ?");
    $stmt->bind_param("si", $session_name, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Error: A session with this name already exists.";
    } else {
        if ($id > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE sessions SET session_name=?, starting_date=?, status=?, remarks=? WHERE session_id=?");
            $stmt->bind_param("ssssi", $session_name, $starting_date, $status, $remarks, $id);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO sessions (session_name, starting_date, status, remarks) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $session_name, $starting_date, $status, $remarks);
        }
        $stmt->execute();
        header("Location: batches.php");
        exit();
    }
}

// Fetch data for update
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM sessions WHERE session_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $session_name = $data['session_name'];
    $starting_date = $data['starting_date'];
    $status = $data['status'];
    $remarks = $data['remarks'];
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
                <h2><?= $id ? 'Edit' : 'Add' ?> Batch</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <table class="table">
                        <tr>
                            <td><label>Batch:</label></td>
                            <td><input type="text" name="session_name" value="<?= htmlspecialchars($session_name) ?>" class="form-control" required></td>
                        </tr>
                        <tr>
                            <td><label>Starting Date</label></td>
                            <td><input type="date" name="starting_date" value="<?= $starting_date ?>" class="form-control" required></td>
                        </tr>
                        <tr>
                            <td><label>Status</label></td>
                            <td>
                                <select name="status" class="form-control">
                                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Remarks</label></td>
                            <td><textarea name="remarks" class="form-control"><?= htmlspecialchars($remarks) ?></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">
                                <button type="submit" class="btn btn-success">Save</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
