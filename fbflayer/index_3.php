<?php

	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$event_id	=	$_GET['id'];
	$event_image	=	getSingleColumn('event_image',"select * from `events` where `id`='$event_id'");
	
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="/js/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="/js/jquery-ui-full-1.5.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/js/jquery.flip.min.js"></script>
<script type="text/javascript" src="/js/script.js"></script>
<script type="text/javascript" src="/js/common_bc.js"></script>
<style>
.sponsor{
		width:520px;
}
.sponsorFlip{
		left:0;
		top:0;
		width:520px;
}
.sponsorData{
		display:none;
}

body{
	padding:0 !important;
	margin:0 !important;
	width:520px !important;
	min-width:520px !important;
	}
</style>

<link href="/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="page-bg" class="translucent"></div>
<div class="subscribe-overlayer" id="overlayer" align="center"></div>
<span id="flayermain" >
<div style="position:relative; width:520px; ">
  <div class="sponsor" id="spons"> <div class="clipImage" id="clickhere"><img src="/images/click_here.png" alt="" title="Click to flip" /></div>
    <div class="sponsorFlip">
      <div class="flayerTopFB" style="background:url('/images/new_flayer_top_fb.png') no-repeat scroll center top #FFFFFF!important">
	 
        <div class="flayerBottomFB" style="background:url('/images/new_flayer_bottom_fb.png') no-repeat scroll center bottom #FFFFFF!important">
          <div class="flayerMiddleFB" style="background:url('/images/new_flayer_middle_fb.png') repeat-y scroll 0 0 transparent!important">
            <div class="flayer" id="flayer" align="center">
			  <?php			  
		$event_image = removeSpaces("events","event_image",$event_image,"../event_images/");
					//	removeSpaces('tableName','filedName','imageName','path');
		
			echo	getFlayerImage($event_image,'','','486');
			  ?>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    <div class="sponsorData">
      <?php include("details.php"); ?>
    </div>
  </div>
</div>
</span>
</div>
</body>
</html>