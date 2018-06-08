<?php
include_once("../class/users.class.php");
include_once("../class/projects.class.php");
include_once("../class/roles.class.php");
include_once("../class/projectUser.class.php");
include_once("../class/utilities.class.php");

$utilities = new utilities();
$userUID = $utilities->randomNumber();

session_start();
extract($_POST);
extract($_GET);

$username = $_SESSION["username"];
$users = new users();
$userInfo = $users->getUserInfo($username);
if($userInfo["permissionLevel"] != 1){
	header("location: dashboard.php");
}

if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]) 
	&& isset($_POST["password"]) && isset($_POST["roleID"])){
	$fname = $_POST["fname"];
	$lname = $_POST["lname"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$roleID = $_POST["roleID"];
	$projects = null;
	if(isset($_POST["projects"])){
		$projects = $_POST["projects"];
	}

			// Hash-Salt password
	$salt = hash("sha256", strval(rand()));
	$saltedPassword = $password . $salt;
	$hashedSaltedPassword = hash("sha256", $saltedPassword);

	$db=new connect();
	$db->db1->beginTransaction();
	$insert = false;
	try{
		$insertUser = $users->insertNewUser($db,$userUID,$fname,$lname,$email,$hashedSaltedPassword,$salt,1,$roleID);

		// If user wasn't inserted throw exception
		if(!$insertUser){
			throw new Exception("Unable to insert user");
		} else if($insertUser && ($projects != null && count($projects) > 0)){
			$projectUser = new projectUser();
			$insertProjectUser = $projectUser->insertIntoProjectUser($db, $projects, $userUID);
			if(!$insertProjectUser){
				throw new Exception("Unable to insert to project-user table");
				return;
			}
		}
		$db->db1->commit();
		$insert = true;
		echo json_encode($insert);
	} catch(PDOException $e){
		$db->db1->rollBack();
		$insert = false;
		echo json_encode($insert);
	} catch(Exception $e){
		$db->db1->rollBack();
		$insert = false;
		echo json_encode($insert);
	}
	return;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Add New User</title>

	<!-- EXTERNAL CSS-->
	<?php include_once("../includes/css.php") ?>

	<style>
		.invalid{
			background-color:#d25656; 
			color:white; 
			text-align:center; 
			margin-top:0; 
			margin-bottom:15px;
		}
		.valid{
			background-color:#5e68f3; 
			color:white; 
			text-align:center; 
			margin-top:0; 
			margin-bottom:15px;
		}
		.main-panel{
			width:70%;
		}
		.project-list{
			height:225px;
			overflow-y:auto;
		}
		a.link{
			cursor:pointer;
			font-weight:normal;
		}
	</style>
</head>
<!-- STYLE -->
<body>
	<?php include_once('../includes/navbar.php') ?>

	<div class="container-fluid main-panel">
		<div class="panel panel-default" style="border:0; margin-bottom:50px">
			<div class="panel-heading" style="text-align:center;">
				<h3 style="margin:5px;" class="text-center bold">Add New User</h3>
			</div>
			<div id="successful" class="row valid hidden">
				<h5>Successfully registered! Wait for confirmation</h5>
			</div>
			<div id="unsuccessful" class="row invalid hidden">
				<h5>Unable to register account! Contact the administrator</h5>
			</div>
			<div id="invalid" class="row invalid hidden">
				<h5>Please fill out all the fields!</h5>
			</div>
			<div id="password_error" class="row invalid hidden">
				<h5>Passwords do not match</h5>
			</div>
			<div class="panel-body" style="margin-bottom:0">
				<div class="row">
					<div class="col-md-8">
						<div class="row form-group">
							<div class="col-md-6 fname" style="padding-left:0">
								<input id="fname" type="text" class="form-control" placeholder="First Name" autofocus required>
							</div>
							<div class="col-md-6 lname" style="padding-right:0">
								<input id="lname" type="text" class="form-control" placeholder="Last Name" required>
							</div>
						</div>
						<div class="row form-group">
							<input id="email" type="text" class="form-control" placeholder="Email Address" required>
						</div>
						<div class="row form-group">
							<input id="password1" type="password" class="form-control" placeholder="Password" required>
						</div>
						<div class="row form-group">
							<input id="password2" type="password" class="form-control" placeholder="Re-enter Password" required>
						</div>
						<div class="row form-group">
							<select id="role" class="form-control" required>
								<option value="na">Select Role</option>
								<?php 
								$roles = new roles();
								$roleList = $roles->getAllRoles();

								for($i=0; $i<count($roleList); $i++){
									echo "<option value='".$roleList[$i]["roleID"]."'>".$roleList[$i]["roleName"]."</option>";
								}
								?>
							</select>
						</div>
					</div>
					<!-- PROJECT PANEL -->
					<div class="col-md-4">
						<div class="row">
							<label for="projectList">Assign Projects (<a class='link' onclick="selectAll()">All</a> | <a class='link' onclick="selectNone()">None</a>)</label>
						</div>
						<div class="row project-list">
							<?php
							$projects = new projects();
							$projectList = $projects->getProjectList();

							for($i=0; $i<count($projectList);$i++){
								echo "<div class='project-check checkbox' style='font-size:15px'>";
								echo "<label><input type='checkbox' style='width:15px; height:15px;' value='".$projectList[$i]["projectUID"]."'>".$projectList[$i]["projectName"]."</label>";
								echo "</div>";
							}
							?>
						</div>
					</div>
				</div>
				<div class="row form-group" style="padding-left:15px">
					<button type="button" class="btn btn-success" onclick="addUser()">Add User</button>
					<a href="user-list.php"><button type="button" class="btn btn-danger" >Cancel</button></a>
				</div>
			</div> <!-- END PANEL BODY -->
		</div> <!-- END PANEL -->
	</div> <!-- END CONTAINER-FLUID -->

	<div class="navbar-fixed-bottom">
		<?php include_once("../includes/footer.php"); ?>
	</div>

	<!-- EXTERNAL JAVASCRIPT -->
	<?php include_once('../includes/js.php'); ?>

	<!-- INTERNAL JAVASCRIPT -->
	<script type="text/javascript">
		var checkall = false;

		// Assign Projects depending on role
		$("#role").change(function(){
			var role = $("#role option:selected").val();
			if(role == 1){
				$('input[type=checkbox]').prop('disabled',false);
				if(!checkall){
					selectAll();
				}
			} else {
				$('input[type=checkbox]').prop('disabled',false);
				if(role == 3){
					$('input[type=checkbox]').prop('disabled',true);
				}
				selectNone();
			}
		});

		// Select functions
		function selectAll(){
			$(".project-check").find("input[type=checkbox]").prop("checked",true);
			checkall = true;
		}

		function selectNone(){
			$(".project-check").find("input[type=checkbox]").prop("checked",false);
			checkall = false;
		}

		// Send ajax post
		function addUser(){
			var fname = $("#fname").val().trim();
			var lname = $("#lname").val().trim();
			var email = $("#email").val().trim();
			var passwd1 = $("#password1").val();
			var passwd2 = $("#password2").val();
			var roleID = $("#role option:selected").val();

			var projects = [];
			var count = 0;
			$(".checkbox").each(function(){
				if($(this).find("input[type=checkbox]").prop("checked")){
					projects[count] = $(this).find("input[type=checkbox]").val();
					count++;
				}
			});

			// Validate empty fields
			if(fname == "" || lname == "" || email == "" || passwd1 == "" || passwd2 == "" || roleID == "na"){
				$("#invalid").removeClass("hidden");
				return;
			} else {
				$("#invalid").addClass("hidden");

				// Validate passwords
				if(passwd1 != passwd2){
					$("#password_error").removeClass("hidden");
					return;
				} else{
					$("#password_error").addClass("hidden");
				}
			}

			// Insert into database
			var data = {
				fname: fname,
				lname: lname,
				email: email,
				password: passwd1,
				roleID: roleID,
				projects: projects
			}
			
			// Sends to top of page
			$.ajax({
				data:data,
				method:"POST",
				dataType:"json",
				// dataType: "text",
				success:function(result){
					console.log(JSON.stringify(result));
					// console.log(result);
					if(result){
						$("#successful").removeClass("hidden");
						$("#unsuccessful").addClass("hidden");
					} else {
						$("#unsuccessful").removeClass("hidden");
						$("#successful").addClass("hidden");
					}
				},
				error:function(xhr,status,error){
					console.log("XHR: " + JSON.stringify(xhr));
					console.log("Status: " + JSON.stringify(status));
					console.log("Error: " + JSON.stringify(error));
				}
			});
		}
	</script>
</body>
</html>