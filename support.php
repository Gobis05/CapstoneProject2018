<?php
  session_start();
  include('dashboard-header.html');
 ?>
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
      <?php include('sidebar.php') ?>
    </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="./js/jquery.min.js"><\/script>')</script>
    <script src="./js/bootstrap.min.js"></script>
  </body>
</html>
