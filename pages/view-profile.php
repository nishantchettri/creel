<?php
include_once("../class/users.class.php");
include_once("../class/roles.class.php");

session_start();
extract($_POST);
extract($_GET);

$username = $_SESSION["username"];

$user = new users();
$userinfo = $user->getUserInfo($username);

/*if(isset($_POST["submit"])){
    if(isset($_FILES["file"]) && isset($_POST["table"])){
        $file = $_FILES["file"]["tmp_name"];
        $file = str_replace("\\","\\\\",$file);
        $uploaded = 0;
        $table = $_POST["table"];

        $response = array();

        try{
            $dbcon = new connect();

            // Get column names of table
            $qry = $dbcon->db1->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :table");
            $qry->bindParam(":table",$table,PDO::PARAM_STR);
            $qry->execute();
            $res = $qry->fetchAll();

            // Put JSON Array into simple array
            $dbColumns = array();
            for($i=0; $i<count($res);$i++){
                array_push($dbColumns, $res[$i]["COLUMN_NAME"]);
            }

            // Read first line of CSV file
            $csv = fopen($file,"r");
            $firstLine = fgetcsv($csv);

            // 1. Check if number of columns in database == number of columns in CSV
            // 2. If column count is same, check if column name in database == column name in CSV
            $invalid = false;
            if(count($firstLine) == count($dbColumns)){
                for($i=0; $i<count($firstLine); $i++){
                    // If column names do not match (case-insensitive)
                    if(strcasecmp($firstLine[$i],$dbColumns[$i])!=0){
                        $invalid = true;
                        break;
                    }
                }
                // If error found
                if($invalid){
                    $response["status"] = 0;
                    $response["message"] = "Column Names do not match";
                } else {
                    // If error not found, upload CSV data to table
                    // Need to initialize in this manner 
                    // FILE UPLOAD only possible when MYSQL_ATTR_LOCAL_INFILE=>true
                    $db = new PDO("mysql:host=".$hostname.";dbname=".$dbname.";",$username,$password, array(PDO::MYSQL_ATTR_LOCAL_INFILE => true));
            
                    // set the PDO error mode to exception
                    // throws exception
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $queryString = "LOAD DATA LOCAL INFILE '".$file."' INTO TABLE ".$table." FIELDS TERMINATED BY \",\" OPTIONALLY ENCLOSED BY '\"' ESCAPED BY \"\\\\\" LINES TERMINATED BY \"\\n\" IGNORE 1 LINES";

                    $query = $db->prepare($queryString);
                    $query->execute();
                    $rows=$query->rowCount();
                    
                    // Check if new rows were added
                    if($rows>0){
                        $response["status"] = 1;
                        $response["message"] = "Successfully uploaded new records";
                    } else {
                        $response["status"] = 0;
                        $response["message"] = "No new records found";
                    }
                }
            } else {
                $response["status"] = 0;
                $response["message"] = "Column Lengths do not match";
            }
            echo json_encode($response);
            exit;
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    } // if file and table name sent
}*/
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Profile</title>
    <?php include_once('../includes/css.php') ?>
    <link href="../plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>

    <style>
        /*.min-width-margin{
            margin-left:5px;
            margin-right:5px;
        }           
        .bold{
            font-weight:bold;
        }*/
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
        /*.label-background{
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
        }*/
        /*.main-form{
            margin-bottom:50px;
        }*/
    </style>
</head>
<body>
    <?php include_once('../includes/navbar.php') ?>
    
    <!-- START ALERT MESSAGE -->
    <!-- <div class="row success hidden" style="width:100%; background-color:lightblue; mar">
        <h2 align="center" id="success_msg" style="padding-top:10px; padding-bottom:10px; color:white; font-family:calibri">Successfully Updated</h2>
    </div>
    <div class="row error hidden" style="width:100%; background-color:red; mar">
        <h2 align="center" id="error_msg" style="padding-top:10px; padding-bottom:10px; color:white; font-family:calibri">Update Failure</h2>
    </div> -->
    <!-- END ALERT MESSAGE -->

    <div class="container-fluid div-center">
        <div class="panel panel-default" style="margin-bottom:50px">
            <div class="panel-heading row" style="text-align:center;">
                <div class="col-md-8">
                    <h3 style="margin:5px;" class="text-left bold">My Profile</h3>
                </div>
                <div class="col-md-4 text-right">
                    <button class="btn btn-default lock" onclick="makeEditable('unlock')"><span class="fa fa-lock fa-lg"></span></button>
                    <button class="btn btn-default unlock hidden" onclick="makeEditable('lock')"><span class="fa fa-unlock fa-lg"></span></button>
                </div>
            </div>
            <div class="panel-body">
                <div class="row col-md-12" style="margin-bottom:15px">
                    <form id="myForm" method="post">
                        <div class="row">
                            <div class="form-group col-md-6" style="padding-left:0">
                                <label for="fname">First name</label>
                                <input id="fname" type="text" class="editable form-control" value="<?php echo $userinfo['fname']; ?>" readonly>
                            </div>
                            <div class="form-group col-md-6" style="padding-right:0">
                                <div class="row>
                                    <label for="lname">Last name</label>
                                    <input id="lname" type="text" class="editable form-control" value="<?php echo $userinfo['lname']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="text" class="editable form-control" value="<?php echo $userinfo['email']; ?>" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                $roles = new roles();
                                $allRoles = $roles->getAllRoles();
                            ?>
                            <div class="form-group">
                                <label for="userRole">User Role</label>
                                <select class="form-control" id="userRole">
                                    <?php for($i=0;$i<count($allRoles);$i++){
                                        if($userinfo["roleID"] == $allRoles[$i]["roleID"]){
                                            echo "<option value='".$allRoles[$i]['roleID']."' selected>".$allRoles[$i]['roleName'   ]."</option>";
                                        } else{
                                            echo "<option value='".$allRoles[$i]['roleID']."'>".$allRoles[$i]['roleName']."</option>";
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="text-align:center;">
                            <button type="button" onclick="submitForm()" class="btn btn-success btn-block">SUBMIT</button>
                        </div>
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
        // change role
        function submitForm(){
            var role = $("#userRole option:selected").val();
        }

        // Make disabled fields editable
        function makeEditable(action){
            if(action == "unlock"){
                $(".editable").prop("readonly",false);
                $(".unlock").removeClass("hidden");
                $(".lock").addClass("hidden");
            } else if(action == "lock"){
                $(".editable").prop("readonly",true);
                $(".lock").removeClass("hidden");
                $(".unlock").addClass("hidden");
            }
        }
    </script>   
    </body>
</html>