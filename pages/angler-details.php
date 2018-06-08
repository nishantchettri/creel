<?php
	include_once("../class/anglers.class.php");
	include_once("../class/projects.class.php");
	include_once("../class/projectspecies.class.php");
	include_once("../class/fishcaught.class.php");
	include_once("../class/utilities.class.php");
	include_once("../class/users.class.php");

	// error_reporting(1);
	session_start();
	// Validate user and user_edit information
	$username = $_SESSION["username"];
	$users = new users();
	$userInfo = $users->getUserInfo($username);
	if($userInfo["permissionLevel"] != 1){
		header("location: dashboard.php");
	}
	
	extract($_POST);
	extract($_GET);

	$error = 0;

	// Get Necessary Information from GET ids
	$anglerDetails = array();
	$projectDetails = array();
	$partiesUID = "";
	$scheduleUID = "";
	// Check if valid angler UID is received in the URL
	if(isset($_GET["anglerUID"])){
		// If details are not found, redirect to dashboard.php
		$anglerUID = $_GET["anglerUID"];

		// Get Angler List
		$anglers = new anglers();
		$anglerDetails = $anglers->getAnglerByAnglerUID($anglerUID);

		$partiesUID = $anglerDetails["partiesUID"];
		$scheduleUID = $anglerDetails["scheduleUID"];
		
		// Get Optional Question Titles
		$projectUID = $anglerDetails["projectUID"];
		$project = new projects();
		$projectDetails = $project->getProjectByProjectUID($projectUID);

		// If angler not found from UID
		if($anglerDetails == ""){
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
		<title>MDC Creel -- Angler Details</title>

		<!-- EXTERNAL CSS-->
		<?php include_once("../includes/css.php") ?>
		<link href="../plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
		
		<style>	
			.bold{
				font-weight:bold;
			}		
			.label-background{
				background-color:#f5f5f5;
				border-color:#ddd;
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
			.panel-body > .col-xs-6 > div.row{
				margin-bottom:10px !important;
			}
			label{
				font-size:16px;
			}
			.col-xs-8 > .row{
				margin-bottom:10px;
			}
			a:hover{
				text-decoration:none;
			}
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<?php include_once('../includes/navbar.php') ?>

		<div class="container-fluid" style="margin-bottom:15px;">
			<?php if($error == 1){ ?>
				<div class="row error text-center">
					<h4>ERROR: No Record Found!</h4>
				</div>
				<div class="row text-center">
					<a href="dashboard.php"><button type="button" class="btn btn-default">Back</button></a>
				</div>
			<?php } else { ?>

			<div class="row">
				<div class="col-xs-7" style="padding-left:5px;">
					<h4 id="anglerUID" style="font-weight:bold; margin-bottom:15px"> Angler UID: <?php echo $anglerDetails["anglerUID"] ?></h4>
					<input type="hidden" id="clerk" value="<?php echo $_SESSION['username'] ?>" />
				</div>
				<div class="col-xs-5 text-right" style="padding-right:5px;">
					<?php
						if($anglerDetails["verifiedStatus"] == "1"){
							echo("<h4 style='margin-top:0; margin-bottom:15px'>Verified: <span class='glyphicon glyphicon-ok' style='color:green; background-color:white; border:1px solid black; padding:5px; border-radius:100%'></span></h4>");
						} else{
							echo("<h4 style='margin-top:0; margin-bottom:15px'>Verified: <span class='glyphicon glyphicon-remove' style='color:red; background-color:white; border:1px solid black; padding:5px; border-radius:100%'></span></h4>");
						}
					?>
				</div>
			</div>
			<!-- BEGIN ANGLER INFO PANEL -->
			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 style="margin:0">Angler Details</h4>
					</div>
					<div class="panel-body">
						<div class="col-xs-6" style="border-right:1px dashed lightgray;">
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="anglerNumber">Angler Number</label>
								</div>
								<div class="col-xs-6">
									<input id="anglerNumber" class="form-control" type="text" value="<?php echo $anglerDetails['anglerNumber'] ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="timeStarted">Time Started</label>
								</div>
								<div class="col-xs-6">
									<input id="timeStarted" class="form-control" type="text" value="<?php echo $anglerDetails['timeStarted'] ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="timeContactedStopped">Time Stopped</label>
								</div>
								<div class="col-xs-6">
									<input id="timeContactedStopped" class="form-control" type="text" value="<?php echo $anglerDetails['timeContactedStopped'] ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="fishingHours">Fishing Hours</label>
								</div>
								<div class="col-xs-6">
									<input id="fishingHours" class="form-control" type="text" value="<?php echo $anglerDetails['fishingHours'] ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="zipCode">Zip Code</label>
								</div>
								<div class="col-xs-6">
									<input id="zipCode" class="form-control" type="text" value="<?php echo $anglerDetails['zipCode'] ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="destination">Destination</label>
								</div>
								<div class="col-xs-6">
									<?php 
										$utilities = new utilities();
										$destinationLabel = $utilities->getDestination($anglerDetails["destination"]);
									?>
									<input id="destination" class="form-control" type="text" value="<?php echo $destinationLabel ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="trip">Trip</label>
								</div>
								<?php 
									$utilities = new utilities();
									$tripLabel = $utilities->getTrip($anglerDetails["trip"]);
								?>
								<div class="col-xs-6">
									<input id="trip" class="form-control" type="text" value="<?php echo $tripLabel ?>" readonly/>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="satisfaction">Satisfaction (1-10)</label>
								</div>
								<div class="col-xs-6">
									<input id="satisfaction" class="form-control" type="text" value="<?php echo $anglerDetails['satisfaction'] ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="race">Race</label>
								</div>
								<div class="col-xs-6">
									<?php 
										$utilities = new utilities();
										$raceLabel = $utilities->getRace($anglerDetails["race"]);
									?>
									<input id="race" class="form-control" type="text" value="<?php echo $raceLabel ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="fType">Fish Type</label>
								</div>
								<?php 
									$utilities = new utilities();
									$fTypeLabel = $utilities->getFishingType($anglerDetails["fishType"]);
								?>
								<div class="col-xs-6">
									<input id="fType" class="form-control" type="text" value="<?php echo $fTypeLabel ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="lure">Lure</label>
								</div>
								<?php 
									$utilities = new utilities();
									$lure = $utilities->getLure($anglerDetails["lure"]);
								?>
								<div class="col-xs-6">
									<input id="lure" class="form-control" type="text" value="<?php echo $lure ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="method">Method</label>
								</div>
								<?php 
									$utilities = new utilities();
									$methodLabel = $utilities->getMethod($anglerDetails["method"]);
								?>
								<div class="col-xs-6">
									<input id="method" class="form-control" type="text" value="<?php echo $methodLabel ?>" readonly/>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 text-center">
									<label for="anglerPref">Angler Preference</label>
								</div>
								<div class="col-xs-6">
								<?php
									$projectSpecies = new projectspecies();
									$species = $projectSpecies->getSpeciesNameBySpeciesCode($anglerDetails["anglerPreference"]);
									$speciesName = "";
									if(count($species)>0){
										$speciesName = $anglerDetails['anglerPreference']." - ".$species["speciesNameFirst"];
									}
								?>
									<input id="anglerPref" class="form-control" type="text" value="<?php echo $speciesName ?>" readonly/>
								</div>
							</div>
						</div>
					</div>
				</div>				
			</div><!-- END ANGLER INFO PANEL -->

			<!-- BEGIN FISH CAUGHT PANEL -->
			<?php
				$fish = new fishcaught();
				$fishDetails = $fish->getFishByAnglerUID($anglerDetails["anglerUID"]);
				if($fishDetails != null){
					if($fishDetails[0]["photoTaken"] == "1"){
						$fishCaughtImageDetails = $fish->getFishImageByFishCaughtUID($fishDetails[0]["fishCaughtUID"]);
						$src = 'data: image/jpg;base64,'.$fishCaughtImageDetails["image"];
						$image = 1;
					} else {
						$src = '../resources/images/no-image.png';
						$image = 0;
					}
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-6" style="padding-left:0">
							<h4>Fish Caught</h4>
						</div>
						<div class="col-md-6 text-right">
							<button class="btn btn-default lock" onclick="editFish('unlock')"><span class="fa fa-lock fa-lg"></span></button>
							<button class="btn btn-default unlock hidden" onclick="editFish('lock')"><span class="fa fa-unlock fa-lg"></span></button>
						</div>
					</div>
				</div>
				<div class="row">
					<h4 id="update_success" class="text-center hidden" style="background-color:#008000; color:white; margin-top:0; padding:10px">Successfully updated</h4>
					<h4 id="update_fail" class="text-center hidden" style="background-color:#d83232; color:white; margin-top:0; padding:10px">Error: Unable to update!</h4>
				</div>
				<div class="panel-body">
					<div class="col-xs-4 text-center">
						<?php if($image == 1) { ?>
							<img id="fish-thumbnail" src="<?php echo $src ?>" class="img-thumbnail" width="345"/>
						<?php } else { ?>
							<img id="fish-thumbnail" src="<?php echo $src ?>" class="img-thumbnail" width="250" height="auto"/>
						<?php } ?>
					</div>
					<div class="col-xs-8">
						<div class="row" hidden>
							<input id="fishCaughtUID" type="hidden" class="form-control" value="<?php echo $fishDetails[0]["fishCaughtUID"] ?>">
						</div>
						<div class="row" hidden>
							<input id="imageNumber" type="hidden" class="form-control" value="1"/>
						</div>
						<div class="row">
							<div class="col-xs-4 text-center">
								<label for="speciesCode">Species</label>
							</div>
							<div class="col-xs-8">
								<select id="species" class="form-control editFish" disabled>
								<?php
									$projectUID = $anglerDetails["projectUID"];
									$projectSpecies = new projectspecies();
									$projectSpeciesList = $projectSpecies->getAllProjectSpeciesByProjectUID($projectUID);
									echo "<option value='999'>Select One</option>";
									for($i=0; $i<count($projectSpeciesList); $i++){
										$speciesCode = $projectSpeciesList[$i]["speciesCode"];
										$speciesName = $projectSpeciesList[$i]["speciesNameFirst"];
										if(strcasecmp($fishDetails["0"]["speciesCode"],$speciesCode)==0){
											echo "<option value='".$speciesCode."' selected>".$speciesCode.": ".$speciesName."</option>";
										} else{
											echo "<option value='".$speciesCode."'>".$speciesCode.": ".$speciesName."</option>";
										}
									}
								?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-4 text-center">
								<label for="category">Category</label>
							</div>
							<div class="col-xs-8">
								<input id="category" type="text" class="form-control editFish" value="<?php echo $fishDetails[0]["category"] ?>" style="display:inline-block" readonly/>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-4 text-center">
								<label for="numCaught">Number Caught</label>
							</div>
							<div class="col-xs-8">
								<input id="numCaught" type="text" class="form-control editFish" value="<?php echo $fishDetails[0]["numberCaught"] ?>" style="display:inline-block" readonly/>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-4 text-center">
								<label for="length">Length (inches)</label>
							</div>
							<div class="col-xs-8">
								<input id="length" type="text" class="form-control editFish" value="<?php echo $fishDetails[0]["length"] ?>" style="display:inline-block" readonly/>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-4 text-center">
								<label for="measured">Measured</label>
							</div>
							<div class="col-xs-8">
								<select id="measured" class="form-control editFish" disabled>
									<option value="999">Select One</option>
									<?php 
										// $utilities = new utilities();
										// $measuredLabel = $utilities->getMeasured();
										$measured = $fishDetails[0]["measured"];
										if($measured == "Y"){
											echo "<option value='".$measured."' selected>Yes</option>";
											echo "<option value='N'>Yes, but NOT by MDC</option>";
											echo "<option value='NZ'>No</option>";
											echo "<option value='NA'>Refused</option>";
										} else if($measured =="N"){
											echo "<option value='Y'>Yes</option>";
											echo "<option value='".$measured."' selected>Yes, but NOT by MDC</option>";
											echo "<option value='NZ'>No</option>";
											echo "<option value='NA'>Refused</option>";
										} else if($measured =="NZ"){
											echo "<option value='Y'>Yes</option>";
											echo "<option value='N'>Yes, but NOT by MDC</option>";
											echo "<option value='".$measured."' selected>No</option>";
											echo "<option value='NA'>Refused</option>";
										} else if($measured =="NA"){
											echo "<option value='Y'>Yes</option>";
											echo "<option value='N'>Yes, but NOT by MDC</option>";
											echo "<option value='NZ'>No</option>";
											echo "<option value='".$measured."' selected>Refused</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 text-right">
								<button id="updateFish" class="btn btn-primary" type="button" onclick="updateFishDetails()" disabled>Update Fish Details</button>
							</div>
						</div>
					</div><!-- END COL-XS-8 -->
				</div> <!-- END PANEL BODY -->
				<div class="panel-footer">
					<div class="row">
						<div class="col-xs-2 text-center">
							<?php if(is_null($fishDetails) || count($fishDetails) < 2){ ?>
								<h5 style="margin:0 5px"><a onclick="previousFish($(this))"><button class="btn btn-default btn-block" disabled>Previous Fish</button></a></h5>
							<?php } else{ ?>
								<h5 style="margin:0 5px"><a onclick="previousFish($(this))"><button class="btn btn-default btn-block">Previous Fish</button></a></h5>
							<?php } ?>
						</div>
						<div class="col-xs-8 text-center image-list">
							<h5 style="margin:0 5px">
							<?php 
								if(!is_null($fishDetails) && count($fishDetails) > 1){
									for($i=0; $i< count($fishDetails); $i++){
										if($i==0){
											echo("<button id='fish".intval($i+1)."' fishCaughtUID='".$fishDetails[$i]['fishCaughtUID']."' class='imageOrders btn btn-default active' onclick='selectImage($(this))' >".intval($i+1)."</button>");	
										} else{
											echo("<button id='fish".intval($i+1)."' fishCaughtUID='".$fishDetails[$i]['fishCaughtUID']."' class='imageOrders btn btn-default' onclick='selectImage($(this))' >".intval($i+1)."</button>");
										}
									} 
								} else {
									echo("<button id='fish1 fishCaughtUID='".$fishDetails[0]['fishCaughtUID']."' onclick='selectImage($(this))' class='btn btn-default' disabled>1</button>");
								}
							?>
							</h5>
						</div>
						<div class="col-xs-2 text-center">
							<?php if(is_null($fishDetails) || count($fishDetails) < 2){ ?>
								<h5 style="margin:0 5px"><a onclick="nextFish($(this))"><button class="btn btn-default btn-block" disabled>Next Fish</button></a></h5>
							<?php } else { ?>
								<h5 style="margin:0 5px"><a onclick="nextFish($(this))"><button class="btn btn-default btn-block">Next Fish</button></a></h5>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } else { ?> 
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 style="margin-bottom:0">Fish Caught</h4>
					</div>
					<div class="panel-body">
						<h4>No Fish Caught</h4>
					</div>
				</div>

			<?php } ?>
			<!-- END IF-ELSE FISH CAUGHT DETAILS -->
			<!-- END FISH CAUGHT PANEL -->

			<!-- BEGIN OPTIONAL QUESTION PANEL -->
			<div class="panel panel-default optional-questions">
				<div class="panel-heading">
					<h4 style="margin:0">Optional Questions</h4>
				</div>
				<div class="panel-body">
					<?php
						// Optional Question Labels
						$optQ1 = $projectDetails["optionalQ1"];
						$optQ2 = $projectDetails["optionalQ2"];
						$optQ3 = $projectDetails["optionalQ3"];
						$optQ4 = $projectDetails["optionalQ4"];
						$optQ5 = $projectDetails["optionalQ5"];
						$optQ6 = $projectDetails["optionalQ6"];

						// Optional Question Answers
						$optA1 = $anglerDetails["optionalQ1"];
						$optA2 = $anglerDetails["optionalQ2"];
						$optA3 = $anglerDetails["optionalQ3"];
						$optA4 = $anglerDetails["optionalQ4"];
						$optA5 = $anglerDetails["optionalQ5"];
						$optA6 = $anglerDetails["optionalQ6"];


					?>
					<div class="row col-xs-6">
						<?php if($optQ1 != null && !empty(rtrim($optQ1))){ ?>
							<div class="row opt-question">
								<div class="col-xs-6">
									<label for="optQ1"><?php echo $optQ1 ?></label>
								</div>
								<div class="col-xs-6">
									<input id="optQ1" type="text" class="form-control" value="<?php echo $optA1 ?>" readonly/>
								</div>
							</div>
						<?php } ?>

						<?php if($optQ2 != null && !empty(rtrim($optQ2))){ ?>
							<div class="row opt-question">
								<div class="col-xs-6">
									<label for="optQ2"><?php echo $optQ2 ?></label>
								</div>
								<div class="col-xs-6">
									<input id="optQ2" type="text" class="form-control" value="<?php echo $optA2 ?>" readonly />
								</div>
							</div>
						<?php } ?>

						<?php if($optQ3 != null && !empty(rtrim($optQ3))){ ?>
							<div class="row opt-question">
								<div class="col-xs-6">
									<label for="optQ3"><?php echo $optQ3 ?></label>
								</div>
								<div class="col-xs-6">
									<input id="optQ3" type="text" class="form-control" value="<?php echo $optA3 ?>" readonly />
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="row col-xs-6">
						<?php if($optQ4 != null && !empty(rtrim($optQ4))){ ?>
							<div class="row opt-question">
								<div class="col-xs-6">
									<label for="optQ4"><?php echo $optQ4 ?></label>
								</div>
								<div class="col-xs-6">
									<input id="optQ4" type="text" class="form-control" value="<?php echo $optA4 ?>" readonly/>
								</div>
							</div>
						<?php } ?>

						<?php if($optQ5 != null && !empty(rtrim($optQ5))){ ?>
							<div class="row opt-question">
								<div class="col-xs-6">
									<label for="optQ5"><?php echo $optQ5 ?></label>
								</div>
								<div class="col-xs-6">
									<input id="optQ5" type="text" class="form-control" value="<?php echo $optA5 ?>" />
								</div>
							</div>
						<?php } ?>

						<?php if($optQ6 != null && !empty(rtrim($optQ6))){ ?>
							<div class="row opt-question">
								<div class="col-xs-6">
									<label for="optQ6"><?php echo $optQ6 ?></label>
								</div>
								<div class="col-xs-6">
									<input id="optQ6" type="text" class="form-control" value="<?php echo $optA6 ?>" />
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<!-- END OPTIONAL QUESTION PANEL -->

			<div class="panel panel-default">
					<div class="panel-heading"><h4 style="margin:2px">Verify Angler</h4></div>
					<div class="panel-body">
					<div class="row" style="margin-bottom:10px">
						<textarea id='anglerVerifyComments' class='form-control editable'><?php echo $anglerDetails['verifiedComments']?></textarea>
					</div>
					<div class="row">
						<div class="row buttons text-right">
							<button class="btn btn-md btn-primary verify" onclick="verify()">Verify</button>
							<!-- <button class="btn btn-md btn-warning edit" onclick="edit()">Edit</button> -->
							<?php echo ("<a href='party-details.php?partiesUID=".$partiesUID."&scheduleUID=".$scheduleUID."'><button class='btn btn-md btn-danger'>Cancel</button></a>"); ?>
						</div>
					</div>
				</div>
			</div>

			<?php } ?>
		</div><!-- END CONTAINER FLUID -->

		<?php if($error == 1){ 
				echo("<div class='navbar-fixed-bottom'>");
				include_once("../includes/footer.php");
				echo("</div>");
			} else { 
				include_once("../includes/footer.php");
			} 
		?>


		<!-- EXTERNAL JAVASCRIPT -->
		<?php include_once('../includes/js.php'); ?>
		<script type="text/javascript">
			$(document).ready(function(){
				/*var isCommentsDisabled = $("#anglerVerifyComments").prop('readonly');
				if(isCommentsDisabled){
					toggleVerifyButton("disable");
				} else {
					toggleVerifyButton("enable")
				}*/
			});

			// Disable verify button 
			// If angler comment is disabled
			// If editable fields are disabled
			function toggleVerifyButton(action){
				/*if(action == "enable"){
					$(".verify").removeProp("disabled");
				} else {
					$(".verify").prop("disabled",true);
				}*/
			}

			// Make disabled fields editable
			function editFish(action){
				if(action == "unlock"){
					$(".editFish").prop("readonly",false);
					$(".editFish").prop("disabled",false);
					$("#updateFish").prop("disabled",false);
					$(".unlock").removeClass("hidden");
					$(".lock").addClass("hidden");
				} else if(action == "lock"){
					$(".editFish").prop("readonly",true);
					$(".editFish").prop("disabled",true);
					$("#updateFish").prop("disabled",true);
					$(".lock").removeClass("hidden");
					$(".unlock").addClass("hidden");
				}
			}

			// Send to verification page
			function verify(){
				var comments = $("#anglerVerifyComments").val();
				if(comments.trim() != "" && comments != null){
					// Send anglerUID and clerk 
					var anglerUIDString = $("#anglerUID").text();
					var anglerUID = anglerUIDString.split(":")[1].trim();
					var clerk = $("#clerk").val();
					var verifiedDate = dateFormat(new Date(),"yyyy-mm-dd hh:MM:ss");
					var data = {anglerUID:anglerUID, clerk:clerk, comments:comments, verifiedDate:verifiedDate};
					
					// Update to database
					$.ajax({
						url:"../ajax-handlers/angler-handler.php",
						data:data,
						method:"POST",
						dataType:"text",
						success:function(result){
							// console.log(result);
							if(result == '1' || result == 1){
								// console.log("success");
								window.location="angler-details.php?anglerUID="+anglerUID;
							}
						},
						error:function(xhr, status, error){
							console.log("XHR: " + JSON.stringify(xhr));
							console.log("Status: " + JSON.stringify(status));
							console.log("Error: " + JSON.stringify(error));
						}
					})
				} else {
					toggleVerifyButton("disable");
				}
			}

			// Enable all editable fields
			/*function edit(){
				$(".editable").removeProp("readonly");
				$(".edit").prop("disabled","true");
				toggleVerifyButton("enable");
			}*/

			// Returns the number on button of active fish
			// Return type: integer
			function getActiveFish(){
				var imageNumber;
				$(".image-list button").each(function(){
					if($(this).hasClass("active")){
						imageNumber = parseInt($(this).attr("id").substr(4));
					}
				});
				return imageNumber;
			}

			// Get Previous Fish Information
			function previousFish(button){
				editFish("lock");
				hideMessage();
				// Check which fish is active right now
				var imageNumber = getActiveFish();

				// If the currently active button isn't 1
				// Else do nothing
				if(imageNumber > 1){
					var prevFishCaughtUID = $("#fish"+(imageNumber-1)).attr("fishCaughtUID");
					$.ajax({
						url:"../ajax-handlers/fishcaught-handler.php",
						method:"POST",
						data:{action:"getFish", fishCaughtUID:prevFishCaughtUID},
						dataType:'json',
						success:function(result){
							var fishCaught = result["fishCaught"];
							if(fishCaught != null){
								// Set Fish Caught Details
								setFishCaughtDetails(fishCaught);

								// Check if any photo available
								var photoTaken = fishCaught["photoTaken"];
								if(photoTaken == "1"){
									var fishCaughtImageDetails = result["fishCaughtImageDetails"];
									setImage(fishCaughtImageDetails);
								} else {
									// Set No Image available icon
									$("#fish-thumbnail").prop("src","../resources/images/no-image.png");
									$("#fish-thumbnail").css("height","250");
									$("#fish-thumbnail").css("width","250");
								}

								// Set New Active Button
								$("#fish"+imageNumber).removeClass("active");
								$("#fish"+(imageNumber-1)).addClass("active");
							}
						},
						error:function(xhr,status,error){
							console.log(JSON.stringify(xhr));
							console.log(JSON.stringify(status));
							console.log(JSON.stringify(error));	
						}
					});
				}
			}

			// Get Next Fish Information
			function nextFish(button){
				editFish("lock");
				hideMessage();

				// Check which fish is active right now
				var imageNumber = getActiveFish();

				// Only fetch record if last button is not active
				// Will need to disable next fish later when last fish is active
				var fishCount = $(".image-list").find("button").length; // Total number of fish caught by angler
				if(imageNumber != fishCount){
					var nextFishCaughtUID = $("#fish"+(imageNumber+1)).attr("fishCaughtUID");
					$.ajax({
						url:"../ajax-handlers/fishcaught-handler.php",
						method:"POST",
						data:{action:"getFish", fishCaughtUID:nextFishCaughtUID},
						dataType:'json',
						success:function(result){
							var fishCaught = result["fishCaught"];
							if(fishCaught != null){
								// Set Fish Caught Details
								setFishCaughtDetails(fishCaught);

								// Check if any photo available
								var photoTaken = fishCaught["photoTaken"];
								if(photoTaken == "1"){
									var fishCaughtImageDetails = result["fishCaughtImageDetails"];
									setImage(fishCaughtImageDetails);
								} else {
									// Set No Image available icon
									setNoImage();
								}

								// Set New Active Button
								$("#fish"+imageNumber).removeClass("active");
								$("#fish"+(imageNumber+1)).addClass("active");
							}
						},
						error:function(xhr,status,error){
							console.log(JSON.stringify(xhr));
							console.log(JSON.stringify(status));
							console.log(JSON.stringify(error));	
						}
					});
				}
			}

			// If a certain fish number button is pressed
			function selectImage(button){
				editFish("lock");
				hideMessage();

				var imageNumber = button.attr("id");
				var id = button.attr("fishCaughtUID");
				
				// If currently active fish is pressed 
				// Do nothing
				if(button.hasClass("active")){
					console.log("RETURNED");
					return;
				} else {
					$.ajax({
						url:"../ajax-handlers/fishcaught-handler.php",
						method:"POST",
						data:{action:"getFish", fishCaughtUID:id},
						dataType:'json',
						success:function(result){
							var fishCaught = result["fishCaught"];
							if(fishCaught != null){
								// Set Fish Caught Details
								setFishCaughtDetails(fishCaught);

								// Check if any photo available
								var photoTaken = fishCaught["photoTaken"];
								if(photoTaken == "1"){
									var fishCaughtImageDetails = result["fishCaughtImageDetails"];
									setImage(fishCaughtImageDetails);
								} else {
									// Set No Image available icon
									setNoImage();
								}

								// Remove active class from all buttons
								$(".image-list button").each(function(){
									$(this).removeClass("active");
								});
								// Set active class on button that was pressed
								$("#"+imageNumber).addClass("active");
							}
						},
						error:function(xhr,status,error){
							console.log(JSON.stringify(xhr));
							console.log(JSON.stringify(status));
							console.log(JSON.stringify(error));	
						}
					});
				}
			}

			// Set all meta information about the fish in the right hand side fields
			function setFishCaughtDetails(fishCaught){
				// Get Fish Caught Details
				var fishCaughtUID = fishCaught["fishCaughtUID"];
				var speciesCode = fishCaught["speciesCode"];
				// alert(speciesCode);
				var speciesCommonName = fishCaught["speciesCommonName"];
				var category = fishCaught["category"];
				var numberCaught = fishCaught["numberCaught"];
				var length = fishCaught["length"];
				var measured = fishCaught["measured"];

				// Set Fish Caught Details
				$("#fishCaughtUID").val(fishCaughtUID);
				$("#species").val(speciesCode);
				// $("#speciesCommonName").val(speciesCommonName);
				$("#category").val(category);
				$("#numberCaught").val(numberCaught);
				$("#length").val(length);

				// Add options to measured
				$("#measured").empty();
				$("#measured").append("<option value='999'>Select One</option>");
				$("#measured").append("<option value='Y'>Yes</option>");
				$("#measured").append("<option value='N'>Yes, but NOT by MDC</option>");
				$("#measured").append("<option value='NZ'>No</option>");
				$("#measured").append("<option value='NA'>Refused</option>");
				$("#measured").val(measured);
			}

			// Convert byte64 from database to image in image holder
			function setImage(fishCaughtImageDetails){
				$("#fish-thumbnail").prop("src","data:image/png;base64,"+fishCaughtImageDetails["image"]);
				$("#fish-thumbnail").removeAttr("style");
			}

			// If no image for fish caught, the set "no image available" image
			function setNoImage(){
				$("#fish-thumbnail").prop("src","../resources/images/no-image.png");
				$("#fish-thumbnail").css("height","250");
				$("#fish-thumbnail").css("width","250");
			}

			// Update fish details
			function updateFishDetails(){
				var fishCaughtUID = $("#fishCaughtUID").val();
				var speciesCode = $("#species option:selected").val();
				var category = $("#category").val();
				var numCaught = $("#numCaught").val();
				var length = $("#length").val();
				var measured = $("#measured option:selected").val();
				if((speciesCode == "" || speciesCode == "999") ||
					category == "" || 
					(numCaught == "" || numCaught == 0) || 
					(length == "" || length == 0) 
					|| measured == "999"){
					alert("Please fill out all the details");
					return;
				}

				var data = {
					action:"updateFish",
					fishCaughtUID:fishCaughtUID,
					speciesCode:speciesCode,
					category:category,
					numCaught:numCaught,
					length:length,
					measured:measured
				}

				// Update fishcaught
				$.ajax({
					url:"../ajax-handlers/fishcaught-handler.php",
					data:data,
					dataType:"json",
					method:"POST",
					success:function(result){
						if(result==1){
							$("#update_success").removeClass("hidden");
							$("#update_fail").addClass("hidden");
						} else {
							$("#update_success").addClass("hidden");
							$("#update_fail").removeClass("hidden");
						}
					}, 
					error:function(xhr,status,error){
						console.log(JSON.stringify(xhr));
						console.log(JSON.stringify(status));
						console.log(JSON.stringify(error));	
					}
				});
			}

			function hideMessage(){
				$("#update_success").addClass("hidden");
				$("#udpate_fail").addClass("hidden");
			}
		</script>
	</body>
</html>