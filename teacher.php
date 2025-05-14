<?php
include('db.php');
$message = '';

// Check login
if (!isset($_SESSION['userid']) or $_SESSION['urole'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$teacher_id = 0;
$teacher_name = "";
$cnic = "";
$photo = "";
$designation = "";
$highest_qualification = "";
$bps = "";
$department = "";
$subject = "";
$job_nature = "Contract";
$joining = "";
$job_status = "Active";
$status = 1;
$update = false;

// Save Teacher (for adding new teacher)
if (isset($_POST['save'])) {
    $teacher_name = $_POST['teacher_name'];
    $cnic = $_POST['cnic'];
    $designation = $_POST['designation'];
    $highest_qualification = $_POST['highest_qualification'];
    $bps = $_POST['bps'];
    $department = $_POST['department'];
    $subject = $_POST['subject'];
    $job_nature = $_POST['job_nature'];
    $joining = $_POST['joining'];
    $job_status = $_POST['job_status'];

    // Check for duplicate CNIC
    $check = mysqli_query($conn, "SELECT * FROM teachers WHERE cnic = '$cnic'");
    if (mysqli_num_rows($check) > 0) {
        $message .= "CNIC already exists. Cannot add duplicate teacher.";
    }

    // Handle photo upload
    $photo = "";
    if (!empty($_FILES['photo']['name'])) {
        $photo = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    // Insert new teacher
    $query = "INSERT INTO teachers (teacher_name, cnic, photo, designation, highest_qualification, bps, department, subject, job_nature, joining, job_status, status) 
              VALUES ('$teacher_name', '$cnic', '$photo', '$designation', '$highest_qualification', '$bps', '$department', '$subject', '$job_nature', '$joining', '$job_status', '$status')";
    mysqli_query($conn, $query);

    // Redirect to the edit mode to update details
    $teacher_id = mysqli_insert_id($conn); // Get the last inserted teacher's ID
    header('Location: teacher.php?id=' . $teacher_id);
    exit();
}

// Update Teacher (when editing)
if (isset($_POST['update'])) {
    $teacher_id = $_POST['teacher_id'];
    $teacher_name = $_POST['teacher_name'];
    $cnic = $_POST['cnic'];
    $designation = $_POST['designation'];
    $highest_qualification = $_POST['highest_qualification'];
    $bps = $_POST['bps'];
    $department = $_POST['department'];
    $subject = $_POST['subject'];
    $job_nature = $_POST['job_nature'];
    $joining = $_POST['joining'];
    $job_status = $_POST['job_status'];

    // Check for duplicate CNIC
    $check = mysqli_query($conn, "SELECT * FROM teachers WHERE cnic = '$cnic' AND teacher_id != '$teacher_id'");
    if (mysqli_num_rows($check) > 0) {
        $message .= "CNIC already exists. Cannot update teacher with duplicate CNIC.";
    }

    // Update photo if new photo is uploaded
    $photo_query = "";
    if (!empty($_FILES['photo']['name'])) {
        // Remove the existing photo from the folder if found
        $result = mysqli_query($conn, "SELECT photo FROM teachers WHERE teacher_id = '$teacher_id'");
        $row = mysqli_fetch_assoc($result);
        if ($row['photo'] && file_exists($row['photo'])) {
            unlink($row['photo']); // Delete the old photo
        }
        // Upload new photo
        $photo = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
        $photo_query = ", photo='$photo'";
    }

    // Update teacher details
    $query = "UPDATE teachers SET 
                teacher_name = '$teacher_name',
                cnic = '$cnic',
                designation = '$designation',
                highest_qualification = '$highest_qualification',
                bps = '$bps',
                department = '$department',
                subject = '$subject',
                job_nature = '$job_nature',
                joining = '$joining',
                job_status = '$job_status'
                $photo_query
              WHERE teacher_id = '$teacher_id'";
    mysqli_query($conn, $query);

    header('Location: teacher.php?id=' . $teacher_id);
    exit();
}

// Fetch teacher data for editing
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    $update = true;
    $result = mysqli_query($conn, "SELECT * FROM teachers WHERE teacher_id = '$teacher_id'");

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $teacher_name = $row['teacher_name'];
        $cnic = $row['cnic'];
        $photo = $row['photo'];
        $designation = $row['designation'];
        $highest_qualification = $row['highest_qualification'];
        $bps = $row['bps'];
        $department = $row['department'];
        $subject = $row['subject'];
        $job_nature = $row['job_nature'];
        $joining = $row['joining'];
        $job_status = $row['job_status'];
    }
}

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
                <h2><?php echo $update ? 'Update Teacher' : 'Add Teacher'; ?></h2>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">

                    <table>
                        <tr>
                            <td><label>Name:</label></td>
                            <td><input type="text" name="teacher_name" value="<?php echo $teacher_name; ?>" required></td>
                        </tr>
                        <tr>
                            <td><label>CNIC:</label></td>
                            <td><input type="text" name="cnic" value="<?php echo $cnic; ?>" required></td>
                        </tr>
                        <tr>
                            <td><label>Photo:</label></td>
                            <td>
                                <input type="file" name="photo"><br>
                                <?php if ($update && $photo): ?>
                                    <img src="<?php echo $photo; ?>" width="100" height="100"><br>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Designation:</label></td>
                            <td><input type="text" name="designation" value="<?php echo $designation; ?>"></td>
                        </tr>
                        <tr>
                            <td><label>Highest Qualification:</label></td>
                            <td><input type="text" name="highest_qualification" value="<?php echo $highest_qualification; ?>" required></td>
                        </tr>
                        <tr>
                            <td><label>BPS:</label></td>
                            <td><input type="number" name="bps" value="<?php echo $bps; ?>"></td>
                        </tr>
                        <tr>
                            <td><label>Department:</label></td>
                            <td>
                                <select name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Computer Science" <?php if ($department == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                                    <option value="Physics" <?php if ($department == 'Physics') echo 'selected'; ?>>Physics</option>
                                    <option value="Chemistry" <?php if ($department == 'Chemistry') echo 'selected'; ?>>Chemistry</option>
                                    <option value="Mathematics" <?php if ($department == 'Mathematics') echo 'selected'; ?>>Mathematics</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Subject:</label></td>
                            <td><input type="text" name="subject" value="<?php echo $subject; ?>"></td>
                        </tr>
                        <tr>
                            <td><label>Job Nature:</label></td>
                            <td>
                                <select name="job_nature">
                                    <option value="Permanent" <?php if ($job_nature == 'Permanent') echo 'selected'; ?>>Permanent</option>
                                    <option value="Contract" <?php if ($job_nature == 'Contract') echo 'selected'; ?>>Contract</option>
                                    <option value="Visiting" <?php if ($job_nature == 'Visiting') echo 'selected'; ?>>Visiting</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Joining Date:</label></td>
                            <td><input type="date" name="joining" value="<?php echo $joining; ?>" required></td>
                        </tr>
                        <tr>
                            <td><label>Job Status:</label></td>
                            <td>
                                <select name="job_status">
                                    <option value="Active" <?php if ($job_status == 'Active') echo 'selected'; ?>>Active</option>
                                    <option value="Resigned" <?php if ($job_status == 'Resigned') echo 'selected'; ?>>Resigned</option>
                                    <option value="Retired" <?php if ($job_status == 'Retired') echo 'selected'; ?>>Retired</option>
                                    <option value="Suspended" <?php if ($job_status == 'Suspended') echo 'selected'; ?>>Suspended</option>
                                </select>
                            </td>
                        </tr>
                    </table><br>

                    <?php if ($update): ?>
                        <button type="submit" name="update">Update</button>
                    <?php else: ?>
                        <button type="submit" name="save">Save</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
