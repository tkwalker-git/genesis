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
<script type="text/javascript">
$(document).ready(function(){
	$('#clickhere').css('cursor','pointer');
});
</script>
<style>

.sponsor{
		width:490px;
		margin:auto;
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
	left: 0;
    min-height: 300px;
    top: 0;
	margin:auto;
    width: 490px!important;
	text-align:center
	/*	height:700px;
			width:100%;
		height:100%; */
}
.sponsorData{
		/* Hiding the .sponsorData div */
		display:none;
}
</style>

</style>

</style>
<div style="position:relative;">
                <div class="sponsor" id="spons">
   					 <div class="sponsorFlip">
					 
				 <div id="clickhere">
                  <?php
				  $event_image = removeSpaces("events","event_image",$event_image,"../event_images/");
					echo	getFlayerImage($event_image,'','','486');
					?>
                  </div>
				  </div>
              
              <div class="sponsorData">
                <?php
					 	include("../sp_details.php");
					  ?>
              </div>
			  </div></div>