<?php
session_start();
//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
if(!isset($_SESSION['valid'])){
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
}
include('dashboard-header.html');

//Database info
$host = "localhost";
$database = "NHOHVA";
$user = "mg1021";		$password = "goodspec";
$charset = "utf8";
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$opt = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];
//Creates a database object
$pdo = new PDO($dsn, $user, $password, $opt);

?>
<html>
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
		// print_r($clubs);
		?>
		<div id="header">
			<h2>Administration Dashboard</h2>
			<a href="Users.php"><button class="myButton" type="submit">Manage Users</button></a>
			<a href="Clubs.php"><button class="myButton" type="submit">Manage Clubs</button></a>
			<a href="Admins.php"><button class="myButton" type="submit">Manage Admins</button></a>				

		<form action="Scripts/reports.php" method="post" class="form-signin">
			<select name="report" class="form-control" placeholder="Generate a Report" required>
				<option disabled selected value>Generate a Report</option>
				<?php if($_SESSION['superAdmin'] == 1){ ?>
					<option value="notInClub">Users not in a club</option>
				<?php }?>
				<option value="userByClubCount">Number of Users by Club</option>
			</select><BR>
			<button class="myButton" type="submit">Create Report</button>
		</form>
		</div>
									<?php 
		if($_SESSION['superAdmin'] == 1){
			$numQry = $pdo->prepare('SELECT COUNT(Email) AS Num FROM User WHERE Email NOT IN(SELECT Email from Membership)');
			$numQry->execute();
			$total = $numQry->fetch();
			$num = $total['Num']; ?>
			<center><h2>Number of Users not in a Club: <?=$num?></h2></center>
			<?php
		} ?>

		<div id="graph-div">
			<div id="totalUsersChart" class="chart" style="height:370px; width:800px;"></div>
			<div id="newUsersChart" class="chart" style="height:370px; width:800px;"></div>
		</div>
		
		<?php	
			$adminOf = array();
			//check if the user is a superAdmin... If they are not, find the clubIds that they are admins of
			if($_SESSION['superAdmin'] == 1){
				$adminQry = $pdo->prepare('SELECT ClubName, ClubId from Club WHERE ClubId != 0 ORDER BY ClubName');
				$adminQry->execute();
				//Put the ids into an array
				while($admin = $adminQry->fetch()){
					array_push($adminOf, $admin['ClubId']);
				}
			} else {
				$adminQry = $pdo->prepare('SELECT ClubId FROM Admins WHERE Email=?');
				$adminQry->execute([$_SESSION['email']]);
				//Put the ids into an array
				while($admin = $adminQry->fetch()){
					array_push($adminOf, $admin['ClubId']);
				}
			}
			//join the array into a comma seperated list for the query
			$ids = join(",",$adminOf);
			$adminQry2 = $pdo->prepare('SELECT ClubName, ClubId from Club WHERE ClubId !=0 AND ClubId IN ('.$ids.')ORDER BY ClubName');
			$adminQry2->execute();
			$clubs = array();
			$numOfMem = array();
			$barMax = 0;
			
			//Get the year for NHOHVAId comparison
			$year = date('y'); 
			//Get the total number of users...
			$totalCount = $pdo->prepare('SELECT COUNT(ClubId) AS TotalNumMem FROM Membership WHERE ClubId IN ('.$ids.') AND SUBSTR(NHOHVAId, 1, 2) = '.$year);
			$totalCount->execute();
			$rowTotal = $totalCount->fetch();
			$totalMem = $rowTotal['TotalNumMem'];
			
			//Loop through each clubId and get the number of members in that club
			while($row = $adminQry2->fetch()){				
				//Get the number of users in the current club...
				$memberCount = $pdo->prepare('SELECT COUNT(ClubId) AS NumOfMembers FROM Membership WHERE ClubId =? AND SUBSTR(NHOHVAId, 1, 2) = '.$year);
				$memberCount->execute([$row['ClubId']]);
				$row2 = $memberCount->fetch();
				
				if ($row2['NumOfMembers'] > $barMax){
					$barMax = $row2['NumOfMembers'];
				}
				
				//echo $row2['NumOfMembers'];
				//print_r($row2);
				array_push($clubs, $row["ClubName"]);
				array_push($numOfMem, $row2['NumOfMembers']);
				
				
				
			}
			//echo $barMax;
			
			// print_r($clubs);
			// print_r($numOfMem);
			// print_r($totalMem);

		$circleData = "";
		$barData = "";
		$year = getDate();
		$year = $year['year'];
		for($i = 0; $i < sizeof($clubs); $i++){
			$circleData .= "{ y: ".$numOfMem[$i].", label: '".$clubs[$i]."' },";
			$barData .= "{ y: ".$numOfMem[$i].", label: '".$clubs[$i]."' },";
		}
		echo("<script>
		window.onload = function () {

			var circleChart = new CanvasJS.Chart('totalUsersChart', {
				animationEnabled: true,
				title:{
					text: 'Total Members: ".$totalMem."',
					horizontalAlign: 'center'
				},
				data: [{
					click: function(e){
						var club = e.dataPoint.label
						//alert(club);
						sessionStorage.setItem(\"clubName\", club);
						window.open(\"http://turing.plymouth.edu/~mg1021/NHOHVA/Users-Chart-Transfer.php\",'Members of e.dataPoint.label','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no');
						
					},
					type: 'doughnut',
					startAngle: 60,
					//innerRadius: 60,
					indexLabelFontSize: 14,
					indexLabel: '{label} - #percent%',
					toolTipContent: '<b>{label}:</b> {y} (#percent%)',
					dataPoints: [".$circleData."]
				}]
			});
			circleChart.render();
		
			var chart = new CanvasJS.Chart('newUsersChart', {
				animationEnabled: true,
				
				title:{
					text:'New Users by Club in ".$year."'
				},
				axisX:{
					interval: 1
				},
				axisY2:{
					interlacedColor: 'rgba(1,77,101,.2)',
					gridColor: 'rgba(1,77,101,.1)',
					title: 'Number of New Users'
				},
				data: [{
					type: 'bar',
					name: 'clubs',
					axisYType: 'secondary',
					color: '#014D65',
					dataPoints: [ ".$barData."]
				}]
			});
			chart.render();

			}
		</script>");
		////alert(  \"dataSeries Event => Type: \"+ e.dataSeries.type+ \", dataPoint { y: \"+ e.dataPoint.y + \" }\" );
		?>
		
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
		<script src="./js/bootstrap.min.js"></script>
		<script src="./js/canvasjs.min.js"></script>
	</body>
</html>

