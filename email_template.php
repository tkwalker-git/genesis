<?php
$res = mysql_query("select * from `orders` where `id`='$order_id' && `type`='ticket'");
	if(mysql_num_rows($res)){
		while($row = mysql_fetch_array($res)){
			$total_price	= $row['total_price'];
			$order_date		= $row['date'];
			$event_id		= $row['main_ticket_id'];
			$net_total		= $row['net_total'];
			$discount		= $row['discount'];
			if($total_price == 0.01){
				$total_price = '0.00';
				}
			
		}
	
		
	
	$event_name		= getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");
	$venue_attrib	= getEventLocations($event_id);

	
	$dowlodUrlPath		= getSingleColumn('file_name',"select * from `tickets_record` where `order_id`='$order_id'");
	
	$dowlodUrlPath = base64_encode($dowlodUrlPath);
	
	ob_start();

?>

<div style="width:712px;font-size:12px;	margin:auto;padding-top:50px;">
  <div style="float:left;"><img src="<?php echo IMAGE_PATH; ?>logo4.gif" /></div>
  <div style="float:right;	padding:20px 0 0 0;"><img src="<?php echo IMAGE_PATH; ?>order_acknowledgment.png" /></div>
  <div style="clear:both;"></div>
  <div style="background:url(<?php echo IMAGE_PATH; ?>new_flayer_email_top.png) no-repeat; width:712px;	height:10px;">&nbsp;</div>
  <div style="background:url(<?php echo IMAGE_PATH; ?>new_flayer_email_middle.png) repeat-y;width:662px;padding:12px 24px 24px 26px;">
    <div style="float:left;"><strong>Order Number:</strong> <span style="color:#3b5998;"><u><?php echo $order_id;?></u></span></div>
    <div style="float:right;font-size:13px;">Ordered on <?php echo date('F d, Y', strtotime($order_date)); ?></div>
    <div style="clear:both;"></div>
    <br />
    <br />
    <table cellpadding="0" cellspacing="0" width="96%" align="center">
      <tr>
        <td><div style="font-size:14px;	font-weight:bold;color:#45bb96;"><?php echo $event_name; ?></div></td>
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
			$ticket_id		= $row['ticket_id'];
			$price			= $row['price'];
			$buyer_fee		= $row['buyer_fee'];
			
			$service_fees = $buyer_fee + $service_fees;
			?>
            <tr>
              <td style="border-bottom:#cccccc solid 1px;"><?php echo date('d M. Y', strtotime($row['date'])); ?></td>
              <td style="border-bottom:#cccccc solid 1px;"><font color="#3b5998">
                <?php
					echo getSingleColumn('title',"select * from `event_ticket_price` where `id`='$ticket_id'");
				?>
                </font></td>
              <td style="border-bottom:#cccccc solid 1px;" align="center"><?php echo $row['quantity']; ?></td>
              <td align="center">$<?php echo number_format($price, 2,'.',''); ?></td>
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
              <td align="center"> $<?php echo $net_total; ?></td>
            </tr>
          </table></td>
      </tr>
    </table>
    <div style="background:#f5f5f5;	border:#ececec solid 1px;	border-bottom:none;	padding:20px">
      <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td width="23%"><strong>Bill to</strong></td>
          <td width="31%"><?php
		  $res = mysql_query("select * from `paymeny_info` where `order_id`='$order_id'");
		  while($row = mysql_fetch_array($res)){
		  
		  $f_name		=	$row['f_name'];
		  $l_name		=	$row['l_name'];
		  $address		=	$row['address1'];
		  $city			=	$row['city'];
		  $country		=	$row['country'];
		  $email		=	$row['email'];
		  
		  }
		  ?>
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
		<div style="width:200px;border-bottom:#000000 solid 1px;padding-bottom:5px;margin-bottom:5px;">
			<table width="100%">
				<tr>
					<td>Subtotal:</td>
					<td>$<?php echo $net_total; ?></td>
				</tr>
				<?php if ($discount && $discount!=0){?>
				<tr>
					<td>Discount:</td>
					<td>-$<?php echo $discount; ?></td>
				</tr>
			  <?php }?>
				<tr>
					<td><strong>Order Total:</strong></td>
					<td><strong>$<?php echo $total_price; ?></strong></td>
				</tr>
			</table>
		</div>
    </div>
  </div>
  <div style="background:url(<?php echo IMAGE_PATH; ?>new_flayer_email_bottom.png) no-repeat;width:712px;height:10px;">&nbsp;</div>
</div>
<?php

$contents = ob_get_contents();
ob_clean();
$message = $contents;
	
	}
	else{
		$message ='';
		}
?>