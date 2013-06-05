<div class="recommendedBlock">
		<div class="recommended_heading heading_dark_16">
			<div style="float:left" >Top recommended Event for <?php echo $name;?></div>
			<div style="float:right"><a style="color:#52BDE9; font-size:15px; " href="javascript:void(0)" onclick="showHowWeDetermine('<?php echo ABSOLUTE_PATH;?>')">How did we determine these events?</a></div>
		</div>	
		
		<ul class="recommend_ul">
		<?php
			
			
			
			$sql2 = makeRecommendedQueryExtended("","O");
				
			$res2 = mysql_query($sql2); 
			
			if ( mysql_num_rows($res2) < 4 ) 
				$sql3 = " UNION " . makeRecommendedQueryExtended("","S");
					
			
			$sql2 = $sql2 . $sql3 . " ORDER BY `id` DESC LIMIT 4";
			
			$res2 = mysql_query($sql2); 
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
			<?php } ?>
			<div class="clr"></div>
		</ul>
	
	</div><!--end recommendedBlock-->
	
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
	
	<div id="recomended<?php echo $cid;?>" class="categoryEventBlock">
		<div class="recommended_heading heading_dark_16"><span class="heading_colored_16"><?php echo $attr_name;?></span> <span class="heading_dark_16">Events</span></div>
		
		<div class="categoryEventBlock_left">
			<div style="min-height:230px">
				<span class="heading_dark_14_bold">These event are based on your preferences in:</span>
				<br /><br />
				<?php
					$sq3 = "select prefrence_type from member_prefrences where member_id='". $member_id ."' AND selection IN ('O','S') AND prefrence_type IN (select id from sub_categories where categoryid=". $cid .") ORDER BY selection";
					$rs3 = mysql_query($sq3); 
					echo '<span style="display:block;"><strong><a href="event_preference_setting.php" style="color:#54BCEB; font-weight:bold" >Categories</a></strong><br>';
					while ( $ro3 = mysql_fetch_assoc($rs3) ) {
						$sbname  = attribValue("sub_categories","name","where id=" . $ro3['prefrence_type']  );
						$sbsname = attribValue("sub_categories","seo_name","where id=" . $ro3['prefrence_type']  );
						//$chref 	= ABSOLUTE_PATH . 'category/' . $cseoname . '/' . $sbsname . '.html' ;
						//echo '<span style="display:block;"><a href="'. $chref .'" style="color:#54BCEB; font-weight:bold" >'. $sbname . '</a></span>';
						echo '<font style="color:#333333; " >'. $sbname . '</font>, ';
					}
					
					$sq4 = "select music_genre from member_music_pref where member_id='". $member_id ."' AND selection IN ('O','S') ORDER BY selection";
					$rs4 = mysql_query($sq4); 
					if ( mysql_num_rows($rs4) > 0 ) {
						echo '<br /><br /><span style="display:block;"><strong><a href="music_preference_setting.php" style="color:#54BCEB; font-weight:bold" >Music Genre</a></strong><br>';
						while ( $ro4 = mysql_fetch_assoc($rs4) ) {
							$sbname  = attribValue("music","name","where id=" . $ro4['music_genre']  );
							echo '<font style="color:#333333;" >'. $sbname . '</font>, ';
						}
						echo '</span><br />';
					}
					
					$sq5 = "select age_id from member_age_pref  where member_id='". $member_id ."' AND selection IN ('O','S') ORDER BY selection";
					$rs5 = mysql_query($sq5); 
					if ( mysql_num_rows($rs5) > 0 ) {
						echo '<span style="display:block;"><strong><a href="age_preference_setting.php" style="color:#54BCEB; font-weight:bold" >Age Groups</a></strong><br>';
						while ( $ro5 = mysql_fetch_assoc($rs5) ) {
							$sbname  = attribValue("age","name","where id=" . $ro5['age_id']  );
							echo '<font style="color:#333333; " >'. $sbname . '</font>, ';
						}
						echo '</span><br />';
					}						
				?>
			</div>			
			<div class="next_prev" style="margin-top:20px!important">
			
				<div class="prev_btn"><a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" /></a></div>
				<div class="next_btn">
					<?php if ( $totr > 4 ) { ?>
						<a href="javascript:showNextRecomeEvents('<?php echo ABSOLUTE_PATH;?>','<?php echo $cid;?>','<?php echo $attr_name;?>','next',1,<?php echo $totr;?>,'')">
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
		  		
			$sql2 = $recQuery . " ORDER BY `id` DESC LIMIT 4";
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
				<!--<button class="not_interested_btn_small" name="addEvent"></button>-->
			</li>
			<?php } ?>
				<div class="clr"></div>
			</ul>
		
		</div><!--end categoryEventBlock_right-->
		
		<div class="clr"></div>
	</div><!--end categoryEventBlock-->

	<?php } } ?>