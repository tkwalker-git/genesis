<!DOCTYPE HTML>
<html>
<head>
<script src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position){
			if(document.getElementById('text1').value !="" && document.getElementById('text2').value !="")
			{
							
						var latitude = document.getElementById('text1').value;
       					var longitude = document.getElementById('text2').value;
					
				}
				else
				{
					
					  var latitude = position.coords.latitude;
       					var longitude = position.coords.longitude;
					}
      codeLatLng(latitude,longitude);
        //var coords = new google.maps.LatLng(latitude, longitude);
		  /*var mapOptions = {
			  zoom: 15,
			  center: coords,
			  mapTypeControl: true,
			  navigationControlOptions: {
				  style: google.maps.NavigationControlStyle.SMALL
			  },
            mapTypeId: google.maps.MapTypeId.ROADMAP
            };*/
            /*map = new google.maps.Map(
                document.getElementById("mapContainer"), mapOptions
                );*/
            /*var marker = new google.maps.Marker({
                    position: coords,
                    map: map,
                    title: "Your current location!"
            });*/
 
        });

  function codeLatLng(lat, lng) {
geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
      console.log(results)
	    if (results[1]) 
		{
		var str=results[0].formatted_address;
        var a1 = new Array();
a1=str.split(',');
         
		 //formatted address
         //find country name
          
         document.getElementById("eventlocation").innerHTML = a1[1]+" Events"
      
        } else {
          alert("No results found");
        }
      } else {
        alert("Geocoder failed due to: " + status);
      }
    });
  }

    }else {
        alert("Geolocation API is not supported in your browser.");
    }
	
</script>
<?php
function getLatLong($code){
 $mapsApiKey = 'ABQIAAAAsXKRdV_WKrK53uAcIjCakRSMgB7ul5s6g15-8yzHjti59-jHGxReLNwrIYVCwn9UAdtHt94AL272XA';
 $query = "http://maps.google.com/maps/geo?q=".urlencode($code)."&output=json&key=".$mapsApiKey;
 $data = file_get_contents($query);

 // if data returned
 if($data){
  // convert into readable format
  $data = json_decode($data);
   $long = $data->Placemark[0]->Point->coordinates[0];
  $lat = $data->Placemark[0]->Point->coordinates[1];

  return array('Latitude'=>$lat,'Longitude'=>$long);
 }else{
  return false;
 }
}
if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode'] != "")
{
	$val = getLatLong($_REQUEST['zipcode']);
	
}
?>
