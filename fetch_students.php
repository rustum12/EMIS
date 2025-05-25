<?php
include 'db.php';

if (isset($_GET['class_id']) && isset($_GET['session_id'])) {
    $class_id = intval($_GET['class_id']);
    $session_id = intval($_GET['session_id']);

    $query = "
   SELECT s.*
FROM students s
INNER JOIN classes c ON s.class = c.CID
WHERE s.status != 'deleted'
AND c.CID = $class_id
AND c.session_id = $session_id
";

    $students = mysqli_query($conn, $query);

    if (mysqli_num_rows($students) > 0) {
        echo "<h5 class='mt-4'>Student List:</h5>";
        echo "<table class='table table-bordered mt-2'>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";
        while ($student = mysqli_fetch_assoc($students)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($student['student_name']) . "</td>";
            echo "<td>
                    <select name='status[{$student['student_id']}]' class='form-control'>
                        <option value='Present'>Present</option>
                        <option value='Absent'>Absent</option>
                        <option value='Late'>Late</option>
                    </select>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='text-danger'>No students found for this class and session.</p>";
    }
} else {
    echo "<p class='text-warning'>Invalid request.</p>";
}
?>
