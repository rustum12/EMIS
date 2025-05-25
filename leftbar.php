<h3 class="mb-3"><i class="fas fa-compass me-2"></i>Navigation</h3>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
        </a>
    </li>

    <?php if (isset($_SESSION['userid']) && ($_SESSION['urole'] === 'Parents' or $_SESSION['urole'] === 'Admin')) { ?>
        <li class="nav-item">
            <a class="nav-link" href="view_child_attendance.php">
                <i class="fas fa-calendar-check me-2 text-info"></i> View Child Attendance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="view_child_marks.php">
                <i class="fas fa-clipboard-list me-2 text-info"></i> View Child Marks
            </a>
        </li>
        <!---<li class="nav-item">
            <a class="nav-link" href="parent_communication.php">
                <i class="fas fa-comments me-2 text-info"></i> Communicate with Teachers/Admins
            </a>
        </li>--->
    <?php } ?>

    <?php if (isset($_SESSION['userid']) && in_array($_SESSION['urole'], ['Admin', 'Student'])) { ?>
        <li class="nav-item">
            <a class="nav-link" href="view_materials.php">
                <i class="fas fa-book-reader me-2 text-warning"></i> View Materials
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="submit_assignment.php">
                <i class="fas fa-upload me-2 text-success"></i> Submit Assignment
            </a>
        </li>
    <?php } ?>

    <?php if (isset($_SESSION['userid']) && in_array($_SESSION['urole'], ['Admin', 'Teacher'])) { ?>
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
            <a class="nav-link" href="attendance.php">
                <i class="fas fa-clipboard-check me-2 text-success"></i>5. Mark Attendance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="attendance_history.php">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>6. Attendance History
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="enter_marks.php">
                <i class="fas fa-pen-nib me-2 text-info"></i>7. Enter Marks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="upload_material.php">
                <i class="fas fa-book-open me-2 text-warning"></i>8. Upload Study Materials
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="create_quiz.php">
                <i class="fas fa-question-circle me-2 text-danger"></i>9. Create Quiz
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="quiz_results.php">
                <i class="fas fa-chart-bar me-2 text-secondary"></i>10. Quiz Performance
            </a>
        </li>
    <?php } ?>

    <?php if (isset($_SESSION['userid']) && $_SESSION['urole'] === 'Admin') { ?>
        <li class="nav-item">
            <a class="nav-link" href="teacher_class.php">
                <i class="fas fa-chalkboard-teacher me-2 text-danger"></i>11. Assign Classes to Teachers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="teachers.php">
                <i class="fas fa-user-tie me-2 text-success"></i>12. Manage Teachers
            </a>
        </li>
    <?php } ?>
</ul>
