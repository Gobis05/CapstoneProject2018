<div class="col-sm-3 col-md-2 sidebar">
  <ul class="nav nav-sidebar">
    <?php 
		if(basename($_SERVER['PHP_SELF']) == 'index.php'){ ?>
			<li class="active"><a href="#">Overview<span class="sr-only">(current)</span></a></li>
    <?php
		} else {
    ?>
			<li><a href="./index.php">Overview</a></li>
    <?php 
		}
		if(basename($_SERVER['PHP_SELF']) == 'pick-club.php'){ ?>
			<li class="active"><a href="#">Select Clubs<span class="sr-only">(current)</span></a></li>
    <?php
		} else{
    ?>
			<li><a href="./pick-club.php">Select Clubs</a></li>
    <?php 
		}
		if(isset($_SESSION['memID'])){
			if(basename($_SERVER['PHP_SELF']) == 'membership-id.php'){ ?>
				<li class="active"><a href="#">Membership ID<span class="sr-only">(current)</span></a></li>
      <?php
			} else{
      ?>
				<li><a href="./membership-id.php">Membership ID</a></li>

    <?php 
			}
		}
		/*if(isset($_SESSION['memID']) && ($_SESSION['admin'] == true)){
			if(basename($_SERVER['PHP_SELF']) == 'admin.php'){ ?>
				<li class="active"><a href="#">Administration<span class="sr-only">(current)</span></a></li>
      <?php
			} else{
      ?>
				<li><a href="./admin.php">Administration</a></li>

    <?php 
			}
		}*/
		if(isset($_SESSION['memID'])){
			if(basename($_SERVER['PHP_SELF']) == 'calendar.php'){ ?>
				<li class="active"><a href="#">Calendar<span class="sr-only">(current)</span></a></li>
      <?php
			} else {
      ?>
				<li><a href="./calendar.php">Calendar</a></li>
    <?php 
			}
		}
		if(isset($_SESSION['memID'])){
			if(basename($_SERVER['PHP_SELF']) == 'club-news.php'){ ?>
				<li class="active"><a href="#">Club News<span class="sr-only">(current)</span></a></li>
    <?php
			} else {
      ?>
				<li><a href="./club-news.php">Club News</a></li>
    <?php 
			}
		}
		if(basename($_SERVER['PHP_SELF']) == 'support.php'){ ?>
			<li class="active"><a href="#">Support<span class="sr-only">(current)</span></a></li>
    <?php
		} else {
    ?>
			<li><a href="./support.php">Support</a></li>
    <?php 
		} ?>
  </ul>
</div>
