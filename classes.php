<?php
include 'db.php';
 
// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle toggle status
if (isset($_GET['toggle'])) {
    $CID = intval($_GET['toggle']);
    $stmt = $conn->prepare("SELECT class_status FROM classes WHERE CID = ?");
    $stmt->bind_param("i", $CID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $newStatus = ($row['class_status'] == 'inactive') ? 'active' : 'inactive';
        $updateStmt = $conn->prepare("UPDATE classes SET class_status = ? WHERE CID = ?");
        $updateStmt->bind_param("si", $newStatus, $CID);
        $updateStmt->execute();
    }
}

// Get session data
$session_Array = [];
$sessionsResult = $conn->query("SELECT session_id, session_name FROM sessions");
while ($sRow = $sessionsResult->fetch_assoc()) {
    $session_Array[$sRow['session_id']] = $sRow['session_name'];
}

// Get class data
$result = $conn->query("SELECT * FROM classes");

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
                <h2>All Classes</h2>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th colspan="6"></th>
                            <th><a href="class.php" class="btn btn-success">Add</a></th>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Short</th>
                            <th>Batches</th>
                            <th>Status</th>
                            <th>Edit</th>
                            <th>Toggle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $row['CID'] ?></td>
                            <td><?= htmlspecialchars($row['class_name']) ?></td>
                            <td><?= htmlspecialchars($row['class_short']) ?></td>
                            <td><?= htmlspecialchars($session_Array[$row['session_id']] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['class_status']) ?></td>
                            <td><a href="class.php?id=<?= $row['CID'] ?>">Edit</a></td>
                            <td>
                                <?php if ($row['class_status'] == 'inactive'): ?>
                                    <a href="classes.php?toggle=<?= $row['CID'] ?>" class="text-success">Activate</a>
                                <?php else: ?>
                                    <a href="classes.php?toggle=<?= $row['CID'] ?>" class="text-danger">Inactivate</a>
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
