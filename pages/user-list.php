<?php
	include_once("../class/users.class.php");
	include_once("../class/roles.class.php");

	session_start();
	extract($_POST);
	extract($_GET);

	// Validate user and user_edit information
	$username = $_SESSION["username"];
	$users = new users();
	$userInfo = $users->getUserInfo($username);
	if($userInfo["permissionLevel"] != 1){
		header("location: dashboard.php");
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>User List</title>

		<!-- EXTERNAL CSS-->
		<?php include_once("../includes/css.php") ?>
		<link rel="stylesheet" type="text/css" href="../plugins/datatables/css/dataTables.bootstrap.min.css">
		
		<style>	
            /* table.dataTable th, */
            /*table.dataTable td {
                max-width:200px !important;
                overflow:hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }*/
            #userList{
            	width:100% !important; /* without scroll bar table looks unbalanced */
            }
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<?php include_once('../includes/navbar.php') ?>

		<div class="container-fluid" style="margin-bottom:30px">
			<!-- MODAL -->
			<div class="modal fade" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="bold modal-title">Alert</h5>
			      </div>
			      <div class="modal-body">
			        <h4 id="modal-message" style="font-weight:normal"></h4>
			      </div>
			      <div class="modal-footer">
			      	<div id="authorize-footer">
				        <button type="button" class="btn btn-success" onclick="authorize('yes')">Yes</button>
				        <button type="button" class="btn btn-danger"  onclick="authorize('no')">No</button>
				    </div>
				    <div id="role-footer">
				        <button type="button" class="btn btn-success" onclick="updateUserRole('yes')">Yes</button>
				        <button type="button" class="btn btn-danger"  onclick="updateUserRole('no')">No</button>
				    </div>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<!-- BEGIN SURVEY DATE/TIME -->
			<div class="row creel-details" style="padding:15px; padding-top:0">
				<!-- BEGIN PARTY DETAILS -->
				<div class="row">
					<h3 style="padding-left:15px; margin-top:0; margin-bottom:20px">User List <button class="btn btn-sm btn-primary"><a href="add-user.php" style="color:white; text-transform:none">Add User</a></button></h3>
				</div>
				<div class="row">
					<table id="userList" class="table table-bordered table-striped text-center">
						<thead>
							<tr>
								<th class="text-center">Full Name</th>
								<th class="text-center">Email</th>
								<th class="text-center">Authorized</th>
								<th class="text-center">Role</th>
								<th class="text-center">Edit</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$userList = $users->getUserList();
								$roles = new roles();
								$rolesList = $roles->getAllRoles();
								if(count($userList) > 0){
									for($i=0; $i<count($userList); $i++){
										$email = $userList[$i]["email"];
										if(strcasecmp($username,$email)!=0){
											$userUID = $userList[$i]["userUID"];
											$fname = $userList[$i]["fname"];
											$lname = $userList[$i]["lname"];
											$authorized = $userList[$i]["authorized"];
											$roleID = $userList[$i]["roleID"];
											$roleName = $userList[$i]["roleName"];

											echo "<tr>";
											echo "<td>".$fname." ".$lname."</td>";
											echo "<td>".$email."</td>";
											if($authorized == 1){ 
												echo "<td>Authorized</td>";
											} else {
												echo "<td style='color:red; font-weight:bold'>Unauthorized</td>";
											}
											echo "<td>".$roleName."</td>";
											echo "<td>
													<a href='edit-user.php?uid=".$userUID."'><button class='btn btn-default'><span class='glyphicon glyphicon-pencil'></span></button></a>
													<a href='#'><button class='btn btn-default disabled'><span class='glyphicon glyphicon-trash'></span></button></a>
												</td>";
											echo"</tr>";
										}
									}
								} else {
									echo "<tr>";
									echo "<td colspan='8'><h3>No Data Available</h3></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div> <!-- END TABLE -->
			</div>
		</div><!-- END CONTAINER FLUID -->

		<div class="navbar-fixed-bottom">
			<?php include_once("../includes/footer.php"); ?>
		</div>




	<!-- EXTERNAL JAVASCRIPT -->
	<?php include_once('../includes/js.php'); ?>
	<script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables/js/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript">		
		// On Document ready
		$("document").ready(function(){
			$("#userList").DataTable({
				sorting:[2,"desc"]
			});
		});

	</script>
	</body>
</html>