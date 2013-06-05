<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_name			=	$_POST["name"];
$bc_email			=	$_POST["email"];
$bc_username		=	$_POST["username"];
$bc_password		=	$_POST["password"];
$bc_email_verify	=	($action != "edit") ? '1' : $_POST["email_verify"];
$bc_usertype		=	$_POST["usertype"];
$bc_memberdate		=	date("Y-m-d");
//$bc_facebookid		=	$_POST["facebookid"];
//$bc_imageUrl		=	$_POST["imageUrl"];
$bc_status			=	($action != "edit") ? '1' : $_POST["status"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["name"] == "")
	$errors[] = "Name: can not be empty";
if ($_POST["email"] == "")
	$errors[] = "Email: can not be empty";
if ($_POST["username"] == "")
	$errors[] = "Username: can not be empty";
if ($_POST["password"] == "")
	$errors[] = "Password: can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into users (firstname,email,username,password,email_verify,usertype,createddate,enabled) 
			values ('" . $bc_name . "','" . $bc_email . "','" . $bc_username . "','" . $bc_password . "','" . $bc_email_verify . "','" . $bc_usertype . "','" . $bc_memberdate . "','" . $bc_status . "')";
			$res	=	mysql_query($sql) or die(mysql_error());
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update users set firstname = '" . $bc_name . "', email = '" . $bc_email . "',username = '" . $bc_username . "', password = '" .  $bc_password . "', email_verify= '" . $bc_email_verify . "',usertype= '" . $bc_usertype . "', enabled = '" .  $bc_status . "' where id=$frmID";
			$res	=	mysql_query($sql);
			if ($res) {
				$sucMessage = "Record Successfully updated.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit
$sql	=	"select * from `orders` where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_user_id		 =	 DBout($row["user_id"]);
		$bc_total_price	 =	 DBout($row["total_price"]);
		$bc_date		 =	 date('d M Y', strtotime($row["date"]));
		$bc_order_id	=	$frmID;
		} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Product Order Details</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>

<tr>
<td colspan="2" align="left"><strong><u>Order Details:</u></strong></td>
</tr>

<tr>
<td width="24%" align="right" class="bc_label"> User Name:</td>
<td width="76%" align="left" class="bc_input_td"><?php echo getUserName($bc_user_id); ?></td>
</tr>

<tr>
<td align="right" class="bc_label">Total Price:</td>
<td align="left" class="bc_input_td">$<?php echo $bc_total_price; ?></td>
</tr>

<tr>
<td align="right" class="bc_label">Date:</td>
<td align="left" class="bc_input_td"><?php echo $bc_date; ?></td>
</tr>

<tr>
<td colspan="2" align="left"><strong><u>Order Deals: </u></strong></td>
</tr>

<tr>
<td colspan="2" valign="top">
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#D7C7A8" width="100%" align="center">
<tr bgcolor="#CFBB86">
<td width="60%" height="30" align="center"><strong>Deal Name</strong></td>
<td width="19%" align="center"><strong>Quantity</strong></td>
<td width="21%" align="center"><strong>Price</strong></td>
</tr>
<?php
$res = mysql_query("select * from `order_products` where `order_id`='$bc_order_id'");
while($row = mysql_fetch_array($res)){
?>
<tr>
<td width="60%" height="30" align="center"><?php echo getProductName($row['product_id']); ?></td>
<td width="19%" align="center"><?php echo $row['quantity']; ?></td>
<td width="21%" align="center">$<?php echo $row['price']; ?></td>
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
<tr>
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
</tr>

<?php } ?>

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
