<!DOCTYPE html >
<html>
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
 <title>QA Lab Equipment Request  Approval </title> 
  <style>
html,body{
	height:100%;
}
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

/* Style the accordion panel. Note: hidden by default */
div.panel {
    padding: 0 18px;
    background-color: white;
    display: none;
} 
.panel {
    background: #4E9CAF;
    border-radius: 5px;
    font-weight: bold;
}
  </style>
</head>
<body>
  <?php
   date_default_timezone_set("America/Chicago");
  
//Including the request class:
require_once('Request.php');


//if(isset($_POST['submit'])){
//	$input = $_POST['firstName'];
//	echo "Success! you input:" .$input;
//}


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

	//Creating an aray of all of the available devices.
	$availableDevices = array();
	$stmt = $mysqli->prepare("select Type,Model,Name,OS,ID from Device where Available is null");
         if(!$stmt){
                 printf("Query Prep to get available deviced failed: %s\n", $mysqli->error);
                 exit;
         }

         $stmt->execute();
         $stmt->bind_result($deviceType, $deviceModel,$deviceName,$deviceOS,$deviceID);
  
         while($stmt->fetch()){
		 $device = new stdClass();
		 $device->type = $deviceType;
		 $device->model = $deviceModel;
		 $device->name = $deviceName;
		 $device->os = $deviceOS;
		 $device->id = $deviceID;
		array_push($availableDevices, $device);	
	}
         $stmt->close();



function getUserHTML($UserId, $mysqli, &$request){
	
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
		$request->setUserInfo($firstName,$lastName,$email,$phone);
	}
	$stmt->close();
	return $UserColumnHTML;
}

function getDevicesHTML($RequestId,$mysqli,&$request){
	$stmt = $mysqli-> prepare("select Type, OS, Setup, Peripherals from DeviceRequestInfo where RequestId = ?");
	if(!$stmt){
		printf("Query to get Request info failed: %s\n", $mysqli->error);
		exit;
	}
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
		$request->setDevices($type,$os,$setup,$peripherals);
		
	}
	$stmt->close();
	return $deviceHTML;
}

function NonNull(&$var){
	if(is_null($var)) {
		$var = "N/A";
	}
}


function getRequestHTML($RequestId, $mysqli,&$request){
	$stmt = $mysqli-> prepare("select checkoutDate,returnDate,pickupPerson,pickupLocation,DateGenerated from Request where id = ?");
	if(!$stmt){
		printf("Query to get Request info failed: %s\n", $mysqli->error);
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
		$request->setRequestInfo($checkoutDate,$returnDate,$pickupPerson,$pickupLocation,$dateGenerated);
	}
	$stmt->close();
	return $rentalColumnHTML;
}


//Building each row of our display:
$RequestRow = "";
$deviceSelect = "";

foreach($availableDevices as $device){
	$deviceSelect .= "<option value=\"{$device->id}\" deviceType=\"{$device->type}\" deviceModel=\"{$device->model}\" deviceName=\"{$device->name}\" deviceOS=\"{$device->os}\">{$device->model} </option>";	
}


foreach ($requestIDArr as $key => $value) {
	
	//Creating the Request object to store all of the information about the request.
	$request = new Request($value[0],$value[1]);
	//Getting all the data and HTML to populate the request rows;
	$UserHTML = getUserHTML($value[0], $mysqli,$request);
	$DeviceHTML = getDevicesHTML($value[1],$mysqli,$request);
	$RequestHTML = getRequestHTML($value[1],$mysqli,$request);
	$RequestRow .= "<a data-toggle=\"modal\" href=\"#{$value[1]}\" data-target=\"#bannerformmodal{$request->requestId}\">".$UserHTML.$DeviceHTML.$RequestHTML."</a>
	<div class=\"modal fade bannerformmodal{$request->requestId}\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"bannerformmodal{$request->requestId}\" aria-hidden=\"true\" id=\"bannerformmodal{$request->requestId}\">
		<div class=\"modal-dialog modal-lg\">
          		<div class=\"modal-content\">
                		<div class=\"modal-header\">
                			<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
                			<h2 class=\"modal-title\" id=\"myModalLabel\">Approve Request</h2>
                		</div>
                		
				<div class=\"modal-body\">
					<form formNumber=\"{$request->requestId}\" id=\"approve_form{$request->requestId}\" action = \"\" ajaxtarget=\"approveRequest.php\"class=\"form-horizontal approve_form\" method=\"POST\">
					<input type=\"hidden\" name=\"userId\"  value=\"{$request->userId}\" id=\"userId\" />
					<input type=\"hidden\" name=\"requestId\"  value=\"$request->requestId\" id=\"requestId\" />
					<div class=\"form-group form-group-sm\">
						<!-- Left Column -->
						<div class=\"col-sm-6\">
							<p class=\"lead\"> User Information</p>

							<div class=\"form-group\">
								<label for=\"firstName\" class=\"col-sm-3 control-label bg\">First Name</label>
									<div class=\"col-sm-7\">
									<input name=\"firstName\" class=\"form-control\" id =\"firstName\" value=\"{$request->firstName}\" type=\"text\">
									</div>
							</div>	
							<div class=\"form-group\">
								<label for=\"lastName\" class=\"col-sm-3 control-label bg\">Last Name</label>
									<div class=\"col-sm-7\">
									<input name=\"lastName\" class=\"form-control\" id =\"lastName\" value=\"{$request->lastName}\" type=\"text\">
									</div>
							</div>
							<div class=\"form-group\">
								<label for=\"email\" class=\"col-sm-3 control-label bg\">Email</label>
									<div class=\"col-sm-7\">
									<input name=\"email\" class=\"form-control\" id =\"email\" value=\"{$request->email}\" type=\"text\">
									</div>
							</div>
							<div class=\"form-group\">
								<label for=\"phone\" class=\"col-sm-3 control-label bg\">Phone</label>
									<div class=\"col-sm-7\">
									<input name=\"phone\" class=\"form-control\" id =\"phone\" value=\"{$request->phone}\" type=\"text\">
									</div>
							</div>	
							<p class=\"lead\"> Request Information</p>
							<div class=\"form-group\">
								<label for=\"checkoutDate\" class=\"col-sm-3 control-label bg\">Checkout Date</label>
									<div class=\"col-sm-7\">
										<input name=\"checkoutDate\" class=\"form-control\" id =\"checkoutDate\" value=\"{$request->checkoutDate}\" type=\"text\">
									</div>
							</div>	
							<div class=\"form-group\">
								<label for=\"returnDate\" class=\"col-sm-3 control-label bg\">Return Date</label>
									<div class=\"col-sm-7\">
										<input name=\"returnDate\" class=\"form-control\" id =\"returnDate\" value=\"{$request->returnDate}\" type=\"text\">
									</div>
							</div>
							<div class=\"form-group\">
								<label for=\"pickupPerson\" class=\"col-sm-3 control-label bg\">Pickup Person</label>
									<div class=\"col-sm-7\">
										<input name=\"pickupPerson\" class=\"form-control\" id =\"pickupPerson\" value=\"{$request->pickupPerson}\" type=\"text\">
									</div>
							</div>	
							<div class=\"form-group\">
								<label for=\"pickupLocation\" class=\"col-sm-3 control-label bg\">Pickup Location</label>
									<div class=\"col-sm-7\">
										<input name=\"pickupLocation\" class=\"form-control\" id =\"pickupLocation\" value=\"{$request->pickupLocation}\" type=\"text\">
									</div>
							</div>	
							<div class=\"form-group\">
								<label for=\"dateGenerated\" class=\"col-sm-3 control-label bg\">Date Generated</label>
									<div class=\"col-sm-7\">
										<input name=\"dateGenerated\" class=\"form-control\" id =\"dateGenerated\" value=\"{$request->dateGenerated}\" type=\"text\">
									</div>
							</div>	

						</div>

						<!-- Right Column -->
						<div class=\"col-sm-6\">
							<p class=\"lead\"> Devices Requested</p>
							<div class=\"form-group\">
								<label for=\"type\" class=\"col-sm-3 control-label bg\">Type</label>
									<div class=\"col-sm-7\">
										<input name=\"deviceType\" class=\"form-control\" id =\"type\" value=\"{$request->type}\" type=\"text\">
									</div>
							</div>	
							<div class=\"form-group\">
								<label for=\"os\" class=\"col-sm-3 control-label bg\">OS</label>
									<div class=\"col-sm-7\">
										<input name=\"os\" class=\"form-control\" id =\"os\" value=\"{$request->os}\" type=\"text\">
									</div>
							</div>
							<div class=\"form-group\">
								<label for=\"setup\" class=\"col-sm-3 control-label bg\">Setup</label>
									<div class=\"col-sm-7\">
										<input name=\"setup\" class=\"form-control\" id =\"setup\" value=\"{$request->setup}\" type=\"text\">
									</div>
							</div>	
							<div class=\"form-group\">
								<label for=\"peripherals\" class=\"col-sm-3 control-label bg\">Peripherals</label>
									<div class=\"col-sm-7\">
										<input class=\"form-control\" id =\"peripherals\" value=\"{$request->peripherals}\" type=\"text\">
									</div>
</div>
							<p class=\"lead\"> Select Device </p>
							<div class=\"form-group\">
								<label for=\"device\" class=\"col-sm-3 control-label bg-danger\">Device</label>
									<div class=\"col-sm-7\">
									<select name=\"device\" class=\"form-control deviceDrop\">
										{$deviceSelect}
									</select>

									</div>
							</div>
							<div id=\"update{$request->requestId}\"> </div>
						</div>
					</div>
					</form>
							
                           	<div class=\"modal-footer\">
              		  		<button type=\"button\" formNumber=\"{$request->requestId}\" class=\"btn btn-default submitForm\">Submit</button>
              			</div>          
        		</div>
        	</div>
	</div>
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
		<h2> User </h2>

	</div>

	<div class="col-md-4">
		<h2> Devices Requested </h2>
	</div>

	<div class="col-md-4">
		<h2> Request Information </h2>
	</div>

      </div>

	<?php
		echo $RequestRow ; 
	?>	

</div>

<div class="footer navbar-fixed-bottom">
<footer class="container-fluid">
</footer>
</div>


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
	$('.deviceDrop').change(function(event) {
		var requestId =$(this).parents('form').find('#requestId').val(); 
		var option = $('option:selected',this);
		$('#update'+requestId).html('<div class="col-sm-3 control-label"> Model: </div>' +
			'<div class="col-sm-7 control-label ">' + option.attr("deviceModel") +'</div>' +
			'<div class="col-sm-3 control-label"> Type:  </div>' +
			'<div class="col-sm-7 control-label ">' + option.attr("deviceType") +'</div>' +
			'<div class="col-sm-3 control-label"> Name:  </div>' +
			'<div class="col-sm-7 control-label ">' + option.attr("deviceName") +'</div>' +
			'<div class="col-sm-3 control-label"> OS:</div>' +
			'<div class="col-sm-7 control-label ">' + option.attr("deviceOS") +'</div>'		
			);
	}); 

	$(".approve_form").on("submit", function(e) {
	        var postData = $(this).serializeArray();
		var formURL = $(this).attr("ajaxtarget");
		var formNumber = $(this).attr('formNumber'); 
		
		$.ajax({
			url: formURL,
			type: "POST",
			data: postData,
			success: function(data, textStatus, jqXHR) {
				//$('#bannerformmodal'+formNumber+' .modal-header .modal-title').html("Result");
				//$('#bannerformmodal'+formNumber+' .modal-body').html(data);
				alert(data);
				window.location.reload();
				},
				error: function(jqXHR, status, error) {
					console.log(status + ": " + error);
					}
			});
		e.preventDefault();
		});
	$(".submitForm").click(function() {		
			var formNumber = $(this).attr('formNumber'); 
			$("#approve_form"+formNumber).submit();
		});
</script>
</html>
