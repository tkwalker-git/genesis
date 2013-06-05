<?php
	
	// location infotmation
	$venue_info = $venue_attrib[1];
	
	// host id
	$event_host_id = attribValue("event_hosts","id","where event_id='". $event_id ."'");
	
	// 7263+Maple+Place+%23108,+Annandale,+VA+(J+B+Investigations)+@38.8317,-77.195886
	$raw_address = $venue_info['venue_address'] . '+' . $venue_info['venue_zip'] . '+' . $venue_info['venue_city'] . ',+' . $venue_info['venue_state'] . ' ('. $venue_info['venue_name'] .')+@' . $venue_info['venue_lat'].','.$venue_info['venue_lng'];
	
?>

<div id="locationMap">
		<div style="width:925px; margin:auto">	
			
			<div class="generalText_bold_16">Jeo diew grab event</div>
			<div class="awatarBlock"><img src="<?php echo IMAGE_PATH;?>user_awatar.png" height="253" width="211" border="0" /></div><!--end awatarBlock-->
			
			<div class="awatar_right" style="width:500px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr height="35">
					<td colspan="2" class="generalText_bold_16">Jane's Favorites</td>
				  </tr>
				   <tr height="35">
					<td class="heading_colored_14">Event Types:</td>
					<td>Lounges    |    Conferences    |    Live    |    Basketaball    |</td>
				  </tr>
				  <tr height="35">
					<td>&nbsp;</td>
					<td>Lounges    |    Conferences    |    Live    |    Basketaball    |</td>
				  </tr>
				  <tr height="35">
					<td>&nbsp;</td>
					<td>Lounges    |    Conferences    |    Live    |    Basketaball    |</td>
				  </tr>
				  <tr height="35">
					<td class="heading_colored_14">Event Types:</td>
					<td>R&B    |    Pop    |    Hip Hop    |    Reggae    |    House    |</td>
				  </tr>
				</table>

			</div><!--end awatar_right-->

		</div>	
			<div class="clr"></div>
		
		<?php getReviewsList($event_host_id,'host'); ?>
		
	
	
		<div class="recommendedBlock" style="border: 1px solid #C1C1C1;; margin-top:10px;">
			<div class="recommended_heading heading_dark_16">
				<span style="float:left; ">Top Recommended Events <?php echo $logged_in_member_name; ?></span>
				<div class="next_prev" style="margin-top:0px; float:right!important;">
					<!--<div class="prev_btn"><img src="<?php echo IMAGE_PATH;?>prev_btn.png" width="60" height="20" border="0" /></div>
					<div class="next_btn"><img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" /></div>-->
				</div><!--end next_prev-->
			</div>
			
			<ul class="category_ul" style="margin-left:20px">
				<?php
					$sql2 = "select * from events order by id DESC limit 5";
					$res2 = mysql_query($sql2); 
					while ($rows2 = mysql_fetch_assoc($res2) ) {
						$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),25);
						$full_name		= DBout($rows2['event_name']);
						$event_date 	= date("M d, Y",strtotime($rows2['event_date']));
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
	
	
	</div>
</div><!--end center_contents-->

