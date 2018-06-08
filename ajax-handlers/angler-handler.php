<?php
	include_once('../class/anglers.class.php');
		
	// Update Verification Status for Angler
	if(isset($_POST["anglerUID"]) && isset($_POST["comments"]) && isset($_POST["verifiedDate"])){
		$anglerUID = $_POST["anglerUID"];
		$clerk = $_POST["clerk"];
		$comments = $_POST["comments"];
		$verifiedDate = $_POST["verifiedDate"];

		$angler = new anglers();
		$updateAngler = $angler->updateAngler($anglerUID, $clerk, $comments, $verifiedDate);
		echo $updateAngler;
	}
?>