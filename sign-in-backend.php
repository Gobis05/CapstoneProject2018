<?php
session_start();
/*Check if the user is already verified. If not, then checks credentials.
if(!isset($_SESSION['valid'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}*/
//info retained from sign-in
$email = $_POST['email'];
$passwordUser = $_POST["password"];
//Get the encrypted password
$encryptedPassword = hash("sha256", $passwordUser);

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

//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);

//We get a query to get all emails, passwords, first names, and last names in the user table.
$stmt = $pdo->query('SELECT Email, Password, FirstName, LastName, FamilyStatus, Active, Address, Zip, DoB FROM User');

//Whether or not the user information is verified
$unverified = true;

//Fetches the next row (in this case, just an email) and loops unti match is found or out of rows
while (($row = $stmt->fetch()) && $unverified){
	if($email == $row['Email'] && $encryptedPassword == $row['Password'] && $encryptedPassword != ""){
		$unverified = false;
		//Set the necessary session variables
		$_SESSION["firstName"] = $row["FirstName"];
		$_SESSION["lastName"] = $row["LastName"];
		$_SESSION["user"] = $email;
		$_SESSION['valid'] = true;
		$_SESSION['email'] = $row['Email'];
		$_SESSION['FamilyStatus'] = $row['FamilyStatus'];
		$_SESSION['active'] = $row['Active'];
		$_SESSION['page'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$_SESSION['address'] = $row['Address'];
		$_SESSION['zip'] = $row['Zip'];
		$_SESSION['dateOfBirth'] = $row['DoB'];
		//The session is valid, now check if they are an admin...
		$stmt2 = $pdo->prepare('SELECT Email, ClubId FROM Admins WHERE Email=? ORDER BY ClubId');
		$stmt2->execute([$_SESSION['email']]);
		$row2 = $stmt2->fetch();
		$_SESSION['admin'] = false;
		$_SESSION['superAdmin'] = false;
		if ($email == $row2['Email']){
			$_SESSION['admin'] = true;
			if ($row2['ClubId'] == 0){
				$_SESSION['superAdmin'] = true;
			}
		}
		//run the images script to create store the user's image...
		//exec('images.php');
		
	}
}

//Fetches the next row (in this case, just an email) and loops unti match is found or out of rows
if(!$unverified){
	//Check if the user is active (Their NHOHVA id is not expired)
	$stmt = $pdo->prepare('SELECT Active FROM User WHERE Email = ?');
	$stmt->execute([$_SESSION['email']]);
	$row = $stmt->fetch();
	if($row['Active'] == 'y'){
		//Get the NHOHVAId if the user has one
		$stmt = $pdo->prepare('SELECT NHOHVAId, RegistrationDate, ExpireDate FROM Membership WHERE Email = ?');
		$stmt->execute([$_SESSION['email']]);
		$row = $stmt->fetch();
		if(!is_null($row)){
			$_SESSION['memID'] = $row['NHOHVAId'];
			$_SESSION['dateOfIssue'] = $row['RegistrationDate'];
			$_SESSION['expirationDate'] = $row['ExpireDate'];
			include("images.php");
		}
	} //Get everything else

	//Initialize the cart related numbers to 0
	$_SESSION['Clubs Selected'] = 0;
	$_SESSION['Cost'] = 0;

	//Check if the user has anything left in the cart
	$cartqry = $pdo->prepare('SELECT MemType, ClubId FROM Cart WHERE Email=?');
	$cartqry->execute([$_SESSION['email']]);
	while($row = $cartqry->fetch()){
		$_SESSION['Clubs Selected'] +=1;
		$clubId = $row['ClubId'];
		$memType = $row['MemType'];
		$fee = 0;

		//Get the Fee!
		$memTyprqry = $pdo->prepare('SELECT '.$memType.' FROM Fee WHERE ClubId = ?');
		$memTyprqry->execute([$clubId]);
		$mem = $memTyprqry->fetch();
		$fee = $mem[$memType];

		$_SESSION['Cost'] += $fee;
	}
	if (isset($_SESSION['Attempted club'])){
		$clubId = $_SESSION['Attempted club'];
		$feeType = $_SESSION['Attempted Fee'];
		$pdo = new PDO($dsn, $user, $password, $opt);
		//We add the transaction to the cart
		$cartInsert = $pdo->prepare('INSERT INTO Cart (Email, ClubId, MemType) VALUES (?,?, ?)');
		$cartInsert->execute([$_SESSION['email'], $clubId, $feeType]);

		$feeQuery = $pdo->prepare('SELECT '.$feeType.' FROM Fee WHERE clubId = ?');
		$feeQuery->execute([$clubId]);
		$row = $feeQuery->fetch();

		$addedFee = $row[$feeType];
		//echo $addedFee;

		$currentDueQuery = $pdo->prepare('SELECT AmountDue FROM User WHERE Email = ?');
		$currentDueQuery->execute([$_SESSION['email']]);
		$due = $currentDueQuery->fetch();

		$amountDue = $due['AmountDue'];
		$amountDue += $addedFee;
		//echo $amountDue;

		$updateUser = $pdo->prepare('UPDATE User SET AmountDue = ? WHERE Email = ?');
		$updateUser->execute([$amountDue, $_SESSION['email']]);


		//We create session variables for use in the cart
		if(isset($_SESSION['Clubs Selected'])){
			$_SESSION['Clubs Selected'] += 1;
			$_SESSION['Cost'] += $addedFee;
		} else{
			$_SESSION['Clubs Selected'] = 1;
			$_SESSION['Cost'] = $addedFee;
		}
		unset($_SESSION['Attempted club']);
		unset($_SESSION['Attempted Fee']);
	}
	//exec("php /home/mg1021/Home/NHOHVA/images.php");
	//shell_exec("php", "/home/mg1021/Home/NHOHVA/images.php");
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
} else {
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
}
?>
