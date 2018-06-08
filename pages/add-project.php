<?php
	include_once("../class/projects.class.php");
	include_once("../class/users.class.php");
	include_once("../class/utilities.class.php");

	// Get Project UID
	$utilities = new utilities();
	$projectUID = $utilities->randomNumber();
	
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

	// Add Project
	if(isset($_POST["projectName"]) && isset($_POST["projectLocation"]) && isset($_POST["projectType"]) 
			&& isset($_POST["projectStartDate"]) && isset($_POST["projectStopDate"])){
		$projectName = $_POST["projectName"];
		$projectLocation = $_POST["projectLocation"];
		$projectType  = $_POST["projectType"];
		$projectStartDate = $_POST["projectStartDate"];
		$projectStopDate  = $_POST["projectStopDate"];
		$projectDescription = $_POST["projectDescription"];
		$optionalQ1  = $_POST["optionalQ1"];
		$optionalQ2  = $_POST["optionalQ2"];
		$optionalQ3  = $_POST["optionalQ3"];
		$optionalQ4  = $_POST["optionalQ4"];
		$optionalQ5  = $_POST["optionalQ5"];
		$optionalQ6  = $_POST["optionalQ6"];

		$projects = new projects();
		$insertProject = $projects->insertNewProject($projectUID, $projectName, $projectType, $projectLocation, $projectDescription, $projectStartDate, $projectStopDate, $optionalQ1, $optionalQ2, $optionalQ3, $optionalQ4, $optionalQ5, $optionalQ6);
		echo json_encode($insertProject);
		return;
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Add Project</title>

		<!-- EXTERNAL CSS-->
		<?php include_once("../includes/css.php") ?>	
		<link href="../plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
		<link href="../plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

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
			@media(max-width:768px){
				.no-padding{
					padding-left:0;
					padding-right:0;
				}
			}
			.no-left-pad{
				padding-left:0;
			}
			.no-right-pad{
				padding-right:0;
			}
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<?php include_once('../includes/navbar.php') ?>
		<!-- MODAL -->
		<div id="add_schedule" class="modal fade" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="bold modal-title">Add Schedule?</h5>
		      </div>
		      <div class="modal-body">
		        <h4 id="modal-message" style="font-weight:normal">Do you want to add a schedule for the project?</h4>
		      </div>
		      <div class="modal-footer">
		        <a href="upload-csv.php"><button type="button" class="btn btn-success">Yes</button></a>
		        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<div class="container-fluid">
			<div class="panel panel-default" style="border:0; margin-bottom:50px">
				<div class="panel-heading row" style="text-align:center;">
					<div class="col-md-8" style="padding-left:0;">
						<h3 style="margin:5px; margin-left:0" class="text-left bold">Add Project</h3>
					</div>
					<!-- <div class="col-md-4 text-right" style=" padding-right:0">
						<button class="btn btn-default lock" onclick="makeEditable('unlock')"><span class="fa fa-lock fa-lg"></span></button>
						<button class="btn btn-default unlock hidden" onclick="makeEditable('lock')"><span class="fa fa-unlock fa-lg"></span></button>
					</div> -->
				</div>
				<div id="successful" class="row valid hidden">
					<h5>Successfully added project!</h5>
				</div>
				<div id="unsuccessful" class="row invalid hidden">
					<h5>Unable to add project! Contact the administrator</h5>
				</div>
				<div id="incomplete" class="row invalid hidden">
					<h5>Please fill out all the fields!</h5>
				</div>
				<div class="panel-body" style="margin-bottom:0">
					<!-- PROJECT UID  -->
					<input id="projectUID" type="hidden" value="<?php echo strtoupper($projectUID) ?>" />
			    	<div class="row">
			    		<div class="form-group">
				    		<div class="form-group no-padding col-md-6 col-xs-12" style="padding-left:0">
				    			<label for="projectName">Project Name</label>
				    			<input id="projectName" type="text" value="" class="form-control" placeholder="Project Name" required>
				    		</div>
				    		<div class="form-group no-padding col-md-6 col-xs-12" style="padding-right:0">
				    			<label for="projectLocation">Project Location</label>
				    			<input id="projectLocation" type="text" value="" class="form-control" placeholder="Project Location" required>
				    		</div>
				    	</div>
			        </div>
			        <div class="row">
			    		<div class="form-group">
				    		<div class="form-group no-padding col-md-4 col-xs-12" style="padding-left:0">
				    			<label for="projectType">Project Type</label>
				    			<select id="projectType" class="form-control" required>
			        				<option value='na'>Select One</option>
			        				<option value='Access'>Access</option>
			        				<option value='Stream'>Stream</option>
			        				<option value='Roving'>Roving</option>
				          		</select>
				    		</div>
				    		<div class="form-group no-padding col-md-4 col-xs-12">
				    			<label for="projectStartDate">Project Start Date</label>
				    			<input id="projectStartDate" type="text" value="" class="form-control" placeholder="Project Start Date" required readonly>
				    		</div>
				    		<div class="form-group no-padding col-md-4 col-xs-12" style="padding-right:0">
				    			<label for="projectStopDate">Project Stop Date</label>
				    			<input id="projectStopDate" type="text" value="" class="form-control" placeholder="Project Stop Date" required readonly>
				    		</div>
				    	</div>
			        </div>
			        <div class="row form-group">
			        	<div class="form-group no-padding col-md-4 col-xs-12" style="padding-left:0">
			    			<label for="projectDescription">Project Description</label>
			        		<textarea id="projectDescription" rows="6" class="form-control"></textarea>
			    		</div>
			    		<div class="form-group no-padding col-md-8 col-xs-12" style="padding-right:0;">
			    			<div class="row">
			    				<label for="projectDescription">Optional Questions</label>
			    			</div>
			    			<div class="row">
			    				<div class="row">
			    					<div class="col-md-6 no-left-pad no-padding col-xs-12">
			    						<input id="optionalQ1" type="text" value="" class="form-control form-group" placeholder="Optional Question 1">
			    					</div>
			    					<div class="col-md-6 no-right-pad no-padding col-xs-12">
			    						<input id="optionalQ2" type="text" value="" class="form-control form-group" placeholder="Optional Question 2">
			    					</div>
			    				</div>
			    				<div class="row">
			    					<div class="col-md-6 no-left-pad no-padding col-xs-12">
			    						<input id="optionalQ3" type="text" value="" class="form-control form-group" placeholder="Optional Question 3">
			    					</div>
			    					<div class="col-md-6 no-right-pad no-padding col-xs-12">
			    						<input id="optionalQ4" type="text" value="" class="form-control form-group" placeholder="Optional Question 4">
			    					</div>
			    				</div>
			    				<div class="row">
			    					<div class="col-md-6 no-left-pad no-padding col-xs-12">
			    						<input id="optionalQ5" type="text" value="" class="form-control form-group" placeholder="Optional Question 5">
			    					</div>
			    					<div class="col-md-6 no-right-pad no-padding col-xs-12">
			    						<input id="optionalQ6" type="text" value="" class="form-control form-group" placeholder="Optional Question 6">
			    					</div>
			    				</div>
					        </div>
			    		</div>
			        </div>
			      	<div class="row">
			      		<div class="form-group no-padding col-md-6" style="padding-left:0">
					      	<div class="form-group col-md-6 col-xs-6" style="padding-left:0">
						      	<a><button type="button" onclick="addProject()" class="btn btn-success">Add Project</button></a>
						      	<a href="project-list.php" style="text-decoration: none"><button type="button" class="btn btn-danger">Cancel</button></a>
					      	</div>
				      	</div>
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
			$(document).ready(function(){
				$("#projectStartDate").datepicker({
					dateFormat:"yy-mm-dd",
					// maxDate:0,
					onSelect:function(dateText, obj){
				         var tempStartDate = new Date(dateText);
				         var default_end = new Date(tempStartDate.getFullYear(), tempStartDate.getMonth(), tempStartDate.getDate()+1);
						$("#projectStopDate").datepicker("destroy");
						$("#projectStopDate").val('');
						$("#projectStopDate").datepicker({
							dateFormat:"yy-mm-dd",
							minDate: default_end
						});
					}
				});
			});

			// Update Project
			function addProject(){
				var projectUID = $("#projectUID").val();
				var projectName = $("#projectName").val().trim();
				var projectLocation = $("#projectLocation").val().trim();
				var projectType = $("#projectType option:selected").val();
				var projectStartDate = $("#projectStartDate").val();
				var projectStopDate = $("#projectStopDate").val();
				var projectDescription = $("#projectDescription").val();
				var optionalQ1 = $("#optionalQ1").val();
				var optionalQ2 = $("#optionalQ2").val();
				var optionalQ3 = $("#optionalQ3").val();
				var optionalQ4 = $("#optionalQ4").val();
				var optionalQ5 = $("#optionalQ5").val();
				var optionalQ6 = $("#optionalQ6").val();

				// Validate empty fields
				if(projectName == "" || projectLocation == "" || 
					projectType == "na" || projectStartDate == "" || projectStopDate == ""){
					$("#incomplete").removeClass("hidden");
					return;
				} else {
					$("#incomplete").addClass("hidden");
				}

				// Insert into database
				var data = {
					projectUID:projectUID,
					projectName:projectName,
					projectLocation:projectLocation,
					projectType :projectType,
					projectStartDate:projectStartDate,
					projectStopDate :projectStopDate,
					projectDescription:projectDescription,
					optionalQ1 :optionalQ1,
					optionalQ2 :optionalQ2,
					optionalQ3 :optionalQ3,
					optionalQ4 :optionalQ4,
					optionalQ5 :optionalQ5,
					optionalQ6 :optionalQ6 
				}

				// Sends to top of page
				$.ajax({
					data:data,
					method:"POST",
					dataType:"json",
					success:function(result){
						if(result){ // true || false
							$("#successful").removeClass("hidden");
							$("#unsuccessful").addClass("hidden");
							$("#add_schedule").modal("show");
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