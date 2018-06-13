var baseTotal;
window.onload = function() {
  baseTotal = document.getElementById("total").innerHTML;
}

function updateTotal(){
  var curTotal = document.getElementById("total");
  var donation = document.getElementById("donation");
  var amountOfDonation = donation.value;
  document.getElementById("payPalDonation").value = amountOfDonation;
  if(!amountOfDonation || amountOfDonation.charAt(0) == "."){
    amountOfDonation = "0";
  }
  amountOfDonation = parseInt(amountOfDonation);
  if(amountOfDonation < 0){
    amountOfDonation = 0;
  }
  var newMoney = parseInt(baseTotal) + amountOfDonation;
  curTotal.innerHTML = newMoney + ".00";
}
