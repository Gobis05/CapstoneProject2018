"use strict";

var onLeapYear = false;

function validateEmail(mail){
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)){
    return (true)
  }
  return (false)
}
$(document).ready(function(){
  $("form").submit(function(){
    var x = document.forms["form"]["email"].value;
      if(validateEmail(x)){
      }
      else{
        event.preventDefault();
        alert("That is an invalid email. Please submit a proper email.");
      }
  });
});

$(function(){

	var today = new Date();
	var min = today.getFullYear() - 100;
    //populate our years select box
    for (var i = today.getFullYear()-18; i > min; i--){
  		$('#years').append($('<option />').val(i).html(i));
    }
    //populate our months select box
    for (var i = 1; i < 13; i++){
      if(i == 1){
        var thisMonth = "January";
      }
      else if(i == 2){
        var thisMonth = "February";
      }
      else if(i == 3){
        var thisMonth = "March";
      }
      else if(i == 4){
        var thisMonth = "April";
      }
      else if(i == 5){
        var thisMonth = "May";
      }
      else if(i == 6){
        var thisMonth = "June";
      }
      else if(i == 7){
        var thisMonth = "July";
      }
      else if(i == 8){
        var thisMonth = "August";
      }
      else if(i == 9){
        var thisMonth = "September";
      }
      else if(i == 10){
        var thisMonth = "October";
      }
      else if(i == 11){
        var thisMonth = "November";
      }
      else{
        var thisMonth = "December";
      }
  		$('#months').append($('<option />').val(i).html(thisMonth));
    }
    //populate our Days select box
    //updateNumberOfDays();
    //"listen" for change events
    $('#months').change(function(){
      updateNumberOfDays($('#months'),('#years'));
    });

    $('#years').change(function(){
      updateNumberOfDaysSafe($('#months'), $('#years').val());
    });

});


//function to update the days based on the current values of month and year
function updateNumberOfDaysSafe(month, year){
    if(month.val() == 2 && year%4 == 0){
      $('#days').html('');
      var month = $('#months').val();
      var year = $('#years').val();
      var days = daysInMonth(month, year);

      for(var i=1; i < days+1 ; i++){
        $('#days').append($('<option />').val(i).html(i));
      }
      onLeapYear = true;
    }
    else if(onLeapYear){
      $('#days').html('');
      var month = $('#months').val();
      var year = $('#years').val();
      var days = daysInMonth(month, year);

      for(var i=1; i < days+1 ; i++){
        $('#days').append($('<option />').val(i).html(i));
      }
      onLeapYear = false;
    }
}

//function to update the days based on the current values of month and year without resetting day
function updateNumberOfDays(month, year){
    if(month.val() == 2 && year%4 == 0){
      onLeapYear = true;
    }
    else{
      onLeapYear = false;
    }
    $('#days').html('');
    var month = $('#months').val();
    var year = $('#years').val();
    var days = daysInMonth(month, year);

    for(var i=1; i < days+1 ; i++){
		$('#days').append($('<option />').val(i).html(i));
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
