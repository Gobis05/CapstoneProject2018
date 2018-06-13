<?php
	session_start();
	//Information given from the form
	$email = $_POST["email"];
  
	//check if the email exists!
	//function used from: https://stackoverflow.com/questions/19261987/how-to-check-if-an-email-address-is-real-or-valid-using-php
	function domain_exists($email, $record = 'MX'){
		list($user, $domain) = explode('@', $email);
		return checkdnsrr($domain, $record);
	}
	if(!domain_exists($email)) {
		echo "Redirecting. The provided email address does not exist. <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php';\",3000);</script>";
	} else {
		//Get the info from the form
		$userPassword = $_POST["password"];
		$passwordConfirmation = $_POST["passwordConfirmation"];
		$firstName = $_POST["firstName"];
		$lastName = $_POST["lastName"];
		$marital = $_POST["marital"];
		$spouseF = $_POST['spouseFName'];
		$spouseL = $_POST['spouseLName'];
		$marital = $_POST['marital'];
		//If they are single set the spouse info to NULL
		if ($marital == 's'){
			$spouseF = NULL;
			$spouseL = NULL;
		}
		$birthMonth = $_POST["birthMonth"];
		$birthday = $_POST["birthday"];
		if($birthday == "- Day -"){
			$birthday = 1;
		}
		$birthYear = $_POST["birthYear"];
		$dateOfBirth = $birthYear."-".$birthMonth."-".$birthday;
		$address = $_POST["address"];
		$city = $_POST["city"];
		$state = $_POST["state"];
		$zip = $_POST["zip"];
		$fullAddress = $address.", ".$city.", ".$state;
		$joinNHOHVA = $_POST['joinNHOHVA'];
		//Validate that the password was entered correctly on the server side
		if($userPassword != $passwordConfirmation){
			$_SESSION['passwordConfirmed'] = false;
			header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php");
		}
		//Get the current date
		$time = getDate();
		$currentYear = $time["year"];
		$currentMonth = $time["mon"];
		$currentDay = $time["mday"];
		//Verify that the user isn't too young
		if($currentYear - $birthYear < 18){
			$_SESSION['validBirthday'] = false;
			header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php");
		} else if($currentYear - $birthYear == 18 && $currentMonth - $birthMonth < 0){
			$_SESSION['validBirthday'] = false;
			header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php");
		} else if($currentYear - $birthYear == 18 && $currentMonth - $birthMonth == 0 && $currentDay - $birthday < 0){
			$_SESSION['validBirthday'] = false;
			header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php");
		} else {
			//Database info
			$host = "localhost";
			$database = "NHOHVA";
			$user = "mg1021";		$password = "goodspec";
			$charset = "utf8";
			$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
			$opt = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			];
			//Creates a database object
			$pdo = new PDO($dsn, $user, $password, $opt);
			//Checks to see if the email is already taken
			$emailQuery = $pdo->query('SELECT Email FROM User');
			while(($row = $emailQuery->fetch())){
				if($row['Email'] == $email){
					$_SESSION['validEmail'] = false;
					header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/registration.php");
				}
			}
			//Performs the insertQuery operation and creates the account.
			$encryptedPassword = hash("sha256", $userPassword);
			$insertQuery = $pdo->prepare('INSERT INTO User (Email, Password, FirstName, LastName, SpouseFirstName, SpouseLastName, DoB, Address, Zip, OptIn) VALUES (?,?,?,?,?,?,?,?,?,?)');
			$insertQuery->execute([$email, $encryptedPassword, $firstName, $lastName, $spouseF, $spouseL, $dateOfBirth, $fullAddress, $zip, $joinNHOHVA]);
			$_SESSION['valid'] = true;
			$_SESSION['admin'] = false;
			$_SESSION['email'] = $email;
			$_SESSION['firstName'] = $firstName;
			$_SESSION['lastName'] = $lastName;
			$_SESSION['address'] = $fullAddress;
			$_SESSION['zip'] = $zip;
			$_SESSION['dateOfBirth'] = $dateOfBirth;
		}
		//For the user that chose a club before creating an account
		if (isset($_SESSION['Attempted club'])){
			$clubId = $_SESSION['Attempted club'];
			$feeType = $_SESSION['Attempted Fee'];
			$pdo = new PDO($dsn, $user, $password, $opt);
			//We add the transaction to the cart
			$cartInsert = $pdo->prepare('INSERT INTO Cart (Email, ClubId, MemType) VALUES (?,?, ?)');
			$cartInsert->execute([$_SESSION['email'], $clubId, $feeType]);

			//Get the associated fee
			$feeQuery = $pdo->prepare('SELECT '.$feeType.' FROM Fee WHERE clubId = ?');
			$feeQuery->execute([$clubId]);
			$row = $feeQuery->fetch();

			$addedFee = $row[$feeType];

			$currentDueQuery = $pdo->prepare('SELECT AmountDue FROM User WHERE Email = ?');
			$currentDueQuery->execute([$_SESSION['email']]);
			$due = $currentDueQuery->fetch();

			$amountDue = $due['AmountDue'];
			$amountDue += $addedFee;

			$updateUser = $pdo->prepare('UPDATE User SET AmountDue = ? WHERE Email = ?');
			$updateUser->execute([$amountDue, $_SESSION['email']]);


			//We create session variables for use in the cart
			if(isset($_SESSION['Clubs Selected'])){
				$_SESSION['Clubs Selected'] += 1;
				$_SESSION['Cost'] += $addedFee;
			} else {
				$_SESSION['Clubs Selected'] = 1;
				$_SESSION['Cost'] = $addedFee;
			}
			unset($_SESSION['Attempted club']);
			unset($_SESSION['Attempted Fee']);
		}
		//Send them an email confirming their account creation...
		$message = 'Hi '.$_SESSION['firstName'].',
	
Thank you for creating an account with NHOHVA! 

Happy Riding!
NHOHVA';
		
		require_once('/home/mg1021/Home/NHOHVA/PHPMailer/class.phpmailer.php');
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: NHOHVA Do Not Reply <mg1021@plymouth.edu>' . "\r\n"; //info@nhohva.org
		
		$email = new PHPMailer();
		$email->From      = 'mg1021@plymouth.edu';
		$email->FromName  = 'NHOHVA Do Not Reply';
		$email->Subject   = 'Club Join Confirmation';
		$email->Body      = $message;
		$email->AddAddress( $_SESSION['email'] );

		$email->Send();
			
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	}
 ?>
