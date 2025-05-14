<h3 class="mb-3"><i class="fas fa-compass me-2"></i>Navigation</h3>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
        </a>
    </li>
	<?php
		if (isset($_SESSION['userid']) and $_SESSION['urole'] == 'Admin') {
	?>
    <li class="nav-item">
        <a class="nav-link" href="users.php">
            <i class="fas fa-users-cog me-2 text-success"></i>1. Manage Users
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="batches.php">
            <i class="fas fa-layer-group me-2 text-danger"></i>2. Manage Batches
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="classes.php">
            <i class="fas fa-chalkboard me-2 text-warning"></i>3. Manage Classes
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="students.php">
            <i class="fas fa-user-graduate me-2 text-info"></i>4. Manage Students
        </a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="teachers.php">
        <i class="fas fa-chalkboard-teacher me-2 text-danger"></i>5. Manage Teachers
    </a>
</li>
    <li class="nav-item">
    <a class="nav-link" href="teacher_class.php">
        <i class="fas fa-chalkboard-teacher me-2 text-danger"></i>6. Assign Classes to Teachers
    </a>
</li>
<?php
		}
?>
</ul>
