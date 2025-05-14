<?php
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle status toggle request
if (isset($_GET['toggle_id'])) {
    $toggle_id = intval($_GET['toggle_id']);
    
    if ($_SESSION['userid'] == $toggle_id) {
        $message = "You cannot change your own status.";
    } else {
        // Get current status
        $check = $conn->prepare("SELECT status FROM users WHERE uid = ?");
        $check->bind_param("i", $toggle_id);
        $check->execute();
        $res = $check->get_result();
        $user = $res->fetch_assoc();

        if ($user) {
            $new_status = ($user['status'] === 'inactive') ? 'active' : 'inactive';
            $update = $conn->prepare("UPDATE users SET status = ? WHERE uid = ?");
            $update->bind_param("si", $new_status, $toggle_id);
            if ($update->execute()) {
                $message = "User status updated to $new_status.";
            } else {
                $message = "Failed to update user status.";
            }
        }
    }
}

// Fetch users from database (excluding status 'deleted')
$sql = "SELECT * FROM users WHERE status != 'deleted'";
$result = $conn->query($sql);

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
                <h2 class="text-center">All Users</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="5">&nbsp;</th>
                            <th colspan="2" style="text-align:right"><a href="user.php" class="btn btn-success">Add New</a></th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email/CNIC</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 0; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= ++$sn ?></td>
                                <td><?= htmlspecialchars($row['uname']) ?></td>
                                <td><?= htmlspecialchars($row['uemail']) ?><br><?= htmlspecialchars($row['CNIC']) ?></td>
                                <td><?= htmlspecialchars($row['urole']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><a href="user.php?id=<?= $row['uid'] ?>">Edit</a></td>
                                <td>
                                    <?php if ($row['uid'] != $_SESSION['userid']): ?>
                                        <a href="users.php?toggle_id=<?= $row['uid'] ?>" 
                                           onclick="return confirm('Are you sure you want to <?= $row['status'] === 'inactive' ? 'activate' : 'inactivate' ?> this user?')">
                                            <?= $row['status'] === 'inactive' ? 'Activate' : 'Inactivate' ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
