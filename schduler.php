<?php

require_once("admin/database.php"); 
include("includes/header.php"); 

$bc_appt_type		=	$_POST["appt_type"];
$bc_appt			=	$_POST["appt"];
$bc_patient_name	=	$_POST["patient_name"];
$bc_date			=	date('Y-m-d', strtotime(str_replace("/","-",$_POST["date"])));
$bc_time			=	date('H:i',strtotime($_POST["time"]));
$bc_duration		=	$_POST["duration"];
$bc_resource_name	=	$_POST["resource_name"];
$bc_comment			=	$_POST["comment"];
$bc_new_patient		=	$_POST['new_patient'];
$bc_repeat			=	$_POST["repeat"];
$bc_days			=	$_POST["days"];
$bc_till			=	date('Y-m-d', strtotime(str_replace("/","-",$_POST["till"])));

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["appt"] == "")
	$errors[] = "Appt can not be empty";
if ($_POST["patient_name"] == "")
	$errors[] = "Patient Name can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
		echo	$sql	=	"insert into schduler (`appt_type`,`appt`,`patient_name`,`date`,`time`,`duration`,`resource_name`,`comment`,`new_patient`,`repeat`,`days`,`till`) values ('" . $bc_appt_type . "','" . $bc_appt . "','" . $bc_patient_name . "','" . $bc_date . "','" . $bc_time . "','" . $bc_duration . "','" . $bc_resource_name . "','" . $bc_comment . "','" . $bc_new_patient ."','". $bc_repeat . "','" . $bc_days . "','" . $bc_till . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update schduler set `appt_type` = '" . $bc_appt_type . "', `appt` = '" . $bc_appt . "', `patient_name` = '" . $bc_patient_name . "', `date` = '" . $bc_date . "', `time` = '" . $bc_time . "', `duration` = '" . $bc_duration . "', `resource_name` = '" . $bc_resource_name . "', `comment` = '" . $bc_comment . "', `new_patient` = '". $bc_new_patient ."', `repeat` = '" . $bc_repeat . "', `days` = '" . $bc_days . "', `till` = '" . $bc_till . "' where id=$frmID";
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

	$sql	=	"select * from schduler where id=$frmID";
	$res	=	mysql_query($sql);
	if ($res) {
		if ($row = mysql_fetch_assoc($res) ) {
			$bc_appt_type		=	$row["appt_type"];
			$bc_appt			=	$row["appt"];
			$bc_patient_name	=	$row["patient_name"];
			$bc_date			=	$row["date"];
			$bc_duration		=	$row["duration"];
			$bc_resource_name	=	$row["resource_name"];
			$bc_comment			=	$row["comment"];
			$bc_new_patient		=	$row['new_patient'];
			$bc_repeat			=	$row["repeat"];
			$bc_days			=	$row["days"];
			$bc_till			=	$row["till"];
		} // end if row
		$action = "edit";
	} // end if
?>
<style>
	.new_input{
			background:#FFF;
	}
</style>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="35%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" ></td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Appt Type</td>
<td align="left" class="bc_input_td">
<label for="patient"><input name="appt_type" id="patient" type="radio" <?php if ($bc_appt_type == 1){echo 'checked="checked"';} ?> value="1" /> Patient</label> &nbsp; 
<label for="other_appt"><input name="appt_type" id="other_appt" type="radio" <?php if ($bc_appt_type == 0){echo 'checked="checked"';} ?> value="0" /> Other Appt</label>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Appt</td>
<td align="left" class="bc_input_td">
<select name="appt" id="appt" class="new_input" >
	<?php
		for($i='A';$i<='F';$i++){?>
        	<option value="<?php echo $i; ?>" <?php if ($bc_appt == $i){ echo 'selected="selected"'; } ?>><?php echo $i; ?></option>
    <?php } ?>
</select>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Patient Name</td>
<td align="left" class="bc_input_td">
<input type="text" name="patient_name" id="patient_name" class="new_input" value="<?php echo $bc_patient_name; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Date / Time</td>
<td align="left" class="bc_input_td">
<input type="text" name="date" id="date2" style="width:82px; text-align:left" class="new_input" value="<?php echo date('d/m/Y', strtotime($bc_date)); ?>"/>
&nbsp; &nbsp; 
<select name="time" id="" class="new_input">
	<option <?php if (date('h:i A',strtotime($bc_time)) == '01:00 AM'){ echo 'selected="selected"';} ?> >01:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '02:00 AM'){ echo 'selected="selected"';} ?> >02:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '03:00 AM'){ echo 'selected="selected"';} ?> >03:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '04:00 AM'){ echo 'selected="selected"';} ?> >04:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '05:00 AM'){ echo 'selected="selected"';} ?> >05:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '06:00 AM'){ echo 'selected="selected"';} ?> >06:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '07:00 AM'){ echo 'selected="selected"';} ?> >07:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '08:00 AM'){ echo 'selected="selected"';} ?> >08:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '09:00 AM'){ echo 'selected="selected"';} ?> >09:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '10:00 AM'){ echo 'selected="selected"';} ?> >10:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '11:00 AM'){ echo 'selected="selected"';} ?> >11:00 AM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '12:00 PM'){ echo 'selected="selected"';} ?> >12:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '01:00 PM'){ echo 'selected="selected"';} ?> >01:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '02:00 PM'){ echo 'selected="selected"';} ?> >02:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '03:00 PM'){ echo 'selected="selected"';} ?> >03:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '04:00 PM'){ echo 'selected="selected"';} ?> >04:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '05:00 PM'){ echo 'selected="selected"';} ?> >05:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '06:00 PM'){ echo 'selected="selected"';} ?> >06:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '07:00 PM'){ echo 'selected="selected"';} ?> >07:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '08:00 PM'){ echo 'selected="selected"';} ?> >08:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '09:00 PM'){ echo 'selected="selected"';} ?> >09:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '10:00 PM'){ echo 'selected="selected"';} ?> >10:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '11:00 PM'){ echo 'selected="selected"';} ?> >11:00 PM</option>
    <option <?php if (date('h:i A',strtotime($bc_time)) == '12:00 AM'){ echo 'selected="selected"';} ?> >12:00 AM</option>
</select>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Duration (mins)</td>
<td align="left" class="bc_input_td">
<select name="duration" class="new_input">
	<?php
		for($i=1;$i<=60;$i++){?>
        	<option value="<?php echo $i; ?>" <?php if ($bc_duration == $i){ echo 'selected="selected"'; } ?>><?php echo $i; ?></option>
    <?php } ?>
</select>

</td>
</tr>

<tr>
<td align="right" class="bc_label" valign="top">Resource Name</td>
<td align="left" class="bc_input_td">

<select name="resource_name" id="resource_name" class="new_input">
	<?php
		for($i='A';$i<='F';$i++){?>
        	<option value="<?php echo $i; ?>" <?php if ($bc_resource_name == $i){ echo 'selected="selected"'; } ?>><?php echo $i; ?></option>
    <?php } ?>
</select>
<br />
<label><input type="checkbox" name="new_patient" id="new_patient" <?php if ($bc_new_patient == 1){ echo 'checked="checked"'; } ?>  value="1" /> New Patient</label>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Comment</td>
<td align="left" class="bc_input_td">
<textarea  name="comment" id="comment" class="new_input" style="width:300px;height:80px;" /><?php echo $bc_comment; ?></textarea>
</td>
</tr>
<tr>
	<td colspan="2">

<fieldset style="color:#000; font-size:13px" class="new_input">
    <legend>Repeat</legend>
<table width="100%">
    <tr>
    <td width="22%" align="right" class="bc_label">Repeat</td>
    <td width="78%" align="left" class="bc_input_td">
    <input type="checkbox" value="1" class="" name="repeat" <?php if ($bc_repeat == 1){ echo 'checked="checked""';} ?> />
    </td>
    </tr>
    
    <tr>
    <td align="right" class="bc_label">Days</td>
    <td align="left" class="bc_input_td">
    <select id="days" name="days" class="new_input">
        <?php
            for($i=1;$i<=20;$i++){?>
                <option value="<?php echo $i; ?>" <?php if ($bc_days == $i){ echo 'selected="selected"'; } ?>><?php echo $i; ?></option>
        <?php } ?>
    </select>
    </td>
    </tr>
    
    <tr>
    <td align="right" class="bc_label">Till</td>
    <td align="left" class="bc_input_td">
    <input type="text" name="till" style="width:82px;" id="till" class="new_input" value="<?php echo date('d/m/Y', strtotime($bc_till)); ?>"/>
    </td>
    </tr>
</table>
</fieldset>
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
	include("includes/footer.php"); 
?>
<script type="text/javascript" src="js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script>
	$(function() {
		$( "#till,#date2" ).datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: true,
			changeYear: true,
			yearRange: '1940:2011'
			});
	});
</script>