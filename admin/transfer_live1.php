<?php

	include_once('database.php');
	
	// Step 2: Match Names
	$sql = "select * from venues_temp where venue_name != ''";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) {
		$ven_id		= $row['id'];
		$ven_name 	= $row['venue_name'];
		
		$sql1 = "select * from venues where venue_name='". $ven_name ."'";
		$res1 = mysql_query($sql1);
		if ( mysql_num_rows($res1) > 0 ) {
			if ( $row1 = mysql_fetch_assoc($res1) ) {
				$venue_id = $row1['id'];
				mysql_query("insert into venue_events select NULL,event_id,'". $venue_id ."' as venue_id from venue_events_temp where venue_id='". $ven_id ."'");
				mysql_query("DELETE from venue_events_temp where venue_id='". $ven_id ."'");
				//mysql_query("DELETE from venues_temp where id='". $ven_id ."'");
			}
		}
	}
	
	// Step 3: Match Address
	$sql = "select * from venues_temp where venue_address != ''";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) {
		$ven_id			= $row['id'];
		$ven_address 	= $row['venue_address'];
		
		$sql1 = "select * from venues where venue_address='". $ven_address ."'";
		$res1 = mysql_query($sql1);
		if ( mysql_num_rows($res1) > 0 ) {
			if ( $row1 = mysql_fetch_assoc($res1) ) {
				$venue_id = $row1['id'];
				mysql_query("insert into venue_events select NULL,event_id,'". $venue_id ."' as venue_id from venue_events_temp where venue_id='". $ven_id ."'");
				mysql_query("DELETE from venue_events_temp where venue_id='". $ven_id ."'");
				//mysql_query("DELETE from venues_temp where id='". $ven_id ."'");
			}
		}
	}
	
	// Step 4: Match Lat/lng Groups with 4 significant digits
	$sql = "select * from venues_temp where venue_lng!='' AND venue_lat!=''";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) {
		$lat 	= $row['venue_lat'];
		$lng 	= $row['venue_lng'];
		$ven_id	= $row['id'];
		$sql1 = "select * from venues where substring(venue_lat,1,7)='". substr($lat,0,7) ."' AND substring(venue_lng,1,8)='". substr($lng,0,8) ."'
				ORDER BY CASE 
				WHEN venue_type != '' AND venue_address != '' AND venue_city != '' AND venue_zip != '' AND venue_state != '' THEN 0 
				WHEN venue_address != '' AND venue_city != '' AND venue_zip != '' AND venue_state != '' THEN 1 
				WHEN venue_address != '' AND venue_city != '' AND venue_state != '' AND venue_zip = '' THEN 2 
				WHEN venue_address != '' AND venue_city != '' AND venue_state = '' AND venue_zip = '' THEN 3 
				WHEN venue_address != '' AND venue_city = '' AND venue_state = '' AND venue_zip = '' THEN 4 
				WHEN venue_address = ''  THEN 5 ELSE 6 END";
		$res1 = mysql_query($sql1);
		$tot  = mysql_num_rows($res1);
		$k=0;
		while ( $row1 = mysql_fetch_assoc($res1) ) {
			if ( $tot > 1 ) {
				$k++;
				if ( $k == 1 ) {
					$default_venu_id = $row1['id'];
				} else {
					if ( $default_venu_id > 0 ) {
						mysql_query("insert into venue_events select NULL,event_id,'". $default_venu_id ."' as venue_id from venue_events_temp where venue_id='". $ven_id ."'");
						mysql_query("DELETE from venue_events_temp where venue_id='". $ven_id ."'");
						//mysql_query("DELETE from venues_temp where id='". $ven_id ."'");
					}	
				}	
			}
		}
	}
	
	//mysql_query("insert into venues select NULL,source_id,venue_type,venue_name,venue_address,venus_radius,venue_lng,venue_lat,add_date,status,del_status,venue_city,venue_state,venue_country,venue_zip,categories,tags,averagerating,phone,neighbor,image from venues_temp");
	//mysql_query("insert into venue_events select* from venue_events_temp");
	
?>