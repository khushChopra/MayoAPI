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
      	$tsql1 = "select * from employee where phoneNumber='".$_GET['phoneNumber']."'";
    	$getResults= mysqli_query($conn, $tsql1);
    	if($row = mysqli_fetch_array($getResults, mysqli_fetch_assoc)){

    		// send user data
    		$result["message"] = "User logged in successfully";
            $result['employee']['name'] = $row['name'];
            $result['employee']['age'] = $row['age'];
            $result['employee']['skill'] = $row['skill'];
            $result['employee']['city'] = $row['city'];
            $result['employee']['state'] = $row['state'];
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
            $tsql1 = "select * from employee where phoneNumber='".$input['phoneNumber']."'";
            $getResults= mysqli_query($conn, $tsql1);
            if($row = mysqli_fetch_array($getResults, mysqli_fetch_assoc)){
                message_and_code("User already exists",400);
                break;
            }
            else{
                // new user is to be created and token is sent to the user


                $phoneNumber = $input['phoneNumber'];
                $password = hash('sha256', $input['password']);
                $name = $input['name'];
                $age = $input['age'];
                $skill = $input['skill'];
                $city = $input['city'];
                $state = $input['state'];
                $tsql1= "insert into employee values('".$phoneNumber."','".$password."','".$name."',".$age.",".$skill.",'".$city."','".$state."')";
                $insertReview = mysqli_query($conn, $tsql1);
                // check for server error
                if($insertReview==FALSE){
                    message_and_code("Server error",500);
                    break;
                }
                $result["message"] = "user created successfully";
                $result['employee']['name'] = $name;
	            $result['employee']['age'] = $age;
	            $result['employee']['skill'] = $skill;
	            $result['employee']['city'] = $city;
	            $result['employee']['state'] = $state;
                http_response_code(200);
                echo json_encode($result);
                break;
            }    
            break;
        }
        else if($input['action']==2){
            // echo "login";
            // curl --header "Content-Type: application/json" --request POST --data '{"action":2,"email":"khush@gmail.com","pass":"password"}' https://purohitji.azurewebsites.net/user.php
            $tsql1 = "select * from users where email='".$input['email']."'";
            $getResults= mysqli_query($conn, $tsql1);
            if($row = mysqli_fetch_array($getResults, mysqli_fetch_assoc)){
                // user exists
                $passwordquery = "select * from users where email='".$input['email']."' and pass='".hash('sha256', $input['pass'])."'";
                $passwordqueryResult = mysqli_query($conn, $passwordquery);
                if($row = mysqli_fetch_array($passwordqueryResult, mysqli_fetch_assoc)){
                    $result['token'] = generateToken();
                    $token = hash('sha256', $result['token']);
                    $tokenquery = "update users set token='".$token."' where email='".$input['email']."'";
                    $tokenqueryResult= mysqli_query($conn, $tokenquery);
                    if($tokenqueryResult==FALSE){
                        message_and_code("Server error",500);
                        break;
                    }
                    http_response_code(200);
                    
                    $result["message"] = "User logged in";
                    $result['user']['email'] = $row['email'];
                    $result['user']['number'] = $row['number'];
                    $result['user']['gender'] = $row['gender'];
                    $result['user']['age'] = $row['age'];
                    $result['user']['fullname'] = $row['fullname'];
                    $result['user']['location'] = $row['location'];
                    echo json_encode($result);
                    break;
                    break;
                }
                else{
                    message_and_code("Incorrect pass", 400);
                }
                break;
            }
            else{
                message_and_code("User does not exist",400);
                break;
            }    
            break;
        }
        else if($input['action']==3){
        	// curl --header "Content-Type: application/json" --request POST --data '{"action":3,"email":"khush@gmail.com","number":"9999999999","token":"SHg6ya4By0woKv6j","pass":"password","gender":"male","age":20,"location":"Varanasi","fullname":"Puro"}' https://purohitji.azurewebsites.net/user.php
            // echo "update info";
         //   curl --header "Content-Type: application/json" --request POST --data '{"action":3,"email":"testupdate@gmail.com","token":"xJSCl7WNvdVM9cfb","number":"9999999999","pass":"password","gender":"male","age":20,"location":"Varanasi","fullname":"Khush Chopra"}' https://purohitji.azurewebsites.net/user.php
			
			$tsql1 = "select * from users where email='".$input['email']."'";
	
            $getResults= mysqli_query($conn, $tsql1);
            if($row = mysqli_fetch_array($getResults, mysqli_fetch_assoc)){
                // user exists
            	$tsql2 = "select * from users where email='".$input['email']."' and token='".hash('sha256', $input['token'])."'";
            	$getResults2= mysqli_query($conn, $tsql2);
            	if($row = mysqli_fetch_array($getResults2, mysqli_fetch_assoc)){
            		// token correct
            		$tokenquery = "";
            		if($input['pass']!=""){
            			$tokenquery = "update users set number='".$input['number']."' , pass='".hash('sha256', $input['pass'])."' , gender='".$input['gender']."' , age=".$input['age']." , location='".$input['location']."' , fullname='".$input['fullname']."' where email='".$input['email']."'";
            		}
            		else{
            			$tokenquery = "update users set number='".$input['number']."' , gender='".$input['gender']."' , age=".$input['age']." , location='".$input['location']."' , fullname='".$input['fullname']."' where email='".$input['email']."'";
            		}
            		
	                $tokenqueryResult= mysqli_query($conn, $tokenquery);
	                if($tokenqueryResult==FALSE){
	                    message_and_code("Server error",500);
	                    break;
	                }
	                message_and_code("Successfully changed", 200);
	                break;
            	}
            	else{
            		// incorrect token
            		message_and_code("Not Authenticated", 401);
            		break;
            	}
                
                
            }
            else{
                message_and_code("User does not exist",400);
                break;
            }    
            break;
        }
}
mysqli_close($conn);
?>