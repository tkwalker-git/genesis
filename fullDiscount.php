<?php

if($order_id){
	$sql = "update `orders` set `total_price`='0.01' where `id`='$order_id'";
				$rs = mysql_query($sql);
				if($rs){
				
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
					$subject 	= "EventGrabber";
					
					@mail($prmtr_email,$subject,$contents,$headers);
					
					$_SESSION['orderDetail']='';
					
					echo "<script>window.location.href='".ABSOLUTE_PATH."buy_tickets_step3.php?o=".base64_encode($order_id)."';</script>";
			
					exit();
			}
	}
?>