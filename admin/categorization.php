<?php

include_once('database.php');
include_once('header.php'); 
?>

<div class="bc_heading">
	<div>Event Categorization</div>
</div>

<?php if ( $_GET['start'] == '' ) { ?>

<div style="padding:20px">
	<a href="?start=1"><strong>Start Categorization</strong></a>
	<br>
	This may take some time.
</div>

<?php } else { ?>

<div style="padding:20px">


<?php
	$i=0;
	
	$res2 = mysql_query("select id,tags,event_age_suitab from events where tags != '' AND (subcategory_id='' OR subcategory_id=0)");
	while ($rows= mysql_fetch_assoc($res2) ) {
		
		$score = array();
		
		$eid 		= $rows['id'];
		$tg			= $rows['tags'];
		$sage		= $rows['event_age_suitab'];
		$cat	= 0;
		$subcat = 0;
		
		if ( trim($tg) != '' ) {
			$eTags  = array();
			$tags	= substr($tg,1) ;
			$tags  	= str_replace(","," ",$tags) ;
			$tags  	= str_replace("_"," ",$tags) ;
			$eTags	= explode(" ",$tags);
	
			$q = "select * from sub_categories ";
			$res = mysql_query($q);
			
			while( $r=mysql_fetch_assoc($res) ) {
				
				$ttags	= $r['tag'];	
				if ( $ttags != '' ) {
					$cTags  = array();
					$cTags	= explode(",",$ttags);
					$cat	= $r['categoryid'];
					$subcat = $r['id'];
	
					foreach ($eTags as $et) 
						foreach ($cTags as $ct) 
							if ( strtolower($ct) == strtolower($et) )
								$score[$cat][$subcat]++;
								//echo $ct . ' = ' . $et . '<br>';
				}
				
			}
	
			
			if ( count($score) > 0 ) {
				$t=0;
				$sc=0;
				$ssc=0;
				foreach ( $score as $k => $v) 
					foreach ( $v as $k1 => $v1) 
						if ( $v1 > $t ) {
							$t = $v1;
							$sc  = $k; // selected Cat
							$ssc = $k1; // selected sub Cat
						}
				if ( $sc > 0 && $ssc > 0 ) {
					
					if ( $ssc == '24' )
						$age1 = ',event_age_suitab=6';
					else
						$age1 = '';	
					
					$hasVenue = attribValue('venue_events', 'venue_id', "where event_id='$eid' LIMIT 1");
					
					if ( ($sage > 0 || age1 != '') && $hasVenue > 0 )	
						$ssql = "update events set category_id='$sc', subcategory_id='$ssc',event_status='1'". $age1 ." where id=$eid";
					else
						$ssql = "update events set category_id='$sc', subcategory_id='$ssc',event_status='0'". $age1 ." where id=$eid";
							
					if ( mysql_query($ssql) ) {
						$i++;
						echo $i . ': Event Id = ' . $eid . ' Updated<br>'; 
					}
				}	
			}
			//print_r($score);
		} 
		
	}
	
	if ( $i == 0 ) 
		echo '<h1>No Matching Tags Found.</h1>';
	else
		echo '<h1>'. $i  .' Records matched and categorized.</h1>';
	
?>

</div>

<?php 
} 
include_once('footer.php');
?>