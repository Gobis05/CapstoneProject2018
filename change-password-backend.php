<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.html");
}
//The password received from update-password.php
$passwordUser = $_POST["password"];
$passwordConfirmation = $_POST["passwordConfirmation"];
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

  //Validate that the password was entered correctly on the server side
if($passwordUser != $passwordConfirmation){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/change-password.php");
} else {
	//Creates a database object
	$pdo = new PDO($dsn, $user, $password, $opt);
	//We get a query to get all emails, passwords, first names, and last names in the user table.
	$encryptedPassword = hash("sha256", $passwordUser);
	$stmt = $pdo->prepare('UPDATE User SET Password=? WHERE Email = ?');
	$stmt->execute([$encryptedPassword, $_SESSION['email']]);
	if(isset($_SESSION['passwordReset'])){
		$_SESSION['valid'] = false;
		$_SESSION['passwordReset'] = false;
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
	} else {
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/profile.php");
	}
}
?>
