<?php 
require_once('admin/database.php');
require_once('site_functions.php');

	$order_id = base64_decode($_GET['o']);
	
$res = mysql_query("select * from `orders` where `id`='$order_id' && `type`='ticket'");
	if(mysql_num_rows($res)){
		while($row = mysql_fetch_array($res)){
			$total_price	= $row['total_price'];
			if($total_price == 0.01){
				$total_price = '0.00';
				}
			
			$order_date		= $row['date'];
			$event_id		= $row['main_ticket_id'];
			$net_total		= $row['net_total'];
			$discount		= $row['discount'];
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Order Acknowledgement</title>
<style>
body{
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
<script>
$(document).ready(function(){
	$('#download').click(function(){
	window.open("download.php?fn=<?php echo $dowlodUrlPath; ?>");
	});
});
</script>
</head>
<body>
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
	  <strong>An email has been sent to <?php echo $email; ?>, Check mail box for download tickets</strong>
	<!-- <div><img src="<?php echo IMAGE_PATH; ?>new_flayer_downloadButton.png" id="download" style="cursor:pointer" align="right" /></div>--><br />

  </div>
  <div class="new_flayer_email_bottom">&nbsp;</div>
</div>

</body>
</html>
<?php

$contents = ob_get_contents();
ob_clean();
echo $contents;

?>