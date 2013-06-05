<?php

$bc_title	=	$_POST["title"];
$bc_video	=	$_POST["video"];

$frmID	=	$_GET["id"];

if($_GET["id"]){
if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'learning_library',$_GET['id']) =='false'){
echo "<script>window.location.href='clinic_manager.php?p=videos';</script>";
}
}

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["title"] == "")
	$errors[] = "Video Title can not be empty";
if ($_POST["video"] == "")
	$errors[] = "Video Embed Code can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into learning_library (title,video,clinic_id) values ('" . $bc_title . "','" . $bc_video . "','".  $_SESSION['LOGGEDIN_MEMBER_ID'] ."')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update learning_library set title = '" . $bc_title . "', video = '" . $bc_video . "',clinic_id='".  $_SESSION['LOGGEDIN_MEMBER_ID'] ."' where id=$frmID";
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
$sql	=	"select * from learning_library where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_title	=	$row["title"];
		$bc_video	=	$row["video"];
	} // end if row
	$action = "edit";
} // end if 

?>
<div class="yellow_bar">
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
	<tr class="bc_heading">
		<td colspan="2" align="left" >Learning Library</td>
 	</tr>
 </table>
</div>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">

<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Video Title</td>
<td align="left" class="bc_input_td">
<input type="text" name="title" id="title" class="new_input" style="width:300px" value="<?php echo $bc_title; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Video Embed Code</td>
<td align="left" class="bc_input_td">
<textarea  name="video" id="video" class="new_input" style="width:400px;height:100px;" /><?php echo $bc_video; ?></textarea>
</td>
</tr>

<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" value="Save" class="bc_button" />
</td>
</tr>
</table>
</form>