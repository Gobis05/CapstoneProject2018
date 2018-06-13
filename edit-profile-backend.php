<?php
   session_start();
   //Check if the user is already verified. If not, then checks credentials.
   if(!isset($_SESSION['valid'])){
     header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
   }
   

   
  //Information given from the form
  $email = $_POST["email"];
  $firstName = $_POST["firstName"];
  $lastName = $_POST["lastName"];
  $birthMonth = $_POST["birthMonth"];
  $birthday = $_POST["birthday"];
  $birthYear = $_POST["birthYear"];
  $dateOfBirth = $birthYear."-".$birthMonth."-".$birthday;
  $address = $_POST["address"];
  $city = $_POST["city"];
	$state = $_POST["state"];
 	$zip = $_POST["zip"];
 	$areaCode = $_POST["areaCode"];
 	$firstThree = $_POST["firstThree"];
 	$lastFour = $_POST["lastFour"];
	if ($areaCode != "" and $firstThree !="" and $lastFour!=""){
		$phoneNum = $areaCode." ".$firstThree."-".$lastFour;
	} else {
		$phoneNum = "";
	}
	$spouseF = $_POST['spouseFName'];
	$spouseL = $_POST['spouseLName'];
	$marital = $_POST['marital'];
	if ($marital == 's'){
		$spouseF = NULL;
		$spouseL = NULL;
	}
  $joinNHOHVA = $_POST['joinNHOHVA'];

	
 	$fullAddress = $address.", ".$city.", ".$state;
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
		//Creates a database object
		$pdo = new PDO($dsn, $user, $password, $opt);
		//Performs the insertQuery operation.
		$updateQuery = $pdo->prepare('UPDATE User SET Email=?, FirstName=?, LastName=?, DoB=?, Address=?, Zip=?, PhoneNum=?, SpouseFirstName=?, SpouseLastName=?, FamilyStatus=?, OptIn=? WHERE Email=?');
 		$updateQuery->execute([$email, $firstName,$lastName, $dateOfBirth, $fullAddress, $zip, $phoneNum, $spouseF, $spouseL, $marital, $joinNHOHVA, $email]);
		   if(!isset($_POST["adminSubmit"])){
				$_SESSION['valid'] = true;
				$_SESSION['email'] = $email;
				
				  if (isset($_SESSION['memID'])){
					unlink('/home/mg1021/Home/NHOHVAIdCards/'.$_SESSION['firstName'].$_SESSION['lastName'].'NHOHVAIdCard'.$_SESSION['memID'].'.png');
				}
				$_SESSION['firstName'] = $firstName;
				$_SESSION['lastName'] = $lastName;
				include ('images.php');
				header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/profile.php");
			} else {
				header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/Users.php");
			}
 ?>
