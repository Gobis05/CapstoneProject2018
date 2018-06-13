<?php
 session_start();
 //Check if the user is already verified. If not, then checks credentials.
 if(!isset($_SESSION['valid'])){
   header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
 }
if(!isset($_POST['club'])){
 header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/pick-club.php");
}

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

$pdo = new PDO($dsn, $user, $password, $opt);

$clubIds = array();

//Get the club IDs they are not a member of and that are not in the cart.
$activeqry = $pdo->prepare('SELECT Active FROM User WHERE Email = ?');
$activeqry->execute([$_SESSION['email']]);
$active = $activeqry->fetch();
if($active['Active'] == 'y'){
	$cartqry = $pdo->prepare('SELECT ClubId FROM Club WHERE ClubId NOT IN (SELECT ClubId FROM Membership where NHOHVAId = ?) AND ClubId NOT IN (SELECT ClubId FROM Cart where Email = ?)');
	$cartqry->execute([$_SESSION['memID'], $_SESSION['email']]);
	//for each clubId they are not a member of or is not in the cart, put them into an array
	while($row = $cartqry->fetch()){
		$clubId = $row['ClubId'];
		//echo $clubId.', ';
		array_push($clubIds, $clubId);
	}
} else {
	$cartqry = $pdo->prepare('SELECT ClubId FROM Club WHERE ClubId NOT IN (SELECT ClubId FROM Cart where Email = ?)');
	$cartqry->execute([$_SESSION['email']]);
	//for each clubId they are not a member of or is not in the cart, put them into an array
	while($row = $cartqry->fetch()){
		$clubId = $row['ClubId'];
		//echo $clubId.', ';
		if ($clubId != 0){
			array_push($clubIds, $clubId);
		}
	}
}

//Ensure the $clubIds has at least 1 element in it... If it doesn't reload the page
if (sizeof($clubIds) == 0){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/pick-club.php");
}

//Pick a random Id from that array
$randomClub = $clubIds[array_rand($clubIds)];
//echo $randomClub;
if ($randomClub != NULL){
  /*Creates a database object*/
  $pdo = new PDO($dsn, $user, $password, $opt);
  
  //Get the Fee type:
  $feeType = $pdo->prepare('Select * FROM Fee WHERE ClubId=?');
  $feeType->execute([$randomClub]);
  $feeFetch = $feeType->fetch();
  if($feeFetch['Flat'] != 0){
	  $memType = "Flat";
  } else {
	  $memType = "Single";
  }
  //We add the random club to the cart  
  $cartInsert = $pdo->prepare('INSERT INTO Cart (Email, ClubId, MemType) VALUES (?,?,?)');
  $cartInsert->execute([$_SESSION['email'], $randomClub, $memType]);

  $feeQuery = $pdo->prepare('SELECT '.$memType.' FROM Fee WHERE ClubId = ?');
  $feeQuery->execute([$randomClub]);
  $row = $feeQuery->fetch();

  $addedFee = $row[$memType];
  //echo $addedFee.", ";

  $currentDueQuery = $pdo->prepare('SELECT AmountDue FROM User WHERE Email = ?');
  $currentDueQuery->execute([$_SESSION['email']]);
  $due = $currentDueQuery->fetch();

  $amountDue = $due['AmountDue'];
  $amountDue += $addedFee;
  //echo $amountDue.", ";

  $updateUser = $pdo->prepare('UPDATE User SET AmountDue = ? WHERE Email = ?');
  $updateUser->execute([$amountDue, $_SESSION['email']]);


  //We create session variables for use in the cart
  if(isset($_SESSION['Clubs Selected'])){
    $_SESSION['Clubs Selected'] += 1;
    $_SESSION['Cost'] += $addedFee;
  }
  else{
    $_SESSION['Clubs Selected'] = 1;
    $_SESSION['Cost'] = $addedFee;
  }
}

 header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/cart.php");
?>
