<?php 

require_once('admin/database.php');
require_once('site_functions.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
$event_id		= $_GET['id'];
$eventUserId	= getSingleColumn("userid","select * from `events` where `id`='$event_id'");
$event_name		= getSingleColumn("event_name","select * from `events` where `id`='$event_id'");

if($eventUserId!=$_SESSION['LOGGEDIN_MEMBER_ID'])
	echo "<script>window.location.href='manage_event.php';</script>";



if(isset($_POST['submit'])){
	$note	= $_POST['note'];
	$close	= DBin($_POST['close']);
	if($close)
		$active = "Closed";
	else
		$active = "Active";
	
	$alrdyId	= getSingleColumn("id","select * from `event_rsvp_close` where `event_id`='$event_id'");
	if($alrdyId){
		$r = mysql_query("UPDATE `event_rsvp_close` SET  `note` =  '$note', `close` =  '$close' WHERE `id` = $alrdyId");
	}
	else{
		$r = mysql_query("INSERT INTO `event_rsvp_close` (`id`, `note`, `event_id`, `close`) VALUES (NULL, '$note', '$event_id', '$close')");
	}
	if($r)
		$msg = "RSVP ".$active." Successfully";
	else
		$msg = "<strong>Error:</strong> Try again later";
}





$res = mysql_query("select * from `event_rsvp_close` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
	$note	= DBout($row['note']);
	$close	= $row['close'];
}


$meta_title	= 'Close RSVP';
include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Close RSVP </div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:10px;">
		  <span style="color:#FF0000"><?php echo $msg; ?></span>
		  <form method="post">
		  	<table cellpadding="0" cellspacing="0" width="83%" align="center" border="0">
				<tr>
					<td height="40" width="18%"><strong>Event Name</strong>:</td>
					<td width="82%"><?php echo $event_name; ?></td>
				</tr>
				<tr>
					<td><strong>Closing Note</strong>:</td>
					<td><textarea name="note" id="note"><?php echo $note; ?></textarea></td>
				</tr>
				<tr>
					<td height="40"><strong>Close Rsvp</strong>:</td>
					<td><input type="checkbox" name="close" value="1" <?php if($close==1){ echo 'checked="checked"';} ?> ></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="image" src="images/submit_btn.jpg" value="submit" name="submit">
						<input type="hidden" value="submit" name="submit">
					</td>
				</tr>	
			</table>
			</form>
		  
		  </div> <!-- end whiteMiddle -->
        </div>
      </div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>
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
		content_css : "style.css",
	});
</script>