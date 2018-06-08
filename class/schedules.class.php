<?php
	require_once('connect.class.php');
	class schedules
	{
		// Get All Schedule
		public function getAllSchedules(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblschedule");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get schedules using projectUID
		public function getSchedulesByProjectUID($projectUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT scheduleUID,surveyStartDateTime,surveyStopDateTime,surveyLocation 
										FROM tblschedule 
										WHERE projectUID=:projectUID");
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get project using scheduleUID
		public function getProjectByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT project.* 
										FROM tblprojects project, tblschedule sched 
										WHERE sched.scheduleUID=:scheduleUID
										AND sched.projectUID = project.projectUID");
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch(PDO::FETCH_ASSOC);
			return $res;
		}

		// Get surveyLocations using projectUID
		public function getSurveyLocationsByProjectUID($projectUID){
			$dbcon = new connect();
			$qryString = "SELECT surveyLocation, count(surveyLocation) as countSurveyLocation
										from tblschedule 
										where projectUID = :projectUID 
										AND surveyStatus != '1'
										GROUP BY surveyLocation";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get schedules using scheduleUID
		public function getScheduleByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblschedule WHERE scheduleUID=:scheduleUID AND surveyStatus !='1'");
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Get schedules using surveyLocation
		public function getScheduleBySurveyLocation($surveyLocation){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT scheduleUID, surveyStartDateTime, surveyStopDateTime 
										from tblschedule 
										where surveyLocation = :surveyLocation 
										AND surveyStatus != '1'
										ORDER BY surveyStartDateTime DESC");
			$qry->bindParam(":surveyLocation",$surveyLocation,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get surveyLocations using projectUID
		public function getSurveyLocationsByDate($projectUID, $surveyStartDateTime, $surveyStopDateTime){
			$dbcon = new connect();
			$qryString = "SELECT scheduleUID, surveyLocation 
							from tblschedule
							where projectUID = :projectUID
							and surveyStatus != '1'
							and surveyStartDateTime = :surveyStartDateTime
							and surveyStopDateTime = :surveyStopDateTime";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->bindParam(":surveyStartDateTime",$surveyStartDateTime,PDO::PARAM_STR);
			$qry->bindParam(":surveyStopDateTime",$surveyStopDateTime,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get list of schedules by surveyDate
		public function getSchedulesByDate($projectUID, $surveyStartDateTime, $surveyStopDateTime, $sort){
			$dbcon = new connect();
			$sortQueryString = "";
			if(strcasecmp($sort,"date")==0){
				$sortQueryString = "surveyStartDateTime";
			} else if(strcasecmp($sort,"location")==0){
				$sortQueryString = "surveyLocation";
			}

			$qryString = "SELECT scheduleUID, surveyStartDateTime, surveyStopDateTime, surveyLocation 
							FROM tblschedule 
							WHERE projectUID=:projectUID
							AND surveyStartDateTime >= :surveyStartDateTime 
							AND surveyStopDateTime <= :surveyStopDateTime
							ORDER BY ".$sortQueryString." desc";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->bindParam(":surveyStartDateTime",$surveyStartDateTime,PDO::PARAM_STR);
			$qry->bindParam(":surveyStopDateTime",$surveyStopDateTime,PDO::PARAM_STR);
			// $qry->bindParam(":sort",$sortQueryString,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}


		// Get Schedule Start and Stop Dates by Project UID
		public function getScheduleDatesByProjectUID($projectUID){
			$dbcon = new connect();
			$qryString = "SELECT surveyStartDateTime, surveyStopDateTime, 
							count(surveyStartDateTime) as countSurveyStartDateTime 
							from tblschedule
							where projectUID = :projectUID
							and surveyStatus != '1'
							group by surveyStartDateTime
							order by surveyStartDateTime desc";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get total number of schedules in each month
		// e.g. month_year format == "October 2017"
		public function getScheduleMonthsByProjectUID($projectUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT date_format(surveyStartDateTime,'%M %Y') as month_year, 
											min(surveyStartDateTime) as surveyStartDateTime, 
											max(surveyStopDateTime) as surveyStopDateTime, 
											count(surveyStartDateTime) as total
											from tblschedule 
											where projectUID = :projectUID
											group by date_format(surveyStartDateTime,'%M %Y') 
											order by surveyStartDateTime desc");
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		/************** VERIFICATION STATUSES *************/
		// Get list of verified schedules in each month
		public function getVerificationStatusByProjectUID($projectUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT sched.scheduleUID, parties.verifiedStatus, 
										count(parties.verifiedStatus) as total
										from tblschedule sched, tblparties parties
										where sched.projectUID = :projectUID
										and sched.scheduleUID = parties.scheduleUID
										group by sched.scheduleUID, parties.verifiedStatus");
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get verified status by scheduleUID
		public function getVerificationStatusByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT party.verifiedStatus AS verifiedStatus, 
											count(party.verifiedStatus) AS total
										FROM tblschedule sched, tblparties party
										WHERE sched.scheduleUID = :scheduleUID
										AND sched.scheduleUID = party.scheduleUID
										GROUP BY party.verifiedStatus");
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get list of verified schedules in each month
		public function getVerificationStatusByDate($projectUID, $startDate, $stopDate){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT sched.scheduleUID, parties.verifiedStatus, 
										count(parties.verifiedStatus) as total
										from tblschedule sched, tblparties parties
										where sched.projectUID = :projectUID
										and sched.surveyStartDateTime >= :startDate
										and sched.surveyStopDateTime <= :stopDate
										and sched.scheduleUID = parties.scheduleUID
										group by sched.scheduleUID, parties.verifiedStatus");
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->bindParam(":startDate",$startDate,PDO::PARAM_STR);
			$qry->bindParam(":stopDate",$stopDate,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get locations grouped by verified status using location name
		public function getVerificationStatusByLocationName($location){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT party.verifiedStatus AS verifiedStatus, 
											count(party.verifiedStatus) AS total
										FROM tblschedule sched, tblparties party
										WHERE sched.surveyLocation = :location
										AND sched.scheduleUID = party.scheduleUID
										GROUP BY party.verifiedStatus");
			$qry->bindParam(":location",$location,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}
	}
?>