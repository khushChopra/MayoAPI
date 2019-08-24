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
        $tsql1 = "select * from feedback where personID='".$_GET['personID']."'";
        $getResults= mysqli_query($conn, $tsql1);

        $count = 0;
        while($row = mysqli_fetch_array($getResults)){
            $result['feedback'][$count]['text'] = $row['text'];
            $result['feedback'][$count]['rating'] = $row['rating'];
            $result['feedback'][$count]['personID'] = $row['personID'];
            $count = $count + 1;
        }
        $result["count"] = $count;
        echo json_encode($result);
        break;
    case 'POST':
        $tsql1 = "insert into feedback (personID, `text`,rating) VALUES ('".$input['personID']."','".$input['text']."',".$input['rating'].")";
        $insertReview = mysqli_query($conn, $tsql1);
        // check for server error
        if($insertReview==FALSE){
            message_and_code("Server error",500);
            break;
        }
        else{
            message_and_code("Success",500);
            break;
        }
        break;
}
mysqli_close($conn);
?>