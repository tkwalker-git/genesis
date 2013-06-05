<div id="deals">
	<div class="deal_round">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		 <tr>
			<td align="left" height="350" valign="top" style="padding:20px; font-size:13px" >
				<?php echo $event_description;?>
			</td>
		</tr></table>		
	</div>
</div><!--end center_contents-->

<div class="clr"></div>

<div class="recommendedBlock" style="border: 1px solid #C1C1C1;; margin-top:10px;">
			<div class="recommended_heading heading_dark_16">
				<span style="float:left; ">Events Similar to <?php echo $event_name; ?></span>
				<div class="next_prev" style="margin-top:0px; float:right!important;">
					<!--<div class="prev_btn"><img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" /></div>
					<div class="next_btn"><img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" /></div>-->
				</div><!--end next_prev-->
			</div>
			
			<ul class="category_ul" style="margin-left:20px">
				<?php
					$sql2 = "select * from events where subcategory_id='$subcategory_id' and id != '$c_event_id' AND is_expiring=1 order by id DESC limit 5";
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
					<div class="add_to_event_btn_small"><?php getAddToWallButton($rows2['id'],1); ?></div>
					<div class="star_rating" style="text-align:center"><?php getEventRatingAggregate($rows2['id']); ?></div>
				</li>
				<?php } ?>
			<div class="clr"></div>
			</ul>
		
		</div><!--end recommendedBlock-->