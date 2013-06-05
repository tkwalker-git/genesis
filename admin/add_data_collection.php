<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_name	=	$_POST["name"];
$bc_link	=	$_POST["link"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["link"] == "")
	$errors[] = "Link can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into data_collection_links (name,link) values ('" . $bc_name . "','" . $bc_link . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update data_collection_links set name = '" . $bc_name . "', link = '" . $bc_link . "' where id=$frmID";
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
$sql	=	"select * from data_collection_links where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_name	=	$row["name"];
		$bc_link	=	$row["link"];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Add Data Collection Links</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td width="21%" align="right" class="bc_label">Name</td>
<td width="79%" align="left" class="bc_input_td">
<input type="text" name="name" id="name" style="width:400px" class="bc_input" value="<?php echo $bc_name; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Link</td>
<td align="left" class="bc_input_td">
<input type="text" name="link" id="link" style="width:400px" class="bc_input" value="<?php echo $bc_link; ?>"/>
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