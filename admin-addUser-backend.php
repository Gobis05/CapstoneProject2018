<?php
session_start();
//Information given from the form
$email = $_POST["email"];
$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$marital = $_POST["marital"];
if ($marital == 's'){
	$spouseF = NULL;
	$spouseL = NULL;
	$memType = "Single";
} else {
	$spouseF = $_POST['spouseFName'];
	$spouseL = $_POST['spouseLName'];
	$memType = "Family";
}
$birthMonth = $_POST["birthMonth"];
$birthday = $_POST["birthday"];
if($birthday == "- Day -"){
	$birthday = 1;
}
$birthYear = $_POST["birthYear"];
$dateOfBirth = $birthYear."-".$birthMonth."-".$birthday;
$address = $_POST["address"];
$city = $_POST["city"];
$state = $_POST["state"];
$zip = $_POST["zip"];
$fullAddress = $address.", ".$city.", ".$state;
$areaCode = $_POST["areaCode"];
$firstThree = $_POST["firstThree"];
$lastFour = $_POST["lastFour"];
if ($areaCode != "" and $firstThree !="" and $lastFour!=""){
	$phoneNum = $areaCode." ".$firstThree."-".$lastFour;
} else {
	$phoneNum = "";
}
$joinNHOHVA = $_POST['joinNHOHVA'];
$clubId = $_POST["club"];
  
//Get the current date
$time = getDate();
$currentYear = $time["year"];
$currentMonth = $time["mon"];
$currentDay = $time["mday"];
//Verify that the user isn't too young
if($currentYear - $birthYear < 18){
	$_SESSION['validBirthday'] = false;
	//echo("bad year");
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/AddUser.php");
} else if($currentYear - $birthYear == 18 && $currentMonth - $birthMonth < 0){
	$_SESSION['validBirthday'] = false;
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/AddUser.php");
} else if($currentYear - $birthYear == 18 && $currentMonth - $birthMonth == 0 && $currentDay - $birthday < 0){
	$_SESSION['validBirthday'] = false;
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/AddUser.php");
} else {
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
	//Decides the membership type based on whether or not a FlatFee exists for the club chosen.
	$memTypeQry = $pdo->query('SELECT Flat, Single, Family FROM Fee WHERE ClubId = ?');
	$memTypeQry->execute([$clubId]);
	$mem = $memTypeQry->fetch();
	while ($mem = $memTypeQry->fetch()){
		if($mem['Flat'] != 0){
			$memType = "Flat";
		}
	}
	//Checks to see if the email is already taken
	$emailQuery = $pdo->query('SELECT Email FROM User');
	while(($row = $emailQuery->fetch())){
		//echo("found: ".$row['Email'].'. Checking to see if it is like '.$email);
		if($row['Email'] == $email){
			$_SESSION['validEmail'] = false;
			header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php");
		}
	}
	
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
		  $insertQuery->execute([$random, $email, $today, $expires, $clubId, $memType]);
		  $searching = false;
		}
	}
	//Performs the insertQuery operation.
	$insertQuery = $pdo->prepare('INSERT INTO User (Email, FirstName, LastName, SpouseFirstName, SpouseLastName, DoB, Address, Zip, OptIn, PhoneNum) VALUES (?,?,?,?,?,?,?,?,?,?)');
	$insertQuery->execute([$email, $firstName, $lastName, $spouseF, $spouseL, $dateOfBirth, $fullAddress, $zip, $joinNHOHVA, $phoneNum]);
}
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
?>
