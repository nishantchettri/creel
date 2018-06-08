<?php
	include_once("../class/users.class.php");

	extract($_POST);
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MDC Creel -- Verify</title>

		<!-- EXTERNAL CSS-->
		<?php include_once("../includes/css.php") ?>
		<link rel="stylesheet" href="../css/tree-menu.css" />
		<!-- <link rel="stylesheet" href="lib/jquery.ntm/themes/default/css/theme.css" /> -->


		<style>
			body{
				background:transparent;
				background-color:#afe2af;
				padding-top:70px;
			}
			.min-width-margin{
				margin-left:5px;
				margin-right:5px;
			}			
			.bold{
				font-weight:bold;
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
			.label-background{
				background-color:#f5f5f5;
				border-color:#ddd;
			}
			.demo{
				height:400px;
				overflow-y:auto;
			}
			.sidebar{
				background-color:white; 
				border-radius:3px;
				padding:0;
			}
		</style>
	</head>
	<!-- STYLE -->
	<body>
		<div class="row" style="margin-bottom:50px">
			<div class="navbar navbar-default navbar-fixed-top">
				<div class="container-fluid">
					<div class="col-xs-4">
						<a class="navbar-brand" href="dashboard.php" >
					    	<img alt="MDC" src="../images/full-logo.png" height="90px" width="auto">
					   	</a>
					</div>
				</div>
			</div>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<!-- <div class="row">
			<div class="navbar navbar-default" style="margin-top:10px;padding:0;border-radius:0">
				<div class="container-fluid no-pad" style="padding-left:150px;">
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				    	<ul class="nav navbar-nav">
				        	<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
				        	<li><a href="#">Link</a></li>
				        	<li class="dropdown">
				          		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
				          		<ul class="dropdown-menu">
				            		<li><a href="#">Action</a></li>
				            		<li><a href="#">Another action</a></li>
				            		<li><a href="#">Something else here</a></li>
				            		<li role="separator" class="divider"></li>
				            		<li><a href="#">Separated link</a></li>
				            		<li role="separator" class="divider"></li>
				            		<li><a href="#">One more separated link</a></li>
				          		</ul>
				        	</li>
				      	</ul>
				    </div><!-- /.navbar-collapse -->
				<!--</div>
			</div>
		</div> -->
		<div class="container-fluid">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3>Verify</h3>
				</div>
				<div class="panel-body">
	                <form role="form" action="save_artifact.php" method="POST">
	                    <input class="form-control" name="id" type="hidden" value="">
	                    <fieldset>
	                        <div class="form-group">
	                            <input class="form-control" name="title" type="title" value="" autofocus>
	                        </div>
							<div class="form-group">
	                            <input class="form-control" placeholder="price" name="retail_price" value="" type="retail_price">
	                        </div>
	                        <div class="form-group">
								<select class="form-control" name="artifact_type" readonly>
									<option>---Select One---</option>
								</select>
	                        </div>
							<div class="form-group">
								<textarea class="form-control" rows="5" name="description"></textarea>
							</div>
	                        <!-- Change this to a button or input when using this as a form -->
	                        <button type="submit" name="save" class="btn btn-lg btn-success ">Save</button>
							<button type="submit" name="delete" class="btn btn-lg btn-danger ">Delete</button>
	                    </fieldset>
	                </label>
	                </form>
	            </div>
	        </div>
		</div><!-- END CONTAINER FLUID -->




	<!-- EXTERNAL JAVASCRIPT -->
	<?php include_once('../includes/js.php'); ?>

	<script type="text/javascript" src="../js/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="../js/ntm.js"></script>
	<script type="text/javascript" src="../js/tree-menu.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".demo").ntm({
			});
		});

		$(document).on('click', 'a', function(){
			var li_val = $(this).text();

		});

		function login(){
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

			if(error == 0){
				data = {username:username, password:password};
				$.ajax({
					data:data,
					method:"POST",
					dataType:"text",
					success:function(result){
						if(result == 1){
							$("#invalid").removeClass("hidden");
						} else{
							// location.href = "dashboard.php";
						}
					},
					error:function(xhr,status,error){
						console.log("XHR: " + JSON.stringify(xhr));
						console.log("Status: " + JSON.stringify(status));
						console.log("Error: " + JSON.stringify(error));
					}
				})
			}
		}
	</script>
	</body>
</html>