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
	$bc_name			=	DBin($_POST["name"]);
	$bc_url				=	DBin($_POST["url"]);
	$bc_image			=	$_FILES['sponsor_image']['name'];

	
	if ( trim($bc_name) == '' )
		$errors[] = 'Please enter Sponsor Name';
	if ( trim($bc_url) == '' )
		$errors[] = 'Please enter Sponsor Url';
	if(!isValidURL($bc_url) && $bc_url!='')
		$errors[] = "Please enter valid URL";
		
		
	$sql = "select `image` from `sponsor` where `id`='$frmID'";
	$res = mysql_query($sql);
	if($row = mysql_fetch_assoc($res)){
	if($row['image']==''){
	if ( trim($bc_image) == ''   )
		$errors[] ='Please enter Sponsor Image';
		}
	}
	
	
		
		
	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	$bc_image = '';

		if (isset($_FILES['sponsor_image']['name']) && !empty($_FILES["sponsor_image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["sponsor_image"]["name"] ;
			if ($action1 == "edit") {
				deleteImage($frmID,"sponsor","image");
			}
			move_uploaded_file($_FILES["sponsor_image"]["tmp_name"], '../images/' .$bc_image);
			makeThumbnail($bc_image, '../images/', '', 105, 103,'th_');
			$sql_img = ", image = '$bc_image'";
		}
	
	$rs = mysql_query("select * from `sponsor` where `id`='$frmID'");
	if(mysql_num_rows($rs)){
	$action = "edit";
	}	
	if($action == "save"){	
	$res = mysql_query("INSERT INTO `sponsor` (`id`, `name`, `url`, `image`) VALUES (NULL, '$bc_name', '$bc_url', '$bc_image')");
	}
	if($action=="edit"){	
	$sql	=	"UPDATE `sponsor` SET `name` = '$bc_name', `url` = '$bc_url' ";
	if($sql_img){
	$sql.=$sql_img;
	}
	$sql.=	 " WHERE `id` = '$frmID'";
	$res2 = mysql_query($sql);
	}
	
if ($res) {
		$sucMessage = "Sponsor Successfully inserted.";
		}
		elseif($res2){
		$sucMessage = "Sponsor Successfully updated.";
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
	
	$rs = mysql_query("select * from `sponsor` where `id`='$frmID'");
	while($r = mysql_fetch_array($rs)){
	$bc_name			=	$r["name"];
	$bc_url				=	$r["url"];
	$bc_image			=	$r["image"];
	}}
?>


<form method="post" name="bc_form" enctype="multipart/form-data" action="" autocomplete="off" >
  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr class="bc_heading">
      <td colspan="2" align="left">Add/Edit Sponsor</td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
    </tr>
	 <tr>
      <td width="22%" align="right" class="bc_label">Sponsor Name:<font color="#FF0000">*</font></td>
      <td width="78%" align="left" class="bc_input_td"><input type="text" class="bc_input" name="name" value="<?php echo $bc_name; ?>" style="width:250px" />      </td>
    </tr>
    <tr>
      <td width="22%" align="right" class="bc_label">Url:<font color="#FF0000">*</font></td>
      <td width="78%" align="left" class="bc_input_td"><input type="text" class="bc_input" name="url" value="<?php echo $bc_url; ?>" style="width:350px" />      </td>
    </tr>
    <tr>
      <td width="22%" align="right" class="bc_label">Sponsor Image:<font color="#FF0000">*</font></td>
      <td width="78%" align="left" class="bc_input_td">
	  <?php 
if( $bc_image != ''  ) {
	if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
		$bc_image1 = ABSOLUTE_PATH . 'images/th_'.$bc_image;
	else
		$bc_image1 = $bc_image;	
		
	echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer"
	rel="sponsor|image|'.$bc_image.'|../images/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
							
 ?>
<input type="file" name="sponsor_image" id="sponsor_image" class="bc_input" value="<?php echo $bc_image; ?>"/>
<br />
<?php echo $image_del;  ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><input name="submit" type="submit" value="Save" class="bc_button" id="submit" /></td>
    </tr>
  </table>
</form>
<?php 
require_once("footer.php"); 
?>
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
</script>