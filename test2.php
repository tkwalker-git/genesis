<?php
	include_once('admin/database.php');
	include_once('site_functions.php');
	$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];


// echo basename("http://localhost/site/category/live-entertainment.html");


/*$res = mysql_query("select * from `event_ticket`");
while($row = mysql_fetch_array($res)){
	$ticket_id	= $row['id'];
	$qty		= $row['quantity_available'];
	mysql_query("update `event_ticket_price` set `qty`='$qty' where `ticket_id`='$ticket_id'");
}
*/


/*  print_r(getGeoLocation2('Mucario Law, PLLC'));
	
	
function getGeoLocation2($address)
{
	$prepAddr = str_replace(' ','+',$address.", Orlando,FL");
    $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false'); 
    $output= json_decode($geocode);
	$latlng = array();
    $latlng['lat'] = $output->results[0]->geometry->location->lat;
    $latlng['lng'] = $output->results[0]->geometry->location->lng;
	return $latlng;
}*/


	$res		= mysql_query("select * from `floridazipcodes`");
	$match		= 0;
	$no_match	= 0;
	while($row	= mysql_fetch_array($res)){
	$Zipcode	= $row['Zipcode'];
	
	$res2		= mysql_query("select * from `zipcodes` where `zipcode`='".$Zipcode."'");
		if(mysql_num_rows($res2))
			$match++;
		else
			$no_match++;
	}
	
	echo $match." Zip Codes already in our database and ".$no_match." are not";


//base64_decode($d);
	
//	$mainTicketId	= getSingleColumn("ticket_id","select * from `event_ticket_price` where `id`='$ticket_id'");
//	$event_id		= getSingleColumn("event_id","select * from `event_ticket` where `id`='$mainTicketId'");
//	
//	
//	$resOrder = mysql_query("select * from `orders` where `main_ticket_id`='$event_id' && `total_price`!='' && `total_price`!='0' && `type`='ticket'");
//	while($rowOrder = mysql_fetch_array($resOrder)){
//		$order_id
//	
//	
//	}


	


//	$qry = "SELECT * FROM `venues` WHERE `venue_zip`=0 || `venue_zip`=''";
//	$res = mysql_query($qry);
//	while($row = mysql_fetch_array($res)){
//		echo $row['id'].'<br />';	
//	}


//
//	$qry = "SELECT * FROM `venue_events`";
//	$res = mysql_query($qry);
//	while($row = mysql_fetch_array($res)){
//		$venue_id	= $row['venue_id'];
//		$event_id	= $row['event_id'];
//		$zip	= getSingleColumn("venue_zip","select * from `venues` where `id`='$venue_id'");
//		if($zip)
//			mysql_query("UPDATE `events` SET `zipcode` = '$zip' WHERE `id` = '$event_id'");
//		
//	}
	
	
//	$zip	= '32839';
//	$geoLocation		= getGeoLocation($zip);
//	$lat	= $geoLocation['lat'];
//	$lng	= $geoLocation['lng'];
	
//	$qryZip	= "select DISTINCT zipcode from zipcodes where (( ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS(( ".$lng." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1) <= 25 and ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS(( ".$lng." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1) >= 0 and  latitude!=''";

//	$resZip	= mysql_query($qryZip);
//	$i=0;
//	while($rowZip	= mysql_fetch_array($resZip)){
//	$i++;
//		if($i!=mysql_num_rows($resZip))
//			$coma	= ',';
//		else
//			$coma	= '';
//		$showZip	.= $rowZip['zipcode'].$coma;
//	}
	
// $_SESSION['userZip'] = $showZip;


/*	if ($_SERVER["SERVER_PORT"] != "80")
		$pageURL = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	else
		$pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			

echo $pageURL;*/



/*$sql = "load data LOCAL infile 'zipcodes.csv' 
   into table zipcodes 
   fields terminated by ',' 
   ENCLOSED BY '\"' 
   lines terminated by '\n' 
   IGNORE 1 LINES 
   (id,zipcode,city,state,county,acreacode,latitude,longitude,timezone)";
  
 mysql_query($sql);*/


//
//$qry = "SELECT * FROM `orders` WHERE `main_ticket_id` = '23277'";
//$res = mysql_query($qry);
//while($row = mysql_fetch_array($res)){
//	echo $row['id'].",";
//}





?>