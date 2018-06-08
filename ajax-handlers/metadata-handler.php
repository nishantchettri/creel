<?php
	/**
	* Used to view Raw Database tables
	**/
	extract($_POST);
	extract($_GET);
	
	// DBInfo
	include_once("../class/connect.class.php");
	
	// DB Connection
	try{		
		if(isset($_POST['tableName'])){
			$tableName = $_POST['tableName'];
			// Create Database Connection and get records from table
			$dbcon= new connect();
			$query=$dbcon->db1->prepare("SELECT * FROM ".$tableName);
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
		} 
	} catch(PDOException $e){
		echo "Error: ". $e->getMessage();
	}

?>