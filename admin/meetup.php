<?php

include_once('database.php');
include_once("xmlparser.php");
include_once('header.php'); 

$fl_cities_q = "select * from fl_cities";
$fl_cities_res = mysql_query($fl_cities_q);

?>
<div class="bc_heading">
<div>MeetUp</div>
</div>
<div id="outer">
	<form name="flcities" id="flcities" action="" method="post">
		<div id="form">
			<div class="label"><strong> City :</strong></div>
			<div class="input">
				<select name="cities">
					<option value=""> Select City </option>
					<?php while($fl_cities_r = mysql_fetch_assoc($fl_cities_res)){ ?>
						<option value="<?php echo DBout($fl_cities_r['city']);?>"><?php echo DBout($fl_cities_r['city']);?></option>
					<?php }?>
				</select>
			</div>
			<div id="submitBtn">	
		<input type="button" value="Collect Events" onclick="document.flcities.submit()" class="addBtn">
		</div>
				
	</div>
   </form>
</div>


<div style="padding:20px">

<?php
ob_start();

if(isset($_POST['cities']) && $_POST['cities'] != ""){
	
	mysql_query("TRUNCATE TABLE venues_temp");
	mysql_query("TRUNCATE TABLE venue_events_temp");
	
	echo '<br><br><div id="loading" style="padding:20px; text-align:center"><img src="../images/loading.gif" /><br><br>Collecting data for ' . $_POST['cities'] . '</div>';

	ob_flush();
	ob_end_flush();
	ob_clean();
	
    $city2 = trim($_POST['cities']);


	$uptodate 	= date("mdY");
	// https://api.meetup.com/2/open_events.xml?status=upcoming&key=6a74e2b3771705b7b466f47557949&after=08012011&city=ORLANDO&state=FL&country=US
	// https://api.meetup.com/2/open_events.xml?status=upcoming&key=6a74e2b3771705b7b466f47557949&after=07092011&city=ORLANDO&state=FL&country=US
	$url 		= 'https://api.meetup.com/2/open_events.xml?key=6a74e2b3771705b7b466f47557949&city='. strtoupper($city2) .'&state=FL&country=US&after='. $uptodate .'&status=upcoming';
	
	//$url 		= 'meetup_xml.xml';
	
	$xml 		= simplexml_load_file($url);
	$data 		= $xml->head;
	$total 		= $data->count;
	
	$items 	= $xml->items->item;
	$total_added = 0;
	foreach ($items as $item)
	{
		$event_id	= DBin($item->id);
		$name 		= DBin($item->name);
		$state		= DBin($item->venue->state);
		$lat		= DBin($item->venue->lat);
		$lon 		= DBin($item->venue->lon);
		$address	= DBin($item->venue->address_1);
		$venu_id	= DBin("MP-".$item->id);
		$descr 		= DBin($item->description);
		$fee 		= DBin($item->fee);
		$owner 		= DBin($item->organizer_name);
		$date		= (float)$item->time;
		$city 		= DBin($item->venue->city);
		$phone 		= DBin($item->venue->phone);
		$image 		= DBin($item->photo_url);
		$zip 		= DBin($item->venue->zip);
		$venu_name	= DBin($item->venue->name);

		$date = ceil($date/1000);
		$bc_seo_name	=	make_seo_names($name,"events","seo_name","");
		$start_date = date("Y-m-d",$date);
		$start_time = date("g:i A",$date);
		$bc_start_time = date("H:i:s",$date);
		

		if ($address != '') 
		{
			$event_query = "insert ignore into events (event_source,source_id,event_name,seo_name,event_description,event_start_time,event_image,added_by,event_cost,event_status,category_id,subcategory_id,zipcode) 
								VALUES ('MeetUp','$event_id','$name','$bc_seo_name','$descr','$start_time','$image','$owner','$fee','1',18,35,'$zip')";
			
			mysql_query($event_query) ;
			$event_insert_id = mysql_insert_id();
				
			if( $event_insert_id > 0){
				$total_added++;
				
				$venue_query = "insert ignore into venues_temp (source_id,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,phone)
								 VALUES ('$venu_id','$venu_name','$address','$city','$state','$zip','$lon','$lat','$phone') ";
				mysql_query($venue_query) ;
				$venue_insert_id = mysql_insert_id();
				if($venue_insert_id == 0) {
					$dup_res = mysql_query("select id from venues where source_id = '$venu_id'");
					if( $dup_r = mysql_fetch_assoc($dup_res) )
						$venue_id = $dup_r['id'];
				} 
							
				$venue_event_query = "insert into venue_events_temp (venue_id, event_id) values ('$venue_insert_id','$event_insert_id')";
				mysql_query($venue_event_query) ;
			}
				
			if($event_insert_id > 0){
				$event_dates_query = "insert into event_dates (event_id, event_date) values ('$event_insert_id', '$start_date')";
				mysql_query($event_dates_query);
				$date_id = mysql_insert_id();
				
				mysql_query("INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '$bc_start_time', '', '$date_id');");
			}
		}	
	}
	
	// update event venues
	include_once("transfer_live.php");
	
}

?>
</div>
<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added.</h1>';
</script>

<?php  include_once('footer.php')?>