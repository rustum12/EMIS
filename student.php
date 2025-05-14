<?php 
include('db.php');
$message = '';
$editing = false;
 
// Default student array for form values
$student = [
    'student_name' => '', 'father_name' => '', 'email' => '', 'phone' => '',
    'address' => '', 'gender' => '', 'dob' => '', 'class' => '', 'status' => 'registered',
    'city' => '', 'state' => '', 'session_id' => '', 'photo' => '',
    'student_cnic' => '', 'father_cnic' => ''
];

// Login check
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch for edit
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $id");
    $student = mysqli_fetch_assoc($res);
    if ($student) {
        $editing = true;
		
    } else {
        $message = "Student not found!";
    }
}
// Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$pid = isset($_POST['pid']) ? ($_POST['pid']) : false;
    foreach ($student as $key => $value) {
        $student[$key] = mysqli_real_escape_string($conn, $_POST[$key] ?? '');
    }

    // Handle photo
    if (!empty($_FILES['photo']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['photo']['tmp_name']);
        $fileSize = $_FILES['photo']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $message .= "Only image files (JPG, PNG, GIF) are allowed.<br>";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $message .= "Photo must be less than 2MB.<br>";
        } else {
            // Generate unique file name
            $photoName = time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/$photoName");

            // If editing, delete the old photo if it exists
            if ($editing &&  file_exists("uploads/" . $pid)) {
                @unlink("uploads/" . $pid);
            }

            $student['photo'] = $photoName;
        }
    } 
	elseif ($editing) {
        // If no new photo uploaded, retain the existing photo
        $student['photo'] = $pid;
    }
	 

    // Validations
    if (!preg_match("/^[a-zA-Z ]{3,50}$/", $student['student_name'])) {
        $message .= "Invalid student name.<br>";
    }
    if (!empty($student['student_cnic']) && !preg_match("/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/", $student['student_cnic'])) {
        $message .= "Invalid Student CNIC format.<br>";
    }
    if (!empty($student['father_cnic']) && !preg_match("/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/", $student['father_cnic'])) {
        $message .= "Invalid Father CNIC format.<br>";
    }
    if (!empty($student['city']) && !preg_match("/^[a-zA-Z ]{3,50}$/", $student['city'])) {
        $message .= "Invalid City name.<br>";
    }
    if (!empty($student['state']) && !preg_match("/^[a-zA-Z ]{3,50}$/", $student['state'])) {
        $message .= "Invalid State name.<br>";
    }
    if (!empty($student['dob'])) {
        $dobDate = strtotime($student['dob']);
        $minDate = strtotime("-5 years");
        if ($dobDate > $minDate) {
            $message .= "Student must be at least 5 years old.<br>";
        }
    }

    // Uniqueness checks
    if (empty($message)) {
        $checkEmail = mysqli_query($conn, "SELECT student_id FROM students WHERE email = '{$student['email']}' AND student_id != $id");
        if (mysqli_num_rows($checkEmail) > 0) {
            $message .= "Email already exists.<br>";
        }

        $checkCNIC = mysqli_query($conn, "SELECT student_id FROM students WHERE student_cnic = '{$student['student_cnic']}' AND student_id != $id");
        if (mysqli_num_rows($checkCNIC) > 0) {
            $message .= "Student CNIC already exists.<br>";
        }
    }

    // Insert or Update
    if (empty($message)) {
        if ($id > 0) {
           $query = "UPDATE students SET 
                student_name='{$student['student_name']}', father_name='{$student['father_name']}',
                email='{$student['email']}', phone='{$student['phone']}', address='{$student['address']}',
                gender='{$student['gender']}', dob='{$student['dob']}', class='{$student['class']}',
                status='{$student['status']}', city='{$student['city']}', state='{$student['state']}',
                session_id='{$student['session_id']}', photo='{$student['photo']}',
                student_cnic='{$student['student_cnic']}', father_cnic='{$student['father_cnic']}'
                WHERE student_id = $id";
            $run = mysqli_query($conn, $query);
            $message = $run ? "Student updated successfully." : "Update failed.";
        } else {
            $query = "INSERT INTO students 
                (student_name, father_name, email, phone, address, gender, dob, class, status, city, state, session_id, photo, student_cnic, father_cnic)
                VALUES (
                    '{$student['student_name']}', '{$student['father_name']}', '{$student['email']}', '{$student['phone']}',
                    '{$student['address']}', '{$student['gender']}', '{$student['dob']}', '{$student['class']}', 
                    '{$student['status']}', '{$student['city']}', '{$student['state']}', '{$student['session_id']}',
                    '{$student['photo']}', '{$student['student_cnic']}', '{$student['father_cnic']}')";
            $run = mysqli_query($conn, $query);
            $message = $run ? "New student added successfully." : "Insertion failed.";
        }
    }
}
$sessions_result = mysqli_query($conn, "SELECT * FROM sessions");
$classes_result2 = mysqli_query($conn, "SELECT * FROM classes WHERE session_id = '{$student['session_id']}'");
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
                <h3 class="text-center"><?= $editing ? 'Update' : 'Add' ?> Student</h3>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                        <input type="hidden" name="pid" value="<?= $student['photo'] ?>">
                    <?php endif; ?>
                    <table class="table table-bordered">
                        <tr>
                            <td>Name:</td>
                            <td><input type="text" name="student_name" value="<?= $student['student_name'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>Father Name:</td>
                            <td><input type="text" name="father_name" value="<?= $student['father_name'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td><input type="email" name="email" value="<?= $student['email'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Phone:</td>
                            <td><input type="text" name="phone" value="<?= $student['phone'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td><textarea name="address"><?= $student['address'] ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Gender:</td>
                            <td>
                                <select name="gender">
                                    <option value="">--Select--</option>
                                    <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>DOB:</td>
                            <td><input type="date" name="dob" value="<?= $student['dob'] ?>"></td>
                        </tr>
						 <tr>
                            <td>Batch:</td>
                            <td> 
							
							
							
							<select name="session_id" id="session_id" required onchange="fetchClasses(this.value)">
    <option value="">Select Batch</option>
    <?php while($row = mysqli_fetch_assoc($sessions_result)) {
				$selected = '';
				if($student['session_id'] == $row['session_id'])
						$selected = 'selected="selected"';
			?>
        <option value="<?php echo $row['session_id']; ?>"  <?php echo $selected; ?>>
            <?php echo $row['session_name']; ?>
        </option>
    <?php } ?>
</select>
							
							
							
							</td>
                        </tr>
                        <tr>
                            <td>Class:</td>
                            <td>							<div id="class-container">
    <select name="class" id="class" required>
        <option value="">Select class</option>
        <?php 
        while($rowd = mysqli_fetch_assoc($classes_result2)) {
            $selected = ($student['class'] == $rowd['CID']) ? 'selected="selected"' : '';
        ?>
            <option value="<?php echo $rowd['CID']; ?>" <?php echo $selected; ?>>
                <?php echo $rowd['class_name']; ?> (<?php echo $rowd['class_short']; ?>)
            </option>
        <?php } ?>
    </select>
</div>
							
							</td>
                        </tr>
                        <tr>
                            <td>Student CNIC:</td>
                            <td><input type="text" name="student_cnic" value="<?= $student['student_cnic'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Father CNIC:</td>
                            <td><input type="text" name="father_cnic" value="<?= $student['father_cnic'] ?>"></td>
                        </tr>
                        <tr>
                            <td>City:</td>
                            <td><input type="text" name="city" value="<?= $student['city'] ?>"></td>
                        </tr>
                        <tr>
                            <td>State:</td>
                            <td><input type="text" name="state" value="<?= $student['state'] ?>"></td>
                        </tr>
                       
                       <?php if ($_SESSION['urole'] == 'Admin'): ?>
    <tr>
        <td><label>Status:</label></td>
        <td>
            <select name="status"  required>
                <?php
                    foreach ($statusStudents as $status):
                ?>
                    <option value="<?php echo $status; ?>" <?php echo ($student['status'] == $status) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($status); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
<?php endif; ?>
                        <tr>
                            <td>Photo:</td>
                            <td>
                                <input type="file" name="photo" >
                                <?php if ($editing && $student['photo']): ?>
                                    <br><img src="uploads/<?= $student['photo'] ?>" width="80">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <button type="submit" class="btn btn-primary"><?= $editing ? 'Update' : 'Add' ?> Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
  function fetchClasses(sessionId) {
    if (sessionId === '') {
        document.getElementById('class-container').innerHTML = '<select name="class" id="class" required><option value="">Select class</option></select>';
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax.php", true); // Send to ajax.php instead of same file
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (this.status === 200) {
            document.getElementById("class-container").innerHTML = this.responseText;
        }
    };
    xhr.send("session_id=" + encodeURIComponent(sessionId)); // Correct POST data format
}

    </script><?php include 'footer.php'; ?>
