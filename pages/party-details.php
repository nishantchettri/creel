<?php
include_once("../class/anglers.class.php");
include_once("../class/header.class.php");
include_once("../class/parties.class.php");
include_once("../class/projects.class.php");
include_once("../class/schedules.class.php");
include_once("../class/projectspecies.class.php");
include_once("../class/users.class.php");

session_start();

// Validate user and user_edit information
$username = $_SESSION["username"];
// echo $username;
$users = new users();
$userInfo = $users->getUserInfo($username);
if($userInfo["permissionLevel"] != 1){
	header("location: dashboard.php");
}

	
extract($_GET);
extract($_POST);
// date_default_timezone_set('America/Chicago');
$today = date("m-d-Y H:i");
$error = 0;

	// Get Necessary Information from GET ids
$anglerDetails = array();
$headerDetails = array();
$partyDetails = array();
$projectDetails = array();
$scheduleDetails = array();
	// Check if valid parties UID and schedule UID is received in the URL
if(isset($_GET["partiesUID"]) && isset($_GET["scheduleUID"])){
		// Get Party Details via ID
		// If details are not found, redirect to dashboard.php
	$partiesUID = $_GET["partiesUID"];
	$scheduleUID = $_GET["scheduleUID"];
	

		// Get Party Details
	$parties = new parties();
	$partyDetails = $parties->getPartyByPartiesUID($partiesUID);

		// Get Survey Location, Start and Stop Times
	$schedules = new schedules();
	$scheduleDetails = $schedules->getScheduleByScheduleUID($scheduleUID);

		// Get Project Name
	$projects = new projects();
	$projectDetails = $projects->getProjectByProjectUID($scheduleDetails["projectUID"]);
	$projectName = $projectDetails["projectName"];

		// Get Survey Header details
	$header = new header();
	$headerDetails = $header->getHeaderByHeaderUID($partyDetails["headerUID"]);

		// Get Angler List
	$anglers = new anglers();
	$anglerDetails = $anglers->getAnglersPrimaryInfoByPartiesUID($partiesUID);

		// If angler not found from UID
	if(count($partyDetails) == 0 || count($scheduleDetails) == 0){
		$error = 1;
	}
} else {
		//header("location: dashboard.php");
	$error = 1;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>MDC Creel -- Party Details</title>

	<!-- EXTERNAL CSS-->
	<?php include_once("../includes/css.php") ?>
	
	<style>
	.min-width-margin{
		margin-left:5px;
		margin-right:5px;
	}			
	.bold{
		font-weight:bold;
	}		
	.label-background{
		background-color:#f5f5f5;
		border-color:#ddd;
	}
	.sidebar{
		background:transparent; 
		border-radius:3px;
		padding:0;
	}
	.survey-details, .project-details{
		padding-top:0; 
		border-radius:2px;
	}
	.error{
		background-color:#d25656;
		color:white; 
		margin-bottom:10px;
	}
	h5{
		font-weight:bold;
	}
	td{
		text-align:center;
	}
</style>
</head>
<!-- STYLE -->
<body>
	<?php include_once('../includes/navbar.php') ?>

	<div class="container-fluid" style="margin-bottom:15px">
		<?php if($error == 1){ ?>
		<div class="row error text-center">
			<h4>ERROR: No Record Found!</h4>
		</div>
		<div class="row text-center">
			<a href="dashboard.php"><button type="button" class="btn btn-default">Back</button></a>
		</div>
		<?php } else { ?>

		<!-- BEGIN SIDEBAR -->
		<div class="row">
			<input id="partiesUID" type="hidden" value="<?php echo $partiesUID ?>" />
			<input id="scheduleUID" type="hidden" value="<?php echo $scheduleUID ?>" />
			<input id="verifyingClerk" type="hidden" value="<?php echo $username ?>" />
			<div class="col-xs-12 col-md-6 project-details">
				<h4>Project Details</h4>	
				<table class="table table-striped table-bordered" style="margin-bottom:5px">
					<thead>
						<tr>
							<th>Project</th>
							<th>Location</th>
							<th>Date</th>
							<th>Start Time - End Time</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$startTime = explode(" ",$scheduleDetails["surveyStartDateTime"]);
						$stopTime = explode(" ",$scheduleDetails["surveyStopDateTime"]);
						?>
						<tr>
							<td><?php echo $projectName ?></td>
							<td><?php echo $scheduleDetails["surveyLocation"] ?></td>
							<td><?php echo $startTime[0] ?></td>
							<td><?php echo $startTime[1]." - ".$stopTime[1] ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div class="col-xs-12 col-md-6 survey-details">
				<h4>Survey Details</h4>	
				<table class="table table-striped table-bordered" style="margin-bottom:5px">
					<thead>
						<tr>
							<th>Secchi</th>
							<th width="30%">Water Temperature (F)</th>
							<th>Water Level</th>
							<th>Clerk</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $headerDetails["secchi"] ?></td>
							<td><?php echo $headerDetails["waterTemperature"] ?></td>
							<td><?php echo $headerDetails["waterLevel"] ?></td>
							<td><?php echo $headerDetails["clerk"] ?></td>
							<td><?php echo $headerDetails["headerStatus"] ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div><!-- END SIDEBAR -->


		<!-- BEGIN SURVEY DATE/TIME -->
		<div class="row creel-details" style="padding:15px">
			<!-- BEGIN PARTY DETAILS -->
			<h4>Anglers List (<?php echo count($anglerDetails) ?>)</h4>
			<div class="row">
				<table id="partyDetails" class="table table-bordered table-striped text-center">
					<thead>
						<tr>
							<th class="text-center label-background">Angler No.</th>
							<th class="text-center label-background">Time Started</th>
							<th class="text-center label-background" width="20%">Time Contacted/Stopped</th>
							<th class="text-center label-background">Zip Code</th>
							<th class="text-center label-background" width="25%">Angler Preference</th>
							<!-- <th class="text-center label-background">Angler Comments</th> -->
							<th class="text-center label-background">Angler Status</th>
							<th class="text-center label-background">Verified</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$projectspecies = new projectspecies();
						if(count($anglerDetails) > 0){
							for($i=0; $i<count($anglerDetails); $i++){
								$anglerUID = $anglerDetails[$i]["anglerUID"];
								$anglerNumber = $anglerDetails[$i]["anglerNumber"];
								$timeStarted = $anglerDetails[$i]["timeStarted"];
								$timeContactedStopped = $anglerDetails[$i]["timeContactedStopped"];
								$zipCode = $anglerDetails[$i]["zipCode"];
								$anglerStatus = $anglerDetails[$i]["anglerStatus"];
								$verifiedStatus = $anglerDetails[$i]["verifiedStatus"];

										// Get Fish Name
								$anglerPreference = $anglerDetails[$i]["anglerPreference"];
								$species = $projectspecies->getSpeciesNameBySpeciesCode($anglerPreference);
								$speciesCode = $species["speciesCode"];
								$speciesNameFirst = $species["speciesNameFirst"];
										// $speciesNameLast = $species["speciesNameLast"];

								echo "<tr>";
								echo "<td>".$anglerNumber."</td>";
								echo "<td>".$timeStarted."</td>";
								echo "<td>".$timeContactedStopped."</td>";
								echo "<td>".$zipCode."</td>";
								echo "<td>".$speciesCode." - ".$speciesNameFirst."</td>";
								if($anglerStatus == 0){ 
									echo "<td>Completed</td>";
									if($verifiedStatus == "1"){
										echo("<td class='verifiedStatus'><a href='angler-details.php?anglerUID=".$anglerUID."'><button class='btn btn-default bg-green'><span class='glyphicon glyphicon-ok'></span></button></a></td>");
									} else {
										echo("<td class='verifiedStatus'><a href='angler-details.php?anglerUID=".$anglerUID."'><button class='btn btn-default bg-red'><span class='glyphicon glyphicon-remove'></span></button></a></td>");
									}
								} else {
									echo "<td>Refused</td>";
									echo "<td></td>";
								}
								
								
								echo"</tr>";
							}
						} else {
							echo "<tr>";
							echo "<td colspan='7'><h3>No Data Available</h3></td>";
							echo "</tr>";
						}
						?>
					</tbody>
						<!-- <tfoot>
							<tr class="text-right">
								<td colspan="13"><label>Last Updated On:&nbsp;</label><?php echo $today ?></td>
							</tr>	
						</tfoot> -->
					</table>
				</div> <!-- END TABLE -->
				<?php } // end else if-error = 1?>
			</div>

			<!-- BEGIN VERIFICATION PANEL -->
			<div class="container-fluid" style="margin-bottom:15px">
				<div class="panel panel-default">
					<div class="panel-heading"><h4 style="margin:2px">Verify Party</h4></div>
					<div class="panel-body">
						<div class="row" style="margin-bottom:10px">
							<textarea id='partyVerifyComments' class='form-control'><?php echo $partyDetails['verifiedComments']?></textarea>
						</div>
						<div class="row">
							<div class="row buttons text-right">
								<button class="btn btn-md btn-primary verify" onclick="verify()">Verify</button>
								<!-- <a href="#"><button class="btn btn-md btn-warning edit">Edit</button></a> -->
								<?php echo ("<a href='dashboard.php'><button class='btn btn-md btn-danger cancel'>Cancel</button></a>"); ?>
							</div>
						</div>
					</div>
				</div>
				<!-- END VERIFICATION PANEL -->

			</div><!-- END CONTAINER FLUID -->

			<div class="navbar-fixed-bottom">
				<?php include_once("../includes/footer.php"); ?>
			</div>

			<!-- EXTERNAL JAVASCRIPT -->
			<?php include_once('../includes/js.php'); ?>
			<script type="text/javascript">
			// Send to verification page
			function verify(){
				var comments = $("#partyVerifyComments").val();
				// Send anglerUID and clerk 
				var partiesUID = $("#partiesUID").val();
				var scheduleUID = $("#scheduleUID").val();
				var clerk = $("#verifyingClerk").val();
				var verifiedDate = dateFormat(new Date(),"yyyy-mm-dd hh:MM:ss");
				var data = {partiesUID:partiesUID, clerk:clerk, comments:comments, verifiedDate:verifiedDate};
				
				// Update to database
				$.ajax({
					url:"../ajax-handlers/parties-handler.php",
					data:data,
					method:"POST",
					dataType:"text",
					success:function(result){
						if(result == '1' || result == 1){
							console.log("success");
							window.location="party-details.php?partiesUID="+partiesUID+"&scheduleUID="+scheduleUID;
						}
					},
					error:function(xhr, status, error){
						console.log("XHR: " + JSON.stringify(xhr));
						console.log("Status: " + JSON.stringify(status));
						console.log("Error: " + JSON.stringify(error));
					}
				});
			}
		</script>
	</body>
	</html>