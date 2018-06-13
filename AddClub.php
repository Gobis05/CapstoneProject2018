<?php
	session_start();
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

		<title>Add a New Club!</title>

		<!-- Bootstrap core CSS -->
		<link href="./css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="./css/signin.css" rel="stylesheet">
		<link href="./css/dashboard.css" rel="stylesheet">
	</head>

	<body>
		<?php
			//Include the right navigation bar
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
				<?php //include('sidebar.php') ?>
				<!-- A form to create a new club -->
				<form name="form" class="form-signin" action="admin-createClub-backend.php" method="post">
					<h2 class="form-signin-heading">Edit Club Info</h2>
					<label for="inputClubName" class="sr-only">Club Name</label>
					<input type="clubName" id="clubName" name="clubName" class="form-control" placeholder="Club Name" required autofocus>
					<label for="id" class="sr-only">Club Id</label>
					<select id="id" type="id" name="id" class="form-control" placeholder="Club ID" required>
						<option disabled selected value>Available Club Ids</option>
						<?php
							$clubIds = array();
							$pdo = new PDO($dsn, $user, $password, $opt);
							$idQry = $pdo->prepare('SELECT ClubId FROM Club');
							$idQry->execute();
							while($ids = $idQry->fetch()){
								$id = (int)$ids['ClubId'];
								array_push($clubIds, $ids['ClubId']);
							}
							$i = 0;
							while ($i < 100){ 
								if (!in_array($i, $clubIds)){
									?>
									<option value="<?=$i?>"><?=$i?></option>	
									<?php
								}
							$i++;
							}
						?>
					</select>				
					<label for="email" class="sr-only">Email address</label>
					<input type="email" id="email" name="email" class="form-control" placeholder="Club Email" required>
					<label for="address" class="sr-only">Address</label>
					<input type="address" name="address" class="form-control" placeholder="Address" required>
					<label for="url" class="sr-only">URL</label>
					<input type="url" name="url" class="form-control" placeholder="URL" required>
					<label for="maxSize" class="sr-only">Max Machine Size</label>
					<input type="maxSize" name="maxSize" class="form-control" placeholder="Max Machine Size (in inches)" required>
					<label for="president" class="sr-only">President</label>
					<input type="president" name="president" class="form-control" placeholder="President" required>
				
					<center><H4 class="form-signin-heading">Phone Number:</H4></center>
					<div class = "dob">
						<label for="areaCode" class="sr-only">Area Code</label>
						<input type="areaCode" name="areaCode" maxlength = "3" class="form-control">
					</div>
					<div class = "dob">
						<label for="firstThree" class="sr-only">First 3</label>
						<input type="firstThree" name="firstThree" maxlength = "3" class="form-control">
					</div>
					<div class = "dob">
						<label for="lastFour" class="sr-only">Last 4</label>
						<input type="lastFour" name="lastFour" maxlength = "4" class="form-control"><BR>
					</div>
					<label for="presEmail" class="sr-only">President Email</label>
					<input type="presEmail" name="presEmail" class="form-control" placeholder="President Email" required>
					<label for="allowed" class="sr-only">Allowed OHRV</label>
					<input type="allowed" name="allowed" class="form-control" placeholder="Allowed OHRV" required>
					<label for="descr" class="sr-only">Description</label>
					<textarea rows="4" type="descr" name="descr" class="form-control" placeholder="Description"></textarea>
					<BR><button class="btn btn-lg btn-primary btn-block" type="submit">Create Club!</button>
				</form>
				  <a href="Clubs.php"><button class="btn btn-lg btn-primary btn-block" style="width: 300px; display: block; margin: auto; background-color: red">Cancel</button></a>
			</div>
		</div> <!-- /container -->
	</body>
</html>
