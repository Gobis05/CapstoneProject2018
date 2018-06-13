<?php
  session_start();
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

    <title>Create Account</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet"> -->

    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">
    <link href="./css/dashboard.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <?php include('nonmember-navbar.php'); ?>
    <div class="container">
      <div class="row">
      <?php ////include('sidebar.php');
      //If the entered password didn't match
        if(isset($_SESSION['passwordConfirmed'])){
      ?>
      <center><p><strong>The entered passwrods did not match.</strong></p></center>
      <?php
          unset($_SESSION['passwordConfirmed']);
        }
        //If the entered date was under 18
        if(isset($_SESSION['validBirthday'])){?>
          <center><p><strong>You are too young to become a member.</strong></p></center>
        <?php
          unset($_SESSION['validBirthday']);
        }
        if(isset($_SESSION['validEmail'])){?>
          <center><p><strong>This email already has an account</strong></p></center>
        <?php
          unset($_SESSION['validEmail']);
        }
      ?>

      <form name="form" class="form-signin" action="account-creation.php" method="post">
        <h2 class="form-signin-heading">Create Account</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
        <label for="confirmPassword" class="sr-only">Confirm Password</label>
        <input type="password" id="confirmPassword" name="passwordConfirmation" class="form-control" placeholder="Confirm password" required>
        <p><label for="inputFirstName" class="sr-only">First Name</label>
        <input type="firstName" name="firstName" class="form-control" placeholder="First name" required>
        <label for="inputLastName" class="sr-only">Last Name</label>
		<input type="lastName" name="lastName" class="form-control" placeholder="Last name" required></p>
		<h4 class="form-signin-heading">Add Spouse Name?</h4>
		<center><input onchange="marriedStatus()"id="marital" type="radio" name="marital" value="f"> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp
		<input onchange="singleStatus()" id="marital" type="radio" name="marital" value="s"> No<br>
		<label id="spouseFLabel" for="spouseFName" class="sr-only" style="display: none">Spouse First Name</label>
		<input id="spouseFInput" type="spouseFName" name="spouseFName" class="form-control" placeholder="Spouse First Name" style="display: none">
        <label id="spouseLLabel" for="spouseLName" class="sr-only" style="display: none">Spouse Last Name</label>
  		<input id="spouseLInput" type="spouseLName" name="spouseLName" class="form-control" placeholder="Spouse Last Name" style="display: none">
		</center><center><H4 class="form-signin-heading">Date of Birth:</H4></center>
        <div class = "dob">
			<label for="inputMonthOfBirth" class="sr-only">Month of Birth</label>
			<select id="months" type = "birthMonth" name = "birthMonth" class="form-control"
			placeholder="Month" required><option disabled selected value>- Month -</option></select>
        </div>
		<div class = "dob">
			<label for="inputDayOfBirth" class="sr-only">Day of Birth</label>
			<select id="days" type = "birthday" name = "birthday" class="form-control"
			placeholder="Day" required><option>- Day -</option></select>
        </div>
	    <div class = "dob">
			<label for="inputYearOfBirth" class="sr-only">Year of Birth</label>
			<select id="years" type = "birthYear" name = "birthYear" class="form-control"
			placeholder="Year" required><option disabled selected value>- Year -</option></select>
        </div>
		 <label for="inputAddress" class="sr-only">Address</label>
        <input type="address" name="address" class="form-control" placeholder="Address" required>
        <label for="inputCity" class="sr-only">City/Town</label>
          <input type="city" name="city" class="form-control" placeholder="City/Town" required>
		    <label for="inputState" class="sr-only">State</label>
          <select id="state" type="state" name="state" class="form-control" placeholder="State" required>
            <option disabled selected value>Select a State</option>
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
          <input type="zip" maxlength = "5" name="zip" class="form-control" placeholder="Zip Code" required>
		  <center><H5 class="form-signin-heading">Would you like to become a member of NHOHVA for free?</H5></center>
		<center><input id="joinNHOHVA" type="radio" name="joinNHOHVA" value="y"> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp
				<input id="joinNHOHVA" type="radio" name="joinNHOHVA" value="n"> No<br></center><BR>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Create Account</button>
		  </form><h5 class="form-signin-heading">OR</h5>
		  <a href="sign-in.php"><button class="btn btn-lg btn-primary btn-block" style="width: 300px; display: block; margin: auto">Sign into Existing Account</button></a>
		  <BR>
		  <a href="index.php"><button class="btn btn-lg btn-primary btn-block" style="width: 300px; display: block; margin: auto; background-color: red">Back to main page</button></a>

    </div>
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <script src="./js/ie10-viewport-bug-workaround.js"></script> -->
  </body>
</html>
