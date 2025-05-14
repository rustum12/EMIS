<?php
include('db.php');

if (isset($_POST['session_id'])) {
    $session_id = intval($_POST['session_id']);
    $query = "SELECT * FROM classes WHERE session_id = $session_id";
    $result = mysqli_query($conn, $query);

    echo '<select name="class" id="class" required>';
    echo '<option value="">Select class</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['CID'] . '">' . $row['class_name'] . ' (' . $row['class_short'] . ')</option>';
    }
    echo '</select>';
}
?>