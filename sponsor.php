<?php
	
	require_once('admin/database.php');
	require_once('site_functions.php');
	
$sponsors_info = array(
       array("Frontline Promotions","Always taking it to the next level","http://www.weareclassicweekend.com","http://www.eventgrabber.com/images/FrontlineLogo1.jpg"),
       array("Torrence Lifestyles","Torrence Lifestyles and Entertainment","http://www.facebook.com/pages/Torrence-Lifestyles-and-Entertainment/186046164761885","http://www.eventgrabber.com/images/TORRENCELIFELOGO.jpg"),
  //     array("Eventgrabber.com","Ultimate source for local events","http://www.eventgrabber.com","eg.jpg"),
      );
	
	
//	shuffle($sponsors_info);
?>
<style>
.sponsor{
	cursor:pointer;
	text-align:center;
	}
.sponsorFlip{
	min-height: 109px;
	}
.sponsorData{
	color:#fff;
	}
.sponsorDescription{
	padding:10px 5px;
	}
.sponsorData{
	display:none;
	}
.sponsorURL a{
	color:#0099FF;
	}
</style>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script>
$(document).ready(function(){


var container  =	$('img[name=sponsorImage[]]');

	$(container).each(function() {
	var width = $(this).innerWidth();

	var id = $(this).attr('id');
	
	id = id.split('image');
	
	$('#sponsor'+id[1]).css('width',width);

	});



});
/*
	var id	=	id.split('image');
	alert(id[0]);
	alert(id[1]);
*/

</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="flip/jquery.flip.min.js"></script>
<script type="text/javascript" src="flip/script.js"></script>
<link href="<?php echo ABSOLUTE_PATH; ?>style.css" rel="stylesheet" type="text/css">
<link href="<?php echo ABSOLUTE_PATH; ?>florida.css" rel="stylesheet" type="text/css">


<div class="sponsorsContents"> <span><img src="<?php echo IMAGE_PATH; ?>sponsors_heading.png" width="298" height="57" border="0" /></span>
<ul>
<?php
$id = 0;
	foreach($sponsors_info as $sponsors)
		{
		$id++;
	?>
	  <li>
		<div class="sponsor" id="sponsor<?php echo $id; ?>" title="Click to flip">
		  <div class="sponsorFlip"> <img src="<?php echo $sponsors[3]; ?>" border="0" id="image<?php echo $id; ?>" name="sponsorImage[]" /> </div>
		  <div class="sponsorData">
			<div class="sponsorDescription"> <strong><?php echo $sponsors[0]; ?></strong><br /><?php echo $sponsors[1]; ?> </div>
			<div class="sponsorURL"> <a href="<?php echo $sponsors[2]; ?>" target="_blank">Website</a> </div>
		  </div>
		</div>
	  </li>
	 <?php
	 }
	?>
</ul>
</div>