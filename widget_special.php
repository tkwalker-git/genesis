<script>
$(document).ready(function(){
	var container		=	$('div.ew-sugesstions');
	var numbrRecord		=	$('.ew-suggetions-block', container).size();
	var innerWidth		=	$('.ew-suggetions-block', container).innerWidth()+25;
	var width = numbrRecord*innerWidth;

	$('#rec').css('width',width);
	$('#rec').css('position','relative');
	
	var current = 0;
	if(numbrRecord <= 4){
		$('.ew-left-control').css('display','none');
		$('.ew-right-control').css('display','none');
	}
	$('.ew-left-control').css('display','none');

	var i = 0;
$('.ew-left-control').click(function(){
	if(current!=0){
		$('.ew-right-control').css('display','block');
		current--;
		var right = $('.ew-suggetions-block', container).innerWidth()+25;
		right = right*current;
		$('#rec').animate({'right' : right}, 500);
		if(current==0){
			$('.ew-left-control').css('display','none');
		}
	}
});

$('.ew-right-control').click(function(){	
	if(current!=(numbrRecord-4)){
		$('.ew-left-control').css('display','block');
		current++;
		var right = $('.ew-suggetions-block', container).innerWidth()+25;
		right = right*current;
		$('#rec').animate({'right' : right}, 500);
		if(current==(numbrRecord-4)){
			$('.ew-right-control').css('display','none');
		}
	}
});

});
</script>

<?php
	if($_SESSION['userZip'])
		$userZip =	"and zipcode in (".$_SESSION['userZip'].")";
?>
<div class="blocker">
<div class="blockerTop"></div>
<!--end blockerTop-->
<div class="blockerRepeat">
<span class="ew-heading" style="font-size:18px;">Events Similar to <?php echo $event_name; ?> </span>

						<span class="dottedSeparator"></span>
						<span class="ew-left-control"></span>						
						<span class="ew-right-control"></span>
						
						<div style="position:relative; height:354px; overflow:hidden">
						<div class="ew-sugesstions">
						
						
						<div id="rec">
  <?php
  
  
					$sql2 = "select * from events where subcategory_id='$subcategory_id' ".$userZip." and id != '$c_event_id' AND is_expiring=1 order by id DESC";
					$res2 = mysql_query($sql2); 
					while ($rows2 = mysql_fetch_assoc($res2) ) {
						$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
						$full_name		= DBout($rows2['event_name']);
						$event_date 	= getEventStartDates($rows2['id']);
						$source			= $rows2['event_source'];
						$event_image	= getEventImage($rows2['event_image'],$source,'');
						$event_url		= getEventURL($rows2['id']);
				?>
<div class="ew-suggetions-block">
							
								<div class="ew-suggetion-top"></div><!--end ew-suggetion-top-->
								
								<div class="ew-suggetion-center">
								
									<span class="ew-suggetion-tiny-heading"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></span>
									
									<span class="ew-suggetion-date"><?php echo $event_date;?></span>
									
									<span class="ew-suggetion-separator"></span>
	<!--<img src="<?php echo IMAGE_PATH; ?>suggetion-thumb.png" width="156" height="196" border="0" />-->
	 <a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>" style="display: block; height: 194px;overflow: hidden;"><?php echo str_replace('align="left"','',$event_image);?></a>
	
	</div><!--end ew-suggetion-center-->
	
	<div class="ew-suggetion-bottom"></div>
	<div class="suggetion-btn">
      <?php getAddToWallButton($rows2['id'],1); ?>
    </div>
    <div class="star_rating" style="text-align:center">
      <?php getEventRatingAggregate($rows2['id']); ?>
    </div>
  </div><!--end ew-suggetions-block-->
  <?php } ?>
  </div>
<br class="clear" />
					</div>
					</div>
					</div> <!--end blockerRepeat-->
					<div class="blockerBottom"></div> <!--end blockerBottom-->
				</div>
<!--end blocker-->