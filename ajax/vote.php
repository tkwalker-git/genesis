<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
///////////////////////  $user_id  loggedin member id
	
		$team_id	=	$_GET['team_id'];
		$poll_id	=	$_GET['poll_id'];
		$session_id =	session_id();


$res = mysql_query("select * from `poll_voting` where `poll_id`='$poll_id' && `session_id`='$session_id'");

if(mysql_num_rows($res)==0){

mysql_query("INSERT INTO `poll_voting` (`id`, `session_id`, `team_id`, `poll_id`, `vote`) VALUES (NULL, '$session_id', '$team_id', '$poll_id', '1')");

echo getPollTeams($poll_id,'');

}

?>