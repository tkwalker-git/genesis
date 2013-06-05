<?php

/* 
	Notes: This script read .ics file and grab the event ids then get the details of each event
*/

include_once("database.php");
include_once('api/facebook.php');
include_once('header.php'); 



if($_GET['del']){
	$del_id = $_GET['del'];
	
	$re = mysql_query("select * from `data_collection_links`");
	if(mysql_num_rows($re) != 1){
	
	$res = mysql_query("delete from `data_collection_links` where `id`='$del_id'");
	if($res){
		$msg = '<span style="color:#ff0000">Entry deleted successfully</span>';
	}
	else{
		$msg = '<span style="color:#ff0000"><strong>Error:</strong> Please try again later</span>';
	}
	}
	else{
		$msg = '<span style="color:#ff0000"><strong>Error:</strong> You cannot delete this entry</span>';
	}

}
?>

<div class="bc_heading">
  <div>FaceBook Events</div>
</div>
<div style="padding:20px">
  <?php
  
if ( isset($_POST['submit'])) {

	$facebook = new Facebook(array(
						'appId'      => FACEBOOK_APP_ID,
						'secret'     => FACEBOOK_SECRET,
						'cookie'     => true
	));
	
	$id = $_POST['ids'];
	for($i=0;$i < count($id);$i++){
		
		$idf = $id[$i];
		$res = mysql_query("select * from `data_collection_links` where `id`='$idf'");
		while($row = mysql_fetch_array($res)){
		$url = $row['link'];
		if($url!=''){
		
	$ur = str_replace("webcal://","http://",$url);

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
		$bc_seo_name	=	make_seo_names($name,"events","seo_name","");
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
		
		if ( $value['start_time'] > $today  ) {
			
			//echo $value['start_time'] . ' - ' . $today;
			//echo '<br>';
			$event_query = "insert ignore into events (event_source,source_id,event_name,seo_name,event_description,event_image,added_by,event_cost,event_status,event_start_time,event_end_time,is_expiring ) 
									VALUES ('Facebook','$eid','$name','$bc_seo_name','$desc','$image','$host','','0','$stime','$etime','1')";
		
			
			mysql_query($event_query) ;
			$event_insert_id = mysql_insert_id();
				
			if( $event_insert_id > 0){
				
				$total_added++;
				
				$venue_insert_id = getExistingVenueId($venu_name);
				
				if ( $venue_insert_id == 0 ) {
					$venue_query = "insert ignore into venues (source_id,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,phone)
									 VALUES ('$eid','$venu_name','$address','$city','$state','$zip','$lng','$lat','') ";
					mysql_query($venue_query) ;
					$venue_insert_id = mysql_insert_id();
				}
				
				$venue_event_query = "insert into venue_events (venue_id, event_id) values ('$venue_insert_id','$event_insert_id')";
				mysql_query($venue_event_query) ;
			}
				
			if($event_insert_id > 0){
				foreach ($dates as $date){
					$dateQry = "INSERT INTO `event_dates` (`id`, `event_id`, `event_date`) VALUES (NULL, '$event_insert_id', '$date')";
					$dateRes = mysql_query($dateQry);
					$date_insert_id = mysql_insert_id();
					if($date_insert_id > 0){
						mysql_query("INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '$stime', '$etime', '$date_insert_id')");
					}
				
				}	
			}
		}
		
	}
	} // end url!=''
		} // end for loop
	}
	
	?>
  <h1><?php echo $total_added;?> Records added.</h1>
  <?php 
		
		include_once("fb_categorization.php");
			
	} 
?>


<!--
<tr bgcolor="<?php echo $bg; ?>">
        <td width="3%"><input type="checkbox" />
        </td>
        <td width="10%"><strong><?php echo $row['name']; ?></strong> </td>
        <td width="81%"><?php echo $row['link']; ?> </td>
		<td width="6%" align="center"><a href="#"><img src="images/delete.png" /></a></td>
</tr>
-->
	  
	  
	  <?php echo $msg; ?>
  <form method="post" enctype="multipart/form-data" action="">
    <table cellpadding="8" cellspacing="1" width="100%">
	<tr><td colspan="4" align="right"><a href="add_data_collection.php"><strong>Add New</strong></a></td></tr>
      <?php
$res = mysql_query("select * from `data_collection_links` ORDER BY `id` ASC");
$bg = '#fff';
while($row = mysql_fetch_array($res)){
if($bg=='#fff'){
	$bg = '#efefef';
}
else{
	$bg = '#fff';
}

?>
      <tr bgcolor="<?php echo $bg; ?>">
        <td width="4%"><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>" />
        </td>
        <td width="13%"><strong><?php echo $row['name']; ?></strong> </td>
        <td width="77%"><?php echo $row['link']; ?> </td>
		<td width="6%" align="center"><a onclick="return confirm('Are you sure you want delete?');" href="?del=<?php echo $row['id']; ?>"><img src="images/delete.png" title="delete" /></a></td>
      </tr>
      <?php } ?>
	  
	  <tr><td colspan="4" align="center"><br />
<input type="submit" name="submit" value="Submit"  /></td></tr>
    </table>
    <br />
  
  </form>
</div>
<?php  include_once('footer.php')?>
