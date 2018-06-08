<?php
	require_once('connect.class.php');
	class roles
	{
		// Get entire records
		public function getAllRoles(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblroles");
			$qry->execute();
			$res = $qry->fetchAll(); // Fetches record
			return $res;
		}

		// Get permission level by role ID
		public function getRoleByID($roleID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblroles WHERE roleID=:roleID");
			$qry->bindParam(":roleID",$roleID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch(); // Fetches record
			return $res;
		}

		// Get permission level by role name
		public function getRoleByName($roleName){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblroles WHERE roleName=:roleName");
			$qry->bindParam(":roleName",$roleName,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch(); // Fetches record
			return $res;
		}
	}

?>