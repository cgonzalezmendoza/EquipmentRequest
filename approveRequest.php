<?php
	if(isset($_POST['firstName'])){
		$first_name = $_POST['firstName'];
		echo "Thank you " . $first_name . "for your submission.";
	}
	else{
		echo "Failed!";
		$first_name = $_POST['firstName'];
		echo "Thank you " . $first_name . "for your submission.";

	}
?>
