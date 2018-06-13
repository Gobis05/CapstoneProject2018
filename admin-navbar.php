<nav class="navbar navbar-inverse navbar-fixed-top" style="padding-right: 10px">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
	  <ul class="nav navbar-nav navbar-left">
        <li><a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-home"></span></a></li>
		<li><a class="navbar-brand" href="#"><?= $_SESSION['firstName'].' '.$_SESSION['lastName'] ?></a></li>
      </ul>      
	  <?php if(isSet($_SESSION['memID'])){?>
      <?php } ?>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <?php
        if(isSet($_SESSION['Clubs Selected'])){
        ?>
          <li><a href="./cart.php" style="padding-top: 6px; padding-bottom: 0px"><button type="button" class="btn btn-success btn-block">
            Click here to checkout! Clubs selected:<?= $_SESSION['Clubs Selected'] ?> ($<?= $_SESSION['Cost'] ?>.00)<BR></button></a></li>
        <?php
        }
        else{
        ?>
          <li><a href="./cart.php">Click here to checkout! (0 clubs selected) [$0.00]</a></li>
        <?php
        }
        ?>
		<li><a href="pick-club.php">Select Clubs</a></li>
        <li><a href="profile.php">Profile</a></li>
		<li><a href="admin.php">Administration</a></li>
        <li><a href="sign-out.php">Sign Out</a></li>
      </ul>
    </div>
  </div>
</nav>
