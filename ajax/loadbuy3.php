<?php

require_once('../admin/database.php');

require_once('../site_functions.php');

	

	$active='buy';

	

	$discount_code	= $_SESSION['ticketOrder']['discount_code'];

	

	

$disc	=  getSingleColumn("percent_value","select * from `coupons` where `code`='".$discount_code."'");



	if($disc == 100)

		$fullDisc	= 1;

	else

		$fullDisc	= 0;	

	

	$gender				= $_POST['gender'];

	$fname				= $_POST['fname'];

	$lname				= $_POST['lname'];

	$city				= $_POST['city'];

	$dob				= date('Y-m-d', strtotime($_POST['dob']));

	$email				= $_POST['email'];

	$discount_code		= $_SESSION['ticketOrder']['discount_code'];

	$ticket_buyer_name	= $_POST['ticket_buyer_name'];

	$ticket_buyer_email	= $_POST['ticket_buyer_email'];

	$date				= date('Y-m-d');

	$bc_event_id		= $_SESSION['ticketOrder']['event_id'];

	$custom_order_id	= time();

	$event_id			= $bc_event_id;

	

	$promoter_id	= getSingleColumn("userid","select * from `events` where `id`='".$bc_event_id."'");

	

	$res = mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`, `main_ticket_id`, `order_id`, `promoter_id`) VALUES (NULL, '$user_id', '0', '$date', 'ticket', '$bc_event_id', '$custom_order_id', '$promoter_id')");

	$order_id = mysql_insert_id();

	

	if($res){

	

		mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`, `f_name`, `l_name`, `address1`, `city`, `zip`, `country`, `email`, `state`, `phone` ) VALUES (NULL, '$order_id', '$c_name', '$c_type', '$c_month', '$c_year', '$c_number', '$c_csc', '$fname', '$lname', '$c_address', '$city', '$c_postal_code', '$c_country', '$email', '$c_province', '$c_telephone')");

	

		$qtys				= explode(",", $_SESSION['ticketOrder']['ticket_qty']);

		$ids				= explode(",", $_SESSION['ticketOrder']['ticket_id']);

		$dates				= explode(",", $_SESSION['ticketOrder']['ticket_date']);

		$bc_t_buyer_name	= explode(",", $ticket_buyer_name);

		$bc_t_buyer_email	= explode(",", $ticket_buyer_email);

			

		$service_charge = 1.00;

		$total_price	=	0;

		$g=0;

		for($i=0;$i< count($ids); $i++) {

			$id			= $ids[$i];

			$qty		= $qtys[$i];

			$date_id	= $dates[$i];

			$total_price='';

			if($qty!='' && $qty!=0){

				$ev_date	= getSingleColumn('event_date',"select * from event_dates where id=$date_id");

				$t_time		= getSingleColumn('start_time',"select * from event_times where date_id=$date_id");

				$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");

				while($row = mysql_fetch_array($res)){	

					$price = $row['price'];

					$title = $row['title'];

					$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$bc_event_id");

					$buyer_service_free_after_percent	=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;

					$buyer_service_free_after_percent	=	$buyer_service_free_after_percent*$buyer/100;

					$finalServiceCharges				=	number_format($buyer_service_free_after_percent, 2,'.','');

					$priceAfterQty						=	$price*$qty;

					$priceAfterQty						=	$priceAfterQty+($finalServiceCharges*$qty);

					$total_prices						=	$priceAfterQty+$total_prices;

					$forTicketOne						=	number_format($price+$buyer_service_free_after_percent, 2,'.','');

			

					for($e=0;$e<$qty;$e++){

						$maxValue = getMaxValue("order_tickets","ticket_number");

						$t_buyer_name	= $bc_t_buyer_name[$g];

						$t_buyer_email	= $bc_t_buyer_email[$g];

						$ticket_number	= $maxValue+1;

						mysql_query("INSERT INTO `order_tickets` (`id`, `ticket_id`, `price`, `quantity`, `date`, `order_id`, `t_time`, `name`, `email`, `ticket_number`) VALUES (NULL, '$id', '$price', '1', '$ev_date', '$order_id', '$t_time', '$t_buyer_name', '$t_buyer_email', '$ticket_number');");

						$g++;

						} // end for

				} // end while

			} // end if

		} // end for



			$package_total = number_format(($total_prices+$service_charge)-calculateDiscount($bc_event_id,$discount_code,$total_prices), 2,'.','');

		

		if(calculateDiscount($bc_event_id,$discount_code,$total_prices)!=0)

			mysql_query("update `orders` set `coupon_code`='$discount_code' where `id`='$order_id'");

		

			

	

	}  // end if($res)

	



	$event_id		= $bc_event_id;

	$event_name		= getSingleColumn("event_name","select * from `events` where `id` = '$event_id'");

	

	$event_url = getEventURL($event_id);

	$event_name = getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");

	

	$sql_t_count	= mysql_query("select COUNT(id) as tQty  from `order_tickets` where `order_id` = '$order_id'");

	while($row_count = mysql_fetch_array($sql_t_count)){

		$ticketQTY	= $row_count[tQty];

	}

include("../flayerMenu.php");

$event_url = getEventURL($bc_event_id);



?>



<div class="inrDiv" style="min-height:500px"><br />

  <div class="progresbar<?php if ($fullDisc == 1){ echo "4";}else{ echo "3";}?>"></div><br />

  <?php

  if($fullDisc == 1){





	if($order_id){

		$sql = "update `orders` set `total_price`='0.01' where `id`='$order_id'";

					$rs = mysql_query($sql);

					if($rs){

					

						$email		= getSingleColumn('email',"select * from `paymeny_info` where `order_id`='$order_id'");

						

						$file_name = generateTicketsPDF($order_id);

						

						mysql_query("INSERT INTO `tickets_record` (`id`, `order_id`, `user_id`, `file_name`, `status`) VALUES (NULL, '$order_id', '$user_id', '$file_name', '0');");

						

						include("../email_template.php");

						

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

						

						echo "Check your mail box for Download your tickets";

				}

		}

		

}

else{

?>

 	<form name="buyAd" action="<?php echo PAYPAL_URL; ?>" method="post" target="_top">

				<input type="hidden" name="cmd" value="_xclick" />

				<input type="hidden" name="business" value="<?=BUSINESS_EMAIL?>" />

				<input type="hidden" name="item_name" value="Buy Tickets (<?php echo $event_name; ?>) (Tickets QTY <?php echo $ticketQTY; ?>)" />

				<input type="hidden" name="item_number" value="1" />

				<input type="hidden" name="amount" value="<?=$package_total?>" />

				<input type="hidden" name="currency_code" value="USD" />

				<input type="hidden" name="shipping" value="0.00" />

				<input type="hidden" name="custom" value="BT-<?php echo $custom_order_id?>" />

				<input type="hidden" name="shipping2" value="0.00" />

				<input type="hidden" name="handling" value="0.00" />

				<input type="hidden" name="undefined_quantity" value="0" />

				<input type="hidden" name="receiver_email" value="<?=BUSINESS_EMAIL?>" />

				<input type="hidden" name="no_shipping" value="1" />

				<input type="hidden" name="no_note" value="0" />

				<input type="hidden" name="notify_url" value="<?=IPN_URL?>">

				<input type="hidden" name="return" value="<?php echo $event_url; ?>">

				<input type="hidden" name="cancel_return" value="<?php echo $event_url; ?>">

				<input  type="submit" value="Click here if you are not redirected within 5 seconds" />

			</form>

			<script language="javascript">

				setTimeout("SubForm()", 0); 



				function SubForm() {

					document.buyAd.submit();

				}

			</script>

 <?php } ?>

  

</div>