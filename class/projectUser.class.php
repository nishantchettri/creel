<?php
	require_once('connect.class.php');

	class projectUser
	{
		// Get All Projects for user
		public function getProjectListByUser($userUID){
			$dbcon= new connect();
			$qryString = "SELECT * FROM tblproject_user where userUID=:userUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":userUID",$userUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}

		// Get All users in a project
		public function getUserListByProject($projectUID){
			$dbcon= new connect();
			$qryString = "SELECT * FROM tblproject_user where projectUID=:projectUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// INSERT new project-user relationship
		public function insertIntoProjectUser($db, $projectUID, $userUID){ 
			// $dbcon= new connect();
			$qryString = "INSERT INTO tblproject_user VALUES ";
			
			// CREATE QUERY STRING
			// Loop activates if projectUID has more than 1 item
			// If 1 item then string concat after the loop works
			for($i=0; $i<count($projectUID)-1; $i++){
				$qryString = $qryString." (:projectUID".$i.", :userUID), ";
			}
			$qryString = $qryString." (:projectUID".(count($projectUID)-1).", :userUID)";
			
			$qry=$db->db1->prepare($qryString);

			// BIND PROJECT UID
			for($i=0; $i<count($projectUID); $i++){
				$qry->bindParam(":projectUID".$i, $projectUID[$i], PDO::PARAM_STR);
			}
			// BIND USER UID
			$qry->bindParam(":userUID",strtoupper($userUID),PDO::PARAM_STR);
			$res = $qry->execute();
			return $res;
		}

		// UPDATE project-user relationship
		public function updateProjectUserRelationship($dbcon, $projectUID, $userUID){
			$res = false;
		
			// First delete the existing relationships
			$qryString = "DELETE FROM tblproject_user where userUID=:userUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":userUID",$userUID,PDO::PARAM_STR);
			$res = $qry->execute();
				
			// If deleted == true
			if($res){
				// Insert new records in loop
				$qryString = "INSERT INTO tblproject_user VALUES (:projectUID, :userUID)";
				for($i=0; $i<count($projectUID); $i++){
					$qry=$dbcon->db1->prepare($qryString);
					$qry->bindParam(":projectUID",$projectUID[$i],PDO::PARAM_STR);
					$qry->bindParam(":userUID",$userUID,PDO::PARAM_STR);
					$res = $qry->execute();
				}
			}

			return $res;
		}

		// Delete all project-user relationship by UserUID
		public function deleteProjectUserByUserUID($userUID){
			$dbcon= new connect();
			$qryString = "DELETE FROM tblproject_user WHERE userUID = :userUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":userUID",$userUID,PDO::PARAM_STR);
			$res = $qry->execute();
			return $res;
		}

		// Delete all project-user relationship by projectUID
		public function deleteProjectUserByProjectUID($projectUID){
			$dbcon= new connect();
			$qryString = "DELETE FROM tblproject_user WHERE projectUID = :projectUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$res = $qry->execute();
			return $res;
		}
	}
?>