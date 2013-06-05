<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$active='location';


$event_id		= $_POST['event_id'];

$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);

if ( $row = mysql_fetch_assoc($res) ) {
	
	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
	$bc_alter			= $row['alter'];
	$bc_alter_url		= $row['alter_url'];
	
	}	
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
			list($width, $height, $type, $attr) = @getimagesize(ABSOLUTE_PATH . 'venue_images/' . $venue_info['image']);
			list($width, $height) = getPropSize($width, $height, 474,2000);
			$venue_img = '<img src="' . ABSOLUTE_PATH . 'venue_images/' . $venue_info['image'] . '" height="'. $height . '" width="' . $width . '"   />';
		} else {
			$img_params = returnImage($venue_info['image'],474,2000);
			$venue_img = '<img '. $img_params .' />'; 
		}	
	} else {
		$no_image = 1;
	}
	
	if ( $venue_cg_id != -1 )		
		include_once("../citygrid_location_details.php");
	else {
		$sqlImages = "select image from venue_images where venue_id='". $venue_info['id'] ."' LIMIT 4";
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
		});
	


</script>


        <?php include("../flayerMenu.php"); ?>
     <div class="inrDiv">
      <div class="event-name-big2"> <?php echo $venue_info['venue_name'];?></div>
      <br />
<div class="ev_directions">
		Directions: 
		<a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&daddr=<?php echo $raw_address;?>&iwstate1=dir:to">To here</a> 
					 - <a target="_blank" style="font-weight:normal;" href="http://www.google.com/maps?source=uds&saddr=<?php echo $raw_address;?>&iwstate1=dir:from">From here</a>
		</div>
		<div class="clear"></div>
	  <div class="new_flayer_gallry">
	  <div style="height: 317px; overflow: hidden;">
	  <?php
							if ( count($images) > 0 ) {
								// Big Image
								if ( $images[0]['width'] > 0 && $images[0]['height'] > 0 )
									list($viw, $vih) = getPropSize($images[0]['width'], $images[0]['height'], 453,2000);
								else {
									list($viw, $vih, $type33, $attr33) = getimagesize($images[0]['url']);
									list($viw, $vih) = getPropSize($viw, $vih, 453,2000);
								}
								
								if ( $no_image != 1 )
									echo $venue_img;
								else
									echo $big_image =  '<img src="'. $images[0]['url'] .'" width="'. $viw .'" height="'. $vih .'" />';
								
								}else {
								if ( $no_image == 1 ) {
									// Try Yelp for the image
									$venue_img = getLocationImageFromYelp($venue_info['venue_lat'],$venue_info['venue_lng']);
									if ( $venue_img != '' ) {
										mysql_query("update venues set image='". $venue_img ."' where id='". $venue_info['id'] ."'");
										list($width, $height, $type, $attr) = @getimagesize($venue_img);
										
										list($width, $height) = getPropSize($width, $height, 453,2000);
										
										$venue_img = '<img src="'. $venue_img.'" height="'. $height .'" width="'. $width .'" />';
									} else {
										$venue_img = getLocationImage($venue_info['venue_lat'],$venue_info['venue_lng']);
										$venue_img = '<img src="'. $venue_img.'" height="300" width="300" />';
									}
								} 
								
								echo str_replace("margin:10px 0px","",$venue_img);
							}	?>
							
				</div>
	  </div>
	  <div class="eventImagesGallery">
          <ul class="images">
            <?php
			if ( count($images) > 0 ) {
				$cu = 0;
				foreach ($images as $kv => $vimage) {
					$cu++;
					if ( $cu < 4 )
						$st = 'style="margin-right:3px!important"';
					else
						$st = 'style="margin-right:0px!important"';
					list($w1, $h1) = getPropSize($vimage['width'], $vimage['height'], 107,1000);
					//echo $small_image =  '<li '. $st .'><div style="height:106px; overflow:hidden"><a rel="venue_image_gallery" href="'. $vimage['url'] .'"><img src="'. $vimage['url'] .'" width="'. $w1 .'" height="'. $h1 .'" align="left" /></a></div></li>';
					echo $small_image =  '<li '. $st .'><div style="height:106px; overflow:hidden">
<a href="javascript:void(0)" onclick="showimage(\''.ABSOLUTE_PATH.'\',\''. $vimage['url'] .'\')" rel="venue_image_gallery"><img src="'. $vimage['url'] .'" width="'. $w1 .'" height="'. $h1 .'" align="left" /></a></div></li>';
				}
			}
			?>
            <div class="clr"></div>
          </ul>
          <ul>
            <li><strong class="new_blue">Address:</strong><br />
              <?php echo $display_address;?> </li>
            <li><strong class="new_blue">Phone:</strong><br />
              <?php
								if ( $venue_info['phone'] != '' )
									$formated_phone = format_phone($venue_info['phone']);
								else if ( $display_phone != '' )
									$formated_phone = $display_phone;
								else
									$formated_phone = 'N/A';	
									
									echo $formated_phone;
							?>
            </li>
            <li><strong class="new_blue">Neighborhood:</strong><br />
              <?php
			  if ( count($neighbors) > 0 ) 
					$neg = implode(", ", $neighbors);
				else
				$neg = $venue_info['neighbor'];
				
				echo $neg; ?>
            </li>
            <li><strong class="new_blue">Location Type:</strong><br />
              <?php echo ($venue_info['venue_type'] != '') ? $venue_info['venue_type'] : 'N/A';?></li>
          </ul>
          <div class="clr"></div>
        </div>

</div>
