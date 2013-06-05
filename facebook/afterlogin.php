<h1>After Login Page</h1>

<?php

include('facebook.php');

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

$cookie 	= get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);
$url		= "https://graph.facebook.com/me?access_token=" .$cookie['access_token']."";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$xml_resp 	= curl_exec($ch);
curl_close($ch);
$userfacebook = json_decode($xml_resp);

if($userfacebook->id != '')
{
	echo $gender = $userfacebook->gender;
	echo '<br>';
	echo $email	= $userfacebook->email;
	echo '<br>';
	echo $fb_id	= $userfacebook->id;
	echo '<br>';
	echo $name 	= $userfacebook->name;
	echo '<br>';
	echo $fname	= $userfacebook->first_name;
	echo '<br>';
	echo $bday	= $userfacebook->birthday;
	
}

$facebook = new Facebook( array(
							  'appId'  => FACEBOOK_APP_ID,
							  'secret' => FACEBOOK_SECRET,
							  'cookie' => true, // enable optional cookie support
						));

if ($session) {
	try {
		$uid = $facebook->getUser();
		$me = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		print_r($e);
	}
}

function get_facebook_cookie($app_id, $application_secret) {
	$args = array();
	parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
	ksort($args);
	$payload = '';
	
	foreach ($args as $key => $value)
		if ($key != 'sig') 
	  		$payload .= $key . '=' . $value;

	if (md5($payload . $application_secret) != $args['sig']) 
		return null;
	
	return $args;
}


?>