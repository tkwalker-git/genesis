<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$event_id 		= $_POST['event_id'];
	if($event_image==''){
	$event_image	=	getSingleColumn('event_image',"select * from `events` where `id`='$event_id'");
	}
	
	$e_type = $_POST['e_type'];
	
	if($e_type == 'pangea'){
		$extra	= "pangea";
		}
	?>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-1.4.min.js"></script>

<script src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui-full-1.5.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.flip.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/script.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/common_bc.js"></script>

<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>

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

.new_flayer_title{
	margin-top:5px !important;
	}

</style>

<div style="position:relative; width:520px; ">
  <div class="sponsor" id="spons"> <div class="clipImage" id="clickhere"><img src="/images/click_here.png" alt="" title="Click to flip" /></div>
    <div class="sponsorFlip">
      <div class="flayerTopFB" style="background:url('/images/new_flayer_top_fb.png') no-repeat scroll center top #FFFFFF!important">
	 
        <div class="flayerBottomFB" style="background:url('/images/new_flayer_bottom_fb.png') no-repeat scroll center bottom #FFFFFF!important">
          <div class="flayerMiddleFB" style="background:url('/images/new_flayer_middle_fb.png') repeat-y scroll 0 0 transparent!important">
            <div class="flayer" id="flayer" align="center" <?php if($e_type=='nba'){ echo 'style="overflow:visible; height:auto; min-height:500px"';} ?>>
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
      <?php include("../fbflayer/details.php"); ?>
    </div>
  </div>
</div>
