<?php
  session_start();
  //Information given from the form
  $clubId = $_POST["club"];
  $email = $_POST["email"];

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
		
		if ($clubId == 0){
			$countqry = $pdo->prepare('SELECT * FROM Admins WHERE ClubId = 0');
			$countqry->execute();
			$count = $countqry->rowCount();
			if ($count > 1){
				$adminQry = $pdo->prepare('DELETE FROM Admins WHERE Email=? AND ClubId=?');	
				$adminQry->execute([$email, $clubId]);
				echo "Redirecting. Successfully removed this administrator! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Admins.php';\",3000);</script>";
			} else {
				echo "Redirecting. Cannot remove, must have at least one administrator for NHOHVA! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Admins.php';\",3000);</script>";
			}
		} else {
			$adminQry = $pdo->prepare('DELETE FROM Admins WHERE Email=? AND ClubId=?');
			$adminQry->execute([$email, $clubId]);
			echo "Redirecting. Successfully removed this administrator! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Admins.php';\",3000);</script>";
		}
 ?>
