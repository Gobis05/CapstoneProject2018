<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid']) || !isset($_SESSION['memID'])){
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
if(!isset($_SESSION['checkout'])){
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/pick-club.php");
}

//Database info
$host = "localhost";
$database = "NHOHVA";
$user = "mg1021";$password = "goodspec";
$charset = "utf8";
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$opt = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];
//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);
//Get the club Id for the desired club
foreach($_SESSION['checkout'] as $clubId){
	//Get the membership type from the Cart
	$getMem = $pdo->prepare('SELECT MemType FROM Cart WHERE ClubId = ? AND Email = ?');
	$getMem->execute([$clubId, $_SESSION['email']]);
	$mem = $getMem->fetch();
	
	//Get how much the Club fee was based on MemType
	$feeQuery = $pdo->prepare('SELECT '.$mem['MemType'].' FROM Fee WHERE ClubId = ?');
	$feeQuery->execute([$clubId]);
	$fee = $feeQuery->fetch();
	$fee2 = $fee[$mem['MemType']];

	//Get current AmountDue
	$currCost = $pdo->prepare('SELECT AmountDue FROM User WHERE Email=?');
	$currCost->execute([$_SESSION['email']]);
	$currAmount = $currCost->fetch();
	$currAmt = $currAmount['AmountDue'];
	$newAmountDue = $currAmt - $fee2;
	
	//We get a query to get all user memberships
	$stmt = $pdo->prepare('SELECT ClubId FROM Membership WHERE NHOHVAId = ?');
	$stmt->execute([$_SESSION['memID']]);
	$row = $stmt->fetch();
	//If there is only a single entry without an associated club
	if($row['ClubId'] == 0){
		$update = $pdo->prepare('UPDATE Membership SET ClubId = ?, MembershipType = ? WHERE NHOHVAId = ? AND ClubId=0 AND Email = ?');
		$update->execute([$clubId, $mem['MemType'], $_SESSION['memID'], $_SESSION['email']]);
		
		//Update User Table
		$update2 = $pdo->prepare('UPDATE User SET AmountDue=?, Active=? WHERE Email=?');
		$update2->execute([$newAmountDue, 'y', $_SESSION['email']]);
	}
	else{
		//Gets the current day for the ClubJoinDate date
		$date=getdate();
		$y = $date['year'];
		$m = $date['mon'];
		$d = $date['mday'];
		$today = $y."-".$m."-".$d;		
		$insert = $pdo->prepare('INSERT INTO Membership (NHOHVAId, ClubId, Email, RegistrationDate, ExpireDate, MembershipType, ClubJoinDate) VALUES (?,?,?,?,?,?,?)');
		$insert->execute([$_SESSION['memID'], $clubId, $_SESSION['email'], $_SESSION['dateOfIssue'], $_SESSION['expirationDate'], $mem['MemType'],$today]);
		
		//Update User Table
		$update2 = $pdo->prepare('UPDATE User SET AmountDue=? WHERE Email=?');
		$update2->execute([$newAmountDue, $_SESSION['email']]);
	}
	//We remove the club from the cart
	$cartRemove = $pdo->prepare('DELETE FROM Cart WHERE ClubId = ? AND Email = ?');
	$cartRemove->execute([$clubId, $_SESSION['email']]);
	
	
}
//create the card to email to the user
	include("images.php");
	$username = $_SESSION['firstName'].$_SESSION['lastName'];
	$message = 'Hi '.$_SESSION['firstName'].',

	Thank you for joining a club! Attached to this email you will be able to see your NHOHVA Card with your NHOHVA Id!
	
Happy Riding!
NHOHVA';
	
	//Now mail it to them...
	require_once('/home/mg1021/Home/NHOHVA/PHPMailer/class.phpmailer.php');
	
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: NHOHVA Do Not Reply <mg1021@plymouth.edu>' . "\r\n"; //info@nhohva.org
	
	$email = new PHPMailer();
	$email->From      = 'mg1021@plymouth.edu';
	$email->FromName  = 'NHOHVA Do Not Reply';
	$email->Subject   = 'Club Join Confirmation';
	$email->Body      = $message;
	$email->AddAddress( $_SESSION['email'] );

	$attachment = '/home/mg1021/Home/NHOHVAIdCards/'.$username.'NHOHVAIdCard'.$_SESSION['memID'].'.png';
	//echo $attachment;

	$email->AddAttachment($attachment , 'NHOHVAIdCard.png');
	$email->Send();

$_SESSION['Clubs Selected'] = 0;
$_SESSION['Cost'] = 0;
unset($_SESSION['checkout']);
header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
?>
