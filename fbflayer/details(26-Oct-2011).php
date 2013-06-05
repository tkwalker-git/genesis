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
	$category			= attribValue("categories","name","where id=" . $row["category_id"] );
	$category_seo		= attribValue("categories","seo_name","where id=" . $row["category_id"] );
	$subcategory_id		= DBout($row["subcategory_id"]);
	$subcategory		= attribValue("sub_categories","name","where id=" . $row["subcategory_id"] );
	$sub_cat_seo		= attribValue("sub_categories","seo_name","where id=" . $row["subcategory_id"] );
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
	$event_seo_name		= $row['seo_name'];
	$event_date			= getEventDates($event_id);
	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
	$time				= $event_start_time . ' - ' . $event_end_time;
	$cost				= $event_cost;
	
	$meta_title			= $event_name;
	
	$event_description_s = $event_description;
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
<span id="mainflayer">
<div class="flayerTopWhiteFB">
  <div class="flayerBottomWhiteFB">
    <div class="flayerMiddleWhiteFB">
      <div class="flayerCenterFB" align="center">
	   <div class="backImage" id="back"><img src="<?php echo IMAGE_PATH; ?>front_cover.png" alt="" title="Front Cover here" /></div>
	  <span  id="flayer"> 
        <?php include("../flayerMenuFB.php"); ?>
        <table width="100%" cellpadding="10" cellspacing="0" class="eventDetails">
          <tr>
            <td align="left"><span class="event-name-big2"><strong><?php echo $event_name; ?></strong></span> <br>
              <span class="ev_date" style="font-size:16px"><?php echo date('l F d, Y', strtotime($event_date)); ?></span> </td>
          </tr>
          <tr>
            <td align="left" style="font-size:14px"><table cellpadding="0" cellspacing="0">
                <tr>
                  <td><span class="title">Category:</span> <span class="cat-name"><a target="_blank" class="cate_links" href="<?php echo $category_link;?>"><?php echo $category;?></a></span></td>
                  <td> &nbsp; <img src="<?php echo IMAGE_PATH;?>catSubCat.png" align="absmiddle" /> &nbsp; </td>
                  <td><span class="sub-cat-name"> <span class="title">Sub-Category:&nbsp;&nbsp;</span> <a target="_blank" class="cate_links" href="<?php echo $sub_category_link;?>"><?php echo $subcategory;?></a></span></td>
                </tr>
              </table></td>
          </tr>
          
          <tr>
            <td align="left" style="font-size:14px"><span class="title">Hosted by:</span> <span class="host-name"><?php echo $added_by;?></span> </td>
          </tr>
          <tr>
            <td align="left" style="font-size:14px"><span class="title">Location:</span> <span class="locName"><?php echo $venue_attrib[1]['venue_name'];?></span> </td>
          </tr>
         <tr>
            <td align="left" style="font-size:14px"><table width="100%"><tr><td width="50%"><span class="title">Time:</span> <span class="timing"><?php echo $time;?></span> </td>
			<td width="50%">
			 <span class="title">Cost:</span> <span class="cost"><?php echo $cost;?></span></td>
            </tr></table>
			 </td>
          </tr>
          <tr>
            <td align="left"><span class="title">Summary:</span>
			<div class="clr"></div>
			<div class="details" style="height:100px; background:#f5f5f5; border:solid 1px #e8e8e8; padding:3px; margin:3px 0; overflow:auto"><?php echo $event_description_s;?></div></td>
          </tr>
        </table>
        <div class="shareBox"><span>Share this event with friends. . .</span>
          <div class="socials"><a href="#"><img src="<?php echo IMAGE_PATH; ?>facebook.png" alt="" title=""></a> <a href="#"><img src="<?php echo IMAGE_PATH; ?>twitter.png" alt="" title=""></a>
            <div class="clr" style="height:13px">&nbsp;</div>
            <a href="#"><img src="<?php echo IMAGE_PATH; ?>sendEmail.png" alt="" title=""></a> <a href="#"><img src="<?php echo IMAGE_PATH; ?>invite.png" alt="" title=""></a>
            <div class="clr"></div>
          </div>
        </div>
</span>
      </div>
    </div>
  </div>
</div>
</span>