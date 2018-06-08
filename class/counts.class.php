<?php
	require_once('connect.class.php');
	class counts
	{
		// Get All Counts
		public function getAllCounts(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblcounts");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}

		// Get counts using projectUID
		public function getCountsByProjectUID($projectUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblcounts WHERE projectUID=:projectUID");
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}

		// Get counts using scheduleUID
		public function getCountsByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qryString = "SELECT * FROM tblcounts WHERE scheduleUID=:scheduleUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}
	}
?>