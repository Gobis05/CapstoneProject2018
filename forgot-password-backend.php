<?php
session_start();

$email = $_POST["email"];

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
	$getMem = $pdo->prepare('SELECT Email FROM User WHERE Email = ?');
	$getMem->execute([$email]);
	if($mem = $getMem->fetch()){
		//The email exists, send them a random 6-digit number for password resetting.
		$num = mt_rand(100000,999999);
		$user = $mem['Email'];
		$time = date("Y-m-d h:i:s");
		//Put this reset code in the database for later verification...
		$updateQuery = $pdo->prepare('UPDATE User SET ResetCode=?, ResetCodeTimeStamp=? WHERE Email=?');
 		$updateQuery->execute([$num, $time, $email]);
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: NHOHVA Do Not Reply <mg1021@plymouth.edu>' . "\r\n"; //info@nhohva.org
	
		$msg = '<html><body>
			<div>
				<p>Use the following 6-digit code to reset your password:</p>
			</div>
			<div>
				<p>'.$num.'</p>
			</div>
			<div>
				<p>This code will be valid for 30 minutes.</p>
			<div>
			<div>
				Thank you,
			</div>
			<div>
				NHOHVA
			</div>';
		mail($email, 'Password Reset Code', $msg, $headers);
		echo "Thank you, you will get an email momentarily! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/password-recovery.php';\",3000);</script>";	
	} else {
		echo "Redirecting to previous page. This email does not exist! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/forgot-password.php';\",3000);</script>";	
	}
	
?>
