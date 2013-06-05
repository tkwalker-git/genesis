<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$active='location';


$event_id		= $_POST['event_id'];

$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);
if ($row = mysql_fetch_assoc($res)){	
	$bc_location_name	= $row['location_name'];
	$bc_address			= $row['address'];
	$bc_city			= $row['city'];
	$bc_zip				= $row['zip'];
	$bc_location_img	= $row['location_img'];
}		
?>


        <?php include("../flayerMenuFB.php"); ?>
     <div class="inrDiv">
      <div class="event-name-big2"> <?php echo $bc_location_name;?></div>
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
		  	if($bc_location_img){
				echo "<img src='".IMAGE_PATH.$bc_location_img."'>";
			}
			else{
				echo "<img src='". IMAGE_PATH ."no_venue_image1.png' width='460' height='318'>";
				}
		  ?>
          
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
<a href="javascript:void(0)" onclick="showimage(\'\/\',\''. $vimage['url'] .'\')" rel="venue_image_gallery"><img src="'. $vimage['url'] .'" width="'. $w1 .'" height="'. $h1 .'" align="left" /></a></div></li>';
				}
			}
			?>
            <div class="clr"></div>
          </ul>
          <ul>
            <li><strong class="new_blue">Address:</strong><br />
              <?php echo $bc_address;?> </li>
            <li><strong class="new_blue">City:</strong><br />
              <?php echo $bc_city; ?>
            </li>
            <li><strong class="new_blue">Zip / Postal Code:</strong><br />
              <?php echo $bc_zip; ?>
            </li>
          </ul>
          <div class="clr"></div>
        </div>
</div>