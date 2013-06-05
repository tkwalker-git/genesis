<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$active='buy';
	

	$event_id		= $_POST['event_id'];


		$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
		while($row = mysql_fetch_array($res)){
			$bc_ticket_id			=	$row['id'];
			$bc_name				=	$row['name'];
			$bc_price				=	$row['price'];
			$bc_ticket_id			=	$row['id'];
			$bc_max_order			=	$row['max_tickets_order'];
			$bc_min_order			=	$row['min_tickets_order'];
			$bc_service_fee_type	=	$row['service_fee_type'];
			$bc_service_fee			=	$row['service_fee'];
			$bc_quantity_available	=	$row['quantity_available'];
		}
		
		
	
?>
<?php include("../flayerMenu.php"); ?>

<script language="javascript">

var abs_url					=	'/';
var abs_url_secure			=	'/';

$(document).ready(function(){
	$('#orderTickets').click(function(){
	
	var selectedDomains = new Array();
	jQuery.each(jQuery("select[name='qty[]']"), function() {
		selectedDomains.push(jQuery(this).val());
	  });
  
  var availQty = new Array();
	jQuery.each(jQuery("input[name='availQty[]']"), function() {
		availQty.push(jQuery(this).val());
	});
	
	var titles = new Array();
	jQuery.each(jQuery("input[name='title[]']"), function() {
		titles.push(jQuery(this).val());
	});
  
  	var valid = 0;
	
	for(var i=0; i<selectedDomains.length; i++) {
		if(selectedDomains[i] > 0) {
			if(selectedDomains[i] > Number(availQty[i])){
				alert(titles[i]+' - Quantity Available '+availQty[i]+' tickets');
				return false;
			}
			valid	= 1;
		}
	}
	
	if(valid == 1){
		var ids		=	new Array();
		var qtys	=	new Array();
		var dates	=	new Array();
	
		jQuery.each(jQuery("input[name='id[]']"), function() {
					if($(this).val()!=''){
				ids.push(jQuery(this).val());
				  }
			});
	
	
		jQuery.each(jQuery("select[name='qty[]']"), function() {
					if($(this).val()!=''){
				qtys.push(jQuery(this).val());
				  }
			});
		
		
		jQuery.each(jQuery("select[name='date[]']"), function() {
					if($(this).val()!=''){
				dates.push(jQuery(this).val());
				  }
			});
		
		var event_id = $('#event_id').val();
			
		if($('#discount_code').val()!=''){
			var discount_code = $('#discount_code').val();
		}
		else{
			var discount_code ='';
		}
	
		 $.ajax({  
			type: "POST",
			url: abs_url+"ajax/loadbuy2.php",
			data: "event_id=" + event_id + "&ids=" + ids + "&qtys=" +qtys + "&dates=" +dates + "&discount_code=" +discount_code,
			dataType: "text/html",
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			$("#flayer").html(html);
			},
			complete: function()
			{
				hideOverlayer();
			}
		});
	} // end if (valid==1)
	else{
		alert('Please select ticket Qty');
		return false;
		}
	});
});
	</script>

<div id="message"></div>
<div class="inrDiv">
  <div class="progresbar1"></div>
  <br />
  <?php
		
		if (validEventTicketSaleTime($event_id)=='no'){
			echo "<div style='width:252px;margin:auto'><strong style='color:#d60808'>Error:</strong> Buy ticket time is not started or expire.</div>";
			}
	//	if($_POST['ss']){}
			else{
			$res = mysql_query("select * from `events` where `id`='$event_id'");
	  while($rows1 = mysql_fetch_array($res)){
		$source			=	$rows1['event_source'];
		$event_image	=	getEventImage($rows1['event_image'],$source);
		$event_url		=	getEventURL($rows1['id']);
		$event_name		=	$rows1['event_name'];
		$event_date		=	getEventDates($rows1['id']);
		$venue			=	getEventLocations($event_id);
		}
		$v		=	explode("<br>", $venue[0]);
		$venue	=	$v[0];
		if($v[1]!=''){
		$venue	.=	", ".$v[1];
		}
		$ticketEndSale	=	getTicketEndSale($event_id);
		$venue_attrib		= getEventLocations($event_id);
		
		$im		=	explode('>',$event_image);
		
?>
  <div class="new_flayer_title"><?php echo trim($event_name); ?></div>
  <div class="new_flayer_date"><?php echo $event_date; ?></div>
  <?php echo $venue_attrib[1]['venue_name']; ?><br />
  <br />
  <table cellpadding="10" cellspacing="0" width="100%">
    <tr bgcolor="#e4f0d8">
      <td width="36%"><strong>Ticket Type</strong></td>
      <td width="13%"><strong>PRICE</strong></td>
      <td width="16"><strong>FEE</strong></td>
      <td width="22%" align="center"><strong>DATE / TIME</strong></td>
      <td width="10%"><strong>QUANTITY</strong></td>
    </tr>
    <?php
		  $res = mysql_query("select * from `event_ticket_price` where `ticket_id`='$bc_ticket_id'");
			if(mysql_num_rows($res)){
			$s = 0;
			$bg = '#e4f0d8';
			while($row = mysql_fetch_array($res)){
			if($bg == '#e4f0d8')
				$bg = '#d1e5c0';
			else
				$bg = '#e4f0d8';
			?>
    <tr bgcolor="<?php echo $bg; ?>">
      <td>
	  	<input type="hidden" name="id[]" value="<?php echo $row['id'];?>" />
		<input type="hidden" name="title[]" value="<?php echo $row['title'];?>" />
        <strong><?php echo $row['title']; ?></strong>
		<small><?php echo substr($row['desc'],0,150); ?></small></td>
      <td><strong>$<?php echo number_format($row['price'], 2,'.',''); ?></strong></td>
      <td><strong>$<?php
			$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
			$buyer_service_free_after_percent		=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
			$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
			echo	$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');?></strong></td>
      <td align="center"><?php
			$dates	= getEventDatesInArray2($event_id);
			$dt		= $dates['date'];
			$dtIds	= $dates['id'];
			
			echo "<select name='date[]' style='width:100px'>";
			for ($i=0;$i<count($dt);$i++){
			$event_time = getEventTime($dtIds[$i]);
			if ( $event_time['start_time'] != '' ) 
		$time = date("h:i a", strtotime($event_time['start_time']));
		
		echo "<option value='".$dtIds[$i]."'>".date('M d, Y', strtotime($dt[$i]))." - ".$time ."</option>";
			}
			echo "</select>";
			 ?></td>
      <td>
	  	<input type="hidden" name="availQty[]" value="<?php echo $row['qty']-countSoldTickets($row['id']); ?>" />
	  	<select name="qty[]">
          <option value="0">0</option>
          <?php
		  for ($i=1;$i<=30;$i++){
			  echo '<option value="'.$i.'"';
		 	 if($_POST['qty'][$s]==$i){
				  echo 'selected="selected"';
			  }
			  echo '>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>';
		  } ?>
        </select>
      </td>
    </tr>
    <?php } }
	if($bg == '#e4f0d8')
		$bg = '#d1e5c0';
	else
		$bg = '#e4f0d8';
	?>
    <tr bgcolor="<?php echo $bg; ?>">
      <td colspan="5" align="right"><span class="addDiscountCode" onclick="$('#discount_code').css('display','block')">Add Discount Code</span>
        <input type="text" id="discount_code" style="display:none; width: 104px;" /></td>
    </tr>
	<?php
	if($bg == '#e4f0d8')
		$bg = '#d1e5c0';
	else
		$bg = '#e4f0d8';
		?>
    <tr bgcolor="<?php echo $bg; ?>">
      <td><strong>Secure Payments By</strong>
        <iframe src="<?php echo ABSOLUTE_PATH; ?>secure_payment.php" width="150px" style="border:0; height:92px" scrolling="no"></iframe>
        <img src="<?php echo IMAGE_PATH; ?>cc_cards.png" />
        <!--<img src="<?php echo IMAGE_PATH; ?>secure_payment_paypal.png" />--></td>
      <td colspan="4" align="right" valign="bottom"><img src="<?php echo IMAGE_PATH; ?>new_flayer_continueButton.png" id="orderTickets" style="cursor:pointer" align="right" />
        <input type="hidden" name="ticket_id" value="<?php echo $bc_ticket_id; ?>" />
        <input type="hidden" name="event_id" id="event_id" value="<?php echo $event_id; ?>" />
      </td>
    </tr>
  </table>
  <?php } ?>
  <div align="right"><br />
    <a href="<?php echo ABSOLUTE_PATH; ?>"><img src="<?php echo IMAGE_PATH; ?>powered_by.png" width="225" height="26" border="0" /></a></div>
</div>
