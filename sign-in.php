<?php
  session_start();
  $_SESSION['EnteredProperly'] = true;
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

		<title>Sign In</title>

		<!-- Bootstrap core CSS -->
		<link href="./css/bootstrap.min.css" rel="stylesheet">

		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="./css/signin.css" rel="stylesheet">
		<link href="./css/dashboard.css" rel="stylesheet">

	</head>
	<body>
		<?php include('nonmember-navbar.php'); ?>
		<div class="container-fluid">
			<div class="container">
				<form style="margin: 0 auto !important; " class="form-signin" action="sign-in-backend.php" method="post">
					<h2 class="form-signin-heading">Sign in</h2>
					<label for="inputEmail" class="sr-only">Email address</label>
					<input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
					<label for="inputPassword" class="sr-only">Password</label>
					<input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
					<div class="checkbox">
						<label>
							<input type="checkbox" value="remember-me"> Remember me
						</label>
					</div>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
					<center><a href="forgot-password.php">Forgot password</a></center>
				</form>
				<a href="registration.php"><button class="btn btn-lg btn-primary btn-block" style="width: 300px; display: block; margin: auto">Create an Account</button></a>
			</div>
		</div>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="./js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>
