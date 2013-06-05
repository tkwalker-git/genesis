<?php

require_once("database.php"); 
require_once("header.php"); 

$frmID = 1;

$bc_meta_title		=	$_POST["meta_title"];
$bc_meta_desc		=	$_POST["meta_desc"];
$bc_meta_keywords	=	$_POST["meta_keywords"];


$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into default_settings (meta_title,meta_desc,meta_keywords) values ('" . $bc_meta_title . "','" . $bc_meta_desc . "','" . $bc_meta_keywords . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update default_settings set meta_title = '" . $bc_meta_title . "', meta_desc = '" . $bc_meta_desc . "', meta_keywords = '" . $bc_meta_keywords . "' where id=$frmID";
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
$sql	=	"select * from default_settings where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_meta_title		=	$row["meta_title"];
		$bc_meta_desc		=	$row["meta_desc"];
		$bc_meta_keywords	=	$row["meta_keywords"];
	} // end if row
	$action = "edit";
} // end if
?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr class="bc_heading">
      <td colspan="2" align="left" >Default Meta Settings</td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Meta Title:</td>
      <td align="left" class="bc_input_td"><input type="text" name="meta_title" id="meta_title" class="bc_input" style=" width:350px" value="<?php echo $bc_meta_title; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Meta Description:</td>
      <td align="left" class="bc_input_td"><textarea name="meta_desc" id="meta_desc" style="height:100px; width:350px" ><?php echo $bc_meta_desc; ?></textarea></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Meta Keywords:</td>
      <td align="left" class="bc_input_td"><textarea name="meta_keywords" id="meta_keywords" style="height:100px; width:350px" ><?php echo $bc_meta_keywords; ?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><input name="submit" type="submit" value="Save" class="bc_button" />
      </td>
    </tr>
  </table>
</form>
<?php 
require_once("footer.php"); 
?>