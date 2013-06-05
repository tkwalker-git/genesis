<?php

include('facebook.php');

define('FACEBOOK_APP_ID','175448372466498');
define('FACEBOOK_SECRET','2796fe4808043bb6c401ffc236bc1b2c');

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$fql = "SELECT eid, name, tagline, pic_small, host, description, event_type, event_subtype, creator, location, venue, privacy FROM event WHERE eid IN (select eid from event_member where uid=629817651) "; // Working Fine
 
$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);
 
print_r($response);

//$events = $facebook->api_client->events_get(1183299645);



?>