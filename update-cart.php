<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid']) || !isset($_SESSION['memID'])){
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
//club and membership type to remove
$club = $_POST['remove'];
$feeArr = explode('|', $club);
$clubId = $feeArr[0];
$feeType = $feeArr[1];

//Database info
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

/*Creates a database object*/
$pdo = new PDO($dsn, $user, $password, $opt);
//Get the fee type from the Cart before deleting
/*$memType = $pdo->prepare('SELECT MemType FROM Cart WHERE ClubId = ? AND Email = ? AND MemType = ?');
$memType->execute([$clubId, $_SESSION['Email'], $feeType]);
$memType = $memType->fetch();*/

//Get how much the Club fee was based on MemType
$feeQuery = $pdo->prepare('SELECT '.$feeType.' FROM Fee WHERE ClubId = ?');
$feeQuery->execute([$clubId]);
$fee = $feeQuery->fetch();
$fee2 = $fee[$feeType];

//We remove the club from the cart
$cartRemove = $pdo->prepare('DELETE FROM Cart WHERE ClubId = ? AND Email = ?');
$cartRemove->execute([$clubId, $_SESSION['email']]);

//Get current AmountDue
$currCost = $pdo->prepare('SELECT AmountDue FROM User WHERE Email=?');
$currCost->execute([$_SESSION['email']]);
$currAmount = $currCost->fetch();
$currAmt = $currAmount['AmountDue'];

$newAmountDue = $currAmt - $fee2;
//echo $newAmountDue;

//Update AmountDue from User Table
$amountDue = $pdo->prepare('UPDATE User SET AmountDue=? WHERE Email=?');
$amountDue->execute([$newAmountDue, $_SESSION['email']]);


//We create session variables for use in the cart
if(isset($_SESSION['Clubs Selected'])){
  $_SESSION['Clubs Selected'] --;
  $_SESSION['Cost'] -= $fee2;
}
else{
  $_SESSION['Clubs Selected'] = 1;
  $_SESSION['Cost'] = $fee2;
}
header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/cart.php");
?>