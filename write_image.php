<?php
$values = array(
				'event_name'	=>	"Disney On Ice presents DARE TO DREAM",
				'ticket_number'	=>	"0000008830",
				'event_date'	=>	date('Y-m-d'),
				'time'			=>	date('H:i'),
				'recipi'		=>	"John Smith",
				'state' 		=>	"Amway Center",
				'address'		=>	"400 W Church Street",
				'city' 			=>	"Orlando FL",
				'zip' 			=>	"32805",
				'expire_date'	=>	"Friday, Sep. 30, 2011",
				'event_image'	=>	"ss",
				'ticket_type'	=>	"This is the ticket type",
				'ticket_price'	=>	"$25.00"
				);
//function generateTicket($values){
$folderName	=	time();
	for($i=1;$i<=5;$i++){
	$as	=	$i;
		$event_name		=	$values['event_name'];
		$ticket_number	=	"Ticket # ".$values['ticket_number'];
		$event_date		=	$values['event_date'];
		$time			=	$values['time'];
		$recipi			=	$values['recipi'];
		$state			=	$values['state'];
		$address		=	$values['address'];
		$city			=	$values['city'];
		$zip			=	$values['zip'];
		$expire_date	=	$values['expire_date'];
		$event_image	=	$values['event_image'];
		$ticket_type	=	$values['ticket_type'];
		$ticket_price	=	$values['ticket_price'];
			
		$width			=	800;
		$height			=	436;
	
		$arialbd		=	"arialbd.ttf";
		$arial			=	"arial.ttf";
				
		$im		=	imagecreatefrompng("blankticket.png");
		list($eventImageWidth, $eventImageHeight, $eventImageType, $attr) = getimagesize("event_images/eventTicketImg.gif");
		
		if($eventImageType==1){
		$src	=	imagecreatefromgif("event_images/eventTicketImg.gif");
		}elseif($eventImageType==2){
		$src	=	imagecreatefromjpeg("event_images/eventTicketImg.gif");
		}elseif($eventImageType==3){
		$src	=	imagecreatefrompng("event_images/eventTicketImg.gif");
		}
		
		$black	=	imagecolorallocate($im, 0, 0, 0);
		$yellow	=	imagecolorallocate($im, 238, 175, 12);
		$blue	=	imagecolorallocate($im, 1, 100, 165);
		
		imagettftext($im, 10, 0, 583, 68, $black, $arialbd, $ticket_number);
		
		imagettftext($im, 10, 0, 201, 120, $yellow, $arialbd, "Event");
		imagettftext($im, 13, 0, 201, 140, $black, $arialbd, $event_name);
		imagettftext($im, 9, 0, 201, 164, $black, $arialbd, "DATE:");
		imagettftext($im, 9, 0, 240, 164, $blue, $arialbd, date('l, F d, Y', strtotime($event_date)));
		imagettftext($im, 9, 0, 201, 186, $black, $arialbd, "TIME:");
		imagettftext($im, 9, 0, 240, 186, $blue, $arialbd, date('h:i A', strtotime($time)));
		imagettftext($im, 9, 0, 201, 207, $black, $arialbd, "TYPE:");
		imagettftext($im, 9, 0, 240, 207, $blue, $arialbd, $ticket_type);
		imagettftext($im, 9, 0, 201, 228, $black, $arialbd, "PRICE:");
		imagettftext($im, 9, 0, 240, 228, $blue, $arialbd, $ticket_price);
		imagettftext($im, 9, 0, 201, 261, $black, $arialbd, "RECIPIENTS:");
		imagettftext($im, 9, 0, 201, 275, $blue, $arialbd, $recipi);
		imagettftext($im, 9, 0, 302, 261, $black, $arialbd, "REDEEM AT:");
		imagettftext($im, 9, 0, 302, 275, $blue, $arialbd, $state);
		imagettftext($im, 8, 0, 302, 288, $blue, $arial, $address);
		imagettftext($im, 8, 0, 302, 300, $blue, $arial, $city.", ");
		imagettftext($im, 8, 0, 362, 300, $blue, $arial, $zip);
		imagettftext($im, 9, 0, 419, 261, $black, $arialbd, "TICKET EXPIRES:");
		imagettftext($im, 9, 0, 419, 275, $blue, $arialbd, date('l, M. d, Y', strtotime($event_date)));
		imagecopymerge($im, $src, 89, 109, 0, 0, $eventImageWidth, $eventImageHeight, 100);
		
		
		header ("Content-type: image/png");
		mkdir("tickets/".$folderName.".rar", 0777);
		
		imagepng($im, 'tickets/'.$folderName.'/ticket'.$i.'.png');
		imagedestroy($im);
		
//		imagepng($im);

		
	//	header('Content-Type: image/png');
	//	header('Content-Disposition: attachment;filename="ticket'.$i.'.png"');
		$fp=fopen('temp_ticket.png','r');
		fpassthru($fp);
		fclose($fp);
		}
		
//}

?>