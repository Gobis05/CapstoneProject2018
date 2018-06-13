<?php
  session_start();
	//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
	//if(!isset($_SESSION['valid'])){
		//header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	//}
  include('dashboard-header.html');

	$clubName = $_POST['clubName'];
 
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
				
	/* check connection */
	$pdo = new PDO($dsn, $user, $password, $opt);
 
	$clubIdQry = $pdo->prepare('SELECT ClubId FROM Club WHERE ClubName=?');
	$clubIdQry->execute([$clubName]);
	$id = $clubIdQry->fetch();
	$clubId = $id['ClubId'];
 
 ?>

<html lang="en">
	<head>
		<title>Administration</title>

		<!-- Bootstrap core CSS -->
		<link href="./css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="./css/signin.css" rel="stylesheet">
		<link href="./css/dashboard.css" rel="stylesheet">
		<style>
			html, body {
				background-color: #FFFFFF !important;
			}
			#header {
				margin-top: 50px;
				text-align: center;
				width:100%;
			}
			.myButton {
				padding: 15px;
				margin:0 10px;
				width:auto;
				height:auto;
				background-color: #337ab7;
				font-size: 2rem;
				border: none;
				color: #FFFFFF;
				border-radius: 10px;
			}
			.myButton:hover {
				background-color: #286090;
			}
			#graph-div {
				width:100%;
				text-align:center;
				
			}
			.chart {
				display:inline-block;
				margin-top:100px;
			}
			@media (max-width: 800px) {
				.chart {
					max-width: 100%;
					margin-top:20px;
				}
			}
			@media (max-width: 600px) {
				.myButton {
					width: 80%;
					margin: 0 auto;
					margin-bottom: 10px;
				}
			}
		</style>
	</head>
<body>
    <?php
		if(isset($_SESSION['valid']) && ($_SESSION['admin'] == false)){
			include('navbar.php');
		} else if (isset($_SESSION['valid']) && ($_SESSION['admin'] == true)){
			include('admin-navbar.php');
		} else {
			include('nonmember-navbar.php');
		}
    ?>

    <div class="container-fluid">
      <div class = "row">
	  <?php
		//////include('sidebar.php');
	?>
       <div class="row placeholders" style="height: auto;">
	   <!--<p><button onclick="sortTable()">Sort</button></p>-->
	   	<?php
		//if($_SESSION['superAdmin'] == true){ ?>
			<!--<center><a href="AddUser.php"><button style="width:300px;" class="btn btn-lg btn-primary btn-block">Add a User!</button></a></center><BR>-->
		<?php// } ?>
		<!--Table of the assets-->
		<center><h2>Current Members in <?=$clubName?></h2> </center>
		<div>
		<form action="Scripts/reports.php" method="post" class="form-signin">
			<input type="hidden" id="report" name="report" value=<?=$clubId?>>
			<button class="myButton" type="submit">Export Table</button>
		</form>
		</div>
		<table style="padding: 5px;" id="table" align="center" width="90%" cellspacing="2" border="2">
			<tr align="center" style="border-bottom: solid 1px black;">
				<th style="text-align: center;">First Name</th>
				<th style="text-align: center;">Last Name</th>
				<th style="text-align: center;">Email</th>
				<th style="text-align: center;">Date of Birth</th>
				<th style="text-align: center;">Amount Due</th>
				<th style="text-align: center;">Active?</th>
				<th style="text-align: center;">Phone Number</th>
				<th style="text-align: center;">NHOHVA Id</th>
				<th style="text-align: center;">Issue Date</th>
				<th style="text-align: center;">Expiration Date</th>
				<!--<th style="text-align: center;">Update User</th>
				<th style="text-align: center;">Remove</th>-->
			</tr> 
				<?php
					$year = date('y');
					$query = $pdo->prepare('SELECT * FROM Membership WHERE ClubId=? AND SUBSTR(NHOHVAId, 1, 2) = '.$year);
						$query->execute([$clubId]);
						
						while($row = $query->fetch()){
							$membershipQry = $pdo->prepare('SELECT * FROM Membership WHERE Email=? AND SUBSTR(NHOHVAId, 1, 2) = '.$year.' ORDER BY ClubId');
							$membershipQry->execute([$row['Email']]);

								$userqry = $pdo->prepare('SELECT * FROM User WHERE Email=?');
								$userqry->execute([$row['Email']]);
								$user = $userqry->fetch();
								$firstName = $user['FirstName'];
								$lastName = $user['LastName'];
								$email = $user['Email'];
								$dob = $user['DoB'];
								$amountDue = $user['AmountDue'];
								$active = $user['Active'];
								$phoneNum = $user['PhoneNum'];
							
							$NHOHVAId = "";
							$exp="";
							$issued="";
							while($row2 = $membershipQry->fetch()){
								$exp = date_create($row2['ExpireDate']);
								$exp = date_format($exp, 'n-j-Y');
								$issued = date_create($row2['RegistrationDate']);
								$issued = date_format($issued, 'n-j-Y');
								$NHOHVAId = $row2['NHOHVAId'];
							}
						?>
								<tr>
								<tr>
									<td style="padding: 5px;"><input type="hidden" name="FirstName" value="<?=$firstName?>"><?=$firstName?></td>
									<td style="padding: 5px;"><input type="hidden" name="LastName" value="<?=$lastName?>"><?=$lastName?></td>
									<td style="padding: 5px;"><input type="hidden" name="email" value="<?=$email?>"><?=$email?></td>
									<td style="padding: 5px;"><input type="hidden" name="DoB" value="<?=$dob?>"><?=$dob?></td>
									<td style="padding: 5px;"><input type="hidden" name="AmountDue" value="<?=$amountDue?>"><?=$amountDue?></td>
									<td style="padding: 5px;"><input type="hidden" name="Active" value="<?=$active?>"><?=$active?></td>
									<td style="padding: 5px;"><input type="hidden" name="PhoneNum" value="<?=$phoneNum?>"><?=$phoneNum?></td>
									<td style="padding: 5px;"><input type="hidden" name="NHOHVAId" value="<?=$NHOHVAId?>"><?=$NHOHVAId?></td>
									<td style="padding: 5px;"><input type="hidden" name="issued" value="<?=$issued?>"><?=$issued?></td>
									<td style="padding: 5px;"><input type="hidden" name="exp" value="<?=$exp?>"><?=$exp?></td>
									
									<!--<td>
										<button type="submit" name="clicked" value="editUsersChart" class="btn btn-link btn-xs">
											<h4><span class="glyphicon glyphicon-pencil"></span></h4>
										</button>
									</td>
									<td>
										<button type="submit" name="clicked" value="removeUsersChart" class="btn btn-link btn-xs" onclick="return confirm('Are you sure you want to remove this user? \nThis will only prevent them from logging in but retain their information');">
											<h4><span class="glyphicon glyphicon-trash"> </span></h4>
										</button>
									</td>-->
								</tr>
						<?php
						}
				?>
		</table>
		<BR><BR>
	</div>
    </div>
  </div>
      <!-- Bootstrap core JavaScript
      ================================================== -->
      <!-- Placed at the end of the document so the pages load faster -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
	</body>
 </html>

