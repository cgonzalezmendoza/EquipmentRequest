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

//Inserting user info into User table


//Inserting Request into Request table
//Inserting into the Many-to-Many  DevicesRequested table 



$name = $_POST['name'];




//printf("Your name is: %s", htmlentities($name)); 
$to = "cgonzalezmendoza@wustl.edu";
$message = "Hello! This is a simple email message.";
if(mail("cgonzalezmendoza@wustl.edu","Request Form",$message)){
 	echo 'Mail succesfull';
}
else{
	echo 'Error, Mail not sent!';
}
?>


</body>
</html> 
