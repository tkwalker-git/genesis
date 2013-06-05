<?php

function generateTicket($values){
	
	$event_name		=	$values['event_name'];
	$ticket_number	=	$values['ticket_number'];
	$event_date		=	$values['event_date'];
	$recipi			=	$values['recipi'];
	$state			=	$values['state'];
	$address		=	$values['address'];
	$city			=	$values['city'];
	$expire_date	=	$values['expire_date'];
	$event_image	=	$values['image'];
	
	$width				=	900;
	$height				=	675;
	
	$ticket_number_size	=	18;
	$event_name_size	=	19;
	$event_date_size	=	16;
	$recipi_size		=	14;
	$address_size		=	13;
	
	$arialbd			=	"arialbd.ttf";
	$arial				=	"arial.ttf";
	
		
	$im		=	imagecreatefromjpeg("ticket.jpg");
	
	list($eventImageWidth, $eventImageHeight, $eventImageType, $attr) = getimagesize("event_images/th_".$event_image);
	
	if($eventImageType==1){
	$src	=	imagecreatefromgif("event_images/th_".$event_image);
	}elseif($eventImageType==2){
	$src	=	imagecreatefromjpeg("event_images/th_".$event_image);
	}elseif($eventImageType==3){
	$src	=	imagecreatefrompng("event_images/th_".$event_image);
	}
	
	$ticket_number_color	= imagecolorallocate($im, 98, 98, 98);
	$event_name_color		= imagecolorallocate($im, 36, 36, 36);
	$event_date_color		= imagecolorallocate($im, 10, 108, 157);
	$recipi_color			= imagecolorallocate($im, 10, 108, 157);

//	$image	=	"IMAGE NOT AVAILABLE";
	
	imagettftext($im, $ticket_number_size, 0, 749, 64, $ticket_number_color, $arial, $ticket_number);
	imagettftext($im, $event_name_size, 0, 339, 150, $event_name_color, $arialbd, $event_name);
	imagettftext($im, $event_date_size, 0, 339, 179, $event_date_color, $arialbd, $event_date);
	imagettftext($im, $recipi_size, 0, 339, 262, $event_date_color, $arialbd, $recipi);
	imagettftext($im, $recipi_size, 0, 339, 366, $event_date_color, $arialbd, $state);
	imagettftext($im, $address_size, 0, 339, 385, $event_date_color, $arial, $address);
	imagettftext($im, $address_size, 0, 339, 403, $event_date_color, $arial, $city);
	imagettftext($im, $event_date_size, 0, 339, 494, $event_date_color, $arialbd, $expire_date);

	imagecopymerge($im, $src, 33, 105, 0, 0, $eventImageWidth, $eventImageHeight, 75);
	
	
	
	
	 header ("Content-type: image/jpeg");
	 imagejpeg($im, 'image.jpg');
	 imagejpeg($im);
	
	header('Content-Type: image/jpeg');
	header('Content-Disposition: attachment;filename="ticket.jpg"');
	$fp=fopen('temp_cert.jpg','r');
	fpassthru($fp);
	fclose($fp);

}

?>