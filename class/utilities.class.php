<?php
	include_once("projectUser.class.php");

	class utilities{

		// Convert base64 from MySQL to JPG
		public function base64_to_jpeg($base64_string) {
		    $src = 'data:image/jpg;base64,'.$base64_string;
		    return $src;
		}

		// Convert base64 from MySQL to PNG
		public function base64_to_png($base64_string) {
		    $src = 'data:image/png;base64,'.$base64_string;
		    return $src;
		}

		// Get Trip label
		public function getDestination($destination){
			$destinationLabel = "";
			switch($destination){
				case "0":
					$destinationLabel = "No";
					break;
				case "1":
					$destinationLabel = "Yes";
					break;
				case "NA":
					$destinationLabel = "Refused";
					break;
				default:
					$destinationLabel = "Unknown";
					break;
			}			
			return $destinationLabel;
		}

		// Get Trip label
		public function getTrip($trip){
			$tripLabel = "";
			switch($trip){
				case "C":
					$tripLabel = "Completed";
					break;
				case "I":
					$tripLabel = "Incomplete";
					break;
				case "NA":
					$tripLabel = "Refused";
					break;
				default:
					$tripLabel = "Unknown";
					break;
			}			
			return $tripLabel;
		}

		// Get Race Label
		public function getRace($race){
			$raceLabel = "";
			switch($race){
				case "W":
					$raceLabel = "White";
					break;
				case "B":
					$raceLabel = "Black";
					break;
				case "NA":
					$raceLabel = "Refused";
					break;
				default:
					$raceLabel = "Other";
					break;
			}
			return $raceLabel;
		}


		// Get Fish Type 
		public function getFishingType($fType){
			$fishingType = "";
			switch($fType){
				case "1":
					$fishingType = "Boat";
					break;
				case "2":
					$fishingType = "Bank/Dock";
					break;
				case "3":
					$fishingType = "Heat Dock";
					break;
				case "4":
					$fishingType = "Canoe";
					break;
				case "5":
					$fishingType = "Wade/Belly";
					break;
				case "6":
					$fishingType = "Handicap";
					break;
				case "NA":
					$fishingType = "Refused";
					break;
				default:
					$fishingType = "No";
					break;
			}
			return $fishingType;
		}

		// Get Lure 
		public function getLure($lure){
			$lureLabel = "";
			switch($lure){
				case "1":
					$lureLabel = "Art";
					break;
				case "2":
					$lureLabel = "Fly";
					break;
				case "3":
					$lureLabel = "Nat";
					break;
				case "4":
					$lureLabel = "Prepared";
					break;
				case "5":
					$lureLabel = "Combo";
					break;
				case "NA":
					$lureLabel = "Refused";
					break;
				default:
					$lureLabel = "Unknown";
					break;
			}
			return $lureLabel;
		}

		// Get Method 
		public function getMethod($method){
			$methodLabel = "";
			switch($method){
				case "1":
					$methodLabel = "Still";
					break;
				case "2":
					$methodLabel = "Cast";
					break;
				case "3":
					$methodLabel = "Troll";
					break;
				case "4":
					$methodLabel = "Drift";
					break;
				case "5":
					$methodLabel = "Set/Radi";
					break;
				case "6":
					$methodLabel = "Gig";
					break;
				case "7":
					$methodLabel = "Trotline";
					break;
				case "8":
					$methodLabel = "Jug";
					break;
				case "9":
					$methodLabel = "Snag";
					break;
				case "0":
					$methodLabel = "Bow";
					break;
				case "NA":
					$methodLabel = "Refused";
					break;
				default:
					$methodLabel = "Unknown";
					break;
			}
			return $methodLabel;
		}


		/** Fishing Panel **/
		// Get Measured Label
		public function getMeasured($measured){
			$measuredLabel = "";
			switch($measured){
				case "Y":
					$measuredLabel = "Yes, By MDC";
					break;
				case "N":
					$measuredLabel = "Yes, but NOT by MDC";
					break;
				case "NZ":
					$measuredLabel = "No";
					break;
				case "NA":
					$measuredLabel = "Refused";
					break;
				default:
					$measuredLabel = "No";
					break;
			}
			return $measuredLabel;
		}

		// Get Random UID
		public function randomNumber(){
		    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		        // 32 bits for "time_low"
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		        // 16 bits for "time_mid"
		        mt_rand( 0, 0xffff ),

		        // 16 bits for "time_hi_and_version",
		        // four most significant bits holds version number 4
		        mt_rand( 0, 0x0fff ) | 0x4000,

		        // 16 bits, 8 bits for "clk_seq_hi_res",
		        // 8 bits for "clk_seq_low",
		        // two most significant bits holds zero and one for variant DCE1.1
		        mt_rand( 0, 0x3fff ) | 0x8000,

		        // 48 bits for "node"
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		    );
		}

		// Get Table data to download for CSV
		public function getTableData($projectUID, $tableName, $userUID){
			$dbcon= new connect();
			$queryString = "";
			if($tableName == "all"){
				$queryString = "SELECT t1.*, 
								IFNULL(t2.surveyStartDateTime,'') as surveyStartDateTime, IFNULL(t2.surveyStopDateTime,'') as surveyStopDateTime, IFNULL(t2.surveyLocation,'') as surveyLocation, IFNULL(t2.surveyStatus,'') as surveyStatus, 
								IFNULL(t3.clerk,'') as clerk, IFNULL(t3.secchi,'') as secchi, IFNULL(t3.waterTemperature,'') as waterTemperature, IFNULL(t3.waterLevel,'') as waterLevel, IFNULL(t3.clerkComments,'') as clerkComments, IFNULL(t3.headerStatus,'') as headerStatus,
								IFNULL(t4.partyNumber,'') as partyNumber, IFNULL(t4.partySize,'') as partySize, IFNULL(t4.partyComments,'') as partyComments, IFNULL(t4.partyStatus,'') as partyStatus,
								IFNULL(t5.anglerNumber,'') as anglerNumber, IFNULL(t5.timeStarted,'') as timeStarted, IFNULL(t5.timeContactedStopped,'') as timeContactedStopped, IFNULL(t5.fishingHours,'') as fishingHours, IFNULL(t5.zipCode,'') as zipCode, IFNULL(t5.destination,'') as destination, IFNULL(t5.satisfaction,'') as satisfaction, IFNULL(t5.trip,'') as trip, IFNULL(t5.race,'') as race, IFNULL(t5.fishType,'') as fishType, IFNULL(t5.lure,'') as lure, IFNULL(t5.method,'') as method, IFNULL(t5.anglerPreference,'') as anglerPreference, IFNULL(t5.loc,'') as loc, IFNULL(t5.boat,'') as boat, IFNULL(t5.optionalQ1,'') as optionalQ1, IFNULL(t5.optionalQ2,'') as optionalQ2, IFNULL(t5.optionalQ3,'') as optionalQ3, IFNULL(t5.optionalQ4,'') as optionalQ4, IFNULL(t5.optionalQ5,'') as optionalQ5, IFNULL(t5.optionalQ6,'') as optionalQ6, IFNULL(t5.anglerComments,'') as anglerComments, IFNULL(t5.anglerStatus,'') as anglerStatus,
								IFNULL(t6.speciesCode,'') as speciesCode, IFNULL(t6.category,'') as category, IFNULL(t6.numberCaught,'') as numberCaught, IFNULL(t6.length,'') as length, IFNULL(t6.measured,'') as measured, IFNULL(t6.photoTaken,'') as photoTaken, IFNULL(t6.fishStatus,'') as fishStatus
								from tblprojects t1 
								left join tblschedule t2 on t1.projectUID = t2.projectUID
								left join tblheader t3 on t2.scheduleUID = t3.scheduleUID
								left join tblparties t4 on t3.headerUID = t4.headerUID
								left join tblanglers t5 on t4.partiesUID = t5.partiesUID
								left join tblfishcaught t6 on t5.anglerUID = t6.anglerUID
								where t1.projectUID = :projectUID
								order by t1.projectName";
				
			} else {
				$queryString = "SELECT * FROM ".$tableName." WHERE projectUID = :projectUID";
				/*$whereClause = "";
			    $projectUser = new projectUser();
			    $projectList = $projectUser->getProjectListByUser($userUID);*/

			    /*if(count($projectList) == 0){
			    	echo json_encode(0);
			    	return;
			    }

			    if(strcasecmp($tableName,"tblspecies") != 0){
			    	$whereClause = $whereClause." WHERE projectUID IN (";
			    	for($i=0; $i<count($projectList)-1;$i++){
			    		$projectUID = $projectList[$i]["projectUID"];
			    		$whereClause = $whereClause."'".$projectUID."', ";
			    	}
			    	$whereClause = $whereClause."'".$projectList[count($projectList)-1]["projectUID"]."')";
			   	}	

			   	$queryString = $queryString.$whereClause;*/
			   	// $qry=$dbcon->db1->prepare($queryString);
			}

		   	// EXECUTE QUERY
		   	$qry=$dbcon->db1->prepare($queryString);
			$qry->bindParam(":projectUID",$projectUID,PDO::PARAM_STR);
		    $qry->execute();
		    $res = $qry->fetchAll(PDO::FETCH_ASSOC); // Fetches record
		    echo json_encode($res);
		    return;
		}
	}
?>