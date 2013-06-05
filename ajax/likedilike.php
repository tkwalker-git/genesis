<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');


$event_id	= $_POST['event_id'];
$type		= $_POST['type'];

if($_SESSION['like'][$event_id]!='yes'){
$res = mysql_query("select * from `events` where `id`='$event_id'");
while($row = mysql_fetch_array($res)){
	$like		=	$row['like'];
	$dislike	=	$row['dislike'];
}

if($type=='like')
	$add = $like+1;
else
	$add = $dislike+1;


$res = mysql_query("UPDATE `events` SET `$type` = '$add' WHERE `id` = '$event_id'");

if($res){
	echo "Submitted";
	$_SESSION['like'][$event_id] = 'yes';
	}
else{
	echo "Error";
	}
}
else{
	echo "Already Submitted";
	}
?>