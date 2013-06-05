<?php
	
include_once('database.php');
include_once('header.php');
include_once("xmlparser.php");
$fl_cities_q = "select * from fl_cities";
$fl_cities_res = mysql_query($fl_cities_q);

?>

<div class="bc_heading">
<div>EventFull</div>
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
			<!--<input type="submit" value="submit" />-->
			</div>
		</form>
		<br>
		This process can take lot of time depending on the number of events... 
</div>

<div style="padding:20px">

<?php

ob_start();

if(isset($_POST['cities']) && $_POST['cities'] != ""){
	
	mysql_query("TRUNCATE TABLE venues_temp");
	mysql_query("TRUNCATE TABLE venue_events_temp");
	
	echo '<br><br><div id="loading" style="padding:20px; text-align:center"><img src="'.IMAGE_PATH.'loading.gif" /><br><br>Collecting data for ' . $_POST['cities'] . '</div>';

	ob_flush();
	ob_end_flush();
	ob_clean();
	
    $city2 = trim($_POST['cities']);
	//echo $city;
	
	$url2 		= "http://api.eventful.com/rest/events/search?app_key=CSxRkR9vtmFKQTsX&location=$city2,fl&date=Future&sort_orde=date&page_size=1";
	$nom2 		= get_url_contents($url2);
	
	$xmlparser2	= new xmlparser();
	$data2		= $xmlparser2->GetXMLTree($nom2);
	//print_r($data);
	
	$total_records 	= $data2['SEARCH']['0']['TOTAL_ITEMS'][0]['VALUE'];
	$rpp			= $data2['SEARCH']['0']['PAGE_SIZE'][0]['VALUE'];
	$pagecount 		= $data2['SEARCH']['0']['PAGE_COUNT'][0]['VALUE'];
	$pagenum 		= $data2['SEARCH']['0']['PAGE_NUMBER'][0]['VALUE'];
	
	$pages = ceil($total_records/100);
	
	unset($xmlparser2);
	$xmlparser2 = NULL;
	
	$total_added = 0;
	
	for ( $a=1; $a<=$pages;$a++) {
	
		$url 		= "http://api.eventful.com/rest/events/search?app_key=CSxRkR9vtmFKQTsX&location=$city2,fl&date=Future&sort_orde=date&page_size=100&page_number=".$a;
		$nom 		= get_url_contents($url);
		
		$xmlparser	= new xmlparser();
		$data		= $xmlparser->GetXMLTree($nom);
		
		$locations_size = sizeof($data['SEARCH']['0']['EVENTS']['0']['EVENT']);
		
	
		for($j=0; $j<$locations_size; $j++){
			
			$venu_lat			= '';
			$venu_lon			= '';
			
			$event_id 			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['ATTRIBUTES']['ID']);
			$name				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['TITLE']['0']['VALUE']);
			$url				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['URL']['0']['VALUE']);
			$descr				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['DESCRIPTION']['0']['VALUE']);
			$address			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['ADDRESS']['0']['VALUE']);
			$start				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['START_TIME']['0']['VALUE']);
			$stop				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['STOP_TIME']['0']['VALUE']);
			
			$venu_id			= DBin('EF-'.$data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['VENUE_ID']['0']['VALUE']);
			$venu_name			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['VENUE_NAME']['0']['VALUE']);
			$venu_address		= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['VENUE_ADDRESS']['0']['VALUE']);
			$venu_city			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['CITY_NAME']['0']['VALUE']);
			$venu_state			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['REGION_ABBR']['0']['VALUE']);
			$venu_zip			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['POSTAL_CODE']['0']['VALUE']);
			$venu_lat			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['LATITUDE']['0']['VALUE']);
			$venu_lon			= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['LONGITUDE']['0']['VALUE']);
			
			$owner				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['OWNER']['0']['VALUE']);
			
			//$image				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['IMAGE']['0']['URL']['0']['VALUE']);
			$image				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['IMAGE']['0']['MEDIUM'][0]['URL']['0']['VALUE']);
			
			$owner  = str_replace("_"," ",$owner);
			
			$start_date = date("Y-m-d",strtotime($start));
			$stop_date  = date("Y-m-d",strtotime($stop));
			
			$start_time = date("g:i A",strtotime($start));
			$stop_time  = date("g:i A",strtotime($stop));
			$addDate = date("Y-m-d");
			//	echo $event_id . ' | ' . $name . ' | ' . $venu_name . ' | ' . $venu_address . ' | ' . $start_time . ' | ' . $stop_time . ' <br> '  ;
			
			/*
			$url3 = 'http://api.evdb.com/rest/events/tags/list?app_key=CSxRkR9vtmFKQTsX&id=' . $event_id ;
			$xml3 = simplexml_load_file($url3);
			
			$tags 	= $xml3->tags->tag;
			$tgs	= '';	
			foreach ($tags as $tag) {
				$tg = $tag->title;
				if ( $tg != '' ) {
					mysql_query("insert ignore into event_tags (tag) VALUES ('$tg')");
					$tgs .= ',' . $tg;
				}	
			}
			*/
			
			$event_query = "insert ignore into events 
				(event_source,source_id,event_name,event_description,event_start_time,event_end_time,event_image,publishdate,added_by,tags,event_status,zipcode) 
				VALUES ('EventFull','$event_id','$name','$descr','$start_time','$stop_time','$image','$addDate','$owner','$tgs','0','$venu_zip')";
			 mysql_query($event_query) ;
			$event_insert_id = mysql_insert_id();
			$total_added++;
			
			if($venu_id != "EF-" && $event_insert_id > 0){
				
				if ( $owner != '' )
					mysql_query("insert ignore into event_hosts VALUES(NULL,'". $event_id ."','". $event_insert_id ."','". $owner ."')");
				
				$ex_venue = matchVenueLatLng($venu_lat,$venu_lon);
				
				if ( $ex_venue > 0 ) {
					$venue_insert_id = $ex_venue;
				} else {
				
					$venue_query = "insert ignore into venues_temp (source_id,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,add_date)
									 VALUES ('$venu_id','$venu_name','$venu_address','$venu_city','$venu_state','$venu_zip','$venu_lon','$venu_lat','$addDate') ";
					mysql_query($venue_query) ;
					if(mysql_insert_id() == 0){
						$dup_res = mysql_query("select id from venues_temp where source_id = '$venu_id'");
						if($dup_r = mysql_fetch_assoc($dup_res)){
							$venue_insert_id = $dup_r['id'];
						}
					} elseif($event_insert_id > 0){				
						$venue_insert_id = mysql_insert_id();
					}
				}
				
				$venue_event_query = "insert into venue_events_temp (venue_id, event_id) values ('$venue_insert_id','$event_insert_id')";
				mysql_query($venue_event_query) ;
			}
			
			if($event_insert_id > 0){
				$event_dates_query = "insert into event_dates (event_id, event_date) values ('$event_insert_id', '$start_date')";
				mysql_query($event_dates_query) ;
			}
		}
		
		unset($xmlparser);
		$xmlparser = NULL;
	}
	
	// update event venues
	include_once("transfer_live.php");
	
}		
?>

</div>


<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added. </h1><br><br><p>All newely added events will be without tags. Please <a href="eventfull_tags.php">collect tags</a> here and then run Categorization script to auto categorize events.</p>';
</script>
<?php  include_once('footer.php')?>