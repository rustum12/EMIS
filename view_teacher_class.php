<?php 
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

 

// Fetch teacher-class assignments
$query = "
    SELECT 
        tc.teacher_id, 
        tc.class_id, 
        tc.session_id, 
        t.teacher_name, 
        c.class_name, 
        s.session_name 
    FROM 
        teacher_classes tc 
    JOIN 
        teachers t ON tc.teacher_id = t.teacher_id 
    JOIN 
        classes c ON tc.class_id = c.CID 
    JOIN 
        sessions s ON tc.session_id = s.session_id 
    WHERE 
        tc.status = 'Active'
";
$result = $conn->query($query);
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
			                <a href="teacher_class.php" class="btn btn-success" >Assign Classes to Teachers</a>

 <div class="container">
    <h2 class="text-center">Assigned Classes</h2>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Session</th>
                                <th>Class Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                                        <td><?= htmlspecialchars($row['session_name']) ?></td>
                                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No records found!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

             </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
