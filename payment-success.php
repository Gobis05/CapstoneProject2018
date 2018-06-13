<?php
	session_start();
	//Check if the user is already verified. If not, then checks credentials.
	if(!isset($_SESSION['valid'])){
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
	}
	
	$_SESSION['page'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	//If the payment is complete and the user has an ID
	if(isset($_SESSION['memID'])){
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/club-join-backend.php");
	} else { //The user needs an ID...
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/idGen.php");
	}

?>
