<?php

if(!$_POST){
	echo "Could not approve request";
	exit;
}
date_default_timezone_set("America/Chicago");

//Setting up the database
$mysqli = new mysqli('localhost','Carlos','mangekyou','equipmentloandb');

if($mysqli->connect_errno){
		printf("Connection failed: %s\n", $mysqli->connect_error);
			exit;
}


//Update User
$stmt = $mysqli->prepare("update User set FirstName = ?, LastName = ?, Email = ?, Phone = ?  where id = ?");
 if(!$stmt){
	        printf("Query Prep for Updating User Failed: %s\n", $mysqli->error);
		       exit;
 }

$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$userId = $_POST["userId"];

 $stmt->bind_param('ssssi',$firstName,$lastName,$email,$phone,$userId);
 $stmt->execute();
 $stmt->close();

//Update Request
$stmt = $mysqli->prepare("update Request set checkoutDate = ?, returnDate = ?, pickupPerson = ?, pickupLocation = ?, DateGenerated = ?, granted = 1 where id = ?");
 if(!$stmt){
	        printf("Query Prep for Updating User Failed: %s\n", $mysqli->error);
		       exit;
 }

$checkoutDate = $_POST["checkoutDate"];
$returnDate = $_POST["returnDate"];
$pickupPerson = $_POST["pickupPerson"];
$pickupLocation = $_POST["pickupLocation"];
$dateGenerated = $_POST["dateGenerated"];
$requestId = $_POST["requestId"];

 $stmt->bind_param('sssssi',$checkoutDate,$returnDate,$pickupPerson,$pickupLocation, $dateGenerated, $requestId);
 $stmt->execute();
 $stmt->close();

//Match Device
$stmt = $mysqli->prepare("update Device set RequestID = ? where ID = ?");
 if(!$stmt){
	        printf("Query Prep for Updating User Failed: %s\n", $mysqli->error);
		       exit;
 }

$deviceId = $_POST["device"];

 $stmt->bind_param('ii',$requestId,$deviceId);
 $stmt->execute();
 $stmt->close();




 echo'Request Succesfull!'; 
  
 ?>


