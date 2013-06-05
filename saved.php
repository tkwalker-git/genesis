<?php  
	require_once('admin/database.php');
	require_once('site_functions.php');
	
	$event_id = $_GET['id'];
	if ( $event_id > 0 && $_GET['type']=='event' ){
		$event_url		= getEventURL($event_id);
		}
	
	require_once('includes/header.php'); 
	
?>

<div style="width:960px; margin:auto;">
	<div class="welcomeBox"></div>
	<div class="eventDetailhd"><span><?php echo ucwords($_REQUEST['type']); ?> Saved</span></div>
	<div class="clr">&nbsp;</div>

	<div style="width:960px; margin:auto; padding-bottom:10px; height:300px; padding-top:20px; font-size:16px; line-height:25px">
		<strong>Congratulations! Your <?php echo $_REQUEST['type']; ?> is now published.  See your <?php echo $_REQUEST['type']; ?> listing 
		<a href="<?php if ($_REQUEST['type']=='event'){ echo $event_url;} else{ 
		$r = mysql_query("select * from `products` where `id`='$event_id'");
		while($row = mysql_fetch_array($r)){
		$seo_name	=	$row['seo_name'];
		$id			=	$row['id'];
		}
		echo ABSOLUTE_PATH."deal/". $seo_name."/".$id.".html"; } ?>" style="text-decoration:underline; color:#0066FF">here</a>.</strong>
		
		<br />
		
		<a href="<?php echo ABSOLUTE_PATH; if ($_GET['type']=='event'){ echo "create_event.php";} else{ echo "create_deal.php";} ?>" style="text-decoration:underline; color:#0066FF">Add More <?php echo ucwords($_REQUEST['type'])."s";?></a> &nbsp; - &nbsp; 
		<a href="<?php echo ABSOLUTE_PATH; if ($_GET['type']=='event'){ echo "activity_manager.php";} else{ echo "manage_deals.php";} ?>" style="text-decoration:underline; color:#0066FF">Manage <?php echo ucwords($_REQUEST['type'])."s";?></a>
	</div>
</div>
<?php  require_once('includes/footer.php'); ?>