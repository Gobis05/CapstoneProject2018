<?php
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
    $random = strval(rand(0, 9));
    $viable = true;
    while ($row = $stmt->fetch()){
      $id = $row['NHOHVAId'];
      $num = preg_replace("/[^0-9]/", "", $id);
      if($num == $random){
        $viable = false;
      }
    }
    if($viable){
      echo("<p>".$random." is a viable number</p>");
      $searching = false;
    }
    else{
      echo("<p>".$random." is not a viable number</p>");
    }
  }
?>
