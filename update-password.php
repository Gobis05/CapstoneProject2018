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

    <title>Update Password</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">


    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">

  </head>

  <body>
    <?php 
		if(isset($_SESSION['valid']) && ($_SESSION['admin'] == false)){
			include('navbar.php');
		} else {
			include('admin-navbar.php');
		}
	?>
	
    <div class="container">
      <div class="row">
		<?php //include('sidebar.php') ?>
		<form name="form" class="form-signin" action="verify-password.php" method="post">
			<h2 class="form-signin-heading">Update Password</h2>
			<label for="inputPassword" class="sr-only">Password</label>
			<input type="password" id="inputPassword" name="password" class="form-control" placeholder="Current Password" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Verify</button><br>
			<a href="profile.php"><button class="btn btn-lg btn-primary btn-block" type="submit">Cancel</button></a>
		</form>
	  </div>
    </div> <!-- /container -->
  </body>
</html>
