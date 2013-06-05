<?php

include_once("../admin/database.php");
//include_once('../admin/api/facebook.php');

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

if ($cookie) { 
	
	$friends = json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $cookie['access_token']), true);
	$friends = $friends['data'];

	echo '<table width="100%" cellpadding="3" cellspacing="0" align="left">
			<tr style="background-color:#EEEEEE; border-bottom:#CCCCCC solid 1px">
				<td align="left" width="20"><input type="checkbox" onChange="toggleAll(this)" name="toggle_all" title="Select/Deselect all" checked></td>
				<td align="left">Select/Deselect All</td>
			</tr>';
	foreach ($friends as $friend) {
		echo '<tr><td align="left">
				<input name="check_1" value="'. $friend['id'] .'" type="checkbox" class="thCheckbox">
			</td>';
		echo '<td align="left">'. $friend['name'] .'</td>';
		echo '</tr>';
	}
	echo '</table>';
		
		
} else { ?>
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
