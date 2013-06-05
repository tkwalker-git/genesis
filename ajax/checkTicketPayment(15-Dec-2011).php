<?php

	

	require_once('../admin/database.php');

	require_once('../site_functions.php');





		$c_country		= $_POST["country"];

		$c_number		= $_POST["c_number"];

		$c_month		= $_POST["c_month"];

		$c_year			= $_POST["c_year"];

		$c_csc			= $_POST["c_csc"];

		$c_name			= $_POST["c_name"];

		$c_address		= $_POST["c_address"];

		$c_city			= $_POST["c_city"];

		$c_province		= $_POST["c_province"];

		$c_postal_code	= $_POST["c_postal_code"];

		$c_telephone	= $_POST["c_telephone"];

		$c_type			= $_POST["c_type"];

		$c_email		= $_POST["c_email"];		

		

		$grand_total	= $_SESSION['orderMember']['total'];



		$customer_details['first_name'] 		= $_SESSION['orderMember']['fname'];

		$customer_details['last_name'] 			= $_SESSION['orderMember']['lname'];

		$customer_details['address'] 			= $c_address;

		$customer_details['zip'] 				= $c_postal_code;

		$customer_details['city'] 				= $c_city;

		$customer_details['state'] 				= $c_province;

		$customer_details['country'] 			= $country;

		$customer_details['card_number'] 		= $c_number;

		$customer_details['card_code'] 			= $c_csc;

		$customer_details['card_expiry_month'] 	= $c_month;

		$customer_details['card_expiry_year'] 	= $c_year;

		$customer_details['totalAmount']		= $grand_total;

		

		

			include_once("../includes/authorize.net.php");

			$response = authorize_process($customer_details);	

			list($processed, $response_arr) = authorize_isProcessed($response);

			

			if (!$processed) {

			

	$sucMessage =  $response_arr[3];

	echo $sucMessage;

		

			} else {



	$date		=	date('Y-m-d');

	$folderName	=	time();

	mkdir("../tickets/".$folderName, 0700);

	$bc_event_id = $_SESSION['orderMember']['event_id'];

	$main_ticket_id = getSingleColumn('id',"select * from `event_ticket` where `event_id`='$bc_event_id'");

	

	

	$gender		= $_SESSION['orderMember']['gender'];

	$fname		= $_SESSION['orderMember']['fname'];

	$lname		= $_SESSION['orderMember']['lname'];

	$city		= $_SESSION['orderMember']['city'];

	$dob		= date('Y-m-d', strtotime($_SESSION['orderMember']['dob']));

	$email		= $_SESSION['orderMember']['email'];



	mysql_query("INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `sex`, `dob`, `city`, `send_recommended_events`) VALUES (NULL, '$fname', '$lname', '$email', '$gender', '$dob', '$city', '0');");

	$user_id = mysql_insert_id();

	

	

	

mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`, `main_ticket_id`) VALUES (NULL, '$user_id', '$grand_total', '$date', 'ticket', '$main_ticket_id')");

	

	$order_id	=	mysql_insert_id();

	

	$qtys	= explode(",",$_SESSION['ticketOrder']['ticket_qty']);

	$ids	= explode(",",$_SESSION['ticketOrder']['ticket_id']);

	$dates	= explode(",",$_SESSION['ticketOrder']['ticket_date']);

	

		$total_price	=	0;

	for($i=0;$i< count($ids); $i++) {

		$id		=	$ids[$i];

		$qty	=	$qtys[$i];

		$total_price='';

	if($qty!='' && $qty!=0){

	$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");

	while($row = mysql_fetch_array($res)){	

		$price = $row['price'];

		

		$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$bc_event_id");

	

	$buyer_service_free_after_percent		=	$row['price']*5.50/100+.99;

	$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;

			

	$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');

	$priceAfterQty	=	$price*$qty;

	$priceAfterQty	=	$priceAfterQty+($finalServiceCharges*$qty);

	$total_price	=	$priceAfterQty+$total_price;

	

	$forTicketOne	=	number_format($price+$buyer_service_free_after_percent, 2,'.','');

	

	mysql_query("INSERT INTO `order_tickets` (`id`, `ticket_id`, `price`, `quantity`, `date`, `order_id`) VALUES (NULL, '$id', '$priceAfterQty', '$qty', '$date', '$order_id');");

	$ticket_id_for_ticket	=	mysql_insert_id();

	

	$rt = mysql_query("select * from `events` where `id`='$bc_event_id'");

	while($row = mysql_fetch_array($rt)){

	$event_name			=	$row['event_name'];

	$event_image		=	$row['event_image'];



	if($event_image==''){

	$event_image = "noimg.gif";

	}

	}

for ($d=1;$d<=$qty;$d++){



	if($qty>1){

	$ticket_number	=	str_pad($ticket_id_for_ticket."_".$d, 12, "0", STR_PAD_LEFT);

	}

	else{

	$ticket_number	=	str_pad($ticket_id_for_ticket, 10, "0", STR_PAD_LEFT);

	}

	

	

	$ev_date_id = $dates[$i];

	

	$timeRow =  getEventTime($ev_date_id);

	

	$event_time =  date('h:i A', strtotime($timeRow['start_time']));

	

		if($timeRow['end_time']!='' && $timeRow['end_time']!='00:00:00'){

			$event_time.=  ' - '.date('h:i A', strtotime($timeRow['ent_time']));

		}

		

	 $values = array(

					'event_name'	=>	$event_name,

					'ticket_number'	=>	$ticket_number,

					'event_date'	=>	getDateById($dates[$i]),

					'time'			=>	$event_time,

					'recipi'		=>	$bc_name,

					'state' 		=>	$c_province,

					'address'		=>	$bc_address,

					'city' 			=>	$bc_city,

					'expire_date'	=>	getDateById($dates[$i]),

					'ticket_type'	=>	$title,

					'event_image'	=>	$event_image,

					'ticket_price'	=>	$forTicketOne

					);

					

//generateTicket($values);



		$event_name		= $values['event_name'];

		$ticket_number	= $values['ticket_number'];

		$event_date		= $values['event_date'];

		$time			= $values['time'];

		$recipi			= $values['recipi'];

		$state			= $values['state'];

		$address		= $values['address'];

		$city			= $values['city'];

		$zip			= $values['zip'];

		$expire_date	= $values['expire_date'];

		$event_image	= $values['event_image'];

		$ticket_type	= $values['ticket_type'];

		$ticket_price	= $values['ticket_price'];

		$width			= 800;

		$height			= 436;

		$arialbd		= "../arialbd.ttf";

		$arial			= "../arial.ttf";

				

		$im		=	imagecreatefrompng("../blankticket.png");

		

		if ( !file_exists(DOC_ROOT .  'event_images/ico_' . $event_image ) && file_exists(DOC_ROOT .  'event_images/' . $event_image )) {

		makeThumbnail($event_image, '../event_images/', '', 106, 2000,'ico_');

		}

		

		list($eventImageWidth, $eventImageHeight, $eventImageType, $attr) = getimagesize("../event_images/ico_".$event_image."");

		if($eventImageType==1){

		$src	=	imagecreatefromgif("../event_images/ico_".$event_image."");

		}elseif($eventImageType==2){

		$src	=	imagecreatefromjpeg("../event_images/ico_".$event_image."");

		}elseif($eventImageType==3){

		$src	=	imagecreatefrompng(ABSOLUTE_PATH."../event_images/ico_".$event_image."");

		}

		

		$black	=	imagecolorallocate($im, 0, 0, 0);

		$yellow	=	imagecolorallocate($im, 238, 175, 12);

		$blue	=	imagecolorallocate($im, 1, 100, 165);

		

		

		if ($bc_service_fee_type==2){

        $t_price	=	$ticket_price+$bc_service_fee;

		}

		else{

		$t_price	=	$ticket_price;

		}



	

		imagettftext($im, 10, 0, 583, 68, $black, $arialbd, "Ticket # ".$ticket_number);		

		imagettftext($im, 10, 0, 201, 120, $yellow, $arialbd, "Event");

		imagettftext($im, 13, 0, 201, 140, $black, $arialbd, $event_name);

		imagettftext($im, 9, 0, 201, 164, $black, $arialbd, "DATE:");

		imagettftext($im, 9, 0, 242, 164, $blue, $arialbd, date('l, F d, Y', strtotime($event_date)));

		imagettftext($im, 9, 0, 201, 186, $black, $arialbd, "TIME:");

		imagettftext($im, 9, 0, 242, 186, $blue, $arialbd, date('h:i A', strtotime($time)));

		imagettftext($im, 9, 0, 201, 207, $black, $arialbd, "TYPE:");

		imagettftext($im, 9, 0, 242, 207, $blue, $arialbd, $ticket_type);

		imagettftext($im, 9, 0, 201, 228, $black, $arialbd, "PRICE:");

		imagettftext($im, 9, 0, 242, 228, $blue, $arialbd, "$".round($t_price, 2));

		imagettftext($im, 9, 0, 201, 261, $black, $arialbd, "RECIPIENTS:");

		imagettftext($im, 9, 0, 201, 275, $blue, $arialbd, $_SESSION['orderMember']['fname']." ".$_SESSION['orderMember']['lname']);

		imagettftext($im, 9, 0, 302, 261, $black, $arialbd, "REDEEM AT:");

		imagettftext($im, 9, 0, 302, 275, $blue, $arialbd, $c_province);

		imagettftext($im, 8, 0, 302, 288, $blue, $arial, $c_ddress);

		imagettftext($im, 8, 0, 302, 300, $blue, $arial, $c_city.", ");

		imagettftext($im, 8, 0, 362, 300, $blue, $arial, $c_postal_code);

		imagettftext($im, 9, 0, 419, 261, $black, $arialbd, "TICKET EXPIRES:");

		imagettftext($im, 9, 0, 419, 275, $blue, $arialbd, date('l, M. d, Y', strtotime($event_date)));

		imagecopymerge($im, $src, 89, 109, 0, 0, $eventImageWidth, $eventImageHeight, 100);

//		imagecopymerge($im, $src, 89, 109, 0, 0, 106, $eventImageHeight, 100);

		

	//	header ("Content-type: image/png");

		

		imagepng($im, '../tickets/'.$folderName.'/ticket'.rand().'.png');

	//	imagedestroy($im);	

	}}

	}}

	

$zipFileNam = "../tickets/".$folderName.".zip";



$zip = new ZipArchive();



if ($zip->open($zipFileNam, ZIPARCHIVE::CREATE)!==TRUE) 

    die("cannot open $folderName\n");



$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../tickets/$folderName/"));

foreach ($iterator as $key=>$value)

{

	$zip->addFile(realpath($key), $key) or die ("ERROR: Could not add file: $key");

}

$zip->close();





$dirname	=	"../tickets/".$folderName;

	if (is_dir($dirname))

		$dir_handle = opendir($dirname);

	if (!$dir_handle)

	return false;

		while($file = readdir($dir_handle)) {

			if ($file != "." && $file != "..") {

				if (!is_dir($dirname."/".$file)){

					unlink($dirname."/".$file);

					}

			else{

				delete_directory($dirname.'/'.$file);

				}

			}

		}

	closedir($dir_handle);





$zipNam = $folderName.".zip";

mysql_query("INSERT INTO `tickets_record` (`id`, `order_id`, `user_id`, `file_name`, `status`) VALUES (NULL, '$order_id', '$user_id', '$zipNam', '0');");







		$length		= strlen($c_number);

		$characters	= 4;

		$start		= $length - $characters;

		$c_number	= substr($c_number , $start ,$characters);

		$c_number	= "XXXX".$c_number;

		



mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`) VALUES (NULL, '$order_id', '$c_name', '$c_type', '$c_month', '$c_year', '$c_number', '$c_csc')");



mysql_query("INSERT INTO `billing_information` (`id`, `order_id`, `country`, `address`, `city`, `state`, `postal_code`, `telephone`, `email`) VALUES (NULL, '$order_id', '$c_country', '$c_address', '$c_city', '$c_province', '$c_postal_code', '$c_telephone', '$c_email');");





echo "order_id|".$order_id;





		} // END ELSE



?>

