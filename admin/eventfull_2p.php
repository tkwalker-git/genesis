<?php
	
include_once('database.php');
include_once('header.php');
include_once("xmlparser.php");

$page = (int)$_GET['pg'];

if ( $page <= 0 )
	die('Page is not specified.');
	
	for ( $a=0; $a<2;$a++) {
		
		$p = $page + $a;
		
		$url 		= "http://api.eventful.com/rest/events/search?app_key=CSxRkR9vtmFKQTsX&location=orlando,fl&date=Future&sort_orde=date&page_size=100&page_number=".$p;
		$nom 		= get_url_contents($url);
		
		$xmlparser	= new xmlparser();
		$data		= $xmlparser->GetXMLTree($nom);
		
		$locations_size = sizeof($data['SEARCH']['0']['EVENTS']['0']['EVENT']);
		$total_added = 0;
	
		for($j=0; $j<$locations_size; $j++){
	
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
			$image				= DBin($data['SEARCH']['0']['EVENTS']['0']['EVENT'][$j]['IMAGE']['0']['MEDIUM'][0]['URL']['0']['VALUE']);
			
			$owner  = str_replace("_"," ",$owner);
			
			$start_date = date("Y-m-d",strtotime($start));
			$stop_date  = date("Y-m-d",strtotime($stop));
			
			$start_time = date("g:i A",strtotime($start));
			$stop_time  = date("g:i A",strtotime($stop));
			$addDate = date("Y-m-d");
			
			$url3 = 'http://api.evdb.com/rest/events/tags/list?app_key=CSxRkR9vtmFKQTsX&id=' . $event_id ;
			$xml3 = simplexml_load_file($url3);
			
			$tags 	= $xml3->tags->tag;
			$tgs	= '';
			if ( is_array($tags) ) {
				foreach ($tags as $tag) {
					$tg = $tag->title;
					if ( $tg != '' ) {
						mysql_query("insert ignore into event_tags (tag) VALUES ('$tg')");
						$tgs .= ',' . $tg;
					}	
				}
			}
						
			$event_query = "insert ignore into events 
				(event_source,source_id,event_name,event_description,event_start_time,event_end_time,event_image,publishdate,added_by,tags) 
				VALUES ('EventFull','$event_id','$name','$descr','$start_time','$stop_time','$image','$addDate','$owner','$tgs')";
			 mysql_query($event_query) or die("event_query_error".mysql_error());
			$event_insert_id = mysql_insert_id();
			
			if($venu_id != "EF-" && $event_insert_id > 0){
				$total_added++;
				
				
				if ( $owner != '' )
					mysql_query("insert ignore into event_hosts VALUES(NULL,'". $event_id ."','". $event_insert_id ."','". $owner ."')");
				
				$venue_query = "insert ignore into venues
				 (source_id,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,add_date)
				 VALUES ('$venu_id','$venu_name','$venu_address','$venu_city','$venu_state','$venu_zip','$venu_lon','$venu_lat','$addDate') ";
				 mysql_query($venue_query) ;
					if(mysql_insert_id() == 0){
							  
							  $dup_res = mysql_query("select id from venues where source_id = '$venu_id'");
							  if($dup_r = mysql_fetch_assoc($dup_res)){$venue_insert_id = $dup_r['id'];}
							
						}elseif($event_insert_id > 0){				
							$venue_insert_id = mysql_insert_id();
						}
				
				$venue_event_query = "insert into venue_events (venue_id, event_id) values ('$venue_insert_id','$event_insert_id')";
				mysql_query($venue_event_query);
			}
			if($event_insert_id > 0){
				$event_dates_query = "insert into event_dates (event_id, event_date) values ('$event_insert_id', '$start_date')";
				mysql_query($event_dates_query);
			}
		}
		
		unset($xmlparser);
		$xmlparser = NULL;
		
	}


include_once('footer.php');		
?>
