<?php
set_time_limit(500);
include_once('database.php');
include_once('header.php');


function getExistingVenueId2($name)
{
	$sql  = "SELECT id FROM venues WHERE venue_name  like '%". $name ."%'";
	$res  = mysql_query($sql);
	if ( mysql_num_rows($res) > 0 ) {
		if ( $row = mysql_fetch_assoc($res) ) {
			return $row['id'];
		}	
	}
	
	return 0;
}

function getGeoLocation2($address)
{
	$prepAddr = str_replace(' ','+',$address.", Orlando,FL");
    $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false'); 
    $output= json_decode($geocode);
	$latlng = array();
    $latlng['lat'] = $output->results[0]->geometry->location->lat;
    $latlng['lng'] = $output->results[0]->geometry->location->lng;
	return $latlng;
}


function dates_range($date1, $date2){
  if ($date1<$date2){ 
    $dates_range[]=$date1; 
    $date1=strtotime($date1); 
    $date2=strtotime($date2); 
    while ($date1!=$date2){
      $date1=mktime(0, 0, 0, date("m", $date1), date("d", $date1)+1, date("Y", $date1)); 
      $dates_range[]=date('Y-m-d', $date1); 
    }}
  return $dates_range; 
}

?>

<div class="bc_heading">
<div>Orlando Slice</div>
</div>
<div id="outer">
		
		<form name="flcities" id="flcities" action="" method="post">
		<div id="form">
			<div id="submitBtn">	
				<input type="submit" name="submit" value="Collect Events" class="addBtn" />
			</div>
			<!--<input type="submit" value="submit" />-->
			</div>
		</form>
		
</div>

<div style="padding:20px">

<?php
	if($_POST['submit']){
	$total_added	= 0;
	echo '<br><br><div id="loading" style="padding:20px; text-align:center"><img src="'.IMAGE_PATH.'loading.gif" /><br><br>Collecting data from Orlando Slice</div>';
	
		
		function pageLink($pageLink){
						$web 		= file_get_contents ($pageLink);
						$main		= 	explode('body_events_main',$web);
						if(strstr($main[1],"pagination smallpagination")){
							$main2		= 	explode('pagination smallpagination',$main[1]);
							if(strstr($main2[1],'<li class="right">')){
								$nextPages	=	explode('<li class="right">',$main2[1]);
								$nextPage	=	explode('</li>',$nextPages[1]);
								if (preg_match('/href="([^"]*)"/i', $nextPage[0] , $regs))
									$nextPageLink = $regs[1];
							}
						}
						$main3		= explode('class="tb"',$main2[0]);
						for ($i=1;$i<count($main3);$i++){
							$h3		= explode("<h3>", $main3[$i]);
							$h3		= explode("</h3>", $h3[1]);
							if (preg_match('/href="([^"]*)"/i', $h3[0] , $regs))
								$links[] = $regs[1];
						}
						if(isset($nextPageLink)){
							$links[] = pageLink($nextPageLink);
						}
						return $links;	
		 } // END FUNCTION
		
		
		function flatten_array($mArray) {
			$sArray = array();
			foreach ($mArray as $row) {
				if ( !(is_array($row)) ) {
					if($sArray[] = $row){
					}
				} else {
					$sArray = array_merge($sArray,flatten_array($row));
				}
			}
			return $sArray;
		}
		$links = pageLink('http://www.orlandoslice.com/events');
		$allLinks = flatten_array($links);
		
		if(is_array($allLinks)){
			foreach($allLinks as $link){
				$innerPage	= file_get_contents($link);
				$innerBox	= explode("column1",$innerPage);
				$innerBox	= explode("pagination",$innerBox[1]);
				$eTitle		= explode("<h1>", $innerBox[0]);
				$eTitle		= explode("</h1>", $eTitle[1]);
				$eTitle		= $eTitle[0]; ////////////////////////////////////////// EVENT TITLE
				
				$eImage		= explode('pad5',$innerBox[0]);
				$eImage		= explode('</div>',$eImage[1]);
				$eImage		= explode('src="',$eImage[0]);
				$eImage		= explode('"',$eImage[1]);
				$eImage		= $eImage[0]; ////////////////////////////////////////// EVENT IMAGE
				
				$eDetails	= explode('<p>',$innerBox[0]);
				$eDetails	= explode('</p>',$eDetails[1]);
				$eDetails	= $eDetails[0];
			
				$eDateTime	= explode('<br />', $eDetails);
				$eDateTimes	= str_replace('Time: ','',$eDateTime[0]);
				
				if(strstr($eDateTimes,"from")){
					$eDateTimes		= explode('from',$eDateTimes);
					$eStartDate		= strip_tags($eDateTimes[0]);		
					$eStartEndTime	= explode('to',$eDateTimes[1]);
					$eStartTime		= trim($eStartEndTime[0]);
					$eEndTime		= trim($eStartEndTime[1]);
					$eEndDate		= '';
				}
				elseif(strstr($eDateTimes,"at")){
					$eDateTimes		= explode('to',$eDateTimes);
					$eStartDateTimes2	= strip_tags($eDateTimes[0]);
					if(strstr($eStartDateTimes2,"at")){
						$eStartDateTimes2	= explode('at',$eStartDateTimes2);
						$eStartDate			= strip_tags($eStartDateTimes2[0]);
						$eStartTime			= trim($eStartDateTimes2[1]);
						$eEndDateTimes2		= strip_tags($eDateTimes[1]);
						$eEndDateTimes2		= explode('at',$eEndDateTimes2);
						$eEndDate			= strip_tags($eEndDateTimes2[0]);
						$eEndTime			= trim($eEndDateTimes2[1]);		
						}
					else{
						$eStartDate			= strip_tags($eDateTimes[0]);
						$eEndDate			= strip_tags($eDateTimes[1]);
						$eStartTime			= '';
						$eEndTime			= '';
					}
				}
		
				$eventStartDate	=	$eStartDate;		////// EVENT START DATE
				$eventEndDate	=	$eEndDate;			////// EVENT END DATE
				$eventStartTime	=	$eStartTime;		////// EVENT START TIME
				$eventEndTime	=	$eEndTime;			////// EVENT END TIME
				$eLocation		= strip_tags(str_replace('Location:','',$eDateTime[1]));  /// EVENT LOCATION
				
				foreach ($eDateTime as $otherInfo){
					if(strstr($otherInfo,"Location"))
						$eLocation		= strip_tags(str_replace('Location:','',$eDateTime[1]));  /// EVENT LOCATION
					if(strstr($otherInfo,"Street"))
						$eStreet		= strip_tags(str_replace('Street:','',$otherInfo));  /// EVENT STREET
					if(strstr($otherInfo,"City/Town"))
						$eCityTown		= strip_tags(str_replace('City/Town:','',$otherInfo));  /// EVENT CITY/TOWN	
					if(strstr($otherInfo,"Website or Map")){
						if (preg_match('/href="([^"]*)"/i', $otherInfo , $regs))
									$eWebsite = $regs[1];
						}
					if(strstr($otherInfo,"Phone"))
						$ePhone			= strip_tags(str_replace('Phone:','',$otherInfo));  /// EVENT PHONE
					if(strstr($otherInfo,"Event Type"))
						$eType			= strip_tags(str_replace('Event Type:','',$otherInfo));  /// EVENT TYPE
					if(strstr($otherInfo,"Organized By"))
						$eOrganizedBy	= strip_tags(str_replace('Organized By:','',$otherInfo));  /// EVENT Organized By
				}
				
				$forDescp		= explode('xg_user_generated">', $innerBox[0]);
				$forDescp		= explode('</div>', $forDescp[1]);
				$eDescription	= DBin($forDescp[0])."<br /><strong>Powerd by Orlando Slice</strong><br />"; /// EVENT DESCRIPTION	
				
				$eDescription	.= DBin("<img src=http://www.eventgrabber.com/images/OrlandoSliceLogo.jpg />");
				
				
				$event_source	= "Orlando Slice";
				$source_id		= "OS-".rand(); 
				
				$seo_link	= explode("/",$link);
				$seo_name	= end($seo_link);
			
			$eAlready = getSingleColumn("id","select * from `events` where `seo_name`='$seo_name'");
			
			if($eAlready ==''){
				$venue_name		= trim($eLocation);
				$venue_address	= trim($eStreet);
				$venue_city		= trim($eCityTown);
				$venue_state	= "";	
				
				//	include("../factual/test.php");
				
				$res = mysql_query("INSERT INTO `events` (`id`, `event_source`, `source_id`, `fb_event_id`, `userid`, `category_id`, `subcategory_id`, `event_name`, `seo_name`, `musicgenere_id`, `event_start_time`, `event_end_time`, `event_description`, `event_cost`, `event_image`, `event_video`, `start_campaign`, `end_campaign`, `event_sell_ticket`, `event_age_suitab`, `event_status`, `publishdate`, `averagerating`, `tags`, `modify_date`, `del_status`, `added_by`, `men_preferred_age`, `women_preferred_age`, `occupation_target`, `video_name`, `video_embed`, `event_score`, `repeat_event`, `repeat_freq`, `privacy`, `pending_approval`, `type`, `featured`, `free_event`, `event_type`, `is_expiring`, `like`, `dislike`, `view`, `alter`, `alter_url`,`zipcode`) 
				VALUES 
				(NULL, 'Orlando Slice', '".$source_id."', NULL, '', '', '', '".$eTitle."', '".$seo_name."', '', '', '', '".$eDescription."', '', '".$eImage."', '', '', '', '', '', '0', '".date('Y-m-d')."', '', '".trim($eType)."', NULL, '0', 'Orlando Slice', '', '', '', '', '', '', '', '', '', '0', '', '0', '0', '0', '1', '', '', '', '', '','')");
				
				$event_id	= mysql_insert_id();
				
				if($event_id){
					$total_added++;
					
					////////////////// START VENUE WORK //////////////////
						
						$venue_id	= getExistingVenueId2($venue_name);
						if($venue_id){
							mysql_query("INSERT INTO `venue_events` (`id`, `venue_id`, `event_id`) VALUES (NULL, '$venue_id', '$event_id')");
						} // end if ($venue_id)
						else{
						
							$lat_lng	= getGeoLocation2($venue_name);
							$venue_lat	= $lat_lng['lat'];
							$venue_lng	= $lat_lng['lng'];
								
							$venue_id	= getSingleColumn("id","select * from `venues` where `venue_lng`!='' && `venue_lat`!='' && `venue_lng`='$venue_lng' && `venue_lat`='$venue_lat'");
							if($venue_id){
								mysql_query("INSERT INTO `venue_events` (`id`, `venue_id`, `event_id`) VALUES (NULL, '$venue_id', '$event_id')");
							} // end if ($venue_id)
							else{
								$source_id	=	'OS-'.rand();
								$add_date	= date('Y-m-d');
								mysql_query("INSERT INTO `venues` (`id`, `source_id`, `venue_type`, `venue_name`, `venue_address`, `venus_radius`, `venue_lng`, `venue_lat`, `add_date`, `status`, `del_status`, `venue_city`, `venue_state`, `venue_country`, `venue_zip`, `categories`, `tags`, `averagerating`, `phone`, `neighbor`, `image`, `setting_plan`, `user_id`) VALUES (NULL, '$source_id', '', '$venue_name', '$venue_address', '', '$venue_lng', '$venue_lat', '$add_date', '1', '0', 'Orlando', 'FL', '', '32801', '', '', '', '', '', '', '', '')");
								$venue_id = mysql_insert_id();
							}
						} // end else
						
						if($venue_id){
							$updateZip = getSingleColumn("venue_zip","select * from `venues` where `id`='$venue_id'");
							mysql_query("update `events` set `zipcode`='$updateZip' where `id`='$event_id'");
							}
					
					////////////////// END VENUE WORK //////////////////
					
					
					if($eventStartTime)
						$eventStartTime	= date('H:i:s',strtotime($eventStartTime));
					if($eventEndTime)
						$eventEndTime	= date('H:i:s',strtotime($eventEndTime));
					
					$eventStartDate	= date('Y-m-d',strtotime($eventStartDate));
					if($eventEndDate){
						$eventEndDate	= date('Y-m-d',strtotime($eventEndDate));
						$dates	= dates_range($eventStartDate,$eventEndDate);
						foreach($dates as $date){
							mysql_query("INSERT INTO `event_dates` (`id`, `event_id`, `event_date`, `expired`) VALUES (NULL, '".$event_id."', '".$date."', '0')");
							$date_id = mysql_insert_id();
							if($date_id)
								mysql_query("INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '".$eventStartTime."', '".$eventEndTime."', '".$date_id."')");
						} // end foreach
					
					} // end if $eventEndDate
					else{
						mysql_query("INSERT INTO `event_dates` (`id`, `event_id`, `event_date`, `expired`) VALUES (NULL, '".$event_id."', '".$eventStartDate."', '0')");
						$date_id = mysql_insert_id();
						if($date_id)
							mysql_query("INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '".$eventStartTime."', '".$eventEndTime."', '".$date_id."')");
					} // end else
		
				} // end if $event_id
		
			} // end if $eAlready == ""
			$venue_name		= "";
			$venue_address	= "";
			$venue_city		= "";
			
			} // end foreach
		} // end if(is_array($allLinks)){
	
	
	}

?>
</div>

<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added. </h1><br><br><p>All newely added events will be without Categorization. Please run Categorization script to auto categorize events.</p>';
</script>
<?php  include_once('footer.php')?>