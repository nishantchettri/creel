<?php
	include_once('../class/parties.class.php');
		
	// Update Verification Status for Angler
	if(isset($_POST["partiesUID"]) && isset($_POST["comments"]) && isset($_POST["verifiedDate"])){
		$partiesUID = $_POST["partiesUID"];
		$clerk = $_POST["clerk"];
		$comments = $_POST["comments"];
		$verifiedDate = $_POST["verifiedDate"];

		$parties = new parties();
		$updateParties = $parties->updateParties($partiesUID, $clerk, $comments, $verifiedDate);
		echo $updateParties;
	}
?>