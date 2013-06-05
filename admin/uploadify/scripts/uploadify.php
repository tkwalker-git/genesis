<?php

if( isset($_FILES) ) {

	require_once("../../database.php");
	
	$fileName   = time() . str_replace(' ','', strtolower( $_FILES['Filedata']['name'] ) );
	$tempFile   = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $fileName;
	
	move_uploaded_file($tempFile,$targetFile);
	makeThumbnail($fileName, '../../../images/gallery/', '', 180, 160);
	makeThumbnail($fileName, '../../../images/gallery/', '', 50, 50, 'ico_');
	
	$alb_id = (int)$_GET['alb'];
	mysql_query("insert into images (image, album_id) values ('$fileName', '$alb_id')");
	
	echo "1";

}

?>