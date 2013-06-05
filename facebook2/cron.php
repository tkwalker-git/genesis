<?php

	require_once("./config.php");
	$cache     = './cache/facebook-json.txt';
	$output    = array();
	
	require './facebook/facebook.php';
	
	$facebook = new Facebook(array(
	  'appId'  => APP_ID,
	  'secret' => APP_SECRET,
	));
	$final_feed   = array();
	foreach($facebook_ids as $facebook_id){
		$feed   = $facebook->api('/'.$facebook_id.'/feed');
		if(is_array($feed['data']) && !empty($feed['data'])){
			for($i=0;$i<TOTAL_FEEDS;$i++){
				$final_feed[end(explode("_",$feed['data'][$i]['id']))]= $feed['data'][$i]; 
			}
		}
	}
	
	ksort($final_feed);
	
	
	$output       = array_slice($final_feed, 0, TOTAL_FEEDS);
	if(empty($output)) exit;
	
	$final_output = addslashes(@json_encode($output));
	$cachefile    = fopen($cache, 'wb');
	fwrite($cachefile,utf8_encode($final_output));
	fclose($cachefile);
?>