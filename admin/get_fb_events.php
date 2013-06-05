<?php

include_once("database.php");
include_once('api/facebook.php');
include_once('header.php'); 
?>

<div class="bc_heading">
	<div>MeetUp</div>
</div>

<div style="padding:20px">

<?php
$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$sql	=	"select * from fb_groups";
$res	=	mysql_query($sql);
if ($res) 
	while ($row = mysql_fetch_assoc($res) ) 
		$bc_gid[]		=	DBout($row["gid"]);

$group_ids = implode(',',$bc_gid);
//'8253926053,8835675836';
	
$fql = "SELECT eid, name, tagline, pic_small,pic_big,pic, host, description, event_type, event_subtype, creator, location, venue, start_time,end_time FROM event 
		WHERE eid IN (select eid from event_member where uid IN (". $group_ids .") ) "; 
 
//echo '<hr><hr>';

$response = $facebook->api(	
							array(
								'method' => 'fql.query',
								'query' =>$fql,
							)
						);

//print_r($response); 

//echo '<strong>' . $fbid . '</strong><hr>';

$total_added = 0;

mysql_query("TRUNCATE TABLE venues_temp");
mysql_query("TRUNCATE TABLE venue_events_temp");

foreach ($response as $value) {
	
	$eid 	= "FB-".$value['eid'];
	$name	= DBin($value['name']);
	$pic_s	= $value['pic_small'];
	$pic_b	= $value['pic_big'];
	$pic	= $value['pic'];
	$host	= $value['host'];
	$venu_name	= $value['location'];
	$desc	= DBin(nl2br($value['description']));
	$cat	= $value['event_type'];
	$scat	= $value['event_subtype'];
	$sdate	= @date("Y-m-d",$value['start_time']);
	$edate	= @date("Y-m-d",$value['end_time']);
	
	$today = strtotime(date("m-d-Y"));
	
	$stime	= @date("g:i A",$value['start_time']);
	$etime	= @date("g:i A",$value['end_time']);
	
	$venue	= $value['venue']; // array
	
	if ($pic_b != '' )
		$image =  $pic_b;
	else {
		if ($pic != '' )
			$image =  $pic;
		else
			$image =  $pic_s;	
	}	
	
	$address	= $venue['street'];
	$city		= $venue['city'];
	$state		= $venue['state'];
	$lat		= $venue['latitude'];
	$lng		= $venue['longitude'];
	
	$today	= strtotime(date("Y-m-d"));
	
	if ( $value['start_time'] > $today  ) {
		
		//echo $value['start_time'] . ' - ' . $today;
		//echo '<br>';
		$event_query = "insert ignore into events (event_source,source_id,event_name,event_description,event_image,added_by,event_cost,event_status,event_start_time,event_end_time ) 
								VALUES ('Facebook','$eid','$name','$desc','$image','$host','','0','$stime','$etime')";
	
		
		mysql_query($event_query) ;
		$event_insert_id = mysql_insert_id();
			
		if( $event_insert_id > 0){
			$venu_id = $eid;
			$total_added++;
			
			$venue_insert_id = matchVenueLatLng($lat,$lng);
			
			if ( $venue_insert_id == 0 ) {
				$venue_query = "insert ignore into venues_temp (source_id,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,phone)
							 VALUES ('$venu_id','$venu_name','$address','$city','$state','$zip','$lng','$lat','') ";
				mysql_query($venue_query) ;
				$venue_insert_id = mysql_insert_id();
			}
			
			$venue_event_query = "insert into venue_events_temp (venue_id, event_id) values ('$venue_insert_id','$event_insert_id')";
			mysql_query($venue_event_query) ;
		}
			
		if($event_insert_id > 0){
			$event_dates_query = "insert into event_dates (event_id, event_date) values ('$event_insert_id', '$sdate')";
			mysql_query($event_dates_query);
			if ( $sdate != $edate ) {
				$event_dates_query = "insert into event_dates (event_id, event_date) values ('$event_insert_id', '$edate')";
				mysql_query($event_dates_query);
			}	
		}
	}
	
	// update event venues
	include_once("transfer_live.php");
	
	/*echo $stime . ' = ' . $etime . '<br>';
	echo 'Eid: ' . $value['eid'] . '<br>';
	echo 'Name: ' . $value['name'] . '<br>';
	echo 'Pic: ' . $value['pic_small'] . '<br>';
	echo 'Host: ' . $value['host'] . '<br>';
	echo 'Location: ' . $value['location'] . '<br><hr><br>';
	*/
}

?>

<h1><?php echo $total_added;?> Records added.</h1>

</div>
<?php  include_once('footer.php')?>
