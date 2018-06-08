<?php
	include_once('../class/fishcaught.class.php');
		
	// Get Fish Details and Image (if exists) 
	// from tblFishCaught and tblFishCaughtImages table
	if(isset($_POST["action"])){
		$action = $_POST["action"];
		$tblFishCaught = new fishCaught();

		// Get Fish Details
		if(strcasecmp($action, "getFish")==0){
			if(isset($_POST["fishCaughtUID"])){
				$fishCaughtUID = $_POST["fishCaughtUID"];
				$fishCaught = $tblFishCaught->getFishByFishCaughtUID($fishCaughtUID);
				$fishCaughtImage = "";
				if($fishCaught["photoTaken"] == "1"){
					$fishCaughtImage = $tblFishCaught->getFishImageByFishCaughtUID($fishCaughtUID);
				}

				$arr = [];
				$arr["fishCaught"] = $fishCaught;
				$arr["fishCaughtImageDetails"] = $fishCaughtImage;
				echo json_encode($arr);
				return;
			}
		} else if(strcasecmp($action, "updateFish")==0){
			if(isset($_POST["fishCaughtUID"]) && isset($_POST["speciesCode"]) && isset($_POST["category"]) && isset($_POST["numCaught"]) && isset($_POST["length"]) && isset($_POST["measured"])){
				$fishCaughtUID = $_POST["fishCaughtUID"];
				$speciesCode = $_POST["speciesCode"];
				$category = $_POST["category"];
				$numCaught = $_POST["numCaught"];
				$length = $_POST["length"];
				$measured = $_POST["measured"];

				$updateFish = $tblFishCaught->updateFishCaught($fishCaughtUID, $speciesCode, $category, $numCaught, $length, $measured);
				echo $updateFish;
				return;
			}
		}
	}
?>