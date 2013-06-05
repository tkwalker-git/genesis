<?php
include_once('admin/database.php');
include_once("admin/xmlparser.php");

// http://api.citygridmedia.com/content/places/v2/detail?listing_id=11579059&publisher=test&client_ip=122.123.124.125&nolog=1
$publisher_id = 'test';

$url		= 'http://api.citygridmedia.com/content/places/v2/detail?listing_id='. $venue_cg_id .'&publisher='. $publisher_id .'&client_ip='. $_SERVER['REMOTE_ADDR'] .'&nolog=1';

//$url 		= "http://eventgrabber/cg_venue_xml.xml";
$nom 		= get_url_contents($url);
$xmlparser	= new xmlparser();
$data		= $xmlparser->GetXMLTree($nom);
//print_r($data);
$action_target  = 'listing_profile';
$location_id 	= $data['LOCATIONS'][0]['LOCATION'][0]['ID'][0]['VALUE'];
$reference_id 	= $data['LOCATIONS'][0]['LOCATION'][0]['REFERENCE_ID'][0]['VALUE'];
$impression_id 	= $data['LOCATIONS'][0]['LOCATION'][0]['IMPRESSION_ID'][0]['VALUE'];
$profile_url	= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['PROFILE_URL'][0]['VALUE'];
$reviews_url	= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['REVIEWS_URL'][0]['VALUE'];
$video_url		= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['VIDEO_URL'][0]['VALUE'];
$website_url	= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['WEBSITE_URL'][0]['VALUE'];
$email_url		= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['EMAIL_LINK'][0]['VALUE'];
$resv_url		= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['RESERVATION_URL'][0]['VALUE'];
$send_friend	= $data['LOCATIONS'][0]['LOCATION'][0]['URLS'][0]['SEND_TO_FRIEND_URL'][0]['VALUE'];
$display_phone	= $data['LOCATIONS'][0]['LOCATION'][0]['CONTACT_INFO'][0]['DISPLAY_PHONE'][0]['VALUE'];

$overall_rating = $data['LOCATIONS'][0]['LOCATION'][0]['REVIEW_INFO'][0]['OVERALL_REVIEW_RATING'][0]['VALUE'];
$reviews_count	= $data['LOCATIONS'][0]['LOCATION'][0]['REVIEW_INFO'][0]['TOTAL_USER_REVIEWS'][0]['VALUE'];
$custom_message = $data['LOCATIONS'][0]['LOCATION'][0]['CUSTOMER_CONTENT'][0]['CUSTOMER_MESSAGE'][0]['VALUE'];

$bulletsArr		= $data['LOCATIONS'][0]['LOCATION'][0]['CUSTOMER_CONTENT'][0]['BULLETS'][0]['BULLET'];
if ( is_array($bulletsArr) ) {
	foreach ( $bulletsArr as $key => $value) 
		$bullets[] = $value['VALUE'];
}

$offersArr		= $data['LOCATIONS'][0]['LOCATION'][0]['OFFERS'][0]['OFFER'];
if ( is_array($offersArr) ) {
	foreach ( $offersArr as $key => $value) {
		$offers[$key]['name'] 		= $value['OFFER_NAME'][0]['VALUE'];
		$offers[$key]['text'] 		= $value['OFFER_TEXT'][0]['VALUE'];
		$offers[$key]['descr'] 		= $value['OFFER_DESCRIPTION'][0]['VALUE'];
		$offers[$key]['url'] 		= $value['OFFER_URL'][0]['VALUE'];
		$offers[$key]['exp_date'] 	= $value['OFFER_EXPIRATION_DATE'][0]['VALUE']; // 1969-12-31T16:00:00.000-08:00
	}	
}

$neigArr		= $data['LOCATIONS'][0]['LOCATION'][0]['NEIGHBORHOODS'][0]['NEIGHBORHOOD'];
if ( is_array($neigArr) ) {
	foreach ( $neigArr as $key => $value) 
		$neighbors[] = $value['VALUE'];
}

$imagesArr		= $data['LOCATIONS'][0]['LOCATION'][0]['IMAGES'][0]['IMAGE'];
$p=0;
if ( is_array($imagesArr) ) {
	foreach ( $imagesArr as $key => $value) {
		if ( $value['IMAGE_URL'][0]['VALUE'] != '' ) {
			$images[$p]['url'] = $value['IMAGE_URL'][0]['VALUE'];
			$images[$p]['height'] = $value['HEIGHT'][0]['VALUE'];
			$images[$p]['width'] = $value['WIDTH'][0]['VALUE'];
			$p++;
		}	
	}	
}

$reviewsArr		= $data['LOCATIONS'][0]['LOCATION'][0]['REVIEW_INFO'][0]['REVIEWS'][0]['REVIEW'];
if ( is_array($reviewsArr) ) {
	foreach ( $reviewsArr as $key => $value) {
		$reviews[$key]['title'] 	= $value['REVIEW_TITLE'][0]['VALUE'];
		$reviews[$key]['text'] 		= $value['REVIEW_TEXT'][0]['VALUE'];
		$reviews[$key]['author'] 	= $value['REVIEW_AUTHOR'][0]['VALUE'];
		$reviews[$key]['date'] 		= $value['REVIEW_DATE'][0]['VALUE'];
		$reviews[$key]['rating'] 	= $value['REVIEW_RATING'][0]['VALUE'];
	}	
}

?>
<!-- Tracking Code -->

<script type="text/javascript">
    var _csv = {};
    _csv['action_target'] = 'listing_profile';
    _csv['listing_id'] = '<?php echo $location_id;?>';
    _csv['publisher'] = '<?php echo $publisher_id;?>';
    _csv['reference_id'] = '<?php echo $reference_id;?>';
   // _csv['muid'] = '';
    _csv['i'] = '<?php echo $impression_id;?>';
</script>
<script type="text/javascript" src="http://api.citygridmedia.com/ads/tracker/assets/api/scripts/tracker.js"></script>
<noscript>
    <img src='http://api.citygridmedia.com/ads/tracker/imp?action_target=listing_profile&listing_id=<?php echo $location_id;?>&publisher=<?php echo $publisher_id;?>&reference_id=<?php echo $reference_id;?>&i=<?php echo $impression_id;?>' width='1' height='1' alt='' />
</noscript>



<!-- Tracking Code -->

