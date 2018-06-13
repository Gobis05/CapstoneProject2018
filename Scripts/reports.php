<?php
session_start();
//Get the type of report being generated...
$report = $_POST['report'];
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
//For file name purposes, get the date
$time = time();
$date = date('Y-m-d', $time);
$str = "";
//Get the year for NHOHVAId comparison
$year = date('y');
if($report == "notInClub"){
	$reportQry = $pdo->prepare('SELECT Email, FirstName, LastName FROM User WHERE Email NOT IN(SELECT Email from Membership)');
	$reportQry->execute();
	$str .= "First Name, Last Name, Email\n";
	//$file = '/home/mg1021/Home/NHOHVAReports/test.txt';
	//Put the ids into an array
	while($row = $reportQry->fetch()){
		$str.=$row['FirstName'].",".$row['LastName'].",".$row['Email']."\n";
	}
	//$text = file_get_contents($file);
	//$text .= $str;

	 
	file_put_contents("../../NHOHVAReports/NotInAClub".$date.".csv", $str);
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/admin.php");
} else if ($report == "userByClubCount"){
	
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
	//echo $ids;
	$adminQry2 = $pdo->prepare('SELECT ClubName, ClubId from Club WHERE ClubId !=0 AND ClubId IN ('.$ids.')ORDER BY ClubName');
	$adminQry2->execute();

	


	$str .= "Club Name, First Name, Last Name, Email\n";
	//Loop through each clubId and get the number of members in that club
	while($row = $adminQry2->fetch()){				
		//Get the user's emails in the current club...
		$members = $pdo->prepare('SELECT Email FROM Membership WHERE ClubId =? AND SUBSTR(NHOHVAId, 1, 2) = '.$year);
		$members->execute([$row['ClubId']]);
		
		//now get their names
		$i=1;
		while($row2 = $members->fetch()){
			$membersInfo = $pdo->prepare('SELECT Email, FirstName, LastName FROM User WHERE Email =?');
			$membersInfo->execute([$row2['Email']]);
			$row3 = $membersInfo->fetch();
			if ($i > 1){
				$str.=",".$row3['FirstName'].",".$row3['LastName'].",".$row3['Email'];
			} else {
				$str.=$row['ClubName'].",".$row3['FirstName'].",".$row3['LastName'].",".$row3['Email'];
			}
			$str.="\n";
			$i++;
		}
		$str.="\n";
		//echo $row2['NumOfMembers'];
		//print_r($row2);
		//array_push($clubs, $row["ClubName"]);
		//array_push($numOfMem, $row2['NumOfMembers']);
	}
	file_put_contents("../../NHOHVAReports/MembersByClub".$date.".csv", $str);
	//echo $str;
	header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/admin.php");
} else {
	$clubNameQry = $pdo->prepare('SELECT ClubName FROM Club WHERE ClubId =?');
	$clubNameQry->execute([$report]);
	$getName = $clubNameQry->fetch();
	$clubName = $getName['ClubName'];
	
	$str .= "Members of ".$clubName.":\nFirst Name, Last Name, Email, DoB, Amount Due, Active?, Phone Number, NHOHVA Id, Issue Date, Expiration Date\n";
	//Get the user's emails in the current club...
		$members = $pdo->prepare('SELECT Email, NHOHVAId, ClubJoinDate, ExpireDate FROM Membership WHERE ClubId =? AND SUBSTR(NHOHVAId, 1, 2) = '.$year);
		$members->execute([$report]);
		
		//now get their names
		while($row2 = $members->fetch()){
			$membersInfo = $pdo->prepare('SELECT Email, FirstName, LastName, DoB, AmountDue, Active, PhoneNum FROM User WHERE Email =?');
			$membersInfo->execute([$row2['Email']]);
			$row3 = $membersInfo->fetch();
			$str.=$row3['FirstName'].",".$row3['LastName'].",".$row3['Email'].",".$row3['DoB'].",".$row3['AmountDue'].",".$row3['Active'].",".$row3['PhoneNum'].",".$row2['NHOHVAId'].",".$row2['ClubJoinDate'].",".$row2['ExpireDate'];
			$str.="\n";
		}
		$str.="\n";
		//echo $row2['NumOfMembers'];
		//print_r($row2);
		//array_push($clubs, $row["ClubName"]);
		//array_push($numOfMem, $row2['NumOfMembers']);
	file_put_contents("../../NHOHVAReports/MembersOfClub".$report."-".$date.".csv", $str);
	echo "Successfully exported table to NHOHVAReports! Closing this window. <script>setTimeout(\"window.close();\",3000);</script>";
}
?>