<?php
	include_once("../class/users.class.php");
	extract($_POST);
	extract($_GET);

	if(!isset($_SESSION["username"])){
		header("location:index.php");
	}
	
	$username = $_SESSION["username"];
	$users = new users();
	$userInfo = $users->getUserInfo($username);

?>
<div class="row" style="margin-bottom:0px">
	<div class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="col-xs-4">
				<a class="navbar-brand" href="dashboard.php" style="padding-top:5px">
			    	<img alt="MDC" src="../resources/images/full-logo.png" height="65px" width="auto">
			   	</a>
			</div>
		</div>
	</div>
</div>

<!-- Collect the nav links, forms, and other content for toggling -->
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<div class="row">
	<div class="navbar navbar-default" style="margin-top:10px;padding:0;border-radius:0">
		<div class="container-fluid no-pad">
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		    	<ul class="nav navbar-nav">
		    		<?php if($current_page == "dashboard.php"){ ?>
		        		<li class="active"><a href="dashboard.php">Home</a></li>
		        	<?php } else { ?>
		        		<li><a href="dashboard.php">Home</a></li>
		        	<?php } ?>

		        	<!-- <?php if($current_page == "dashboard_full.php"){ ?>
		        		<li class="active"><a href="dashboard_full.php">Tabular Dashboard</a></li>
		        	<?php } else { ?>
		        		<li><a href="dashboard_full.php">Tabular Dashboard</a></li>
		        	<?php } ?> -->

		        	<!-- ONLY ALLOW ADMIN TO VIEW THIS PAGE -->
		        	<?php if($userInfo["permissionLevel"] <= 2){ ?>
		        		<li class="dropdown">
			          		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Utilities <span class="caret"></span></a>
			          		<ul class="dropdown-menu">
			          			<!-- Only show upload utility to admins -->
			            		<?php if($userInfo["permissionLevel"] == 1){ ?>
			            		<li><a href="upload-csv.php"><span class="glyphicon glyphicon-upload"></span> Upload New Data</a></li>
			            		<?php } ?>
			            		<!-- Admin and biologist views -->
			            		<li><a href="download-csv.php"><span class="glyphicon glyphicon-download"></span> Download Records</a></li>
			            		<li><a href="creel-db.php"><span class="glyphicon glyphicon-eye-open"></span> View Raw Database</a></li>
			          		</ul>
			        	</li>
			        	<!-- Only show upload utility to admins -->
	            		<?php if($userInfo["permissionLevel"] == 1){ ?>
	        				<li class="dropdown">
	        					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage Users <span class="caret"></span></a>
	        					<ul class="dropdown-menu">
	        						<li><a href="user-list.php"><span class="glyphicon glyphicon-th-list"></span> User List</a></li>
	        						<li><a href="add-user.php"><span class="glyphicon glyphicon-plus"></span> Add User</a></li>
	        					</ul>
	        				</li>
	        				<li class="dropdown">
	        					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage Projects <span class="caret"></span></a>
	        					<ul class="dropdown-menu">
	        						<li><a href="project-list.php"><span class="glyphicon glyphicon-th-list"></span> Project List</a></li>
	        						<li><a href="add-project.php"><span class="glyphicon glyphicon-plus"></span> Add Project</a></li>
	        					</ul>
	        				</li>
	        			<?php } ?>
	        		<?php } ?>
		      	</ul>
		      	<ul class="nav navbar-nav navbar-right">
		      		<li class="dropdown">
			        	<a href="#">My Account <span class="caret"></span></a>
			        	<ul class="dropdown-menu">
				        	<li><a href="view-profile.php"><span class="glyphicon glyphicon-user"></span> View Profile</a></li>
				        	<li><a href="change-password.php"><span class="glyphicon glyphicon-pencil"></span> Change Password</a></li>
				        	<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
			        	</ul>
		        	</li>
		      	</ul>
		    </div><!-- /.navbar-collapse -->
		</div>
	</div>
</div> 