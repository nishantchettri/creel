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
	if($userInfo["permissionLevel"] == 1 and $_GET["projectUID"]){
		$projectUID = $_GET["projectUID"];
		$projects = new projects();
		$editProject = $projects->getProjectByProjectUID($projectUID);
		if(count($editProject[0]) == 0){
			header("location: project-list.php");
		}
	} else {
		header("location: dashboard.php");
	}

	// Edit Project
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

		// Update project
		$projects = new projects();
		$updateProject = $projects->updateProject($projectUID, $projectName, $projectType, $projectLocation, $projectDescription, $projectStartDate, $projectStopDate, $optionalQ1, $optionalQ2, $optionalQ3, $optionalQ4, $optionalQ5, $optionalQ6);
		echo $updateProject;
		return;
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Edit Project</title>

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
			textarea{
				resize: vertical;
			}
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<?php include_once('../includes/navbar.php') ?>

		<div class="container-fluid">
			<div class="panel panel-default" style="border:0; margin-bottom:50px">
				<div class="panel-heading row" style="text-align:center;">
					<div class="col-md-8" style="padding-left:0;">
						<h3 style="margin:5px; margin-left:0" class="text-left bold">Edit Project</h3>
					</div>
					<div class="col-md-4 text-right" style=" padding-right:0">
						<button class="btn btn-default lock" onclick="makeEditable('unlock')"><span class="fa fa-lock fa-lg"></span></button>
						<button class="btn btn-default unlock hidden" onclick="makeEditable('lock')"><span class="fa fa-unlock fa-lg"></span></button>
					</div>
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
					<input id="projectUID" type="hidden" value="<?php echo $editProject["projectUID"] ?>" />
			    	<div class="row">
			    		<div class="form-group">
				    		<div class="form-group no-padding col-md-6 col-xs-12" style="padding-left:0">
				    			<label for="projectName">Project Name</label>
				    			<input id="projectName" type="text" value="<?php echo $editProject["projectName"]; ?>" class="form-control editable" placeholder="Project Name" required disabled>
				    		</div>
				    		<div class="form-group no-padding col-md-6 col-xs-12" style="padding-right:0">
				    			<label for="projectLocation">Project Location</label>
				    			<input id="projectLocation" type="text" value="<?php echo $editProject["projectLocation"]; ?>" class="form-control editable" placeholder="Project Location" required disabled>
				    		</div>
				    	</div>
			        </div>
			        <div class="row">
			    		<div class="form-group">
				    		<div class="form-group no-padding col-md-4 col-xs-12" style="padding-left:0">
				    			<label for="projectType">Project Type</label>
				    			<select id="projectType" class="form-control editable" required disabled>
				        			<?php if($editProject["projectType"]=="Access"){
				        				echo "<option value='Access' selected>Access</option>";
				        				echo "<option value='Stream'>Stream</option>";
				        				echo "<option value='Roving'>Roving</option>";
		        					} else if($editProject["projectType"]=="Stream"){
		        						echo "<option value='Access'>Access</option>";
				        				echo "<option value='Stream' selected>Stream</option>";
				        				echo "<option value='Roving'>Roving</option>";
		        					} else if($editProject["projectType"]=="Roving"){
		        						echo "<option value='Access'>Access</option>";
				        				echo "<option value='Stream'>Stream</option>";
				        				echo "<option value='Roving' selected>Roving</option>";
		        					}?>
				          		</select>
				    		</div>
				    		<div class="form-group no-padding col-md-4 col-xs-12">
				    			<label for="projectStartDate">Project Start Date</label>
				    			<input id="projectStartDate" type="text" value="<?php echo date("Y-m-d", strtotime($editProject["projectStartDate"])) ?>" class="form-control editable" placeholder="Project Stop Date" required disabled>
				    		</div>
				    		<div class="form-group no-padding col-md-4 col-xs-12" style="padding-right:0">
				    			<label for="projectStopDate">Project Stop Date</label>
				    			<input id="projectStopDate" type="text" value="<?php echo date("Y-m-d", strtotime($editProject["projectStopDate"])) ?>" class="form-control editable" placeholder="Project Stop Date" required disabled>
				    		</div>
				    	</div>
			        </div>
			        <div class="row form-group">
			        	<div class="form-group no-padding col-md-4 col-xs-12" style="padding-left:0">
			    			<label for="projectDescription">Project </label>
			        		<textarea id="projectDescription" rows="6" class="form-control"><?php echo $editProject["projectDescription"] ?></textarea>
			    		</div>
			    		<div class="form-group no-padding col-md-8 col-xs-12" style="padding-right:0;">
			    			<div class="row">
			    				<label for="projectDescription">Optional Questions</label>
			    			</div>
			    			<div class="row">
			    				<div class="row">
			    					<div class="col-md-6 no-left-pad no-padding col-xs-12">
			    						<input id="optionalQ1" type="text" value="<?php echo $editProject["optionalQ1"] ?>" class="form-control form-group" placeholder="Optional Question 1">
			    					</div>
			    					<div class="col-md-6 no-right-pad no-padding col-xs-12">
			    						<input id="optionalQ2" type="text" value="<?php echo $editProject["optionalQ2"] ?>" class="form-control form-group" placeholder="Optional Question 2">
			    					</div>
			    				</div>
			    				<div class="row">
			    					<div class="col-md-6 no-left-pad no-padding col-xs-12">
			    						<input id="optionalQ3" type="text" value="<?php echo $editProject["optionalQ3"] ?>" class="form-control form-group" placeholder="Optional Question 3">
			    					</div>
			    					<div class="col-md-6 no-right-pad no-padding col-xs-12">
			    						<input id="optionalQ4" type="text" value="<?php echo $editProject["optionalQ4"] ?>" class="form-control form-group" placeholder="Optional Question 4">
			    					</div>
			    				</div>
			    				<div class="row">
			    					<div class="col-md-6 no-left-pad no-padding col-xs-12">
			    						<input id="optionalQ5" type="text" value="<?php echo $editProject["optionalQ5"] ?>" class="form-control form-group" placeholder="Optional Question 5">
			    					</div>
			    					<div class="col-md-6 no-right-pad no-padding col-xs-12">
			    						<input id="optionalQ6" type="text" value="<?php echo $editProject["optionalQ6"] ?>" class="form-control form-group" placeholder="Optional Question 6">
			    					</div>
			    				</div>
					        </div>
			    		</div>
			        </div>
			      	<div class="row">
			      		<div class="form-group no-padding col-md-6" style="padding-left:0">
					      	<div class="col-md-6 col-xs-6" style="padding-left:0">
						      	<a><button type="button" onclick="editProject()" class="btn btn-success">Edit Project</button></a>
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
			// Add date picker
			$(document).ready(function(){
				var projectStartDate = $("#projectStartDate").val();
				var projectStopDate = $("#projectStopDate").val();

				// Set start datepicker with start date
				$("#projectStartDate").datepicker({
					dateFormat:"yy-mm-dd",
					setDate: projectStartDate,
					onSelect:function(dateText, obj){
				        var tempStartDate = new Date(dateText);
				        console.log(tempStartDate);
				        var default_end = new Date(tempStartDate.getFullYear(), tempStartDate.getMonth(), tempStartDate.getDate()+1);
						$("#projectStopDate").datepicker("destroy");
						$("#projectStopDate").val('');
						$("#projectStopDate").datepicker({
							dateFormat:"yy-mm-dd",
							minDate: default_end
						});
					}
				});

				// Set min date for stop datepicker
				var mindate = new Date(projectStartDate);
				var default_min = new Date(mindate.getFullYear(), mindate.getMonth(), mindate.getDate()+1);
				$("#projectStopDate").datepicker({
					dateFormat:"yy-mm-dd",
					setDate: projectStopDate,
					minDate: default_min
				});
			});

			// Make disabled fields editable
			function makeEditable(action){
				if(action == "unlock"){
					$("#projectStartDate").prop("readonly",true);
					$("#projectStopDate").prop("readonly",true);
					// $("#projectStartDate").datepicker("refresh");
					
					$(".editable").prop("disabled",false);
					$(".unlock").removeClass("hidden");
					$(".lock").addClass("hidden");
				} else if(action == "lock"){
					$("#projectStartDate").prop("readonly",false);
					$("#projectStopDate").prop("readonly",false);

					$(".editable").prop("disabled",true);
					$(".lock").removeClass("hidden");
					$(".unlock").addClass("hidden");
				}
			}

			// Update Project
			function editProject(){
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
					dataType:"text",
					success:function(result){
						if(result == "0"){
							$("#unsuccessful").removeClass("hidden");
							$("#successful").addClass("hidden");
						} else {
							$("#successful").removeClass("hidden");
							$("#unsuccessful").addClass("hidden");							
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