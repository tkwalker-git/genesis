<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

$event_id = $_POST['event_id'];
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
	$event_seo_name		= $row['seo_name'];
	$event_date			= getEventDates($event_id);
	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
	$time				= $event_start_time . ' - ' . $event_end_time;
	$cost				= $event_cost;
	
	$meta_title			= $event_name;
	
	$event_description_s = strip_tags($event_description);
//	$event_description_s = breakStringIntoMaxChar($event_description_s,200);
		
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
?>

<div class="flayerTopWhite">
  <div class="flayerBottomWhite">
    <div class="flayerMiddleWhite">
      <div class="flayerCenter" align="center">
        <?php include("../flayerMenu.php"); ?>
        <table width="100%" cellpadding="10" cellspacing="0" class="eventDetails">
          <tr>
            <td align="left"><span class="event-name-big2"><strong><?php echo $event_name; ?></strong></span> <br>
              <span class="ev_date"><?php echo date ('l F 3, Y', strtotime($event_date)); ?>
              </span> </td>
          </tr>
          <tr>
            <td align="left"><table cellpadding="0" cellspacing="0">
                <tr>
                  <td><span class="title">Category:</span> <span class="cat-name"><a class="cate_links" href="<?php echo $category_link;?>"><?php echo $category;?></a></span></td>
                  <td> &nbsp; <img src="<?php echo IMAGE_PATH;?>catSubCat.png" align="absmiddle" /> &nbsp; </td>
                  <td><span class="sub-cat-name"> <span class="title">Sub-Category:&nbsp;&nbsp;</span> <a class="cate_links" href="<?php echo $sub_category_link;?>"><?php echo $subcategory;?></a></span></td>
                </tr>
              </table></td>
          </tr>
          
          <tr>
            <td align="left"><span class="title">Hosted by:</span> <span class="host-name"><?php echo $added_by;?></span> </td>
          </tr>
          <tr>
            <td align="left"><span class="title">Location:</span> <span class="locName"><?php echo $venue_attrib[1]['venue_name'];?></span> </td>
          </tr>
          <tr>
            <td align="left"><span class="title">Time:</span> <span class="timing"><?php echo $time;?></span> </td>
          </tr>
          <tr>
            <td align="left"><span class="title">Cost:</span> <span class="cost"><?php echo $cost;?></span> </td>
          </tr>
          <tr>
            <td align="left"><span class="title">Details:</span><p class="details"><?php echo $event_description_s;?></p></td>
          </tr>
        </table>
        <div class="shareBox"><span>Share this event with friends. . .</span>
          <div class="socials"><a href="#"><img src="images/facebook.png" alt="" title=""></a> <a href="#"><img src="images/twitter.png" alt="" title=""></a>
            <div class="clr" style="height:13px">&nbsp;</div>
            <a href="#"><img src="images/sendEmail.png" alt="" title=""></a> <a href="#"><img src="images/invite.png" alt="" title=""></a>
            <div class="clr"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>