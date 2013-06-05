<?php 

require_once('admin/database.php');
require_once('site_functions.php');

		
if ( $_POST['orderNow'] ){
	$_SESSION['orderDetail']['event_id']	  = $_POST['event_id'];
	$_SESSION['orderDetail']['qtys']		  = $_POST['qty'];
	$_SESSION['orderDetail']['ids']			  = $_POST['id'];
	$_SESSION['orderDetail']['dates']		  = $_POST['date'];
	$_SESSION['orderDetail']['discount_code'] = $_POST['discount_code'];
}



$disc	=  getSingleColumn("percent_value","select * from `coupons` where `code`='".$_SESSION['orderDetail']['discount_code']."'");

	if($disc == 100)
		$fullDisc	= 1;
	else
		$fullDisc	= 0;
		
if($_SESSION['orderDetail']==''){
	echo "Direct access to this page is not allowed.";
	exit();
	}
	
	
$event_id = $_SESSION['orderDetail']['event_id'];
$event_dateT		= getEventStartDateFB($event_id);
$bc_event_id	=	$event_id;
		
if (isset($_POST["submit"]) || isset($_POST["submit"]) ) {	

	$bc_fname			=	$_POST['fname'];
	$bc_lname			=	$_POST['lname'];
	$bc_email			=	$_POST['email'];
	$bc_address1		=	$_POST['address1'];
	$bc_address2		=	$_POST['address2'];
	$bc_city			=	$_POST['city'];
	$bc_zip				=	$_POST['zip'];
	$bc_country			=	$_POST['country'];
	$bc_name			=	$_POST["name"];
	$bc_card_type		=	$_POST['cardType'];
	$bc_month			=	$_POST['month'];
	$bc_year			=	$_POST['year'];
	$bc_card_number		=	$_POST['number'];
	$bc_securityCode	=	$_POST['securityCode'];
	$bc_t_buyer_name	=	$_POST['t_buyer_name'];
	
	$bc_t_buyer_email	=	$_POST['t_buyer_email'];
	
	
	if ( trim($bc_fname) == '' )
		$errors[] = 'First Name is required.';
	if ( trim($bc_lname) == '' )
		$errors[] = 'Last Name is required.';
	if(! validateEmail($bc_email) )
		$errors[] = 'Email is not valid.';
	if ( trim($bc_address1) == '' )
		$errors[] = 'Address Line 1 is required.';
	if ( trim($bc_city) == '' )
		$errors[] = 'City is required.';
	if ( trim($bc_zip) == '' )
		$errors[] = 'Postal Code is required.';
		
		
	/// ERR FOT TICKET BUYER START ///
	
	if(is_array($bc_t_buyer_name)){
		for($i = 0; $i < count($bc_t_buyer_name); $i++){
			$t = $i+1;
			if ( trim($bc_t_buyer_name[$i]) == '' )
				$errors[] = 'Full Name is required. (Ticket #'.$t.')';
			if(!validateEmail($bc_t_buyer_email[$i]))
				$errors[] = 'Email Address is invalid. (Ticket #'.$t.')';
		}	// end for loop
	} // end if is_array
	
	/// ERR FOT TICKET BUYER END ///

		
	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	
		$res = mysql_query("select * from `users` where `id`='$user_id'");
		while($row = mysql_fetch_array($res)){
			$bc_firstname	=	$row['firstname'];
			$bc_lastname	=	$row['lastname'];
			$bc_zip_code	=	$row['zip'];
		}
		
		
		$qtys	= $_SESSION['orderDetail']['qtys'];
		$ids	= $_SESSION['orderDetail']['ids'];
		$dates	= $_SESSION['orderDetail']['dates'];
			
		for($i=0;$i < count($ids);$i++){
		$s++;	
			
			if($qtys[$i]!='' && $qtys[$i]!='0'){
				$ticketDetail = getTicketDetail($ids[$i]);
				$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
				$buyer_service_free_after_percent		=	$ticketDetail['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
				$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
				$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');
				$totalAmount = (($ticketDetail['price'] + $finalServiceCharges) * $qtys[$i]) + $totalAmount;
				}
			}
		
		$discount	= calculateDiscount($event_id,$_SESSION['orderDetail']['discount_code'],$totalAmount);
		
		$net_total			= $totalAmount;
		$package_total		= number_format($totalAmount - $discount,2);
		
		
			$date		=	date('Y-m-d');
			$folderName	=	time();
			mkdir("tickets/".$folderName, 0700);
			if(calculateDiscount($event_id,$_SESSION['orderDetail']['discount_code'],$totalAmount)!=0){
				$coupon_code =	$_SESSION['orderDetail']['discount_code'];
			}
			
			$custom_order_id = time();
			
			$promoter_id	= getSingleColumn("userid","select * from `events` where `id`='".$event_id."'");
			
			$res = mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `discount`, `net_total`, `date`, `type`, `main_ticket_id`, `coupon_code`, `order_id`, `promoter_id`) VALUES (NULL, '$user_id', '0', '$discount', '$net_total', '$date', 'ticket', '$event_id', '$coupon_code', '$custom_order_id', '$promoter_id')");
			$order_id	=	mysql_insert_id();
			
			if($res){
		
				mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`, `f_name`, `l_name`, `address1`, `address2`, `city`, `zip`, `country`, `email`) VALUES (NULL, '$order_id', '$bc_name', '$bc_card_type', '$bc_month', '$bc_year', '$card_number', '$bc_securityCode', '$bc_fname' , '$bc_lname' , '$bc_address1' , '$bc_address2' , '$bc_city' , '$bc_zip' , '$bc_country','$bc_email')");
				
				
					$qtys	= $_SESSION['orderDetail']['qtys'];
					$ids	= $_SESSION['orderDetail']['ids'];
					$dates	= $_SESSION['orderDetail']['dates'];
					$g=0;
					for($i=0;$i < count($ids);$i++){
						$total_price='';
						$id			=	$ids[$i];
						$qty		=	$qtys[$i];
						$date_id	=	$dates[$i];
						
						if($qty!='' && $qty!=0){
							$date = getSingleColumn('event_date',"select * from `event_dates` where `id`='$date_id'");
							$time = getSingleColumn('start_time',"select * from `event_times` where `date_id`='$date_id'");
							$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");
							while($row = mysql_fetch_array($res)){	
								$price = $row['price'];
								$title = $row['title'];
								$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$bc_event_id");
								$buyer_service_free_after_percent		=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
								$buyer_service_free_after_percent		=	number_format($buyer_service_free_after_percent*$buyer/100,2);
								$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');
								$priceAfterQty			=	$price*$qty;
								$priceAfterQty			=	$priceAfterQty+($finalServiceCharges*$qty);
								$total_price			=	$priceAfterQty+$total_price;
								$forTicketOne			=	number_format($price+$buyer_service_free_after_percent, 2,'.','');
								$product_name			=	$row['title'];
								
								$total_fee				=	number_format((($price*TICKET_FEE_PERSENT)/100)+TICKET_FEE_PLUS,2);
								
								for($e=0;$e<$qty;$e++){
									$maxValue = getMaxValue("order_tickets","ticket_number");
									$t_buyer_name	=	$bc_t_buyer_name[$g];
									$t_buyer_email	=	$bc_t_buyer_email[$g];
									$ticket_number = $maxValue+1;
									mysql_query("INSERT INTO `order_tickets` (`id`, `ticket_id`, `price`, `product_name`, `eg_fee`, `service_fee`, `total_fee`, `buyer_fee`, `quantity`, `date`, `order_id`, `t_time`, `name`, `email`, `ticket_number`) VALUES (NULL, '$id', '$price', '$product_name', '".TICKET_FEE_PLUS."', '".TICKET_FEE_PERSENT."', '$total_fee', '$buyer_service_free_after_percent', '1', '$date', '$order_id', '$time', '$t_buyer_name', '$t_buyer_email', '$ticket_number');");
									$g++;
								} // end for
							} // end while
						} // end if($qty!='' && $qty!=0)
					} // end for
				
				$event_url = getEventURL($event_id);
				$event_name = getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");
				
				$sql_t_count	= mysql_query("select COUNT(id) as tQty  from `order_tickets` where `order_id` = '$order_id'");
				while($row_count = mysql_fetch_array($sql_t_count)){
					$ticketQTY	= $row_count[tQty];
				}
				
				$_SESSION['orderDetail']['discount_code'] = '';
				
				
				if($fullDisc == 1)
					include_once('fullDiscount.php');
				?>
				
			<form name="buyAd" action="<?php echo PAYPAL_URL; ?>" method="post">
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
			
			<?php	
			
				/* echo "<script>window.location.href='buy_tickets_step3.php?order=".$order_id."'</script>"; */
				
			} // end if ($res)
			
			 else {
				$sucMessage = "Error: Please try Later";
			}
	
	}
	else{
		$sucMessage = $err;
		}
		}


$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];


$event_name = getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");

	$event_date			= getEventDates($event_id);
	$venue_attrib		= getEventLocations($event_id);



$meta_title	= 'Buy Tickets';
include_once('includes/header.php');
?>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Buy Tickets</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:1px;">
            <!--Start new code-->
            <span class="ew-heading"><?php echo $event_name; ?></span>
            <!--end ew-heading-->
            <span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo date('M d, Y',strtotime($event_dateT[0])); ?></span> <span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo $venue_attrib[1]['venue_name']; ?></span>
            <!--<span class="dotted-separator"></span>-->
            <br />
            <form method="post" action="buy_tickets_step2.php">
              <table cellpadding="10" cellspacing="0" width="100%">
                <tr bgcolor="#e4f0d8" style="font-size:22px">
                  <td width="28%">TICKET TYPE</strong></td>
                  <td width="18%" align="center">PRICE</strong></td>
                  <td width="18%" align="center">FEE</strong></td>
                  <td width="18%" align="center">QUANTITY</td>
                  <td width="18%" align="center">TOTAL</td>
                </tr>
                <?php
			$bg = '#e4f0d8';
			
			$qtys	= $_SESSION['orderDetail']['qtys'];
			$ids	= $_SESSION['orderDetail']['ids'];
			$dates	= $_SESSION['orderDetail']['dates'];
			$totalAmount = 0;
			for($i=0;$i < count($ids);$i++){
			$s++;
			
				if($qtys[$i]!='' && $qtys[$i]!='0'){
			
				$ticketDetail = getTicketDetail($ids[$i]);
				
					if($bg == '#e4f0d8')
						$bg = '#d1e5c0';
					else
						$bg = '#e4f0d8';
				$ticket_description = $ticketDetail['desc'];
			?>
                <tr bgcolor="<?php echo $bg; ?>" style="font-size:20px">
                  <td><?php echo $ticketDetail['title']; ?> <small style="font-size:12px;"><?php echo substr($ticket_description,0,150); ?></small></td>
                  <td align="center">$<?php echo number_format($ticketDetail['price'], 2,'.',''); ?></td>
                  <td align="center">$<?php			
				$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
				$buyer_service_free_after_percent		=	$ticketDetail['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
				$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
				echo $finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');?>
                    </strong></td>
                  <td align="center"><?php echo $qtys[$i]; ?></td>
                  <td align="center"><?php
			$totalAmount = (($ticketDetail['price'] + $finalServiceCharges) * $qtys[$i]) + $totalAmount;
			echo "$".number_format(($ticketDetail['price']+$finalServiceCharges)*$qtys[$i], 2,'.','');
			?></td>
                </tr>
                <?php } }
		
		if($bg == '#e4f0d8')
						$bg = '#d1e5c0';
					else
						$bg = '#e4f0d8';
		if(calculateDiscount($event_id,$_SESSION['orderDetail']['discount_code'],$totalAmount)!=0){?>
                <tr bgcolor="<?php echo $bg; ?>"  style="font-size:22px">
                  <td colspan="4" align="right"><strong>DISCOUNT</strong></td>
                  <td align="center"><strong ><?php echo "$".number_format(calculateDiscount($event_id,$_SESSION['orderDetail']['discount_code'],$totalAmount), 2,'.','')?></strong></td>
                </tr>
                <?php }?>
                <tr bgcolor="<?php echo $bg; ?>" style="font-size:24px">
                  <td colspan="4" align="right"><strong>TOTAL AMOUNT DUE</strong></td>
                  <td align="center"><strong style="color:#ec0000">
				  <?php $finalTotal = $totalAmount-calculateDiscount($event_id,$_SESSION['orderDetail']['discount_code'],$totalAmount);
				   echo "$".number_format($finalTotal, 2);?></strong></td>
                </tr>
              </table>
            </form>
            <!--End new code-->
            <div class="borderDoted"></div>
            <div class="pymnt_details"><br>
              <?php
			  echo $sucMessage;
			//  $suc=0;
			  if ( $suc != 1 ){ ?>
              <form method="post" name="bc_form" enctype="multipart/form-data" action="">
			  
					<div class="title"> <?php if ($fullDisc!=1){ echo 'Cardholder Information'; } else{ echo 'Your Information'; } ?></div>
					<div class="clr"></div>
					<div class="evField">First Name:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="eventname"  name="fname" value="<?php echo $bc_fname; ?>" style="width:300px" />
					</div>
					<div class="clr"></div>
					<div class="evField">Last Name:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="eventname"  name="lname" value="<?php echo $bc_lname; ?>" style="width:300px" />
					</div>
					<div class="clr"></div>
					<div class="evField">Email:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="email"  name="email" value="<?php echo $bc_email; ?>" style="width:300px" />
					</div>
					<div class="clr"></div>
					<div class="evField">Address Line 1:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="eventname"  name="address1" value="<?php echo $bc_address1; ?>" style="width:300px" />
					</div>
					<div class="clr"></div>
					<div class="evField">Address Line 2:</div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
					</div>
					<div class="clr"></div>
					<div class="evField">City:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="eventname"  name="city" value="<?php echo $bc_city; ?>" style="width:300px" />
					</div>
					<div class="clr"></div>
					<div class="evField">Postal Code:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <input type="text" maxlength="100" id="eventname"  name="zip" value="<?php echo $bc_zip; ?>" style="width:70px" />
					</div>
					<div class="clr"></div>
					<div class="evField">Country:<font color="#FF0000">*</font></div>
					<div class="evLabal" style="width:320px">
					  <select style="padding:2px; width:300px" name="country" id="country">
						<option <?php if ($b_country=='US' || $b_country==''){ echo 'selected="selected"'; } ?> value="US">United States</option>
					  </select>
					</div>
					<div class="clr"></div>
				
                <!-- Ticket Information Start -->
                <div class="title"> Ticket Information</div>
                <div class="clr">&nbsp;</div>
                <?php
			   
			$qtys	= $_SESSION['orderDetail']['qtys'];
			$ids	= $_SESSION['orderDetail']['ids'];
			$dates	= $_SESSION['orderDetail']['dates'];
			$s=0;
			$x=0;
			for($i=0;$i< count($ids); $i++) {
				$id		=	$ids[$i];
				$qty	=	$qtys[$i];
				
			for($z=0;$z < $qty; $z++) {
			$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");
			while($row = mysql_fetch_array($res)){	
				$price = $row['price'];
				$title = $row['title'];
				$s++;
				?>
                <strong style="font-size:14px">Ticket #<?php echo $s; ?> - <?php echo $title; ?></strong>
                <div class="clr"></div>
                <div class="evField">Full Name:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="" name="t_buyer_name[]" value="<?php echo $bc_t_buyer_name[$x]; ?>" style="width:300px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Email Address:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="" name="t_buyer_email[]" value="<?php echo $bc_t_buyer_email[$x]; ?>" style="width:300px" />
                </div>
				<?php if ($s!=$qty){?>
	                <div class="clr" style="border-bottom:#eee solid 1px; margin-bottom:6px; width:97%"></div>
                <?php
					}
			  $x++;
				}
			}
			}
			?>
                <!-- Ticket Information End -->
                <!--<div class="title"> Payment Information</div>
                <div class="clr"></div>
                <div class="evField">Name on Card:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Credit Card Type:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <select style="width:150px" name="cardType">
                    <option value="">--Select--</option>
                    <option value="visa" <?php if($bc_card_type=="visa"){ echo 'selected="selected"'; }?>>Visa</option>
                    <option value="mastercard"<?php if($bc_card_type=="mastercard"){ echo 'selected="selected"'; }?>>MasterCard</option>
                    <option value="AMEX"<?php if($bc_card_type=="AMEX"){ echo 'selected="selected"'; }?>>AMEX</option>
                    <option value="jcb"<?php if($bc_card_type=="jcb"){ echo 'selected="selected"'; }?>>JCB</option>
                  </select>
                </div>
                <div class="clr"></div>
                <div class="evField">Expiration date (Month/Year):<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <select name="month" style="width:75px" id="dalete1">
                    <option value="">Month</option>
                    <?php
	  for($i=1;$i<13;$i++){
		?>
                    <option value="<?php echo $i; ?>" <?php if($bc_month==$i){ echo 'selected="selected"'; }?>><?php echo str_pad($i,2,0,STR_PAD_LEFT); ?></option>
                    <?php } ?>
                  </select>
                  &nbsp;
                  <select name="year" style="width:85px" id="delete2">
                    <option value="">Year</option>
                    <?php
					for($i=date('Y');$i<date('Y')+10;$i++){
						?>
                    <option value="<?php echo $i; ?>" <?php if($bc_year==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="clr"></div>
                <div class="evField">Credit Card Number: <font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="number" value="<?php echo $bc_card_number; ?>" style="width:300px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Security Code (CVV): <font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="securityCode" value="<?php echo $bc_securityCode; ?>" style="width:300px" />
                </div>-->
                <div class="clr"></div>
                <div class="evField">&nbsp;</div>
                <div class="ev_fltlft" style="width:308px" align="right">
				<?php if($fullDisc!=1){?>
					<input type="image" src="<?php echo IMAGE_PATH; ?>paypal_checkout.png" name="submit" value="Submit">
					<br><br>
					<img src="<?php echo IMAGE_PATH; ?>paypal_icons.gif" />
				<?php }else{ ?>
                <br />

					<input type="image" src="<?php echo IMAGE_PATH; ?>submit_btn.jpg" />
				<?php } ?>
				<input type="hidden" name="submit" value="Submit">
				  
				  
				  
                </div>
                <div class="clr"></div>
              </form>
              <?php }?>
            </div>
            <div class="securtyText">You can shop at <strong>www.eventgrabber.com</strong> with confidence. We have partnered with <a href="http://www.authorize.net" target="_blank">Authorize.Net</a>, a leading payment gateway since 1996, to accept credit cards and electronic check payments safely and securely for our customers.<br />
              <br />
              The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks. See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="_blank">online payments diagram</a> to see how it works.<br />
              <br />
              The company adheres to strict industry standards for payment processing, including:
              <ul>
                <li>128-bit Secure Sockets Layer (SSL) technology for secure Internet Protocol (IP) transactions.</li>
                <li>Industry leading encryption hardware and software methods and security protocols to protect customer information.</li>
                <li>Compliance with the Payment Card Industry Data Security Standard (PCI DSS).</li>
              </ul>
              For additional information regarding the privacy of your sensitive cardholder data, please read the <a href="http://www.authorize.net/company/privacy/" target="_blank">Authorize.Net Privacy Policy</a>. <strong>www.eventgrabber.com</strong> is registered with the Authorize.Net Verified Merchant Seal program. <br>
              <br>
              <div class="AuthorizeNetSeal" style="padding:3px 0; margin:auto">
                <script>
		var ANS_customer_id="66c2e366-3b5b-46ac-8044-75be4c0c6909";
	</script>
                <script type="text/javascript" language="javascript" src="https://verify.authorize.net/anetseal/seal.js" ></script>
              </div>

              <div style="text-align:center"><img src="<?php echo IMAGE_PATH; ?>cc_cards.png" /></div>
            </div>
            <div class="clr"></div>
          </div>
        </div>
      </div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>
