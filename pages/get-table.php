<?php
	header("Access-Control-Allow-Origin: *");
		
	extract($_POST);
	extract($_GET);
	
	// DBInfo
	require_once("db-info.php");
	
	// DB Connection
	try{
		$db = new PDO("mysql:host=".$hostname.";dbname=".$dbname.";",$username,$password);
		
		// set the PDO error mode to exception
		// throws exception
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		if(isset($_POST['tableName'])){
			$tableName = $_POST['tableName'];
			$query = $db->prepare("select * from ".$tableName);
			$query->execute();
			$rows = $query->rowCount(); // check if query returned any rows
			if($rows > 0){
				// Gets row values
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				$arr = array();
				for($i=0; $i<$rows; $i++){
					$row = $result[$i];
					$obj = array();
					$j = 0;
					foreach($row as $key=>$val){
						$obj[$key] = $val;
						$j++;
					}
					array_push($arr,$obj);	
				}
				echo json_encode($arr);
				exit;
			} else {
				$arr = array();
				echo json_encode($arr);
				exit;
			}
		} else {
			// create the SELECT query
			$query = $db->prepare("select table_name from information_schema.tables WHERE table_schema = '".$dbname."'");
			$query->execute();
			$rows = $query->rowCount(); // check if query returned any rows
			if($rows > 0){
				// Gets row values
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				header("Content-Type: application/json");
				echo json_encode($result);
				exit;
			}
		}
	} catch(PDOException $e){
		echo "Error: ". $e->getMessage();
	}

?>