<?php
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$user = [
    'uid' => '',
    'uname' => '',
    'uemail' => '',
    'urole' => '',
    'status' => 'active'
];
$isEdit = false;
$myID	=	$_SESSION['userid'];
 
// If editing
     $id = $myID;
    $query = "SELECT * FROM users WHERE uid = $id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    

    $isEdit = true;
 

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $isEdit = $id > 0;
	$cnic = mysqli_real_escape_string($conn, $_POST['cnic']);

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
  	$role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = ($_SESSION['urole'] === 'Admin') ? mysqli_real_escape_string($conn, $_POST['status']) : ($isEdit ? $user['status'] : 'active');
	
	// Check if trying to assign Admin to someone who is not the current Admin
$isSelf = ($isEdit && $_SESSION['userid'] == $id);
if ($role === 'Admin' && !$isSelf) {
    $adminCheckQuery = "SELECT COUNT(*) AS admin_count FROM users WHERE urole = 'Admin'";
    $adminCheckResult = mysqli_query($conn, $adminCheckQuery);
    $adminRow = mysqli_fetch_assoc($adminCheckResult);

    if ($adminRow['admin_count'] >= 1) {
        $message .= "Only one Admin is allowed in the system.<br>";
        $role = $user['urole']; // fallback to original role if editing
    }
}
    // Validate
    if (!preg_match("/^[a-zA-Z ]{3,50}$/", $name)) {
        $message .= "Invalid Full Name. Only letters and spaces (3-50 characters).<br>";
    }
	// CNIC validation: 13 digits only
if (!preg_match("/^\d{13}$/", $cnic)) {
    $message .= "Invalid CNIC. It must be exactly 13 digits.<br>";
}
else{
	$cnicCheckQuery = "SELECT uid FROM users WHERE CNIC  = '$cnic'" . ($isEdit ? " AND uid != $id" : "");
    $cnicCheckResult = mysqli_query($conn, $cnicCheckQuery);
    if (mysqli_num_rows($cnicCheckResult) > 0) {
        $message .= "CNIC already exists!<br>";
    }  
}
    $emailCheckQuery = "SELECT uid FROM users WHERE uemail = '$email'" . ($isEdit ? " AND uid != $id" : "");
    $emailCheckResult = mysqli_query($conn, $emailCheckQuery);
    if (mysqli_num_rows($emailCheckResult) > 0) {
        $message .= "Email already exists!<br>";
    } else {
        $passwordQuery = "";
        if (!empty($password)) {
            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,32}$/", $password)) {
                $message .= "Password must be 8-32 characters with uppercase, lowercase, number, and special character.<br>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $passwordQuery = ", upassword = '$hashed_password'";
            }
        }

    

        if (empty($message)) {
            if ($isEdit) {
                $updateQuery = "UPDATE users SET uname = '$name', uemail = '$email', urole = '$role', status = '$status',CNIC = '$cnic' $passwordQuery WHERE uid = $id";
                $result = mysqli_query($conn, $updateQuery);
                $message .= ($result && mysqli_affected_rows($conn) > 0) ? " Profile updated successfully.<br>" : "No changes were made.<br>";
            } else {
                if (empty($passwordQuery)) {
                    $message .= "Password is required for new user.<br>";
                } else {
				$insertQuery = "INSERT INTO users (uname, uemail, urole, status, upassword, CNIC)
                VALUES ('$name', '$email', '$role', '$status', '$hashed_password', '$cnic')";
                    $result = mysqli_query($conn, $insertQuery);
                    $message .= $result ? "New user added successfully.<br>" : "Error adding user.<br>";
                }
            }

            // Refresh user after update
            if ($isEdit) {
                $query = "SELECT * FROM users WHERE uid = $id";
                $result = mysqli_query($conn, $query);
                $user = mysqli_fetch_assoc($result);
            }
        }
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
                <h2 class="text-center"><?= $isEdit ? "Update Profile" : "" ?></h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $_SESSION['userid'] ?>">
                    <?php endif; ?>

                    <table class="table table-bordered">
                        <tr>
                            <td><label>Full Name:</label></td>
                            <td><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['uname']) ?>" required></td>
                        </tr>
                        <tr>
                            <td><label>Email:</label></td>
                            <td><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['uemail']) ?>" required></td>
                        </tr>
                        <tr>
                            <td><label><?= $isEdit ? "New Password (leave blank to keep current):" : "Password:" ?></label></td>
                            <td><input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>></td>
                        </tr>
						<tr>
    <td><label>CNIC/B-Form:</label></td>
    <td><input type="text" name="cnic" class="form-control" value="<?= htmlspecialchars($user['CNIC'] ?? '') ?>" required></td>
</tr>				<?php if ($_SESSION['urole'] != 'Admin'): ?>
    <?php $isSelf = ($_SESSION['userid'] == $user['uid']); ?>
    
    <tr>
        <td><label>Role:</label></td>
        <td>
            <select name="role" class="form-control" required <?php echo $isSelf ? 'disabled' : ''; ?>>
                <?php foreach ($userRole as $role): ?>
                    <option value="<?php echo $role; ?>" <?php echo ($user['urole'] == $role) ? 'selected' : ''; ?>>
                        <?php echo $role; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($isSelf): ?>
                <input type="hidden" name="role" value="<?php echo $user['urole']; ?>">
            <?php endif; ?>
        </td>
    </tr>

    <tr>
        <td><label>Status:</label></td>
        <td>
            <select name="status" class="form-control" required <?php echo $isSelf ? 'disabled' : ''; ?>>
                <option value="">Select One</option>
                <?php foreach ($statusOptions as $status): ?>
                    <option value="<?php echo $status; ?>" <?php echo ($user['status'] == $status) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($status); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($isSelf): ?>
                <input type="hidden" name="status" value="<?php echo $user['status']; ?>">
            <?php endif; ?>
        </td>
    </tr>
<?php endif; ?>

                    </table>

                    <button type="submit" class="btn btn-primary"><?= $isEdit ? "Update Profile" : "Add User" ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
