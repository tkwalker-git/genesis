<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$ticket_id	= $_SESSION['event_ticket_id'];
	
	$res = mysql_query("select * from `event_ticket` where `id`='$ticket_id'");
	if(mysql_num_rows($res)){
	while($row = mysql_fetch_array($res)){
	$buyer_service_free		=	$row['buyer_event_grabber_fee'];
	$prometer_service_free	=	$row['prometer_event_grabber_fee'];
	}
	?>
	<style>
	table tr td{
		text-align:center;
		border:#999 solid 1px;
	}
	</style>
	<br>
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr style="background:#999; color:#fff; height:30px">
	<td width="24%" style="text-align:left; padding-left:5px"><strong>Ticket Type</strong></td>
	<td width="19%"><strong>Initial Ticket Price</strong></td>
	<td width="19%"><strong>Promoter Fees</strong></td>
	<td width="19%"><strong>Customer Fees</strong></td>
	<td width="19%"><strong>Final Ticket Price</strong>
	</td>
	</tr>
	<?php
	$re = mysql_query("select * from `event_ticket_price` where `ticket_id`='$ticket_id'");
	if(mysql_num_rows($re)){?>
	<script language="javascript">
	$('#create_ticket').attr("src","images/created_ticket.png");
	$('#noTicket').attr('checked', false);
	$('#showCostPrice').css('visibility','hidden');
	</script>
	<?php
	$bg	=	"#efeded";
	while($ro = mysql_fetch_array($re)){
	$title	=	$ro['title'];
	$price	=	$ro['price'];
	if($bg=="#efeded"){
	$bg	=	"#fff";
	}
	else{
	$bg	=	"#efeded";
	}
	
	$buyer_service_free_after_percent		=	$price*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer_service_free/100;
		
	$prometer_service_free_after_percent	=	$price*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$prometer_service_free_after_percent	=	$prometer_service_free_after_percent*$prometer_service_free/100;
	
	?>

<tr bgcolor="<?php  echo $bg; ?>">
	<td style="text-align:left; padding:5px"><?php echo  $title; ?></td>
	<td><?php if ($price!='free' && $price!=''){echo "$".number_format($price, 2,'.','');} else{ echo "Free";} ?></td>
	<td>$ <?php echo  number_format($prometer_service_free_after_percent, 2,'.',''); ?></td>
	<td>$ <?php echo  number_format($buyer_service_free_after_percent, 2,'.',''); ?></td>
	<td>$ <?php echo  number_format($price+$buyer_service_free_after_percent, 2,'.',''); ?></td>
</tr>
<?php
}}}
?>