<?php
include 'db.php';

if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Toggle status (active/inactive)
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $currentStatus = $_GET['status'] ?? '';

    // Toggle logic
    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

    $stmt = $conn->prepare("UPDATE sessions SET status = ? WHERE session_id = ?");
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();
    header("Location: batches.php");
    exit();
}

// Fetch non-deleted sessions
$result = $conn->query("SELECT * FROM sessions WHERE status != 'deleted'");

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
                <h2>All Batches</h2>
                <table class="table table-bordered">
                    <tr>
                        <th colspan="6"></th>
                        <th><a href="batch.php" class="btn btn-success">+ Add</a></th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>Batch</th>
                        <th>Starting Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th colspan="2">Actions</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['session_id'] ?></td>
                        <td><?= htmlspecialchars($row['session_name']) ?></td>
                        <td><?= htmlspecialchars($row['starting_date']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['remarks']) ?></td>
                        <td>
                            <a href="batch.php?id=<?= $row['session_id'] ?>">Edit</a>
                        </td>
                        <td>
                            <a href="batches.php?toggle=<?= $row['session_id'] ?>&status=<?= $row['status'] ?>"
                               onclick="return confirm('Are you sure you want to <?= $row['status'] === 'active' ? 'inactivate' : 'activate' ?> this session?')">
                               <?= $row['status'] === 'active' ? 'Inactivate' : 'Activate' ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
