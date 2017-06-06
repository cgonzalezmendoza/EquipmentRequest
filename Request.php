<?php

class Request{
	var $userId;
	var $requestId;
	var $firstName;
	var $lastName;
	var $email;
	var $phone;
	var $type;
	var $os;
	var $setup;
	var $peripherals;
	var $checkoutDate;
	var $returnDate;
	var $pickupPerson;
	var $pickupLocation;
	var $dateGenerated;

	function __construct($userId,$requestId){
		$this->userId = $userId;
		$this->requestId = $requestId;
	}

	function setUserInfo($firstName, $lastName, $email, $phone){
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->phone = $phone;
	}

	function setDevices($type,$os,$setup,$peripherals){
		$this->type = $type;
		$this->os = $os;
		$this->setup = $setup;
		$this->peripherals = $peripherals;
	}

	function setRequestInfo($checkoutDate, $returnDate, $pickupPerson, $pickupLocation, $dateGenerated){
		$this->checkoutDate = $checkoutDate;
		$this->returnDate = $returnDate;
		$this->pickupPerson = $pickupPerson;
		$this->pickupLocation = $pickupLocation;
		$this->dateGenerated = $dateGenerated;
	}


}

?>

