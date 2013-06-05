<?php

/* 
	Notes: This script read .ics file and grab the event ids then get the details of each event
*/

include_once("database.php");
include_once('api/facebook.php');
include_once('header.php'); 
?>

<div class="bc_heading">
	<div>FaceBook Events</div>
</div>

<div style="padding:20px">

<?php
$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$a = file_get_contents("http://www.facebook.com/ical/u.php?uid=1387282969&key=AQAXhoE07Bu7lWbD");

$pattern = '/UID:e(.*?)@facebook.com/i';
$m = preg_match_all($pattern, $a, $matches);

$event_ids = implode(",",$matches[1]);

	
$fql = "SELECT eid, name, tagline, pic_small,pic_big,pic, host, description, event_type, event_subtype, creator, location, venue, start_time,end_time FROM event 
		WHERE eid IN (". $event_ids ." ) "; 
 
//echo '<hr><hr>';

$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);

//print_r($response); 

//echo '<strong>' . $fbid . '</strong><hr>';

$total_added = 0;

foreach ($response as $value) {
	
	$eid 	= $value['eid'];
	$name	= DBin($value['name']);
	$event_type	= DBin($value['event_type']);
	$event_subtype	= DBin($value['event_subtype']);
	
	echo $eid . ' = '  . $event_type . ' = ' . $event_subtype . ' <br>';
}

?>



</div>
<?php  include_once('footer.php')?>
