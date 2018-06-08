<?php
include_once("../class/users.class.php");
include_once("../class/roles.class.php");
include_once("../class/projects.class.php");
include_once("../class/projectUser.class.php");

session_start();
extract($_POST);
extract($_GET);

	// Validate user and user_edit information
$username = $_SESSION["username"];
$users = new users();
$userInfo = $users->getUserInfo($username);
$userUID = null;
if($userInfo["permissionLevel"] == 1 and $_GET["uid"]){
	$userUID = $_GET["uid"];
	$editUser = $users->getUserByUID($userUID);
	if(count($editUser[0]) == 0){
		header("location: user-list.php");
	}

	// Get ProjectUser List
	$projectUser = new projectUser();
	$projectUserList = $projectUser->getProjectListByUser($userUID);
} else {
	header("location: dashboard.php");
}

	// Update Users
if(isset($_POST["email"]) && isset($_POST["lname"]) &&  isset($_POST["fname"]) && isset($_POST["authorized"]) && isset($_POST["roleID"])){ 
	$fname = $_POST["fname"];
	$email = $_POST["email"];
	$lname = $_POST["lname"];
	$authorized = $_POST["authorized"];
	$roleID = $_POST["roleID"];
	$projects = null;
	if(isset($_POST["projects"])){
		$projects = $_POST["projects"];
	}

	$db=new connect();
	$db->db1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->db1->beginTransaction();
	$editUser = false;
	try{
		$editUser = $users->editUser($userUID, $fname, $lname, $email, $authorized, $roleID); 
		if(!json_encode($editUser)){
			throw new Exception("Unable to update user");
		} 	
		// Update project user relationship
		$projectUser = new projectUser();
		$updateProjectUser = $projectUser->updateProjectUserRelationship($db, $projects, $userUID);
		if(!$updateProjectUser){
			throw new Exception("Unable to update project-user relationships");
			return;
		}
		$db->db1->commit();
	} catch (PDOException $e){
		$db->db1->rollBack()
		echo json_encode($e->getMessage());
		return;
	} catch (Exception $e){
		$db->db1->rollBack()
		echo json_encode($e->getMessage());
		return;
	}
	echo json_encode($editUser);
	return;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Edit User</title>

	<!-- EXTERNAL CSS-->
	<?php include_once("../includes/css.php") ?>	
	<link href="../plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>

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
		height:200px;
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
			<div class="panel-heading row" style="text-align:center;">
				<div class="col-md-8">
					<h3 style="margin:5px;" class="text-left bold">Edit User</h3>
				</div>
				<div class="col-md-4 text-right">
					<button class="btn btn-default lock" onclick="makeEditable('unlock')"><span class="fa fa-lock fa-lg"></span></button>
					<button class="btn btn-default unlock hidden" onclick="makeEditable('lock')"><span class="fa fa-unlock fa-lg"></span></button>
				</div>
			</div>
			<div id="successful" class="row valid hidden">
				<h5>Successfully added user!</h5>
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
							<div class="col-md-6" style="padding-left:0">
								<input id="fname" type="text" value="<?php echo $editUser["fname"]; ?>" class="form-control editable" placeholder="First Name" autofocus required readonly>
							</div>
							<div class="col-md-6 lname" style="padding-right:0">
								<input id="lname" class="form-control editable" type="text" value="<?php echo $editUser["lname"]; ?>"class="form-control" placeholder="Last Name" required readonly>
							</div>
						</div>
						<div class="row form-group">
							<input id="email" class="form-control editable" type="text" value="<?php echo $editUser["email"]; ?>"class="form-control" placeholder="Email Address" required readonly>
						</div>
						<div class="row form-group">
							<select id="authorized" class="form-control" required>
								<?php if($editUser["authorized"]==1){
									echo "<option value='0'>Unauthorized</option>";
									echo "<option value='1' selected>Authorized</option>";
								} else {
									echo "<option value='0' selected>Unauthorized</option>";
									echo "<option value='1'>Authorized</option>";
								}?>
							</select>
						</div>
						<div class="row form-group">
							<select id="role" class="form-control" required>
								<?php 
								$roles = new roles();
								$roleList = $roles->getAllRoles();
					          			// Select approprite role ID
								for($i=0; $i<count($roleList); $i++){
									if($roleList[$i]["roleID"] == $editUser["roleID"]){
										echo "<option value='".$roleList[$i]["roleID"]."' selected>".$roleList[$i]["roleName"]."</option>";
									} else{
										echo "<option value='".$roleList[$i]["roleID"]."'>".$roleList[$i]["roleName"]."</option>";
									}
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

									// if projectuserlist exists, check the existing relationship
							if($projectUserList != null && count($projectUserList) > 0){
								for($i=0; $i<count($projectList);$i++){
									$project = $projectList[$i];
									$match = false;
											// Check if projectuser relationship exists
									for($j=0; $j<count($projectUserList); $j++){
										$projectUser = $projectUserList[$j];
										if($project["projectUID"] == $projectUser["projectUID"]){
											$match = true;
											break;
										}
									}
											// Check or uncheck based on match
									if($match){
										echo "<div class='project-check checkbox' style='font-size:15px'>";
										echo "<label><input type='checkbox' style='width:15px; height:15px;' value='".$projectList[$i]["projectUID"]."' checked>".$projectList[$i]["projectName"]."</label>";
										echo "</div>";
									} else {
										echo "<div class='project-check checkbox' style='font-size:15px'>";
										echo "<label><input type='checkbox' style='width:15px; height:15px;' value='".$projectList[$i]["projectUID"]."'>".$projectList[$i]["projectName"]."</label>";
										echo "</div>";
									}
								}
									} else { // if projectuserlist does not exist just display the project list
										for($i=0; $i<count($projectList);$i++){
											echo "<div class='project-check checkbox' style='font-size:15px'>";
											echo "<label><input type='checkbox' style='width:15px; height:15px;' value='".$projectList[$i]["projectUID"]."'>".$projectList[$i]["projectName"]."</label>";
											echo "</div>";
										}
									}
									?>
								</div>
							</div>
						</div>
						<div class="row form-group" style="padding-left:15px">
							<button type="button" class="btn btn-success" onclick="editUser()">Edit User</button>
							<a href="user-list.php" style="text-decoration: none"><button type="button" class="btn btn-danger">Cancel</button></a>
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
			//var roleID = $("#role option:selected").val();
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
			
			function editUser(){
				var fname = $("#fname").val().trim();
				var lname = $("#lname").val().trim();
				var email = $("#email").val().trim();
				var authorized = $("#authorized option:selected").val();
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
			if(fname == "" || lname == "" || 
					email == "" || authorized == "" || roleID == ""){
				$("#invalid").removeClass("hidden");
				return;
			} else {
				$("#invalid").addClass("hidden");
			}

				// Insert into database
				var data = {
					fname: fname,
					lname: lname,
					email: email,
					authorized: authorized,
					roleID: roleID,
					projects: projects
				}
				
				// Sends to top of page
				$.ajax({
					data:data,
					method:"POST",
					dataType:"json",
					success:function(result){
						console.log("Updated: " + JSON.stringify(result));
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

			// Make disabled fields editable
			function makeEditable(action){
				if(action == "unlock"){
					$(".editable").prop("readonly",false);
					$(".unlock").removeClass("hidden");
					$(".lock").addClass("hidden");
				} else if(action == "lock"){
					$(".editable").prop("readonly",true);
					$(".lock").removeClass("hidden");
					$(".unlock").addClass("hidden");
				}
			}
		</script>
	</body>
	</html>