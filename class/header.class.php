<?php
	require_once('connect.class.php');
	class header
	{
		// Get All Projects
		public function getAllHeaders(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblheader");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get project details using projectUID
		public function getHeaderByHeaderUID($headerUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblheader WHERE headerUID=:headerUID");
			$qry->bindParam(":headerUID",$headerUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Get project details using projectUID
		public function getHeaderByScheduleUID($scheduleUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblheader WHERE scheduleUID=:scheduleUID");
			$qry->bindParam(":scheduleUID",$scheduleUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}
	}
?>