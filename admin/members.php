<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_name			=	$_POST["name"];
$bc_email			=	$_POST["email"];
$bc_username		=	$_POST["username"];
$bc_password		=	$_POST["password"];
$bc_email_verify	=	$_POST["email_verify"];
$bc_usertype		=	$_POST["usertype"];
$bc_memberdate		=	date("Y-m-d");
//$bc_facebookid		=	$_POST["facebookid"];
//$bc_imageUrl		=	$_POST["imageUrl"];
$bc_status			=	$_POST["status"];

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
			$sql	=	"insert into users (firstname,email,username,password,email_verify,usertype,createddate,enables) 
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
$sql	=	"select * from users where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_name			 =	 DBout($row["firstname"]);
		$bc_email			 =	 DBout($row["email"]);
		$bc_username		 =	 DBout($row["username"]);
		$bc_password		 =	 DBout($row["password"]);
		$bc_email_verify	 =	 DBout($row["email_verify"]);
		$bc_usertype		 =	 DBout($row["usertype"]);
		$bc_memberdate		 =	 DBout(date("m-d-Y",strtotime($row["createddate"])));
		$bc_facebookid		 =	 DBout($row["facebookid"]);
		$bc_imageUrl		 =	 DBout($row["imageUrl"]);
		$bc_status			 =	 DBout($row["enabled"]);
		
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Members</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label"> Name:</td>
<td align="left" class="bc_input_td">
<input type="text" name="name" id="name" class="bc_input" value="<?php echo $bc_name; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Email:</td>
<td align="left" class="bc_input_td">
<input type="text" name="email" id="email" class="bc_input" value="<?php echo $bc_email; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Username:</td>
<td align="left" class="bc_input_td">
<input type="text" name="username" id="username" class="bc_input" value="<?php echo $bc_username; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Password:</td>
<td align="left" class="bc_input_td">
<input type="password" name="password" id="password" class="bc_input" value="<?php echo $bc_password; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">User Type:</td>
<td align="left" class="bc_input_td">
<select name="usertype" id="usertype" class="bc_input" >
<option value="">-- User Type --</option>
<?php 
$bc_arr_usertype = array("1"=>"Member", "2"=>"Promoter"); 
foreach($bc_arr_usertype as $key => $val)
{
	if ($key == $bc_usertype)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
<?php } ?>
 </select>

</td>
</tr>
<?php if($action != "save"){ ?>
<tr>
<td align="right" class="bc_label">Email Verify:</td>
<td align="left" class="bc_input_td">
<select name="email_verify" id="email_verify" class="bc_input" >
<option value="">-- Email Varify --</option>
<?php 
$bc_arr_email_verify = array("1"=>"Yes", "0"=>"No"); 
foreach($bc_arr_email_verify as $key => $val)
{
	if ($key == $bc_email_verify)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
<?php } ?>
 </select>
</td>
</tr>
<?php } ?>

<?php if($action != "save"){ ?>
<tr>
<td align="right" class="bc_label">Status:</td>
<td align="left" class="bc_input_td">
<select name="status" id="status" class="bc_input" >
<option value="">-- Status --</option>
<?php 
$bc_arr_email_verify = array("0"=>"Not Active", "1"=>"Active"); 
foreach($bc_arr_email_verify as $key => $val)
{
	if ($key == $bc_status)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
<?php } ?>
 </select>
</td>
</tr>
<?php } ?>

<?php if($action != "save" && $bc_facebookid != '0'){ ?>
<tr>
<td align="right" class="bc_label">Facebook Id:</td>
<td align="left" class="bc_input_td">
	<?php echo $bc_facebookid; ?>
</td>
</tr>
<?php } ?>

<?php
if($_GET['id']){ ?>
<tr>
<td align="right" class="bc_label"></td>
<td align="left" class="bc_input_td">
<a href="sold_report.php?type=member&id=<?php echo $frmID; ?>" style="color:#0066FF"><strong>Download Sold Tickets Report</strong></a>
</td>
</tr>

<?php
}
?>

<?php if($action != "save"){ ?>
<tr>
<td align="right" class="bc_label">Memeber Date:</td>
<td align="left" class="bc_input_td">
	<?php echo date("m/d/Y", strtotime($bc_memberdate)); ?>
</td>
</tr>
<?php } ?>



<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" value="Save" class="bc_button" />
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
