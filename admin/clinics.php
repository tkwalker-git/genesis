<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_clinicname	=	$_POST["clinicname"];
$bc_address1	=	$_POST["address1"];
$bc_address2	=	$_POST["address2"];
$bc_city		=	$_POST["city"];
$bc_state		=	$_POST["state"];
$bc_zip			=	$_POST["zip"];
$bc_phone1		=	$_POST["phone1"];
$bc_phone2		=	$_POST["phone2"];
$bc_fax1		=	$_POST["fax1"];
$bc_fax2		=	$_POST["fax2"];
$bc_website		=	$_POST["website"];
$bc_			=	$_POST["bio_nutritional_cafe_details"];
$bc_other_info	=	$_POST["other_info"];

$bc_arr_state	=	array();
$arrRES = mysql_query("select id as id, state as value from usstates");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$bc_arr_state[$bc_row["id"]] = $bc_row["value"];
	
$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["clinicname"] == "")
	$errors[] = "Clinic Name can not be empty";
if ($_POST["address1"] == "")
	$errors[] = "Address1 can not be empty";
if ($_POST["city"] == "")
	$errors[] = "City can not be empty";
if ($_POST["state"] == "")
	$errors[] = "State can not be empty";
if ($_POST["zip"] == "")
	$errors[] = "Zip can not be empty";
if ($_POST["phone1"] == "")
	$errors[] = "Phone1 can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into clinic (clinicname,address1,address2,city,state,zip,phone1,phone2,fax1,fax2,website,bio_nutritional_cafe_details,other_info) values ('" . $bc_clinicname . "','" . $bc_address1 . "','" . $bc_address2 . "','" . $bc_city . "','" . $bc_state . "','" . $bc_zip . "','" . $bc_phone1 . "','" . $bc_phone2 . "','" . $bc_fax1 . "','" . $bc_fax2 . "','" . $bc_website . "','" . $bc_ . "','" . $bc_other_info . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update clinic set clinicname = '" . $bc_clinicname . "', address1 = '" . $bc_address1 . "', address2 = '" . $bc_address2 . "', city = '" . $bc_city . "', state = '" . $bc_state . "', zip = '" . $bc_zip . "', phone1 = '" . $bc_phone1 . "', phone2 = '" . $bc_phone2 . "', fax1 = '" . $bc_fax1 . "', fax2 = '" . $bc_fax2 . "', website = '" . $bc_website . "', bio_nutritional_cafe_details = '" . $bc_ . "', other_info = '" . $bc_other_info . "' where id=$frmID";
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
$sql	=	"select * from clinic where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_clinicname	=	$row["clinicname"];
		$bc_address1	=	$row["address1"];
		$bc_address2	=	$row["address2"];
		$bc_city	=	$row["city"];
		$bc_state	=	$row["state"];
		$bc_zip	=	$row["zip"];
		$bc_phone1	=	$row["phone1"];
		$bc_phone2	=	$row["phone2"];
		$bc_fax1	=	$row["fax1"];
		$bc_fax2	=	$row["fax2"];
		$bc_website	=	$row["website"];
		$bc_	=	$row["bio_nutritional_cafe_details"];
		$bc_other_info	=	$row["other_info"];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Clinics</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Clinic Name</td>
<td align="left" class="bc_input_td">
<input type="text" name="clinicname" id="clinicname" class="bc_input" value="<?php echo $bc_clinicname; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Address1</td>
<td align="left" class="bc_input_td">
<input type="text" name="address1" id="address1" class="bc_input" value="<?php echo $bc_address1; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Address2</td>
<td align="left" class="bc_input_td">
<input type="text" name="address2" id="address2" class="bc_input" value="<?php echo $bc_address2; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">City</td>
<td align="left" class="bc_input_td">
<input type="text" name="city" id="city" class="bc_input" value="<?php echo $bc_city; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">State</td>
<td align="left" class="bc_input_td">
<select name="state" id="state" class="bc_input" >
<?php 
foreach($bc_arr_state as $key => $val)
{
	if ($key == $bc_state)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo ucwords(strtolower($val)); ?> </option>
<?php } ?>
 </select>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Zip</td>
<td align="left" class="bc_input_td">
<input type="text" name="zip" id="zip" class="bc_input" value="<?php echo $bc_zip; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Phone1</td>
<td align="left" class="bc_input_td">
<input type="text" name="phone1" id="phone1" class="bc_input" value="<?php echo $bc_phone1; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Phone2</td>
<td align="left" class="bc_input_td">
<input type="text" name="phone2" id="phone2" class="bc_input" value="<?php echo $bc_phone2; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Fax1</td>
<td align="left" class="bc_input_td">
<input type="text" name="fax1" id="fax1" class="bc_input" value="<?php echo $bc_fax1; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Fax2</td>
<td align="left" class="bc_input_td">
<input type="text" name="fax2" id="fax2" class="bc_input" value="<?php echo $bc_fax2; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Website</td>
<td align="left" class="bc_input_td">
<input type="text" name="website" id="website" class="bc_input" value="<?php echo $bc_website; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Bio_nutritional_cafe_details</td>
<td align="left" class="bc_input_td">
<textarea  name="bio_nutritional_cafe_details" id="bio_nutritional_cafe_details" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_; ?></textarea>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Other_info</td>
<td align="left" class="bc_input_td">
<textarea  name="other_info" id="other_info" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_other_info; ?></textarea>
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