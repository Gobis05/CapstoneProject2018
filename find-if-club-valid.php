<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
  echo("true");
}
else {
  //Start connection with the database
  $clubId = $_POST['id'];
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
  $clubId = $_POST['id'];
  //If they aren't yet a member, then they haven't joined any clubs, so we return true
  if(!isset($_SESSION['memID'])){
    $clubQuery2 = $pdo->prepare('SELECT * FROM Cart WHERE ClubId = ? and Email = ?');
    $clubQuery2->execute([$clubId, $_SESSION['email']]);
    if($clubQuery2->rowCount() > 0){
      echo("in cart");
    }
    else{
      echo("true");
    }
  }
  else{
    //Select the clubs they are members of
    $clubQuery = $pdo->prepare('SELECT * FROM Membership WHERE ClubId = ? and NHOHVAId = ?');
    $clubQuery->execute([$clubId, $_SESSION['memID']]);

    //Select the clubs they have in the cart
    $clubQuery2 = $pdo->prepare('SELECT * FROM Cart WHERE ClubId = ? and Email = ?');
    $clubQuery2->execute([$clubId, $_SESSION['email']]);
    //$row = $clubQuery->fetch();
    if($clubQuery2->rowCount() > 0){
      echo("in cart");
    }
    else if($clubQuery->rowCount() > 0){
      echo("false");
    }
    else{
      echo("true");
    }
  }
}
?>
