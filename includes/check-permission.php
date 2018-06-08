<?php
	if(!isset($_SESSION["username"]) || !isset($_SESSION["permission_level"]) || $_SESSION['permission_level'] > 2) {
        header("Location:index.php");
        exit();
    }
?>