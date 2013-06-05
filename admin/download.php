<?php

require_once("database.php"); 


if($_GET['id']){

	$order_id = base64_decode($_GET['id']);
	$file = getSingleColumn("file_name","select * from tickets_record where id=$order_id");

	$realPath	= DOC_ROOT.$file;
	$fName		= explode("pdf/",$file);
	$fileName	= $fName[1];

	if ( $realPath != '' && $fileName != '' ) {

		$fileDataType = 'application/pdf';
		$fileSize = @filesize($realPath);
		
		header("Pragma: private");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/pdf");
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
//		header("Content-Transfer-Encoding:­ binary");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
			
		if($fileSize!=FALSE){
			header("Content-length: $fileSize");
		}
		
		readfile($realPath);
	}
	else {
		echo '<h1>File not Found! OR Your Order page is expired.</h1>';
	}
}

?>