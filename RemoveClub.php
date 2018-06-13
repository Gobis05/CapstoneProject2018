<?php
  session_start();
  //Information given from the form
  $clubId = $_POST["club"];

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
		
		$adminQry = $pdo->prepare('DELETE FROM Club WHERE ClubId=?');
		$adminQry->execute([$clubId]);

	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/Admins.php");
 ?>
