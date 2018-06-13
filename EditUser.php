<?php
	session_start();
	
	$button = $_POST['clicked'];
	$email = $_POST['email'];

  if(!isset($_SESSION['valid'])){
     header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
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
      
   if ($button == "remove"){
		$removeQry = $pdo->prepare('UPDATE User SET Password="" WHERE Email = ?');
		$removeQry->execute([$email]);
		$removeQry2 = $pdo->prepare('DELETE FROM Admins where Email = ?');
		$removeQry2->execute([$email]);
		echo "Redirecting. Successfully removed the user! <script>setTimeout(\"location.href = 'http://turing.plymouth.edu/~mg1021/NHOHVA/Users.php';\",3000);</script>";
   } else {
		include('dashboard-header.html');
	   	$firstName = $_POST['FirstName'];
		$lastName = $_POST['LastName'];
		$dob = $_POST['DoB'];
		$amountDue = $_POST['AmountDue'];
		$active = $_POST['Active'];
		$phoneNum = $_POST['PhoneNum'];
		$NHOHVAId = $_POST['NHOHVAId'];
		$clubs = $_POST['memberOfList'];
		$issued = $_POST['issued'];
		$exp = $_POST['exp'];
		
		$userqry = $pdo->prepare('SELECT * from User where Email = ?');
		$userqry->execute([$email]);
		$row = $userqry->fetch();
		
		//FIX THIS TO GO WITH QUERY
		$bDay = strtotime($row["DoB"]);
		$birthMonth = date("m", $bDay);
		$birthday = date("d", $bDay);
		$birthYear = date("Y", $bDay);
		$dateOfBirth = $birthYear."-".$birthMonth."-".$birthday;
		$address = explode(", ", $row['Address']);
		$zip = $row["Zip"];
		$phoneNum = $row["PhoneNum"];
		$areaCode = substr($phoneNum, 0, 3);
		$firstThree = substr($phoneNum, 4, 3);
		$lastFour = substr($phoneNum, 8, 4);
		if ($areaCode != "" and $firstThree !="" and $lastFour!=""){
			$phoneNum = $areaCode." ".$firstThree."-".$lastFour;
		} else {
			$phoneNum = "";
		}
		$spouseF = $row['SpouseFirstName'];
		$spouseL = $row['SpouseLastName'];
		$marital = $row['FamilyStatus'];
		if ($marital == 's'){
			$spouseF = NULL;
			$spouseL = NULL;
		}
		$joinNHOHVA = $row['OptIn'];

	   
	?>
	<!DOCTYPE html>
	<html lang="en">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="./nhohva.jpg">
		<!-- Automatically fills birthday, birthmonth, and birth year with correct options -->
		<script src = "http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src = "./js/registration.js"></script>
		<script src = "./js/edit-profile.js"></script>


		<title>Edit User Info</title>

		<!-- Bootstrap core CSS -->
		<link href="./css/bootstrap.min.css" rel="stylesheet">

		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<!-- <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet"> -->

		<!-- Custom styles for this template -->
		<link href="./css/signin.css" rel="stylesheet">
		<link href="./css/dashboard.css" rel="stylesheet">
	  </head>

	  <body>
		  <?php
			if(isset($_SESSION['valid']) && ($_SESSION['admin'] == false)){
				include('navbar.php');
			} else if (isset($_SESSION['valid']) && ($_SESSION['admin'] == true)){
				include('admin-navbar.php');
			} else {
				include('nonmember-navbar.php');
			}
		?>    
		<div class="container">
		  <div class="row">
		  <?php 
			////include('sidebar.php');
		  ?>

		  <form name="form" class="form-signin" action="edit-profile-backend.php" method="post">
        <h2 class="form-signin-heading">Update User's Info</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="email" class="form-control" value="<?=$email?>" required autofocus>
        <p><label for="inputFirstName" class="sr-only">First Name</label>
        <input type="firstName" name="firstName" class="form-control" value="<?=$firstName?>" required>
        <label for="inputLastName" class="sr-only">Last Name</label>
        <input type="lastName" name="lastName" class="form-control" value="<?=$lastName?>" required></p>
				<h4 class="form-signin-heading">Add Spouse Name?</h4>
		<center><input  onchange="singleStatus()" id="marital" type="radio"
  		<?php
			if ($spouseF == NULL && $spouseL == NULL){ ?>
				checked = "checked"
  		<?php
			} ?>  
				name="marital" value="s"> No
  		<input id = "single" onchange="marriedStatus()"id="marital" type="radio"
  		<?php
		if ($spouseF != NULL && $spouseL != NULL){ ?>
  			checked = "checked"
  		<?php } ?> id = "married" name="marital" value="m"> Yes<br>
  			<label id="spouseFLabel" for="spouseFName" class="sr-only"
       <?php 
		if ($spouseF != NULL && $spouseL != NULL){ ?>
          style="display: block">
      <?php } else {?>
        style="display: none"> <?php } ?>
        Spouse First Name</label>
  			<input id="spouseFInput" type="spouseFName" name="spouseFName" class="form-control" placeholder="Spouse First Name" value="<?=$spouseF?>"
       <?php 
		if ($spouseF != NULL && $spouseL != NULL){ ?>
          style="display: block">
          <?php } else {?>
            style="display: none">
          <?php } ?>
          <label id="spouseLLabel" for="spouseLName" class="sr-only"
         <?php 
		 if ($spouseF != NULL && $spouseL != NULL){ ?>
            style="display: block">
        <?php } else {?>
          style="display: none"> <?php } ?>
          Spouse Last Name</label>
    			<input id="spouseLInput" type="spouseLName" name="spouseLName" class="form-control" placeholder="Spouse Last Name" value="<?=$spouseL?>"
         <?php 
		 if ($spouseF != NULL && $spouseL != NULL){ ?>
            style="display: block">
            <?php } else {?>
              style="display: none">
			             <?php } ?>
		</center><center><H4 class="form-signin-heading">Date of Birth:</H4></center>
        <div class = "dob">
			<label for="inputMonthOfBirth" class="sr-only">Month of Birth</label>
			<select id="months" type = "birthMonth" name = "birthMonth" class="form-control"
			placeholder="Month" required><option selected>-Month-</option></select>
        </div>
		<div class = "dob">
			<label for="inputDayOfBirth" class="sr-only">Day of Birth</label>
			<select id="days" type = "birthday" name = "birthday" class="form-control"
			placeholder="Day" required><option disabled selected value>-Day-</option></select>
        </div>
	    <div class = "dob">
			<label for="inputYearOfBirth" class="sr-only">Year of Birth</label>
			<select id="years" type = "birthYear" name = "birthYear" class="form-control"
			placeholder="Year" required><option disabled selected value>-Year-</option></select>
        </div>
		 <label for="inputAddress" class="sr-only">Address</label>
        <input type="address" name="address" class="form-control" value="<?=$address[0]?>" required>
        <label for="inputCity" class="sr-only">City/Town</label>
          <input type="city" name="city" class="form-control" value="<?=$address[1]?>" required>
		    <label for="inputState" class="sr-only">State</label>
          <select id="state" type="state" name="state" class="form-control" placeholder="State" required>
				<option value="<?=$address[2]?>" selected><?=$address[2]?></option>
				<option value="AL">Alabama</option>
				<option value="AK">Alaska</option>
				<option value="AZ">Arizona</option>
				<option value="AR">Arkansas</option>
				<option value="CA">California</option>
				<option value="CO">Colorado</option>
				<option value="CT">Connecticut</option>
				<option value="DE">Delaware</option>
				<option value="DC">District Of Columbia</option>
				<option value="FL">Florida</option>
				<option value="GA">Georgia</option>
				<option value="HI">Hawaii</option>
				<option value="ID">Idaho</option>
				<option value="IL">Illinois</option>
				<option value="IN">Indiana</option>
				<option value="IA">Iowa</option>
				<option value="KS">Kansas</option>
				<option value="KY">Kentucky</option>
				<option value="LA">Louisiana</option>
				<option value="ME">Maine</option>
				<option value="MD">Maryland</option>
				<option value="MA">Massachusetts</option>
				<option value="MI">Michigan</option>
				<option value="MN">Minnesota</option>
				<option value="MS">Mississippi</option>
				<option value="MO">Missouri</option>
				<option value="MT">Montana</option>
				<option value="NE">Nebraska</option>
				<option value="NV">Nevada</option>
				<option value="NH">New Hampshire</option>
				<option value="NJ">New Jersey</option>
				<option value="NM">New Mexico</option>
				<option value="NY">New York</option>
				<option value="NC">North Carolina</option>
				<option value="ND">North Dakota</option>
				<option value="OH">Ohio</option>
				<option value="OK">Oklahoma</option>
				<option value="OR">Oregon</option>
				<option value="PA">Pennsylvania</option>
				<option value="RI">Rhode Island</option>
				<option value="SC">South Carolina</option>
				<option value="SD">South Dakota</option>
				<option value="TN">Tennessee</option>
				<option value="TX">Texas</option>
				<option value="UT">Utah</option>
				<option value="VT">Vermont</option>
				<option value="VA">Virginia</option>
				<option value="WA">Washington</option>
				<option value="WV">West Virginia</option>
				<option value="WI">Wisconsin</option>
				<option value="WY">Wyoming</option>
			</select>
		    <label for="inputZip" class="sr-only">Zip Code</label>
          <input type="zip" name="zip" class="form-control" value=<?=$row['Zip']?> required>
		<center><H4 class="form-signin-heading">Phone Number:</H4></center>
		<div class = "dob">
			<label for="areaCode" class="sr-only">Area Code</label>
			<input type="areaCode" name="areaCode" maxlength = "3" class="form-control" value="<?=$areaCode?>">
        </div>
		<div class = "dob">
			<label for="firstThree" class="sr-only">First 3</label>
			<input type="firstThree" name="firstThree" maxlength = "3" class="form-control" value="<?=$firstThree?>">
        </div>
	    <div class = "dob">
			<label for="lastFour" class="sr-only">Last 4</label>
			<input type="lastFour" name="lastFour" maxlength = "4" class="form-control" value="<?=$lastFour?>"><BR>
        </div>
		  <center><H5 class="form-signin-heading">Would you like to become a member of NHOHVA for free?</H5></center>
		  <center><input  id="joinNHOHVA" type="radio"
		<?php
		if ($row['OptIn'] == 'y'){ ?>
			checked = "checked"
		<?php }
		?>  name="joinNHOHVA" value="y"> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp
		<input id = "joinNHOHVA" type="radio"
		<?php
		if ($row['OptIn'] == 'n'){ ?>
			checked = "checked"
		<?php } ?> id = "joinNHOHVA" name="joinNHOHVA" value="n"> No<br>
		<!--<center><input id="joinNHOHVA" type="radio" name="joinNHOHVA" value="y"> Yes
				<input id="joinNHOHVA" type="radio" name="joinNHOHVA" value="n"> No<br></center><BR>-->
			<br><button class="btn btn-lg btn-primary btn-block" name="adminSubmit" type="submit">Save Changes</button>
      </form>
	</div>
		</div>
		<BR><a href="Users.php"><button class="btn btn-lg btn-primary btn-block" style="width: 300px; display: block; margin: auto; background-color: red">Cancel</button></a>
	  </body>
	</html>
<?php
   }
?>
