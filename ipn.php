<?php
require_once('admin/database.php');
require_once('site_functions.php');

$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";


$dtl2 = explode("-",$_POST['custom']);
$itemType2 		= $dtl2[0]; // Ad, 

	$fp = fsockopen (PAYPAL_SERVICE_URL, 443, $errno, $errstr, 30);

	// mysql_query("UPDATE `events` SET `event_status` = '1' WHERE `id` = '$event_id'");

$item_name 			= $_POST['item_name'];
$item_number 		= $_POST['item_number'];
$payment_status 	= $_POST['payment_status'];
$payment_amount 	= $_POST['mc_gross'];
$payment_currency 	= $_POST['mc_currency'];
$txn_id 			= $_POST['txn_id'];
$receiver_email 	= $_POST['receiver_email'];
$payer_email 		= $_POST['payer_email'];

if (!$fp) {
// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
	
			$dtl = explode("-",$_POST['custom']);
			$itemType 		= $dtl[0]; // Ad, 
			$order_id		= $dtl[1];
		
			if (  $itemType == 'Ad' ) {
				$sql = "update sold_slots set amount='". $payment_amount ."',status=1 where order_id='$order_id'";
				mysql_query($sql);
			} // end $itemType == 'Ad'
			
			elseif (  $itemType == 'CF' ) {
				$sql = "update orders set total_price='". $payment_amount ."' where order_id='$order_id'";
				$rs = mysql_query($sql);
				if($rs){
					$event_id	=	getSingleColumn('main_ticket_id',"select * from `orders` where `order_id`='$order_id'");
					mysql_query("UPDATE `events` SET `event_status` = '1' WHERE `id` = '$event_id'");
					}
			} // end  $itemType == 'CF'
			
			elseif (  $itemType == 'BT' ) {
			
				$sql = "update orders set total_price='". $payment_amount ."' where order_id='$order_id'";
				$rs = mysql_query($sql);
				if($rs){
				
					$order_id	= getSingleColumn('id',"select * from `orders` where `order_id`='$order_id'");
					$email		= getSingleColumn('email',"select * from `paymeny_info` where `order_id`='$order_id'");
					
					$file_name = generateTicketsPDF($order_id);
					mysql_query("INSERT INTO `tickets_record` (`id`, `order_id`, `user_id`, `file_name`, `status`) VALUES (NULL, '$order_id', '$user_id', '$file_name', '0');");
					
					include("email_template.php");
					
					$a = explode("pdf/", $file_name);
					$to				= $email;
					$fileWithPath	= $file_name;
					$subject		= "Ticket";
					$fileType		= "pdf";
					$filename		= $a[1];
					
					$order_type	= getSingleColumn('type',"select * from `orders` where `order_id`='$order_id'");
					
					if($order_type=='table')
						$message = '';
					
					emailAttachment($to,$fileWithPath,$subject,$message,$fileType,$filename);
					
					$eventUserId	=	getSingleColumn('userid',"select * from `events` where `id`='$event_id'");
					$prmtr_email	=	getSingleColumn('email',"select * from `users` where `id`='$eventUserId'");
					
					$contents = $message;
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: "EventGrabber" <info@eventgrabber.com>' . "\r\n";
					$subject = "EventGrabber";
					
					@mail($prmtr_email,$subject,$contents,$headers);
					
					$_SESSION['orderDetail']='';

				} // end if ($rs)
			
			} // end $itemType == 'BT'
			
			$last_error = 'IPN Validation Success.<hr>' . $res ;
		}
		else if (strcmp ($res, "INVALID") == 0) {
			$last_error = 'IPN Validation Failed.<hr>' . $res ;
		}
	}
	fclose ($fp);
}

// mail("ali@bluecomp.net", "Paypal Response - EG", $last_error);

?>
