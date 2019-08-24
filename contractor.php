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
      	$tsql1 = "select * from contractor where phoneNumber='".$_GET['phoneNumber']."' and password='".hash('sha256',$_GET['password'])."'";
    	$getResults= mysqli_query($conn, $tsql1);

    	if($row = mysqli_fetch_array($getResults)){

    		// send user data
    		$result["message"] = "User logged in successfully";
            $result['contractor']['name'] = $row['name'];
            $result['contractor']['address'] = $row['address'];
            echo json_encode($result);
            break;
    	}
    	else{
    		message_and_code("Phone number or password incorrect",400);
        }
        break;
    case 'POST':


        if($input['action']==1){
            //  echo "signup";
            $tsql1 = "select * from contractor where phoneNumber='".$input['phoneNumber']."'";
            $getResults= mysqli_query($conn, $tsql1);
            if($row = mysqli_fetch_array($getResults)){
                message_and_code("User already exists",400);
                break;
            }
            else{
                // new user is to be created and token is sent to the user


                $phoneNumber = $input['phoneNumber'];
                $password = hash('sha256', $input['password']);
                $name = $input['name'];
                $address = $input['address'];
                $tsql1= "insert into contractor values('".$phoneNumber."','".$password."','".$name."','".$address."')";
                $insertReview = mysqli_query($conn, $tsql1);
                // check for server error
                if($insertReview==FALSE){
                    message_and_code("Server error",500);
                    break;
                }
                $result["message"] = "user created successfully";
                $result['contractor']['name'] = $name;
	            $result['contractor']['address'] = $address;
                http_response_code(200);
                echo json_encode($result);
                break;
            }    
            break;
        }
        else if($input['action']==2){
            // echo "update";
            $tsql1 = "select * from contractor where phoneNumber='".$input['phoneNumber']."'";
            $getResults= mysqli_query($conn, $tsql1);
            if($row = mysqli_fetch_array($getResults)){
                $name = $input['name'];
                $address = $input['address'];

                $tsql1= "update contractor set name='".$name."',address='".$address."'";

                $updateReview = mysqli_query($conn, $tsql1);
                // check for server error
                if($updateReview==FALSE){
                    message_and_code("Server error",500);
                    break;
                }

                $result["message"] = "user updated successfully";
                $result['contractor']['name'] = $name;
                $result['contractor']['address'] = $address;

                http_response_code(200);
                echo json_encode($result);
                break;


            }
            else{
            	message_and_code("User doesn't exists",400);
                break;
            }    
            break;
        }
}
mysqli_close($conn);
?>