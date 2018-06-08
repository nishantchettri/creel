<?php
	include_once("../class/users.class.php");
	session_start();
	extract($_POST);
	extract($_GET);
	$error = 0;

	// Returned from dashboard
	if(isset($_GET["err"])){
		$error = 1;
	}

	// Login form values submitted from JQUERY
	if(isset($_POST["username"]) && isset($_POST["password"])){
		$username = $_POST["username"];
		$password = $_POST["password"];
		
		$user = new users();
		$checkUser = $user->validateUser($username, $password);

		if($checkUser == null){
			$error = 1;
		} else {
			$_SESSION['username'] = $username;
			$_SESSION["userUID"] = $checkUser["userUID"];
			$error = 0;
		}
		echo json_encode($checkUser);
		return;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>

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
			#invalid{
				background-color:#d25656; 
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
					<h4 style="margin:5px; font-size:30px; font-family:calibri" class="text-center bold">MDC CREEL SURVEY</h4>
				</div>
				<div id="invalid" class="row hidden">
					<h5>Username/Password not found</h5>
				</div>
				<div class="panel-body" style="margin-bottom:0">
					<div class="row col-md-12">
						<form id="login">
					    	<div class="row">
					    		<span id="username-error" class="hidden">*Invalid Email Address</span>
					        	<div class="form-group username">
					          		<input id="username" type="text" class="form-control" placeholder="User ID / Email Address" autofocus>
					        	</div>
					        </div>
					        <div class="row">
					        	<span id="password-error" class="hidden">*Invalid Password Entry</span>
					        	<div class="form-group password">
					          		<input id="password" type="password" class="form-control" placeholder="Password">
					        	</div>
					      	</div>
					      	<div class="row">
						      	<div class="form-group">
							      	<input type="submit" class="btn btn-success btn-block" value="Log In"/>
						      	</div>
					      	</div>
						</form>
					</div>
				</div> <!-- END PANEL BODY -->
				<div class="panel-footer">
					<div class="row text-right">
				      	<a href="register.php">Sign Up</a>
			      	</div>
				</div>
			</div> <!-- END PANEL -->
		</div> <!-- END CONTAINER-FLUID -->

		<!-- EXTERNAL JAVASCRIPT -->
		<?php include_once('../includes/js.php'); ?>

		<!-- INTERNAL JAVASCRIPT -->
		<script type="text/javascript">
			$("#login").submit(function(e){
				// Prevent submit button from submitting form via PHP
				e.preventDefault();

				// Get form values
				var username = $("#username").val();
				var password = $("#password").val();
				var error = 0;
				
				// Check for incomplete username field
				if($.trim(username) == "" || username == null){
					$(".username").addClass("has-error");
					$("#username-error").removeClass("hidden");
					error++;
				} else{
					$(".username").removeClass("has-error");
					$("#username-error").addClass("hidden");
				}

				// Check for incomplete password field
				if($.trim(password) == "" || password == null){
					$(".password").addClass("has-error");
					$("#password-error").removeClass("hidden");
					error++;
				} else{
					$(".password").removeClass("has-error");
					$("#password-error").addClass("hidden");
				}

				// If no error in form
				if(error == 0){
					data = {username:username, password:password};
					$.ajax({
						data:data,
						method:"POST",
						dataType:"json",
						success:function(result){
							// console.log(JSON.stringify(result));
							if(result == null || result == false){ // unauthorized or not found
								$("#invalid").removeClass("hidden");
							} else{
								$("#invalid").addClass("hidden");
								window.location = "dashboard.php";
							}
						},
						error:function(xhr,status,error){
							console.log("XHR: " + JSON.stringify(xhr));
							console.log("Status: " + JSON.stringify(status));
							console.log("Error: " + JSON.stringify(error));
						}
					});
				}
			});
		</script>
	</body>
</html>