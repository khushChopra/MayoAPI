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

        $tsql1 = "select * from jobRequest";
        if($_GET['city']!=""){
            $tsql1 = "select * from jobRequest where city='".$_GET['city']."'";
        }

        
        $getResults= mysqli_query($conn, $tsql1);

        $count = 0;
        while($row = mysqli_fetch_array($getResults)){
            $result['jobRequest'][$count]['contractorID'] = $row['contractorID'];
            $result['jobRequest'][$count]['jobID'] = $row['jobID'];
            $result['jobRequest'][$count]['title'] = $row['title'];
            $result['jobRequest'][$count]['numberOfPeople'] = $row['numberOfPeople'];
            $result['jobRequest'][$count]['skill'] = $row['skill'];
            $result['jobRequest'][$count]['city'] = $row['city'];
            $result['jobRequest'][$count]['address'] = $row['address'];
            $result['jobRequest'][$count]['status'] = $row['status'];
            $count = $count + 1;
        }
        $result["count"] = $count;
        echo json_encode($result);
        break;
    case 'POST':


        if($input['action']==1){
            $tsql1 = "update jobRequest set status=".$input['status']." where jobID=".$input['jobID'];
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




        $tsql1 = "insert into jobRequest (contractorID, title, numberOfPeople, skill, city, address, status) VALUES ('".$input['contractorID']."','".$input['title']."',".$input['numberOfPeople'].",".$input['skill'].",'".$input['city']."','".$input['address']."',".$input['status'].")";
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