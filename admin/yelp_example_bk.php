<?php

include_once('database.php');
require_once ('yelp_lib.php');

$unsigned_url2 = "http://api.yelp.com/v2/search?location=Orlando";
//$unsigned_url2 = "http://api.yelp.com/v2/search?location=Orlando&category_filter=arts,beautysvc,education,eventservices,food,hotelstravel,nightlife,pets,publicservicesgovt,religiousorgs,restaurants,shopping";

$consumer_key 		= "2rAJbVGZCjq4lkOJeM5lyw";
$consumer_secret	= "b9RCOfJy0XLZ59r3BBUVzMaCKI8";
$token 				= "d6MQYMqKCuTfQtvEHUSakg7p2C2jqNTt";
$token_secret 		= "Z6ZVBvYk-O7UOEbvk15CHLGk69U";

$token 				= new OAuthToken($token, $token_secret);
$consumer 			= new OAuthConsumer($consumer_key, $consumer_secret);
$signature_method 	= new OAuthSignatureMethod_HMAC_SHA1();
$oauthrequest 		= OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url2);

$oauthrequest->sign_request($signature_method, $consumer, $token);
$signed_url 		= $oauthrequest->to_url();

$ch = curl_init($signed_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$data = curl_exec($ch); 
curl_close($ch);

$response = json_decode($data);
//print_r($response);

$total_rec = $response->total;
//echo '<hr>';
$pages = ceil($total_rec/20);
//die();

for ( $i=$_GET['s'];$i<$_GET['e'];$i++) {
	
	$offset = $i * 20;
	$unsigned_url3 = $unsigned_url2 . "&offset=$offset";
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
	
	echo 'Iteration: ' . $i . ' - Total: ' . count($records2) .'<br>';
	
	foreach ($records2 as $obj ) {
		
		$location_id 		= $obj->id;
		$name				= DBin($obj->name);
		$address			= DBin($obj->location->display_address[0]);
		$city				= $obj->location->city;
		$state				= $obj->location->state_code;
		$zip				= $obj->location->postal_code;
		$phone				= $obj->display_phone;
		$rating				= $obj->rating_img_url;
		$categories			= $obj->categories;
		$lati 				= $obj->location->coordinate->latitude;
		$long 				= $obj->location->coordinate->longitude;
		$image 				= $obj->image_url;
		$neighbor      		= DBin($obj->location->neighborhoods[0]);
		
		$rating = str_replace('http://media4.px.yelpcdn.com/static/201012163106483837/i/ico/stars/stars_','',$rating);
		
		$rating = str_replace('.png','',$rating);
		$r = explode("_",$rating);
		if ( count( $r) > 1 )
			$rating = $r[0] . '.5';
		else
			$rating = $r[0];	
		
		if ( is_array($categories) ) {
			$vtype	= DBin($categories[0][0]);
			foreach ( $categories as $cat) {
				$cate .= DBin($cat[0]) . ',';
			}
		}
		
		if ( mysql_query("insert ignore into venues_yp (source_id,venue_type,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,categories,averagerating,tags,phone,neighbor,image) VALUES ('$location_id','$vtype','$name','$address','$city','$state','$zip','$long','$lati','$cate','$rating','$tag','$phone','$neighbor','$image') ") ) {
			$total_added++;
		} else {
			echo mysql_error() . '<br>';
		}	
	}

}
?>