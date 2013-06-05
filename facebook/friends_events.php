<?php

include('facebook.php');

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

/*
$act = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id=167535613301542&client_secret=91350cc09f69e16007825383f54c0209&redirect_uri=' . urlencode('http://www.eventgrabber.com/').'');
$token = str_replace("access_token=","",$act);

echo $friends = file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $token);
*/

// TK id = 1387282969

// Get Friends List
$friends = $facebook->api('/me/friends');
print_r($friends);




/*
//$fql = "SELECT eid, name, pic, creator FROM event WHERE eid IN (139106456101039,158780864178979,115328961880697,207545062604804) "; // Working Fine
$fql = "SELECT eid, name FROM event WHERE eid IN (select eid from event_member where uid=144023482300245) "; // Working Fine
 
$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);
 
print_r($response);


*/

?>