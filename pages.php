<?php
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

include_once('includes/header.php');
?>

<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".hint").fancybox({
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: 400,
		'type'				: 'iframe'
	});
	});
</script>
<div style="padding-top:20px;">
  <div class="creatAnEvent">
    <div class="width96">
      <div class="creatAnEventMdl">Title</div>
    </div>
  </div>
  <!-- /creatAnEvent -->
  <div class="width96" style="padding-top:23px" id="showPages" align="center">
    <?php 
if($_REQUEST['type']=='details'){
include("details.php");
}
elseif($_REQUEST['type']=='photo'){
include("photo.php");
}
elseif($_REQUEST['type']=='location'){
include("location.php");
}
elseif($_REQUEST['type']=='specials'){
include("specials.php");
}
else{
include("flayer.php");
}?>
  </div>
</div>
<?php include_once('includes/footer.php');?>