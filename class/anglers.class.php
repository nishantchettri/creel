<?php
	require_once('connect.class.php');
	class anglers
	{
		// Validate username and password
		// Return array -- [user_allowed, user_id]
		public function getAllAnglers(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblanglers");
			$qry->execute();
			$res = $qry->fetchAll(); // Fetches record
			return $res;
		}

		// Get entire row using username
		public function getAnglerByAnglerUID($anglerUID){
			$dbcon = new connect();
			$qryString = "SELECT * FROM tblanglers WHERE anglerUID = :anglerUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":anglerUID",$anglerUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Get initial information about angler using party UID
		public function getAnglersPrimaryInfoByPartiesUID($partiesUID){
			$dbcon = new connect();
			$qryString = "SELECT anglerUID, anglerNumber, timeStarted, timeContactedStopped, 
									zipCode, anglerPreference, verifiedStatus, anglerComments, anglerStatus 
						from tblanglers ang
						WHERE partiesUID = :partiesUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":partiesUID",$partiesUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get anglers by Parties UID
		public function getAnglersByPartiesUID($partiesUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblanglers WHERE partiesUID=:partiesUID");
			$qry->bindParam(":partiesUID",$partiesUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}


		// Update Angler after verification
		public function updateAngler($anglerUID, $clerk, $comments, $verifiedDate){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("UPDATE tblanglers SET verifiedStatus='1', verifiedBy=:clerk, verifiedComments=:comments, verifiedDateTime=:verifiedDate WHERE anglerUID=:anglerUID");
			$qry->bindParam(":anglerUID",$anglerUID,PDO::PARAM_STR);
			$qry->bindParam(":clerk",$clerk,PDO::PARAM_STR);
			$qry->bindParam(":comments",$comments,PDO::PARAM_STR);
			$qry->bindParam(":verifiedDate",$verifiedDate,PDO::PARAM_STR);
			$res = $qry->execute();
			return $res; // 0 if fail, >0 if success
		}
	}

?>