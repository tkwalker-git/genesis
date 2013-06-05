<?php 

require_once('admin/database.php');
require_once('site_functions.php');

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

$category_seo 	= $_GET['category'];
$sub_cat_seo	= $_GET['sub_cat'];
$event_seo_name	= $_GET['event_id'];

$event_id		= getSingleColumn('id',"select * from `events` where `seo_name`='$event_seo_name'");

$type			= $_GET['type'];

if ( !is_numeric($event_id) || $event_id <= 0 )
	die("Direct access to this page is not allowed.");

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
//	$time				= $event_start_time . ' - ' . $event_end_time;
	$cost				= $event_cost;
	
	$event_dateT		= getEventStartDateFB($event_id);
	$event_time			= getEventTime($event_dateT[1]);
	
	if ( $event_time['start_time'] != '' && $event_time['start_time'] != '00:00:00' ) 
		$time = date("h:i a", strtotime($event_time['start_time']));
		
	if ( $event_time['end_time'] != '' && $event_time['end_time'] != '00:00:00' ) 
		$time = $time . ' - ' . date("h:i a", strtotime($event_time['end_time']));	
	$meta_title			= $event_name;
	
	$event_description_s = strip_tags($event_description);
	$event_description_s = breakStringIntoMaxChar($event_description_s,200);
		
	if (trim($event_image) != '') {

		if ( substr($event_image,0,7) != 'http://' && substr($event_image,0,8) != 'https://' ) {
		
		if ( file_exists(DOC_ROOT . 'event_images/' . $event_image ) ) {
		
			$image = ABSOLUTE_PATH .'event_images/' . $event_image;
			$imageE = ABSOLUTE_PATH .'event_images/' . $event_image;
			
			}
			else{
			$img_params = ' src="'. ABSOLUTE_PATH .'images/bigAvatar_photo.png" width="174" border="0" ';
			$kk = 1;
			}
		} else {
			if ( $source == "EventFull") {
				if ( strtolower(substr($event_image,-4,4)) != '.gif')
					$image = str_replace("/medium/","/large/",$event_image);	
			}else {
				$image = $event_image;
			}	
		}
	//	$img_params = returnImage($image,272,375);
	if($kk!=1){
	list($viw, $vih) = getimagesize($image);
	list($viw, $vih) = getPropSize($viw, $vih, 272,524);
	$img_params = 'src="'.$image.'" height="'.$vih.'" width="'.$viw.'" ';
		}
	} else {
		$img_params = ' src="'. ABSOLUTE_PATH .'images/bigAvatar_photo.png" width="174" border="0" ';
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

include_once('includes/header.php');

?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$("a#eventImage").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	});
</script>

<div id="main">
	<div id="main-inner">
		<div id="contents">
		<?php
		if(validEventTicketSaleTime($event_id)=='yes'){
		$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
		if(mysql_num_rows($res)){
		?>
		<div align="right" style="padding:5px;"><a href="<?php echo ABSOLUTE_PATH."book-tickets/".$category_seo."/".$sub_cat_seo."/".$event_id;?>.html"><img src="<?=IMAGE_PATH?>book-ticket-btn.png" /></a></div>
		<?php } }?>
		
			<div id="contents-top"></div>
				<div id="contents-middle">
					<div id="event-details">
					 <div id="event-left">
						<div id="event-flyer-bg">
							<div id="event-flyer">
								<div style="width:272px; overflow:hidden; max-height:524px">
									<?php echo $image_display;?>
								</div>	
							</div>
						</div><!-- event-flyer-bg -->
						<br>
						<center>
						
						<?php 

							$already = attribValue('event_wall', 'id', "where event_id='$event_id' and userid='$member_id'");
							if ( $already > 0 ) 
								echo  '<img src="'. ABSOLUTE_PATH .'images/added_to_event_btn_small.png" />';
							else	
								echo '<a href="javascript:void(0)" onclick="addToEventWall(\''. ABSOLUTE_PATH .'\','. $event_id .')"><img src="'. ABSOLUTE_PATH .'images/add_event22.png" /></a><br><br>&nbsp;'; 
						?>
						</center>
					 </div><!-- event-left -->	
					 <div id="event-right">
					 	<table width="100%" cellpadding="10" cellspacing="0" align="left">
						<tr>
							<td align="left">
								<span class="event-name-big"><?php echo $event_name; ?></span>
								<br>
								<span class="heading"><?php echo $event_date; ?><!--Friday March 11, 2011 - Sunday March 13, 2011--></span>
							</td>
						</tr>
						<tr>
							<td align="left">
								<table>
								<tr>
								<td><span class="title">Category:</span> <span class="cat-name"><a class="cate_link" href="<?php echo $category_link;?>"><?php echo $category;?></a></span></td>
								<td><!--<img src="<?php echo IMAGE_PATH;?>rightarrow.png" align="absmiddle" />-->&nbsp;</td>
								<td><span class="sub-cat-name"> <span class="title">Sub-Category:&nbsp;&nbsp;</span> <a class="cate_link" href="<?php echo $sub_category_link;?>"><?php echo $subcategory;?></a></span></td>
								</tr></table>
							</td>
						</tr>
						<?php
						$r4 = mysql_query("select * from event_music where event_id='".$event_id."'");
						if ( $r4 ) {
						while ( $ro4 = mysql_fetch_assoc($r4) )
								$mmids[] = attribValue('music', 'name', "where id='". $ro4['music_id'] ."'");

						if ( is_array($mmids) )
							$music_genre_name = implode(", ",$mmids);	
						else
							$music_genre_name = '';	
						if($music_genre_name!=''){?>
						<tr>
							<td align="left">
								<span class="title">Music Genre:</span>
								<span class="host-name"><?php echo $music_genre_name;?></span>
							</td>
						</tr>
						<?php
						}}
						if($added_by!=''){
						?>
						<tr>
							<td align="left">
								<span class="title">Hosted by:</span>
								<span class="host-name"><?php echo $added_by;?></span>
							</td>
						</tr>
						<?php 
						}
						?>
						<tr>
							<td align="left">
								<span class="title">Location:</span>
								<span class="locName"><?php echo $venue_attrib[1]['venue_name'];?>  <a href="?type=location#map22">see map</a></span>
							</td>
						</tr>
						<tr>
							<td align="left">
								<span class="title">Time:</span>
								<span class="timing"><?php echo $time;?></span>
							</td>
						</tr>
						<?php
						if($cost!=''){?>
						<tr>
							<td align="left">
								<span class="title">Cost:</span>
								<span class="cost"><?php echo $cost;?></span>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td align="left">
								<span class="title">Summary:</span>
								<p><?php echo $event_description_s;?> (<a href="?type=details">more</a>)</p>
							</td>
						</tr>
						<tr>
							<td align="left">
								<span class="heading">
									Share this event:
									<script type="text/javascript">var addthis_pub = "UnitedDatabase";</script>
									<a  href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()" ><img src="<?php echo ABSOLUTE_PATH;?>images/share_this.png" align="absmiddle" /></a>
									<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
								</span>
								<span class="social-imgs">
									<!--
									<table cellpadding="3" cellspacing="0">
									<tr>
									<td><img src="<?php echo IMAGE_PATH;?>share-icon-1.png" /></td>
									<td>
									<script type="text/javascript">var addthis_pub = "UnitedDatabase";</script>
									<a  href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()" ><img src="<?php echo ABSOLUTE_PATH;?>images/share_this.png" align="absmiddle" /></a>
									<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
									</td>
									<td><?php //getFShareBtn($page_url);?></td>
									<td><?php //getReTweetBtn($page_url);?></td>
									</tr></table>-->
									
									
									
								</span>
							</td>
						</tr>
						</table>	
						
					 </div><!-- event-right -->	
					
					</div><!-- Event-details -->
					
					<div id="nav">
						<div class="navigation_bar">
	 
						  <div class="nav">
						   
							   <div class="menu_heading heading_colored_14"></div><!--end menu_heading--> 
							   
							   <div class="menuBlock">
							   
								<ul>
								<?php  if ( $type == 'deals') { ?>
								  <li class="libutton" style="border-left:none!important"><a href="#" class="a_active"><span class="span_active">Nearby Deals</span></a></li>
								 <li class="libutton"><a href="?type=location"><span>Location Info</span></a></li>
								 <li class="libutton"><a href="?type=details"><span>Event Details</span></a></li>
								 <li class="libutton"><a href="?type=eventrating"><span>Reviews & Rating</span></a></li>
								
								 <?php } else if ($type == 'location') { ?>
								  <li class="libutton"><a href="?type=deals"><span>Nearby Deals</span></a></li>
								  <li class="libutton"><a href="#" class="a_active"><span class="span_active">Location Info</span></a></li>
								  <li class="libutton"><a href="?type=details"><span>Event Details</span></a></li>
								  <li class="libutton"><a href="?type=eventrating"><span>Reviews & Rating</span></a></li>
								
								<?php } else if (  $type == '' || $type == 'details') { ?>
								  <li class="libutton"><a href="?type=deals"><span>Nearby Deals</span></a></li>
								  <li class="libutton"><a href="?type=location"><span>Location Info</span></a></li>
								  <li class="libutton"><a href="#" class="a_active"><span class="span_active">Event Details</span></a></li>
								  <li class="libutton"><a href="?type=eventrating"><span>Reviews & Rating</span></a></li>
								
								<?php } else if (  $type == 'eventrating') { ?>
   								  <li class="libutton"><a href="?type=deals"><span>Nearby Deals</span></a></li>
								  <li class="libutton"><a href="?type=location"><span>Location Info</span></a></li>
								  <li class="libutton"><a href="?type=details"><span>Event Details</span></a></li>
								  <li class="libutton"><a href="?type=eventrating" class="a_active"><span class="span_active">Reviews & Rating</span></a></li>
								 <?php } ?>
								 
								 
								 <!--<li class="libutton last"><a href="?type=posted"><span>Posted By</span></a></li>-->
								</ul>
							   
							   </div><!--end menu-->
							   
							  </div><!--end nav-->
						  
						 </div><!--end navigation_bar-->
					 </div><!-- id-nav -->
					
					<?php 
						
						if ( $type == 'location' ) {
							include_once("widget_location.php");
						/*else if ( $type == 'host' )
							include_once("widget_host.php");*/
						} else if ( $type == 'posted' )
							include_once("widget_posted.php");
						else if ( $type == 'eventrating' )
							include_once("widget_eventrating.php");	
						else if ( $type == 'deals' )
							include_once("widget_deals.php");		
						else
							include_once("widget_eventdetails.php");
							
					?>
					
				</div><!-- contents-middle-->
			<div id="contents-bottom"></div>	
		</div><!-- contents -->
	</div><!-- main-inner -->
</div><!-- main -->

<div class="clr"></div>

<?php
include_once('includes/footer.php');?>