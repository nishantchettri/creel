<?php
	include_once("../class/projects.class.php");
	include_once("../class/users.class.php");
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
		<title>Project List</title>

		<!-- EXTERNAL CSS-->
		<?php include_once("../includes/css.php") ?>
		<link rel="stylesheet" type="text/css" href="../plugins/datatables/css/dataTables.bootstrap.min.css">
		
		<style>	            
            /* table.dataTable th, */
            table.dataTable td {
                max-width:200px !important;
                overflow:hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            #projectList{
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
					<h3 style="padding-left:15px; margin-top:0; margin-bottom:20px">Project List <button class="btn btn-sm btn-primary"><a href="add-project.php" style="color:white; text-decoration:none">Add Project</a></button></h3>
				</div>
				<div class="row">
					<table id="projectList" class="table table-bordered table-striped text-center">
						<thead>
							<tr>
								<th class="text-center">Name</th>
								<th class="text-center">Type</th>
								<th class="text-center">Location</th>
								<th class="text-center">Start Date</th>
								<th class="text-center">Stop Date</th>
								<th class="text-center">Status</th>
								<th class="text-center">Edit</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$projects = new projects();
								$projectList = $projects->getProjectList();
								if(count($projectList) > 0){
									for($i=0; $i<count($projectList); $i++){
										$projectUID = $projectList[$i]["projectUID"];
										$projectName = $projectList[$i]["projectName"];
										$projectType = $projectList[$i]["projectType"];
										$projectLocation = $projectList[$i]["projectLocation"];
										$projectStartDate = $projectList[$i]["projectStartDate"];
										$projectStopDate = $projectList[$i]["projectStopDate"];
										$today = date("M-d-Y");

										echo "<tr>";
										echo "<td>".$projectName."</td>";
										echo "<td>".$projectType."</td>";
										echo "<td>".$projectLocation."</td>";
										echo "<td>".date("M-d-Y", strtotime($projectStartDate))."</td>";
										echo "<td>".date("M-d-Y", strtotime($projectStopDate))."</td>";
										if(strtotime($projectStartDate) > strtotime($today)){
											echo "<td style='color:green; font-weight:bold'>Not Started</td>";	
										}else if(strtotime($projectStartDate) <= strtotime($today) && strtotime($projectStopDate) >= strtotime($today)){
											echo "<td style='color:green; font-weight:bold'>Running</td>";	
										} else {
											echo "<td style='color:red; font-weight:bold'>Ended</td>";	
										}
										echo "<td>
												<a href='edit-project.php?projectUID=".$projectUID."'><button class='btn btn-default'><span class='glyphicon glyphicon-pencil'></span></button></a>
												<a href='#'><button class='btn btn-default disabled'><span class='glyphicon glyphicon-trash'></span></button></a>
											</td>";
										echo"</tr>";
									}
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
			$("#projectList").DataTable();
		});
	</script>
	</body>
</html>