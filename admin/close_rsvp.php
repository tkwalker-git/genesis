<?php

require_once("database.php");
require_once("header.php");

$bc_note	=	DBin($_POST["note"]);
$bc_rsvp	=	$_POST["close"];


$frmID	=	$_GET["id"];

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
	

		 $alrdyId	= getSingleColumn("id","select * from `event_rsvp_close` where `event_id`='$frmID'");
		if($alrdyId){
			$res = mysql_query("UPDATE `event_rsvp_close` SET  `note` =  '$bc_note', `close` =  '$bc_rsvp' WHERE `event_id` = $frmID");
		}
		else{
			$res2 = mysql_query("INSERT INTO `event_rsvp_close` (`id`, `note`, `event_id`, `close`) VALUES (NULL, '$bc_note', '$frmID', '$bc_rsvp')");
			$frmID = mysql_insert_id();
		}
		 
		 	if ($res) {
				$sucMessage = "Record Successfully updated.";
			}
			elseif ($res2) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		
		

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit
$sql	=	"select * from event_rsvp_close where event_id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_note	=	$row["note"];
		$bc_rsvp	=	$row["close"];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Close RSVP</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>

<tr>
<td align="right" class="bc_label">Event Name:</td>
<td align="left" class="bc_input_td"> <?php echo getSingleColumn("event_name","select * from `events` where `id`='$frmID'"); ?>
</td>
</tr>


<tr>
<td align="right" class="bc_label">Closing Note:</td>
<td align="left" class="bc_input_td">
<textarea  name="note" id="note" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_note; ?></textarea>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Close RSVP:</td>
<td align="left" class="bc_input_td">

<input type="checkbox" name="close" id="close" <?php if ($bc_rsvp=='1'){ echo 'checked="checked"'; }?>  value="1" /><br>

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
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script>
	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "note",
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