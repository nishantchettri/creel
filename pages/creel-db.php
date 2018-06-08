<?php
    include_once("../class/connect.class.php");
    
    session_start();
    extract($_POST);
    extract($_GET);

    // create the SELECT query
    $dbcon= new connect();
    $qry=$dbcon->db1->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = '".DBNAME."'");
    $qry->execute();
    $rows = $qry->rowCount(); // check if query returned any rows
    if($rows > 0){
        $tableList = $qry->fetchAll();
    }
?>

<!DOCTYPE html>
    <html>
    <head>
        <title>View Database</title>

        <?php include_once("../includes/css.php") ?>

        <link rel="stylesheet" type="text/css" href="../plugins/datatables/css/dataTables.bootstrap.min.css">

        <style>
            select{
                font-size:16px !important;   
            }
            
            .dataTables_length, .dataTables_info{
                float:left;
            }
            
            /* table.dataTable th, */
            table.dataTable td {
                max-width:200px !important;
                overflow:hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            
        </style>
    </head>
    <body>
        <?php include_once("../includes/navbar.php") ?>

        <div class="container-fluid" style="margin-bottom:15px;">
            <div class="row" style="margin-bottom:15px">
                <label for="tableList"><h4 style="margin-bottom:0; padding-left:15px">List of Tables&nbsp;</h4></label>
                <select id="tableList" class="form-control" style="width:20%; display:inline-block;">
                    <option value="na">Select One</option>
                    <?php
                        for($i=0; $i<count($tableList); $i++){
                            $tableName = $tableList[$i]["table_name"];
                            $display = false;
                            $displayName = "";
                            switch($tableName){
                                case "tblanglers":
                                    $display = true;
                                    $displayName = "Anglers";
                                    break;
                                case "tblcounts":
                                    $display = true;
                                    $displayName = "Counts";
                                    break;
                                case "tblcounttimes":
                                    $display = true;
                                    $displayName = "Count Times";
                                    break;
                                case "tblfishcaught":
                                    $display = true;
                                    $displayName = "Fish Caught";
                                    break;
                                case "tblfishcaughtimages":
                                    $display = true;
                                    $displayName = "Fish Caught Image";
                                    break;
                                case "tblheader":
                                    $display = true;
                                    $displayName = "Header";
                                    break;
                                case "tblparties":
                                    $display = true;
                                    $displayName = "Parties";
                                    break;
                                // case "tblproject_user":
                                    // $displayName = "Project User Relationship";
                                    // break;
                                case "tblprojects":
                                    $display = true;
                                    $displayName = "Projects";
                                    break;
                                case "tblprojectspecies":
                                    $display = true;
                                    $displayName = "Project Species";
                                    break;
                                // case "tblroles":
                                    // $displayName = "Roles";
                                    // break;
                                case "tblschedule":
                                    $display = true;
                                    $displayName = "Schedules";
                                    break;
                                case "tblspecies":
                                    $display = true;
                                    $displayName = "Species";
                                    break;
                                default:
                                    $display = false;
                                    break;
                            }

                            if($display){
                                echo("<option value='".$tableName."'>".$displayName."</option>");
                            }
                        }
                    ?>
                </select>
            </div>
            <div id="tableDiv" class="row text-center" style="padding-bottom:50px;">
                <!-- ADDED TABLE VIA JQUERY -->
                <div class="panel panel-default">
                    <div class="panel-heading text-left">
                        <span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;Information
                    </div>
                    <div class="panel-body">
                        <h4 style='margin-top:0'>No Table Selected</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="navbar navbar-fixed-bottom">
            <?php include_once("../includes/footer.php"); ?>
        </div>


        <!-- JAVASCRIPT -->
        <?php include_once("../includes/js.php"); ?>
        <script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../plugins/datatables/js/dataTables.bootstrap.min.js"></script>
        <!-- <script src="../plugins/datatables/js/dataTables.responsive.min.js"></script> -->
        <script>
            // If project selection changed
            $("#tableList").on('change',function(){
                var tableName = $(this).find("option:selected").val();
                if(tableName != 'na'){
                    $.ajax({
                        data:{tableName:tableName},
                        url:"../ajax-handlers/metadata-handler.php",
                        type:"POST",
                        dataType:'json',
                        success:function(tableData){
                            $("#tableDiv").empty();
                            $("#tableDiv").append("<table id='myTable' class='table table-bordered table-striped nowrap' cellspacing='0' width='100%'><thead></thead><tbody></tbody></table>");
                            for(var i=0; i<tableData.length; i++){
                                var record = tableData[i]; // each row
                                // Create Table Head
                                if(i==0){
                                    $("#myTable").find('thead').append("<tr></tr>");
                                    $.each(record,function(key,value){
                                        $("#myTable thead tr").append('<th>' +key+ '</th>');
                                    });
                                    $("#myTable").find('thead tr td:last').append('</tr></thead>');

                                } 
                                
                                // Create Table Body
                                $("#myTable").find('tbody').append('<tr>');
                                var count = 0;
                                $.each(record,function(key,value){
                                    if(tableName == "tblfishcaughtimages" && key=="image"){
                                        $("#myTable").find('tbody tr:last').append('<td><img src="data:image/png;base64,' + value + '" height="100" width="auto"/></td>');
                                    } else{

                                        if(key=="uploadDate"){
                                            value = reduceTime(value);
                                            $("#myTable").find('tbody tr:last').append('<td>' +value+ '</td>');    
                                        } else {
                                            $("#myTable").find('tbody tr:last').append('<td>' +value+ '</td>');
                                        }
                                    }
                                    if(count == 0 && key == "surveyStatus"){
                                        //console.log(value);
                                    }
                                });
                                count++;
                                $("#myTable").find('tbody tr:last').append('</tr>');

                                if(i==tableData.length-1){
                                    $("#myTable").DataTable({
                                        responsive:true,
                                        scrollX:true
                                    });
                                }
                            }//end for loop
                        },
                        error:function(xhr, status, error){
                            console.log('Error:' + error);
                            console.log('xhr:' + JSON.stringify(xhr));
                            console.log('status:' + status);
                        }
                    });
                    $("#tableDiv").removeClass("hidden");
                } else {
                    $("#tableDiv").empty();
                    $("#tableDiv").append("<div class='panel panel-default'></div>");
                    $(".panel").append("<div class='panel-heading text-left'></div>");
                    $(".panel").append("<div class='panel-body'></div>");
                    $(".panel-heading").append("<span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;Information");
                    $(".panel-body").append("<h4 style='margin-top:0'>No Table Selected</h4>");
                }
            });

            function activateDataTable(){
                $("#myTable").DataTable({destroy:true});
                $("#myTable").DataTable().columns.adjust().draw();
            }

            // Reduce Time 
            function reduceTime(value){
                return value;
                var originalDate = new Date(value);
                
                var timeDifference = dateFormat(originalDate,"o").replace("-","");
                var offsetTime = dateFormat(timeDifference,"o").replace("+","");

                var offsetTimeHours = (parseInt(offsetTime.substr(1,2)) - 1) * 60 * 60 * 1000; // in milliseconds
                var offsetTimeMinutes = parseInt(offsetTime.substr(3)) * 60 * 1000; // in milliseconds
                var totalOffset = offsetTimeHours + offsetTimeMinutes;

                var originalDateInMilliseconds = originalDate.getTime();
                var finalTimeInMilliseconds = originalDateInMilliseconds - totalOffset;
                return dateFormat(new Date(finalTimeInMilliseconds),"yyyy-mm-dd HH:MM:ss");
            }
        </script>
    </body>
</html>