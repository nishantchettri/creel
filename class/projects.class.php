<?php
	require_once('connect.class.php');

	class projects
	{
		// Get All Projects
		public function getProjectList(){
			$dbcon= new connect();
			$qryString = "SELECT * FROM tblprojects";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get All Projects
		public function getProjectsForTreeview(){
			$dbcon= new connect();
			$qryString = "SELECT proj.projectUID as projectUID, 
							proj.projectName as projectName, 
							proj.projectStartDate as projectStartDate, 
							proj.projectStopDate as projectStopDate, 
							count(sched.projectUID) as countProjects
							FROM tblprojects proj
							JOIN tblschedule sched
							ON proj.projectUID = sched.projectUID
							GROUP BY proj.projectUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get project details using projectUID
		public function getProjectByProjectUID($projectUID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM tblprojects WHERE projectUID=:projectUID");
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}

		// Add new project
		public function insertNewProject($projectUID, $projectName, $projectType, $projectLocation, $projectDescription, $projectStartDate, $projectStopDate, $optionalQ1, $optionalQ2, $optionalQ3, $optionalQ4, $optionalQ5, $optionalQ6){
			$dbcon = new connect();
			$qryString = "INSERT INTO tblprojects 
							VALUES (:projectUID, :projectName, :projectType, :projectLocation, :projectDescription, :projectStartDate, :projectStopDate, :optionalQ1, :optionalQ2, :optionalQ3, :optionalQ4, :optionalQ5, :optionalQ6)";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->bindParam(":projectName",$projectName,PDO::PARAM_STR);
			$qry->bindParam(":projectLocation",$projectLocation,PDO::PARAM_STR);
			$qry->bindParam(":projectDescription",$projectDescription,PDO::PARAM_STR);
			$qry->bindParam(":projectType",$projectType,PDO::PARAM_STR);
			$qry->bindParam(":projectLocation",$projectLocation,PDO::PARAM_STR);
			$qry->bindParam(":projectStartDate",$projectStartDate,PDO::PARAM_STR);
			$qry->bindParam(":projectStopDate",$projectStopDate,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ1",$optionalQ1,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ2",$optionalQ2,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ3",$optionalQ3,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ4",$optionalQ4,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ5",$optionalQ5,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ6",$optionalQ6,PDO::PARAM_STR);
			$res = $qry->execute();
			return $res;
		}

		// Edit project
		public function updateProject($projectUID, $projectName, $projectType, $projectLocation, $projectDescription, $projectStartDate, $projectStopDate, $optionalQ1, $optionalQ2, $optionalQ3, $optionalQ4, $optionalQ5, $optionalQ6){
			$dbcon = new connect();
			$qryString = "UPDATE tblprojects SET projectName=:projectName, projectType=:projectType, projectLocation= :projectLocation, projectDescription=:projectDescription, projectStartDate=:projectStartDate, projectStopDate=:projectStopDate, optionalQ1=:optionalQ1, optionalQ2=:optionalQ2, optionalQ3=:optionalQ3, optionalQ4=:optionalQ4, optionalQ5=:optionalQ5, optionalQ6=:optionalQ6 WHERE projectUID = :projectUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->bindParam(":projectName",$projectName,PDO::PARAM_STR);
			$qry->bindParam(":projectLocation",$projectLocation,PDO::PARAM_STR);
			$qry->bindParam(":projectDescription",$projectDescription,PDO::PARAM_STR);
			$qry->bindParam(":projectType",$projectType,PDO::PARAM_STR);
			$qry->bindParam(":projectLocation",$projectLocation,PDO::PARAM_STR);
			$qry->bindParam(":projectStartDate",$projectStartDate,PDO::PARAM_STR);
			$qry->bindParam(":projectStopDate",$projectStopDate,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ1",$optionalQ1,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ2",$optionalQ2,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ3",$optionalQ3,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ4",$optionalQ4,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ5",$optionalQ5,PDO::PARAM_STR);
			$qry->bindParam(":optionalQ6",$optionalQ6,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->rowCount();
			return $res;

		}
	}
?>