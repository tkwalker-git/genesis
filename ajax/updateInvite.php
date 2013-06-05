<?php
require_once('../admin/database.php');
require_once('../site_functions.php');

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

$invite	= getSingleColumn("id","select * from `completeness` where `user_id`='$member_id'");
if($invite)
	mysql_query("UPDATE `completeness` SET `invite` = '1' WHERE `user_id` = '$member_id'");
else
	mysql_query("INSERT INTO `completeness` (`id`, `invite`, `user_id`) VALUES (NULL, '1', '$member_id')");
?>