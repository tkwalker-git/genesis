<?php
$action1	= "";
$bc_patient_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
if($_POST["date"]=='')
	$date_add	= date('m/d/Y');
else
	$date_add	= $_POST["date"];	

$bc_add_date		=	date('m/d/Y',strtotime($date_add));
$bc_add_time		=	$_POST["time"];
$bc_reading			=	DBin($_POST["reading"]);
$bc_meal			=	$_POST["meal"];
$bc_arr_meal		=	array(1 => "Before", 2 => "After");
$eating_out			=	$_POST["eating_out"];
$exercise			=	$_POST["exercise"];
$stress				=	$_POST["stress"];
$fever				=	$_POST["fever"];
$vomiting			=	$_POST["vomiting"];
$bc_notes			=	DBin($_POST["notes"]);
if($eating_out=='1')
	$eating_out_check = 'checked="checked"';
else
	$eating_out_check = '';
//	
if($exercise=='1')
	$exercise_check = 'checked="checked"';
else
	$exercise_check = '';
//
if($stress=='1')
	$stress_check = 'checked="checked"';
else
	$stress_check = '';
//
if($fever=='1')
	$fever_check = 'checked="checked"';
else
	$fever_check = '';
//
if($vomiting=='1')
	$vomiting_check = 'checked="checked"';
else
	$vomiting_check = '';

$frmID	=	$_GET["id"];
if($_GET["id"]>0)
	$action1 = "edit";
else
	$action1 = "save";
$sucMessage = "";
if (isset($_POST["continue"]) ) {

	
	$errors = array();
	if ($_POST["date"] == "")
		$errors[] = "Date: can not be empty";
	if ($_POST["time"] == "")
		$errors[] = "Time: can not be empty";	
	if ($_POST["reading"] == "" || !is_numeric($_POST["reading"]))
		$errors[] = "Reading: can not be empty or string";
	if ($_POST["meal"] == "")
		$errors[] = "Meal: can not be empty";
	
	$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
	for ($i=0;$i<count($errors); $i++) {
		$err .= '<li>' . $errors[$i] . '</li>';
	}
	$err .= '</ul></td></tr></table>';

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into blood_gluco (patient_id,add_date, add_time,reading,meal,eating_out,exercise,stress,fever,vomiting,notes) values ('" . $bc_patient_id . "','" . date('Y-m-d',strtotime($bc_add_date)) . "','". $bc_add_time ."','" . $bc_reading . "','" . $bc_meal . "','" . $eating_out . "','" . $exercise . "','" . $stress . "','" . $fever . "','" . $vomiting . "','" . $bc_notes . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
			 ?>
             <script type="text/javascript">
			 $(function() {
             	$( "#dialog-confirm" ).dialog({
					  resizable: false,
					  height:140,
					  modal: true,
					  buttons: {
						"Add More": function() {
						  $( this ).dialog( "close" );
						 window.location.href = 'blood_gluco.php';
						},
						"I`m Done.": function() {
						  $( this ).dialog( "close" );
						  window.location.href = 'blood_gluco.php?p=log';
						}
					  }
					});
			 });
             </script>	
			<?php }else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update blood_gluco set patient_id = '" . $bc_patient_id . "', add_date = '" . date('Y-m-d',strtotime($bc_add_date)) . "', add_time = '" . $bc_add_time . "', reading = '" . $bc_reading . "', meal = '" . $bc_meal . "',eating_out = '".$eating_out."', exercise = '".$exercise."', stress ='".$stress."',fever = '".$fever."', vomiting = '".$vomiting."', notes = '" . $bc_notes . "' where id = '".$frmID."'";
			$res	=	mysql_query($sql);
			if ($res) {
			?>
           <script type="text/javascript">
			 $(function() {
             	$( "#dialog-confirm1" ).dialog({
					  resizable: false,
					  height:140,
					  modal: true,
					  buttons: {
						"Close": function() {
						  $( this ).dialog( "close" );
						  window.location.href = 'blood_gluco.php?p=log';
						}
					  }
					});
			 });
             </script>
            <?php    
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit
$sql	=	"select * from blood_gluco where id='".$frmID."'";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_patient_id		=	$row["patient_id"];
		$bc_add_date		=	$row["add_date"];
		$bc_add_time		=	$row["add_time"];
		$bc_reading			=	$row["reading"];
		$bc_meal			=	$row["meal"];
		$bc_factors			=	$row["factors"];
		$eating_out			=	$row["eating_out"];
		$exercise			=	$row["exercise"];
		$stress				=	$row["stress"];
		$fever				=	$row["fever"];
		$vomiting			=	$row["vomiting"];
		$bc_notes			=	$row["notes"];
		//factors
		if($eating_out=='1')
			$eating_out_check = 'checked="checked"';
		else
			$eating_out_check = '';
		//	
		if($exercise=='1')
			$exercise_check = 'checked="checked"';
		else
			$exercise_check = '';
		//
		if($stress=='1')
			$stress_check = 'checked="checked"';
		else
			$stress_check = '';
		//
		if($fever=='1')
			$fever_check = 'checked="checked"';
		else
			$fever_check = '';
		//
		if($vomiting=='1')
			$vomiting_check = 'checked="checked"';
		else
			$vomiting_check = '';	
	} // end if row
	$action = "edit";
} // end if 

?>
<style type="text/css">
.whiteMiddle .evField {
	
	}

.whiteMiddle .evField {
	text-align:left;
	font-size:15px;
	width:100px;
	}
.evLabal{
	font-size:15px;
	width:170px!important;
	}
	
.evInput{
	font-size:14px;
	}
.ew-heading{
	color: #49BA8D;
    font-size: 24px;}
	
.ew-heading a{
	color: #FF7A57;
    float: right;
    font-size: 14px;
	text-decoration:underline;}

.ew-heading-behind{
	color: #6EB432;
    font-size: 24px;}

.ew-heading-behind span{}

.ew-heading-a{
	color: #212121;
    font-size: 20px;}
.iconimg{
	padding-top:8px;}
.save_button{
	}
#dialog-confirm{
	display:none;
}
#dialog-confirm1{
	display:none;
}	
</style>
    
    
<div class="yellow_bar"> &nbsp; Add Blood Glucose</div>
<div id="dialog-confirm1" title="Update">
	  <span class="ui-icon ui-icon-circle-check" style="float:left; margin-left:10px;"></span>&nbsp;Your Entery has been Update.
</div>
<div id="dialog-confirm" title="Saved">
	  <span class="ui-icon ui-icon-circle-check" style="float:left; margin-left:10px;"></span>&nbsp;Your Entery has been Saved.
</div>
<div style="padding:0 10px;"><br />
<?php if(count($errors)>0){ ?>
	<div class="error"><?php echo $err; ?></div>
<?php } 
	elseif( $sucMessage != '' ) { ?>
<div class="error"><?php echo $sucMessage; ?></div>
<?php } ?>	
	<form action="" method="post" name='profrm' enctype="multipart/form-data">
	<input type="hidden" name="patient_id" id="patient_id" class="bc_input" value="<?php echo $bc_patient_id; ?>"/>
	<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
    <div class="clr"></div>
    
	<div class="editProox" style="width:280px; float:right; border:#E0ECF8 solid 1px; background-color:#F1F5F9; padding:30px 20px">
		<strong>Date:</strong> Enter the date you took your reading. Please enter it in the following format: MM/DD/YYYY.<br /><br />
		<strong>Time:</strong> Enter the time you took your reading. Please enter it in the following format: HH:MM AM/PM.<br /><br />
		<strong>Glucose:</strong> Enter the blood glucose number displayed on your meter. It's important to be as accurate as you can with this entry, so try to enter it while your meter is nearby.<br /><br />
		<strong>Meal:</strong> Check the appropriate box if your glucose level was measured right before a meal or 1-2 hours after a meal. This could help you spot patterns so you can adjust your diet, as well as help you and your health care provider decide if you need to adjust your diabetes medication.<br /><br />
		<strong>Glucose Factors:</strong> Choose any icon that has occurred before or after this blood glucose reading. This could help you and your health care provider better manage your diabetes by understanding what affects your readings.<br /><br />

**To edit a glucose reading after leaving this page, click the reading on the View Graph tab or View Log tab.
	</div>
	
	<div class="editProox" style="width:350px; float:left">
      <div class="evField">Date</div>
      <div class="evLabal">
        <input type="text" name="date" class="evInput" id="date" style="width:150px; height:20px" value='<?php echo $bc_add_date;?>' /></div>
       <div class="iconimg"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/cal3.png" id="datepickerImage"/></div>
      <div class="clr"></div>
      
      <div class="evField">Time</div>
      <div class="evLabal">
        <input type="text" name="time" class="evInput" id="time" style="width:150px; height:20px; margin-top:0px !important;" value='<?php echo $bc_add_time;?>' />
       </div> 
      <div class="iconimg"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/time3.png" id="timePickerImage"/></div>
      <div class="clr"></div>
      
      <div class="evField">Glucose</div>
      <div class="evLabal">
        <input type="text" name="reading" class="evInput" style="width:150px; height:20px; vertical-align:top;" value='<?php echo $bc_reading;?>'/></div><div class="iconimg">
        mg/dL
      </div>      
      <div class="clr"></div>
      
      <div class="evField">Meal</div>
      <div class="evLabal">
      	<table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <?php 
		
		foreach($bc_arr_meal as $key => $val)
		{
			if ($key == $bc_meal)
				$sel = 'checked="checked"';
			else
				$sel = '';	
		?>
        <td valign="top">
		<input name="meal" id="meal3" type="radio" <?php echo $sel; ?>  value="<?php echo $key; ?>" /></td><td><?php echo $val; ?>
        </td>
		<?php } ?>
		 </tr></table>
		
      </div>      
      <div class="clr"></div>
      <div class="evField">Factors</div>
      <div class="evLabal">
      	<table width="100%" cellpadding="2" cellspacing="0" border="0">
	  	 <tr>
         	<td><input type="checkbox" name="eating_out" <?php echo $eating_out_check; ?> id="eating_out"  value="1" /></td>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/eatout.png"/></td>
            <td>Eating Out</td>
         </tr>  
         <tr>
         	<td><input type="checkbox" name="exercise" id="exercise" <?php echo $exercise_check; ?> value="1" /></td>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/excercise.png"/></td>
            <td>Exercise</td>
         </tr>
         <tr>
         	<td><input type="checkbox" name="stress" id="stress" <?php echo $stress_check; ?>  value="1" /></td>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/stress.png"/></td>
            <td>Stress</td>
         </tr>
         <tr>
         	<td><input type="checkbox" name="fever" id="fever" <?php echo $fever_check; ?>  value="1" /></td>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/fever.png"/></td>
            <td>Fever</td>
         </tr>
         <tr>
         	<td><input type="checkbox" name="vomiting" id="vomiting" <?php echo $vomiting_check; ?>  value="1" /></td>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/vomit.png"/></td>
            <td>Vomiting</td>
         </tr> 
         
        </table>
	</div>
      <div class="clr"></div>
	  
	  <div class="evField">
	  	Notes
		<br>
		<textarea  name="notes" id="notes" class="evInput" style="height:100px; width:350px" /><?php echo $bc_notes; ?></textarea>
		</div>
        <div class="clr"></div>
        <div class="save_button">
		<input type="image" src="<?php echo IMAGE_PATH; ?>save_&_continue.png" align="right" name="continue" value="Save & Continue" style="padding:10px 0 0 10px;" />
		<input type="hidden" name="continue" value="Save & Continue" />
	  </div>
      <div class="clr"></div>
 	  </div>	
	
	</form>
</div>