<?php 
	session_start();
	/*if($_SESSION['page_ref'] != 'signup-step2.php'){
		header ("Location: index.php");
	} */

?>
<?php
require_once('admin/database.php');
?>
<?php require_once('includes/header.php'); ?>

	
	<div id="main">
		<!-- Start Middle-->
		<div id="middleContainer">
			<div id="signup-main">
					
				<div id="signup-main-top"></div>
					<div id="signup-main-middle">
						<div id="check-mark"><div id="check-img"><img src="<?php echo IMAGE_PATH;?>check-mark.png" /></div></div>
						<div id="congrat"><p> Congratulations! Your membership is complete. </p> </div>
							<div id="rec-event"><div id="rec-event-img">
								<a href="myeventwall.php"><img src="<?php echo IMAGE_PATH;?>rec-event-btn.png" border="0" /></a>
							</div></div>
						<div id="empty"></div>
					</div><!-- signup-main-middle-->
				<div id="signup-main-bottom"></div>	
			
			</div><!-- signup-main -->
	</div>
	<!--End Middle-->
	</div>
	<div class="clr"></div>
	
<?php require_once('includes/footer.php');?>
<?php
	if(isset($_POST['submit']) && $_POST['submit'] == 'yes'){
		$_SESSION['page_ref'] = '';
		$_SESSION['page_ref'] = 'signup-step3.php';
		header ("Location: signup-step4.php");
	}	
?>

</script>