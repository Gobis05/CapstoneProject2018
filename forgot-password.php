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

    <title>Password Reset</title>

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
      <div class="row">
        <?php //include('sidebar.php') ?>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <div class="container">

      <form class="form-signin" action="forgot-password-backend.php" method="post">
        <h2 class="form-signin-heading">Enter your email</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
		<BR>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
      </form>
        </div>
    </div>
	</div>
	</div>


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
