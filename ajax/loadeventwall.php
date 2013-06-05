<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$value	=	$_POST['value'];
	echo getMyEventwall($value);
?>