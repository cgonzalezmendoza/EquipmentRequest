<!DOCTYPE html>
<html>
<title> Testing output </title>
<meta charset="utf-8">
<body>
<?php

//Setting up the database
$mysqli = new mysqli('localhost','public','publicpassword','equipmentloandb');

if($mysqli->connect_errno){
	printf("Connection failed: %s\n", $mysqli->connect_error);
	exit;
}

//Checking if User exists:
function getId($firstName,$lastName,$mysqli){
	$stmt = $mysqli->prepare("select id from User where firstName = ? and lastname = ? limit 1");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
 
	$stmt->bind_param('ss',$firstName, $lastName);
	$stmt->execute();
	$stmt->bind_result($id);

	while($stmt->fetch()){	
	printf("firstname is: %s, lastname is: %s  id is: %s", $firstName, $lastName, $id);
	$stmt->close();
	return $id;
}
}

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];


$id = getId($firstName,$lastName,$mysqli);
if(is_Null($id)){
	//Inserting user info into User table if she doesnt exist.
	$stmt = $mysqli->prepare("insert into User (FirstName,LastName,Email,Phone) values (?, ?, ?, ?)");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('ssss',$firstName,$lastName,$email,$phone);
	$stmt->execute();
	$stmt->close();
	
	$id = getId($firstName,$lastName,$mysqli);
}

//Inserting Request into Request table
$device = $_POST['device'];
$OS = $_POST['os'];
$setup = $_POST['setup'];
$peripherals = $_POST['peripherals'];
 
$stmt = $mysqli->prepare("insert into DeviceRequestInfo(Type,OS,Setup,Peripherals)  values (?, ?, ?, ?)");
 if(!$stmt){
         printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
}
$stmt->bind_param('ssss',$device,$OS,$setup,$peripherals);
$stmt->execute();
$stmt->close();

//Inserting into the Rental Info Table. 
$dateNeeded = $_POST['dateNeeded'];
$dateReturned = $_POST['dateReturned'];

//printf("date needed is: %s", strtotime($dateNeeded));
//printf("date returned is: %s", strtotime($dateReturned));
//exit();


if(empty($_POST['pickUpPerson'])){
	$pickupPerson = NULL;
}
else{
	$pickupPerson = $_POST['pickUpPerson'];
}

if(empty($_POST['location'])){
	$location = NULL;
}
else{
	$location= $_POST['location'];
}
 
$stmt = $mysqli->prepare("insert into Request(checkoutDate,returnDate,user_id,pickupPerson,pickupLocation)  values (?, ?, ?, ?,?)");
if(!$stmt){
       printf("Query Prep Failed: %s\n", $mysqli->error);
       exit;
}
$stmt->bind_param('ssiss',date("Y-m-d",strtotime($dateNeeded)),date("Y-m-d",strtotime($dateReturned)),$id,$pickUpPerson,$location);
$stmt->execute();
$stmt->close();







//printf("Your name is: %s", htmlentities($name)); 
$to = "cgonzalezmendoza@wustl.edu";
$message = "Hello! This is a simple email message.";
if(mail("cgonzalezmendoza@wustl.edu","Request Form",$message)){
// 	echo 'Mail succesfull';
}
else{
	echo 'Error, Mail not sent!';
	exit();
}
echo'Request Succesfull';
?>
</body>
</html> 
