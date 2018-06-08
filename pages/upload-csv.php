<?php

// include_once("../class/connect.class.php");
include_once("../class/projects.class.php");

$projects = new projects();
$projectList = $projects->getProjectList();

session_start();
extract($_POST);
extract($_GET);
$firstLine = 0;
$dbColumns = 0;

if(isset($_POST["submit"]) && isset($_POST["project"]) && isset($_FILES["file"]) && isset($_POST["tableName"])){
    $file = $_FILES["file"]["tmp_name"];
    $file = str_replace("\\","\\\\",$file);
    $uploaded = 0;
    $table = $_POST["tableName"];
    $projectUID = $_POST["project"];

    $response = array();

    try{
        $dbcon = new connect();

        // Get column names of table
        $qry = $dbcon->db1->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :table and TABLE_SCHEMA = 'creel_db'");
        $qry->bindParam(":table",$table,PDO::PARAM_STR);
        $qry->execute();
        $res = $qry->fetchAll(PDO::FETCH_ASSOC);

        // Put JSON Array into simple array
        $dbColumns = array();
        $compColumns = array();
        $projectUIDIndex = null;
        $columns = "(";
        for($i=0; $i<count($res);$i++){
            if($res[$i]["COLUMN_NAME"] == "projectUID"){
                $projectUIDIndex = $i;
            } else {
                array_push($compColumns, $res[$i]["COLUMN_NAME"]);
            }
            array_push($dbColumns, $res[$i]["COLUMN_NAME"]);
        }

        $columnNames = "(".implode(", ",$dbColumns).")";

        // Read first line of CSV file
        $csv = fopen($file,"r");
        $firstLine = fgetcsv($csv);

        $firstLineString = implode(", ",$firstLine);

        // 1. Check if number of columns in database == number of columns in CSV
        // 2. If column count is same, check if column name in database == column name in CSV
        $invalid = false;
        if(count($firstLine) == count($compColumns)){
            // Compare column names but skip dbColumn's projectUIDIndex column
            for($i=0; $i<count($compColumns);$i++){
                if(strcasecmp(trim($compColumns[$i]),trim($firstLine[$i])) != 0){
                    // echo $compColumns[$i].": ".$firstLine[$i];
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
                $db = new PDO("mysql:host=".HOSTNAME.";dbname=".DBNAME.";",USERNAME,PASSWORD, array(PDO::MYSQL_ATTR_LOCAL_INFILE => true));
        
                // set the PDO error mode to exception
                // throws exception
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // IGNORE keyword appends
                // REPLACE keyword inserts/overwrites
                $queryString = "LOAD DATA LOCAL INFILE '".$file."' IGNORE INTO TABLE ".$table." FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\n' IGNORE 1 LINES (".$firstLineString.") SET projectUID = '".$projectUID."';";

                // echo $queryString;
                // return;

                $query = $db->prepare($queryString);
                $res = $query->execute();
                $rows = $query->rowCount();
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
    }catch(PDOException $e){
        $response["status"] = 0;
        $response["message"] = "Error: " . $e->getMessage();
    }
    echo json_encode($response);
    return;
} // if file and table name sent
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload CSV</title>
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
    </style>
</head>
<body>
    <?php include_once('../includes/navbar.php') ?>

    <div class="container-fluid col-centered" style="margin-bottom:50px;">
        <div class="panel panel-default" style="width:40%; margin:25px auto 0 auto">
            <div class="panel-heading"><h4>Upload CSV File</h4></div>
            <!-- START ALERT MESSAGE -->
            <div class="row success hidden" style="width:100%; background-color:lightblue;">
                <h4 align="center" id="success_msg" style="color:white;"></h4>
            </div>
            <div class="row error hidden" style="width:100%; background-color:red;">
                <h4 align="center" id="error_msg" style="color:white;"></h4>
            </div>
            <!-- END ALERT MESSAGE -->
            <form id="myForm" enctype="multipart/form-data">
                <div class="panel-body">
                    <p><b><u>Note:</u></b> Select project before uploading CSV</p>
                    <div class="row" style="margin-bottom:15px">
                        <label for="projectList">SELECT PROJECT&nbsp;</label>
                        <select id="projectList" class="form-control" name="selectedTable">
                            <option value="na">Select Project</option>
                            <?php
                                for($i=0; $i<count($projectList);$i++){
                                    $projectName = $projectList[$i]["projectName"];
                                    $projectUID = $projectList[$i]["projectUID"];
                                    echo "<option value='".$projectUID."'>".$projectName."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div id="tableName" class="row hidden" style="margin-bottom:15px">
                        <label for="tableList">INSERT INTO&nbsp;</label>
                        <select id="tableList" class="form-control" name="selectedTable">
                            <!-- APPEND FROM JQUERY CODE -->
                        </select>
                    </div>
                    <div id="selectFile" class="row hidden">
                        <div class="row" style="margin-bottom: 15px">
                            <label for="file">CSV File&nbsp;</label>
                            <input id="file" class="btn" name="uploadFile" type="file" style="display:inline-block;"/>
                        </div>
                        <div class="row" style="text-align:center;">
                            <button type="button" onclick="submitForm()" class="btn btn-success btn-block">UPLOAD</button>
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
    $(document).ready(function(){
        $("#tableList").empty();
        $("#tableList").append("<option value='na'>Select a Table</option>");
        $.ajax({
            url:'get-table.php',
            dataType:'json',
            success:function(tableList){
                if(tableList.length>0){
                    for(var i=0; i<tableList.length; i++){
                        var tableName = tableList[i].table_name;
                        var display = false;
                        var displayName = "";
                        switch(tableName){
                            case "tblcounttimes":
                                display = true;
                                displayName = "Count Times";
                                break;
                            case "tblprojectspecies":
                                display = true;
                                displayName = "Project Species";
                                break;
                            case "tblschedule":
                                display = true;
                                displayName = "Schedules";
                                break;
                            default:
                                break;
                        }

                        if(display){
                            $("#tableList").append("<option value='"+tableName+"'>" + displayName + "</option>");
                        }
                    }
                }
            }
        });
    });

    // Show other options only when project is selected
    $("#projectList").on("change",function(){
        var selectedProject = $(this).val();
        if(selectedProject == "na"){
            $("#tableName").addClass("hidden");
            $("#selectFile").addClass("hidden");
        } else {
            $("#tableName").removeClass("hidden");
            $("#selectFile").removeClass("hidden");
        }
    });

    // Submit Button Action
    function submitForm(){
        var form = $("#myForm");
        var fd = new FormData(form);

        // Get Data
        var projectUID = $("#projectList option:selected").val();
        var tableName = $("#tableList option:selected").val();
        var fileInput = $("#file")[0].files[0];

        if(projectUID == "na"){
            $("#error_msg").text("Select a project!");
            $(".error").removeClass("hidden");
            return;
        } else if(tableName == "na"){
            $("#error_msg").text("Select a table!");
            $(".error").removeClass("hidden");
            return;
        } else if(fileInput == null){
            $("#error_msg").text("Select a file!");
            $(".error").removeClass("hidden");
            return;
        } else {
            $(".error").addClass("hidden");

            fd.append("tableName",tableName);
            fd.append("file",fileInput,fileInput.name);
            fd.append("project",projectUID);
            fd.append("submit","submit");

            // Send Data to PHP
            $.ajax({
                type:'POST',
                data:fd,
                processData:false,
                contentType:false,
                dataType:"json",
                success:function(response){
                    // console.log(JSON.stringify(response));
                    // console.log(response);
                    // return;
                    var status = response["status"];
                    var message = response["message"];

                    // If successfully uploaded
                    if(status == 1){
                        $("#success_msg").text(message);
                        $(".error").addClass("hidden");
                        $(".success").removeClass("hidden");
                    } else if (status == 0){
                        $("#error_msg").text(message);
                        $(".success").addClass("hidden");
                        $(".error").removeClass("hidden");
                    }
                },
                error:function(xhr,status,error){
                    // leave blank
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
    }
    </script>   
    </body>
</html>