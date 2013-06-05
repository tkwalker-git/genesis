<style>
	#tot{
		border-bottom:#cccccc solid 1px;
		border-top:#cccccc solid 1px;
	}
	#tot td{
		padding:15px 0;
	}
</style>
<?php
	$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
?>
<div class="yellow_bar">
  <table cellpadding="0"  cellspacing="0" width="99%" align="center">
    <tr>
      <td width="28%">EVENT NAME</td>
      <td width="18%">TICKET TYPE</td>
      <td width="12%" align="center">SOLD</td>
      <td width="15%" align="center">GROSS SALE</td>
      <td width="11%" align="center">FEES</td>
      <td width="16%" align="center">NET PROFIT</td>
    </tr>
  </table>
</div>
<!-- /yellow_bar -->
<?php

	$sql = "select o.main_ticket_id, ot.price, ot.product_name, o.id, ot.ticket_id from orders o, order_tickets ot where o.total_price!='' && ot.order_id=o.id && o.type='ticket' && o.promoter_id='$member_id' GROUP BY ot.ticket_id";

	$res = mysql_query($sql);
	$i=0;
	$bg = "ffffff";
	if(mysql_num_rows($res)){
		while($row = mysql_fetch_array($res)){
			
			
			if($bg=='ffffff')
				$bg='f6f6f6';
			else
				$bg = "ffffff";
			
			$order_id		= $row['id'];
			$event_id		= $row['main_ticket_id'];
			$product_name	= $row['product_name'];
			$ticket_id		= $row['ticket_id'];
			$price			= $row['price'];
			
			$event_url		= getEventURL($event_id);
			
			$sold		= getSingleColumn("tot","select count(*) as tot from order_tickets ot, orders o where ot.ticket_id='$ticket_id' && o.id=ot.order_id && o.total_price!=''");
			$totalSold 	= $sold + $totalSold;
			
			$total_fee			= getSingleColumn('total_fee',"select SUM(total_fee) as total_fee from order_tickets where `ticket_id`='".$ticket_id."'");
			$buyer_fee			= getSingleColumn('buyer_fee',"select SUM(buyer_fee) as buyer_fee from order_tickets where `ticket_id`='".$ticket_id."'");
			
			$fees				= number_format($total_fee - $buyer_fee,2);
			$totFees			= $fees + $totFees;
			
			$newGrs			= $row['price'] * $sold;
			
			$grossSale			= getTicketGross($ticket_id,'ticket');
			$totalGrossSale		= $newGrs + $totalGrossSale;
			$netProfit			= $grossSale - $fees;
			$totalNetProfit		= $netProfit + $totalNetProfit;
			
			?>
		<div class="ev_eventBox" style="border:0">
			<table cellpadding="0" cellspacing="0" width="99%" align="center">
			<tr>
				<td width="28%" valign="top" class="event_name"><a href="<?php echo $event_url; ?>"><?php echo getSingleColumn("event_name","select * from `events` where `id`='$event_id'"); ?></a></td>
				<td width="18%" valign="top"><?php echo $product_name; ?></td>
				<td width="12%" valign="top" align="center"><?php echo $sold; ?></td>
				<td width="15%" valign="top" align="center"><?php echo '$'.number_format($newGrs,2); ?></td>
				<td width="11%" valign="top" align="center"><?php echo '$'.number_format($fees,2); ?></td>
				<td width="16%" valign="top" align="center"><?php echo '$'.number_format($netProfit,2); ?></td>
			</tr>
			</table>
		</div>
<?php } ?>
<table cellpadding="0" cellspacing="0" width="100%" id="tot" align="center" bgcolor="#fff">
  <tr>
    <td width="46%">&nbsp; &nbsp; <strong>Totals:</strong></td>
    <td width="12%" align="center"><?php echo $totalSold; ?></td>
    <td width="15%" align="center"><?php echo '$'.number_format($totalGrossSale,2); ?></td>
    <td width="11%" align="center"><?php echo '$'.number_format($totFees,2); ?></td>
    <td width="16%" align="center"><?php echo '$'.number_format($totalNetProfit,2); ?></td>
  </tr>
</table>
<?php
		}
		else{
			echo "<div style='padding:40px; text-align:center;color:red'><h2>No Record Found</h2></div>";
		}?>
<br />
<div align="right" style="padding-right:20px;"><br />
	<a href="load_xls2.php?type=user" style="color:#0066FF"><strong>Export</strong></a>
	
</div>