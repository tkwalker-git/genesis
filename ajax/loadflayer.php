<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$event_id 		= $_POST['event_id'];
	if($event_image==''){
	$event_image	=	getSingleColumn('event_image',"select * from `events` where `id`='$event_id'");
	}
	?>
<script type="text/javascript" src="<?= ABSOLUTE_PATH; ?>js/jquery.flip.min.js"></script>
<script type="text/javascript" src="<?= ABSOLUTE_PATH; ?>js/script.js"></script>
<style>
.sponsor{
		width:554px;
	/*	width:auto;
		height:auto;
		margin:4px;
	*/
	/* Giving the sponsor div a relative positioning: */
	/*	position:relative;
		cursor:pointer;*/
}
.sponsorFlip{
		/*  The sponsor div will be positioned absolutely with respect
			to its parent .sponsor div and fill it in entirely */
	
	/*	position:absolute; */
		left:0;
		top:0;
		width:534px;
	/*	height:700px;
			width:100%;
		height:100%; */
}
.sponsorData{
		/* Hiding the .sponsorData div */
		display:none;
}
</style>
<div style="position:relative;">
  <div class="sponsor" id="spons">
    <div class="sponsorFlip">
      <div class="new_flayerMain">
        <div id="clickhere" style="position:absolute; right:18px; cursor:pointer"><img src="<?= IMAGE_PATH; ?>new_clickhere.png" alt="" title="Click to flip" /></div>
        <div class="new_flayerMain_middle_main">
          <!-- end inrDiv -->
          <span>
          <?php
	  
	  $event_image = removeSpaces("events","event_image",$event_image,"event_images/");
			echo	getFlayerImage($event_image,'','','520');
	  
	  ?>
          </span> </div>
        <div class="new_flayerMain_bottom">&nbsp;</div>
      </div>
    </div>
    <div class="sponsorData">
      <?php include("../details.php"); ?>
    </div>
  </div>
</div>