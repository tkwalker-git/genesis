<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$active='assessment';
$event_id			= $_POST['event_id'];
$category_link 		= ABSOLUTE_PATH . 'category/' . $category_seo . '.html';
$sub_category_link	= ABSOLUTE_PATH . 'category/' . $category_seo . '/' . $sub_cat_seo . '.html';

$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);

if ( $row = mysql_fetch_assoc($res) ) {
	$c_event_id			= $row["id"];
	$fb_event_id		= $row["fb_event_id"];
	$userid				= $row["userid"];
	$category			= attribValue("categories","name","where id=" . $row["category_id"] );
	$subcategory_id		= DBout($row["subcategory_id"]);
	$subcategory		= attribValue("sub_categories","name","where id=" . $row["subcategory_id"] );;
	$event_name			= DBout($row["event_name"]);

	$event_start_time	= $row["event_start_time"];
	$event_end_time		= $row["event_end_time"];
	$event_start_am_time= $row["event_start_am_time"];
	$event_end_am_time	= $row["event_end_am_time"];
	$event_description	= DBout($row["event_description"]);
	$event_cost			= DBout($row["event_cost"]);
	$event_image		= $row["event_image"];
	$event_sell_ticket	= $row["event_sell_ticket"];
	$event_age_suitab	= $row["event_age_suitab"];
	$event_status		= $row["event_status"];
	$publishdate		= $row["publishdate"];
	$averagerating		= $row["averagerating"];
	$modify_date		= $row["modify_date"];
	$added_by			= $row["added_by"];
	$source				= $row['event_source'];
	$bc_seo_name		= $row['seo_name'];
	$bc_alter			= $row['alter'];
	$bc_alter_url		= $row['alter_url'];
	$event_date			= getEventDates($event_id);
	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
	$event_dateT		= getEventStartDateFB($event_id);
	$event_date			= $event_dateT[0];
	$event_time			= getEventTime($event_dateT[1]);
	$assessment_image	= $row['assessment_image'];
	$cost				= $event_cost;
	if ( is_numeric($cost) )
		$cost = '$' . number_format($cost,2);
	$meta_title			= $event_name;

	$event_description_s = strip_tags($event_description);
//	$event_description_s = breakStringIntoMaxChar($event_description_s,200);

		if ( $event_time['start_time'] != '' ) 
		$time = date("h:i A", strtotime($event_time['start_time']));

		if ( $event_time['end_time'] != '' && $event_time['end_time']!='00:00:00') 
			$time = $time . ' - ' . date("h:i A", strtotime($event_time['end_time']));

	if (trim($event_image) != '') {

		if ( substr($event_image,0,7) != 'http://' && substr($event_image,0,8) != 'https://' ) {
			$image = ABSOLUTE_PATH .'event_images/th_' . $event_image;
			$imageE = ABSOLUTE_PATH .'event_images/' . $event_image;
			
		} else {
			if ( $source == "EventFull") {
				if ( strtolower(substr($event_image,-4,4)) != '.gif')
					$image = str_replace("/medium/","/large/",$event_image);	
			}else {
				$image = $event_image;
			}	
		}
		$img_params = returnImage($image,272,375); 
		
	} else {
		$img_params = ' src="'. ABSOLUTE_PATH .'images/bigAvatar_photo.png" height="376" width="272" border="0" ';
		$kk = 1;
	}	
	
	if ( $imageE != '' ) {
		$image_display = '<a id="eventImage" href="'. $imageE .'" ><img align="center" '. $img_params .' /></a>';	
	} else {
		if ( $kk == 1 )	
			$image_display = '<img align="center" '. $img_params .' />';	
		else
			$image_display = '<a id="eventImage" href="'. $image .'" ><img align="center" '. $img_params .' /></a>';	
	}		
	
	$page_url = ABSOLUTE_PATH . 'category/' . $category_seo . '/' . $sub_cat_seo . '/' . $bc_seo_name . '.html';
	
}
$u = getEventURL($event_id);

$fbu = 'http://www.facebook.com/sharer.php?u=' . urlencode($u) . '&t=' . urlencode($event_name);
$twu = 'http://twitter.com/intent/tweet?url='. urlencode($u). '&via='. urlencode('EventGrabber').'&text='. urlencode($event_name);
?>

       <?php include("../flayerMenuFB.php"); ?>
    <style>
		.cat-name { text-shadow:0 1px #CCCCCC }
		.new_flayer_title {color:#000000!important; font-size:18px!important; margin-top:15px}
		.new_flayer_date {color:#3B5998!important; font-size:14px!important; text-shadow:0 1px #CCCCCC}
		.mapArea {background:none!important; padding-left:0px}
		.cat_loc_tm {width:440px!important}
		.details { color:#777777!important; font-size:11px!important }
		.lableValue {text-shadow:0 1px #CCCCCC; }
		</style>
    <div class="clear"></div>
    <div class="inrDiv">
      <div class="new_flayer_title" style="text-align:center"><img src="<?php echo IMAGE_PATH . $assessment_image; ?>" /></div>	  
	  
	 <div class="new_flayer_title"><br />
     	Summary
     </div>

	<div class="details">
                <?php 
					$event_description_s = strip_tags($event_description_s);
					if(strlen($event_description_s) > 600){
					echo substr($event_description_s,0,600);
				?>
            ... <span style="cursor:pointer;color:#005683" onclick="showText('/','events','event_description','<?php echo $event_id; ?>')"><strong>See More</strong></span>
                <?php
				}
				else{
					echo $event_description_s;
				}
			?>
              </div>
<br />

          <div class="shareBox"><span>Share this event with friends. . .</span>
          <div class="socials"><a href="javascript:void(0)" onclick="windowOpener(500,500,'Modal Window','<?php echo $fbu;?>')"><img src="<?php echo IMAGE_PATH; ?>post_on_fb.png" alt="" title=""></a> <a href="javascript:void(0)" onclick="windowOpener(500,500,'Modal Window','<?php echo $twu;?>')"><img src="<?php echo IMAGE_PATH; ?>post_on_twit.png" alt="" title=""></a>
            <div class="clr" style="height:13px">&nbsp;</div>
			<?php
	           if($extra == "pangea")
		            $append	= '&pangea=1';
            ?>
            <a href="javascript:void(0)" onclick="windowOpener(520,540,'Modal Window','<?php echo ABSOLUTE_PATH;?>send_mail_flyer.php?id=<?php echo $c_event_id . $append; ?>')"><img src="<?php echo IMAGE_PATH; ?>send_mail.png" alt="" title=""></a> 
			<a href="<?php echo $assessment_url; ?>"><img src="<?php echo IMAGE_PATH; ?>eventgrabber_signup.png" alt="" title=""></a>
            <div class="clr"></div>
          </div>
        </div>
    </div>
    <!-- end inrDiv -->