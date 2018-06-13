<?php
  session_start();
	//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
	//if(!isset($_SESSION['valid'])){
		//header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	//}
  include('dashboard-header.html');
 ?>
<html lang="en">
  <head>
    <link rel="icon" href="./logo.gif">

    <title>View Users</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/dashboard.css" rel="stylesheet">
	<link href="./css/assets.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css"
    integrity="sha512-M2wvCLH6DSRazYeZRIm1JnYyh22purTM+FDB5CsyxtQJYeKq83arPe5wgbNmcFXGqiSH2XR8dT/fJISVA1r/zQ=="
    crossorigin=""/>

    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"
    integrity="sha512-lInM/apFSqyy1o6s89K4iQUKg6ppXEgsVxT35HbzUupEVRh2Eu9Wdl4tHj7dZO0s1uvplcYGmt3498TtHq+log=="
    crossorigin=""></script>

	<script src = "./ViewAssets.js"></script>

    <style>
      .safe {
        height: 100%;
        width: 100%;
        margin-right: 0px;
        padding-right: 0px;
        margin-left: 0px;
        margin-bottom: 0px;
      }
      #map{ height: 81%; width: 94%; margin-left: 3%; margin-right: 3%; margin-bottom: 30px;}
      .placeholders{margin-left: 140px; width: calc(100% - 140px);}
      .col-sm-9{padding: 0px; margin-left: 140px;}
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0;}
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
      <div class="row">
	  <?php
		//////include('sidebar.php');
	?>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
           <center><h2>View Users</h2></center>
		</div>
       <div class="row placeholders" style="height: auto;">
	   <!--<p><button onclick="sortTable()">Sort</button></p>-->
	   	<?php
		//if($_SESSION['superAdmin'] == true){ ?>
			<center><a href="AddUser.php"><button style="width:300px;" class="btn btn-lg btn-primary btn-block">Add a User!</button></a></center><BR>
		<?php// } ?>
		<!--Table of the assets-->
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
				<th style="text-align: center;">Member Of</th>
				<th style="text-align: center;">Issue Date</th>
				<th style="text-align: center;">Expiration Date</th>
				<th style="text-align: center;">Update User</th>
				<th style="text-align: center;">Remove</th>
			</tr> 
				<?php
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
				//If they are a super admin then select everything for every user!
				if($_SESSION['superAdmin'] == true){
					$query = $pdo->prepare('SELECT * FROM User ORDER BY LastName');
				} else {
					$clubs = array();
					$adminQry = $pdo->prepare('SELECT ClubId FROM Admins WHERE Email=? AND ClubId != 0');
					$adminQry->execute([$_SESSION['user']]);
					while($admin = $adminQry->fetch()){
						array_push($clubs, $admin['ClubId']);
					}
					$ids = join(",",$clubs);
					$query = $pdo->prepare('SELECT * FROM Membership WHERE ClubId IN ('.$ids.')');
				}
						$query->execute();
						while($row = $query->fetch()){
							$membershipQry = $pdo->prepare('SELECT * FROM Membership WHERE Email=? ORDER BY ClubId');
							$membershipQry->execute([$row['Email']]);
							//If they are a super admin the query is set
							if($_SESSION['superAdmin'] == true){
								$firstName = $row['FirstName'];
								$lastName = $row['LastName'];
								$email = $row['Email'];
								$dob = $row['DoB'];
								$amountDue = $row['AmountDue'];
								$active = $row['Active'];
								$phoneNum = $row['PhoneNum'];
							//If they are NOT a super admin then you need to do another quick query!
							} else {
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
							}
							
							$memberOf = array();
							$NHOHVAId = "";
							$exp="";
							$issued="";
							while($row2 = $membershipQry->fetch()){
								$exp = date_create($row2['ExpireDate']);
								$exp = date_format($exp, 'n-j-Y');
								$issued = date_create($row2['RegistrationDate']);
								$issued = date_format($issued, 'n-j-Y');
								//if (!in_array($row2['ClubId'], $memberOf)){
									array_push($memberOf, $row2['ClubId']);
								//}
								$NHOHVAId = $row2['NHOHVAId'];
							}
							$memberOfList = implode(', ', $memberOf);
						?>
							<form name="form" action="./EditUser.php" class="form-signin" method="post">
								<tr>
									<td style="padding: 5px;"><input type="hidden" name="FirstName" value="<?=$firstName?>"><?=$firstName?></td>
									<td style="padding: 5px;"><input type="hidden" name="LastName" value="<?=$lastName?>"><?=$lastName?></td>
									<td style="padding: 5px;"><input type="hidden" name="email" value="<?=$email?>"><?=$email?></td>
									<td style="padding: 5px;"><input type="hidden" name="DoB" value="<?=$dob?>"><?=$dob?></td>
									<td style="padding: 5px;"><input type="hidden" name="AmountDue" value="<?=$amountDue?>"><?=$amountDue?></td>
									<td style="padding: 5px;"><input type="hidden" name="Active" value="<?=$active?>"><?=$active?></td>
									<td style="padding: 5px;"><input type="hidden" name="PhoneNum" value="<?=$phoneNum?>"><?=$phoneNum?></td>
									<td style="padding: 5px;"><input type="hidden" name="NHOHVAId" value="<?=$NHOHVAId?>"><?=$NHOHVAId?></td>
									<td style="padding: 5px;"><input type="hidden" name="memberOfList" value="<?=$memberOfList?>"><?=$memberOfList?></td>
									<td style="padding: 5px;"><input type="hidden" name="issued" value="<?=$issued?>"><?=$issued?></td>
									<td style="padding: 5px;"><input type="hidden" name="exp" value="<?=$exp?>"><?=$exp?></td>
									
									<td>
										<button type="submit" name="clicked" value="edit" class="btn btn-link btn-xs">
											<h4><span class="glyphicon glyphicon-pencil"></span></h4>
										</button>
									</td>
									<td>
										<button type="submit" name="clicked" value="remove" class="btn btn-link btn-xs" onclick="return confirm('Are you sure you want to remove this user? \nThis will only prevent them from logging in but retain their information');">
											<h4><span class="glyphicon glyphicon-trash"> </span></h4>
										</button>
									</td>
								</tr>
							</form>
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

