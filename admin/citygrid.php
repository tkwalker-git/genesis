<?php
include_once('database.php');
include_once("xmlparser.php");
include_once('header.php'); 

$fl_cities_q = "select * from fl_cities";
$fl_cities_res = mysql_query($fl_cities_q);

?>
<div class="bc_heading">
<div>City Grid</div>
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
		<input type="button" value="Collect Venues" onclick="document.flcities.submit()" class="addBtn">
		</div>
				
	</div>
   </form>
</div>

<div style="padding:20px">

<?php

ob_start();

if(isset($_POST['cities']) && $_POST['cities'] != ""){
	
	
	echo '<br><br><div id="loading" style="padding:20px; text-align:center"><img src="../images/loading.gif" /><br><br>Collecting data for ' . $_POST['cities'] . '</div>';

	ob_flush();
	ob_end_flush();
	ob_clean();
	
    $city2 = trim($_POST['cities']);
	
	$vtypes = array("bar","movietheater","hotel","barclub","restaurant");
	//$vtype= 'bar';
	foreach ( $vtypes as $vtype) {
		
		
		//$url2 		= "http://api.citygridmedia.com/content/places/v2/search/where?type=$vtype&where=$city2,FL&publisher=57ppnvxrzgen946urewmszmd";
		$url2 		= "http://api.citygridmedia.com/content/places/v2/search/where?type=$vtype&where=$city2,FL&publisher=57ppnvxrzgen946urewmszmd";
		$nom2 		= get_url_contents($url2);
		
		$xmlparser2	= new xmlparser();
		$data2		= $xmlparser2->GetXMLTree($nom2);
		
		$total_records 	= $data2['RESULTS']['0']['ATTRIBUTES']['TOTAL_HITS'];
		$rpp			= $data2['RESULTS']['0']['ATTRIBUTES']['RPP'];
		$pagenum 		= $data2['RESULTS']['0']['ATTRIBUTES']['PAGE'];
		$lasthit		= $data2['RESULTS']['0']['ATTRIBUTES']['LAST_HIT'];
		$firsthit 		= $data2['RESULTS']['0']['ATTRIBUTES']['FIRST_HIT'];
		
		if ( $total_records > 0 )
			$total_pages = ceil($total_records/20);
		else
			$total_pages = 1;
		
		$rec_count = 0;
		$total_added = 0;
		
		//$total_pages
		for ( $a=1; $a<$total_pages;$a++) {
			
		  	echo $url = "http://api.citygridmedia.com/content/places/v2/search/where?type=$vtype&where=$city2,FL&publisher=57ppnvxrzgen946urewmszmd&page=".$a;
		  	echo '<br>';
			
			$nom 		= get_url_contents($url);
			
			$xmlparser	= new xmlparser();
			$data		= $xmlparser->GetXMLTree($nom);
			
			$locations_size = sizeof($data['RESULTS']['0']['LOCATIONS']['0']['LOCATION']);
			
			for($j=0; $j<$locations_size; $j++){
			
				$location_id 		= 'CG-'. $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['ATTRIBUTES']['ID'];
				$name				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['NAME']['0']['VALUE'];
				$address			= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['ADDRESS']['0']['STREET']['0']['VALUE'];
				$city				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['ADDRESS']['0']['CITY']['0']['VALUE'];
				$state				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['ADDRESS']['0']['STATE']['0']['VALUE'];
				$zip				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['ADDRESS']['0']['POSTAL_CODE']['0']['VALUE'];
				$phone				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['PHONE_NUMBER']['0']['VALUE'];
				$rating				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['RATING']['0']['VALUE'];
				//$reviews			= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['USER_REVIEW_COUNT']['0']['VALUE'];
				$categories			= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['SAMPLE_CATEGORIES']['0']['VALUE']; 
				$lati 				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['LATITUDE']['0']['VALUE'];
				$long 				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['LONGITUDE']['0']['VALUE'];
				$image 				= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['IMAGE']['0']['VALUE'];
				$neighbor      		= $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['NEIGHBORHOOD']['0']['VALUE'];
				
				$tags = array();
				$tag_size = sizeof($data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['TAGS']['0']['TAG']);
				for($i=0; $i<$tag_size; $i++)
					if($i==0)
						$tags[] = $data['RESULTS']['0']['LOCATIONS']['0']['LOCATION'][$j]['TAGS']['0']['TAG'][$i]['VALUE'];
					
				$tag = implode(",",$tags);
				//echo "Rating :".$rating;
				
				mysql_query("insert ignore into venues (source_id,venue_type,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,categories,averagerating,tags,phone,neighbor,image) VALUES ('$location_id','$vtype','$name','$address','$city','$state','$zip','$long','$lati','$categories','$rating','$tag','$phone','$neighbor','$image') ");
				$total_added++;
				
			}
			
			
			reset($xmlparser);
			
		} // end for loop outter
	}
}

?>
</div>
<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added.</h1>';
</script>

<?php  include_once('footer.php')?>