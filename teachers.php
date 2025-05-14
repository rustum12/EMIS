<?php 
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

 

// Fetch teachers from database
$sql = "SELECT * FROM teachers";
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
                <h2 class="text-center">All Teachers</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="7" class="text-right">
                                <a href="teacher.php" class="btn btn-success">Add New</a>
                            </th>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Job Status</th>
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
                                <?php if (!empty($row['photo']) && file_exists($row['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Photo" width="60" height="60" style="object-fit: cover;">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['designation']); ?></td>
                            <td><?php echo htmlspecialchars($row['job_status']); ?></td>
                            <td>
                                <a href="teacher.php?id=<?php echo htmlspecialchars($row['teacher_id']); ?>">Edit</a>  
                                
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
