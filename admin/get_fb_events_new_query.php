<?php

/* 
	Notes: This script read .ics file and grab the event ids then get the details of each event
*/

include_once("database.php");
include_once('api/facebook.php');
include_once('header.php'); 
?>

<div class="bc_heading">
	<div>FaceBook Events</div>
</div>

<div style="padding:20px">

<?php

if ( isset($_POST['submit']) && $_POST['url'] != '') {

	$facebook = new Facebook(array(
						'appId'      => FACEBOOK_APP_ID,
						'secret'     => FACEBOOK_SECRET,
						'cookie'     => true
	));
	
	$ur = str_replace("webcal://","http://",$_POST['url']);

	//$a = file_get_contents("http://www.facebook.com/ical/u.php?uid=1387282969&key=AQAXhoE07Bu7lWbD");
	$a = file_get_contents($ur);
	
	$pattern = '/UID:e(.*?)@facebook.com/i';
	$m = preg_match_all($pattern, $a, $matches);
	
	$event_ids = implode(",",$matches[1]);
	
		
	$fql = "SELECT eid, name, tagline, pic_small,pic_big,pic, host, description, event_type, event_subtype, creator, location, venue, start_time,end_time FROM event 
			WHERE eid IN (". $event_ids ." ) "; 
	 
	$response = $facebook->api(	
								array(
									'method' => 'fql.query',
									'query' =>$fql,
								)
							);
	
	
	$total_added = 0;
	
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
		$dates = getDatesRange($sdate,$edate);
		$today = strtotime(date("m-d-Y"));
		$stime	= @date("H:i",$value['start_time']);
		$etime	= @date("H:i",$value['end_time']);
		
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
		
//		echo "venue is  = ".$zip	=	$venue['street']."<br /><br />";
		
		if ( $value['start_time'] > $today  ) {
			
			//echo $value['start_time'] . ' - ' . $today;
			//echo '<br>';
			echo $event_query = "insert ignore into events (event_source,source_id,event_name,event_description,event_image,added_by,event_cost,event_status,event_start_time,event_end_time,is_expiring ) 
									VALUES ('Facebook','$eid','$name','$desc','$image','$host','','0','$stime','$etime','1')";
		
			echo '<hr>';
			
			$event_insert_id = rand(1,100);
				
			if($event_insert_id > 0){
			
				foreach ($dates as $date){
					echo $dateQry = "INSERT INTO `event_dates` (`id`, `event_id`, `event_date`) VALUES (NULL, '$event_insert_id', '$date')" . "<br>";
					echo "INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '$stime', '$etime', '$date_insert_id')" . "<br>";
				}	
			}
		}
		
	}
	
	?>
	
		<h1><?php echo $total_added;?> Records added.</h1>
<?php 
		
		include_once("fb_categorization.php");
			
	} 
?>

<form method="post" enctype="multipart/form-data" action="">
<input name="url" type="text" value="webcal://www.facebook.com/ical/u.php?uid=1387282969&key=AQDMMkgMpIYk_C4u" size="100" />
<input name="submit" type="submit" value="Submit" />
</form>

</div>
<?php  include_once('footer.php')?>
