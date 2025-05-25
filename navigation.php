<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="logo.PNG" alt="Logo" style="height: 40px;"> LMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                   
				   
				   
				   <?php
				  	 if (!isset($_SESSION['userid'])) {
				   ?>
				   
				   <li class="nav-item">
    <a class="nav-link" href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
</li>
				<?php
					}
					else {
				   ?>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['uname'] ?>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="profile.php">
        <i class="fas fa-id-badge me-1"></i> Profile
    </a>
</li>			

<li class="nav-item">
    <a class="nav-link" href="index.php?action=logout">
        <i class="fas fa-id-badge me-1"></i> Logout
    </a>
</li>		<?php
					}
				?>	
                    <li class="nav-item">
    <a class="nav-link" href="#">
        <i class="fas fa-user-shield me-1"></i> Privacy
    </a>
</li>
                </ul>
            </div>
        </div>
    </nav>