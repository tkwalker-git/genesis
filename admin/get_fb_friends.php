<?php

require_once("database.php");

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

// TKWalker Facebook profile id = 1387282969

function get_facebook_cookie($app_id, $application_secret) {
  $args = array();
  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
    if ($key != 'sig') {
      $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $application_secret) != $args['sig']) {
    return null;
  }
  return $args;
}

$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://www.facebook.com/2008/fbml">
  <body>
    <?php if ($cookie) { ?>
	
      Your user ID is <?= $cookie['uid'] ?>
	  <? print_r($cookie) ?>
	  <hr>
	  <?php
	  	
		// GET FRIENDS
		
		$friends = json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $cookie['access_token']), true);
		$friends = $friends['data'];

		//echo '<table>';
		foreach ($friends as $friend) {
			/*
			$img = 'http://graph.facebook.com/'. $friend['id'] .'/picture';
			echo '<tr>';
			echo '<td><img src="'. $img .'" align="left"></td>';
			echo '<td>ID: ' . $friend['id'] . ' - Name: ' . $friend['name'] .'</td>';
			echo '</tr>';*/
			
			if ( mysql_query("INSERT IGNORE INTO fb_friends VALUES(NULL,'". $friend['id'] ."','". $friend['name'] ."')") )
				echo 'Added: ' . $friend['name'] . '<br>';
			else
				echo 'Failed: ' . $friend['name'] . '<br>';	
			
		}
		//echo '</table>';
	  
	  } else { ?>
	  
		<fb:login-button></fb:login-button>
		
		<div id="fb-root"></div>
		<script src="http://connect.facebook.net/en_US/all.js"></script>
		<script>
		  FB.init({appId: '<?= FACEBOOK_APP_ID ?>', 
				   status: true,
				   cookie: true, 
				   xfbml: true});
		  
		  FB.Event.subscribe('auth.login', function(response) {
			//window.location.reload();
			alert(response);
		  });
		  
		</script>
	
	<?php } ?> 
	
  </body>
</html>
