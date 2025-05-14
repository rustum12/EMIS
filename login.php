<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uemail = trim($_POST['uemail']);
    $upassword = $_POST['upassword'];
    
    // Validate input
    if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Check user in DB
        $sql = "SELECT * FROM users WHERE uemail = ? AND status = 'active'";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $uemail);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                if (password_verify($upassword, $row['upassword'])) {
                    $_SESSION['userid'] = $row['uid'];
                    $_SESSION['uname'] = $row['uname'];
                    $_SESSION['urole'] = $row['urole'];
                    $_SESSION['CNIC'] = $row['CNIC'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Incorrect password.";
                }
            } else {
                $message = "User not found or inactive.";
            }
        } else {
            $message = "Database error: " . $conn->error;
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


     
                       <h2 class="text-center">Login</h2>
                    <?php if (!empty($message)) echo '<p class="text-danger">' . $message . '</p>'; ?>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="uemail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="upassword" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>