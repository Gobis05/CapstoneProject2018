<?php
//session_start();
//Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
if(!isset($_SESSION['memID'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
}
$host = "localhost";
$database = "NHOHVA";
$user = "mg1021";   $password = "goodspec";
$charset = "utf8";
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$opt = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];
//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);
//Get the user's information from the User table
$userqry = $pdo->prepare('SELECT FirstName, LastName, SpouseFirstName, SpouseLastName, DoB, Address, Email, Zip FROM User WHERE Email = ?');
$userqry->execute([$_SESSION['email']]);
$row = $userqry->fetch();

//Get the club names that the user is a member of
$clubList = "";
$userqry2 = $pdo->prepare('SELECT ClubId FROM Membership WHERE Email=?');
$userqry2->execute([$_SESSION['email']]);
while($row2 = $userqry2->fetch()){
	$clubqry = $pdo->prepare('SELECT ClubName FROM Club WHERE ClubId = ?');
	$clubqry->execute([$row2['ClubId']]);
	$club = $clubqry->fetch();
	$clubList = $clubList.", ".$club['ClubName'];
}
$clubList = substr($clubList, 2);
//Get their membership for an expiration date and registration date. 
$userqry3 = $pdo->prepare('SELECT * FROM Membership WHERE NHOHVAId = ? LIMIT 1');
$userqry3->execute([$_SESSION['memID']]);
$row3 = $userqry3->fetch();

if($row['SpouseFirstName'] == NULL){
	$name = $row['FirstName']." ".$row['LastName'];
} else {
	$name = $row['FirstName']." ".$row['LastName']." or ".$row['SpouseFirstName']." ".$row['SpouseLastName'];
}

$id = $_SESSION['memID'];
$email = $row['Email'];
$address = $row['Address']." ".$row['Zip'];
$expirationDate = date("d/m/Y", strtotime($row3['ExpireDate']));
$issueDate = date("m/d/Y", strtotime($row3['RegistrationDate']));

$background = imagecreatefromjpeg("img/nhohvaBackgroundCard.jpg");
$img = imagecreatetruecolor(100, 100);
$white = imagecolorexact($img, 255, 255, 255);

$font = "OpenSans-Regular.ttf";
$font_size = 14;
$angle = 0;

$width = imagesx($background);

// Get center coordinates of image
$centerX = $width / 2;

//Centering of $name
list($left, $bottom, $right, , , $top) = imageftbbox($font_size, $angle, $font, $name);
$left_offset = ($right-$left) / 2;
$x = $centerX - $left_offset;
imagefttext($background,$font_size,$angle,$x,70,1,$font,$name);

//Centering of $address
list($left, $bottom, $right, , , $top) = imageftbbox($font_size, $angle, $font, $address);
$left_offset = ($right-$left) / 2;
$x = $centerX - $left_offset;
imagefttext($background,$font_size,$angle,$x,100,1,$font,$address);

//Centering of $primaryClub
$wrap = wordwrap ($clubList, $width = 64, "\n");
list($left, $bottom, $right, , , $top) = imageftbbox(10, $angle, $font, $wrap);
$left_offset = ($right-$left) / 2;
$x = $centerX - $left_offset;
imagefttext($background,10,$angle,$x,125,1,$font,$wrap);

$year = date("Y");
$nextYear = $year + 1;
//Static placements: id ,issue date, expiration date
imagefttext($background,10,$angle,58,38,1,$font,$issueDate);
imagefttext($background,10,$angle,10,18,1,$font,$year."-".$nextYear." OHRV Season");
imagefttext($background,10,$angle,304,19,1,$font,$expirationDate);
imagefttext($background,10,$angle,338,36,1,$font,$id);

$fileName=$_SESSION['firstName'].$_SESSION['lastName']."NHOHVAIdCard".$_SESSION['memID'];
header ("Content-type: image/png");
imagepng($background, "/home/mg1021/Home/NHOHVAIdCards/".$fileName.".png");
?>
