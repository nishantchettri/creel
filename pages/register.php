<?php
	include_once("../class/users.class.php");
	include_once("../class/utilities.class.php");

	// Get Project UID
	$utilities = new utilities();
	$userUID = $utilities->randomNumber();

	extract($_POST);
	extract($_GET);

	if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]) && isset($_POST["password"])){
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		$email = $_POST["email"];
		$password = $_POST["password"];

		// Hash-Salt password
		$salt = hash("sha256", strval(mt_rand()));
		$saltedPassword = $password . $salt;
		$hashedSaltedPassword = hash("sha256", $saltedPassword);

		$users = new users();
		$insertUser = $users->insertNewUser(null, $userUID, $fname, $lname, $email, $hashedSaltedPassword, $salt, 0, 3); 
		// authorize = 0; role = 2 = guest
		echo $insertUser;
		return;
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MDC Creel Survey -- Sign Up</title>

		<!-- EXTERNAL CSS-->
		<?php include_once("../includes/css.php") ?>

		<style>
			body{
				background-image:url('../resources/images/background/fishing_1.jpg');
				background-size:1300px 800px;
				background-repeat:no-repeat;
				background-position:top;
			}
			.img-center{
				text-align:center;
			}
			
			#username-error, #password-error{
				font-size:12px;
				text-align:left !important;
				color:red;
			}
			.invalid{
				background-color:#d25656; 
				color:white; 
				text-align:center; 
				margin-top:0; 
				margin-bottom:15px;
			}
			.valid{
				background-color:#5e68f3; 
				color:white; 
				text-align:center; 
				margin-top:0; 
				margin-bottom:15px;
			}
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<div class="container-fluid div-center">
			<div class="panel panel-default" style="border:0">
				<div class="panel-heading" style="text-align:center;">
					<h4 style="margin:5px; font-size:30px; font-family:calibri" class="text-center bold">Sign Up</h4>
				</div>
				<div id="successful" class="row valid hidden">
					<h5>Successfully registered! Wait for confirmation</h5>
				</div>
				<div id="unsuccessful" class="row invalid hidden">
					<h5>Unable to register account! Contact the administrator</h5>
				</div>
				<div id="invalid" class="row invalid hidden">
					<h5>Please fill out all the fields!</h5>
				</div>
				<div id="password_error" class="row invalid hidden">
					<h5>Passwords do not match</h5>
				</div>
				<div class="panel-body" style="margin-bottom:0">
					<div class="row col-md-12">
						<form id="register">
					    	<div class="row">
					        	<div class="form-group fname">
					          		<input id="fname" type="text" class="form-control" placeholder="First Name" autofocus required>
					        	</div>
					        </div>
					        <div class="row">
					        	<div class="form-group lname">
					          		<input id="lname" type="text" class="form-control" placeholder="Last Name" required>
					        	</div>
					        </div>
					    	<div class="row">
					        	<div class="form-group email">
					          		<input id="email" type="text" class="form-control" placeholder="Email Address" required>
					        	</div>
					        </div>
					        <div class="row">
					        	<div class="form-group password1">
					          		<input id="password1" type="password" class="form-control" placeholder="Password" required>
					        	</div>
					      	</div>
					      	<div class="row">
					        	<div class="form-group password2">
					          		<input id="password2" type="password" class="form-control" placeholder="Re-enter Password" required>
					        	</div>
					      	</div>
					      	<div class="row">
						      	<div class="form-group">
							      	<input type="submit" class="btn btn-success btn-block" value="Sign Up" />
						      	</div>
					      	</div>
						</form>
					</div>
				</div> <!-- END PANEL BODY -->
				<div class="panel-footer">
					<div class="row text-right">
				      	<a href="index.php">Back to Login</a>
			      	</div>
				</div>
			</div> <!-- END PANEL -->
		</div> <!-- END CONTAINER-FLUID -->

		<!-- EXTERNAL JAVASCRIPT -->
		<?php include_once('../includes/js.php'); ?>

		<!-- INTERNAL JAVASCRIPT -->
		<script type="text/javascript">
			$("#register").submit(function(e){
				e.preventDefault();

				var fname = $("#fname").val().trim();
				var lname = $("#lname").val().trim();
				var email = $("#email").val().trim();
				var passwd1 = $("#password1").val();
				var passwd2 = $("#password2").val();

				// Validate empty fields
				if(fname == "" || lname == "" || email == "" || passwd1 == "" || passwd2 == ""){
					$("#invalid").removeClass("hidden");
				} else {
					$("#invalid").addClass("hidden");

					// Validate passwords
					if(passwd1 != passwd2){
						$("#password_error").removeClass("hidden");
					} else{
						$("#password_error").addClass("hidden");
					}
				}

				// Insert into database
				var data = {
					fname: fname,
					lname: lname,
					email: email,
					password: passwd1
				}
				// Sends to top of page
				$.ajax({
					data:data,
					method:"POST",
					dataType:"text",
					success:function(result){
						// console.log("Number of records inserted: " + result);
						if(result == 1){
							$("#successful").removeClass("hidden");
							$("#unsuccessful").addClass("hidden");

							$("#fname").val('');
							$("#lname").val('');
							$("#email").val('');
							$("#password1").val('');
							$("#password2").val('');
						} else {
							$("#unsuccessful").removeClass("hidden");
							$("#successful").addClass("hidden");
						}
					},
					error:function(xhr,status,error){
						console.log("XHR: " + JSON.stringify(xhr));
						console.log("Status: " + JSON.stringify(status));
						console.log("Error: " + JSON.stringify(error));
					}
				});
			});	
		</script>
	</body>
</html>