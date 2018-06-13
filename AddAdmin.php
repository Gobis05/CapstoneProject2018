<?php
  session_start();
	//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
	if(!isset($_SESSION['valid'])){
		header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	}
  include('dashboard-header.html');
 ?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Administration</title>

		<!-- Bootstrap core CSS -->
		<link href="./css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="./css/signin.css" rel="stylesheet">
		<link href="./css/dashboard.css" rel="stylesheet">
	</head>
	<body>
		<?php
			//Include the right navbar, this should always be the admin bar because we are dealing with administration
			if(isset($_SESSION['valid']) && ($_SESSION['admin'] == false)){
				include('navbar.php');
			} else if (isset($_SESSION['valid']) && ($_SESSION['admin'] == true)){
				include('admin-navbar.php');
			} else {
				include('nonmember-navbar.php');
			}
		?>
		<div class="container">
			<div class="row">
				<?php ////include('sidebar.php');
					//Database Info
					$host = "localhost";
					$database = "NHOHVA";
					$user = "mg1021";              $password = "goodspec";
					$charset = "utf8";
					$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
					$opt = [
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
						PDO::ATTR_EMULATE_PREPARES   => false,
					 ];
					 $pdo = new PDO($dsn, $user, $password, $opt);
				?>
				<form name="form" class="form-signin" action="admin-addAdmin-backend.php" method="post">
					<h2 class="form-signin-heading">Add an Admin</h2>
					<select id="club" type="club" name="club" class="form-control" placeholder="Club" required>
						<option disabled selected value>Add Admin to this Club...</option>
						<?php
							//check if the user is a superAdmin... If they are not, find the clubIds that they are admins of
							if($_SESSION['superAdmin'] == 1){
								$clubs = $pdo->prepare('SELECT ClubName, ClubId from Club ORDER BY ClubName');
							} else {
								$clubsArr = array();
								$adminQry = $pdo->prepare('SELECT ClubId FROM Admins WHERE Email=?');
								$adminQry->execute([$_SESSION['email']]);
								//Put the ids into an array
								while($admin = $adminQry->fetch()){
									array_push($clubsArr, $admin['ClubId']);
								}
								//join the array into a comma seperated list for the query
								$ids = join(",",$clubsArr);
								$clubs = $pdo->prepare('SELECT ClubName, ClubId from Club WHERE ClubId !=0 AND ClubId IN ('.$ids.')ORDER BY ClubName');
							}
							$clubs->execute();
							while($row = $clubs->fetch()){?>
								<option value="<?=$row['ClubId']?>"><?=$row['ClubName']?></option>
							<?php } ?>
					</select>
					<!-- Put in the all user's that have an account with the website. -->
					<select id="user" type="user" name="user" class="form-control" placeholder="User" required>
						<option disabled selected value>Make this User an Admin...</option>
						<?php
						$qry2 = $pdo->prepare('SELECT Email, FirstName, LastName FROM User ORDER BY LastName');
						$qry2->execute();
						while($row3 = $qry2->fetch()){
							$display = $row3['FirstName']." ".$row3['LastName']." (".$row3['Email'].")";
						?>
							<option value="<?=$row3['Email']?>"><?=$display?></option>
						<?php
						} ?>
					</select><BR>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Add User</button>
				</form>
			</div>
		</div>
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
		<script src="./js/bootstrap.min.js"></script>
	</body>
</html>