<?php

include_once('admin/database.php');
include_once('site_functions.php');
	
$i=0;

$res2 = mysql_query("select id,tags from events where subcategory_id='' && `event_source`='Orlando Slice'");
while ($rows= mysql_fetch_assoc($res2) ) {
	
	$score = array();
	
	$eid 	= $rows['id'];
	$tg		= $rows['tags'];
	
	$cat	= 0;
	$subcat = 0;
	
	if ( trim($tg) != '' ) {
		$eTags  = array();
		$tags	= substr($tg,1) ;
		$tags  	= str_replace(","," ",$tags) ;
		$tags  	= str_replace("_"," ",$tags) ;
		$tags  	= str_replace("/"," ",$tags) ;
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
				if ( mysql_query("update events set category_id='$sc', subcategory_id='$ssc',event_status='1' where id=$eid") ) {
					$i++;
					echo $i . ': Event Id = ' . $eid . ' Updated<br>'; 
				}
			}	
		}
		//print_r($score);
	} 
	
}



?>