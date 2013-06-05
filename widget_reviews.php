<?php
	
	if ( $_GET['attened'] == 'n' && $_GET['event_id'] > 0  ) {
		mysql_query("update event_wall set attended=0 where userid=$member_id and event_id=" . $_GET['event_id']);
		echo "<script>window.location.href='myeventwall.php?type=reviews';</script>";	
	}
	
	$sqle = "select * from events e where id IN ( select event_id from event_wall where userid=". $member_id ." and attended=1 ) and (select event_date from event_dates where event_id=e.id order by event_date ASC LIMIT 1) < '". date("Y-m-d") ."' AND id NOT IN (select key_id from comment where by_user=". $member_id ." and c_type='event') ";
	$rese = mysql_query($sqle);
	$i=0;
	$tot1 = mysql_num_rows($rese);
	
	if ( $tot1 > 0 ) {
	
?>
		<div class="categoryEventBlock">
		<div class="recommended_heading">Rate Events You Attended:</span></div>
		
		<div class="GeneralBlock">
			
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="120" class="heading_colored_16" align="center">Event Date</td>
				<td class="heading_colored_16">Event Name</td>
				<td width="140" class="heading_colored_16">Location</td>
				<td width="120" class="heading_colored_16">Rate Event</td>
				<td width="240" colspan="2" class="heading_colored_16" align="center">Provide Your Feedback</td>
			  </tr>
			  <tr>
				<td colspan="6"><hr /></td>
			  </tr>
			  <?php
			  
				
				while ($evet = mysql_fetch_assoc($rese) ) {
					$i++;
					$event_id = $evet['id'];
					if ( ($i%2) == 0 )
						$cls = 'd_c_1';
					else
						$cls = 'd_c_1';	
					
					if ( $i == $tot1)
						$td_class="";
					else
						$td_class="td_border";
					
					$event_sdate 	= getEventStartDates($event_id);
					$event_url		= getEventURL($event_id);
					$event_name		= DBout($evet['event_name']);
					$venue_attrib	= getEventLocations($event_id);
			  ?>
			  <tr height="40" class="<?php echo $cls;?>">
				<td width="120" align="center" class="heading_dark_14_bold <?php echo $td_class;?>"><?php echo $event_sdate;?></td>
				<td style="padding-right:10px" class="heading_dark_14_bold <?php echo $td_class;?>"><a href="<?php echo $event_url;?>"><?php echo $event_name;?></a></td>
				<td width="140" class="heading_dark_14_bold <?php echo $td_class;?>" ><?php echo $venue_attrib[1]['venue_name'];?></td>
				<td width="120" class="heading_dark_14_bold <?php echo $td_class;?>">
					<?php getEventRatingMember($event_id);?>
				</td>
				<td width="120" class="<?php echo $td_class;?>">
					<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $event_id;?>,'event')">
						<img src="<?php echo IMAGE_PATH;?>write_review_btn.png" width="117" height="23" border="0" />
					</a>	
				</td>
				<td width="120" class="<?php echo $td_class;?>">
					<a href="?type=reviews&event_id=<?php echo $event_id;?>&attened=n" >
						<img src="<?php echo IMAGE_PATH;?>not_attend.png" width="117" height="23" />
					</a>	
				</td>
			  </tr>
			  <?php  } ?>
			</table>
			
		
		</div><!--end GeneralBlock-->
		

		
	<div class="clr"></div>
	</div><!--end categoryEventBlock-->
	<?php } ?>
	
	<div class="categoryEventBlock">
		<div class="recommended_heading">Past Reviews and Ratings:</span></div>
		
		
		<div class="GeneralBlock">
		
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  
			  <tr>
				<td width="120" class="heading_colored_16" align="center">Event Date</td>
				<td class="heading_colored_16">Event Name</td>
				<td width="140" class="heading_colored_16">Location</td>
				<td width="120" class="heading_colored_16">Rating</td>
				<td width="240" class="heading_colored_16" >Feedback Given</td>
			  </tr>
			  <tr>
				<td colspan="6"><hr /></td>
			  </tr>
			  <?php
			  
				$sqle = "select * from events e where id IN ( select key_id from comment where by_user=". $member_id ." and c_type='event' ) ";
				$rese = mysql_query($sqle);
				$i=0;
				$tot1 = mysql_num_rows($rese);
				while ($evet = mysql_fetch_assoc($rese) ) {
					$i++;
					$event_id = $evet['id'];
					if ( ($i%2) == 0 )
						$cls = 'd_c_1';
					else
						$cls = 'd_c_1';	
					
					if ( $i == $tot1)
						$td_class="";
					else
						$td_class="td_border";
					
					$event_sdate 	= getEventStartDates($event_id);
					$event_url		= getEventURL($event_id);
					$event_name		= DBout($evet['event_name']);
					$venue_attrib	= getEventLocations($event_id);
			  ?>
			  <tr height="40" class="<?php echo $cls;?>">
				<td width="120" align="center" class="heading_dark_14_bold <?php echo $td_class;?>"><?php echo $event_sdate;?></td>
				<td style="padding-right:10px" class="heading_dark_14_bold <?php echo $td_class;?>"><a href="<?php echo $event_url;?>"><?php echo $event_name;?></a></td>
				<td width="140" class="heading_dark_14_bold <?php echo $td_class;?>" ><?php echo $venue_attrib[1]['venue_name'];?></td>
				<td width="120" class="heading_dark_14_bold <?php echo $td_class;?>">
					<?php getEventRatingMember($event_id);?>
				</td>
				<td width="240" class="<?php echo $td_class;?>">
					<?php echo getEventReviewMember($event_id);?>
				</td>
			  </tr>
			  <?php  } ?>
			  
			</table>
			
		
		</div><!--end GeneralBlock-->
		

		
	<div class="clr"></div>
	</div><!--end categoryEventBlock-->
