<?php
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='login.php';</script>";



$meta_title = 'EMR Manager';

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
			<?php
					$sess_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
					 $uid = getSingleColumn("username","select * from `users` where `id`='".$sess_id."'");
					 $ups = getSingleColumn("password","select * from `users` where `id`='".$sess_id."'");
					 $src = "https://yourhealthsupport.com:8443/WebApplication1/logging.jsp?u=".$uid."&p=".$ups."&type=pangea";
			 ?>
						<iframe src="<?php echo $src; ?>" width="100%" height="850px" scrolling="no" seamless="yes"></iframe>					
					</div>
					<div class="clr"></div>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
$(document).ready(function () {

    setTimeout(function () {
        if (navigator.appName == "Microsoft Internet Explorer") {
            var $iframe = $("#emriframe");
            var src = $iframe.attr("src");
            $("#emriframe").attr("src", src);
            //$iframe.attr("src", src);
        }
    }, 3000);
});

 </script>
 
 
<?php include_once('includes/footer.php'); ?>

