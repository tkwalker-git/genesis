<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_categoryid			=	DBin($_POST["categoryid"]);
$bc_name				=	DBin($_POST["name"]);
$seo_name				=	DBin($_POST["name"]);
$bc_descr				=	DBin($_POST["descr"]);
$sort_order				=	DBin($_POST["sort_order"]);
$bc_meta_title			=	DBin($_POST["meta_title"]);
$bc_meta_keywords		=	DBin($_POST["meta_keywords"]);
$bc_meta_description	=	DBin($_POST["meta_description"]);
$bc_tags				=	DBin($_POST["tags"]);


$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["categoryid"] == "")
	$errors[] = "Categoryid: can not be empty";
if ($_POST["name"] == "")
	$errors[] = "Name: can not be empty";


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
		 	
			$bc_seo_name = make_seo_names($seo_name,"categories","seo_name","");
			
			$so 	= getSingleColumn('so',"select max(sort_order) as so from sub_categories ");
			$so 	= ((int)$so) + 1 ;
			
			$sql	=	"insert into sub_categories (categoryid,name,seo_name,descr,sort_order,meta_title,meta_keywords,meta_description,tag) values ('" . $bc_categoryid . "','" . $bc_name . "','" . $bc_seo_name . "','" . $bc_descr . "','" . $so . "','" . $bc_meta_title . "','" . $bc_meta_keywords . "','" . $bc_meta_description . "','". $bc_tags ."')";
			$res	=	mysql_query($sql);
			$frmID 	= 	mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update sub_categories set categoryid = '" . $bc_categoryid . "', name = '" . $bc_name . "', descr = '" . $bc_descr . "', meta_title = '" . $bc_meta_title . "', meta_keywords = '" . $bc_meta_keywords . "', meta_description = '" . $bc_meta_description . "',tag='". $bc_tags ."' where id=$frmID";
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
$sql	=	"select * from sub_categories where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_categoryid			=	DBout($row["categoryid"]);
		$bc_name				=	DBout($row["name"]);
		$bc_seo_name			=	DBout($row["seo_name"]);
		$bc_descr				=	DBout($row["descr"]);
		$bc_meta_title			=	DBout($row["meta_title"]);
		$bc_meta_keywords		=	DBout($row["meta_keywords"]);
		$bc_meta_description	=	DBout($row["meta_description"]);
		$bc_tags				=	DBout($row["tag"]);
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Sub categories</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Perent Category  </td>
<td align="left" class="bc_input_td">
<?php
		
if($action=='edit')
{
	$bc_categoryid = $bc_categoryid;
	select_sname("categoryid","",$bc_categoryid,150);
}else {
	$bc_categoryid =NULL;
    select_sname("categoryid");
}
		
?>
		
</td>
</tr>

<tr>
<td align="right" class="bc_label">Sub Category:</td>
<td align="left" class="bc_input_td">
<input type="text" name="name" id="name" class="bc_input" value="<?php echo $bc_name; ?>"/>
</td>
</tr>


<tr>
<td align="right" class="bc_label">Description:</td>
<td align="left" class="bc_input_td">
<textarea  name="descr" id="descr" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_descr; ?></textarea>
</td>
</tr>

<!--
<tr>
<td align="right" class="bc_label">Sorting Order:</td>
<td align="left" class="bc_input_td">
<input type="text" name="sort_order" id="sort_order" class="bc_input" value="<?php echo $sort_order; ?>"/>
</td>
</tr>-->

<tr>
<td align="right" class="bc_label">Tags:<br><font color="red">(Comma Separated)</font></td>
<td align="left" class="bc_input_td">
<textarea  name="tags" id="tags" class="bc_input" style="width:550px;height:40px;" /><?php echo $bc_tags; ?></textarea>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Meta title:</td>
<td align="left" class="bc_input_td">
<input type="text" name="meta_title" id="meta_title" class="bc_input" style="width:550px" value="<?php echo $bc_meta_title; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Meta keywords:</td>
<td align="left" class="bc_input_td">
<textarea  name="meta_keywords" id="meta_keywords" class="bc_input" style="width:550px;height:40px;" /><?php echo $bc_meta_keywords; ?></textarea>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Meta description:</td>
<td align="left" class="bc_input_td">
<textarea  name="meta_description" id="meta_description" class="bc_input" style="width:550px;height:100px;" /><?php echo $bc_meta_description; ?></textarea>
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
<script type="text/javascript">

tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "descr",
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