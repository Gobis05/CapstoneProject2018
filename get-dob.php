<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
//Start connection with the database
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
$pdo = new PDO($dsn, $user, $password, $opt);
//queries for the location data.
$dobqry = $pdo->query('SELECT DoB FROM User WHERE Email = ?');
$dobqry->execute([$_SESSION['email']]);
$row = $dobqry->fetch();
//An array of location data
//Output the data in jason format for ajax usage
echo($row['DoB']);
?>
