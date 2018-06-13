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
           <center><h2>View Clubs</h2></center>
		</div>
       <div class="row placeholders" style="height: auto;">
	   <!--<p><button onclick="sortTable()">Sort</button></p>-->
	   	<?php
		if($_SESSION['superAdmin'] == true){ ?>
			<center><a href="AddClub.php"><button style="width:300px;" class="btn btn-lg btn-primary btn-block">Add a Club!</button></a></center><BR>
		<?php } ?>
		<!--Table of the assets-->
		<table style="padding: 5px;" id="table" align="center" width="90%" cellspacing="2" border="2">
					<tr align="center" style="border-bottom: solid 1px black;">
					<th style="text-align: center;">Club ID</th>
					<th style="text-align: center;">Name</th>
					<th style="text-align: center;">Address</th>
					<th style="text-align: center;">Club Email</th>
					<th style="text-align: center;">President</th>
					<th style="text-align: center;">Phone Number</th>
					<th style="text-align: center;">President's Email</th>
					<th style="text-align: center;">Allowed OHRV</th>
					<th style="text-align: center;">Edit Club</th>
					<?php
						if($_SESSION['superAdmin'] == true){ ?>
							<th style="text-align: center;">Remove Club</th>
							<th style="text-align: center;">Activate/ Deactivate</th>
					<?php } ?>
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
				if($_SESSION['superAdmin'] == true){
					$query = $pdo->prepare('SELECT * FROM Club');
				} else {
					$clubs = array();
					$adminQry = $pdo->prepare('SELECT ClubId FROM Admins WHERE Email=? AND ClubId != 0');
					$adminQry->execute([$_SESSION['user']]);
					while($admin = $adminQry->fetch()){
						array_push($clubs, $admin['ClubId']);
					}
					$ids = join(",",$clubs);
					$query = $pdo->prepare('SELECT * FROM Club WHERE ClubId IN ('.$ids.')');
				}
						$query->execute();
						while($row = $query->fetch()){ ?>
							<form name="form" action="./EditClub.php" class="form-signin" method="post">
								<tr>
									<td style="padding: 5px;"><input type="hidden" name="club" value="<?=$row['ClubId']?>"><?=$row['ClubId']?></td>
									<td style="padding: 5px;"><input type="hidden" name="name" value="<?=$row['ClubName']?>"><?=$row['ClubName']?></td>
									<td style="padding: 5px;"><input type="hidden" name="address" value="<?=$row['Address']?>"><?=$row['Address']?></td>
									<td style="padding: 5px;"><input type="hidden" name="clubEmail" value="<?=$row['ClubEmail']?>"><?=$row['ClubEmail']?></td>
									<td style="padding: 5px;"><input type="hidden" name="president" value="<?=$row['President']?>"><?=$row['President']?></td>
									<td style="padding: 5px;"><input type="hidden" name="phone" value="<?=$row['PhoneNum']?>"><?=$row['PhoneNum']?></td>
									<td style="padding: 5px;"><input type="hidden" name="presEmail" value="<?=$row['PresidentEmail']?>"><?=$row['PresidentEmail']?></td>
									<td style="padding: 5px;"><input type="hidden" name="allowed" value="<?=$row['AllowedOHRV']?>"><?=$row['AllowedOHRV']?></td>
									<td>
										<button type="submit" name="clicked" value="edit" class="btn btn-link btn-xs">
											<h4><span class="glyphicon glyphicon-pencil"></span></h4>
										</button>
									</td>
									<?php
										if($_SESSION['superAdmin'] == true){ ?>
											<td>
											<?php
												if($row['ClubId'] == 0){ ?>
												Cannot Remove club! </td>
											<?php 
												} else { ?>
												<button type="submit" name="clicked" value="remove" class="btn btn-link btn-xs" onclick="return confirm('Are you sure you want to remove this club?');">
													<h4><span class="glyphicon glyphicon-trash"> </span></h4>
												</button>
											</td>
											<?php
												if ($row['Active'] == 'n'){
											?>
											<td>
												<button type="submit" name="clicked" value="activate" class="btn btn-link btn-xs" onclick="return confirm('Are you sure you want to activate this club?');">
													<h4><span class="glyphicon glyphicon-ok"> </span></h4>
												</button>
											</td>
										<?php 
												} else { ?>
											<td>
												<button type="submit" name="clicked" value="deactivate" class="btn btn-link btn-xs" onclick="return confirm('Are you sure you want to deactivate this club?');">
													<h4><span class="glyphicon glyphicon-remove"> </span></h4>
												</button>
											</td>
										<?php 	}
											}
										}
										?>
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

