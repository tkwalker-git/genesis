<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_patient_id	=	$_POST["patient_id"];
$bc_clinic_id	=	$_POST["clinic_id"];
$bc_event_description	=	$_POST["event_description"];
$bc_date	=	$_POST["date"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["patient_id"] == "")
	$errors[] = "patient_id can not be empty";
if ($_POST["clinic_id"] == "")
	$errors[] = "clinic_id can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into schedule_patient (patient_id,clinic_id,event_description,) values ('" . $bc_patient_id . "','" . $bc_clinic_id . "','" . $bc_event_description . "','" . $bc_date . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update schedule_patient set patient_id = '" . $bc_patient_id . "', clinic_id = '" . $bc_clinic_id . "', event_description = '" . $bc_event_description . "',  = '" . $bc_date . "' where id=$frmID";
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
$sql	=	"select * from schedule_patient where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_patient_id	=	$row["patient_id"];
		$bc_clinic_id	=	$row["clinic_id"];
		$bc_event_description	=	$row["event_description"];
		$bc_date	=	$row[""];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" ></td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">patient_id</td>
<td align="left" class="bc_input_td">
<input type="text" name="patient_id" id="patient_id" class="bc_input" value="<?php echo $bc_patient_id; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">clinic_id</td>
<td align="left" class="bc_input_td">
<input type="text" name="clinic_id" id="clinic_id" class="bc_input" value="<?php echo $bc_clinic_id; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">event_description</td>
<td align="left" class="bc_input_td">
<textarea  name="event_description" id="event_description" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_event_description; ?></textarea>
</td>
</tr>

<tr>
<td align="right" class="bc_label">date</td>
<td align="left" class="bc_input_td">
<input type="text" name="date" id="date" class="bc_input" value="<?php echo $bc_date; ?>"/>
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