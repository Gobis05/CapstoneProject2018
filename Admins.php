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

    <title>View Admins</title>

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
		////include('sidebar.php');
	?>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
           <center><h2>View Admins</h2></center>
		</div>
       <div class="row placeholders" style="height: auto;">
	   <!--<p><button onclick="sortTable()">Sort</button></p>-->
		<center><a href="AddAdmin.php"><button style="width:300px;" class="btn btn-lg btn-primary btn-block">Add an admin</button></a></center><BR>
		<!--Table of the assets-->
		<table style="padding: 5px;" id="table" align="center" width="50%" cellspacing="2" border="2">
					<tr align="center" style="border-bottom: solid 1px black;">
					<th style="text-align: center;">Name</th>
					<th style="text-align: center;">Email</th>
					<th style="text-align: center;">Admin Of</th>
					<th style="text-align: center;">Remove Admin</th>
				</tr> <?php
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
				//Generate the right query
				//If they are a super admin show all admins of all clubs
				if($_SESSION['superAdmin'] == true){
					$query = $pdo->prepare('SELECT * FROM Admins');
				//Otherwise find the clubs they are admins of
				} else {
					$clubs = array();
					$adminQry = $pdo->prepare('SELECT ClubId FROM Admins WHERE Email=? AND ClubId != 0');
					$adminQry->execute([$_SESSION['user']]);
					while($admin = $adminQry->fetch()){
						array_push($clubs, $admin['ClubId']);
					}
					$ids = join(",",$clubs);
					$query = $pdo->prepare('SELECT * FROM Admins WHERE ClubId IN ('.$ids.')');
				}
						$query->execute();
						while($row = $query->fetch()){ 
							//now get the club name and user's name from the respective tables...
							$query2 = $pdo->prepare('SELECT FirstName, LastName FROM User WHERE Email=? ORDER BY LastName');
							$query2->execute([$row['Email']]);
							$row2 = $query2->fetch();
							$name = $row2['FirstName'].' '.$row2['LastName'];
							$query3 = $pdo->prepare('SELECT ClubName FROM Club WHERE ClubId=?');
							$query3->execute([$row['ClubId']]);
							$row3 = $query3->fetch();
						?>
							<form name="form" action="./RemoveAdmin.php" class="form-signin" method="post">
								<tr>
									<td><input type="hidden" name="name" value="<?=$name?>"><?=$name?></td>
									<td><input type="hidden" name="email" value="<?=$row['Email']?>"><?=$row['Email']?></td>
									<td><input type="hidden" name="club" value="<?=$row['ClubId']?>"><?=$row3['ClubName']?></td>
									<td>
										<button type="submit" name="clicked" value="remove" class="btn btn-link btn-xs" onclick="return confirm('Are you sure you want to remove this user?');">
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

