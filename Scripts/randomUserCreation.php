<?php
//A script to add  $numUsersToMake number of random users, join them to random clubs, and create Id's

//Database info
$host = "localhost";
$database = "NHOHVA";
$user = "mg1021";		$password = "goodspec";
$charset = "utf8";
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$opt = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);

$i = 1;
$numUsersToMake = 1000;
while ($i <= $numUsersToMake){
	$email = "email".$i."@script.net";
	$firstName = "firstName".$i;
	$lastName = "lastName".$i;
	$birthMonth = "10";
	$birthday = "26";
	$birthYear = "1995";
	$dateOfBirth = $birthYear."-".$birthMonth."-".$birthday;
	$fullAddress = "5 Maple St. Hooksett NH";
	$zip = "03106";

	//Get a random ClubId...
	$clubIds = array();
	$clubQry = $pdo->prepare('SELECT ClubId FROM Club WHERE ClubId != 0');
	$clubQry->execute();
	//Put each ClubId into an array
	while($row = $clubQry->fetch()){
		$clubId = $row['ClubId'];
		array_push($clubIds, $clubId);
	}
	//Pick a random Id from that array
	$randomClub = $clubIds[array_rand($clubIds)];
	if ($randomClub != NULL){
		//Get the Fee type:
		$feeType = $pdo->prepare('Select * FROM Fee WHERE ClubId=?');
		$feeType->execute([$randomClub]);
		$feeFetch = $feeType->fetch();
		if($feeFetch['Flat'] != 0){
			$memType = "Flat";
		} else {
			$memType = "Single";
		}
	}
	
	//Get the current date
	$time = getDate();
	$currentYear = $time["year"];
	$currentMonth = $time["mon"];
	$currentDay = $time["mday"];
	
	//Generate the User's Membership information
	$searching = true;
	while($searching){
		$stmt = $pdo->query('SELECT NHOHVAId FROM Membership');
		//Gets the current day for the isssue date
		$date=getdate();
		$y = $date['year'];
		$m = $date['mon'];
		$d = $date['mday'];
		$today = $y."-".$m."-".$d;
		//Sets the experation date
		$expMonth = 1;
		$expDay=1;
		$expYear = $y +1;
		$expires = $expYear."-".$expMonth."-".$expDay;
		$y = substr( $y, -2);
		//generate the user's NHOHVA ID
		$random = $y."A".strval(rand(0, 9999999));
		$viable = true;
		//go through all existing NHOHVA IDs and if one already exists with this number then generate a new one.
		while ($row = $stmt->fetch()){
			$id = $row['NHOHVAId'];
			if($id == $random){
				$viable = false;
			}
		}
		if($viable){
		  $insertQuery = $pdo->prepare('INSERT INTO Membership (NHOHVAId, Email, RegistrationDate, ExpireDate, ClubId, MembershipType) VALUES (?,?,?,?,?,?)');
		  $insertQuery->execute([$random, $email, $today, $expires, $randomClub, $memType]);
		  $searching = false;
		}
	}
	//Performs the insertQuery operation.
	$insertQuery = $pdo->prepare('INSERT INTO User (Email, FirstName, LastName, DoB, Address, Zip, Active) VALUES (?,?,?,?,?,?,?)');
	$insertQuery->execute([$email, $firstName, $lastName, $dateOfBirth, $fullAddress, $zip, 'y']);

	$i++;
}
  


?>
