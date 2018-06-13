<?php
//This script is to be run once a year to reset the user's memberships to be invalid...
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

//Ensure that today is January 1st AND that the script has not been run...
$time = date("Y-m-d h:i:s");
$today = date("Y-m-d");
//echo $time;
$year = date("Y");
//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);
//We check to make sure this was not run yet today...
$stmt = $pdo->prepare('SELECT COUNT(Name) AS Result FROM Scripts WHERE Name = ? AND LastRun=?');
$stmt->execute(['membershipExpiration',$time]);
$rowTotal = $stmt->fetch();
$num = $rowTotal['Result'];
//echo $num;
if ($today == $year.'-01-01' && $num == 0){
	//Create an entry saying it was run...
	$stmt = $pdo->prepare('INSERT INTO Scripts (Name, LastRun) VALUES(?,?)');
	$stmt->execute(['membershipExpiration',$time]);
	//Now make all memberships inactive...
	$stmt2 = $pdo->prepare('UPDATE User SET Active=? WHERE Active=?');
	$stmt2->execute(['n','y']);
}
?>
