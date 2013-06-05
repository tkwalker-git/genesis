<?php

require_once("database.php"); 
require_once("header.php"); 

$frmID = $_GET['id'];
$sql	=	"select * from `orders` where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_user_id				= DBout($row["user_id"]);
		$bc_total_price			= DBout($row["total_price"]);
		$bc_date				= date('d M Y', strtotime($row["date"]));
		$bc_order_id			= $frmID;
		$bc_main_ticket_id		= $row['main_ticket_id'];
		$bc_net_total			= $row['net_total'];
		$bc_discount			= $row['discount'];
		} // end if row
	$action = "edit";
} // end if 

// $fname = getSingleColumn('firstname',"select * from users where id=$bc_user_id");
// $lname = getSingleColumn('lastname',"select * from users where id=$bc_user_id");
?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Ticket Order Details</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>

<tr>
	<td colspan="2" align="left"><strong><u>Order Details:</u></strong></td>
</tr>


<tr>
	<td align="right" class="bc_label">Event Name:</td>
	<td align="left" class="bc_input_td">
		<?php echo getSingleColumn("event_name","select * from `events` where `id`='$bc_main_ticket_id'"); ?>
	</td>
</tr>


<tr>
	<td align="right" class="bc_label">Added By:</td>
	<td align="left" class="bc_input_td">
		<?php echo getSingleColumn("added_by","select * from `events` where `id`='$bc_main_ticket_id'"); ?>
	</td>
</tr>
<tr>
	<td align="right" class="bc_label">Net Total:</td>
	<td align="left" class="bc_input_td">$<?php echo $bc_net_total; ?></td>
</tr>

<tr>
	<td align="right" class="bc_label">Discount:</td>
	<td align="left" class="bc_input_td">$<?php echo $bc_discount; ?></td>
</tr>

<tr>
	<td align="right" class="bc_label">Total Price:</td>
	<td align="left" class="bc_input_td">$<?php echo $bc_total_price; ?></td>
</tr>

<tr>
	<td align="right" class="bc_label">Order Date:</td>
	<td align="left" class="bc_input_td"><?php echo $bc_date; ?></td>
</tr>

<tr>
	<td colspan="2" align="left"><strong><u>Tickets Details: </u></strong></td>
</tr>

<tr>
<td colspan="2" valign="top">
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#D7C7A8" width="100%" align="center">
	<tr bgcolor="#CFBB86">
		<td width="6%" height="30" align="center"><strong>Ticket#</strong></td>
		<td width="20%" height="30" align="center"><strong>Ticket Type</strong></td>
		<td width="9%" align="center"><strong>Date</strong></td>
		<td width="7%" align="center"><strong>Time</strong></td>
		<td width="9%" align="center"><strong>Name</strong></td>
		<td width="14%" align="center"><strong>Email</strong></td>
		<td width="5%" align="center"><strong>Qty</strong></td>
		<td width="10%" align="center"><strong>Ticket Price</strong></td>
		<td width="8%" align="center"><strong>Fee</strong></td>
		<td width="12%" align="center"><strong>Price</strong></td>
	</tr>
	<?php
	$res = mysql_query("select * from `order_tickets` where `order_id`='$bc_order_id'");
	while($row = mysql_fetch_array($res)){
	?>
		<tr>
			<td width="6%" height="30" align="center"><?php echo  str_pad($row['ticket_number'],8,"0",STR_PAD_LEFT); ?></td>
			<td width="20%" height="30" align="center"><?php echo getTicketTitle($row['ticket_id']); ?></td>
			<td width="9%" align="center"><?php echo $row['date']; ?></td>
			<td width="7%" align="center"><?php echo date('h:i A', strtotime($row['t_time'])); ?></td>
			<td width="9%" align="center"><?php echo $row['name'];?></td>
			<td width="14%" align="center"><?php echo $row['email'];?></td>
			<td width="5%" align="center"><?php echo $row['quantity']; ?></td>
			<td align="center"><?php echo "$".number_format($row['price'], 2,'.',' '); ?></td>
			<td align="center"><?php echo "$".number_format($row['buyer_fee'], 2,'.',' '); ?></td>
			<td align="center"><?php echo "$".number_format($row['buyer_fee'] + $row['price'], 2,'.',' '); ?></td>
		</tr>
	<?php } ?>
</table>
</td>
</tr>



<tr>
<td colspan="2" align="left"><strong><u>Payment Info:</u></strong></td>
</tr>

<?php
$res = mysql_query("select * from `paymeny_info` where `order_id`='$bc_order_id'");
while($row = mysql_fetch_array($res)){
?>
<!--<tr>
<td width="24%" align="right" class="bc_label"> Name of Cardholder:</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['name_cardholder']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Credit Card Type:</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['card_type']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Expiration date (month/year):</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['exp_month']; ?> / <?php echo $row['exp_year']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Credit Card Number:</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['card_number']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Security Code:</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['security_code']; ?></td>
</tr>-->
<tr>
<td width="24%" align="right" class="bc_label"> Name :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['f_name']." ".$row['l_name']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Address 1 :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['address1']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Address 2 :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['address2']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> City :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['city']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Zip :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['zip']; ?></td>
</tr>
<tr>
<td width="24%" align="right" class="bc_label"> Country :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['country']; ?></td>
</tr>
<tr>
<td width="24%" align="right" class="bc_label"> Email :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['email']; ?></td>
</tr>
<tr>
<td width="24%" align="right" class="bc_label"> State :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['state']; ?></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> Phone :</td>
<td width="76%" align="left" class="bc_input_td"><?php echo $row['phone']; ?></td>
</tr>

<?php 


} ?>

<tr>
<td>&nbsp;</td><td align="left">
<!--<input name="submit" type="submit" value="Save" class="bc_button" />-->
</td>
</tr>
</table>
</form>

<?php //require_once("footer.php");?> 
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "descr",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "../style.css",
});


</script>
