<?php
	require_once('connect.class.php');
	class fishcaught
	{
		// Get all fish caught
		public function getAllFishCaught(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblfishcaught");
			$qry->execute();
			$res = $qry->fetchAll(); // Fetches record
			return $res;
		}

		// Get entire row using fish caught UID
		public function getFishByFishCaughtUID($fishCaughtUID){
			$dbcon = new connect();
			$qryString = "SELECT * FROM tblfishcaught WHERE fishCaughtUID=:fishCaughtUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":fishCaughtUID",$fishCaughtUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Get fish record by angler UID
		public function getFishByAnglerUID($anglerUID){
			$dbcon = new connect();
			$qryString = "SELECT * from tblfishcaught WHERE anglerUID = :anglerUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":anglerUID",$anglerUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get fish image by fish caught UID
		public function getFishImageByFishCaughtUID($fishCaughtUID){
			$dbcon = new connect();
			$qryString = "SELECT * from tblfishcaughtimages WHERE fishCaughtUID=:fishCaughtUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":fishCaughtUID",$fishCaughtUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Update fish caught details
		public function updateFishCaught($fishCaughtUID, $speciesCode, $category, $numCaught, $length, $measured){
			$dbcon = new connect();
			$qryString = "UPDATE tblfishcaught SET speciesCode=:speciesCode, category=:category, numberCaught=:numCaught, length=:length, measured=:measured WHERE fishCaughtUID=:fishCaughtUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":fishCaughtUID",$fishCaughtUID,PDO::PARAM_STR);
			$qry->bindParam(":speciesCode",$speciesCode,PDO::PARAM_STR);
			$qry->bindParam(":category",$category,PDO::PARAM_STR);
			$qry->bindParam(":numCaught",$numCaught,PDO::PARAM_STR);
			$qry->bindParam(":length",$length,PDO::PARAM_STR);
			$qry->bindParam(":measured",$measured,PDO::PARAM_STR);
			$res = $qry->execute();
			return $res;
		}
	}
?>