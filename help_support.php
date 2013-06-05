<?php
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='login.php';</script>";


$meta_title = 'Help | Support';
include_once('includes/header.php');
?>



<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu_tk.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
			<div class="whiteMiddle" style="padding-top:1px;">
						<iframe src="https://pangea.desk.com/" width="100%" height="900px" scrolling="no"></iframe>
					</div>
					<div class="clr"></div>
				</div>
			</div>
		</div>
	</div>

<?php include_once('includes/footer.php'); ?>