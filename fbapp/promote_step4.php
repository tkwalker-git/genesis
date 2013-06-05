<?php
	
	error_reporting(E_ALL);

	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='". ABSOLUTE_PATH ."login.php';</script>";
	
	require_once('../includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	if ( $_GET['event_id'] > 0 && $_GET['page_id'] != '' && $_GET['app_db_id'] > 0 ) {
		$app_db_id 	= $_GET['app_db_id'];
		$page_id 	= $_GET['page_id'];
		$event_id 	= $_GET['event_id'];
		
		$fb_app_id  = attribValue('fb_apps', 'app_id', "where id='". $app_db_id ."'");
		$fb_app_sec = attribValue('fb_apps', 'app_secret', "where id='". $app_db_id ."'");
		
		include_once('../facebooksdk/facebook.php');
		$facebook  = new Facebook(array(
			'appId'      => $fb_app_id,
			'secret'     => $fb_app_sec,
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
			$logoutUrl = $facebook->getLogoutUrl(array('next'=>"http://www.eventgrabber.com"));
		} else {
			$loginUrl = $facebook->getLoginUrl(array(
							'next'=>"http://eventgrabber.com",
							'scope' => 'manage_pages,email'
						  ));
		 
			echo  "<script>window.parent.location=\"".html_entity_decode($loginUrl)."\"  </script>";
			exit;
		}
		
		$access_token  	= $facebook->getAccessToken();
		$userfacebook  	= $facebook->api("/me");
		$uid 			= $userfacebook["id"];
	}												
?>
		
		<script>
		window.location.href = 'promote_step5.php?page_id=<?php echo $page_id;?>&uid1=<?php echo $uid;?>&eid=<?php echo $event_id;?>&app_db_id=<?php echo $app_db_id;?>';
		</script>
												