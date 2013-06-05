<?php
	include_once('admin/database.php');
	include_once('site_functions.php');

    if ( $_SESSION['LOGGEDIN_MEMBER_ID'] && $_SESSION['logedin']  ) {
      header("Location: /");exit;
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
        'next'=>ABSOLUTE_PATH."fblogin.php",
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
        'next'=>ABSOLUTE_PATH."fblogin.php",
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
  
	$gender        = $userfacebook["gender"];
	$email	       = $userfacebook["email"];
	$fb_id	       = $userfacebook["id"];
	$fname	       = $userfacebook["first_name"];
	$lname	       = $userfacebook["last_name"];
	

    $fql           = "SELECT pic_small,pic_big,pic FROM profile WHERE id = " . $fb_id; 
	 
	$response      = $facebook->api(	
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
	
	if ($ipath == '') {
		$ipath = $pic;
		if ($ipath == '') {
			$ipath = $pic_s;
		}
	}
	
	$tmp 	= explode("/",$pic);
	$iname 	= $tmp[count($tmp)-1];
	
	copy($pic,'images/members/' . $iname);

	$fsql          = "select * from users where email='". $email ."'";
	$fres          = mysql_query($fsql);
	
	if ( mysql_num_rows($fres) ) {	
		if ( $frow = mysql_fetch_assoc($fres) ) {
			$_SESSION['logedin'] 			= '1';
			$_SESSION['LOGGEDIN_MEMBER_ID'] = $frow['id'];
			$_SESSION['usertype'] 			= $frow['usertype'];
		}

        $user_update = "UPDATE users SET facebookid='$fb_id',facebook_access_token='$access_token' WHERE email='$email'";
		mysql_query($user_update);
	} else {
		
		$password 	 = substr(md5(rand(0,1000)),1,8);
		$username 	 = $email;
		$memberdate	 = date("Y-m-d");
		$user_insert = "insert into users (firstname,lastname, email, username, password, createddate, usertype, email_verify,enabled,facebookid,facebook_access_token,image_name) 
						values ('$name','$lname', '$email', '$username', '$password', '$memberdate', '1','1','1','$fb_id','$access_token','$iname') ";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: EventGrabber.com<info@eventgrabber.com>' . "\r\n";
		$welcome = 'Hello ' . $fname . ',<br>';
		$welcome .= 'Welcome to EventGrabber.com. You will find this site very exciting.<br><br>Thanks,<br>EventGrabber.com';

		if ( mysql_query($user_insert) ) {
			
			$_SESSION['logedin'] 			= '1';
			$_SESSION['LOGGEDIN_MEMBER_ID'] = mysql_insert_id();
			$_SESSION['usertype'] 			= 1;
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: EventGrabber.com<info@eventgrabber.com>' . "\r\n";
			$welcome = 'Hello ' . $fname . ',<br>';
			$welcome .= 'Welcome to EventGrabber.com. You will find this site very exciting.<br><br>Thanks,<br>EventGrabber.com';
			mail($email,"Welcome to EventGrabber.com",$welcome,$headers);
		}
	}
    echo  "<script>window.parent.location=\"".html_entity_decode(ABSOLUTE_PATH)."\"  </script>";
	exit;	
?>