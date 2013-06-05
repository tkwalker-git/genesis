<?php

include_once('database.php');
require_once ('yelp_lib.php');

$consumer_key 		= "2rAJbVGZCjq4lkOJeM5lyw";
$consumer_secret	= "b9RCOfJy0XLZ59r3BBUVzMaCKI8";
$token 				= "d6MQYMqKCuTfQtvEHUSakg7p2C2jqNTt";
$token_secret 		= "Z6ZVBvYk-O7UOEbvk15CHLGk69U";

$token 				= new OAuthToken($token, $token_secret);
$consumer 			= new OAuthConsumer($consumer_key, $consumer_secret);
$signature_method 	= new OAuthSignatureMethod_HMAC_SHA1();

$unsigned_url2 = "http://api.yelp.com/v2/search?ll=28.45734,-81.30613&limit=1";

$unsigned_url3 = $unsigned_url2 ;
$oauthrequest2 = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url3);
$oauthrequest2->sign_request($signature_method, $consumer, $token);
$signed_url2 = $oauthrequest2->to_url();

$ch = curl_init($signed_url2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$data2 = curl_exec($ch); 
curl_close($ch);
	
$response2 = json_decode($data2);
$records2 = $response2->businesses;

if ( count($records2) > 0 ) 
	foreach ($records2 as $obj ) 
		$image = $obj->image_url;

echo $image;

?>