<?php
require_once("database.php");
 

$action = '';
if( isset($_POST['pagetitle']) && isset($_POST['meta_title']) ) {

	$frmID			=	$_GET["id"];
	$upd_id			= (int)$_POST['upd_id'];
	$cp_pt 			= DBin($_POST['pagetitle']);
	$cp_pc			= DBin($_POST['menu_category']);
	$cp_contents	= DBin($_POST['contents']);
	$sql_img		= '';
	$cp_mt 			= DBin($_POST['meta_title']);
	$cp_mk 			= DBin($_POST['meta_keywords']);
	$cp_md 			= DBin($_POST['meta_description']);
	//$seo_name = DBin($_POST['seo_name']);
	
	$seo_name = make_seo_names($cp_pt,"site_pages","seo_name","");
	
	
			if( $upd_id == 0 )
				$Sql = "insert into  site_pages (page_title,page_content,meta_title,meta_keywords,meta_description,seo_name,menu_category) values ('".$cp_pt."','".$cp_contents."','".$cp_mt."','".$cp_mk."','".$cp_md."','".$seo_name."','".$cp_pc."')  ";
 			else
				$Sql = "update site_pages set page_title='$cp_pt',page_content='$cp_contents',meta_title='$cp_mt',meta_keywords='$cp_mk',meta_description='$cp_md',menu_category = '$cp_pc' where id='$upd_id' limit 1";
	 
 $res = mysql_query($Sql) or die(mysql_error());
 
   $msg = 'Page saved successfully...';
   $saved_class = 'saved_class';
}

require_once("header.php");

if( isset($_GET['id']) ) {
	$frmID = (int)$_GET['id'];
	$Sql = mysql_query("select * from site_pages where id='$frmID' limit 1") or die(mysql_query());
	$Res = mysql_fetch_assoc($Sql);
	
	$cp_pt 	 		 = DBout($Res['page_title']);
	$cp_contents 	 = DBout($Res['page_content']);
	$header_img 	 = DBout($Res['header_img']);
	$cp_mt 		 	 = DBout($Res['meta_title']);
	$cp_mk 			 = DBout($Res['meta_keywords']);
	$cp_md 		 	 = DBout($Res['meta_description']);
	$seo_name 		 = DBout($Res['seo_name']);
	$cp_pc	 		 = DBout($Res['menu_category']);
	
}

?>

<form method="post" enctype="multipart/form-data" action="">
<input type="hidden" name="upd_id" value="<?=$frmID?>" />

<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr class="bc_heading"><td>Site Pages</td></tr>
    <tr>
    	<td colspan="2">
			<?php if($msg) {
				echo '<div class="success">'. $msg .'</div>';
			}
	 		?>&nbsp;
        </td>
	</tr>
	
    <tr>
    	<td id="section1">
        	<div class="heading">Contents</div>
        	<table cellpadding="" cellspacing="5" width="98%" align="center">
				<tr>
				  <td>&nbsp;</td>
				</tr>
            	<tr><td class="td_label">Page Title:</td></tr>
                <tr><td class="td_input"><input type="text" name="pagetitle" value="<?php echo $cp_pt; ?>" size="120" /></td></tr>
                 <?php if($seo_name){?>
				<tr>
				  <td c><span class="page_url_link"><?php echo ABSOLUTE_PATH.'pages/'.$seo_name.'.html' ?></span>&nbsp;<a style="text-decoration:none;" href="<?php echo ABSOLUTE_PATH.'pages/'.$seo_name.'.html' ?>" target="_blank"><span>view page</span></a></td>
				</tr>
				<?php } ?>
				<tr>
				   <td>Menu Category:</td>
				</tr>
				<tr>
				   <td>
				      <select name="menu_category" id="menu_category">
					     <option <?php if($cp_pc == ''){?> selected="selected"<?php } ?> value="">No Category</option>
						 <option <?php if($cp_pc == 'Home'){?> selected="selected"<?php } ?> value="Home">Home</option>
						 <option <?php if($cp_pc == 'About'){?> selected="selected"<?php } ?> value="About">About</option>
						 <option <?php if($cp_pc == 'Contact'){?> selected="selected"<?php } ?> value="Contact">Contact</option>
						 <option <?php if($cp_pc == 'Policy'){?> selected="selected"<?php } ?> value="Policy">Policy</option>
					  </select>
				   </td>
				</tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td class="td_label">Page Contents:</td></tr>
                <tr><td class="td_input"><textarea name="contents" style="width:650px;height:200px;" id="descr"><?php echo $cp_contents; ?></textarea></td></tr>
            </table>
        </td>
    </tr>
    
    
    
    <tr>
    	<td id="section1">
        	<div class="heading">Meta</div>
        	<table cellpadding="" cellspacing="5" width="98%" align="center">
            	<tr><td class="td_label">Meta Title:</td></tr>
                <tr><td class="td_input"><input type="text" name="meta_title" value="<?php echo $cp_mt; ?>" size="100" /></td></tr>
                
                <tr><td class="td_label">Meta Keywords:</td></tr>
                <tr><td class="td_input"><textarea name="meta_keywords" style="width:600px;height:40px;"><?php echo $cp_mk; ?></textarea></td></tr>
                
                <tr><td class="td_label">Meta Description:</td></tr>
                <tr><td class="td_input"><textarea name="meta_description" style="width:600px;height:40px;"><?php echo $cp_md; ?></textarea></td></tr>
            </table>
        </td>
    </tr>
	
    <tr><td colspan="2" align="center"><br /><input name="submit" type="submit" value="Save" class="bc_button <?php echo $saved_class;?>" /></td></tr>
</table>
</form>

<?php require_once("footer.php"); ?>
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