<?php
	include_once('../class/schedules.class.php');
	include_once('../class/header.class.php');
	include_once("../class/parties.class.php");
	include_once("../class/counts.class.php");
	
	// If scheduleUID is sent, return value to show on tables
	if(isset($_POST["scheduleUID"])){
		$scheduleUID = $_POST["scheduleUID"];

		// Get Survey Headers for each schedule
		$tblheader = new header();
		$head = $tblheader->getHeaderByScheduleUID($scheduleUID);

		// Get party list for each survey schedule
		$tblparties = new parties();
		$party = $tblparties->getPartiesByScheduleUID($scheduleUID);

		// Get counts
		$tblcounts = new counts();
		$counts = $tblcounts->getCountsByScheduleUID($scheduleUID);

		$tblschedules = new schedules();
		$project = $tblschedules->getProjectByScheduleUID($scheduleUID);

		$arr = [];
		$arr["headerList"] = $head;
		$arr["partyList"] = $party;
		$arr["countsList"] = $counts;
		$arr["project"] = $project;
		
		echo json_encode($arr);
	}

	// Get database records to expand treeview
	// EXPAND PROJECTS
	else if(isset($_POST["action"]) 
				&& $_POST["action"]=="expandProject" 
					&& isset($_POST["projectUID"])){
		$tblschedules = new schedules();

		// Get PROJECT details
		$projectUID = $_POST["projectUID"];
		$finalArray = array();
		
		// Get the list of months in the project
		$res = $tblschedules->getScheduleMonthsByProjectUID($projectUID);
		array_push($finalArray, $res);
		
		// Get the schedule list for each month
		$verifiedSchedules = array();
		for($i=0; $i<count($res);$i++){
			$surveyStartDateTime = $res[$i]["surveyStartDateTime"];
			$surveyStopDateTime = $res[$i]["surveyStopDateTime"];
			$scheduleStatusGroup = $tblschedules->getVerificationStatusByDate($projectUID, $surveyStartDateTime, $surveyStopDateTime);
			array_push($verifiedSchedules, $scheduleStatusGroup);
		}

		// Push to single array
		array_push($finalArray, $verifiedSchedules);
		
		// Return JSON array
		echo json_encode($finalArray);
	} 

	// Get survey location names using start datetime and projectUID
	// EXPAND SURVEYSTARTDATETIME OR EXPAND MONTHS
	else if(isset($_POST["action"]) 
				&& ($_POST["action"]=="expandSurveyStartDateTime" || $_POST["action"]=="expandMonths")
					&& isset($_POST["surveyStartDateTime"]) 
						&& isset($_POST["surveyStopDateTime"]) 
							&& isset($_POST["projectUID"])
								&& isset($_POST["sort"])){
		
		// Create Schedule Objects
		$tblschedules = new schedules();

		// Get parameters
		$action = $_POST["action"];
		$projectUID = $_POST["projectUID"];
		$surveyStartDateTime = $_POST["surveyStartDateTime"];
		$surveyStopDateTime = $_POST["surveyStopDateTime"];
		$sort = $_POST["sort"];

		$res = array();
		// EXPAND SURVEY DATE TIMES
		if(strcasecmp($action, "expandSurveyStartDateTime")==0){
			$res = $tblschedules->getSurveyLocationsBySurveyStartDateTimeAndProjectUID($projectUID, $surveyStartDateTime, $surveyStopDateTime);
			array_push($finalArray, $res);
		} 
		// EXPAND MONTHS
		else if(strcasecmp($action, "expandMonths")==0){
			// Get list of surveys between the given start and stop datetime
			$res = $tblschedules->getSchedulesByDate($projectUID, $surveyStartDateTime, $surveyStopDateTime, $sort);

			// Get verification status groups for the month
			$tblparties = new parties();
			for($i=0; $i<count($res);$i++){
				$schedule = $res[$i];
				$scheduleUID = $schedule["scheduleUID"];
				$verifiedParties = $tblparties->getVerifiedPartiesByScheduleUID($scheduleUID);
				$res[$i]["verifiedParties"] = $verifiedParties;
			}
		}	
		// Return JSON array
		echo json_encode($res);
	}
?>