<?php
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
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
                    <h2 class="text-center">Welcome <?=$_SESSION['uname']?></h2>
                     
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>