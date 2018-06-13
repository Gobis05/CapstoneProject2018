<?php
  session_start();
	//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable accordingly
	if(!isset($_SESSION['valid'])){
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
	}
	//Ensure the last page was payment-success.php
	/*if ($_SESSION['page'] != 'turing.plymouth.edu/~mg1021/NHOHVA/payment-success.php' ){
		//echo 'did not come from success';
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	} else {
		//echo $_SESSION['page'];
		$_SESSION['page'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}*/
  $host = "localhost";
  $database = "NHOHVA";
  $user = "mg1021";  $password = "goodspec";
  $charset = "utf8";
  $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
  $opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  $pdo = new PDO($dsn, $user, $password, $opt);
  $searching = true;
  while($searching){
    $stmt = $pdo->query('SELECT NHOHVAId FROM Membership');
    //Gets the current day for the isssue date
    $date=getdate();
  	$y = $date['year'];
  	$m = $date['mon'];
  	$d = $date['mday'];
  	$today = $y."-".$m."-".$d;
    //Get the date of issue corrected (currently is correct after being retrieved from the database, but not before))
    $_SESSION['dateOfIssue'] = $today;
    //Sets the experation date
  	$expMonth = 1;
  	$expDay=1;
	$expYear = $y +1;
	$expires = $expYear."-".$expMonth."-".$expDay;
    //Get the date of issue
    $_SESSION['expirationDate'] = $expires;
  	$y = substr( $y, -2);
	//Generate a random ID then validate it.
    $random = $y."A".strval(rand(0, 9999999));
    $viable = true;
    while ($row = $stmt->fetch()){
      $id = $row['NHOHVAId'];
      if($id == $random){
        $viable = false;
      }
    }
    if($viable){
      $insertQuery = $pdo->prepare('INSERT INTO Membership (NHOHVAId, Email, RegistrationDate, ExpireDate, ClubJoinDate) VALUES (?,?,?,?,?)');
      $insertQuery->execute([$random, $_SESSION['email'], $today, $expires, $today]);
      $searching = false;
      $_SESSION["memID"] = $random;
    }
  }

  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/club-join-backend.php");
?>
