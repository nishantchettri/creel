<?php
	require_once('connect.class.php');
	class parties
	{
		// Get All Projects
		public function getAllParties(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblparties");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get project details using projectUID
		public function getPartyByPartiesUID($partiesUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblparties WHERE partiesUID=:partiesUID");
			$qry->bindParam(":partiesUID",$partiesUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Get project details using projectUID
		public function getPartiesByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblparties WHERE scheduleUID=:scheduleUID");
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get verification status of all parties using scheduleUID
		public function getVerifiedPartiesByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT verifiedStatus, count(verifiedStatus) as total
										from tblparties
										where scheduleUID = :scheduleUID
										group by verifiedStatus");
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get parties between dates
		public function getVerifiedPartiesByDate($surveyStartDateTime, $surveyStopDateTime){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT *
										from tblparties parties, tblschedule sched
										where sched.surveyStartDateTime > :surveyStartDateTime
										and sched.surveyStopDateTime < :surveyStopDateTime
										and parties.scheduleUID = sched.scheduleUID");
			$qry->bindParam(":surveyStartDateTime",$surveyStartDateTime,PDO::PARAM_STR);
			$qry->bindParam(":surveyStopDateTime",$surveyStopDateTime,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}
		

		// Update Parties after verification
		public function updateParties($partiesUID, $clerk, $comments, $verifiedDate){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("UPDATE tblparties SET verifiedStatus='1', verifiedBy=:clerk, verifiedComments=:comments, verifiedDateTime=:verifiedDate WHERE partiesUID=:partiesUID");
			$qry->bindParam(":partiesUID",$partiesUID,PDO::PARAM_STR);
			$qry->bindParam(":clerk",$clerk,PDO::PARAM_STR);
			$qry->bindParam(":comments",$comments,PDO::PARAM_STR);
			$qry->bindParam(":verifiedDate",$verifiedDate,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->rowCount();
			return $res; // 0 if fail, >0 if success
		}
	}
?>