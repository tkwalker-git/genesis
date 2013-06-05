<?php

require_once("database.php");
include_once('api/facebook.php');

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

// TKWalker Facebook profile id = 1387282969

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$session = $facebook->getSession();
if ($session) {
    $uid 		= $facebook->getUser();
    $friends 	= $facebook->api('/me/friends');
}
print_r($friends);

die();
foreach ($friends as $key=>$value) {
    echo count($value) . ' Friends';
    echo '<hr />';
    echo '<ul id="friends">';
    foreach ($value as $fkey=>$fvalue) {
        echo '<li><img src="https://graph.facebook.com/' . $fvalue[id] . '/picture" title="' . $fvalue[name] . '"/></li>';
    }
    echo '</ul>';
}

die();


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
		
		
	  ?>
    <?php } else { ?>
      <fb:login-button></fb:login-button>
    <?php } ?>

    <div id="fb-root"></div>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
      FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true,
               cookie: true, xfbml: true});
      FB.Event.subscribe('auth.login', function(response) {
        window.location.reload();
      });
    </script>
  </body>
</html>
