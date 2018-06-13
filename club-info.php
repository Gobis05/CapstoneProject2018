<?php
session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
if(!isset($_GET['clubName'])){
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/current-info.php");
}
$clubName = $_GET['clubName'];
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
$clubQuery = $pdo->prepare('SELECT ClubId, Address, ClubEmail, URL, MaxSize, President, PhoneNum, Logo, PresidentEmail, AllowedOHRV, Description FROM Club WHERE ClubName = ?');
$clubQuery->execute([$clubName]);
$clubInfo = $clubQuery->fetch();
$id = $clubInfo['ClubId'];
$address = $clubInfo['Address'];
$clubEmail = $clubInfo['ClubEmail'];
$url = $clubInfo['URL'];
$maxSize = $clubInfo['MaxSize'];
$president = $clubInfo['President'];
$phoneNum = $clubInfo['PhoneNum'];
$logoAddress = $clubInfo['Logo'];
$presidentEmail = $clubInfo['PresidentEmail'];
$allowedOHRV = $clubInfo['AllowedOHRV'];
$description = $clubInfo['Description'];
include('dashboard-header.html');
?>
<body>
  <?php
  if(isset($_SESSION['valid'])){
    include('navbar.php');
  }
  else{
    include('nonmember-navbar.php');
  }
  ?>
  <div class="container-fluid">
    <div class="row">
      <?php include('sidebar.php') ?>
      <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
      <div class="container">
	       <div class="row">
		         <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <div class="card hovercard">
                <div class="cardheader">

                </div>
                <div class="avatar">
                    <img alt="" src="<?=$logoAddress?>">
                </div>
                <div class="info">
                    <div class="title">
                        <a target="_blank" href="<?=$url?>">Website</a>
                    </div>
                    <div class="desc"><strong>Email:</strong> <?=$clubEmail?></div>
                    <div class="desc"><strong>Address:</strong> <?=$address?></div>
                    <div class="desc"><strong>Max Size:</strong> <?=$maxSize?>&rsquo;&rsquo;</div>
                    <div class="desc"><strong>President:</strong> <?=$president?></div>
                    <div class="desc"><strong>Phone Number:</strong> <?=$phoneNum?></div>
                    <div class="desc"><strong>President Email:</strong> <?=$presidentEmail?>&rsquo;&rsquo;</div>
                    <div class="desc"><strong>Allowed Vehicles:</strong> <?=$allowedOHRV?></div>
                    <div class="desc"><strong>Description:</strong> <?=$description?></div>
                </div>
                <div class="bottom">
                    <a class="btn btn-primary btn-twitter btn-sm" href="https://twitter.com/">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <a class="btn btn-danger btn-sm" rel="publisher"
                       href="https://plus.google.com/">
                        <i class="fa fa-google-plus"></i>
                    </a>
                    <a class="btn btn-primary btn-sm" rel="publisher"
                       href="https://plus.google.com/">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a class="btn btn-warning btn-sm" rel="publisher" href="https://plus.google.com/">
                        <i class="fa fa-behance"></i>
                    </a>
                </div>
            </div>

        </div>

	</div>
      </div>
    </div>
  </div>
</body>
