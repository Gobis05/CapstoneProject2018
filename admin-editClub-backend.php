<?php
   session_start();
   //Check if the user is already verified. If not, then checks credentials.
   if(!isset($_SESSION['valid'])){
     header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
   }
   
  //Information given from the form
	$name = $_POST["clubName"];
	$id = $_POST["id"];
	$email = $_POST["email"];
	$address = $_POST["address"];
	$url = $_POST["url"];
	$maxSize = $_POST["maxSize"];
	$president = $_POST["president"];
	$phone = $_POST["phone"];
	$presEmail = $_POST["presEmail"];
	$allowed = $_POST["allowed"];
	$descr = $_POST["descr"];
	
    //Database info
		$host = "localhost";
		$database = "NHOHVA";
		$user = "mg1021";		
		$password = "goodspec";
		$charset = "utf8";
		$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
		$opt = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		//Creates a database object
		$pdo = new PDO($dsn, $user, $password, $opt);
		//Performs the insertQuery operation.
		$updateQuery = $pdo->prepare('UPDATE Club SET ClubName=?, ClubId=?, ClubEmail=?, Address=?, URL=?, MaxSize=?, President=?, PhoneNum=?, PresidentEmail=?, AllowedOHRV=?, Description=? WHERE ClubId=?');
 		$updateQuery->execute([$name, $id, $email, $address, $url, $maxSize, $president, $phone, $presEmail, $allowed, $descr, $id]);
		echo "Redirecting. Successfully updated club info! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Clubs.php';\",3000);</script>";
		//header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/Clubs.php");
 ?>
