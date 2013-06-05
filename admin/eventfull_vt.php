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
	
</div>

<div style="padding:20px">

<?php

ob_start();

if(isset($_POST['cities']) && $_POST['cities'] != ""){
	
	
	echo '<br><br><div id="loading" style="padding:20px; text-align:center"><img src="'.IMAGE_PATH.'loading.gif" /><br><br>Collecting data for ' . $_POST['cities'] . '</div>';

	ob_flush();
	ob_end_flush();
	ob_clean();
	
    $city = trim($_POST['cities']);
	//echo $city;
	
	$url2 		= "http://api.eventful.com/rest/events/search?app_key=CSxRkR9vtmFKQTsX&location=$city,fl&date=Future&sort_orde=date&page_size=1";
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
	
	for ( $a=1; $a<=$pages;$a++) {
	
		$url 		= "http://api.eventful.com/rest/events/search?app_key=CSxRkR9vtmFKQTsX&location=$city,fl&date=Future&sort_orde=date&page_size=100&page_number=".$a;
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
			
			$venue_query = "insert ignore into venues_ev (source_id,venue_name,venue_address,venue_city,venue_state,venue_zip,venue_lng,venue_lat,add_date)
							 VALUES ('$venu_id','$venu_name','$venu_address','$venu_city','$venu_state','$venu_zip','$venu_lon','$venu_lat','$addDate') ";
			mysql_query($venue_query) ;

			unset($xmlparser);
			$xmlparser = NULL;
		}
	}
}		
?>

</div>


<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added.</h1>';
</script>
<?php  include_once('footer.php')?>