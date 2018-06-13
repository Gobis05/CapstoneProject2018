<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
//The password received from update-password.php
$passwordUser = $_POST["password"];
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
//We get a query to get all emails, passwords, first names, and last names in the user table.
$stmt = $pdo->query('SELECT Password FROM User WHERE Email = ?');
$stmt->execute([$_SESSION['email']]);
//Whether or not the user information is verified
$unverified = true;
//Fetches the next row (in this case, just an email) and loops unti match is found or out of rows
$encryptedPassword = hash("sha256", $passwordUser);
$row = $stmt->fetch();
if($encryptedPassword == $row['Password']){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/change-password.php");
} else {
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/update-password.php");
}

?>
