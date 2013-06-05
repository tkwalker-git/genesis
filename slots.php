<?php

require_once("database.php"); 
require_once("header.php"); 

$price	=	DBin($_POST["price"]);
$descr	=	DBin($_POST["descr"]);

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["price"] == "")
	$errors[] = "Price can not be empty";
	
$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
		 	
			$sql	=	"insert into slots (price,descr) values ('" . $price . "','" . $descr . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update slots set price = '" . $price . "', descr = '" . $descr . "' where id=$frmID";
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
$sql	=	"select * from slots where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$price				=	 DBout($row["price"]);
		$descr				=	 DBout($row["descr"]);
	}
	$action = "edit";
} 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >categories</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>

<tr>
<td align="right" class="bc_label">Slot ID:</td>
<td align="left" class="bc_input_td">
<?php echo $frmID; ?>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Price:</td>
<td align="left" class="bc_input_td">
<input type="text" name="price" id="price" class="bc_input" value="<?php echo $price; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Description:</td>
<td align="left" class="bc_input_td">
<textarea  name="descr" class="bc_input" style="width:550px;height:40px;" /><?php echo $descr; ?></textarea>
</td>
</tr>

<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" value="Save" class="bc_button" />
</td>
</tr>
</table>
</form>

<?php 
require_once("footer.php"); 
?>
