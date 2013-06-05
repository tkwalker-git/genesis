<?php  
	require_once('admin/database.php');
	require_once('site_functions.php');
	
	$event_id = $_GET['id'];
	if ( $event_id > 0 )
		$event_url		= getEventURL($event_id);
	
	require_once('includes/header.php'); 
	
?>

<div style="width:960px; margin:auto;">
	<div class="welcomeBox"></div>
	<div class="eventDetailhd"><span>Event Saved</span></div>
	<div class="clr">&nbsp;</div>

	<div style="width:960px; margin:auto; padding-bottom:10px; height:300px; padding-top:20px; font-size:16px; line-height:25px">
		<strong>Congratulations! Your event is now published.  See your event listing <a href="<?php echo $event_url;?>" style="text-decoration:underline; color:#0066FF">here</a>.</strong>
		<br>
		<br>
		<a href="<?php echo ABSOLUTE_PATH;?>add_event.php" style="text-decoration:underline; color:#0066FF">Add More Events</a> &nbsp; - &nbsp; 
		<a href="<?php echo ABSOLUTE_PATH;?>manage_events.php" style="text-decoration:underline; color:#0066FF">Manage Events</a>
	</div>
</div>

<?php  require_once('includes/footer.php'); ?>