<?php  
	
	require_once('admin/database.php');
	
	$seo_name = $_GET['seo_name'];
	$sql = "select * from blog_posts where seo_name='" . $seo_name. "'";
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		$title 		= DBout($row['title']);
		$contents 	= DBout($row['contents']);
	}	
	require_once('includes/header.php'); 
	
?>

<div style="width:960px; margin:auto;">
	<div class="welcomeBox"></div>
	<div class="eventDetailhd"><span><?php echo $title;?></span></div>
	<div class="clr">&nbsp;</div>
	<table width="100%" border="0" cellspacing="10" cellpadding="5">
	  <tr>
		<td width="680" align="left" valign="top">
			<?php echo $contents;?>
		</td>
		<td valign="top" style="background-image:url(<?php echo ABSOLUTE_PATH;?>images/blog_bg.jpg); background-repeat:repeat-x; padding:25px 5px">
			<span class="mainHd"><strong>Recent Posts</strong></span>
		</td>
	  </tr>
	</table>

	
</div>


<?php  require_once('includes/footer.php'); ?>