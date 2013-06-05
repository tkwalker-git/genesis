<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='".ABSOLUTE_PATH."login.php';</script>";
$_SESSION['order_id']='';
$event_id = $_GET['event_id'];





$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
$bc_ticket_id			=	$row['id'];
$bc_name				=	$row['name'];
$bc_price				=	$row['price'];
$bc_ticket_description	=	$row['ticket_description'];
$bc_ticket_id			=	$row['id'];
$bc_service_fee_type	=	$row['service_fee_type'];
$bc_service_fee			=	$row['service_fee'];
$bc_quantity_available	=	$row['quantity_available'];
}



$r = mysql_query("select * from `orders` where `main_ticket_id`='$bc_ticket_id'");
while($m = mysql_fetch_array($r)){
$order_id	=	$m['id'];


$q = mysql_query("SELECT SUM(quantity) AS total FROM order_tickets where `order_id`='$order_id'");
while($m = mysql_fetch_array($q)){
$booked_tickets_quantity	=	$booked_tickets_quantity+$m['total'];
}
}
if($booked_tickets_quantity==''){
$booked_tickets_quantity=0;}

$now_quantity_available = $bc_quantity_available - $booked_tickets_quantity;

require_once('includes/header.php');
if (validEventTicketSaleTime($event_id)=='no'){
	echo "<script>window.location.href='".ABSOLUTE_PATH."myeventwall.php';</script>";
}



$res = mysql_query("select * from `events` where `id`='$event_id'");
if ( $row = mysql_fetch_assoc($res) ) {
	
	$c_event_id			= $row["id"];
	$fb_event_id		= $row["fb_event_id"];
	$userid				= $row["userid"];
	$category			= attribValue("categories","name","where id=" . $row["category_id"] );
	$subcategory_id		= DBout($row["subcategory_id"]);
	$subcategory		= attribValue("sub_categories","name","where id=" . $row["subcategory_id"] );;
	$event_name			= DBout($row["event_name"]);

	$event_description	= DBout($row["event_description"]);
	$event_cost			= DBout($row["event_cost"]);
	$event_image		= $row["event_image"];
	$event_image = removeSpaces("events","event_image",$event_image,"event_images/");
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
	list($viw, $vih) = getPropSize($viw, $vih, 420,'');
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
?>
 <style>
.nav_new {
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 5px 5px 5px 5px;
    box-shadow: 0 0 7px 2px #C7C7C7;
	overflow:hidden;
	margin:0;
	}

.nav_new ul li {
    border-right: 1px solid #B4B4B4;
    float: left;
    list-style: none outside none;
}

.nav_new ul li a {
    color: #000000;
    float: left;
    font-size: 15px;
    font-weight: bold;
    padding: 13px 35px;
    text-decoration: none;
}
</style>
<div class="topContainer">
  <div class="welcomeBox"></div>
 
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Buy Tickets </div>
    <div class="clr"></div>
    <div class="gredBox">
      
    
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
             
			 
			 	<!--Start new code-->
				
				<div class="buyTicketp1">
				
					<div class="thumb-1">
						<div class="thumb_btn"><?php  echo  getAddToWallButton($event_id,''); ?></div>
						<?php echo $image_display; ?>
						<!--<img src="<?php echo IMAGE_PATH; ?>thumb-1.png" width="420" height="578" border="0" />-->
					
					</div> <!--end thumb-1-->
					
					<div class="thumb-1-detail">
						
						<span class="ew-heading"><?php echo $event_name; ?></span><!--end ew-heading-->
						
						<div class="ew-heading-behind">
							
							<span>Celebration</span>
						
						</div> <!--end ew-heading-behind-->
						
						<span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo $event_date; ?></span>
						
						<div class="ew-price-area">
							
							<span  class="ew-heading-a">Price:&nbsp;<span style="color:#ff4e1f;">$99.00</span><button></button></span>
							
						</div> <!--end ew-price-area-->
						
						
						<div class="ew-when-where">
							
							<span class="ew-when-heading">When</span>
							<span>
								Saturday, November 19th <br />
								10&nbsp;PM - 2&nbsp;AM
								
							</span>
							
						</div> <!--end ew-when-where-->
						
						<div class="ew-when-where">
							
							<span class="ew-when-heading">Where</span>
							<span>
								Heaven Event Center <br />
								(Off Sand Lake Road) <br />
								8240 Exchange Dr. <br />
								Orlando, FL 32809 <a href="#">[+] See Map</a>
								
							</span>
							
						</div> <!--end ew-when-where-->
						
						<div class="ew-when-where">
							
							<span class="ew-when-heading">Summary</span>
							<span>
								<?php echo $event_description; ?> <a href="#">[More]</a>
								
							</span>
							
						</div> <!--end ew-when-where-->
					
					</div> <!--end thumb-1-detail-->
					
					<br class="clear" />
				
				</div> <!--end buyTicketp1-->
				
				
				
					<div class="nav_new">
						<ul>
							<li><a href="#">Nearby Deals</a></li>
							<li><a href="#">Location Info</a></li>
							<li class="active"><a href="">Event Details</a></li>
							<li><a href="#">Gallery</a></li>
							<li><a href="#">Videos</a></li>
							<li><a href="#">Special Events</a></li>
						</ul>
						
					</div><!--end nav_new-->
				
				<br class="clear" />
				
				
				<div class="blocker">
					<div class="blockerTop"></div> <!--end blockerTop-->
					<div class="blockerRepeat">
						
						<span class="ew-heading">Event Detail</span>
						
						<p><?php echo $event_description; ?></p>
					
					</div> <!--end blockerRepeat-->
					<div class="blockerBottom"></div> <!--end blockerBottom-->
				</div> <!--end blocker-->
				
				
				<div class="blocker">
					<div class="blockerTop"></div> <!--end blockerTop-->
					<div class="blockerRepeat">
						
						<span class="ew-heading" style="font-size:18px;">Event Similar to Omega Alumni Centenniel Celebration <a href="">How did we determine these events ?</a></span>
						<span class="dottedSeparator"></span>
						
						<div class="ew-sugesstions">
						
						<span class="ew-left-control"></span>
						
						<span class="ew-right-control"></span>
							
							<?php 
								for($i=0 ;  $i <= 3; $i++){	?>
							<div class="ew-suggetions-block">
							
								<div class="ew-suggetion-top"></div><!--end ew-suggetion-top-->
								
								<div class="ew-suggetion-center">
								
									<span class="ew-suggetion-tiny-heading">OACD - Crimson</span>
									
									<span class="ew-suggetion-date">Dec 10, 2011</span>
									
									<span class="ew-suggetion-separator"></span>
									
									<img src="<?php echo IMAGE_PATH; ?>suggetion-thumb.png" width="156" height="196" border="0" />
								
								</div><!--end ew-suggetion-center-->
								
								<div class="ew-suggetion-bottom"></div><!--end ew-suggetion-bottom-->
								
								<button class="suggetion-btn"></button>
								
								<ul class="ratingUL">
									<li><img src="<?php echo IMAGE_PATH; ?>on-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>on-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>on-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>off-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>off-star.png" width="17" height="16" border="0" /></li>
								</ul>
							
							</div><!--end ew-suggetions-block-->
							<?php }?>
						</div> <!--end ew-sugesstions-->
						
						<br class="clear" />
					
					</div> <!--end blockerRepeat-->
					<div class="blockerBottom"></div> <!--end blockerBottom-->
				</div> <!--end blocker-->
				
			 	<!--End new code-->
			 
			 
            </div>
          </div>
        </div>
        <div class="create_event_submited">
       
        </div>
      </form>

    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>