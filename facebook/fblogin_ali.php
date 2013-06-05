<?php 

include('facebook.php');

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

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
$url="https://graph.facebook.com/me?access_token=" .$cookie['access_token']."";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$xml_resp =curl_exec($ch);
curl_close($ch);
$userfacebook = json_decode($xml_resp);
	
if($userfacebook->id!='')
{
	$Gender	= $userfacebook->gender;
	$id		= $userfacebook->id;
	$email	= $userfacebook->email;

}
	
	
	
$facebook = new Facebook(array(
	'appId'  => FACEBOOK_APP_ID,
	'secret' => FACEBOOK_SECRET,
	'cookie' => true, // enable optional cookie support
));

if ($session) {
	try 
	{
		$uid = $facebook->getUser();
		$me = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		error_log($e);
	}
}

if ($me) 
	$logoutUrl = $facebook->getLogoutUrl();
else
	$loginUrl = $facebook->getLoginUrl();

?>
      
    <div id="fb-root"></div>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
      FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true,
               cookie: true, xfbml: true});
      FB.Event.subscribe('auth.login', function(response) {
	   login();
        //window.location.reload();
      });
	  FB.Event.subscribe('auth.logout', function(response) {
		logout();
      });

	  function logout(){
		document.location.href = "logout.php";
  	  }
	  
	  function login(){
		document.location.href = "afterlogin.php";
	  }
      
</script>   

<fb:login-button autologoutlink="true"  width="100" background="white" length="short" label="Logout" perms="email,user_birthday"></fb:login-button>
   
	

     

