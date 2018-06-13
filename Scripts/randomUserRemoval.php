<?php
//A script to add  $numUsersToMake number of random users, join them to random clubs, and create Id's

//Database info
$host = "localhost";
$database = "NHOHVA";
$user = "mg1021";		$password = "goodspec";
$charset = "utf8";
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$opt = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);

$i = 1;
$numUsersToDelete = 1000;
while ($i <= $numUsersToDelete){
	$email = "email".$i."@script.net";
	
	//Performs the insertQuery operation.
	$deleteQry = $pdo->prepare('DELETE FROM User WHERE Email = ?');
	$deleteQry->execute([$email]);
	$deleteQry2 = $pdo->prepare('DELETE FROM Membership WHERE Email = ?');
	$deleteQry2->execute([$email]);

	$i++;
}
  


?>
