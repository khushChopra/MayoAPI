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
        $tsql1 = "select * from govt where city='".$_GET['city']."'";
        $getResults= mysqli_query($conn, $tsql1);

        $count = 0;
        while($row = mysqli_fetch_array($getResults)){
            $result['service'][$count]['body'] = $row['body'];
            $result['service'][$count]['title'] = $row['title'];
            $result['service'][$count]['contact'] = $row['contact'];
            $result['service'][$count]['serviceID'] = $row['courseID'];
            $result['service'][$count]['city'] = $row['city'];
            $count = $count + 1;
        }
        $result["count"] = $count;
        echo json_encode($result);
        break;
    case 'POST':
        $tsql1 = "insert into govt (city, contact,body,title) VALUES ('".$input['city']."','".$input['contact']."','".$input['body']."','".$input['title']."')";
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