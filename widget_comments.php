<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
	<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $event_id;?>,'comment')"><img src="<?php echo IMAGE_PATH; ?>write_comment.png" align="right" /></a>
	<?php
	
	$res = mysql_query("select * from `comment` where `c_type`='comment' && `key_id`='".$event_id."' order by `date_posted` DESC");
	$totalComments	= mysql_num_rows($res);
	$i=0;
	if(mysql_num_rows($res)){
		while($row = mysql_fetch_array($res)){	
			$i++;
			$by_user		= $row['by_user'];
			$comment		= DBout($row['comment']);
			$date_posted	= $row['date_posted'];
			
			$sqm = "select * from users where id='" . $by_user . "'";
			$resm = mysql_query($sqm);
			if ( $rowm = mysql_fetch_assoc($resm) ) {
				$mid			= $rowm['id'];
				$member_image 	= $rowm['image_name'];
				$member_name 	= $rowm['firstname'];
				if ($member_image != '' && file_exists(DOC_ROOT . 'images/members/' . $member_image ) ) {
						$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $member_image,125,150 );
						$member_image = '<img align="center" '. $img .' style="padding-bottom:5px"  />';
					} else
						$member_image = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="150" style="padding-bottom:5px" width="125" border="0" />';
			}
			
		?>
		<div style="min-height:100px; <?php if ($i!=$totalComments){ echo 'border-bottom:#D8D8D8 solid 1px;'; } ?> padding:10px 0">
			<div style="float:left; width:150px;">
				<div style="padding:0 25px 0 0" align="center">
					<?php echo $member_image; ?><br />
					<span class="heading_colored_14" style="text-decoration:underline;"><?php echo $member_name;?></span><br />
				</div>
			</div>
			<div style="float:left; width:700px;">
				<span style="color:#999999; font-weight:bold">
					<?php echo date('d M Y', strtotime($date_posted))." at ".date('h:i A', strtotime($date_posted)); ?>
				</span>
				<br /><br />
				<?php echo $comment; ?>
			</div>
		<div class="clr"></div>
		</div>
		<?php
		}
	}
	else{
		?>
		<div style="padding:30px; text-align:center; color:#990000"> No comment found for this event. Be the first to write a comment. <a onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $event_id;?>,'comment')" href="javascript:void(0)"> Click Here. </a> </div>
		<?php
	
	}
	?>
  </div>
  <!--end blockerRepeat-->
  <div class="blockerBottom"></div>
  <!--end blockerBottom-->
</div>
<!--end blocker-->