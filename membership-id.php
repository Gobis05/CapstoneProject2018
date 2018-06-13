<?php
//This file essentially displays the membership ID photo
	session_start();
	if(!isset($_SESSION['valid'])){
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
	}
	if(!isset($_SESSION['memID'])){
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	}
	include('dashboard-header.html');
	$idCard = $_SESSION['firstName'].$_SESSION['lastName']."NHOHVAIdCard";
 ?>
<html>
	<body>
		<?php 
			//Use the correct navigation bar based on session variables
			if (isset($_SESSION['valid']) && ($_SESSION['admin'] == true)){
				include('admin-navbar.php');
			} else {
				include('navbar.php');
			} 
		?>

		<div class="container-fluid">
			<div class="row">
				<?php //include('sidebar.php') ?>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
					<!-- Generates and displays the image -->
					<a href="http://turing.plymouth.edu/~mg1021/NHOHVAIdCards/<?=$idCard?>.png"><img id="id" src="http://turing.plymouth.edu/~mg1021/NHOHVAIdCards/<?=$idCard?>.png"/></a>
				</div>
			</div>
		</div>

		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="./js/jquery.min.js"><\/script>')</script>
		<script src="./js/bootstrap.min.js"></script>
    </body>
</html>
