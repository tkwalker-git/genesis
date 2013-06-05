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



	$date			= date('Y-m-d');

	$bc_event_id	= $_SESSION['orderMember']['event_id'];

	$main_ticket_id = getSingleColumn('id',"select * from `event_ticket` where `event_id`='$bc_event_id'");

	$gender			= $_SESSION['orderMember']['gender'];

	$fname			= $_SESSION['orderMember']['fname'];

	$lname			= $_SESSION['orderMember']['lname'];

	$city			= $_SESSION['orderMember']['city'];

	$dob			= date('Y-m-d', strtotime($_SESSION['orderMember']['dob']));

	$email			= $_SESSION['orderMember']['email'];

	$discount_code	= $_SESSION['ticketOrder']['discount_code'];



	mysql_query("INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `sex`, `dob`, `city`, `send_recommended_events`) VALUES (NULL, '$fname', '$lname', '$email', '$gender', '$dob', '$city', '0');");

	$user_id = mysql_insert_id();

	

mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`, `main_ticket_id`, `coupon_code`) VALUES (NULL, '$user_id', '$grand_total', '$date', 'ticket', '$main_ticket_id', '$discount_code')");

	

	$order_id			= mysql_insert_id();

	$qtys				= explode(",", $_SESSION['ticketOrder']['ticket_qty']);

	$ids				= explode(",", $_SESSION['ticketOrder']['ticket_id']);

	$dates				= explode(",", $_SESSION['ticketOrder']['ticket_date']);

	$bc_t_buyer_name	= explode(",", $_SESSION['orderMember']['ticket_buyer_name']);

	$bc_t_buyer_email	= explode(",", $_SESSION['orderMember']['ticket_buyer_email']);

		

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

			$total_price						=	$priceAfterQty+$total_price;

			$forTicketOne						=	number_format($price+$buyer_service_free_after_percent, 2,'.','');

	

			for($e=0;$e<$qty;$e++){

				$maxValue = getMaxValue("order_tickets","ticket_number");

				$t_buyer_name	= $bc_t_buyer_name[$g];

				$t_buyer_email	= $bc_t_buyer_email[$g];

				$ticket_number	= $maxValue+1;

				mysql_query("INSERT INTO `order_tickets` (`id`, `ticket_id`, `price`, `quantity`, `date`, `order_id`, `t_time`, `name`, `email`, `ticket_number`) VALUES (NULL, '$id', '$priceAfterQty', '$qty', '$ev_date', '$order_id', '$t_time', '$t_buyer_name', '$t_buyer_email', '$ticket_number');");

				$g++;

				}

			}

		}

	}





	$length		= strlen($c_number);

	$characters	= 4;

	$start		= $length - $characters;

	$c_number	= substr($c_number , $start ,$characters);

	$c_number	= "XXXX".$c_number;

		

		

	mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`, `address1`, `city`, `zip`, `country`, `email`, `state`, `phone` ) VALUES (NULL, '$order_id', '$c_name', '$c_type', '$c_month', '$c_year', '$c_number', '$c_csc', '$c_address', '$c_city', '$c_postal_code', '$c_country', '$c_email', '$c_province', '$c_telephone')");



	$file_name = generateTicketsPDF($order_id);

	mysql_query("INSERT INTO `tickets_record` (`id`, `order_id`, `user_id`, `file_name`, `status`) VALUES (NULL, '$order_id', '$user_id', '$file_name', '0');");

	

	$_SESSION['ticketOrder'] = '';

	$_SESSION['orderMember'] = '';

	

	

	

	

	

	

	

		$res = mysql_query("select * from `orders` where `id`='$order_id' && `type`='ticket'");

	if(mysql_num_rows($res)){

		while($row = mysql_fetch_array($res)){

			$total_price	=	$row['total_price'];

			$order_date		= 	$row['date'];

			$event_id		=	$row['main_ticket_id'];

		}

	}

	else{

		echo "<script>window.location.href='index.php';</script>";

	}

		

	

	$event_name		= getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");

	$venue_attrib	= getEventLocations($event_id);



	

	$dowlodUrlPath		= getSingleColumn('file_name',"select * from `tickets_record` where `order_id`='$order_id'");

	

	$dowlodUrlPath = base64_encode($dowlodUrlPath);

	

	ob_start();



?>



<style>

.main{

	font-family:Arial, Helvetica, sans-serif;

	font-size:12px;

	margin:0;

	padding:0;

	background:#fff;

	}

	

ul, li, h1, h2, h3, h4, h5, h6{

	margin:0;

	padding:0;

	list-style:none;

	}

	

a img{

	border:0;

	}

	

.clear{

	clear:both;

	}

	

.main{

	width:712px;

	margin:auto;

	padding-top:50px;

	}

	

.order_acknowledgment{

	float:right;

	padding:20px 0 0 0;

	}

	

.email_logo{

	float:left;

	}

	

.new_flayer_email_top{

	background:url(<?php echo IMAGE_PATH; ?>new_flayer_email_top.png) no-repeat;

	width:712px;

	height:10px;

	}

	

.new_flayer_email_middle{

	background:url(<?php echo IMAGE_PATH; ?>new_flayer_email_middle.png) repeat-y;

	width:662px;

	padding:12px 24px 24px 26px;

	}



.new_flayer_email_bottom{

	background:url(<?php echo IMAGE_PATH; ?>new_flayer_email_bottom.png) no-repeat;

	width:712px;

	height:10px;

	}

	

.new_blue{

	color:#3b5998;

	}

	

.order_number{

	float:left;

	}



.order_number strong{

	font-size:14px

	}

	

.order_date{

	float:right;

	font-size:13px;

	}

	

.new_flayer_title{

	font-size:14px;

	font-weight:bold;

	color:#45bb96;

	}

	

.ship_to{

	background:#f5f5f5;

	border:#ececec solid 1px;

	border-bottom:none;

	padding:20px

	}



.email_order_total{

	width:200px;

	border-bottom:#000000 solid 1px;

	padding-bottom:5px;

	margin-bottom:5px;

	}

</style>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>

<div class="main">

  <div class="email_logo"><img src="<?php echo IMAGE_PATH; ?>logo4.gif" /></div>

  <div class="order_acknowledgment"><img src="<?php echo IMAGE_PATH; ?>order_acknowledgment.png" /></div>

  <div class="clear"></div>

  <div class="new_flayer_email_top">&nbsp;</div>

  <div class="new_flayer_email_middle">

    <div class="order_number"><strong>Order Number:</strong> <span class="new_blue"><u><?php echo $_GET['order'];?></u></span></div>

    <div class="order_date">Ordered on <?php echo date('F d, Y', strtotime($order_date)); ?></div>

    <div class="clear"></div>

    <br />

    <br />

    <table cellpadding="0" cellspacing="0" width="96%" align="center">

      <tr>

        <td><div class="new_flayer_title"><?php echo $event_name; ?></div></td>

      </tr>

      <tr>

        <td height="22"><strong>Venue: <?php echo $venue_attrib[1]['venue_name']; ?></strong> </td>

      </tr>

      <tr>

        <td valign="top"><br />

          <table width="100%" cellspacing="0" cellpadding="7">

            <tr bgcolor="#f5f5f5">

              <td width="23%"><strong>Date</strong></td>

              <td width="41%"><strong>Product</strong></td>

              <td width="20%" align="center"><strong>Amount</strong></td>

              <td width="16%" align="center"><strong>Price</strong></td>

            </tr>

            <?php

			$res = mysql_query("select * from `order_tickets` where `order_id`='$order_id'");

			$total = 0;

			while($row = mysql_fetch_array($res)){

			$ticket_id = $row['ticket_id'];

			$price = getSingleColumn('price',"select * from `event_ticket_price` where `id`='$ticket_id'");

			

			$main_ticket_id = getSingleColumn('ticket_id',"select * from `event_ticket_price` where `id`='$ticket_id'");

			

			$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where id=$main_ticket_id");

			$buyer_service_free_after_percent	=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;

			$buyer_service_free_after_percent	=	$buyer_service_free_after_percent*$buyer/100;

			$finalServiceCharges				=	number_format($buyer_service_free_after_percent, 2,'.','');

			

			$service_fees = $finalServiceCharges + $service_fees;

			?>

            <tr>

              <td style="border-bottom:#cccccc solid 1px;"><?php echo date('d M. Y', strtotime($row['date'])); ?></td>

              <td style="border-bottom:#cccccc solid 1px;"><font color="#3b5998">

                <?php

					echo getSingleColumn('title',"select * from `event_ticket_price` where `id`='$ticket_id'");

				?>

                </font></td>

              <td style="border-bottom:#cccccc solid 1px;" align="center"><?php echo $row['quantity']; ?></td>

              <td align="center">$<?php echo number_format($price, 2,'.','');

			  $total = $price + $total;

			   ?></td>

            </tr>

            <?php } ?>

            <tr>

              <td></td>

              <td><font color="#3b5998">Service fees</font></td>

			  <td></td>

			  <td align="center">$<?php echo number_format($service_fees, 2,'.',''); ?></td>

			  </tr>

			  <tr>

			  <td></td>

			  <td><font color="#3b5998">Total</font></td>

              <td></td>

              <td align="center">

                $<?php echo number_format($total+$service_fees, 2,'.',''); ?></td>

            </tr>

          </table></td>

      </tr>

    </table>

    <div class="ship_to">

      <table cellpadding="0" cellspacing="0" width="100%">

        <tr>

          <td width="23%"><strong>Bill to</strong></td>

          <td width="31%">

		  <?php

		  $res = mysql_query("select * from `paymeny_info` where `order_id`='$order_id'");

		  while($row = mysql_fetch_array($res)){

		  

		  $f_name		=	$row['f_name'];

		  $l_name		=	$row['l_name'];

		  $address		=	$row['address1'];

		  $city			=	$row['city'];

		  $country		=	$row['country'];

		  $email		=	$row['email'];

		  

		  } ?>

		  <?php echo $f_name." ".$l_name."<br />".$address."<br>".$city.", ".$country; ?>

            <!--<span class="new_blue">(407)720-5280</span>--></td>

          <td width="20%">&nbsp;</td>

          <td width="26%">&nbsp;</td>

        </tr>

      </table>

    </div>

    <img src="<?php echo IMAGE_PATH; ?>new_flayer_email_shadow.gif" />

    <!--    <table cellpadding="24" cellspacing="0" width="100%" style="border-bottom:#000000 dotted 1px;">

      <tr>

        <td  width="21%"><strong>Bill to:</strong></td>

        <td width="52%">3975 Cesare St<br />

          Orlando FL 32839-6441<br />

          <span class="new_blue">(407)720-5280</span></td>

        <td width="13%"></td>

        <td width="14%"></td>

      </tr>

    </table> -->

    <br />

    <div align="right">

      <div class="email_order_total">Subtotal &nbsp; $<?php echo number_format($total+$service_fees, 2,'.',''); ?><br />

      

        <strong>Order Total       $<?php echo number_format($total+$service_fees, 2,'.',''); ?></strong> </div>

		

      </div>

	 



  </div>

  <div class="new_flayer_email_bottom">&nbsp;</div>

</div>

<?php



$contents = ob_get_contents();

ob_clean();

echo $contents;



// Send order acknowldgement email

$headers  = 'MIME-Version: 1.0' . "\r\n";

$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$headers .= 'From: "EventGrabber" <events@eventgrabber.com>' . "\r\n";

$subject = "EventGrabber - Order Acknowledgment";



@mail($email,$subject,$contents,$headers);





	echo "<order>order_id|".$order_id;

	

	} // END ELSE



?>

