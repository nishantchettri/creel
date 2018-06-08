<?php
include_once("../class/users.class.php");
include_once("../class/utilities.class.php");
include_once("../class/projects.class.php");

session_start();
extract($_POST);
extract($_GET);

$username = $_SESSION["username"];
$users = new users();
$userInfo = $users->getUserInfo($username);
if($userInfo["permissionLevel"] > 2){
	header("location: dashboard.php");
}

// Download CSV
if(isset($_POST["tableName"]) && isset($_POST["projectUID"])){
    $tableName = $_POST["tableName"];
    $projectUID = $_POST["projectUID"];
    $utilities = new utilities();
    $tableData = $utilities->getTableData($projectUID, $tableName, $userInfo["userUID"]);
    echo $tableData;
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Download CSV</title>
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
    
    <div class="container-fluid col-centered">
        <div class="panel panel-default" style="width:40%; margin:25px auto 0 auto">
            <div class="panel-heading"><h4>Download CSV File</h4></div>
            <!-- START ALERT MESSAGE -->
		    <div class="row success hidden" style="width:100%; background-color:lightblue;">
		        <h4 align="center" id="success_msg" style="color:white; font-family:calibri">Successfully Uploaded</h4>
		    </div>
		    <div class="row error hidden" style="width:100%; background-color:red; ">
		        <h4 align="center" id="error_msg" style="color:white; font-family:calibri">No records found</h4>
		    </div>
		    <!-- END ALERT MESSAGE -->
            <form id="myForm" enctype="multipart/form-data">
                <div class="panel-body">
                    <?php
                        $projects = new projects();
                        $projectList = $projects->getProjectList();

                    ?>
                     <div class="row project-row" style="margin-bottom:15px">
                        <label for="projectList">SELECT PROJECT:</label>
                        <select id="projectList" class="form-control" name="selectedProject">
                            <option value="na">Select One</option>
                            <?php
                                for($i=0; $i<count($projectList);$i++){
                                    echo "<option value='".$projectList[$i]['projectUID']."'>".$projectList[$i]["projectName"]."</option>";
                                }
                            ?>
                        </select>
                    </div>
                     <div class="row hidden table-row" style="margin-bottom:15px">
                        <label for="tableList">SELECT TABLE TO DOWNLOAD FROM</label>
                        <select id="tableList" class="form-control" name="selectedTable">
                            <!-- APPEND FROM JQUERY CODE -->
                        </select>
                    </div>
                    <div class="row hidden download-row">
                        <div class="row" style="text-align:center;">
                            <button type="button" onclick="downloadCSV()" class="btn btn-success btn-block">DOWNLOAD</button>
                        </div>
                    </div>
                    <hr/>
                    <p>
                        <label>#Note:</label> Please select a project to download data 
                    </p>
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
                            case "tblcounts":
                                display = true;
                                displayName = "Counts";
                                break;
                            case "tblheader":
                                display = true;
                                displayName = "Header";
                                break;
                            case "tblparties":
                                display = true;
                                displayName = "Parties";
                                break;
                            case "tblanglers":
                                display = true;
                                displayName = "Anglers";
                                break;
                            case "tblfishcaught":
                                display = true;
                                displayName = "Fish Caught";
                                break;
                            /*case "tblspecies":
                                display = true;
                                displayName = "Species";
                                break;*/
                            default:
                                break;
                        }

                        if(display){
                            $("#tableList").append("<option value='"+tableName+"'>" + displayName + "</option>");
                        }
                    }
                    $("#tableList").append("<option value='all'>All Tables</option>");
                }
            }
        });
    });

    // Show or hide table list
    $("#projectList").on('change',function(){
        var projectUID = $(this).val();
        if(projectUID == "na"){
            $(".table-row").addClass("hidden");
            if(!$(".download-row").hasClass("hidden")){
                $(".download-row").addClass("hidden");
            }
        } else {
            $(".table-row").removeClass("hidden");
            $("#tableList").val("na").change();
        }
    });

    // Show or hide download button on table select
    $("#tableList").on('change',function(){
        var tableName = $(this).val();
        if(tableName == "na"){
            $(".download-row").addClass("hidden");
        } else {
            $(".download-row").removeClass("hidden");
        }
    });

    // Submit Button Action
    function downloadCSV(){
        var tableName = $("#tableList option:selected").val();
        var projectUID = $("#projectList option:selected").val();
        if(tableName != "na" && projectUID != "na"){
            $.ajax({
                data: {projectUID:projectUID, tableName:tableName},
                method:"POST",
                dataType:"json",
                success:function(result){
                	if(JSON.stringify(result) && JSON.stringify(result.length)>0){
                		$(".error").addClass("hidden");
                		JSONToCSVConvertor(result, tableName, true);
                	} else {
                		$(".error").removeClass("hidden");
                	}
                },
                error:function(xhr,status,error){
                    console.log("XHR: " + JSON.stringify(xhr));
                    console.log("Status: " + JSON.stringify(status));
                    console.log("Error: " + JSON.stringify(error));
                }
            });
        } else {
            //error
        }
    }

    // Creates CSV File
    function JSONToCSVConvertor(jsonData, reportTitle, showLabel) {
        //If jsonData is not an object then JSON.parse will parse the JSON string in an Object
        var arrData = typeof jsonData != 'object' ? JSON.parse(jsonData) : jsonData;
        
        var CSV = '';    
        //Set Report title in first row or line
        //CSV += reportTitle + '\r\n\n';

        //This condition will generate the Label/Header
        // Label = column_names in table
        var row = "";
        if (showLabel) {
            
            //This loop will extract the label from 1st index of on array
            for (var index in arrData[0]) {
                
                //Now convert each value to string and comma-seprated
                row += index + ',';
            }

            row = row.slice(0, -1);
            
            //append Label row with line break
            CSV += row + '\r\n';
        }
        
        //1st loop is to extract each row
        for (var i = 0; i < arrData.length; i++) {
            row = "";
            
            //2nd loop will extract each column and convert it in string comma-seprated
            for (var index in arrData[i]) {
                row += '"' + arrData[i][index] + '",';
            }

            row.slice(0, row.length - 1);
            
            //add a line break after each row
            CSV += row + '\r\n';
        }

        if (CSV == '') {        
            // alert("Invalid data");
            return;
        }   
        
        //Generate a file name
        var fileName = "MyReport_";
        //this will remove the blank-spaces from the title and replace it with an underscore
        fileName += reportTitle.replace(/ /g,"_");   
        
        //Initialize file format you want csv or xls
        var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
        
        // Now the little tricky part.
        // you can use either>> window.open(uri);
        // but this will not work in some browsers
        // or you will not get the correct file extension    
        
        //this trick will generate a temp <a /> tag
        var link = document.createElement("a");    
        link.href = uri;
        
        //set the visibility hidden so it will not effect on your web-layout
        link.style = "visibility:hidden";
        link.download = fileName + ".csv";
        
        //this part will append the anchor tag and remove it after automatic click
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    </script>   
    </body>
</html>