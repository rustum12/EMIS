<?php
include 'db.php';
// Include necessary files if required (e.g., database connection, session handling)


/////////// Logging Out Starts
$loggedoutMessage = '';
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy session and clear session variables
 

    // Finally, destroy the session
    session_destroy();
	
		$loggedoutMessage	=	"<div class='container mt-5'>
            <div class='alert alert-danger text-center' role='alert'>
                <strong>You have been logged out successfully.</strong><br>
                You will be redirected to the main page in 5 seconds.
            </div>
        </div>";
		   header("Refresh:5; url=index.php");

}



//////////////// Logging Out ends







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
       <?php
	   		if($loggedoutMessage){
				echo $loggedoutMessage;
			}
	   ?>
        <h1>Welcome to the Learning Management System (LMS)</h1>
        <p>The Learning Management System (LMS) is designed to streamline the management of educational institutions. It provides administrators, teachers, students, and parents with a centralized platform to manage attendance, track performance, schedule classes, and facilitate communication.</p>
        <p>Our LMS aims to improve the learning experience by offering real-time updates, efficient resource management, and seamless interaction between all stakeholders. With user-friendly dashboards and automated processes, schools can enhance productivity and focus more on education.</p>
        <p>Key Features of the LMS include:</p>
        <ul>
            <li>Automated attendance tracking</li>
            <li>Performance and grade management</li>
            <li>Class scheduling and event management</li>
            <li>Parent-teacher communication system</li>
            <li>Secure and scalable database for storing academic records</li>
        </ul>
        <p>Explore the system and discover how it can revolutionize school management and enhance the overall learning experience!</p>
        </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>