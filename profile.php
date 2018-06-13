<?php
/* This page is a modified verison of this MIT licensed software: https://bootsnipp.com/snippets/nPvnk
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies
or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
   session_start();
   //Check if the user is already verified. If not, then checks credentials.
   if(!isset($_SESSION['valid'])){
     header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
   }
   include('dashboard-header.html');
   $host = "localhost";
   $database = "NHOHVA";
   $user = "mg1021";   $password = "goodspec";
   $charset = "utf8";
   $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
   $opt = [
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES   => false,
   ];
   //Creates a database object
   $pdo = new PDO($dsn, $user, $password, $opt);
   $userqry = $pdo->prepare('SELECT Email, FirstName, LastName, DoB, Address, FamilyStatus, PhoneNum, Zip, SpouseFirstName, SpouseLastName, OptIn FROM User WHERE Email = ?');
   $userqry->execute([$_SESSION['email']]);
   $row = $userqry->fetch();
   if($row['FamilyStatus'] == 'm'){
	   $martialStatus = "Married";
   } else if ($row['FamilyStatus'] == 's'){
	   $martialStatus = "Single";
   } else {
	   $martialStatus = "Not Specified";
   }
   $fullName = $row['FirstName'].' '.$row['LastName'];
   $spouseName = $row['SpouseFirstName'].' '.$row['SpouseLastName'];
   $address = $row['Address'].' '.$row['Zip'];
   $DoB = date("M d, Y", strtotime($row['DoB']));
   $optIn = 'No';
   if ($row['OptIn'] == 'y'){
	   $optIn = 'Yes';
   }
   ?>
   <style>
   .col-xs-6{float: left; width : 122px;}
   #countdown {
     margin:0 auto;
   }
   #countdown th {
     padding: 10px;
     text-align: center;
     border-top: 2px solid black;
     border-left: 2px solid black;
     border-right: 2px solid black;
   }
   #countdown td {
     padding: 15px;
     font-size: 4rem;
     border: 2px solid black;

   }</style>
   <?php
   if (isset($_SESSION['valid']) && ($_SESSION['admin'] == true)){
		include('admin-navbar.php');
    } else {
		include('navbar.php');
    } 
	if (isset($_SESSION['memID'])){
		$idCard =$_SESSION['firstName'].$_SESSION['lastName']."NHOHVAIdCard".$_SESSION['memID'];
	}
   ?>
<!--<table><tr><td>-->
		<!-- Displays the image -->
		<BR>
		<!--</td><td>-->
<div class="container">
	<div class="row">
    <?php ////include('sidebar.php') ?>
      <div id="profileBox">
        <div class="panel panel-default">
          <div class="panel-heading">
            <table style="width:100%">
				<tr>
					<td><h4 >User Profile</h4></td>
					<td align="right"><h4><a href="edit-profile.php">Edit Info <span class="glyphicon glyphicon-pencil"> </span></a></h4></td>
				</tr>
			</table>
          </div>
          <div class="panel-body">
            <div class="box box-info">
              <div class="box-body">
                <div id="profileInfo">
              <div class="clearfix"></div>
                <hr style="margin:5px 0 5px 0;">
                <div class="col-sm-5 col-xs-6 tital " >Name:</div><div class="col-sm-7 col-xs-6 "><?= $fullName ?></div>
                <div class="clearfix"></div>
                <div class="bot-border"></div>
				<div class="col-sm-5 col-xs-6 tital " >Date Of Birth:</div><div class="col-sm-7"><?= $DoB ?></div>
                <div class="clearfix"></div>
                <div class="bot-border"></div>
				<?php
                if($martialStatus == "Married"){
                ?>
                  <div class="col-sm-5 col-xs-6 tital " >Spouse Name:</div><div class="col-sm-7"><?=$spouseName?></div>
                <?php
                }
                ?>
				<div class="clearfix"></div>
                <div class="bot-border"></div>
                <div class="col-sm-5 col-xs-6 tital " >Address:</div><div class="col-sm-7"><?= $address ?></div>
                <div class="clearfix"></div>
                <div class="bot-border"></div>
				<?php
                if($row['PhoneNum'] != ""){
                ?>
                  <div class="col-sm-5 col-xs-6 tital " >Phone #:</div><div class="col-sm-7"><?=$row['PhoneNum']?></div>
                <?php
                }
                ?>
                <div class="clearfix"></div>
                <div class="bot-border"></div>
				<div class="col-sm-5 col-xs-6 tital " >Email:</div><div class="col-sm-7"><?= $row['Email'] ?></div>
                <div class="clearfix"></div>
                <div class="bot-border"></div>
				<div class="col-sm-5 col-xs-6 tital " >Opt-In:</div><div class="col-sm-7"><?= $optIn ?></div>
                <div class="clearfix"></div>
                <div class="bot-border"></div>
            <!-- /.box-body -->
              </div>
          <!-- /.box -->
            </div>
          </div>
        </div>
      </div>
   </div>
   <a href="update-password.php"><button class="btn btn-lg btn-primary btn-block" style="width: 400px; display: block; margin: auto">Update your password</button></a>
		<BR><BR><!--</table></tr></td>-->
</div>
<div>
<?php
      if (isset($_SESSION['memID'])){ ?>
    <center><h3>Time until expiration:</h3></center>
    <table id="countdown">
    <tr>
      <th>MTH</th><th>DAY</th><th>HRS</th><th>MIN</th><th>SEC</th>
    </tr>
    <tr>
      <td id="mth"></td><td id="day"></td><td id="hrs"></td><td id="min"></td><td id="sec"></td>
    </tr>
    <table>
    <script>
		function getDaysInMon(year,month) {
			return new Date(year, month, 0).getDate(); // 0 + number of days
		}
		var day = getDaysInMon(new Date().getFullYear(),new Date().getMonth()+1) - 1;

		var date = new Date();
		var year = date.getFullYear();

		var expDate = new Date("Jan 1, "+(year+1)+" 00:00:00").getTime();
		var countDown = setInterval(function(){
        var curTime = new Date().getTime();
		//console.log(new Date().getDate());
        var timeLeft = expDate - curTime;
        //var day = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        var mth = 12 - (new Date().getMonth()+1);
        var hrs = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var min = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        var sec = Math.floor((timeLeft % (1000 * 60)) / 1000);
        document.getElementById("mth").innerHTML = mth;
        document.getElementById("day").innerHTML = day;
        document.getElementById("hrs").innerHTML = hrs;
        document.getElementById("min").innerHTML = min;
        document.getElementById("sec").innerHTML = sec;

        
		if(timeLeft < 0){
          clearInterval(countDown);
          document.getElementById("countdown-timer").innerHTML = "EXPIRED. PLEASE RENEW IMMEDIATELY.";
        }
      }, 1000);

    </script>
  </div>
  <div class="container-fluid">
    <div class="row">
        <div>
          <center><h3>Your NHOHVA ID Card</h3></center>
          <!-- Generates and displays the image -->
          <a href="http://turing.plymouth.edu/~mg1021/NHOHVAIdCards/<?=$idCard?>.png"><img id="id" src="http://turing.plymouth.edu/~mg1021/NHOHVAIdCards/<?=$idCard?>.png"/></a>
        <BR><BR></div>
      <?php 
      } ?>
    </div>
  </div>