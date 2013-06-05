<?php

include('facebook.php');

define('FACEBOOK_APP_ID','175448372466498');
define('FACEBOOK_SECRET','2796fe4808043bb6c401ffc236bc1b2c');

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));


//$fql = "SELECT eid, name, pic, creator FROM event WHERE eid IN (139106456101039,158780864178979,115328961880697,207545062604804) "; // Working Fine
$fql = 'SELECT  like_count, total_count, share_count, click_count from link_stat  where  url="http://www.facebook.com/UnitedDatabase"'; // Working Fine
 
$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);
 
print_r($response);

$fb_likes = number_format($response[0]['like_count'],0,'',',');
	echo $fb_likes;

//$events = $facebook->api_client->events_get(1183299645);



?>