<?php
require_once('connect.class.php');

class users{
		// Validate username and password
		// Return array -- [user_allowed, user_id]
	public function validateUser($username, $password){
		$dbcon= new connect();
		$qry=$dbcon->db1->prepare("SELECT * FROM tblusers WHERE email=:username and authorized = 1");
		$qry->bindParam(":username",$username,PDO::PARAM_STR);
		$qry->execute();
		$res = $qry->fetch(); // Fetches record
		if($res > 0){
			$match = 0;
			$convertedPassword = "";
			if($res["salt"] != ""){
				$db_pass = $res['pass'];
				$salt = $res['salt'];
				$saltedPassword = $password . $salt;
				$convertedPassword = hash("sha256", $saltedPassword);
				if ($convertedPassword == $db_pass){
					$match = 1;
				}
			} 

			if($match == 0){
				$res = false;
			}
		}
		return $res;
	}

	// Get all users
	public function getUserList(){
		$dbcon = new connect();
		$qry=$dbcon->db1->prepare("SELECT users.*, roles.roleName, roles.permissionLevel 
			FROM tblusers users, tblroles roles 
			WHERE users.roleID = roles.roleID");
		$qry->execute();
		$res = $qry->fetchAll(); // Fetches record
		return $res;
	}

	// Get entire row using username
	public function getUserByUID($userUID){
		$dbcon = new connect();
		$qry=$dbcon->db1->prepare("SELECT users.*, roles.roleName, roles.permissionLevel 
			FROM tblusers users, tblroles roles 
			WHERE users.roleID = roles.roleID
			AND users.userUID=:userUID");
		$qry->bindParam(":userUID",$userUID,PDO::PARAM_STR);
		$qry->execute();
		$res = $qry->fetch(); // Fetches record
		return $res;
	}

	// Get entire row using username
	public function getUserInfo($username){
		$dbcon = new connect();
		$qry=$dbcon->db1->prepare("SELECT users.*, roles.roleName, roles.permissionLevel 
			FROM tblusers users, tblroles roles 
			WHERE users.roleID = roles.roleID
			AND users.email=:username");
		$qry->bindParam(":username",$username,PDO::PARAM_STR);
		$qry->execute();
		$res = $qry->fetch(); // Fetches record
		return $res;
	}

	// Insert new user
	public function insertNewUser($db, $userUID, $fname, $lname, $email, $password, $salt, $authorized, $roleID){
		// $dbcon = new connect();
		if($db == null){ // register.php
			$db = new connect();
		}
		$qry=$db->db1->prepare("INSERT INTO tblusers VALUES (:userUID, :fname, :lname, :email, :password, :salt, :authorized, :roleID)");
		$qry->bindParam(":userUID",strtoupper($userUID),PDO::PARAM_STR);
		$qry->bindParam(":fname",$fname,PDO::PARAM_STR);
		$qry->bindParam(":lname",$lname,PDO::PARAM_STR);
		$qry->bindParam(":email",$email,PDO::PARAM_STR);
		$qry->bindParam(":password",$password,PDO::PARAM_STR);
		$qry->bindParam(":salt",$salt,PDO::PARAM_STR);
		$qry->bindParam(":authorized",$authorized,PDO::PARAM_STR);
		$qry->bindParam(":roleID",$roleID,PDO::PARAM_STR);
		$res = $qry->execute();
		return $res;
	}

	// Update user password
	public function updatePassword($email, $password, $salt){
		$dbcon = new connect();
		$qry=$dbcon->db1->prepare("UPDATE tblusers SET pass=:password, salt=:salt WHERE email=:email");
		$qry->bindParam(":email",$email,PDO::PARAM_STR);
		$qry->bindParam(":password",$password,PDO::PARAM_STR);
		$qry->bindParam(":salt",$salt,PDO::PARAM_STR);
		$res = $qry->execute();
		return $res;
	}

	// Update user roles
	public function editUser($userUID, $fname, $lname, $email, $authorized, $roleID){ 
		$dbcon = new connect();
		$qry=$dbcon->db1->prepare("UPDATE tblusers SET fname=:fname, lname=:lname, email=:email, authorized=:authorized,roleID=:roleID WHERE userUID=:userUID"); 
		$qry->bindParam(":userUID",$userUID,PDO::PARAM_STR);
		$qry->bindParam(":fname",$fname,PDO::PARAM_STR);
		$qry->bindParam(":lname",$lname,PDO::PARAM_STR);
		$qry->bindParam(":email",$email,PDO::PARAM_STR);
		$qry->bindParam(":roleID",$roleID,PDO::PARAM_STR);
		$qry->bindParam(":authorized",$authorized,PDO::PARAM_INT);
		$res = $qry->execute();
		return $res;
	}
}
?>