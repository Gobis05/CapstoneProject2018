var map;
var ajaxRequest;
var plotlist;
var plotlayers=[];

window.onload = function() {
	// set up the map
	map = new L.Map('map');

	// create the tile layer with correct attribution
	var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap contributors</a>';
	var osm = new L.TileLayer(osmUrl, {minZoom: 7, maxZoom: 20, attribution: osmAttrib});

	// start the map to New Hampshire
	map.setView(new L.LatLng(43.8, -71.524),7);
	map.addLayer(osm);
  var info = document.getElementsByClassName("info");
	//Through an ajax request, we get the club information from the database
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
  	if (this.readyState == 4 && this.status == 200) {
    	var long_lat = this.responseText;
			//We put make an array with the data from a club in each element/
			var data = long_lat.split("***");
			//Removes the empty element at the end of the array
			data.pop();
			setMapMarkers(map, data);
    }
  };
	xmlhttp.open("GET","get-club-map-info.php",true);
	xmlhttp.send(null);
}

//Sets up the map markers in accordance to the latitudes and longitudes listed, with popus with club info
function setMapMarkers(map, arr){
	for(var i in arr){
		//console.log(i);
    var fullString = arr[i];
		//console.log(fullString);
		var clubData = fullString.split("_");
		//Make easy to understand variable names out of the data passed by the server
		var clubId = clubData[0];
		var longitude = clubData[1];
		var latitude = clubData[2];
		var logoURL = clubData[3];
		var address = clubData[4];
		var url = clubData[5];
		var clubEmail = clubData[6];
		var phoneNum = clubData[7];
		var clubName = clubData[8];
		var single = clubData[9];
		var goldSingle = clubData[10];
		var family = clubData[11];
		var goldFamily = clubData[12];
		var platinumSponser = clubData[13];
		var goldSponser = clubData[14];
		var silverSponser = clubData[15];
		var business = clubData[16];
		var flat = clubData[17];
		var id = {id : clubId};
		//Ajax query to get whether club has been joined, is in cart, or none of those
		$.ajax({
	    url : "./find-if-club-valid.php",
	    type: "POST",
			async: false,
	    data : id,
	    success: function(data, textStatus, jqXHR){
				//If the user hasn't joined the club or added it to cart yet.
				if(data == "true"){
					//Add markers to the longitude and latitude specified by the database
					var marker = L.marker([longitude, latitude]);
					map.addLayer(marker);
					//sets the html to be set in the popup.
					var popUpHtml = '<img src = "' + logoURL + '"style="max-width: 200px" height = "100px" ><h4 class="contact-info">'+
					address + '<p class="contact-info"><span class="text-muted">' + '#' + clubId +
					'<form action="add-to-cart.php" method="post"><select name = "club" class="form-control">' +
					'<option disabled selected>Options</option>';
					/*var popUpHtml = '<img src = "' + logoURL + '"style="max-width: 200px" height = "100px" ><h4 class="contact-info">'+
					address + '</h4> <p class="web"><a href="' + url + '">Website</a></p><p class="contact-info"><span class="text-muted">' +
					clubEmail + '</span></p> <p class="contact-info"><span class="text-muted">' + phoneNum +
					'</span></p><form action="add-to-cart.php" method="post"><select name = "club" class="form-control">';*/
					if (single != 0){
						popUpHtml += '<option value = "' + clubId + '|Single">Single ' + single + ' </option>'
					//If there is a Gold Single option
				} if (goldSingle != 0){
						popUpHtml += '<option value = "' + clubId + '|GoldSingle">GoldSingle ' + goldSingle + ' </option>'
					//If there is a Family option
				} if (family != 0){
						popUpHtml += '<option value = "' + clubId + '|Family">Family ' + family + ' </option>'
					//If there is a Gold Family option
				} if (goldFamily != 0){
					popUpHtml += '<option value = "' + clubId + '|GoldFamily">GoldFamily ' + goldFamily + ' </option>'
					//If there is a Platinum Sponsor option
				} if (platinumSponser != 0){
					popUpHtml += '<option value = "' + clubId + '|PlatinumSponser">PlatinumSponser ' + platinumSponser + ' </option>'
					//If there is a Gold Sponsor option
				} if (goldSponser != 0){
					popUpHtml += '<option value = "' + clubId + '|GoldSponser">GoldSponser ' + goldSponser + ' </option>'
					//If there is a Silver Sponsor option
				} if (silverSponser != 0){
					popUpHtml += '<option value = "' + clubId + '|SilverSponser">SilverSponser ' + silverSponser + ' </option>'
					//If there is a Business option
				} if (business != 0){
					popUpHtml += '<option value = "' + clubId + '|Business">Business ' + business + ' </option>'
				} if (flat != 0){
					popUpHtml += '<option value = "' + clubId + '|Flat">Flat Rate ' + flat + ' </option>'
					}
					//Closing the related tags
					popUpHtml += '</select></center><center><input type="submit" value="Sign Up" /></center>'
					marker.bindPopup(popUpHtml, {
						maxWidth : "auto"
					});
				}
				//If the user has the club in their cart
				else if(data == "in cart"){
					//console.log("in");
					var greenIcon = L.icon({
			    iconUrl: 'marker-icon-red.png',
					iconSize: [25, 41],
  				iconAnchor: [12, 41],
  				popupAnchor: [1, -34],
  				shadowSize: [41, 41]
					});
					var marker = L.marker([longitude, latitude], {icon: greenIcon});
					map.addLayer(marker);
					//sets the html to be set in the popup.
					var popUpHtml = '<img src = "' + logoURL + '"style="max-width: 200px" height = "100px" ><h4 class="contact-info">'+
					address + '<p class="contact-info"><span class="text-muted">' + '#' + clubId +
					'</span></p><center><p>This is already in your cart.</p></center><center><p><a href="./cart.php"><button type="button" class="btn btn-success btn-block">' +
            'Proceed to checkout!</button></a></p></center>'
					marker.bindPopup(popUpHtml, {
						maxWidth : "auto"
					});
				}
				//If the user has already joined the club
				else{
					//Create a default green icon
					var greenIcon = L.icon({
			    iconUrl: 'marker-icon-green.png',
					iconSize: [25, 41],
  				iconAnchor: [12, 41],
  				popupAnchor: [1, -34],
  				shadowSize: [41, 41]
					});
					//Add it to the appropriate coordinates, then add it to map
					var marker = L.marker([longitude, latitude], {icon: greenIcon});
					map.addLayer(marker);
					//creates the html to be set in the popup.
					var popUpHtml = '<img src = "' + logoURL + '"style="max-width: 200px" height = "100px" ><h4 class="contact-info">'+
					address + '<p class="contact-info"><span class="text-muted">' + '#' + clubId +
					'</span></p><center><p><strong>You are already in this club!</strong></p></center>'
					//Binds the html into a pop up on click
					marker.bindPopup(popUpHtml, {
						maxWidth : "auto"
					});
				}
			},
	    error: function (jqXHR, textStatus, errorThrown){
				//console.log("error");
	    }
		});
  }
}
