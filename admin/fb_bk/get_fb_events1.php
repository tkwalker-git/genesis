<?php

include_once("database.php");
include_once("fb_friends.php");
include_once('api/facebook.php');

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$s = $_GET['s'];

$tmpp = array_slice($friends,$s,100);



$frds = implode(",",$tmpp);
	
$fql = "SELECT eid, name, tagline, pic_small, host, description, event_type, event_subtype, creator, location, venue, privacy FROM event 
		WHERE eid IN (select eid from event_member where uid = ". $_GET['uid'] ." ) "; 
 
//echo '<hr><hr>';

$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);

//print_r($response); 
//echo '<strong>' . $fbid . '</strong><hr>';

foreach ($response as $value) {
	
	echo 'Eid: ' . $value['eid'] . '<br>';
	echo 'Name: ' . $value['name'] . '<br>';
	echo 'Pic: ' . $value['pic_small'] . '<br>';
	echo 'Host: ' . $value['host'] . '<br>';
	echo 'Location: ' . $value['location'] . '<br><hr><br>';
	
}

?>