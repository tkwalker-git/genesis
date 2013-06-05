<?php
require_once('../admin/database.php');
require_once('../site_functions.php');
$suc = false;

$id	 		= $_POST['id1'];
$type		= $_POST['ty'];
$userid 	= $_POST['userid'];
$reviews	= $_POST['reviews'];
$rating 	= $_POST['rating'];

$sql = "select id from comment where by_user=$userid and key_id=$id and c_type='$type'";
$res = mysql_query($sql);

if ( mysql_num_rows($res) > 0 ) {
	$txt = '';
	if ( $reviews != '' )
		$txt = "comment='". DBin($reviews) ."'  ";
	if ( $txt != '')
		$txt .= ',';
	if ( $rating != '' )	
		$txt .= " rating='". $rating ."' ";
	
	$sql = "UPDATE comment set ". $txt ." where by_user='". $userid ."' AND key_id='". $id ."' AND c_type='". $type ."' ";
	if ( mysql_query($sql) )
		echo '1';
	else
		echo '0';
} else {

	$sql = "INSERT INTO comment VALUES (NULL, '". $id ."','". $type ."','". DBin($reviews) ."','". $rating ."','". $userid ."','". date("Y-m-d H:i:s") ."') ";
	if ( mysql_query($sql) )
		echo '1';
	else
		echo '0';	
}?>