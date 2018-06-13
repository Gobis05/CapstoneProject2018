<?php
  session_start();
	//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
	//if(!isset($_SESSION['valid'])){
		//header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	//}
  include('dashboard-header.html');

  
  
  
 /* echo("<script type=\"text/javascript\">
			window.onload = function(){
			//This sessionStorage.getItem(); is also a predefined function in javascript
			//will retrieve session and get the value;
			var a = sessionStorage.getItem(\"clubName\");
			console.log(a);
			}; 
		</script>");*/
  //echo $_GET['a'];
 ?>

<html lang="en">
<body>
<!--HERE!!!!-->
		<form name="club" id="club" action="Users-Chart.php" method="post">
			<input type="hidden" id="clubName" name="clubName">
		</form>
		<script>
			document.getElementById("clubName").value = sessionStorage.getItem("clubName");
			document.club.submit();
		</script>

	</body>
 </html>

