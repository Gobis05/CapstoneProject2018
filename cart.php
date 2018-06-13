<?php
  /* This page is a modified verison of this MIT licensed software: https://bootsnipp.com/snippets/featured/shopping-cart-panel-bs-3
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
    if(!isset($_SESSION['Attempted club']) || !isset($_SESSION['Attempted Fee'])){
      header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/index.html");
    }
  }
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

     <title>NHOHVA Cart</title>

     <!-- Bootstrap core CSS -->
     <link href="./css/bootstrap.min.css" rel="stylesheet">

     <!-- Custom styles for this template -->
     <link href="./css/dashboard.css" rel="stylesheet">
     <script src="./js/cart.js"></script>
     <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
     <!--[if lt IE 9]>
       <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
       <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
     <![endif]-->
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
<!--<div class="container-fluid">-->
	<div class="row">
	<?php //include('sidebar.php') ?>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">
						<div class="row">
							<div class="col-xs-6">
								<h5><span class="glyphicon glyphicon-shopping-cart" style="float left"></span> Shopping Cart</h5>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
          <?php
          $_SESSION['checkout'] = array();
          //Database info
          $host = "localhost";
          $database = "NHOHVA";
          $user = "mg1021";          $password = "goodspec";
          $charset = "utf8";
          $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
          $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
          ];
          //Creates a database object
          $pdo = new PDO($dsn, $user, $password, $opt);
		  $total=2;
        if(isset($_SESSION['valid'])){
          $cartqry = $pdo->prepare('SELECT ClubId, MemType FROM Cart WHERE Email = ?');
          $cartqry->execute([$_SESSION['email']]);
          //for each club in the cart

          while($row = $cartqry->fetch()){
            $clubId = $row['ClubId'];
			$memType = $row['MemType'];
            array_push($_SESSION['checkout'], $clubId);
			$feeqry = $pdo->prepare('SELECT F.'.$memType.', C.ClubName, C.Logo FROM Club C, Fee F WHERE C.ClubId = ? && F.ClubId= ?');
            $feeqry->execute([$clubId, $clubId]);
            $row = $feeqry->fetch();
            $fee = $row[$memType];
			$total+= $fee;
            $clubName = $row['ClubName'];
            $logo = $row['Logo'];
			$feeqry2 = $pdo->prepare('SELECT * FROM Fee WHERE ClubId = ?');
            $feeqry2->execute([$clubId]);
            $row2 = $feeqry2->fetch();
          ?>
					<div class="row">
						<div class="col-xs-2"><img class="img-responsive" alt="<?= $clubName ?>" src="<?= $logo ?>">
						</div>
						<div class="col-xs-4">
							<h4 id="fee"><strong>$<?= $fee ?> <span class="text-muted"></span></strong></h4>
							<h4 class="product-name"><strong><?= $clubName ?></strong></h4><h4><small><?=$memType?> Membership</small></h4>
						</div>

						<div class="col-xs-6">
							<div class="col-xs-2">
								<form action="update-cart.php" method="post">
									<button name="remove" value="<?=$clubId?>|<?=$memType?>"class="btn btn-link btn-xs">
										<h4><span class="glyphicon glyphicon-trash"> </span></h4>
									</button>
								</form>
							</div>
						</div>
					</div>
					<hr>
          <?php
			}
		} else {
           $feeqry = $pdo->prepare('SELECT F.'.$_SESSION['Attempted Fee'].', C.ClubName, C.Logo FROM Club C, Fee F WHERE C.ClubId = ? && F.ClubId= ?');
           $feeqry->execute([$_SESSION['Attempted club'], $_SESSION['Attempted club']]);
           $row = $feeqry->fetch();
		   $fee = $row[$_SESSION['Attempted Fee']];
		   $total += $fee;
           $clubName = $row['ClubName'];
           $logo = $row['Logo'];
           $feeqry2 = $pdo->prepare('SELECT * FROM Fee WHERE ClubId = ?');
           $feeqry2->execute([$_SESSION['Attempted club']]);
           $row2 = $feeqry2->fetch();
        ?>
			<div class="row">
 				<div class="col-xs-2"><img class="img-responsive" alt="<?= $clubName ?>" src="<?= $logo ?>"></div>
 				<div class="col-xs-4">
 					<h4 id="fee"><strong>$<?= $fee ?><span class="text-muted"></span></strong></h4>
 					<h4 class="product-name"><strong><?= $clubName ?></strong></h4><h4><small><?=$_SESSION['Attempted Fee']?> Membership</small></h4>
 				</div>
 				<div class="col-xs-6">
 					<div class="col-xs-2">
 						<form action="update-cart.php" method="post">
 							<button name="remove" value="<?=$_SESSION['Attempted club']?>|<?=$_SESSION['Attempted Fee']?>"class="btn btn-link btn-xs">
 								<h4><span class="glyphicon glyphicon-trash"> </span></h4>
 							</button>
 						</form>
 					</div>
				</div>
 			</div>
 			<hr>
           <?php
         }?>

				</div>
				<div class="row">
					<div class="col-xs-2"><img class="img-responsive" alt="NHOHVA" src="http://turing.plymouth.edu/~mg1021/Images/NHOHVA.jpg"> </div>
					<div class="col-xs-4">
						<h4 id="fee"><strong>$2.00 <span class="text-muted"></span></strong></h4>
						<h4 class="product-name"><strong>NHOHVA</strong></h4><h4><small>$2.00 Transaction Fee</small></h4>
					</div>
				</div><BR>
				<div class="row">
				<div class="col-xs-2"><img class="img-responsive" alt="NHOHVA" src="http://turing.plymouth.edu/~mg1021/Images/NHOHVA.jpg"> </div>
				<div class="col-xs-4">
				<input class="form-control addOption" id="donation" onchange="updateTotal()" type="number" name="donation" style="float: right;" min="1" max="500"">
					<!--<select id="sponsorship" onchange="updateTotal()" class="form-control addOption" name = "sponsor" style="float: right;">
						<option = "No">No thanks</option>
						<option = "1">$1.00</option>
						<option = "5">$5.00</option>
						<option = "10">$10.00</option>
					</select>-->
					<!--<h4 id="fee"><strong><input width="50" type="donation" name="donation" class="form-control" value="0"><span class="text-muted"></span></strong></h4>-->
					<h4 class="product-name"><strong>Would you like to make a donation to NHOHVA?</strong></h4>
					<h4><small>All proceeds go to support affiliated clubs</small></h4>
				</div>
			</div><BR>
				<div class="panel-footer">
					<div class="row text-center">
						<div class="col-xs-9">
							<h4 class="text-right">Total: $<strong id="total"><?= $total ?>.00</strong></h4>
						</div>
						<div class="col-xs-3">
                <?php
                //If there is something to checkout
                if(!empty($_SESSION['checkout'])){
                ?>
				<!-- PayPal button! -->
				<form name="cart" action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_cart">
					<input type="hidden" name="upload" value="1">
					<input type="hidden" name="business" value="harlowdianne59@gmail.com">
					<input type="hidden" name="return" value="http://turing.plymouth.edu/~mg1021/NHOHVA/payment-success.php">
					<input type="hidden" name="cancel_return" value="http://turing.plymouth.edu/~mg1021/NHOHVA/cart.php">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">

					<?php
					$i=1;
					//PayPal values to be passed!
					foreach($_SESSION['checkout'] as $clubId){
						$itemName = "item_name_".$i;
						$itemNumber = "item_number_".$i;
						$itemAmount = "amount_".$i;
						$cartqry2 = $pdo->prepare('SELECT ClubName FROM Club WHERE ClubId = ?');
						$cartqry2->execute([$clubId]);
						$row2 = $cartqry2->fetch();
						$clubName = $row2['ClubName'];

						$memTyprqry = $pdo->prepare('SELECT MemType FROM Cart WHERE ClubId = ? AND Email=?');
						$memTyprqry->execute([$clubId, $_SESSION['email']]);
						$mem = $memTyprqry->fetch();
						$memType = $mem['MemType'];
						$feeqry2 = $pdo->prepare('SELECT '.$memType.' FROM Fee WHERE ClubId = ?');
						$feeqry2->execute([$clubId]);
						$fee2 = $feeqry2->fetch(); ?>
						<input type="hidden" name="<?=$itemName?>" value="<?=$clubName?>">
						<input type="hidden" name="<?=$itemAmount?>" value="<?=$fee2[$memType]?>">
					<?php
						$i++;
					} 
					?>
					<input id ="" type="hidden" name="" value="">
					<?php
					$itemName = "item_name_".$i;
					$itemNumber = "item_number_".$i;
					$itemAmount = "amount_".$i;
					$i++;?>
					<!-- Transaction Fee -->
						<input type="hidden" name="<?=$itemName?>" value="$2 Transaction Fee">
						<input type="hidden" name="<?=$itemAmount?>" value="2.00">
					<?php
						$itemName = "item_name_".$i;
						$itemNumber = "item_number_".$i;
						$itemAmount = "amount_".$i;
					?>
						<!-- Donation -->
						<input type="hidden" name="<?=$itemName?>" value="Donation to NHOHVA">
						<input type="hidden" name="<?=$itemAmount?>" id = "payPalDonation" >
					<?php
					?>
					</form><BR>
                <a href="payment-success.php">
					<button type="button" class="btn btn-success btn-block">
						Test Checkout!
					</button>
				</a>
                <?php
                //If there is nothing to check out
                }else{
					if(isset($_SESSION['Attempted club'])){ ?>
						<p><strong>To continue, you need to create an account</strong></p>
            <a href="registration.php"><button class="btn btn-lg btn-primary btn-block">Create Account</button></a><BR>
			<center><p>Or</p></center>
			<a href="sign-in.php"><button class="btn btn-lg btn-primary btn-block">Sign in</button></a>
					<?php
					} else {
					?>
						<p><strong>Sorry, but there is nothing in the cart!</strong></p>
					<?php
						unset($_SESSION['checkout']);
					}
				} ?>
              </form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!--</div>-->
</body>
