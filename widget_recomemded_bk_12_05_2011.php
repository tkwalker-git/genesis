<div class="recommendedBlock">
		<div class="recommended_heading heading_dark_16">
			<div style="float:left" >Top recommended Event for <?php echo $name;?></div>
			<div style="float:right"><a style="color:#52BDE9; font-size:15px; " href="javascript:void(0)" onclick="showHowWeDetermine('<?php echo ABSOLUTE_PATH;?>')">How did we determine these events?</a></div>
		</div>	
		
		<ul class="recommend_ul">
		<?php
			$sql2 = "select *,(select event_date from event_dates where event_id=events.id LIMIT 1) as event_date1,(select event_date from event_dates where event_date>now() LIMIT 1) as event_date from events  order by event_date limit 4";
			$res2 = mysql_query($sql2); 
		  	while ($rows2 = mysql_fetch_assoc($res2) ) {
				$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),25);
				$full_name		= DBout($rows2['event_name']);
				$event_date 	= date("M d, Y",strtotime($rows2['event_date1']));
				$source			= $rows2['event_source'];
				$event_image	= getEventImage($rows2['event_image'],$source);
				$event_url		= getEventURL($rows2['id']);
		?>
			<li>
				<div class="heading_dark_14" style="width:180px; text-align:center"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></div>
				<div class="d_style" style="text-align:center; width:180px;"><?php echo $event_date;?></div>
				<div class="big_event_photo_block">
					<div class="big_event_photo_border">
						<!--<img src="<?php echo IMAGE_PATH;?>b1.png" height="200" width="163" border="0" />-->
						<!--<img src="http://static.eventful.com/images/large/I0-001/003/897/483-1.jpeg" width="163" height="163" border="0" >-->
						<a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a>
					</div><!--end big_event_photo_border-->
				</div><!--end big_event_photo_block-->
				<div class="add_to_event_btn_big" ><?php getAddToWallButton($rows2['id']); ?></div>
				<div class="star_rating" style="text-align:center"><?php getEventRatingAggregate($rows2['id']); ?></div>
				<!--<button class="not_interested_btn_big" name="addEvent"></button>-->
			</li>
			<?php } ?>
			<div class="clr"></div>
		</ul>
	
	</div><!--end recommendedBlock-->
	
	<?php
	
		$rsc = mysql_query("select * from categories ");
		while ( $rowc = mysql_fetch_assoc($rsc) ) {
			$cid		= $rowc['id'];
			$attr_name 	= DBout($rowc['name']);
			$attr_name	= ucwords($attr_name);
			$val = attribValue("member_prefrences","selection","where prefrence_type=" . $cid . " and member_id=" . $member_id );
			
			$event_ids = returnSubCatEventList($cid);
			
			if ( count($event_ids) > 0 ) {
				
				$recomended_list = implode(",",$event_ids);
				$recomended_list = ' AND id IN ('. $recomended_list .') ';
				
				$sql3 = "select id from events  where category_id='". $cid ."' ". $recomended_list ;
				$res3 = mysql_query($sql3); 
				$totr = mysql_num_rows($res3);
	?>
	
	<div id="recomended<?php echo $cid;?>" class="categoryEventBlock">
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
			
				<div class="prev_btn"><a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" /></a></div>
				<div class="next_btn">
					<?php if ( $totr > 4 ) { ?>
						<a href="javascript:showNextRecomeEvents('<?php echo ABSOLUTE_PATH;?>','<?php echo $cid;?>','<?php echo $attr_name;?>','next',1,<?php echo $totr;?>,'<?php echo $recomended_list;?>')">
							<img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" />
						</a>
					<?php } else { ?>
						<a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" /></a>
					<?php } ?>		
				</div>
							
			</div><!--end next_prev-->
			
		</div><!--end categoryEventBlock_left-->
		<div class="categoryEventBlock_right">
		
			<ul class="category_ul" >
			<?php 
		  		
			$sql2 = "select *,(select event_date from event_dates where event_id=events.id LIMIT 1) as event_date1,(select event_date from event_dates where event_date>now() LIMIT 1) as event_date from events  where category_id='". $cid ."' ". $recomended_list ." order by event_date limit 4";
			$res2 = mysql_query($sql2); 
		  	while ($rows2 = mysql_fetch_assoc($res2) ) {
				$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
				$full_name		= DBout($rows2['event_name']);
				$event_date 	= date("M d, Y",strtotime($rows2['event_date1']));
				$source			= $rows2['event_source'];
				$event_image	= getEventImage($rows2['event_image'],$source,1);
				$event_url		= getEventURL($rows2['id']);
				
			?>
			<li>
				<div class="heading_dark_12" style="width:140px; text-align:center"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></div>
				<div class="d_style_12" style="text-align:center; width:140px;"><?php echo $event_date;?></div>
				<div class="small_event_photo_block">
					<div class="small_event_photo_border">
						<a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a>
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
	</div><!--end categoryEventBlock-->

	<?php } } ?>