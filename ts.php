<?php

	list($width, $height, $type, $attr) = getimagesize("images/add_event.gif");
	
	
	list($eventImageWidth, $eventImageHeight, $eventImageType, $attr) = getimagesize("event_images/th_".$event_image);
	if($eventImageType==1){
	$src	=	imagecreatefromgif("event_images/th_".$event_image);
	}elseif($eventImageType==2){
	$src	=	imagecreatefromjpeg("event_images/th_".$event_image);
	}elseif($eventImageType==3){
	$src	=	imagecreatefrompng("event_images/th_".$event_image);
	
	
	$width				=	900;
	$height				=	675;
	$im		=	imagecreatefromjpeg("ticket.jpg");
	imagecopymerge($im, $src, 33, 105, 0, 0, 289, 424, 75);
	header ("Content-type: image/jpeg");
	imagejpeg($im, 'image.jpg');
	imagejpeg($im);
	
	header('Content-Type: image/jpeg');
	header('Content-Disposition: attachment;filename="ticket.jpg"');
	$fp=fopen('temp_cert.jpg','r');
	fpassthru($fp);
	fclose($fp);
	
	?>