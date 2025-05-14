<?php
include 'db.php';
 
// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Toggle student status between active/inactive
if (isset($_GET['toggle_id'])) {
    $toggle_id = intval($_GET['toggle_id']);
    $stmt = $conn->prepare("SELECT status FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $toggle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $newStatus = ($row['status'] == 'inactive') ? 'active' : 'inactive';
        $updateStmt = $conn->prepare("UPDATE students SET status = ? WHERE student_id = ?");
        $updateStmt->bind_param("si", $newStatus, $toggle_id);
        $updateStmt->execute();
        $updateStmt->close();
    }
    $stmt->close();
    header("Location: students.php");
    exit();
}

// Fetch students (excluding 'deleted')
$sql = "SELECT s.*, c.class_name, ss.session_name
        FROM students s
        LEFT JOIN classes c ON s.class = c.CID
        LEFT JOIN sessions ss ON c.session_id = ss.session_id ";
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
                <h2 class="text-center">All Students</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="7" class="text-right">
                                <a href="student.php" class="btn btn-success">Add New</a>
                            </th>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sn = 0;
                            while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= ++$sn ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Photo" width="60" height="60" style="object-fit: cover;">
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['student_name']) ?></td>
                                <td><?= htmlspecialchars($row['father_name']) ?></td>
                                <td><?= htmlspecialchars($row['class_name']) . ' (' . htmlspecialchars($row['session_name']) . ')' ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <a href="student.php?id=<?= $row['student_id'] ?>">Edit</a> 
                                    
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
