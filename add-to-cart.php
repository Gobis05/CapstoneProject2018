<?php
session_start();

//club chosen
if(!isset($_POST['club'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/pick-club.php");
}

$club = $_POST['club'];					//Gets the clubId and fee type from the previous page
$feeArr = explode('|', $club);			//puts them into an array
$clubId = $feeArr[0];
$feeType = $feeArr[1];

//Check if the user is already verified. If not, then set the session variables for when they create a credentials.
if(!isset($_SESSION['valid'])){
	$_SESSION['Attempted club'] = $clubId;
	$_SESSION['Attempted Fee'] = $feeType;
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/cart.php");
} else {
	//Add the item to the User's Cart
	
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

	/*Creates a database object*/
	$pdo = new PDO($dsn, $user, $password, $opt);

	//We add the club and its related info to the cart
	$cartInsert = $pdo->prepare('INSERT INTO Cart (Email, ClubId, MemType) VALUES (?,?, ?)');
	$cartInsert->execute([$_SESSION['email'], $clubId, $feeType]);

	//Update the user's AmountDue... first get the fee from the Fee table
	$feeQuery = $pdo->prepare('SELECT '.$feeType.' FROM Fee WHERE ClubId = ?');
	$feeQuery->execute([$clubId]);
	$row = $feeQuery->fetch();

	$addedFee = $row[$feeType];

	//Next, get how much they currently owe
	$currentDueQuery = $pdo->prepare('SELECT AmountDue FROM User WHERE Email = ?');
	$currentDueQuery->execute([$_SESSION['email']]);
	$due = $currentDueQuery->fetch();

	//Add how much they owe and the newly added fee
	$amountDue = $due['AmountDue'];
	$amountDue += $addedFee;

	//Update the Amount due to this new amount
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
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/pick-club.php");
}
?>
