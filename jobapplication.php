<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "database-mayo1.cusbnuvbxuoj.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "adminpass";
$database = "mayo";


$conn = mysqli_connect($servername, $username, $password, $database);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'),true);



$result = array();
function message_and_code($message, $code){
    $temp = array();
    $temp["message"] = $message;
    http_response_code($code);
    echo json_encode($temp);
}
switch ($method) {
    case 'GET':
        // gives all
        $tsql1 = "select * from jobApplication";
        if($_GET['applicationID']!=""){
            $tsql1 = "select * from jobApplication where applicationID='".$_GET['applicationID']."'";
        }
        if($_GET['employeeID']!=""){
            $tsql1 = "select * from jobApplication where employeeID='".$_GET['employeeID']."'";
        }
        if($_GET['jobID']!=""){
            $tsql1 = "select * from jobApplication where jobID='".$_GET['jobID']."'";
        }

        
        $getResults= mysqli_query($conn, $tsql1);

        $count = 0;
        while($row = mysqli_fetch_array($getResults)){
            $result['jobApplication'][$count]['applicationID'] = $row['applicationID'];
            $result['jobApplication'][$count]['jobID'] = $row['jobID'];
            $result['jobApplication'][$count]['employeeID'] = $row['employeeID'];
            $result['jobApplication'][$count]['status'] = $row['status'];
            $count = $count + 1;
        }
        $result["count"] = $count;
        echo json_encode($result);
        break;
    case 'POST':



        if($input['action']==1){
            $tsql1 = "update jobApplication set status=".$input['status']." where applicationID=".$input['applicationID'];
            $updateReview = mysqli_query($conn, $tsql1);
            // check for server error
            if($updateReview==FALSE){
                message_and_code("Server error",500);
                break;
            }
            else{
                message_and_code("Success",200);
                break;
            }
            break;
        }


        $tsql1 = "insert into jobApplication (employeeID, jobID, status) VALUES ('".$input['employeeID']."',".$input['jobID'].",".$input['status'].")";
        $insertReview = mysqli_query($conn, $tsql1);
        // check for server error
        if($insertReview==FALSE){
            message_and_code("Server error",500);
            break;
        }
        else{
            message_and_code("Success",200);
            break;
        }
        break;
}
mysqli_close($conn);
?>