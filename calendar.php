<?php
	session_start();
	if(!isset($_SESSION['valid'])){
    header("Location: http://turing.plymouth.edu/~mg1021/NHOHVA/sign-in.php");
  }
 ?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<link href='./css/fullcalendar.min.css' rel='stylesheet' />
<link href='./css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<link href="./css/bootstrap.min.css" rel="stylesheet">
<link href="./css/dashboard.css" rel="stylesheet">
<script src='./js/moment.min.js'></script>
<script src='./js/jquery.min.js'></script>
<script src='./js/fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {

		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay,listWeek'
			},
			defaultDate: '2017-05-12',
			navLinks: true, // can click day/week names to navigate views
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: [
				{
					title: 'All Day Ride in Derry',
					start: '2017-05-01'
				},
				{
					title: 'Club BBQ',
					start: '2017-05-07',
					end: '2017-05-08'
				},
				{
					id: 999,
					title: 'Ride in Perry Stream',
					start: '2017-05-09T08:00:00',
          end: '2017-05-09T13:00:00'
				},
				{
					title: 'Pittsburg ride',
					start: '2017-05-11',
					end: '2017-05-11'
				},
			]
		});

	});

</script>
<style>

	body {
		margin: 40px 10px;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#calendar {
		max-width: 900px;
		margin: 0 auto;
	}

</style>
</head>
<body>
  <?php
	if (isset($_SESSION['valid']) && ($_SESSION['admin'] == true)){
		include('admin-navbar.php');
    } else {
		include('navbar.php');
    }
   ?>

  <div class="container-fluid">
    <div class="row">
      <?php include('sidebar.php') ?>
    <br/><br/><br/>
	  <div id='calendar'></div>
  </div>
</div>

</body>
</html>
