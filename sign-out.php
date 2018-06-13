<?php
  session_start();
  $username = $_SESSION['firstName'].$_SESSION['lastName'];
  if (isset($_SESSION['memID'])){
	unlink('/home/mg1021/Home/NHOHVAIdCards/'.$username.'NHOHVAIdCard'.$_SESSION['memID'].'.png');
  }
  session_unset();
  header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
 ?>
