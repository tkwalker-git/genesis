<?php
if($event_id==''){
$event_id	=	getSingleColumn('id',"select * from `events` where `event_status`='1' AND id IN (select `event_id` from `event_wall` where `userid`='$user_id' ) ORDER BY `id` ASC LIMIT 0,1");
}

$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);

$active=='details';

if ( $row = mysql_fetch_assoc($res) ) {
	
	$c_event_id			= $row["id"];
	$fb_event_id		= $row["fb_event_id"];
	$userid				= $row["userid"];

//	$category			= attribValue("categories","name","where id=" . $row["category_id"] );

//	$category_seo		= attribValue("categories","seo_name","where id=" . $row["category_id"] );
//	$subcategory_id		= DBout($row["subcategory_id"]);
//	$subcategory		= attribValue("sub_categories","name","where id=" . $row["subcategory_id"] );
//	$sub_cat_seo		= attribValue("sub_categories","seo_name","where id=" . $row["subcategory_id"] );
	$event_name			= DBout($row["event_name"]);

//	$event_start_time	= $row["event_start_time"];
//	$event_end_time		= $row["event_end_time"];
//	$event_start_am_time= $row["event_start_am_time"];
//	$event_end_am_time	= $row["event_end_am_time"];
	$event_description	= DBout($row["event_description"]);
//	$event_cost			= DBout($row["event_cost"]);
	$event_image		= $row["event_image"];
//	$event_sell_ticket	= $row["event_sell_ticket"];
//	$event_age_suitab	= $row["event_age_suitab"];
	$event_status		= $row["event_status"];
	$publishdate		= $row["publishdate"];
//	$averagerating		= $row["averagerating"];
//	$modify_date		= $row["modify_date"];
//	$added_by			= $row["added_by"];
//	$source				= $row['event_source'];
	$event_seo_name		= $row['seo_name'];
//	$event_date			= getEventDates($event_id);
//	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
//	$event_dateT		= getEventStartDateFB($event_id);
//	$event_date			= $event_dateT[0];
	//$event_time			= getEventTime($event_dateT[1]);
	$cost				= $event_cost;
	$event_id			= $c_event_id;
	$bc_alter			= $row['alter'];
	$bc_alter_url		= $row['alter_url'];
	
	
//	$u = getEventURL($event_id);
    
    $fbu = 'http://www.facebook.com/sharer.php?u=' . urlencode($u) . '&t=' . urlencode($event_name);
    $twu = 'http://twitter.com/intent/tweet?url='. urlencode($u). '&via='. urlencode('EventGrabber').'&text='. urlencode($event_name);
	
	$cost				= $event_cost;
	if ( is_numeric($cost) )
		$cost = '$' . number_format($cost,2);
	$meta_title			= $event_name;
	
	$event_description_s = $event_description;
	
	if ( $event_time['start_time'] != '' )
		$time = date("h:i A", strtotime($event_time['start_time']));
		
	if ($event_time['end_time'] != '' && $event_time['end_time']!='00:00:00') 
		$time = $time . ' - ' . date("h:i A", strtotime($event_time['end_time']));

//	$event_description_s = strip_tags($event_description);
//	$event_description_s = breakStringIntoMaxChar($event_description_s,400);
		
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
	
	$page_url = ABSOLUTE_PATH . 'category/' . $category_seo . '/' . $sub_cat_seo . '/' . $event_seo_name . '.html';

}

$category_link 		= ABSOLUTE_PATH . 'category/' . $category_seo . '.html';
$sub_category_link	= ABSOLUTE_PATH . 'category/' . $category_seo . '/' . $sub_cat_seo . '.html';

?>

<div class="flayerTopWhiteFB" style="background:url('/images/new_flayer_top_fb.png') no-repeat scroll center top #FFFFFF!important; padding-top:13px">
  <div class="flayerBottomWhiteFB" style="background:url('/images/new_flayer_bottom_fb.png') no-repeat scroll center bottom #FFFFFF!important">
    <div class="flayerMiddleWhiteFB" style="background:url('/images/new_flayer_middle_fb.png') repeat-y scroll 0 0 transparent!important; width:520px!important;">
      <div class="flayerCenterFB" align="center">
	  <?php
		if($_GET['pangea']=='1'){
			$extra	= "pangea";
			$var	= "pangea";
		}
	  ?>
        <div class="front_cover" style="top:-6px"><img src="/images/front_cover.png" onClick="getFlayer('/', '<?php echo $event_id; ?>','fb');" /></div>
        <span id="flayer">
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
          <div class="new_flayer_title"><?php echo $event_name; ?></div>
          
          
          <div class="new_flayer_title"><br />
          	About Speaker
          </div>

            <div class="details">
				<?php 
                $event_description_s = strip_tags($event_description_s);
                if(strlen($event_description_s) > 1000){
                	echo substr($event_description_s,0,1000);
                ?>... <span style="cursor:pointer;color:#005683" onclick="showText('/','events','event_description','<?php echo $event_id; ?>')"><strong>See More</strong></span>
                <?php
                }
                else{
	                echo $event_description_s;
                }
                ?>
            </div><br />

          <div class="shareBox"><span>Share this event with friends. . .</span>
          <div class="socials"><a href="javascript:void(0)" onclick="windowOpener(500,500,'Modal Window','<?php echo $fbu;?>')"><img src="/images/post_on_fb.png" alt="" title=""></a> <a href="javascript:void(0)" onclick="windowOpener(500,500,'Modal Window','<?php echo $twu;?>')"><img src="/images/post_on_twit.png" alt="" title=""></a>
            <div class="clr" style="height:13px">&nbsp;</div>
			<?php
           		if($extra == "pangea")
		            $append	= '&pangea=1';
            ?>
            <a href="javascript:void(0)" onclick="windowOpener(520,540,'Modal Window','<?php echo ABSOLUTE_PATH;?>send_mail_flyer.php?id=<?php echo $c_event_id.$append; ?>')"><img src="/images/send_mail.png" alt="" title=""></a> 
			<a href="<?php echo $assessment_url; ?>"><img src="/images/eventgrabber_rsvp.png" alt="" title="" style="cursor:pointer"></a>
            <div class="clr"></div>
          </div>
        </div>
        </div>
        <!-- end inrDiv -->
        </span> </div>
      <div class="evPoweredBy" style="text-align:right">
      	<img src="/images/powered-by-restoration.png" border="0" />
      </div>
    </div>
  </div>
</div>