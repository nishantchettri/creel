<?php
    include_once("../class/users.class.php");

    session_start();
    extract($_POST);
    extract($_GET);

    if(!isset($_SESSION["username"])){
        header("location:index.php");
    }

    $username = $_SESSION["username"];
    $error = 0;
    $error_msg = "";

    if(isset($_POST["cPass"]) && isset($_POST["passwd1"]) && isset($_POST["passwd2"])){
        $cpass = $_POST["cPass"];
        $passwd1 = $_POST["passwd1"];
        $passwd2 = $_POST["passwd2"];

        $user = new users();
        $validate = $user->validateUser($username, $cpass);

        // If current password matches $res will be true or not null
        $arr = array();
        if($validate){
            // If pass1 and pass2 match
            if(strcasecmp($passwd1, $passwd2)==0){
                // Hash-Salt password
                $salt = hash("sha256", strval(rand()));
                $saltedPassword = $passwd1 . $salt;
                $hashedSaltedPassword = hash("sha256", $saltedPassword);

                // UPDATE PASSWORD
                $update = $user->updatePassword($username, $hashedSaltedPassword, $salt);

                // update returns true or false depending on if it updated or not 
                if($update){
                    $arr["success"] = 1;
                    $arr["message"] = "Successfully updated your password!";
                } else {
                    $arr["success"] = 0;
                    $arr["message"] = "Unable to update your password!";
                }
            } else {
                $arr["success"] = 0;
                $arr["message"] = "Passwords do not match!";
            }
        } else {
            $arr["success"] = 0;
            $arr["message"] = "Current password is incorrect";
        }
        echo json_encode($arr);
        return;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <?php include_once('../includes/css.php') ?>
    <style>
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
        select{
            font-size:16px !important;
        }
        .navbar{
            margin-bottom:0;
        }
        .main-form{
            margin-bottom:50px;
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
<body>
    <?php include_once('../includes/navbar.php') ?>

    <div class="container-fluid col-centered main-form">
        <div class="panel panel-default" style="width:40%; margin:25px auto 0 auto">
            <div class="panel-heading"><h4>Change Password</h4></div>
            <div id="successful" class="row valid hidden">
                <h5 id="successful_message"></h5>
            </div>
            <div id="unsuccessful" class="row invalid hidden">
                <h5 id="unsuccessful_message"></h5>
            </div>
            <form id="updatePassword" method="post">
                <div class="panel-body">
                    <div class="row" style="margin-bottom:15px">
                        <label for="currentPassword">Current Password</label>
                        <input id="currentPassword" type="password" class="form-control" value="">
                    </div>
                    <div class="row" style="margin-bottom:15px">
                        <label for="passwd1">New Password</label>
                        <input id="passwd1" type="password" class="form-control" value="">
                    </div>
                    <div class="row" style="margin-bottom:15px">
                        <label for="passwd2">Re-enter Password</label>
                        <input id="passwd2" type="password" class="form-control" value="">
                    </div>
                    <div class="row" style="text-align:center;">
                        <input type="submit" class="btn btn-success btn-block" value="Update Password" />
                    </div>
                </div>
            </form>     
        </div>        
    </div>

    <div class="navbar navbar-fixed-bottom">
        <?php include_once("../includes/footer.php") ?>
    </div>

    <!-- JAVASCRIPT -->
    <?php include_once('../includes/js.php') ?>
    <script>
        $("#updatePassword").submit(function(e){
            // Prevent submit button from submitting form via PHP
            e.preventDefault();
            var currentPassword = $("#currentPassword").val();
            var password1 = $("#passwd1").val();
            var password2 = $("#passwd2").val();

            var data = {
                cPass: currentPassword,
                passwd1: password1,
                passwd2: password2
            };

            $.ajax({
                data:data,
                type:"POST",
                dataType:'json',
                success:function(result){
                    if(result["success"] == 1){
                        $("#unsuccessful").addClass("hidden");
                        $("#successful").removeClass("hidden");
                        $("#successful_message").text(result["message"]);
                    } else {
                        $("#successful").addClass("hidden");
                        $("#unsuccessful").removeClass("hidden");
                        $("#unsuccessful_message").text(result["message"]);
                    }
                },
                error:function(xhr, status, error){
                    console.log('Error:' + error);
                    console.log('xhr:' + JSON.stringify(xhr));
                    console.log('status:' + status);
                }
            });
        });
    </script>   
    </body>
</html>