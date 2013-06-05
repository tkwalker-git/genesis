<?php

include_once('api/facebook.php');

define('FACEBOOK_APP_ID','129834663751946');
define('FACEBOOK_SECRET','58e36a5c672c0aa978ad8cf1786b89e9');

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$fql = "SELECT eid, name, pic, creator FROM event WHERE eid IN (139106456101039,158780864178979,115328961880697,207545062604804) "; // Working Fine

$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);
 
foreach ($response as $value) {
	
	echo 'Eid: ' . $value['eid'] . '<br>';
	echo 'Name: ' . $value['name'] . '<br>';
	echo 'Pic: ' . $value['pic_big'] . '<br>';
	echo 'Host: ' . $value['host'] . '<br>';
	echo 'Location: ' . $value['location'] . '<br>';
	
}
	

?>