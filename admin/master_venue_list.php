<?php

include_once('database.php');
include_once('header.php'); 
?>

<div class="bc_heading">
	<div>Refresh Master Venue List</div>
</div>

<?php if ( $_GET['start'] == '' ) { ?>

<div style="padding:20px">
	<a href="?start=1"><strong>Start Process</strong></a>
	<br>
	This may take some time.
</div>

<?php } else { ?>

<div style="padding:20px">
<?php

	// add space at the end of all addresses
	$sql = "update venues set venue_address=CONCAT(venue_address,' ') ";
	mysql_query($sql);
	
	// Step 1:

	$sql = "update venues set venue_address=REPLACE(venue_address,'St.','Street') WHERE venue_address LIKE '% St. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'St','Street') WHERE venue_address LIKE '% St %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Ste.','Street') WHERE venue_address LIKE '% Ste. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Ste','Street') WHERE venue_address LIKE '% Ste %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Dr','Drive') WHERE venue_address LIKE '% Dr %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Dr.','Drive') WHERE venue_address LIKE '% Dr. %'";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Rd.','Road') WHERE venue_address LIKE '% Rd. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Rd','Road') WHERE venue_address LIKE '% Rd %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Ave','Avenue') WHERE venue_address LIKE '% Ave %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Ave.','Avenue') WHERE venue_address LIKE '% Ave. %'";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Blvd.','Boulevard') WHERE venue_address LIKE '% Blvd. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Blvd','Boulevard') WHERE venue_address LIKE '% Blvd %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Bldg','Building') WHERE venue_address LIKE '% Bldg %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Bldg.','Building') WHERE venue_address LIKE '% Bldg. %'";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Trl','Trail') WHERE venue_address LIKE '% Trl %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'Trl.','Trail') WHERE venue_address LIKE '% Trl. %'";
	mysql_query($sql);
	
	$sql = "update venues set venue_address=REPLACE(venue_address,'E.','East') WHERE venue_address LIKE '% E. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'E','East') WHERE venue_address LIKE '% E %' ";
	mysql_query($sql);
	
	$sql = "update venues set venue_address=REPLACE(venue_address,'W.','West') WHERE venue_address LIKE '% W. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'W','West') WHERE venue_address LIKE '% W %' ";
	mysql_query($sql);
	
	$sql = "update venues set venue_address=REPLACE(venue_address,'S.','South') WHERE venue_address LIKE '% S. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'S','South') WHERE venue_address LIKE '% S %' ";
	mysql_query($sql);
	
	
	$sql = "update venues set venue_address=REPLACE(venue_address,'N.','North') WHERE venue_address LIKE '% N. %' ";
	mysql_query($sql);
	$sql = "update venues set venue_address=REPLACE(venue_address,'N','North') WHERE venue_address LIKE '% N %' ";
	mysql_query($sql);
	
	
	// Step 2: Match Lat/lng Groups
	$sql = "select * from venue_temp where lat!='' AND lng!=''";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) {
		$lat = $row['lat'];
		$lng = $row['lng'];
		
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
						mysql_query("update venue_events set venue_id=$default_venu_id where venue_id='". $row1['id'] ."'");
						mysql_query("update venue_images set venue_id=$default_venu_id where venue_id='". $row1['id'] ."'");
						mysql_query("delete from venues where id='". $row1['id'] ."'");
					}	
				}	
			}
		}
	}
?>
<h1>Completed</h1>
</div>

<?php 
} 
include_once('footer.php');
?>