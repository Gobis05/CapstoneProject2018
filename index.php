<?php
  session_start();
	//Check if the user is already verified. If not, redirect to check credentials. Otherwise, update $_SESSION['page'] variable
	//if(!isset($_SESSION['valid'])){
		//header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.php");
	//}
  if(isset($_SESSION['memID'])){
    $date=getdate();
    $y = $date['year'];
    $m = $date['mon'];
    $d = $date['mday'];
  }
  include('dashboard-header.html');
 ?>
<html>
<title>Welcome</title>

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
        <?php //include('sidebar.php') ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <?php
          $expired = false;
          if(isset($_SESSION['memID'])){
            $expYear = substr($_SESSION['expirationDate'], 0, 4);
            $expMonth = substr($_SESSION['expirationDate'], 5, 2);
            $expDay = substr($_SESSION['expirationDate'], 8, 2);
            if($y > $expYear || ($y == $expYear && $m > $expMonth) || ($y == $expYear && $m == $expMonth && $expDay > $d)){
              $expired = true;
            }
            if($m > $expMonth){
              $expMonth += 12;
              $y += 1;
            }
            if($d > $expDay){
              $expDay += 31;
              //$m += 1;
            }
            if(!$expired){
              $remainingMonths = 12 - $m;
              $remainingDays = $expDay - $d;
            ?>
              <!--<h1 class="section">Time until renewal</h1>
              <p class="section">You have <strong><?= $remainingMonths ?> months and <?= $remainingDays ?> days</strong>
                until it's time to renew your club membership.</p>-->
              <center><h1 class = "section_two">Your Clubs:</h1></center>
              <div class="row placeholders">
            <?php
            }
          }
          ?>
            <?php
            if(isset($_SESSION['memID'])){
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
              //Creates a database object
              $pdo = new PDO($dsn, $user, $password, $opt);
              $joinedClubs = array();
              //We get the clubs the user has joined, and put them into an array
              $userqry = $pdo->prepare('SELECT ClubId FROM Membership WHERE NHOHVAId = ?');
              $userqry->execute([$_SESSION['memID']]);
              while($row = $userqry->fetch()){
                array_push($joinedClubs, $row['ClubId']);
              }
              //For each club the user has joined, we retrieve and display the info
              foreach($joinedClubs as $clubId){
                $clubqry = $pdo->prepare('SELECT Logo, Address, URL, ClubEmail, PhoneNum, ClubName FROM Club WHERE ClubId = ?');
                $clubqry->execute([$clubId]);
                $row = $clubqry->fetch();

				//Get the Membership type that the user has for that club
				//echo $clubId;
				//echo $_SESSION['email'];
				$clubqry2 = $pdo->prepare('SELECT MembershipType FROM Membership WHERE ClubId = ? AND Email = ?');
                $clubqry2->execute([$clubId, $_SESSION['email']]);
                $row2 = $clubqry2->fetch();
				
                //var_dump($row2);
              ?>
            <div class="image">
              <center><a href="<?= $row['URL'] ?>"><img src="<?= $row['Logo'] ?>" width="200" height="200" class="img-responsive" alt="<?= $row['ClubName'] ?>"></a>
              <p class="contact-info"><?= $row['Address'] ?></p>
			  <p class="membership-type">Membership Type: <?= $row2['MembershipType'] ?></p>
              <p class="web"><a href="<?= $row['URL'] ?>">Website</a></p>
              <p class="contact-info"><span class="text-muted"><?= $row['ClubEmail'] ?></span></p>
              <p class="contact-info"><span class="text-muted"><?= $row['PhoneNum'] ?></span></p></center>
            </div>
            <?php
              }
            ?>
            <?php
            } else {
				?>
				<img src="http://turing.plymouth.edu/~mg1021/Images/NHOHVA.jpg" id="splashImage"/>
				<h3 class="splashParagraph">Welcome to the NHOHVA Club Membership System</h3>
				<p class="splashParagraph">Hosted by: New Hampshire Off Highway Vehicle Association</p>
				<p class="splashParagraph">NH Fish & Game Requirements:<BR><strong>Effective May 1, 2018:</strong> (starting with 2018/2019 registrations), 
				wheeled vehicle registrations will be broken out into member vs. non-club member rates; appropriate proof of membership will be required 
				at the time of registration for club member rates. </p>
				<p class="splashParagraph">Please use this site to support your favorite OHRV clubs and trails. Membership fees are used to promote and 
				maintain the trails we love to ride, so please consider joining more than one. </p>
				<p class="splashParagraph">While you’re here you will also be able choose to become a member of the NH Off Highway Vehicle Association. 
				The association works to promote and foster OHRVing for its diverse members by aiding and guiding with sensible legislation, governing OHRV 
				use and related activities. </p>
				<p class="splashParagraph">NHOHVA works to educate members and the public on good conduct, sportsmanship, and safety and conservation practice. </p>
				<p class="splashParagraph">All club membership terms run January 1st to December 31st</p>
				<p class="splashParagraph">After you have chosen your club memberships you will be able to print out your voucher with a wallet card as proof 
				of club membership. Take your voucher to any of the NH Registration Agents or mail it in to NH F&G along with their OHRV registration application.</p>
				<p class="splashParagraph">ATV (All-Terrain Vehicle, 50”)<BR>UTV (Up to 65”)<BR>TB (Trailbike)</p>
			  </body>
				<?php
			}
             ?>
            </div>
          </div>
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
