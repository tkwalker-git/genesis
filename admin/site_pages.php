<?php

	require_once("database.php"); 
	require_once("header.php");

if($_GET['type'])
	$bc_page_type	=	$_GET['type'];


$bc_page_title			=	DBin($_POST["page_title"]);
$bc_page_content		=	DBin($_POST["page_content"]);
$sort_order				=	DBin($_POST["sort_order"]);
$bc_meta_title			=	DBin($_POST["meta_title"]);
$bc_meta_keywords		=	DBin($_POST["meta_keywords"]);
$bc_meta_description	=	DBin($_POST["meta_description"]);
$seo_name				=	DBin($_POST["page_title"]);
$bc_seo_name 			=	make_seo_names($seo_name,"site_pages","seo_name",$frmID);
$frmID					=	$_GET["id"];


$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["page_title"] == "")
	$errors[] = "Page_title: can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	$bc_page_type			=	$_POST['page_type'];
	
	if (!count($errors)) {
	$bc_image = '';
		if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["image"]["name"] ;
			$bc_image	=	str_replace(" ","_", $bc_image);
			if ($action1 == "edit") {
				deleteImage($frmID,"site_pages","image");
			}
			move_uploaded_file($_FILES["image"]["tmp_name"], '../images/' .$bc_image);
			makeThumbnail($bc_image, '../images/', '', 296 , 127,'th_');
			$sql_img = " `image` = '$bc_image' , ";
		}

		 if ($action1 == "save") {
			$sql	=	"insert into site_pages (page_title,page_content,image,sort_order,header_img,meta_title,meta_keywords,meta_description,seo_name,page_type) values ('" . $bc_page_title . "','" . $bc_page_content . "','" . $bc_image . "','" . $sort_order . "','','" . $bc_meta_title . "','" . $bc_meta_keywords . "','" . $bc_meta_description . "','" . $bc_seo_name . "','" . $bc_page_type . "')";
			
			
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update `site_pages` set `page_title` = '$bc_page_title' , `page_content` = '$bc_page_content' , $sql_img `sort_order` = '$sort_order' , `meta_title` = '$bc_meta_title' , `meta_keywords` = '$bc_meta_keywords' , `meta_description` = '$bc_meta_description' , `seo_name` = '$bc_seo_name' where `id` = '$frmID'";
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
$sql	=	"select * from site_pages where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_page_title			=	 DBout($row["page_title"]);
		$bc_page_content		=	 DBout($row["page_content"]);
		$sort_order				=	 DBout($row["sort_order"]);
		$bc_header_img			=	 DBout($row["header_img"]);
		$bc_meta_title			=	 DBout($row["meta_title"]);
		$bc_meta_keywords		=	 DBout($row["meta_keywords"]);
		$bc_meta_description	=	 DBout($row["meta_description"]);
		$bc_page_type			=	$row['page_type'];
		$bc_image				=	$row['image'];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Add Site Pages</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Page title:</td>
<td align="left" class="bc_input_td">
<input type="text" name="page_title" id="page_title" class="bc_input" value="<?php echo $bc_page_title; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Page content:</td>
<td align="left" class="bc_input_td">
<textarea  name="page_content" id="page_content" class="bc_input" style="width:550px;height:40px;" /><?php echo $bc_page_content; ?></textarea>
</td>
</tr>

<?php
 if($bc_page_type == 'feature'){ ?>
<tr>
<td align="right" class="bc_label">Page Image:</td>
<td align="left" class="bc_input_td">
<?php 
if( $bc_image != ''  ) {
	if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
		$bc_image1 = IMAGE_PATH . 'th_'.$bc_image;
	else
		$bc_image1 = $bc_image;	
		
	echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_event_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="events|event_image|'.$bc_image.'|../event_images/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
							
 ?>
<input type="file" name="image" />
<div class="red">296 x 127</div>
</td>
</tr>
<?php } ?>

<tr>
<td align="right" class="bc_label">Sorting Order:</td>
<td align="left" class="bc_input_td">
<input type="text" name="sort_order" id="sort_order" class="bc_input" value="<?php echo $sort_order; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Meta title:</td>
<td align="left" class="bc_input_td">
<input type="text" name="meta_title" id="meta_title" class="bc_input" value="<?php echo $bc_meta_title; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Meta keywords:</td>
<td align="left" class="bc_input_td">
<input type="text" name="meta_keywords" id="meta_keywords" class="bc_input" value="<?php echo $bc_meta_keywords; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Meta description:</td>
<td align="left" class="bc_input_td">
<textarea  name="meta_description" id="meta_description" class="bc_input" style="width:550px;height:40px;" /><?php echo $bc_meta_description; ?></textarea>
</td>
</tr>

<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" value="Save" class="bc_button" />
<input type="hidden" name="page_type" value="<?php echo $bc_page_type; ?>" />
</td>
</tr>
</table>
</form>

	<?php require_once("footer.php"); ?>

<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "page_content",
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