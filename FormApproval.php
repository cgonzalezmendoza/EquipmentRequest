<!DOCTYPE html >
<html>
<head>
<title>QA Lab Equipment Request  Approval </title> 
 <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <style>

	.margin-buffer{
		 margin-top: 20px;
		margin-bottom: 20px;
	}
    /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
    hr{ border-color: black; }
    .titleText{ text-align: center;}
    .navbar{background-color:#990000;}
    .navbar-header h1{
    	font-family: Times;
    	color:white;
    }
  
    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
      text-align: center;
    }
  
    /* Set black background color, white text and some padding */
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: 100%;} 
    }

  /* Style the buttons that are used to open and close the accordion panel */
button.accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    text-align: left;
    border: none;
    outline: none;
    transition: 0.4s;
}

/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
button.accordion.active, button.accordion:hover {
    background-color: #ddd;
}

/* Style the accordion panel. Note: hidden by default */
div.panel {
    padding: 0 18px;
    background-color: white;
    display: none;
} 

  </style>
</head>
<body>
  <?php
   date_default_timezone_set("America/Chicago");
   
   //Setting up the database
  $mysqli = new mysqli('localhost','public','publicpassword','equipmentloandb');
  
  if($mysqli->connect_errno){
          printf("Connection to database failed!: %s\n", $mysqli->connect_error);
          exit;
  }

	//Creating an array of all of the pending requests
	$requestIDArr = array();
	 $stmt = $mysqli->prepare("select id, user_id from Request where granted is null");
         if(!$stmt){
                 printf("Query Prep to get null requests Failed: %s\n", $mysqli->error);
                 exit;
         }

         $stmt->execute();
         $stmt->bind_result($RequestId, $UserId);
  
         while($stmt->fetch()){
		array_push($requestIDArr, array($UserId, $RequestId));	
	}
         $stmt->close();

function getUserHTML($UserId, $mysqli){
	
	$stmt = $mysqli-> prepare("select FirstName, LastName, Email, Phone from User where id = ?");
	if(!$stmt){
		printf("Query to get User info failed: %s\n", $mysqli->error);
		exit;
	}
	
	$stmt->bind_param('i',$UserId);
	$stmt->execute();
	$stmt->bind_result($firstName,$lastName,$email,$phone);
	
	while($stmt->fetch()){
	$UserColumnHTML = "<div class=\"col-md-4\">
		First Name: {$firstName} <br>
		Last Name: {$lastName} <br>
		Email: {$email} <br>
		Phone: {$phone} <br>
		</div>";
	}
	$stmt->close();
	return $UserColumnHTML;
}

function getDevicesHTML($RequestId,$mysqli){
	$stmt = $mysqli-> prepare("select Type, OS, Setup, Peripherals from DeviceRequestInfo where RequestId = ?");
	if(!$stmt){
		printf("Query to get Request info failed: %s\n", $mysqli->error);
		exit;
	}
//	printf("Device id is: %s\n",$RequestId);
	$stmt->bind_param('i',$RequestId);
	$stmt->execute();
	$stmt->bind_result($type,$os,$setup,$peripherals);
	while($stmt->fetch()){
	if(is_null($setup)){
		$setup = "None.";
	}
	if(is_null($peripherals)){
		$peripherals = "None.";
	}	
	
	$deviceHTML= "<div class=\"col-md-4\">
		Type: {$type} <br>
		OS: {$os} <br>
		Setup: {$setup} <br>
		Peripherals: {$peripherals} <br>
		</div>";
	}
	$stmt->close();
	return $deviceHTML;
}

function NonNull(&$var){
	if(is_null($var)) {
		$var = "N/A";
	}
}

function getRentalHTML($RequestId, $mysqli){
	$stmt = $mysqli-> prepare("select checkoutDate,returnDate,pickupPerson,pickupLocation,DateGenerated from Request where id = ?");
	if(!$stmt){
		printf("Query to get Rental info failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('i',$RequestId);
	$stmt->execute();
	$stmt->bind_result($checkoutDate,$returnDate,$pickupPerson,$pickupLocation,$dateGenerated);
	
	while($stmt->fetch()){
		NonNull($pickupPerson);
		NonNull($pickupLocation);
	
	$rentalColumnHTML = "<div class=\"col-md-4\">
		Checkout Date: {$checkoutDate} <br>
		Return Date: {$returnDate} <br>
		Pickup Person: {$pickupPerson} <br>
		Pickup Location: {$pickupLocation} <br>
		Date Submitted: {$dateGenerated} <br>
		</div>";
	}
	$stmt->close();
	return $rentalColumnHTML;
}


//Building each row of our display:

$RequestRow = "";
foreach ($requestIDArr as $key => $value) {
    //echo "key is: {$key}\n";
//	print($value[0]);
	$UserHTML = getUserHTML($value[0], $mysqli);
	$DeviceHTML = getDevicesHTML($value[1],$mysqli);
	$RentalHTML = getRentalHTML($value[1],$mysqli);
//	$RequestRow .= "<div class = \"row margin-buffer\">".$UserHTML.$DeviceHTML.$RentalHTML."</div>";
	$RequestRow .= "<button class=\"accordion\">".$UserHTML.$DeviceHTML.$RentalHTML."</button> <div class=\"panel\">
  <p>Lorem ipsum...</p>
</div>";





    //print_r($arr);
}


	//Building our Request String:
	$UserColHTML = "";
	//Getting all the pending requests:
 $stmt = $mysqli->prepare("select FirstName, LastName, Email, Phone from User join Request on User.id = Request.user_id where Request.granted is null");
         if(!$stmt){
                 printf("Query Prep to get Requests Failed: %s\n", $mysqli->error);
                 exit;
         }

         $stmt->execute();
         $stmt->bind_result($firstName, $lastName,$email,$phone);
  
         while($stmt->fetch()){
	$UserColHTML .= "<div class = \" row\"> 
			<div class=\"col-md-4\">
		First Name: {$firstName} <br>
		Last Name: {$lastName} <br>
		Email: {$email} <br>
		Phone: {$phone} <br>
		</div>";
	}
	$UserColHTML .= "</div>";
         $stmt->close();

	?>


<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <h1> QA Lab Equipment Request</h1>
    </div>
  </div>
</nav>
 <div class="container">
      <!-- Example row of columns -->
      <div class="row">

	<div class="col-md-4">
		<h2> Requester Info </h2>

	</div>

	<div class="col-md-4">
		<h2> Devices Requested </h2>
	</div>

	<div class="col-md-4">
		<h2> Rental Information </h2>
	</div>

      </div>

	<?php
		echo $RequestRow ; 
	?>	
<footer class="container-fluid">
</footer>



</body>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<script>
	$(document).ready(function(){
      var date_input=$('input[name="dateNeeded"]'); //our date input has the name "date"
      var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options={
        format: 'mm/dd/yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
      };
      date_input.datepicker(options);

	var dateReturned=$('input[name="dateReturned"]'); 
        dateReturned.datepicker(options);



    })
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function(){
        /* Toggle between adding and removing the "active" class,
        to highlight the button that controls the panel */
        this.classList.toggle("active");

        /* Toggle between hiding and showing the active panel */
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    }
} 

//    function unhide(divID, otherDivId) {
 //   var item = document.getElementById(divID);
  //  if (item) {
   //         item.className=(item.className=='hidden')?'unhidden':'hidden';
    //    }
     //   document.getElementById(otherDivId).className = 'hidden';
   // }
</script>
</html>
