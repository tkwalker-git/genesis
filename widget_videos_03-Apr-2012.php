<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat" style="overflow:hidden">
    <?php
	$res = mysql_query("select * from `event_videos` where `event_id`='$event_id'");
	$numberOfVideos = mysql_num_rows($res);
	if($numberOfVideos){
	$i=0;
	while($row = mysql_fetch_array($res)){
		$i++;
		$video_embed = $row['video_embed'];
		$video_name = $row['video_name'];	
		if($video_embed){
		
		$w = explode("width=\"", $video_embed);
		$wd = explode("\"", $w[1]);
		$width = $wd[0];
		if($width==''){		
			$w = explode("width='", $video_embed);
			$wd = explode("'", $w[1]);
			$width = $wd[0];
			}
		?>
	    <div class="new_flayer_title">
			<?php if($video_name){ echo $video_name; }else { echo "Event Video:";}?><br /><br />
		</div>
		<div style="background:#E6E6E6; padding:15px; width:<?php echo $width; ?>px; margin:auto">
		<?php
			echo $video_embed;
			}else{
			echo '<div style="color: #FF0000;font-size: 14px;min-height: 200px;text-align: center;">No Video Available</div>';
			}?>
    </div>
	<?php
	if($i!=$numberOfVideos){?>
	<br><div class="clr" style="border-top: 1px solid #CCCCCC;"><br></div>
	<?php 
	}
	}}
	else{
	echo "<br /><br /><strong>No Video Found.</strong><br /><br /><br /><br />";
	}
	?>
    <!-- deals -->
  </div>
  <!--end blockerRepeat-->
  <div class="blockerBottom"></div>
  <!--end blockerBottom-->
</div>
<!--end blocker-->