<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$member_id		= $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$catid		 	= $_POST['category_id'];
	$attr_name		= attribValue("categories","name","where id=" . $catid);
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	$total_rec		= $_POST['tot'];
	
	//$recQuery		= stripslashes($_POST['list']);
	$recQuery		= $_SESSION['REC_QUERY'][$catid];
	
	$lmt = 5;
	
	$total_pages 	= ceil($total_rec/$lmt);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * $lmt ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * $lmt ; 
	}
			
	$limit = ' order by `id` ASC LIMIT '. $start . ' , ' . $lmt;

?>

		<div class="yellow_bar"><?php echo $attr_name;?> &nbsp; <a href="/settings.php?p=event-preferences">Edit Preferences</a></div>
		<div class="rBox">
		<div class="categoryEventBlock_right">
		
			<ul class="category_ul" >
			<?php 
		  		
			$sql2 = $recQuery . $limit;
			$res2 = mysql_query($sql2); 
		  	while ($rows2 = mysql_fetch_assoc($res2) ) {
				$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
				$full_name		= DBout($rows2['event_name']);
				$event_date 	= getEventStartDates($rows2['id']);
				$source			= $rows2['event_source'];
				$event_image	= getEventImage($rows2['event_image'],$source,1);
				$event_url		= getEventURL($rows2['id']);
				
			?>
			<li>
				<div class="heading_dark_12" style="width:140px; text-align:center"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></div>
				<div class="d_style_12" style="text-align:center; width:140px;"><?php echo $event_date;?></div>
				<div class="small_event_photo_block">
					<div class="small_event_photo_border">
						<div style="overflow:hidden; width:127px; height:160px">
							<a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a>
						</div>	
					</div><!--end big_event_photo_border-->
				</div><!--end big_event_photo_block-->
				<div class="add_to_event_btn_small" name="addEvent"><?php getAddToWallButton($rows2['id'],1); ?></div>
				<div class="star_rating" style="text-align:center"><?php getEventRatingAggregate($rows2['id']); ?></div>
				<!--<button class="not_interested_btn_small" name="addEvent"></button>-->
			</li>
			<?php } ?>
				<div class="clr"></div>
			</ul>
		
		</div><!--end categoryEventBlock_right-->
		
		
		
		
		<br class="clear" />
					
			<div style="margin-top:10px!important; float:right">
			
				<div class="prev_btn">
					<?php if ($pagenum > 1) { ?>
					<a href="javascript:showNextRecomeEvents2('<?php echo ABSOLUTE_PATH;?>','<?php echo $catid;?>','<?php echo $attr_name;?>','prev',<?php echo $pagenum;?>,<?php echo $total_rec;?>,'')">
					<?php } else { ?>
					<a href="javascript:void(0)">	
					<?php } ?>
						<img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" />
					</a>	
				</div>
				<div class="next_btn">
					<?php if ( $pagenum < $total_pages ) { ?>
					<a href="javascript:showNextRecomeEvents2('<?php echo ABSOLUTE_PATH;?>','<?php echo $catid;?>','<?php echo $attr_name;?>','next',<?php echo $pagenum;?>,<?php echo $total_rec;?>,'')">
					<?php } else { ?>
					<a href="javascript:void(0)">
					<?php } ?>
						<img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" />
					</a>	
				</div>
							
			</div><!--end next_prev-->
			
			<br class="clear" />
			
	</div> <!-- end rBox -->
		<div class="clr"></div>
		