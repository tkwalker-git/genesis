<div>
	
		<div class="head_new"><a href="javascript:void(0)" onclick="showHowWeDetermine('<?php echo ABSOLUTE_PATH;?>')"><img src="<?php echo IMAGE_PATH; ?>how_did.png" align="right" /></a>RECOMMENDED FEATURED EVENTS</div>

<div class="recBox">
		<div class="rBox">		
		
		<ul class="recommend_ul">
		<?php
			
			$sql = getTopRecomemdedFeaturedEvents();
			$sql2 = $sql . " ORDER BY `id` DESC LIMIT 4";
			$res2 = mysql_query($sql2); 
			if(mysql_num_rows($res2)){
		  	while ($rows2 = mysql_fetch_assoc($res2) ) {
				$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),25);
				$full_name		= DBout($rows2['event_name']);
				$event_date 	= getEventStartDates($rows2['id']);
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
						<div style="overflow:hidden; width:163px; height:200px"><a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a></div>
					</div><!--end big_event_photo_border-->
				</div><!--end big_event_photo_block-->
				<div class="add_to_event_btn_big" ><?php getAddToWallButton($rows2['id']); ?></div>
				<div class="star_rating" style="text-align:center"><?php getEventRatingAggregate($rows2['id']); ?></div>
				<!--<button class="not_interested_btn_big" name="addEvent"></button>-->
			</li>
			<?php }}
			else{
			?>
			<div style="font-size:15px; min-height:40px;" align="center"><strong>No Record Found</strong></div>
			<?php
			} ?>
			<div class="clr"></div>
			<br class="clear" />
		</ul>
	<br class="clear" />
	</div><!--end recommendedBlock-->
	</div><br class="clear" />
	<div class="head_new">OTHER RECOMMENDED EVENTS</div>
	<?php
	
		$rsc = mysql_query("select * from categories where id IN ( select categoryid from sub_categories where id IN (select prefrence_type from member_prefrences where member_id='". $member_id ."' AND selection IN ('O','S')) ) ");
		while ( $rowc = mysql_fetch_assoc($rsc) ) {
			$cid		= $rowc['id'];
			$attr_name 	= DBout($rowc['name']);
			$attr_name	= ucwords($attr_name);
			$cseoname	= $rowc['seo_name'];
			$val = attribValue("member_prefrences","selection","where prefrence_type=" . $cid . " and member_id=" . $member_id );
			
			$recQuery = makeRecommendedQueryExtended($cid,"O") . " UNION " . makeRecommendedQueryExtended($cid,"S");
			
			$res3 = mysql_query($recQuery); 
			$totr = mysql_num_rows($res3);
			
			if ( $totr > 0 ) {
				
			$_SESSION['REC_QUERY'][$cid] = $recQuery;
	?>
	
	
	<div id="recomended<?php echo $cid;?>" class="recBox">
		<div class="yellow_bar"><?php echo $attr_name;?> &nbsp; <a href="/settings.php?p=event-preferences">Edit Preferences</a></div>
		<div class="rBox">
		<div class="categoryEventBlock_right">
		
			<ul class="category_ul" >
			<?php 
		  		
			$sql2 = $recQuery . " ORDER BY `id` ASC LIMIT 0, 5";
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
					<div class="small_event_photo_border" >
						<div style="overflow:hidden; width:127px; height:160px">
							<a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a></div>
					</div><!--end big_event_photo_border-->
				</div><!--end big_event_photo_block-->
				<div class="add_to_event_btn_small" name="addEvent"><?php getAddToWallButton($rows2['id'],1); ?></div>
				<div class="star_rating" style="text-align:center"><?php getEventRatingAggregate($rows2['id']); ?></div>
				<!-- <button class="not_interested_btn_small" name="addEvent"></button> -->
			</li>
			<?php } ?>
				<div class="clr"></div>
			</ul>
		
		</div><!--end categoryEventBlock_right-->
		
		<div class="clr"></div>
		<div style="margin-top:10px!important; float:right">
			
				<div class="prev_btn"><a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" /></a></div>
				<div class="next_btn">
					<?php if ( $totr > 5 ) { ?>
						<a href="javascript:showNextRecomeEvents2('<?php echo ABSOLUTE_PATH;?>','<?php echo $cid;?>','<?php echo $attr_name;?>','next',1,<?php echo $totr;?>,'')">
							<img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" />
						</a>
					<?php } else { ?>
						<a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" /></a>
					<?php } ?>		
				</div>
							
			</div><!--end next_prev--><br class="clr" />
	</div><!--end categoryEventBlock-->
</div>
	<?php } } ?>
	</div>