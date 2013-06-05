<?php
	include_once('admin/database.php');
	include_once('site_functions.php');
	$logged_member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
    if ( $_SESSION['LOGGEDIN_MEMBER_ID'] && $_SESSION['logedin']  ) {
 //     header("Location: /");exit;
    }

echo getSingleColumn("tot","select COUNT(*) as tot from `event_videos` where `event_id`='14'");