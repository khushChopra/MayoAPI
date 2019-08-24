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

        $city = $_GET['city'];
        $skill = $_GET['skill'];

        $tsql1 = "select 
                    skill, sum(numberOfPeople) as totNum
                FROM
                    jobRequest
                WHERE
                    city = '".$city."' and
                    skill!=".$skill." and
                    status!=2
                GROUP BY
                    skill
                ORDER BY
                    totNum DESC";
        $getResults= mysqli_query($conn, $tsql1);
        $skillWanted = -5;

        if($row = mysqli_fetch_array($getResults)){
            $skillWanted = $row['skill'];
        }
        if($skillWanted<0){
            message_and_code("No course found",400);
        }


        $tsql1 = "select * from course where skill=".$skillWanted;


        $getResults= mysqli_query($conn, $tsql1);

        $count = 0;
        while($row = mysqli_fetch_array($getResults)){
            $result['course'][$count]['body'] = $row['body'];
            $result['course'][$count]['title'] = $row['title'];
            $result['course'][$count]['contact'] = $row['contact'];
            $result['course'][$count]['courseID'] = $row['courseID'];
            $result['course'][$count]['skill'] = $row['skill'];
            $count = $count + 1;
        }
        $result["count"] = $count;
        echo json_encode($result);
        break;
}
mysqli_close($conn);
?>