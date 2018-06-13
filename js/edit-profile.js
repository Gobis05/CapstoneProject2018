"use strict";

function validateEmail(mail){
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)){
    return (true)
  }
  //alert("You have entered an invalid email address!")
  return (false)
}
$(document).ready(function(){
  $("form").submit(function(){
    //event.preventDefault();
    //alert("saved");
    var x = document.forms["form"]["email"].value;
      if(validateEmail(x)){
        //event.preventDefault();
        //alert("good");
      }
      else{
        event.preventDefault();
        alert("That is an invalid email");
      }
  });
});

$(function(){

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
  	if (this.readyState == 4 && this.status == 200) {
    	var bday = this.responseText;
			//We put make an array with the data from a club in each element/
			var data = bday.split("-");
			dobOptions(data);
    }
  };
	xmlhttp.open("GET","get-dob.php",true);
	xmlhttp.send(null);

});

function dobOptions(arr){
	var today = new Date();
	var min = today.getFullYear() - 100;
    //populate our years select box
    for (var i = today.getFullYear()-18; i > min; i--){
		if(i != arr[0]){
			$('#years').append($('<option />').val(i).html(i));
		} else {
			$('#years').append($('<option selected />').val(i).html(i));
		}
    }
    //populate our months select box
    for (var i = 1; i < 13; i++){
		if(i != arr[1]){
			$('#months').append($('<option />').val(i).html(i));
		} else {
			$('#months').append($('<option selected />').val(i).html(i));
		}
    }
    //populate our Days select box
    //updateNumberOfDays();
    updateNumberOfDays(arr);
    //"listen" for change events
    $('#years, #months').change(function(){
        updateNumberOfDays(arr);
    });
}

//function to update the days based on the current values of month and year
function updateNumberOfDays(arr){
    $('#days').html('');
    var month = $('#months').val();
    var year = $('#years').val();
    var days = daysInMonth(month, year);

    for(var i=1; i < days+1 ; i++){
		if(i != arr[2]){
			$('#days').append($('<option />').val(i).html(i));
		} else {
			$('#days').append($('<option selected />').val(i).html(i));
		}

    }
}

//helper function
function daysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
}

function singleStatus(){
  document.getElementById('spouseFLabel').style.display = 'none';
  document.getElementById('spouseFInput').style.display = 'none';
  document.getElementById('spouseLLabel').style.display = 'none';
  document.getElementById('spouseLInput').style.display = 'none';
}

function marriedStatus(){
  document.getElementById('spouseFLabel').style.display = 'block';
  document.getElementById('spouseFInput').style.display = 'block';
  document.getElementById('spouseLLabel').style.display = 'block';
  document.getElementById('spouseLInput').style.display = 'block';
}
