<?php
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	// location infotmation
	$venue_info 		= $venue_attrib[1];
	$event_locations	= $venue_attrib[0];

	if ( substr($venue_info['source_id'],0,2) == 'CG' )
		$venue_cg_id 		= str_replace("CG-","",$venue_info['source_id']);
	else
		$venue_cg_id = -1;
	
	$raw_address = $venue_info['venue_address'] . '+' . $venue_info['venue_zip'] . '+' . $venue_info['venue_city'] . ',+' . $venue_info['venue_state'] . ' ('. $venue_info['venue_name'] .')+@' . $venue_info['venue_lat'].','.$venue_info['venue_lng'];
	
	$display_address = $venue_info['venue_address'] . ' ' . $venue_info['venue_city'] . ' ' . $venue_info['venue_state'] . ', '. $venue_info['venue_zip'];
	
	if ( $venue_info['image'] != '' ) {
		if ( substr($venue_info['image'],0,7) != 'http://' && substr($venue_info['image'],0,8) != 'https://' ) {
			list($width, $height, $type, $attr) = @getimagesize(ABSOLUTE_PATH . 'venue_images/th_' . $venue_info['image']);
			list($width, $height) = getPropSize($width, $height, 300,400);
			$venue_img = '<img src="' . ABSOLUTE_PATH . 'venue_images/th_' . $venue_info['image'] . '" height="'. $height . '" width="' . $width . '" style="margin:10px 0px"  />';
		} else {
			$img_params = returnImage($venue_info['image'],300,400);
			$venue_img = '<img style="margin:10px 0px"  '. $img_params .' />'; 
		}	
	} else {
		$no_image = 1;
	}
	
	if ( $venue_cg_id != -1 )		
		include_once("citygrid_location_details.php");
	else {
		$sqlImages = "select image from venue_images where venue_id='". $venue_info['id'] ."'";
		$resImages = mysql_query($sqlImages);
		$p=0;
		while ( $rowImages = mysql_fetch_assoc($resImages) ) {
			$iPath = ABSOLUTE_PATH . 'venue_images/' . $rowImages['image'];
			$images[$p]['url'] 		= $iPath;
			list($iwidth, $iheight, $type, $attr) = @getimagesize($iPath);
			$images[$p]['height'] 	= $iwidth;
			$images[$p]['width'] 	= $iheight;
			$p++;
		}
	}
				
?>
<script>
$(document).ready(function() {
	$("a[rel=venue_image_gallery]").fancybox({
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'titlePosition' 	: 'over',
			'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
				return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
			}
	});
	
	$("#venue_iframe1").fancybox({
				
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'type'				: 'iframe'
	});
	
	$("#venue_iframe2").fancybox({
				
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: 390,
		'height'			: 200,
		'type'				: 'iframe'
	});
	
	$("#venue_iframe3").fancybox({
				
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: 500,
		'height'			: 300,
		'type'				: 'iframe'
	});
	
	$("#venue_iframe4").fancybox({
				
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: '75%',
		'height'			: '75%',
		'type'				: 'iframe'
	});
	
	$("#venue_iframe5").fancybox({
				
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: 800,
		'height'			: 500,
		'type'				: 'iframe'
	});
	
	$("#venue_iframe6").fancybox({
				
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: 640,
		'height'			: 500,
		'type'				: 'iframe'
	});
	
});	
</script>
<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
<div id="locationMap">
  <div style="width:855px; margin:auto;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="float:none; clear:both">
      <tr>
        <td width="530" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td colspan="2" align="left"  ><table width="100%" cellpadding="5" cellspacing="0" >
                        <tr>
                          <td align="left" style="background-color:#FFF9D9"><span class="dark_gothic_style_18_big" style="color:#990000; font-size:24px"><?php echo $venue_info['venue_name'];?></span> <br />
                            <em><?php echo $display_address;?></em> </td>
                          <td align="center" style="background-color:#990000; color:#FFFFFF" width="135"><a id="venue_iframe6" href="<?php echo $reviews_url;?>#reviewsTab">
                            <?php getReviewStarsOnRating($overall_rating); ?>
                            </a>
                            <?php
									echo '<br>'.$reviews_count.' Review(s)';
								?>
                          </td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td width="320" align="left" valign="top"><?php
							if ( count($images) > 0 ) {
								// Big Image
								
								if ( $images[0]['width'] > 0 && $images[0]['height'] > 0 )
									list($viw, $vih) = getPropSize($images[0]['width'], $images[0]['height'], 300,400);
								else {
									list($viw, $vih, $type33, $attr33) = getimagesize($images[0]['url']);
									list($viw, $vih) = getPropSize($viw, $vih, 300,400);
								}
								
								
								if ( $no_image != 1 )
									echo $venue_img;
								else
									echo $big_image =  '<img style="margin:10px 0px" src="'. $images[0]['url'] .'" width="'. $viw .'" height="'. $vih .'" />';
									
								echo '<div style="clear:both"> </div>';
								foreach ($images as $kv => $vimage) {
									
									list($w1, $h1) = getPropSize($vimage['width'], $vimage['height'], 45,75);
									echo $small_image =  '<div style="width:45px; height:45px; overflow:hidden; float:left; margin-right:5px; position:relative"><a rel="venue_image_gallery" href="'. $vimage['url'] .'"><img style="margin-right:5px; margin-bottom:5px" src="'. $vimage['url'] .'" width="'. $w1 .'" height="'. $h1 .'" align="left" /></a></div>';
									
								}
							} else {
								if ( $no_image == 1 ) {
									// Try Yelp for the image
									$venue_img = getLocationImageFromYelp($venue_info['venue_lat'],$venue_info['venue_lng']);
									if ( $venue_img != '' ) {
										mysql_query("update venues set image='". $venue_img ."' where id='". $venue_info['id'] ."'");
										list($width, $height, $type, $attr) = @getimagesize($venue_img);
										list($width, $height) = getPropSize($width, $height, 300,400);
										$venue_img = '<img src="'. $venue_img.'" height="'. $height .'" width="'. $width .'" style="margin:10px 0px" />';
									} else {
										$venue_img = getLocationImage($venue_info['venue_lat'],$venue_info['venue_lng']);
										$venue_img = '<img src="'. $venue_img.'" height="300" width="300" style="margin:10px 0px" />';
									}
								} 
								
								echo $venue_img;
							}	
						?>
                    </td>
                    <td valign="top" style="padding-top:10px"><?php 
								if ( $venue_info['phone'] != '' )
									$formated_phone = format_phone($venue_info['phone']);
								else if ( $display_phone != '' )
									$formated_phone = $display_phone;
								else
									$formated_phone = 'N/A';	
							?>
                      <span class="heading_colored_14">Phone Number:</span><br />
                      <span style="font-size:14px"> <?php echo $formated_phone;?></span> <br>
                      <br>
					  
                      <?php 
							if ( count($neighbors) > 0 ) 
								$neg = implode(", ", $neighbors);
							else
								$neg = $venue_info['neighbor'];	
							
							if ( $neg != '' ) {	
							?>
                      <span class="heading_colored_14" >Neighborhood:</span><br />
                      <span style="font-size:14px"> <?php echo $neg; ?> </span> <br>
                      <br>
                      <?php } ?>
                      <span class="heading_colored_14" >Location Type:</span><br />
                      <span style="font-size:14px"><?php echo ($venue_info['venue_type'] != '') ? $venue_info['venue_type'] : 'N/A';?></span>
                      <?php if ( $venue_cg_id != -1 )	{ ?>
                      <br>
                      <br>
                      <a id="venue_iframe1" href="<?php echo $send_friend;?>"> <img src="<?php echo ABSOLUTE_PATH;?>images/email_friend.png" width="100" height="24" align="left" border="0" /> </a>
                      <?php if ( $video_url != '' ) { ?>
                      <a id="venue_iframe2" href="<?php echo $video_url;?>"> <img src="<?php echo ABSOLUTE_PATH;?>images/watch_video.png" width="100" height="24" align="left" style="margin-left:10px" /> </a>
                      <?php } else { ?>
                      <a href="javascript:alert('Video is not available for this venue.')"> <img src="<?php echo ABSOLUTE_PATH;?>images/watch_video.png" width="100" height="24" align="left" style="margin-left:10px" /> </a>
                      <?php } ?>
                      <br>
                      <br>
                      <?php if ( $email_url != '' ) { ?>
                      <a style="font-weight:normal; font-size:12px; text-decoration:underline; color:#0099FF" id="venue_iframe5" href="<?php echo $email_url;?>">Email</a>
                      <?php } else { ?>
                      <a style="font-weight:normal; font-size:12px; text-decoration:underline; color:#0099FF"  href="javascript:alert('Email is not available for this venue.')">Email</a>
                      <?php } ?>
                      -
                      <?php if ( $website_url != '' ) { ?>
                      <a style="font-weight:normal; font-size:12px; text-decoration:underline; color:#0099FF" id="venue_iframe4" href="<?php echo $website_url;?>">Website</a>
                      <?php } else { ?>
                      <a style="font-weight:normal; font-size:12px; text-decoration:underline; color:#0099FF"  href="javascript:alert('Website is not available for this venue.')">Website</a>
                      <?php } ?>
                      -
                      <?php if ( $resv_url != '' ) { ?>
                      <a style="font-weight:normal; font-size:12px; text-decoration:underline; color:#0099FF" id="venue_iframe3" href="<?php echo $resv_url;?>">Make Reservation</a>
                      <?php } else { ?>
                      <a style="font-weight:normal; font-size:12px; text-decoration:underline; color:#0099FF"  href="javascript:alert('Reservations are not available for this venue.')">Make Reservation</a>
                      <?php } ?>
                      <?php if ( count($offers) > 0 ) { ?>
                      <br>
                      <br>
                      <a href="#specialOffers"> <img src="<?php echo ABSOLUTE_PATH;?>images/special_offers.jpg" width="210" height="100" border="0" /> </a>
                      <?php } ?>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php if ($custom_message != '' ) { ?>
                  <tr>
                    <td colspan="2" align="left" valign="top"><span class="heading_colored_14" style="text-decoration:underline">Message from <?php echo $venue_info['venue_name'];?></span> <br>
                      <?php echo str_replace('\n','<br>',$custom_message);?>
                      <?php 
							if (count($bullets) > 0 ) {
							?>
                      <ul>
                        <?php foreach ($bullets as $bullet) { ?>
                        <li><?php echo $bullet;?></li>
                        <?php } ?>
                      </ul>
                      <?php
							}
						?>
                    </td>
                  </tr>
                  <?php } ?>
                </table></td>
            </tr>
          </table></td>
        <td style="text-align:center" valign="top"><div id="map22" class="map" style="margin-bottom:15px"></div>
          <br>
          <br>
          <span class="heading_colored_14">Get Directions:</span> <span style="font-size:14px"> <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&daddr=<?php echo $raw_address;?>&iwstate1=dir:to">To here</a> - <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&saddr=<?php echo $raw_address;?>&iwstate1=dir:from">From here</a> </span>
          <?php if ( $venue_cg_id != -1 )	{ ?>
          <br>
          <div style="text-align:center; margin-top:30px; font-weight:normal"> Provided by <a target="_blank" style="color:#0066FF" href="<?php echo $profile_url;?>"><em>Citysearch</em></a> </div>
          <?php } ?>
        </td>
      </tr>
    </table>
  </div>
  <div class="clr"></div>
  <?php 
	
			getReviewsList($venue_info['id'],'venue',$reviews); 
			
			if ( count($offers) > 0 ) {
		?>
  <div class="clr"></div>
  <div id="specialOffers" class="recommendedBlock" style="border: 1px solid #C1C1C1; margin-top:10px; width:854px">
    <div class="recommended_heading heading_dark_16">Special Offers</div>
    <ul class="recommend_ul">
      <?php 
					foreach ( $offers as $offer ) {
				?>
      <li style="width:880px; margin-left: 10px;  padding: 10px ; border: 1px solid #d8d8d8; background-color:#EEEEEE"> <strong><?php echo $offer['text'];?></strong><br>
        <?php echo $offer['descr'];?> <a style="font-weight:normal; font-size:11px; color:#0066FF" target="_blank" href="<?php echo $offer['url'];?>">See Details</a> </li>
      <?php } ?>
      <div class="clr"></div>
    </ul>
  </div>
  <?php 
			} 
		 
				$venue_id = $venue_info['id'];
				
				$venue_event_q = "select event_id from venue_events where venue_id = '$venue_id' and event_id != '$event_id'";
				$venue_event_res = mysql_query($venue_event_q) ;
				$venue_event_total = mysql_num_rows($venue_event_res);
				
				if($venue_event_total > 0){
					//echo $venue_event_total;
					$i=0;
			?>
  <span id="locationeventscontainer">
  <div class="recommendedBlock" style="border: 1px solid #C1C1C1;; margin-top:10px; width:854px;">
    <div class="recommended_heading heading_dark_16"> <span style="float:left;">Other Events at <?php echo $venue_info['venue_name'];?></span>
      <div class="next_prev" style="margin-top:0px; float:right;">
        <?php if($venue_event_total > 5){ ?>
        <div class="prev_btn"> <a href="javascript:void(0)"> <img src="<?php echo IMAGE_PATH;?>prev_disabled.png" width="60" height="20" border="0" /> </a> </div>
        <div class="next_btn"> <a href="javascript:loadNextRecEvent('<?php echo ABSOLUTE_PATH;?>','<?php echo $venue_id;?>','<?php echo $event_id;?>','next',1)"> <img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" /> </a> </div>
        <?php } ?>
        <div class="clr"></div>
      </div>
      <!--end next_prev-->
    </div>
    <ul class="category_ul">
      <?php 
			
			while($venue_event_r = mysql_fetch_assoc($venue_event_res)){
				$eventId = 	$venue_event_r['event_id'];
				$i++;
				if($i > 5){
					break;
				}
		 
				$sql2 = "select * from events where id = '$eventId' AND is_expiring=1  order by id DESC limit 5";
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
            <div style="overflow:hidden; width:127px; height:160px"> <a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a> </div>
          </div>
          <!--end big_event_photo_border-->
        </div>
        <!--end big_event_photo_block-->
        <div class="add_to_event_btn_small">
          <?php getAddToWallButton($rows2['id'],1); ?>
        </div>
        <div class="star_rating" style="text-align:center">
          <?php getEventRatingAggregate($rows2['id']); ?>
        </div>
      </li>
      <?php 
				} 
			 } 
			?>
      <div class="clr"></div>
    </ul>
  </div>
  <!--end recommendedBlock-->
  <?php }  ?>
</div>
</span>
</div>
					</div> <!--end blockerRepeat-->
					<div class="blockerBottom"></div> <!--end blockerBottom-->
				</div> <!--end blocker-->
<!--end center_contents-->
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
							'<?php echo str_replace("'","&#039;",str_replace("\"","&quot;",$event_locations));?></div>' +
							'<br clear="all" /><br>'+
							'Get Directions: <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&daddr=<?php echo str_replace("'","&#039;",str_replace("\"","&quot;",$raw_address));?>&iwstate1=dir:to">To here</a>'+
							' - <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&saddr=<?php echo str_replace("'","&#039;",str_replace("\"","&quot;",$raw_address));?>&iwstate1=dir:from">From here</a>'+
							'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		var marker = new google.maps.Marker({
			  position: VenueLocation, 
			  map: map,
			   title:"<?php echo str_replace("'","&#039;",str_replace("\"","&quot;",$event_locations));?>"
		});
		
		google.maps.event.addListener(marker, 'mouseover', function() {
		  infowindow.open(map,marker);
		});
		
	}
	window.onload=initialize;
	
</script>
