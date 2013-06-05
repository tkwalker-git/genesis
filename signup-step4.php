<?php 
	session_start();
	/*if($_SESSION['page_ref'] != 'signup-step3.php'){
		header ("Location: index.php");
	} */

?>
<?php
require_once('admin/database.php');
?>  
<?php  require_once('includes/header.php'); ?> 

<div id="main">
	<div id="signup-main">
				
			<div id="signup-main-top"></div>
				<div id="signup-main-middle">
					<div id="check-mark"><div id="check-img"><img src="<?php echo IMAGE_PATH;?>check-mark.png" /></div></div>
					<div id="congrat"><p> Congratulations! Your membership is complete. </p> </div>
					<div id="rec-event"><div id="view-event-img"><a href="category.php"><img src="<?php echo IMAGE_PATH;?>view-event-btn.png" /></a></div></div>
					<div id="choice"><p align="center">or</p> </div>
					<div id="pref"><span><a href="#">Edit your Preferences to get Recommended Events</a></span></div>
					<div id="empty"></div>
				</div><!-- signup-main-middle-->
			<div id="signup-main-bottom"></div>	
		
	</div><!-- signup-main -->
</div><!-- main -->

<div class="clr"></div>	
	
	
	
<?php require_once('includes/footer.php');?>
