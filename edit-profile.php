<?php
   session_start();
   //Check if the user is already verified. If not, then checks credentials.
   if(!isset($_SESSION['valid'])){
     header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
   }
   include('dashboard-header.html');
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
   $userqry = $pdo->prepare('SELECT FirstName, LastName, SpouseFirstName, SpouseLastName, DoB, Address, Zip, FamilyStatus, PhoneNum, OptIn FROM User WHERE Email = ?');
   $userqry->execute([$_SESSION['email']]);
   $row = $userqry->fetch();
   $month = date("m", strtotime($row['DoB']));
   $day = date("d", strtotime($row['DoB']));
   $year = date("Y", strtotime($row['DoB']));
   $address = explode(", ", $row['Address']);
   if($row['PhoneNum'] != ""){
	   $areaCode = substr($row['PhoneNum'], 0, 3);
	   $firstThree = substr($row['PhoneNum'], 4, 3);
	   $lastFour = substr($row['PhoneNum'], 8, 4);
   } else {
	   $areaCode = "";
	   $firstThree = "";
	   $lastFour = "";
   }
   //Get the Spouse information:
	$spouseF = $row['SpouseFirstName'];
	$spouseL = $row['SpouseLastName'];
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
    <script src = "./js/edit-profile.js"></script>

    <title>Update</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">
      <form name="form" class="form-signin" action="edit-profile-backend.php" method="post">
        <h2 class="form-signin-heading">Update your info</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="email" class="form-control" value="<?=$_SESSION['email']?>" required autofocus>
        <p><label for="inputFirstName" class="sr-only">First Name</label>
        <input type="firstName" name="firstName" class="form-control" value="<?=$row['FirstName']?>" required>
        <label for="inputLastName" class="sr-only">Last Name</label>
        <input type="lastName" name="lastName" class="form-control" value="<?=$row['LastName']?>" required></p>
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
			<br><button class="btn btn-lg btn-primary btn-block" type="submit">Save Changes</button>
      </form>
	  <form name="form" class="form-signin" action="profile.php" method="post">
		<a href="profile.php"><button class="btn btn-lg btn-primary btn-block" type="submit">Cancel</button></a>
	  </form>
    </div>
  </body>
</html>
