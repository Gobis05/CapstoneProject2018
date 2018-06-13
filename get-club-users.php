<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
//if(!isset($_SESSION['valid'])){
  //header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
//}
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
$year = date('y');
//queries for the location data.
$clubId = 10;
$emailQry = $pdo->prepare('SELECT Email FROM Membership WHERE ClubId=? AND SUBSTR(NHOHVAId, 1, 2) = '.$year);
$emailQry->execute([$clubId]);
//An array of location data
$users = "";
//For each club location
while($row = $emailQry->fetch()){
  $nameQry = $pdo->prepare('SELECT FirstName, LastName, Email FROM User WHERE Email = ?');
  $nameQry->execute([$row['Email']]);
  $name = $nameQry->fetch();
  $users = $name['FirstName'].'_'.$name['LastName'].'_'.$name['Email'].'_***';
  /*$longitude_latitude = $longitude_latitude.$row['ClubId'].'_'.$row['Longitude'].'_'.$row['Latitude'].'_'.$row['Logo'].'_'.
  $row['Address'].'_'.$row['URL'].'_'.$row['ClubEmail'].'_'.$row['PhoneNum'].'_'.$row['ClubName'].'_'.
  $fee['Single'].'_'.$fee['GoldSingle'].'_'.$fee['Family'].'_'.$fee['GoldFamily'].'_'.$fee['PlatinumSponser'].'_'.
  $fee['GoldSponser'].'_'.$fee['SilverSponser'].'_'.$fee['Business'].'_'.$fee['Flat'].'_'.'***';*/
}
//Output the data in jason format for ajax usage
echo($users);
?>
