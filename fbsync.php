<?php
	include_once('admin/database.php');
	include_once('site_functions.php');
	$logged_member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
    if ( $_SESSION['LOGGEDIN_MEMBER_ID'] && $_SESSION['logedin']  ) {
 //     header("Location: /");exit;
    }

	include_once('facebooksdk/facebook.php');
	$facebook  = new Facebook(array(
							'appId'      => FACEBOOK_APP_ID2,
							'secret'     => FACEBOOK_SECRET2,
					));
	$user = $facebook->getUser();
	if ($user) {
	  try {
		$user_profile = $facebook->api('/me');
	  } catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	  }
	}

	if ($user) {
	  $logoutUrl = $facebook->getLogoutUrl(array('next'=>ABSOLUTE_PATH."logout.php"));
	} else {
      $loginUrl = $facebook->getLoginUrl(array(
        'next'=>ABSOLUTE_PATH."fbsync.php",
		'scope' => '
					user_birthday,
                    user_events,
                    friends_events,
                    user_interests,
                    user_likes,
                    email,
                    read_friendlists,
                    offline_access'
	  ));
     
      echo  "<script>window.parent.location=\"".html_entity_decode($loginUrl)."\"  </script>";
	  exit;
	}

	$permissions = $facebook->api("me/permissions");
	if(//$permissions['data'][0]['publish_stream']!=1 ||
	   $permissions['data'][0]['user_birthday']!=1 ||
       $permissions['data'][0]['user_events']!=1 ||
       $permissions['data'][0]['friends_events']!=1 ||
	   $permissions['data'][0]['user_interests']!=1 ||
       $permissions['data'][0]['user_likes']!=1 ||
       $permissions['data'][0]['email']!=1 ||
	   $permissions['data'][0]['read_friendlists']!=1 ||
	   //$permissions['data'][0]['rsvp_event']!=1 ||
       $permissions['data'][0]['offline_access']!=1
      ){
		$loginUrl = $facebook->getLoginUrl(array(
        'next'=>ABSOLUTE_PATH."fbsync.php",
		'scope' => '
					user_birthday,
                    user_events,
                    friends_events,
                    user_interests,
                    user_likes,
                    email,
                    read_friendlists,
                    offline_access'
	  ));
     
      echo  "<script>window.parent.location=\"".html_entity_decode($loginUrl)."\"  </script>";
	  exit;
	}
    
    $access_token  = $facebook->getAccessToken();
    $userfacebook  = $facebook->api("/me");
  
	$gender		= $userfacebook["gender"];
	$email		= $userfacebook["email"];
	$fb_id		= $userfacebook["id"];
	$fname		= $userfacebook["first_name"];
	$lname		= $userfacebook["last_name"];
	$dob		= $userfacebook["birthday"];
	
    $fql		= "SELECT pic_small,pic_big,pic FROM profile WHERE id = " . $fb_id;
	$response	= $facebook->api(	
								array(
									'method' => 'fql.query',
									'query' =>$fql,
								)
							);
	
	foreach ($response as $value) {
	
		$pic_s	= $value['pic_small'];
		$ipath	= $value['pic_big'];
		$pic	= $value['pic'];
			
	}
	
	if ($ipath == ''){
		$ipath = $pic;
		if ($ipath == ''){
			$ipath = $pic_s;
		}
	}
	
	$tmp 	= explode("/",$pic);
	$iname 	= $tmp[count($tmp)-1];
	
	copy($pic,'images/members/' . $iname);



	$syncUser	= getSingleColumn("id","select * from `completeness` where `user_id`='$logged_member_id'");
	if($syncUser)
		mysql_query("UPDATE `completeness` SET `sync` = '1' WHERE `user_id` = '$logged_member_id'");
	else
		mysql_query("INSERT INTO `completeness` (`id`, `sync`, `user_id`) VALUES (NULL, '1', '$logged_member_id')");

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
	if($u_dob == '' || $u_dob == '0000-00-00'){
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