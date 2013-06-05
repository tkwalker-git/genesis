<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_banner_name	=	$_POST["banner_name"];
$bc_version	=	$_POST["version"];
$bc_image	=	$_FILES["image"]["name"];
$bc_link	=	$_POST["link"];
$bc_sort_order	=	$_POST["sort_order"];
$bc_banner_img_top	=	$_POST["banner_img_top"];
$bc_banner_img_left	=	$_POST["banner_img_left"];
$bc_banner_img	=	$_POST["banner_img"];
$bc_banner_img_hover	=	$_POST["banner_img_hover"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["banner_name"] == "")
	$errors[] = "Banner Name: can not be empty";
if ($_POST["version"] == "")
	$errors[] = "Version: can not be empty";
if ($_POST["image"] == "")
	$errors[] = "Image: can not be empty";
if ($_POST["link"] == "")
	$errors[] = "Link: can not be empty";

$seo_name = make_seo_names($bc_banner_name,"home_page_banner","seo_name","");

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		if ($_FILES["image"]["name"] != "") {
			$bc_image  = time() . "_" . $_FILES["image"]["name"] ;
			if ($action1 == "edit") 
				deleteImage($frmID,"home_page_banner","image");
			move_uploaded_file($_FILES["image"]["tmp_name"], '../images/articles/' .$bc_image);
			$bci_image = ',image = "' . $bc_image . '"';
		} else {
			$bci_image = "";
		}

		 if ($action1 == "save") {
			$sql	=	"insert into home_page_banner (banner_name,version,image,link,sort_order,banner_img_top,banner_img_left,banner_img,banner_img_hover) values ('" . $bc_banner_name . "','" . $bc_version . "','" . $bc_image . "','" . $bc_link . "','" . $bc_sort_order . "','" . $bc_banner_img_top . "','" . $bc_banner_img_left . "','" . $bc_banner_img . "','" . $bc_banner_img_hover . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update home_page_banner set banner_name = '" . $bc_banner_name . "', version = '" . $bc_version . "', " . $bci_image . "', link = '" . $bc_link . "', sort_order = '" . $bc_sort_order . "', banner_img_top = '" . $bc_banner_img_top . "', banner_img_left = '" . $bc_banner_img_left . "', banner_img = '" . $bc_banner_img . "', banner_img_hover = '" . $bc_banner_img_hover . "' where id=$frmID";
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
$sql	=	"select * from home_page_banner where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_banner_name	=	$row["banner_name"];
		$bc_version	=	$row["version"];
		$bc_image	=	$row["image"];
		$bc_link	=	$row["link"];
		$bc_sort_order	=	$row["sort_order"];
		$bc_banner_img_top	=	$row["banner_img_top"];
		$bc_banner_img_left	=	$row["banner_img_left"];
		$bc_banner_img	=	$row["banner_img"];
		$bc_banner_img_hover	=	$row["banner_img_hover"];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Test Page Title</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Banner Name:</td>
<td align="left" class="bc_input_td">
<input type="text" name="banner_name" id="banner_name" class="bc_input" value="<?php echo $bc_banner_name; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Version:</td>
<td align="left" class="bc_input_td">
<input type="text" name="version" id="version" class="bc_input" value="<?php echo $bc_version; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Image:</td>
<td align="left" class="bc_input_td">
<?php
 if( $bc_image != '' ) { 
	echo '<img src="../images/articles/'. $bc_image .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="home_page_banner|image|'. $bc_image .'|../images/articles/" />';
} else {
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
}
?>
<input type="file" name="image" id="image" /><br>
<?=$image_del?>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Link:</td>
<td align="left" class="bc_input_td">
<input type="text" name="link" id="link" class="bc_input" value="<?php echo $bc_link; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Sort_order:</td>
<td align="left" class="bc_input_td">
<input type="text" name="sort_order" id="sort_order" class="bc_input" value="<?php echo $bc_sort_order; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Banner_img_top:</td>
<td align="left" class="bc_input_td">
<input type="text" name="banner_img_top" id="banner_img_top" class="bc_input" value="<?php echo $bc_banner_img_top; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Banner_img_left:</td>
<td align="left" class="bc_input_td">
<input type="text" name="banner_img_left" id="banner_img_left" class="bc_input" value="<?php echo $bc_banner_img_left; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Banner_img:</td>
<td align="left" class="bc_input_td">
<input type="text" name="banner_img" id="banner_img" class="bc_input" value="<?php echo $bc_banner_img; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Banner_img_hover:</td>
<td align="left" class="bc_input_td">
<input type="text" name="banner_img_hover" id="banner_img_hover" class="bc_input" value="<?php echo $bc_banner_img_hover; ?>"/>
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