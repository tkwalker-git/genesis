<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_c_type	=	$_POST["c_type"];
$bc_comment	=	$_POST["comment"];
$bc_rating	=	$_POST["rating"];
$bc_by_user	=	$_POST["by_user"];
$bc_date_posted	=	$_POST["date_posted"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["c_type"] == "Plese Select")
	$errors[] = "Please Select Comment Type";
if ($_POST["comment"] == "")
	$errors[] = "Comment: can not be empty";
if ($_POST["rating"] == "Plese Select")
	$errors[] = "Please Select Rating";	

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into comment (c_type,comment,rating,by_user,date_posted) values ('" . $bc_c_type . "','" . $bc_comment . "','" . $bc_rating . "','" . $bc_by_user . "','" . $bc_date_posted . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update comment set c_type = '" . $bc_c_type . "', comment = '" . $bc_comment . "', rating = '" . $bc_rating . "', by_user = '" . $bc_by_user . "', date_posted = '" . $bc_date_posted . "' where id=$frmID";
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
$sql	=	"select * from comment where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_c_type	=	$row["c_type"];
		$bc_comment	=	$row["comment"];
		$bc_rating	=	$row["rating"];
		$bc_by_user	=	$row["by_user"];
		$bc_date_posted	=	$row["date_posted"];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Comments</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Comment Type:</td>
<td align="left" class="bc_input_td">
<select name="c_type" id="c_type" class="bc_input" >

<?php
$bc_arr_c_type = array("Event"=>"Event", "Person"=>"Person", "Category"=>"Category","Sub category"=>"Sub category","Music"=>"Music");  
foreach($bc_arr_c_type as $key => $val)
{
	if ($key == $bc_c_type)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
<?php } ?>
 </select>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Comment:</td>
<td align="left" class="bc_input_td">
<textarea  name="comment" class="bc_input" style="width:550px;height:40px;" /><?php echo $bc_comment; ?></textarea>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Rating:</td>
<td align="left" class="bc_input_td">
<select name="rating" id="rating" class="bc_input" >
<?php 
$bc_arr_rating = array("Good"=>"Good", "Excellent"=>"Excellent", "Fair"=>"Fair","Poor"=>"Poor"); 
foreach($bc_arr_rating as $key => $val)
{
	if ($key == $bc_rating)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
<?php } ?>
 </select>
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
//require_once("footer.php"); 
?>
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">

	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "comment",
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
