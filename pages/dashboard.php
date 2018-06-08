<?php
	include_once("../class/users.class.php");
	include_once("../class/projects.class.php");
	include_once("../class/schedules.class.php");

	ini_set('display_errors', 1);

	session_start();
	extract($_POST);
	extract($_GET);
	$error = 0;

	date_default_timezone_set('America/Chicago');
	$today = date("m-d-Y H:i");

	$tblprojects = new projects();
	$allProjects = $tblprojects->getProjectsForTreeview();

	if(isset($_SESSION["username"])){
		$username = $_SESSION["username"];
	} else {
		header("location:index.php");
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>

		<!-- EXTERNAL CSS -->
		<?php include_once("../includes/css.php") ?>
		<link rel="stylesheet" href="../resources/css/tree-menu.css" />
		<!-- INTERNAL CSS -->
		<style>
			.bold{
				font-weight:bold;
			}
			.demo{
				height:400px;
				overflow-y:auto;
			}
			.sidebar{
				background-color:white; 
				border-radius:3px;
				padding:0;
			}
			.col-xs-2{
				width:20%;
			}
			.col-xs-10{
				width:80%;
			}
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<?php include_once('../includes/navbar.php') ?>
		
		<div class="container-fluid">
			<div class="row col-xs-12 col-sm-4 col-md-3 sidebar" style="border-radius:0">
				<div class="panel panel-default" style="margin-bottom:0">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-6" style="padding-left:0;margin-top:4px">Project Tree</div>
							<!-- <div class="col-xs-6 text-right" style="padding-right:0;"><button class="btn btn-default btn-small" onclick="collapse()" style="cursor:pointer;text-decoration; padding-top:0; padding-bottom:0">Collapse</button></div> -->
							<div class="col-xs-6" style="padding-left:0; padding-right:0">
								<select class="form-control" id="sort_by" style="padding:5px; height:30px">
									<option value="date" selected>By Date</option>
									<option value="location">By Location</option>
								</select>
							</div>
						</div>
					</div>
					<div class="panel-body" style="padding:0">
						<div class="row tree-menu demo" id="tree-menu">
							<?php
								echo("<ul>");
								// BEGIN FOR LOOP - PROJECT NAMES
								for($i=0; $i < count($allProjects); $i++){ 
									$projectUID = $allProjects[$i]["projectUID"];
									$projectName = $allProjects[$i]["projectName"];
									$countProjects = $allProjects[$i]["countProjects"];
									$projectStartDate = $allProjects[$i]["projectStartDate"];
									$projectStopDate = $allProjects[$i]["projectStopDate"];
									$startDate = DateTime::createFromFormat('Y-m-d H:i:s', $projectStartDate);
									$stopDate = DateTime::createFromFormat('Y-m-d H:i:s', $projectStopDate);
									$startDate = $startDate->format('Y-m-d H:i:s');
									$stopDate = $stopDate->format("Y-m-d H:i:s");

									$currentDateTime = date("Y-m-d H:i:s");
									// Append Project Name
									// Check if project has already started
									// If project has not started then bg-gray
									if($currentDateTime < $startDate){
										echo("<li id='".$projectUID."'><a class='bg-gray' onclick='expandProject($(this))' id='".$projectUID."'>".$projectName." (".$countProjects.")</a>");
									} else{
										// else check the verified statuses of schedules in project
										$tblschedules = new schedules();
										$verifiedStatusGroups = $tblschedules->getVerificationStatusByProjectUID($projectUID);

										// Check whether all surveys (aka schedules) have been completed
										// 0 == date in past but has no data
										if(count($verifiedStatusGroups) == 0){
											echo("<li id='".$projectUID."'><a class='bg-red' onclick='expandProject($(this))' id='".$projectUID."'>".$projectName." (".$countProjects.")</a>");
										} else if(count($verifiedStatusGroups) == 1){
											// Only has verified data or has only missing data
											if($verifiedStatusGroups[0]["verifiedStatus"] == 0){
												echo("<li id='".$projectUID."'><a class='bg-orange' onclick='expandProject($(this))' id='".$projectUID."'>".$projectName." (".$countProjects.")</a>");
											} elseif($verifiedStatusGroups[0]["verifiedStatus"] == 1) {
												echo("<li id='".$projectUID."'><a class='bg-green' onclick='expandProject($(this))' id='".$projectUID."'>".$projectName." (".$countProjects.")</a>");
											}
										} else {
											echo("<li id='".$projectUID."'><a class='bg-orange' onclick='expandProject($(this))' id='".$projectUID."'>".$projectName." (".$countProjects.")</a>");
										}
									}
									echo("<ul><li id='empty-child'><a></a></li></ul>");
								} // end for loop
							?> 
						</div> <!-- END TREE VIEW -->
					</div>
					<div class="panel-heading">Legends</div>
					<div class="panel-body" style="padding:0">
						<div class="row legends" id="legends-box">
							<table class="table table-bordered" id="legends-table" style="font-size:11px; margin-bottom:0px">
								<tbody>
									<tr>
										<td class="bg-orange"></td>
										<td>Unverified Data</td>
										<td class="bg-gray"></td>
										<td>Not Started</td>
									</tr>
									<tr>
										<td class="bg-green"></td>
										<td>Verified Data</td>
										<td class="bg-red"></td>
										<td>Missing Data</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div><!-- END SIDEBAR -->

			<!-- MAIN BODY -->
			<div class="row col-xs-12 col-sm-8 col-md-9 creel-details">
				<div class="no-survey">
					<div class="panel panel-default">
						<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;Information</div>
						<div class="panel-body">
							<h4>No Survey Selected</h4>
						</div>
					</div>
				</div>
				<!-- BEGIN SURVEY DATE/TIME -->
				<div class="row hidden-table hidden">
					<h4>Project Details</h4>
					<table id="survey-data" class="table table-bordered table-condensed text-center">
						<tbody>
							<tr>
								<td class="label-background"><label>Project Name</label></td>
								<td id="projectName">-</td>
								<td class="label-background"><label>Project Location</label></td>
								<td id="surveyLocation" colspan="3">-</td>
							</tr>
							<tr>
								<td class="label-background"><label>Survey Date</label></td>
								<td id="surveyDate">-</td>
								<td class="label-background"><label>Start Time</label></td>
								<td id="surveyStartTime">-</td>
								<td class="label-background"><label>End Time</label></td>
								<td id="surveyStopTime">-</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- END SURVEY DATE/TIME -->
				
				<!-- BEGIN SURVEY HEADER DATA -->
				<div class="row hidden-table hidden">
					<h4>Survey Header</h4>
					<table id="survey-data" class="table table-bordered table-condensed text-center">
						<thead>
							<tr>
								<th class="text-center label-background">Secchi</th>
								<th class="text-center label-background">Water Temperature</th>
								<th class="text-center label-background">Water Level</th>
								<th class="text-center label-background">Clerk</th>
								<th class="text-center label-background">Status</th>
								<th class="text-center label-background">Comments</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td id="secchi">-</td>
								<td id="wTemp">-</td>
								<td id="wLevel">-</td>
								<td id="clerk">-</td>
								<td id="surveyStatus">-</td>
								<td id="surveyComments">-</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- END SURVEY HEADER DATA -->

				<!-- BEGIN COUNTS DATA -->
				<div class="row hidden-table hidden">
					<h4>Counter</h4>
					<table id="count-data" class="table table-bordered table-condensed text-center">
						<thead>
							<tr>
								<th id="counter1" class="text-center label-background">Counter 1</th>
								<th id="counter2" class="text-center label-background">Counter 2</th>
								<th id="counter3" class="text-center label-background">Counter 3</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td id="count1">-</td>
								<td id="count2">-</td>
								<td id="count3">-</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- END COUNTS DATA -->

				<!-- BEGIN PARTY DETAILS -->
				<div class="row hidden-table hidden">
					<h4>Parties in Survey</h4>
					<table id="partyTable" class="table table-bordered table-striped text-center">
						<thead>
							<tr class="label-background">
								<th class="text-center">Party #</th>
								<th class="text-center">Party Size</th>
								<th class="text-center">Party Status</th>
								<th class="text-center">Party Comments</th>
								<th class="text-center">Date Created</th>
								<th class="text-center">Uploaded Date</th>
								<th class="text-center">Verified</th>
							</tr>
						</thead>
						<tbody>
							<!-- ADDED VIA JQUERY -->
						</tbody>
					</table>
				</div> <!-- END TABLE -->
			</div> <!-- END PARTY DETAILS -->
		</div><!-- END CONTAINER FLUID -->

		<?php include_once('../includes/footer.php') ?>
		<!-- END HTML -->

		<!-- EXTERNAL JAVASCRIPT -->
		<?php include_once('../includes/js.php'); ?>

		<!-- INTERNAL JAVASCRIPT -->
		<script type="text/javascript" src="../resources/js/ntm.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				// Initialize Tree Menu
				$(".demo").ntm();
			});

			$("#sort_by").on("change",function(){
				collapseAll();
			});

			// Collapse all open rows in treeview
			function collapseAll(){
				$("#tree-menu ul li").each(function(){
					if($(this).hasClass("opened")){
						var row = $(this).find("a");
						row.parent("li").removeClass("opened");
						row.parent("li").addClass("closed");
						row.parent("li").find("ul").css("display","none");
						row.parent("li").find("ul").empty().append("<li><a></a></li>");
					}
				});
			}

			// Lazy Load the Tree View
			// Expand tree and get child-items from database ONLY when + icon is clicked
			// Data size might get too large at initial load, thus, lazy load
			function expandProject(row){
				var id = row.attr("id");
				var sortBy = $("#sort_by option:selected").val();
				
				// AJAX call to get database records
				var url = "../ajax-handlers/schedule-handler.php";
				var data = {action: "expandProject", projectUID: id, sort:sortBy};
				
				// EXPAND PROJECTS
				$.ajax({
					url:url,
					method:'POST',
					data:data,
					dataType:'json',
					success:function(response){
						var result = response[0]; // Months (Count)
						var verifiedSchedules = response[1];
						// If months exist
						if(result.length > 0){

							// Append to obtained list to the li 
							row.parents("li").find("ul").empty();
							var list = row.parents("li").find("ul");
							var projectUID = id;
							var record;
							var total;
							var verifiedStatusGroup;
							var today = new Date();
							var now = today.getTime();
							var surveyStartTime;

							// Loop each Month
							for(var i=0; i<result.length; i++){
								verifiedSchedulesForMonth = verifiedSchedules[i];
								record = result[i];

								surveyStartDateTime = record["surveyStartDateTime"];
								surveyStopDateTime = record["surveyStopDateTime"];
								total = record["total"];
								
								// Get millisecond time for comparison
								surveyStartTime = new Date(surveyStartDateTime);
								surveyStartTime = surveyStartTime.getTime();

								surveyStopTime = new Date(surveyStopDateTime);
								surveyStopTime = surveyStopTime.getTime();
								surveyMonth = dateFormat(surveyStopDateTime,"mmmm yyyy");

								// If month is in future, background == gray
								if(now < surveyStartTime){
									list.append("<li class='parent closed'><a class='bg-gray' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a><ul><li></li></ul></li>");
								} 
								// If month is in past
								// Check if month has any schedules
								else {
									// If there were no schedules in this month in the past
									if(total == 0){
										list.append("<li class='parent closed'><a class='bg-red' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a></li>");
									} else{
										// Check to see the total verified status in month
										var typeOfVerifiedStatus = verifiedSchedulesForMonth.length;
										// If there are no verified statuses
										if(typeOfVerifiedStatus==0){
											list.append("<li class='parent closed'><a class='bg-red' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a><ul><li></li></ul></li>");
										} 
										// If there are only one type of verified statuses
										else if(typeOfVerifiedStatus == 1){
											// If verified status is unverified
											if(verifiedSchedulesForMonth[0]["verifiedStatus"]==0){
												list.append("<li class='parent closed'><a class='bg-orange' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a><ul><li></li></ul></li>");
											} 
											// If verified status is verified
											else if(verifiedSchedulesForMonth[0]["verifiedStatus"]==1){
												// If total verified schedules = total schedules in month then green
												if(parseInt(verifiedSchedulesForMonth[0]["total"]) == total){
													list.append("<li class='parent closed'><a class='bg-green' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a><ul><li></li></ul></li>");
												} 
												// Else background is orange
												else {
													list.append("<li class='parent closed'><a class='bg-orange' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a><ul><li></li></ul></li>");
												}
											}
										} 
										// If both types of verification status in month, i.e. verified AND unverified
										else if(typeOfVerifiedStatus > 1){
											list.append("<li class='parent closed'><a class='bg-orange' onclick='expandMonths($(this))' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime = '"+surveyStopDateTime+"' li-level='months'>" + surveyMonth + " ("+total+")</a><ul><li></li></ul></li>");
										}
									}
								}
							} // end loop by month
						} // end success function
					},
					error:function(xhr, status, error){
						console.log("XHR: " + JSON.stringify(xhr));
						console.log("Status: " + status);
						console.log("Error: " + error);
					}
				}); // end ajax function
			} // end function expand project

			// Expand Months
			function expandMonths(row){
				var open = row.parent("li").hasClass("opened");
				if(open){
					row.parent("li").removeClass("opened");
					row.parent("li").addClass("closed");
					row.parent("li").find("ul").css("display","none");
				} else{
					var url ="../ajax-handlers/schedule-handler.php";
					var projectUID = row.attr("projectUID");
					var surveyStartDateTime = row.attr("surveyStartDateTime");
					var surveyStopDateTime = row.attr("surveyStopDateTime");
					var action = "expandMonths";
					var sortBy = $("#sort_by option:selected").val();
					var data = {
									action: action,
									projectUID: projectUID, 
									surveyStartDateTime:surveyStartDateTime, 
									surveyStopDateTime: surveyStopDateTime, 
									sort:sortBy
								};
					// Call schedule-handler to EXPAND MONTHS
					$.ajax({
						url:url,
						method:"POST",
						data:data,
						dataType:"json",
						success:function(response){
							if(response.length > 0){
								// Append to obtained list to the li
								openParent(row);
								var list = row.parent("li").find("ul");
								
								// Append survey location schedule to tree view
								var schedule;
								var scheduleUID;
								var surveyStartDateTime;
								var surveyStopDateTime;
								var surveyLocation;
								var verifiedParties;

								var today = new Date();
								var now = today.getTime();

								// Loop each schedule
								for(var i=0; i<response.length; i++){
									schedule = response[i];
									scheduleUID = schedule["scheduleUID"];
									surveyStartDateTime = schedule["surveyStartDateTime"];
									surveyStopDateTime = schedule["surveyStopDateTime"];
									surveyLocation = schedule["surveyLocation"];
									verifiedParties = schedule["verifiedParties"];
									
									// Get millisecond time for comparison
									surveyStopTime = new Date(surveyStopDateTime);
									surveyStopTime = surveyStopTime.getTime();
									
									if(surveyStopTime > now){
										list.append("<li><a class='bg-gray' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime='"+surveyStopDateTime+"' onclick='showTables($(this))' scheduleUID='"+scheduleUID+"'>" + dateFormat(surveyStartDateTime,"mm/dd HH:MM") + "-" + dateFormat(surveyStopDateTime,"HH:MM") +" "+ surveyLocation + "</a></li>");
									} else {
										if(verifiedParties.length == 0){
											list.append("<li><a class='bg-red' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime='"+surveyStopDateTime+"' onclick='showTables($(this))' scheduleUID='"+scheduleUID+"'>" + dateFormat(surveyStartDateTime,"mm/dd HH:MM") + "-" + dateFormat(surveyStopDateTime,"HH:MM") +" "+ surveyLocation + "</a></li>");
										} else if(verifiedParties.length == 1) {
											if(verifiedParties[0]["verifiedStatus"] == "0"){
												list.append("<li><a class='bg-orange' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime='"+surveyStopDateTime+"' onclick='showTables($(this))' scheduleUID='"+scheduleUID+"'>" + dateFormat(surveyStartDateTime,"mm/dd HH:MM") + "-" + dateFormat(surveyStopDateTime,"HH:MM") +" "+ surveyLocation + "</a></li>");
											} else if(verifiedParties[0]["verifiedStatus"] == "1"){
												list.append("<li><a class='bg-green' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime='"+surveyStopDateTime+"' onclick='showTables($(this))' scheduleUID='"+scheduleUID+"'>" + dateFormat(surveyStartDateTime,"mm/dd HH:MM") + "-" + dateFormat(surveyStopDateTime,"HH:MM") +" "+ surveyLocation + "</a></li>");
											}
										} else if(verifiedParties.length > 1){
											list.append("<li><a class='bg-orange' projectUID='"+projectUID+"' surveyStartDateTime='"+surveyStartDateTime+"' surveyStopDateTime='"+surveyStopDateTime+"' onclick='showTables($(this))' scheduleUID='"+scheduleUID+"'>" + dateFormat(surveyStartDateTime,"mm/dd HH:MM") + "-" + dateFormat(surveyStopDateTime,"HH:MM") +" "+ surveyLocation + "</a></li>");
										}
									}
								}
							} else {
								row.parents("li").removeClass("parent-item");
							}
						}, 
						error:function(xhr,status,error){
							console.log("XHR Survey: " + JSON.stringify(xhr));
							console.log("Status: " + status);
							console.log("Error: " + error);
						}
					});
					row.parent("li").removeClass("closed");
					row.parent("li").addClass("opened");
					row.parent("li").find("ul").css("display","block");
				}
			}

			/** SURVEY LOCATION OPEN-CLOSE FUNCTIONS **/
			// Close Location Parent Item
			function closeParent(row){
				row.parent("li").removeClass("opened");
				row.parent("li").addClass("closed");
				row.parent("li").find("ul").empty();
				//row.parent("li").find("ul").css("display","none");
			}

			// Open Location Item
			function openParent(row){
				row.parent("li").find("ul").empty();
				row.parent("li").removeClass("closed");
				row.parent("li").addClass("opened");
				row.parent("li").find("ul").css("display","block");
			}


			// Get Table Rows from PHP Handlers
			// Survey Headers -- tblheaders
			// Party List -- tblParties 
			// Using scheduleUID
			function showTables(row){
				$(".hidden-table").removeClass("hidden");
				$(".no-survey").addClass("hidden");
				
				// Get Data from Treeview
				var sortBy = $("#sort_by option:selected").val();
				var scheduleUID = row.attr('scheduleUID');
				var project;
				var location="";
				var datetime;
				var date;
				var startTime;
				var stopTime;
				if(sortBy == "location"){
					project = row.parents("li").find("a").text().split("(")[0];
					location = row.parent("li").parent("ul").parent("li").find("a").text().split("(")[0];
					datetime = row.parent("li").find("a").text().split(" ");
					date = datetime[0];
					startTime = datetime[1].split("-")[0];
					stopTime = datetime[1].split("-")[1];
				} else if (sortBy == "date"){
					project = row.parents("li").find("a").text().split("(")[0];
					var locationString = row.text().split(" ");
					for(var i=2; i<locationString.length; i++){
						location = location + " " + locationString[i];
					}
					datetime = row.text().split(" ");
					date = datetime[0];
					startTime = datetime[1].split("-")[0];
					stopTime = datetime[1].split("-")[1];
				}
				
				// Fill data in Survey Details
				$("#projectName").text(project);
				$("#surveyLocation").text(location);
				$("#surveyDate").text(date);
				$("#surveyStartTime").text(startTime);
				$("#surveyStopTime").text(stopTime);
				$.ajax({
					url:"../ajax-handlers/schedule-handler.php",
					method:'POST',
					data:{
						scheduleUID:scheduleUID
					},
					dataType:'json',
					success:function(result){
						var headerList = result["headerList"];
						if(headerList.length > 0){
							// Get Header Information
							var header = headerList[0];
							var secchi = header["secchi"];
							var wTemp = header["waterTemperature"];
							var wLevel = header["waterLevel"];
							var clerk = header["clerk"];
							var status = header["headerStatus"];
							
							var surveyStatus;
							switch(status){
								case "0":
									surveyStatus = "Cancelled";
									break;
								case "1":
									surveyStatus = "Missed";
									break;
								case "2":
									surveyStatus = "Partial";
									break;
								case "3":
									surveyStatus = "Completed";
									break;
								default:
									surveyStatus = "-";
							}

							var comments = header["clerkComments"];

							// Put header information in the table
							$("#secchi").text(secchi);
							$("#wTemp").text(wTemp);
							$("#wLevel").text(wLevel);
							$("#clerk").text(clerk);
							$("#surveyStatus").text(surveyStatus);
							$("#surveyComments").text(comments);
						} else {
							// Empty all existing data
							$("#secchi").text("-");
							$("#wTemp").text("-");
							$("#wLevel").text("-");
							$("#clerk").text("-");
							$("#surveyStatus").text("-");
							$("#surveyComments").text("-");
						}

						var countsList = result["countsList"];
						var project = result["project"];
						var projectType = project["projectType"];
						if(projectType == "Access"){
							$("#counter1").text("Angler");
							$("#counter2").text("Non-Angler");
							$("#counter3").text("Non-Contact");
						} else if(projectType == "Stream" || projectType == "Roving"){
							$("#counter1").text("Boat");
							$("#counter2").text("Bank");
							$("#counter3").text("PI Boat");
						}
						
						if(countsList.length > 0){
							// Get Header Information
							var count = countsList[0];
							var count1 = count["counter1"];
							var count2 = count["counter2"];
							var count3 = count["counter3"];
							
							// Put header information in the table
							$("#count1").text(count1);
							$("#count2").text(count2);
							$("#count3").text(count3);
						} else {
							// Empty all existing data
							$("#count1").text("-");
							$("#count2").text("-");
							$("#count3").text("-");
						}



						var partyList = result["partyList"];
						// Empty Table Body
						var table = $("#partyTable");
						table.find("tbody").empty();

						if(partyList.length > 0){
							// Get Party Details
							// Add each party detail to new row
							var party;
							var partiesUID;
							var partyNumber;
							var partySize;
							var partyStatus;
							var partyComments;
							var dateCreated;
							var uploadDate;
							var verified = "0";
							var lastRow;
							for(var i=0; i<partyList.length; i++){
								party = partyList[i];
								partiesUID = party["partiesUID"];
								partyNumber = party["partyNumber"];
								partySize = party["partySize"];
								partyStatus = party["partyStatus"];
								switch(partyStatus){
									case "0":
										partyStatus = "Completed";
										break;
									case "1":
										partyStatus = "Refused";
										break;
									default:
										partyStatus = "-";
										break;
								}
								partyComments = party["partyComments"];
								dateCreated = party["dateCreated"];
								uploadDate = party["uploadDate"];
								verified = party["verifiedStatus"];
								if(verified == null || verified == ""){
									verified = "0";
								}

								table.find("tbody").append("<tr>");
								lastRow = table.find("tbody tr:last");
								lastRow.append("<td>" + partyNumber + "</td>");
								lastRow.append("<td>" + partySize + "</td>");
								lastRow.append("<td>" + partyStatus + "</td>");
								lastRow.append("<td>" + partyComments + "</td>");
								lastRow.append("<td>" + dateFormat(dateCreated,"mmm-dd-yyyy HH:MM:ss") + "</td>");
								lastRow.append("<td>" + dateFormat(uploadDate,"mmm-dd-yyyy HH:MM:ss") + "</td>");
								if(verified == "0"){
									lastRow.append("<td><a href='party-details.php?partiesUID="+partiesUID+"&scheduleUID="+scheduleUID+"'><button type='button' id='" + partiesUID + "' class='btn btn-default bg-red'><span class='glyphicon glyphicon-remove'></span></button></a></td>");
								} else if(verified == "1") {
									lastRow.append("<td><a href='party-details.php?partiesUID="+partiesUID+"&scheduleUID="+scheduleUID+"'><button type='button' id='" + partiesUID + "' class='btn btn-default bg-green'><span class='glyphicon glyphicon-ok'></span></button></a></td>");
								}
								table.find("tbody").append("</tr>");
							}
						} else {
							// Append New TR to table stating no parties available for this survey
							table.find("tbody").append("<tr><td colspan='7'><h4>NO PARTIES IN THIS SURVEY</h4></td></tr>");
						}
					},
					error:function(xhr,status,error){
						console.log("XHR: " + JSON.stringify(xhr));
						console.log("Status: " + status);
						console.log("Error: " + error);
					}
				});
			}
		</script>
	</body>
</html>