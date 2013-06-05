<?php
include_once('admin/database.php');
include_once('site_functions.php');
error_reporting(E_ALL);

require_once('twitter/twitteroauth/twitteroauth.php');
$type	= 'sync';
require_once('twitter/config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    session_destroy();
	header('Location: twitter/connect.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
$usertwitter = $connection->get('account/verify_credentials');

    $twitter_access_oauth_token         = $$_SESSION['access_token']['oauth_token'];
    $twitter_access_oauth_token_secret  = $$_SESSION['access_token']['oauth_token_secret'];

    //$userfacebook  = $facebook->api("/me");
  
	$gender        = $usertwitter->gender;
	$email	       = $usertwitter->screen_name."@twitter.com";
	$tw_id	       = $usertwitter->id_str;
	$fname	       = current(explode(" ",$usertwitter->name));
	$lname	       = end(explode(" ",$usertwitter->name));
    $pic           = $usertwitter->profile_image_url;
	$tmp 	       = explode("/",$pic);
	$iname 	       = end($tmp);
	//d($iname);
   // d($pic);
	copy($pic,'../images/members/' . $iname);
	

	$u_gender	= getSingleColumn("sex","select * from `users` where `id`='$logged_member_id'");
	if($u_gender == ''){
		if($gender == 'male')
			$bc_gender	= 'M';
		else
			$bc_gender	= 'F';
			mysql_query("UPDATE `users` SET `sex` = '$bc_gender' WHERE `id` = '$logged_member_id'");
	}
	
	
	$u_email	= getSingleColumn("email","select * from `users` where `id`='$logged_member_id'");
	if($u_email == ''){
			mysql_query("UPDATE `users` SET `email` = '$email' WHERE `id` = '$logged_member_id'");
	}
	
	
	$u_facebookid	= getSingleColumn("facebookid","select * from `users` where `id`='$logged_member_id'");
	if($u_facebookid == '' || $u_facebookid == 0){
			mysql_query("UPDATE `users` SET `facebookid` = '$fb_id' WHERE `id` = '$logged_member_id'");
	}
	
	
	$u_name	= getSingleColumn("firstname","select * from `users` where `id`='$logged_member_id'");
	if($u_name == ''){
			mysql_query("UPDATE `users` SET `firstname` = '$fname' WHERE `id` = '$logged_member_id'");
	}
	
	
	$u_lname	= getSingleColumn("lastname","select * from `users` where `id`='$logged_member_id'");
	if($u_lname == ''){
			mysql_query("UPDATE `users` SET `lastname` = '$lname' WHERE `id` = '$logged_member_id'");
	}
	
	
	$u_dob	= getSingleColumn("dob","select * from `users` where `id`='$logged_member_id'");
	if($u_dob == ''){
			$dob	= date('Y-m-d', strtotime($dob));
			mysql_query("UPDATE `users` SET `dob` = '$dob' WHERE `id` = '$logged_member_id'");
	}
	
	
	$u_image_name	= getSingleColumn("image_name","select * from `users` where `id`='$logged_member_id'");
	if($u_image_name == ''){
			mysql_query("UPDATE `users` SET `image_name` = '$iname' WHERE `id` = '$logged_member_id'");
	}
	
	
	echo  "<script>window.parent.location=\"".html_entity_decode(ABSOLUTE_PATH."settings.php?p=my-profile")."\"  </script>";
	exit;	
?>