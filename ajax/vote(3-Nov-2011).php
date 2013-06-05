<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
///////////////////////  $user_id  loggedin member id
	
$poll_id	=	$_GET['poll_id'];
$entry_id	=	$_GET['entry_id'];
$team		=	$_GET['team'];

if($team=='a'){
$team		=	'teamA_votes';
}
else{
$team		=	'teamB_votes';
}

$count_vote	=	getSingleColumn($team,"select * from `poll_match` where `poll_id`='$poll_id'");

$count_vote++;

$rs = mysql_query("UPDATE `poll_match` SET `$team` = '$count_vote' WHERE `poll_id` = '$poll_id'");
if($rs){
$session_id = session_id();

$poll_match_id	=	getSingleColumn("id","select * from `poll_match` where `poll_id`='$poll_id'");

mysql_query("INSERT INTO `person_voted` (`id`, `poll_match_id`, `session_id`) VALUES (NULL, '$poll_match_id', '$session_id')");



$res = mysql_query("select * from `poll_match` where `poll_id`='$poll_id'");
while($row = mysql_fetch_array($res)){
echo $row['teamA_votes']."-".$row['teamB_votes'];
}
}









?>