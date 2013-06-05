<?php 

require_once('../admin/database.php');
require_once('../site_functions.php');
$suc = false;

$id	 		= $_POST['id1'];
$type		= $_POST['ty'];
$userid 	= $_POST['userid'];
$reviews	= DBin($_POST['reviews']);



	$sql = "INSERT INTO comment VALUES (NULL, '". $id ."','". $type ."','". DBin($reviews) ."','','". $userid ."','". date("Y-m-d H:i:s") ."') ";
	if ( mysql_query($sql) )
		echo '1';
	else
		echo '0';
?>