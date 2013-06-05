<?php

require_once("database.php"); 
require_once("header.php"); 

//$bc_source_id	=	"Admin-".rand();
$bc_event_source = 'Admin'; 

if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];

$action = "save";
$sucMessage = "";

if ( isset($_POST['submit']) ) {
	$bc_name				=	DBin($_POST["name"]);
	$bc_desc				=	$_POST["desc"];
	$bc_meta_title			=	DBin($_POST["meta_title"]);
	$bc_meta_keywords		=	DBin($_POST["meta_keywords"]);
	$bc_meta_description	=	DBin($_POST["meta_description"]);
	$bc_image				=	$_FILES['category_image']['name'];
	$seo_name				=	DBin($_POST["name"]);
	
	$bc_seo_name = make_seo_names($seo_name,"market_category","seo_name","");
	
	
	
	if ( trim($bc_name) == '' )
		$errors[] = 'Please enter Category Name';
		
		
	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	$bc_image = '';
	
		if (isset($_FILES["category_image"]) && !empty($_FILES["category_image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["category_image"]["name"] ;
			if ($action1 == "edit") {
				deleteImage($frmID,"market_category","image");
			}
			move_uploaded_file($_FILES["category_image"]["tmp_name"], '../images/category/' .$bc_image);
			makeThumbnail($bc_image, '../images/category/', '', 200, 200,'th_');
			makeThumbnail($bc_image, '../images/category/', '', 88, 86,'ico_');
			$sql_img = " image = '$bc_image' , ";
		}
	
	$rs = mysql_query("select * from `market_category` where `id`='$frmID'");
	if(mysql_num_rows($rs)){
	$action = "edit";
	}
	
	if($action == "save"){
	$res = mysql_query("INSERT INTO `market_category` (`id`, `name`, `desc`, `meta_title`, `meta_keywords`, `meta_description`, `image`, `seo_name`) VALUES (NULL, '$bc_name', '$bc_desc', '$bc_meta_title', '$bc_meta_keywords', '$bc_meta_description', '$bc_image', '$bc_seo_name');");
			}
	if($action=="edit"){
	$res2 = mysql_query("UPDATE `market_category` SET `name` = '$bc_name', `desc` = '$bc_desc', `meta_title` = '$bc_meta_title', `meta_keywords` = '$bc_meta_keywords', `meta_description` = '$bc_meta_description', `image` = '$bc_image', `seo_name` = '$bc_seo_name' WHERE `id` = '$frmID'");
	}
	
if ($res) {
			$sucMessage = "Record Successfully inserted.";
		}
		elseif($res2){
		$sucMessage = "Record Successfully updated.";
		}
		 else {
			$sucMessage = "Error: Please try Later";
		} 
}
else{
	$sucMessage = $err;
}
}

if($frmID){

	$rs = mysql_query("select * from `market_category` where `id`='$frmID'");
	while($r = mysql_fetch_array($rs)){
	$bc_name				=	$r["name"];
	$bc_desc				=	$r["desc"];
	$bc_meta_title			=	$r["meta_title"];
	$bc_meta_keywords		=	$r["meta_keywords"];
	$bc_meta_description	=	$r["meta_description"];
	$bc_image				=	$r['image'];
	$bc_seo_name			=	$r["seo_name"];
	}
	}
?>
<form method="post" name="bc_form" enctype="multipart/form-data" action="" autocomplete="off" >
  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr class="bc_heading">
      <td colspan="2" align="left">Add/Edit Category </td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
    </tr>
    <tr>
      <td width="22%" align="right" class="bc_label">Category Name:<font color="#FF0000">*</font></td>
      <td width="78%" align="left" class="bc_input_td"><input type="text" class="bc_input" name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Description:</td>
      <td align="left" class="bc_input_td"><textarea name="desc" class="bc_input" style="width:550px;height:40px;" />
        <?php echo $bc_desc; ?>
        </textarea>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Meta title:</td>
      <td align="left" class="bc_input_td"><input type="text" name="meta_title" id="meta_title" style="width:546px" class="bc_input" value="<?php echo $bc_meta_title; ?>"/>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Meta keywords:</td>
      <td align="left" class="bc_input_td"><textarea  name="meta_keywords" id="meta_keywords" class="bc_input" style="width:550px;height:40px;" />
        <?php echo $bc_meta_keywords; ?>
        </textarea>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Meta description:</td>
      <td align="left" class="bc_input_td"><textarea  name="meta_description" id="meta_description" class="bc_input" style="width:550px;height:100px;" />
        <?php echo $bc_meta_description; ?>
        </textarea>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Category Image:</td>
      <td align="left" class="bc_input_td"><?php 
if( $bc_image != ''  ) {
	if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
		$bc_image1 = ABSOLUTE_PATH . 'images/category/ico_'.$bc_image;
	else
		$bc_image1 = $bc_image;	
		
	echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_image" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer"
	rel="market_category|image|'.$bc_image.'|../images/category/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
							
 ?>
        <input type="file" name="category_image" id="category_image" class="bc_input" value="<?php echo $bc_image; ?>"/>
        <br />
        <?php echo $image_del;  ?> </td>
    </tr>
    <tr>
      <td width="22%" align="right" class="bc_label">Seo Name:</td>
      <td width="78%" align="left" class="bc_input_td"><input type="text" class="bc_input" name="seo_name" value="<?php echo $bc_seo_name; ?>" style="width:300px" />
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><input name="submit" type="submit" value="Save" class="bc_button" id="submit" />
      </td>
    </tr>
  </table>
</form>
<?php 
require_once("footer.php"); 
?>
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">

$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide();
		$("#delImg_" + imgInfo[1]).attr("src", "images/no_image.png");
	}
});

	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "desc",
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