<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid']) || ($_SESSION['urole'] !== 'Admin' && $_SESSION['urole'] !== 'Teacher')) {
    header("Location: login.php");
    exit();
}

include 'header.php';
include 'navigation.php';

$filter_name = isset($_GET['student_name']) ? $_GET['student_name'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build dynamic query
$filter_query = "WHERE a.status IS NOT NULL";
if (!empty($filter_name)) {
    $filter_query .= " AND s.student_name LIKE '%" . mysqli_real_escape_string($conn, $filter_name) . "%'";
}
if (!empty($filter_status)) {
    $filter_query .= " AND a.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM attendance a
              JOIN students s ON a.student_id = s.student_id
              $filter_query";
$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch data
$sql = "SELECT a.*, s.student_name, c.class_name, se.session_name
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN classes c ON a.class_id = c.CID
        JOIN sessions se ON a.session_id = se.session_id
        $filter_query
        ORDER BY a.date DESC
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
    <h4 class="mb-4">📆 Attendance History</h4>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="student_name" class="form-control" placeholder="Search by student name" value="<?= htmlspecialchars($filter_name) ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">-- All Statuses --</option>
                <option value="Present" <?= $filter_status == 'Present' ? 'selected' : '' ?>>Present</option>
                <option value="Absent" <?= $filter_status == 'Absent' ? 'selected' : '' ?>>Absent</option>
                <option value="Leave" <?= $filter_status == 'Leave' ? 'selected' : '' ?>>Leave</option>
                <option value="Late" <?= $filter_status == 'Late' ? 'selected' : '' ?>>Late</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Student Name</th>
                <th>Class</th>
                <th>Session</th>
                <th>Date</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                        <td><?= htmlspecialchars($row['session_name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['remarks']) ?></td>
                        <td><?= htmlspecialchars($row['recorded_at']) ?></td>
                        <td>
                            <form action="send_attendance_email.php" method="post">
                                <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                <input type="hidden" name="date" value="<?= $row['date'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Send Email</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<!-- to be modified-->
        

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?student_name=<?= urlencode($filter_name) ?>&status=<?= $filter_status ?>&page=<?= $page - 1 ?>">Prev</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?student_name=<?= urlencode($filter_name) ?>&status=<?= $filter_status ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?student_name=<?= urlencode($filter_name) ?>&status=<?= $filter_status ?>&page=<?= $page + 1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include 'footer.php'; ?>
