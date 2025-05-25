<?php session_start();
// db.php - Database Connection
$servername = "fdb1028.awardspace.net";
$username = "4635387_emis";
$password = "emis1234";
$database = "4635387_emis";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$userRole			= 	array('Admin','Teacher','Student','Parents');
$statusOptions		= 	array('active', 'inactive');
$statusStudents		= 	array('registered', 'admitted', 'banned', 'suspended');
$statusClasses 		= 	['active', 'inactive', 'deleted'];
$sessions_query 	= 	"SELECT session_id, session_name FROM sessions where status='active'";
$sessions_result 	= 	mysqli_query($conn, $sessions_query);

$classes_query = "SELECT 
    c.class_name, 
    c.class_short, 
    c.CID,
	s.session_name
FROM 
    classes c
JOIN 
    sessions s 
ON 
    c.session_id = s.session_id
WHERE 
    c.class_status = 'active' 
    AND s.status = 'active'";
$classes_result = mysqli_query($conn, $classes_query);
$classes_result2 = mysqli_query($conn, $classes_query);
$classes_Array			= 	array();
while($rowarray = mysqli_fetch_assoc($classes_result)) {
$classes_Array[$rowarray['CID']] = $rowarray['class_name']. " (".$rowarray['class_short'].") - ". $rowarray['session_name']; 
}

$q = "SELECT 
   *
FROM 
    sessions
 WHERE 
     
     status = 'active'";
$q_result = mysqli_query($conn, $q);
 $session_Array			= 	array();
while($qarray = mysqli_fetch_assoc($q_result)) {
	$session_Array[$qarray['session_id']] =   $qarray['session_name']; 
}
?>