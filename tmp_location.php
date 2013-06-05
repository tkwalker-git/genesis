<div class="recommendedBlock" style="border: 1px solid #C1C1C1;; margin-top:10px;">
		<div class="recommended_heading heading_dark_16">
			Venue Reviews
			<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $venue_info['id'];?>,'venue')">
				<img src="<?php echo IMAGE_PATH;?>write_review.png" width="141" height="30" align="right" style="float:right; margin-top:-8px; " border="0" />
			</a>	
		</div>
		<?php
			
			$sqlv = "select * from comment where key_id='".$venue_info['id'] . "' AND c_type='venue'";
				$resv = mysql_query($sqlv);
				$totv = mysql_num_rows($resv);
				
				if ($totv > 0 ) {
			
		?>
		<ul class="recommend_ul">
			
			<?php
				
				
					while ($rowv = mysql_fetch_assoc($resv) ) {
						$sqm = "select * from users where id='" . $rowv['by_user'] . "'";
						$resm = mysql_query($sqm);
						if ( $rowm = mysql_fetch_assoc($resm) ) {
							$mid			= $rowm['id'];
							$member_image 	= $rowm['image_name'];
							$member_name 	= $rowm['firstname'];
						}
						
						$cdate 		= date("M d, Y",strtotime($rowv['date_posted']));
						$comment	= DBout($rowv['comment']);
			?>
			
			<li style="width:865px; margin-left: 10px;  padding: 20px; border-bottom: 1px solid #d8d8d8;">
				<div class="eventList">
					<div>
						<img src="<?php echo IMAGE_PATH;?>members/<?php echo $member_image;?>"  border="0" /><br />
						<span class="heading_colored_14" style="text-decoration:underline;"><?php echo $member_name;?></span>
					</div>
				</div>
				<div class="eventList_dt">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td>
							<?php getVenuRating($venue_info['id'],$mid);?>
							&nbsp;<span class="heading_dark_14_bold" style="margin-left:20px;"><?php echo $cdate;?></span></td>
					  </tr>
					  <tr>
					  	<td class="heading_dark_14"  style="padding-top:10px;">
							<?php echo $comment;?>
						</td>
					  </tr>
					   <tr>
					  	<td class="heading_dark_14" valign="top"  style="padding-top:10px;">
							<?php
								
								$already = attribValue("review_helpfull","id","where review_id=" . $venue_info['id'] . " AND userid='". $member_id ."'");
								$said    = attribValue("review_helpfull","status","where review_id=" . $venue_info['id'] . " AND userid='". $member_id ."'");
								if ( $said == 0 )
									$sai = 'No';
								if ( $said == 1 )
									$sai = 'Yes';
								if ( $said == 2 )
									$sai = 'Inappropriate';		
								if ( $already > 0 ) {
									echo '<span class="heading_dark_16">Was this review helpfull? You said "'. $sai .'"</span>';
								} else {	
								
							?>
							<span class="heading_dark_16">Was this review helpfull?</span>
								<button onclick="reviewHelpFull('<?php echo $_SERVER['REQUEST_URI'];?>',<?php echo $venue_info['id'];?>,1)" class="jes_btn"></button>
								<button onclick="reviewHelpFull('<?php echo $_SERVER['REQUEST_URI'];?>',<?php echo $venue_info['id'];?>,0)" class="no_btn"></button>
								<button onclick="reviewHelpFull('<?php echo $_SERVER['REQUEST_URI'];?>',<?php echo $venue_info['id'];?>,2)"  class="inap_btn"></button>
							<?php } ?>	
						</td>
					  </tr>
					  <tr>
					  	<td class="heading_dark_14" valign="top"  style="padding-top:10px;">
							<?php
								$total_reviews1	= getSingleColumn('tot',"select count(*) as tot from review_helpfull where review_id=" . $venue_info['id']);
								$total_helpfull	= getSingleColumn('tot',"select count(*) as tot from review_helpfull where status=1 and review_id=" . $venue_info['id']);

								$total_helpfull_precent = ceil( ($total_helpfull/$total_reviews1) * 100);
								
							?>
							<?php echo $total_helpfull;?> out of <?php echo $total_reviews1;?> members found this review helpful
						</td>
					  </tr>
					  <tr>
					  	<td class="heading_dark_14" valign="top"  style="padding-top:15px;">
							<span class="d_style">Total Reviews:</span> <?php echo $total_reviews1;?>
						</td>
					  </tr>


					  <tr>
					  	<td class="heading_dark_14" valign="top">
							<span class="d_style">Helpfull Percent:</span> <?php echo $total_helpfull_precent;?>%
						</td>
					  </tr>
					</table>

				</div>
			</li>
			<?php }  } else { ?>
			<div style="padding:30px; text-align:center; color:#990000">
				No review found for this Venue. Be the first to write a review. 
				<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $venue_info['id'];?>,'venue')">
					Click Here.
				</a>
			</div>
			<?php } ?>
			<div class="clr"></div>
		</ul>
	
	</div><!--end recommendedBlock-->