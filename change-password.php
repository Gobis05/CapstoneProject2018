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

    <title>Change Password</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">
	<!-- The user must confirm their new password to avoid typos... -->
      <form name="form" class="form-signin" action="change-password-backend.php" method="post">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="New Password" required>
        <label for="confirmPassword" class="sr-only">Confirm Password</label>
        <input type="password" id="confirmPassword" name="passwordConfirmation" class="form-control" placeholder="Confirm Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Change Password</button><br>
		<?php
			if(!isset($_SESSION['passwordReset'])){ ?>
				<a href="profile.php"><button class="btn btn-lg btn-primary btn-block" type="submit">Cancel</button></a>
			<?php 
			} ?>
      </form>
    </div> <!-- /container -->
  </body>
</html>
