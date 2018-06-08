<?php
	require_once('connect.class.php');
	class projectspecies
	{
		// Get All Project Species
		public function getAllProjectSpecies(){
			$dbcon= new connect();
			$qryString = "SELECT * FROM tblprojectspecies";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get All Project Species
		public function getAllProjectSpeciesByProjectUID($projectUID){
			$dbcon= new connect();
			$qryString = "SELECT * FROM tblprojectspecies where projectUID=:projectUID";
			$qry=$dbcon->db1->prepare($qryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		// Get Project Species by species code
		// ONLY GETS SPECIES CODE AND NAME
		public function getSpeciesNameBySpeciesCode($speciesCode){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT speciesCode, speciesNameFirst, speciesNameLast 
										FROM tblprojectspecies 
										WHERE speciesCode=:speciesCode
										GROUP BY speciesCode");
			$qry->bindParam(":speciesCode",$speciesCode,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			return $res;
		}
	}
?>