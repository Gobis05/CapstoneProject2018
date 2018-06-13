<?php
//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
if(!isset($_SESSION['valid'])){
   header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
} else {
	$_SESSION['page'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
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
$locationqry = $pdo->query('SELECT ClubId, Longitude, Latitude FROM Club');
//An array of location data
$longitude_latitude = "";
//For each club location
while($row = $locationqry->fetch()){
  $longitude_latitude = $longitude_latitude.$row['ClubId'].'_'.$row['Longitude'].'_'.$row['Latitude'].' ';
}
//Output the data in jason format for ajax usage
echo($longitude_latitude);
?>
