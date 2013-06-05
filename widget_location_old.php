<?php
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	
	
	// location infotmation
	$venue_info 		= $venue_attrib[1];
	$event_locations	= $venue_attrib[0];
	
	
	// 7263+Maple+Place+%23108,+Annandale,+VA+(J+B+Investigations)+@38.8317,-77.195886
	$raw_address = $venue_info['venue_address'] . '+' . $venue_info['venue_zip'] . '+' . $venue_info['venue_city'] . ',+' . $venue_info['venue_state'] . ' ('. $venue_info['venue_name'] .')+@' . $venue_info['venue_lat'].','.$venue_info['venue_lng'];
	
	if ( $venue_info['image'] == '' ) {
		$venue_img = getLocationImage($venue_info['venue_lat'],$venue_info['venue_lng']);
		$venue_img = '<img src="'. $venue_img.'" height="150" width="290" style="margin:20px 0px" />';
	} else {
		if ( substr($venue_info['image'],0,7) != 'http://' && substr($venue_info['image'],0,8) != 'https://' ) {
			list($width, $height, $type, $attr) = @getimagesize(ABSOLUTE_PATH . 'venue_images/th_' . $venue_info['image']);
			list($width, $height) = getPropSize($width, $height, 290,400);
			$venue_img = '<img src="' . ABSOLUTE_PATH . 'venue_images/th_' . $venue_info['image'] . '" height="'. $height . '" width="' . $width . '" style="margin:20px 0px"  />';
		} else {
			$img_params = returnImage($venue_info['image'],290,400);
			$venue_img = '<img style="margin:20px 0px"  '. $img_params .' />'; 
		}	
	}
			
		
?>

<div id="locationMap">
		<div style="width:925px; margin:auto">	
			<div class="locationDetail" style="width:400px;">
				
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr><td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="300" align="left" class="generalText_bold_16" valign="bottom">
							<?php echo $venue_info['venue_name'];?>
							<br />
							<?php echo $venue_img;?>
						</td>
						<td valign="bottom"> &nbsp;<!--Add Image--> </td>
					</tr>
					</table>	
				</td></tr>
				
				<tr><td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="162" valign="top">
							&nbsp;<br>
							<span class="heading_colored_14" >Address:</span>
							<br>
							<?php echo $event_locations;?>
							<?php if ( $venue_info['phone'] != '' ) { ?>
							<br>
							<span class="heading_colored_14" >Phone:</span>
							<br>
							<?php echo $venue_info['phone'];?>
							<?php } ?>
						</td>
						<td width="238" valign="top">
							&nbsp;<br>
							
							<span class="heading_colored_14" style="width:110px; display:block;float:left">Neighborhood:</span> <span style="display:block; float:left; font-size:14px"> <?php echo $venue_info['neighbor'];?></span>
							<br><br>
							<span class="heading_colored_14" style="width:110px; display:block;float:left">Location Type:</span> <span style="display:block; float:left; font-size:14px"><?php echo $venue_info['venue_type'];?></span>
							<br><br>
							<span class="heading_colored_14" style="width:110px; display:block;float:left">Get Directions:</span> 
				
							<span style="display:block; float:left; font-size:14px">
								<a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&daddr=<?php echo $raw_address;?>&iwstate1=dir:to">To here</a> 
								 - <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&saddr=<?php echo $raw_address;?>&iwstate1=dir:from">From here</a>
							</span>
							
						</td>
					</tr>
					</table>	
				</td></tr>
				</table>

			</div><!--end locationDetail-->
			<div id="map22" class="map"></div>
		</div>	
			<div class="clr"></div>
		
		
		<?php getReviewsList($venue_info['id'],'venue'); ?>
		
			<?php 
				$venue_id = $venue_info['id'];
				
				$venue_event_q = "select event_id from venue_events where venue_id = '$venue_id' and event_id != '$event_id'";
				$venue_event_res = mysql_query($venue_event_q) or die("Error venue event");
				$venue_event_total = mysql_num_rows($venue_event_res);
				
				if($venue_event_total > 0){
					//echo $venue_event_total;
					$i=0;
			?>
		<span id="locationeventscontainer">
		<div class="recommendedBlock" style="border: 1px solid #C1C1C1;; margin-top:10px;">
		<div class="recommended_heading heading_dark_16">
			<span style="float:left;">Other Events at <?php echo $venue_info['venue_name'];?></span>
			<div class="next_prev" style="margin-top:0px; float:right;">
			  <?php if($venue_event_total > 5){ ?>
				<div class="prev_btn">
					<a href="javascript:void(0)">
						<img src="<?php echo IMAGE_PATH;?>prev_disabled.png" width="60" height="20" border="0" />
					</a>
				</div>
				<div class="next_btn">
					<a href="javascript:loadNextRecEvent('<?php echo ABSOLUTE_PATH;?>','<?php echo $venue_id;?>','<?php echo $event_id;?>','next',1)">
						<img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" />
					</a>	
				</div>
				<?php } ?>
				<div class="clr"></div>
			</div><!--end next_prev-->
		</div>
		
		<ul class="category_ul">
		<?php 
			
			while($venue_event_r = mysql_fetch_assoc($venue_event_res)){
				$eventId = 	$venue_event_r['event_id'];
				$i++;
				if($i > 5){
					break;
				}
		 
				$sql2 = "select *,(select event_date from event_dates where event_date>DATE_SUB(CURDATE(),INTERVAL 1 DAY) LIMIT 1) as event_date from events where id = '$eventId' AND (select event_date from event_dates where event_id=events.id ORDER by event_date DESC LIMIT 1) > DATE_SUB(CURDATE(),INTERVAL 1 DAY)  order by event_date limit 5";
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
			<?php 
				} 
			 } 
			?>
		<div class="clr"></div>
		</ul>
	
	</div><!--end recommendedBlock-->
	
	<?php }  ?>
	</div>
	</span>
</div><!--end center_contents-->

<link href="http://code.google.com/apis/maps/documentation/javascript/examples/standard.css" rel="stylesheet" type="text/css" />
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
	function initialize() {
		var VenueLocation = new google.maps.LatLng(<?php echo $venue_info['venue_lat'];?>, <?php echo $venue_info['venue_lng'];?>);
		var panoramaOptions = {
			center: VenueLocation,
			zoom:15,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		var map = new google.maps.Map(document.getElementById("map22"), panoramaOptions);
		
		var contentString = '<div style="color:#000;width:300px;height:150px;">'+
							'<div><strong>Address</strong></div><br />'+
							'<div style="width:270px;float:left;">'+
							//'<img src="<?php echo $venue_img;?>" height="60" width="120" align="left" style="margin-right:10px" />' +
							'<?php echo $event_locations;?></div>' +
							'<br clear="all" /><br>'+
							'Get Directions: <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&daddr=<?php echo $raw_address;?>&iwstate1=dir:to">To here</a>'+
							' - <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&saddr=<?php echo $raw_address;?>&iwstate1=dir:from">From here</a>'+
							'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		var marker = new google.maps.Marker({
			  position: VenueLocation, 
			  map: map,
			   title:"<?php echo $event_locations;?>"
		});
		
		google.maps.event.addListener(marker, 'mouseover', function() {
		  infowindow.open(map,marker);
		});
		
	}
	window.onload=initialize;
	
</script>
