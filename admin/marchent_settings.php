<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_secret_key	 	=	DBin($_POST["secret_key"]);
$bc_api_login	    =	DBin($_POST["api_login"]);
$bc_mode	    	=	DBin($_POST["mode"]);

$frmID = 1;

$sql_image = '';
$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$sucMessage = "";

$errors 	= array();

if($_POST["secret_key"] == "")
  $errors[] = "Secret key Can't be empty!";
if($_POST["api_login"] == "" and $_FILES["video"]["name"] == "")
  $errors[] = "API logic Can't be empty!";
if($_POST["mode"] == "" and $_FILES["video"]["name"] == "")
  $errors[] = "Please Select Mode!";

	$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
	$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {
	
	if (!count($errors)) {

		$sql	=	"update merchant_settings set secret_key = '" . $bc_secret_key . "', api_login = '" . $bc_api_login ."', mode = '" . $bc_mode . "' where id=$frmID";
		$res	=	mysql_query($sql);
		if ($res) {
			$sucMessage = "Record Successfully updated.";
			$saved_class = 'saved_class';
		} else
			$sucMessage = "Error: Please try Later";
	} else {
		$sucMessage = $err;
	}
} 

$sql	=	"select * from merchant_settings where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_id  		=	DBout($row["id"]);
		$bc_secret_key	=	DBout($row["secret_key"]);
		$bc_api_login   =	DBout($row["api_login"]);
		$bc_mode  		=	DBout($row["mode"]);
	} 
}

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">

<tr class="bc_heading">
<td colspan="2" align="left" >Marchent Settings</td>
</tr>
<tr><td colspan="2" align="center" >&nbsp;</td></tr>
<?php if ($sucMessage) { ?>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<?php } ?>
<tr>
<td width="20%" align="right" nowrap="nowrap" class="bc_label">Transaction Key:</td>
<td width="80%" align="left" class="bc_input_td">
<input type="text" name="secret_key" id="secret_key" class="bc_input" value="<?php echo $bc_secret_key; ?>"/>
</td>
</tr>
<tr>
<td width="20%" align="right" nowrap="nowrap" class="bc_label">API Login ID:</td>
<td width="80%" align="left" class="bc_input_td">
<input type="text" name="api_login" id="api_login" class="bc_input" value="<?php echo $bc_api_login; ?>"/>
</td>
</tr>
<tr>
<td width="20%" align="right" nowrap="nowrap" class="bc_label">Mode:</td>
<td width="80%" align="left" class="bc_input_td">
<input type="radio" <?php if($bc_mode == 'Live'){ ?>checked="checked" <?php }?> name="mode" id="mode" value="Live">&nbsp;Live&nbsp;<input type="radio"<?php if($bc_mode == 'Test'){ ?>checked="checked" <?php }?> name="mode" id="mode" value="Test">&nbsp;Test

</td>
</tr>



<tr>
<td nowrap="nowrap">&nbsp;</td>
<td align="left">
<input name="submit" type="submit" value="Save" class="bc_button <?php echo $saved_class;?>" />
</td>
</tr>
</table>
</form>

<?php 
require_once("footer.php"); 
?>