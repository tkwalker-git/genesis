<?php

if($_POST['discount_code']!=''){
	$_SESSION['ticketOrder']['discount_code'] = $_POST['discount_code'];
}

$venue_attrib		= getEventLocations($event_id);

?>
<table cellpadding="5" cellspacing="5" width="100%" bgcolor="#f3f6ea">
  <tr>
    <td width="20%">Event:</td>
    <td width="48%"><strong><?php
	if(strlen(trim($event_name)) > 27)
		echo substr(trim($event_name),0,27)."...";
	else
		echo trim($event_name);
		 ?></strong></td>
	<td width="32%" rowspan="2" align="center"><img src="<?php echo IMAGE_PATH; ?>tweet.png" style="cursor:pointer" /></td>
  </tr>
  <tr>
    <td>Venue:</td>
    <td><strong><?php echo $venue_attrib[1]['venue_name']; ?></strong></td>
  </tr>
</table>
<?php
if($sc!=1){?>
	<div style="height: 416px; overflow: auto;">
	<?php
	}
	?>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
 <td colspan="4" valign="top">
 <table cellpadding="7" cellspacing="0" width="100%">
      <tr bgcolor="#d1e5c0">
        <td width="24%"><strong>Date</strong></td>
        <td width="47%"><strong>Product</strong></td>
        <td width="15%" align="center"><strong>Amount</strong></td>
        <td width="14%" align="center"><strong>Price</strong></td>
      </tr>
      <?php
			
				$qtys	= explode(",",$_SESSION['ticketOrder']['ticket_qty']);
				$ids	= explode(",",$_SESSION['ticketOrder']['ticket_id']);
				$dates	= explode(",",$_SESSION['ticketOrder']['ticket_date']);
			
				$service_charge = 0;
				
				for($i=0;$i<count($qtys);$i++){
					if($qtys[$i]!=0 && $qtys[$i]!=''){
					
					$ticketDetail = getTicketDetail($ids[$i]);
					//print_r($ticketDetail);
					
					?>
      <tr bgcolor="#f3f6ea">
        <td style="border-bottom:#c2c5bb solid 1px;"><?php echo date('d M Y', strtotime(getDateById($dates[$i]))); ?></td>
        <td style="border-bottom:#c2c5bb solid 1px;"><?php echo $ticketDetail['title']; ?></td>
        <td align="center" style="border-bottom:#c2c5bb solid 1px;"><?php echo $qtys[$i]; ?></td>
        <td align="center" style="border-bottom:#c2c5bb solid 1px;">$<?php
				$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
				$buyer_service_free_after_percent		=	$ticketDetail['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
				$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
				$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');
				$price = ($finalServiceCharges+$ticketDetail['price'])*($qtys[$i]);
				echo number_format($price, 2,'.','');
				$finalPrice = $price+$finalPrice;
			?></td>
      </tr>
      <?php
					}
				}
	  	if(calculateDiscount($event_id,$_SESSION['ticketOrder']['discount_code'],$finalPrice)!=0){
		?>
      <tr bgcolor="#f3f6ea">
        <td></td>
        <td class="new_blue" style="padding-bottom:0;">Discount</td>
        <td></td>
        <td align="center" style="padding-bottom:0;"><?php echo "$".number_format(calculateDiscount($event_id,$_SESSION['ticketOrder']['discount_code'],$finalPrice), 2,'.','')?></td>
      </tr>
      <?php }
		if($service_charge!=0){
			?>
      <tr bgcolor="#f3f6ea">
        <td></td>
        <td class="new_blue" style="padding-bottom:0;">Service charge</td>
        <td></td>
        <td align="center" style="padding-bottom:0;">$<?php echo number_format($service_charge, 2,'.',''); ?></td>
      </tr>
      <?php }  ?>
      <tr bgcolor="#f3f6ea">
        <td></td>
        <td class="new_blue" style="padding-top:0;"> Total Cost </td>
        <td>&nbsp;</td>
        <td align="center" style="padding-top:0;">$<?php
		$_SESSION['orderMember']['total'] =  number_format(($finalPrice+$service_charge)-calculateDiscount($event_id,$_SESSION['ticketOrder']['discount_code'],$finalPrice), 2,'.','');
		echo $_SESSION['orderMember']['total'];
		?></td>
      </tr>
    </table>
