<script>
$(document).ready(function(){
	var container			=	$('div.ew-sugesstions');
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

<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat"> <span class="ew-left-control" style="top:96px"></span> <span class="ew-right-control" style="top:96px"></span>
    <div style="height: 248px; overflow: hidden; padding-top: 32px; position: relative;">
      <div class="ew-sugesstions">
        <div id="rec">
          <?php
		 $gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$event_id'");
		 $res = mysql_query("select * from `event_gallery_images` where `gallery_id`='$gallery_id'");
		 $numRows = mysql_num_rows($res);
		 if($numRows){
		 	$i = 0;
		 	while($row = mysql_fetch_array($res)){
		 		if ($row['image']!='' && file_exists(DOC_ROOT . 'event_images/gallery/sub_' . $row['image']) ) {
				$i++;
					list($viw, $vih) = getimagesize(EVENT_IMAGE_PATH.'gallery/sub_'.$row['image']);
					list($viw, $vih) = getPropSize($viw, $vih, 218,1000);
					if($numRows==$i){
						$style = "style='margin:0 0 10px 0;width:".$viw."px'";
					}else{
						$style = "style='width:".$viw."px;margin-bottom: 10px;'";
						}?>
          <div class="ew-suggetions-block"> <a style="max-height:156px; overflow:hidden; border:#e5e5e5 solid 4px; text-align:center; display:block; cursor:pointer" onclick="showimage('<?php echo ABSOLUTE_PATH; ?>','<?php echo ABSOLUTE_PATH.'event_images/gallery/'.$row['image']; ?>')"><img src="<?php echo EVENT_IMAGE_PATH.'gallery/sub_'.$row['image']; ?>" height="<?php echo $vih; ?>" width="<?php echo $viw; ?>" alt="" title=""></a> </div>
          <!--end ew-suggetions-block-->
          <?php }}} ?>
        </div>
        <br class="clear" />
      </div>
    </div>
  </div>
  <!--end blockerRepeat-->
  <div class="blockerBottom"></div>
  <!--end blockerBottom-->
</div>
<!--end blocker-->
