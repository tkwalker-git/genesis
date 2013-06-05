<?php
$app_id 	= "167535613301542";
$app_secret = "91350cc09f69e16007825383f54c0209";
$my_url 	= "http://www.eventgrabber.com/fb_get_tabs.php";
$page_id 	= "234915383217826";

$code = $_REQUEST["code"];

echo '<html><body>';

if(empty($code)) {
  // Get permission from the user to manage their Page. 
  $dialog_url = "http://www.facebook.com/dialog/oauth?client_id="
				. $app_id . "&redirect_uri=" . urlencode($my_url)
				. "&scope=manage_pages";
  echo('<script>top.location.href="' . $dialog_url . '";</script>');
} else {

   // Get access token for the app, so we can GET Page access token
  $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
			  . $app_id . "&redirect_uri=" . urlencode($my_url)
			  . "&client_secret=" . $app_secret
			  . "&code=" . $code;
  $access_token = file_get_contents($token_url);

  $page_token_url 	= "https://graph.facebook.com/" . $page_id . "?fields=access_token&" . $access_token;
  $response 		= file_get_contents($page_token_url);

  // Parse the return value and get the Page access token
  $resp_obj = json_decode($response,true);
  
  $page_access_token = $resp_obj['access_token'];
  
  // Using the Page access token from above,
  // we can GET the settings for the page
  $page_settings_url = "https://graph.facebook.com/" . $page_id . "/settings?access_token=" . $page_access_token;
  $response 		= file_get_contents($page_settings_url);
  $resp_obj 		= json_decode($response,true);
  
  echo '<pre>';
  print_r($response);
  echo '</pre>';
 
}

echo '</body></html>';
?>