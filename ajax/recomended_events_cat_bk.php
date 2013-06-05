<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$attr_name		= $_POST['title'];
	
	$catid		 	= $_POST['category_id'];
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	$total_rec		= $_POST['tot'];
	$recomended_list= $_POST['list'];
	
	$total_pages 	= ceil($total_rec/4);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * 4 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 4 ; 
	}
			
	$limit = ' LIMIT '. $start . ' , 4';

?>

<div class="recommended_heading heading_dark_16"><span class="heading_colored_16"><?php echo $attr_name;?></span> <span class="heading_dark_16">Events</span></div>
		
		<div class="categoryEventBlock_left">
			<div style="min-height:230px">
				<span class="heading_dark_14_bold">These Event are based on your preferances in:</span>
				<br /><br />
				<?php 
					echo '<span style="display:block;"><a href="" class="heading_colored_14" >'. $attr_name . ' : '. $value .'</a></span><br />';
				?>
			</div>			
			<div class="next_prev" style="margin-top:20px!important">
			
				<div class="prev_btn">
					<?php if ($pagenum > 1) { ?>
					<a href="javascript:showNextRecomeEvents('<?php echo ABSOLUTE_PATH;?>','<?php echo $catid;?>','<?php echo $attr_name;?>','prev',<?php echo $pagenum;?>,<?php echo $total_rec;?>,'<?php echo $recomended_list;?>')">
					<?php } else { ?>
					<a href="javascript:void(0)">	
					<?php } ?>
						<img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" />
					</a>	
				</div>
				<div class="next_btn">
					<?php if ( $pagenum < $total_pages ) { ?>
					<a href="javascript:showNextRecomeEvents('<?php echo ABSOLUTE_PATH;?>','<?php echo $catid;?>','<?php echo $attr_name;?>','next',<?php echo $pagenum;?>,<?php echo $total_rec;?>,'<?php echo $recomended_list;?>')">
					<?php } else { ?>
					<a href="javascript:void(0)">	
					<?php } ?>
						<img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" />
					</a>	
				</div>
							
			</div><!--end next_prev-->
			
		</div><!--end categoryEventBlock_left-->
		<div class="categoryEventBlock_right">
		
			<ul class="category_ul" >
			<?php 
		  		
			$sql2 = "select *,(select event_date from event_dates where event_id=events.id ORDER by event_date ASC LIMIT 1) as event_date from events  where category_id='". $catid ."' ". $recomended_list ." AND (select event_date from event_dates where event_id=events.id ORDER by event_date DESC LIMIT 1) > DATE_SUB(CURDATE(),INTERVAL 1 DAY) ORDER by event_source, event_date " . $limit;
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
		
		<div class="clr"></div>