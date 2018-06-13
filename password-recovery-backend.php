<?php
session_start();

$email = $_POST["email"];
$code = $_POST["resetCode"];

//Database info
$host = "localhost";
$database = "NHOHVA";
$user = "mg1021";	$password = "goodspec";
$charset = "utf8";
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$opt = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

	//Creates a database object
	$pdo = new PDO($dsn, $user, $password, $opt);
	
	//Check if the email exists and email them
	$resetQry = $pdo->prepare('SELECT Email, ResetCode, ResetCodeTimeStamp FROM User WHERE Email = ? AND ResetCode = ?');
	$resetQry->execute([$email, $code]);
	if($mem = $resetQry->fetch()){
		$now = date("Y-m-d h:i:s");
		$now = strtotime($now);
		$timeStamp = strtotime($mem['ResetCodeTimeStamp']);
		//strtotime() returns a number in seconds
		//Make sure the code is not more than 30 minutes old... If it is, set it to null and warn the user...
		if (($now - $timeStamp) < 1800){ //1800 seconds = 30 minutes
			//The code is valid so set the ResetCode to NULL in the DB and continue on to resetting the password.
			//echo "valid";
			$updateQuery = $pdo->prepare('UPDATE User SET ResetCode=NULL, ResetCodeTimeStamp=NULL WHERE Email=?');
			$updateQuery->execute([$email]);
			$_SESSION['email'] = $email;
			$_SESSION['valid'] = true;
			$_SESSION['passwordReset'] = true;
			header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/change-password.php");
		} else {
			//The code has expired...set the ResetCode to NULL in the DB and go back to the forgot-password.php
			//echo "expired";
			$updateQuery = $pdo->prepare('UPDATE User SET ResetCode=NULL, ResetCodeTimeStamp=NULL WHERE Email=?');
			$updateQuery->execute([$email]);
			$_SESSION['valid'] = false;
			$_SESSION['passwordReset'] = false;
			echo "Your code has expired! Please request a new one. <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/forgot-password.php';\",3000);</script>";	
		}
	} else {
		echo "Incorrect code, returning to previous page. <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/password-recovery.php';\",3000);</script>";	
	}
	
?>
