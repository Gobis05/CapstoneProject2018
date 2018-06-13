<?php
	session_start();
	//Information given from the form
	$clubId = $_POST["club"];
	$email = $_POST["user"];
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
	//Make sure the user isn't already an admin of the club through the following query:
	$adminQry = $pdo->prepare('SELECT Email, ClubId FROM Admins WHERE Email = ? AND ClubId = ?');
	$adminQry->execute([$email, $clubId]);
	$count = $adminQry->rowCount();
	//If there is no row count then they are not a member, so make them one!
	if ($count == 0){		
		//Performs the insertQuery operation into Admins table.
		$insertQuery = $pdo->prepare('INSERT INTO Admins (Email, ClubId) VALUES (?,?)');
		$insertQuery->execute([$email, $clubId]);
		echo "Redirecting. Successfully added the administrator to the desired club! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Admins.php';\",3000);</script>";
	} else {
		echo "Redirecting back to previous page. This user is already an admin of the desired club! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Admins.php';\",3000);</script>";
	}

	
 ?>
