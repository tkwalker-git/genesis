<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='".ABSOLUTE_PATH."login.php';</script>";
$_SESSION['order_id']='';
$event_id = $_GET['event_id'];
if ( !is_numeric($event_id) || $event_id <= 0 )
	die("Direct access to this page is not allowed.");




$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
$bc_ticket_id			=	$row['id'];
$bc_name				=	$row['name'];
$bc_price				=	$row['price'];
$bc_ticket_description	=	$row['ticket_description'];
$bc_ticket_id			=	$row['id'];
$bc_max_order			=	$row['max_tickets_order'];
$bc_min_order			=	$row['min_tickets_order'];
$bc_service_fee_type	=	$row['service_fee_type'];
$bc_service_fee			=	$row['service_fee'];
$bc_quantity_available	=	$row['quantity_available'];
}



$r = mysql_query("select * from `orders` where `main_ticket_id`='$bc_ticket_id'");
while($m = mysql_fetch_array($r)){
$order_id	=	$m['id'];


$q = mysql_query("SELECT SUM(quantity) AS total FROM order_tickets where `order_id`='$order_id'");
while($m = mysql_fetch_array($q)){
$booked_tickets_quantity	=	$booked_tickets_quantity+$m['total'];
}
}
if($booked_tickets_quantity==''){
$booked_tickets_quantity=0;}

$now_quantity_available = $bc_quantity_available - $booked_tickets_quantity;

require_once('includes/header.php');
if (validEventTicketSaleTime($event_id)=='no'){
	echo "<script>window.location.href='".ABSOLUTE_PATH."myeventwall.php';</script>";
}

?>
<script language="javascript">
var bc_min_order			=	<?php echo $bc_min_order; ?>;
var bc_max_order			=	<?php echo $bc_max_order; ?>;
var booked_tickets_quantity	=	<?php echo $booked_tickets_quantity; ?>;
var now_quantity_available	=	<?php echo $now_quantity_available; ?>;

function validate(f) {
// var aBox=f['qty[]'];
var selectedDomains = new Array();
jQuery.each(jQuery("select[name='qty[]']"), function() {
    selectedDomains.push(jQuery(this).val());
  });
  
  for(var i=0; i<selectedDomains.length; i++) {
	if(selectedDomains[i] > 0) {
	$(".error").html('');
	check(f);
	return false;
}
}
  $(".error").html('<ul><li>Please select ticket Qty</li></ul>');
return false;
}

function check(f){
//var aBox=f['qty[]'];

var count = 0;

for(var i=0; i<selectedDomains.length; i++) {
if(selectedDomains[i]!=0) {
count = Number(selectedDomains[i])+Number(count);
} 
    }
//	alert(count);

if(bc_min_order!='' && bc_min_order!=0){
if(bc_min_order > count){
$(".error").html('<ul><li>Please order minimum '+now_quantity_available+' tickets</li></ul>');
return false;
	}
}


if(bc_max_order!='' && bc_max_order!=0){
if(bc_max_order < count){
$(".error").html('<ul><li>Please order maximum '+bc_max_order+' tickets</li></ul>');
return false;
	}
}

if(now_quantity_available < count){
$(".error").html('<ul><li>Quantity Available '+now_quantity_available+' tickets</li></ul>');
return false;
	}




document.ticketForm.submit();
}
</script>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	
	function submitform(){	
			
	document.forms["searchfrmdate"].submit();

	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <!-- Market Place Top Start -->
    <div class="marketPlaceTop">
      <div class="markeetPlace_title">Ticket Information</div>
      <div class="clear"></div>
    </div>
    <div class="error"></div>
    <!-- Market Place Top END -->
    <!-- Markeet Place InrBody Start -->
    <div class="marketInrBody">
      <?php
		 if($_REQUEST['msg']!=''){?>
      <div align="center" style="color:#FF0000"><br>
        <?php echo $_REQUEST['msg']; ?></div>
      <?php } ?>
      <?php
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
		
		$im		=	explode('>',$event_image);
		echo $img	=	$im[0]." style='margin:0 15px 10px 0; padding:3px; border:#ccc solid 1px'>";
		echo "<span class='event-name-big' style='float:none'><b>".trim(ucwords($event_name))."</b></span><br /><br />";
		echo "<b>Presented By : &nbsp;</b> ".ucwords(getMemberFirstAndLastName($user_id))."<br /><br />";
		echo "<b>Venue : &nbsp;</b> ".str_replace("<br>", " &nbsp;", $venue)."<br /><br />";
		echo "<b>Date : &nbsp;</b> ".$event_date."<br /><br />";
		echo "<b>Quantity Available : &nbsp;</b>".$now_quantity_available." Tickets<br><br>";
		if ($bc_service_fee_type==1 || $bc_service_fee_type==3){
		echo "<b>Service Fee : </b> $".$bc_service_fee."<br><br>";
		if ($bc_ticket_description){
		echo "<b>Ticket Summary : </b><br> &nbsp; &nbsp;  &nbsp; &nbsp; ".$bc_ticket_description;
		}

}
		echo "<br><br><div class='clr'></div>*Online pre-sales for this event end ".date ('D, M d, Y', strtotime($ticketEndSale[0]))." ".date('h:i A', strtotime($ticketEndSale[1]))."<br>";
	
	//	echo "<br><br><br>".$bc_name;
		?>
      <div class="clr"></div>
      <form method="post" onsubmit="return validate(this);" name="ticketForm" action="<?php echo ABSOLUTE_PATH; ?>order_tickets.php">
        <table class="" width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="margin-top:14px;">
          <tr class="markPurchasedTitle">
            <td width="39%" height="35" align="center"><strong>Ticket Title</strong></td>
            <td width="14%" align="center"><strong>Price</strong></td>
            <?php
			if ($bc_service_fee_type==2){?>
            <td width="17%" align="center"><strong>Service Fee</strong></td>
            <?php } ?>
            <td width="13%" align="center"><strong>Date</strong></td>
            <td width="17%" align="center"><strong>Qty</strong></td>
          </tr>
          <?php
	/*	echo "select * from `event_ticket_price` where `ticket_id`='$bc_ticket_id'"; */
	$res = mysql_query("select * from `event_ticket_price` where `ticket_id`='$bc_ticket_id'");
	if(mysql_num_rows($res)){
	$s = 0;
	while($row = mysql_fetch_array($res)){
	
	?>
          <tr>
            <td align="center" style="border:#E4E4E4 solid 1px; padding:5px; border-top:none"><input type="hidden" name="id[]" value="<?php echo $row['id'];?>" />
              <?php echo $row['title']; ?></td>
            <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><strong><?php if (is_numeric($row['price'])){ echo "$";}  echo ucwords($row['price']);?></strong><br />
			<small>+ $<?php
			$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
			
				$buyer_service_free_after_percent		=	$row['price']*5.50/100+.99;
				$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
				
			
			echo	$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');?> Service Charge</small></td>
            <?php
			if ($bc_service_fee_type==2){?>
            <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><?php echo "$".$bc_service_fee; ?></td>
            <?php } ?>
            <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><?php
			$dt	= getEventDatesInArray($event_id);
			echo "<select name='date[]'>";
			for ($i=0;$i<count($dt);$i++){
			echo "<option value='".date('d M Y', strtotime($dt[$i]))."'>".date('d M Y', strtotime($dt[$i]))."</option>";
			}
			echo "</select>";
			 ?></td>
            <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><select name="qty[]">
                <option value="0">0</option>
                <?php
		  for ($i=1;$i<=30;$i++){
		  echo '<option value="'.$i.'"';
		  if($_POST['qty'][$s]==$i){
		  echo 'selected="selected"';
		  }
		  echo '>'.$i.'&nbsp;</option>';
		  } ?>
              </select></td>
          </tr>
          <?php
	$s++;
	 } ?>
          <tr>
            <td style="border-left:#E4E4E4 solid 1px" colspan="2">&nbsp;</td>
            <td  valign="top" colspan="3"  style="padding:15px 11px 0 0;border-right:#E4E4E4 solid 1px;">&nbsp;&nbsp;&nbsp;&nbsp; <img src="<?php echo IMAGE_PATH; ?>back.gif" title="back" border="0" onClick="history.go(-1)" style="cursor:pointer"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="image" src="<?php echo IMAGE_PATH; ?>order_now.gif" title="Order Now" name="orderTickets" value="Order Now" align="right" />
              <input type="hidden" name="orderTickets" value="Order Now" />
              <input type="hidden" name="ticket_id" value="<?php echo $bc_ticket_id; ?>" />
              <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" /></td>
          </tr>
          <?php } 
	
	else{
	echo '<tr><td colspan="7" align="center" style="padding:20px;border-left:#E4E4E4 solid 1px; border-right:#E4E4E4 solid 1px; color:#FF0000">Your Cart is Empty</td></tr>';
	}?>
        </table>
      </form>
      <div class="markPurchasedBottom">&nbsp;</div>
    </div>
    <!-- Markeet Place InrBody End -->
  </div>
</div>
<?php require_once('includes/footer.php');?>