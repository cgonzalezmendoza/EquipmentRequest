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




 echo'Request Succesfull for id:'. $userId; 
  
 ?>


