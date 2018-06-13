<?php
	session_start();
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

		<title>Dashboard Template for Bootstrap</title>

		<!-- Bootstrap core CSS -->
		<link href="./css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="./css/dashboard.css" rel="stylesheet">

		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css"
		integrity="sha512-M2wvCLH6DSRazYeZRIm1JnYyh22purTM+FDB5CsyxtQJYeKq83arPe5wgbNmcFXGqiSH2XR8dT/fJISVA1r/zQ=="
		crossorigin=""/>

		<!-- Make sure you put this AFTER Leaflet's CSS -->
		<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"
		integrity="sha512-lInM/apFSqyy1o6s89K4iQUKg6ppXEgsVxT35HbzUupEVRh2Eu9Wdl4tHj7dZO0s1uvplcYGmt3498TtHq+log=="
		crossorigin=""></script>

		<link rel="stylesheet" href="./css/MarkerCluster.css" />
		<link rel="stylesheet" href="./css/MarkerCluster.Default.css" />
		<script src="./js/leaflet.markercluster-src.js"></script>

		<script src="./js/mapping.js"></script>
		<script src = "./js/pick-club.js"></script>
		
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
		<div class="container-fluid"  style="margin: 0 auto !important; ">
			<div style="margin: 0 auto !important; ">
				<?php ////include('sidebar.php');?>
				<!--<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">-->
					<h2 class="section">Choose from the list of OHRV Clubs or from the map below. <BR>Can’t decide on which club to join?  Use the button to select a club at random.</h2>
				<!--</div>-->
				<!--<div class="row placeholders" style="height: auto;">-->
					<?php
					//Database info
					$host = "localhost";
					$database = "NHOHVA";
					$user = "mg1021";
					$password = "goodspec";
					$charset = "utf8";
					$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
					$opt = [
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
						PDO::ATTR_EMULATE_PREPARES   => false,
					];
					//Creates a database object
					$pdo = new PDO($dsn, $user, $password, $opt);
					$clubs = array();
					//We get the clubIds the user is NOT in and put them into an array
					if (isset($_SESSION['memID'])){
						$userqry = $pdo->query('SELECT ClubId, Active FROM Club WHERE ClubId NOT IN (SELECT ClubId FROM Membership WHERE NHOHVAId = ?) AND ClubId NOT IN (SELECT ClubId FROM Cart WHERE Email = ?) AND ClubId != 0');
						$userqry->execute([$_SESSION['memID'], $_SESSION['email']]);
						while($row = $userqry->fetch()){
							if ($row['Active'] == 'y'){
								array_push($clubs, $row['ClubId']);
							}
						}
					} else if (isset($_SESSION['valid'])) {
						$userqry = $pdo->query('SELECT ClubId, Active FROM Club WHERE ClubId NOT IN (SELECT ClubId FROM Cart WHERE Email = ?) AND ClubId != 0');
						$userqry->execute([$_SESSION['email']]);
						while($row = $userqry->fetch()){
							if ($row['Active'] == 'y'){
								array_push($clubs, $row['ClubId']);
							}
						}
					} else{
						$userqry = $pdo->query('SELECT ClubId, Active FROM Club');
						while($row = $userqry->fetch()){
							if ($row['Active'] == 'y'){
								array_push($clubs, $row['ClubId']);
							}
						}
					}
					//For each club we retrieve and display the info
					foreach($clubs as $clubId){
						$clubqry = $pdo->prepare('SELECT Logo, ClubId, Address, URL, ClubEmail, PhoneNum, ClubName, AllowedOHRV, MaxSize FROM Club WHERE ClubId = ?');
						$clubqry->execute([$clubId]);
						$row = $clubqry->fetch(); ?>
						<div class="image">
							<center><a href="<?= $row['URL'] ?>"><img src="<?= $row['Logo'] ?>" width="200" height="200" class="img-responsive" alt="<?= $row['ClubName'] ?>"></a>
							<p class="contact-info"><?= $row['Address'] ?> <BR>
							Club #<?= $row['ClubId'] ?>&nbsp;&nbsp; <?php
							if ($row['AllowedOHRV'] != ''){ ?>
								(<?= $row['AllowedOHRV'] ?>) <?php
							} ?> <BR> <?php
							if($row['MaxSize'] != 0){ ?>
								Max Machine Size: <?= $row['MaxSize'] ?>" <?php
								if ($row['ClubId'] == 14){ ?>
									(Up to 50" on Rail Trail) <?php
								}
							} ?></p>
							<form action="add-to-cart.php" method="post">
								<select name = "club" class="form-control">
									<option disabled selected>Membership Options</option>
									<?php
										$clubqry2 = $pdo->prepare('SELECT * FROM Fee WHERE ClubId = ?');
										$clubqry2->execute([$clubId]);
										$fee = $clubqry2->fetch();
										$fees = array();
										array_push($fees, $fee['Single'], $row['GoldSingle'], $row['Family'], $row['GoldFamily'], $row['PlatinumSponser'], $row['GoldSponser'], $row['SilverSponser'], $row['Business']);
										//If there is a single option
										if ($fee['Single'] != 0){ ?>
											<option value = "<?= $fee['ClubId'] ?>|Single">Single <?= $fee['Single'] ?></option>
											<?php
										//If there is a Family option
										} if ($fee['Family'] != 0){ ?>
											<option value = "<?= $fee['ClubId'] ?>|Family">Family <?= $fee['Family'] ?></option>
											<?php
										//If there is a FlatFee option
										} if ($fee['Flat'] != 0){ ?>
											<option value = "<?= $fee['ClubId'] ?>|Flat">Flat <?= $fee['Flat'] ?></option>
											<?php
										} ?>
								</select></center>
								<center><input type="submit" value="Add to Cart" /></center>
							</form>
						</div> <?php
					} ?>
				<!--</div>-->
			</div>
		</div>
		<div class="container-fluid safe">
			<div class="row safe">
				<div class="row placeholders safe">
				   <h1 class="sectionTwo">Use the map to select a club where you like to ride.</h1>
					<!-- This is where our map displays -->
					<div id="map"></div>
					<form action="random-join.php" method="post">
						<input onclick="randomButton()" type="submit" id="randomButton" value="Sign Up For a Club at Random" />
					</form>
				</div>
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
