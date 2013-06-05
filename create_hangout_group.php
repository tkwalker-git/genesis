<?php require_once('admin/database.php');

if(isset($_GET["id"])){
	$frmID	=	$_GET["id"];
	$meta_title	=	"Update";
	}
	else{
	$meta_title	=	"Create";
	}
$meta_title	.=	" Hangout Group";

$action = "save";
$sucMessage = "";

	if ( isset($_POST['submit']) ) {
	$bc_name			=	DBin($_POST["name"]);
	$bc_members			=	$_POST["members"];
	$bc_desc			=	DBin($_POST["desc"]);
	
	$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";
	

	
	if ( trim($bc_name) == '' )
		$errors[] = 'Please enter Group Name';
	if ( count($bc_members) == 0 )
		$errors[] = 'Please select Members';
		
	if ( trim($bc_desc) == '' )
		$errors[] = 'Please enter Description';
		
		
		if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	$bc_image = '';
	
	if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["tmp_name"])) {
	
		$bc_image  = time() . "_" . $_FILES["image"]["name"] ;
		
			//if ($action1 == "edit") {
//				deleteImage($frmID,"products","image");
//			}
			move_uploaded_file($_FILES["image"]["tmp_name"], 'images/group/'.$bc_image);
			makeThumbnail($bc_image, 'images/group/', '', 162, 187,'th_');
			//@unlink('images/products/'.$bc_image);
			$sql_img = " ,  image = '$bc_image'";
		}
		
if($action1 == "save"){
	$res = mysql_query("INSERT INTO `hangout_group` (`id`, `name`, `desc`, `image`, `member_id`) VALUES (NULL, '$bc_name', '$bc_desc', '$bc_image', '$user_id');");
	$group_id = mysql_insert_id();
	if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
				}
	
}
if($action1=="edit"){
$group_id = $frmID;
$res2 = mysql_query("UPDATE `hangout_group` SET `name` = '$bc_name', `desc` = '$bc_desc' $sql_img WHERE `id` = '$frmID'");
mysql_query("DELETE FROM `group_members` WHERE `group_id` = '$frmID'");
if ($res2) {
				$sucMessage = "Record Successfully updated.";
			} else {
				$sucMessage = "Error: Please try Later";
				}
	}
	
	for ($i=0;$i<count($bc_members);$i++){
	mysql_query("INSERT INTO `group_members` (`id`, `group_id`, `member_id`) VALUES (NULL, '$group_id', '$bc_members[$i]');");
	}
	
	
	}
	else{
	$sucMessage = $err;
	}
		
	
	}



if($frmID){
$res = mysql_query("select * from `hangout_group` where `id`='$frmID'");
while($row = mysql_fetch_array($res)){
	$bc_name			=	DBout($row["name"]);
	$bc_image			=	$row["image"];
	$bc_desc			=	DBout($row["desc"]);
$res2 = mysql_query("select * from `group_members` where `group_id`='$frmID'");
while($row2	=	mysql_fetch_array($res2)){
$bc_members[]	=	$row2['member_id'];
}


}
$action = "edit";

}

require_once('includes/header.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
		


?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<div class="topContainer">
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="marketPlaceTop" style="padding-bottom: 18px;">
      <?php
	  $active = 'my_network';
	  include("eventwallNav.php"); ?>
      <div class="clear"></div>
    </div>
    <div id="record1">
      <div class="wallTitle">Create Hangout Group</div>
     <div class="clr" style="height:16px">&nbsp;</div>
      <div class="clr"></div>
      <div class="frndBoxTop">
        <div class="frndBoxBottom">
          <div class="frndBoxMiddle">
           <form method="post" name="bc_form" enctype="multipart/form-data" action="">
				 <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
                  <div class="success" align="center"><?php echo $sucMessage; ?></div>
                  <div class="evField">Group Name: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <input type="text" maxlength="100" class="inp" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Members: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <select name="members[]" multiple="multiple" class="inp" style="width:305px; height:100px;">
                      <?php
					$res = mysql_query("select * from `member_referals` where `ref_member_id`='$user_id' ORDER BY `id` ASC");
					while($row = mysql_fetch_array($res)){
					$name 	=	getFnameLname($row['member_id']);
					if($bc_members){
					
					for($i=0;$i<count($bc_members);$i++){
					if($bc_members[$i]==$name['id']){
					$act	=	'selected="selected"';
					}
					}}
					?>
                      <option <?php echo $act; ?>  value="<?php echo $name['id']; ?>"><?php echo $name['name']." ".$name['lname']; ?></option>
                      <?php
					  $act	=	''; } ?>
                    </select>
                  </div>
                  <div class="clr"></div>
				  
				  <div class="evField">Group Image:</div>
                  <div class="evLabal">
                   <?php 
if( $bc_image != '' ) {
	echo '<img src="images/group/th_'.$bc_image .'" class="dynamicImg" id="delImg_image"/><br>';
	$image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="hangout_group|image|'.$bc_image.'|../images/group/" />';
}
else
	echo '<img src="admin/images/no_image.png" class="dynamicImg"width="75" height="76" />';
?>
<input type="file" class="inp" name="image" id="image" /><br />
<?=$image_del?>
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Description: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <textarea name="desc" class="bc_input" style="width:550px;height:250px;" /><?php echo $bc_desc; ?></textarea>
                  </div>
                  <div class="clr"></div>
                  <div align="center"><br>
                    <input type="image" src="<?=IMAGE_PATH;?>save_group.gif" name="submit" value="submit">
                    <input type="hidden" name="submit" value="submit">
                  </div>
                </form>
            <div class="clr"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="clr"></div>
	</div></div>
<?php require_once('includes/footer.php');?>
<script type="text/javascript">
$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide();
		$("#delImg_image").attr("src", "admin/images/no_image.png");
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
		content_css : "style.css",
	});
	
	function submitform(){	
			
	document.forms["searchfrmdate"].submit();

	}

</script>
