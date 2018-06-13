<!DOCTYPE html>
<html>
<head>
Google Maps
</head>
<body>
<form action="" method="post">
<input type="text"  id="textboxid" value="" name="textboxid">
<input type="submit" value="Get">
</form>
 
</body>
</html>
<?php if(isset($_POST["textboxid"])) {
 
	echo $address = urlencode($_POST["textboxid"]);
	 
	// You can also specify country as a default value
	//$url ='http://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false&components=country:US';
	 
	$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false';
	 
	//key=AIzaSyDShKc9KWCKQGYvwBKJLKdrog6HA1ow79k

	
	
	$geocode = file_get_contents($url);
	$results = json_decode($geocode, true);
	 
	if ($results['status'] == 'OK') {
	$lat = $results['results'][0]['geometry']['location']['lat'];
	$long = $results['results'][0]['geometry']['location']['lng'];
	}
	 
	echo " Lat:" . $lat . " ";
	echo "Lat:" . $long . " ";
	if ($results['status'] == 'OK') {
	if (count($results['results']) > 1) {
	   echo "Multiple address found";
	}
	if (count($results['results']) == 1) {
	if (isset($results['results'][0]['partial_match'])) {
	if ($results['results'][0]['partial_match']) {
	echo "This is a partially right address";
	}
	} else {
	echo "Valid address";
	}
	}
	} else {
	echo "Invalid address";
	}
	 
	//To view the complete response
	echo "
	<pre>";
	print_R($results);
	exit;
}
?>