<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$event_id = $_GET['event_id'];
if ( !is_numeric($event_id) || $event_id <= 0 )
	die("Direct access to this page is not allowed.");


$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
$bc_name				=	$row['name'];
$bc_price				=	$row['price'];
$bc_ticket_description	=	$row['ticket_description'];
$bc_ticket_id			=	$row['id'];
$bc_max_order			=	$row['max_tickets_order'];
$bc_min_order			=	$row['min_tickets_order'];
}

if(isset($_REQUEST['orderTickets'])){
$errors = array();
if ( is_array($_POST['id']) ) {


		for($i=0;$i< count($_POST['id']); $i++) {
		$id		=	$_POST['id'][$i];
		$qty	=	$_POST['qty'][$i]+$qty;
		}

if($qty==0){
	$errors[] = 'Please select ticket Qty';
}
else{
if($bc_max_order!='' && $bc_max_order!=0){
if($bc_max_order < $qty){
	$errors[] = 'You can order maximum '.$bc_max_order.' tickets';
	}
}

if($bc_min_order!='' && $bc_min_order!=0){
if($bc_min_order > $qty){
	$errors[] = 'Please order minimum '.$bc_min_order.' tickets';
	}
}
}
}

if ( count( $errors) > 0 ) {
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	else{
	echo "<form method='post' name='formForBookTickets' action='".ABSOLUTE_PATH."order_tickets.php'>";
		for($i=0;$i< count($_POST['id']); $i++) {
		echo '<input type="hidden" name="id[]" value="'.$_POST['id'][$i].'">';
		echo '<input type="hidden" name="qty[]" value="'.$_POST['qty'][$i].'">';
		echo '<input type="hidden" name="event_id" value="'.$event_id.'">';
	}
	
	echo '<input type="hidden" name="ticket_id" value="'.$_POST['ticket_id'].'" />';
	echo '<input type="submit" style="visibility:hidden" name="submitForm" /></form>';
	
	?>
	<script>
	document.formForBookTickets.submit();
	return false;
	</script> 
	<?php
	}
}
require_once('includes/header.php');
?>

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
    <div class="error"><?php echo $err; ?></div>
    <!-- Market Place Top END -->
    <!-- Markeet Place InrBody Start -->
    <div class="marketInrBody">
      <?php
		 if($_REQUEST['msg']!=''){?>
      <div align="center" style="color:#FF0000"><br>
        <?php echo $_REQUEST['msg']; ?></div>
      <?php } ?>
      <div class="evField" style="width:190px">Ticket Name:</div>
      <div class="evLabal" style="padding:11px 5px;"> <?php echo $bc_name; ?> </div>
      <div class="clr"></div>
      <?php if ($bc_ticket_description){ ?>
      <div class="evField" style="width:190px">Ticket Description:</div>
      <div class="evLabal" style="padding:11px 5px;"> <?php echo $bc_ticket_description; ?> </div>
      <div class="clr"></div>
      <?php } ?>
      <form method="post">
        <table class="" width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="margin-top:14px;">
          <tr class="markPurchasedTitle">
            <td width="11%" height="35" align="center"><strong>Ticket Title</strong></td>
            <td width="10%" align="center"><strong>Price</strong></td>
            <td width="6%" align="center"><strong>Qty</strong></td>
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
            <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><?php echo ucwords($row['price']);?></td>
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
            <td colspan="2" style="border-left:#E4E4E4 solid 1px">&nbsp;</td>
            <td  valign="top" style="padding:15px 11px 0 0;border-right:#E4E4E4 solid 1px;">&nbsp;&nbsp;&nbsp;&nbsp; <img src="<?php echo IMAGE_PATH; ?>back.gif" title="back" border="0" onClick="history.go(-1)" style="cursor:pointer"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="image" src="<?php echo IMAGE_PATH; ?>order_now.gif" title="Order Now" name="orderTickets" value="Order Now" align="right" /><input type="hidden" name="orderTickets" value="Order Now" /><input type="hidden" name="ticket_id" value="<?php echo $bc_ticket_id; ?>" /></td>
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
