<?php
include_once('database.php');
include_once('header.php'); 
require_once ('yelp_lib.php');

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

	$cats = array("arts"=>"Arts & Entertainment","beautysvc"=>"Beauty and Spas","education"=>"Education ","eventservices"=>"Event Planning & Services","food"=>"Food","hotelstravel"=>"Hotels & Travel","nightlife"=>"Night Life","pets"=>"Pets","publicservicesgovt"=>"Public Services","religiousorgs"=>"Religious","shopping"=>"Shopping","afghani"=>"Restaurants","african"=>"Restaurants","newamerican"=>"Restaurants","tradamerican"=>"Restaurants","argentine"=>"Restaurants","asianfusion"=>"Restaurants","bbq"=>"Restaurants","basque"=>"Restaurants","belgian"=>"Restaurants","brasseries"=>"Restaurants","brazilian"=>"Restaurants","breakfast_brunch"=>"Restaurants","british"=>"Restaurants","buffets"=>"Restaurants","burgers"=>"Restaurants","burmese"=>"Restaurants","cafes"=>"Restaurants","cajun"=>"Restaurants","cambodian"=>"Restaurants","caribbean"=>"Restaurants","cheesesteaks"=>"Restaurants","chicken_wings"=>"Restaurants","chinese"=>"Restaurants","dimsum"=>"Restaurants","creperies"=>"Restaurants","cuban"=>"Restaurants","delis"=>"Restaurants","diners"=>"Restaurants","ethiopian"=>"Restaurants","hotdogs"=>"Restaurants","filipino"=>"Restaurants","fishnchips"=>"Restaurants","fondue"=>"Restaurants","foodstands"=>"Restaurants","french"=>"Restaurants","gastropubs"=>"Restaurants","german"=>"Restaurants","gluten_free"=>"Restaurants","greek"=>"Restaurants","halal"=>"Restaurants","hawaiian"=>"Restaurants","himalayan"=>"Restaurants","hotdog"=>"Restaurants","hungarian"=>"Restaurants","indpak"=>"Restaurants","indonesian"=>"Restaurants","irish"=>"Restaurants","italian"=>"Restaurants","japanese"=>"Restaurants","korean"=>"Restaurants","kosher"=>"Restaurants","latin"=>"Restaurants","raw_food"=>"Restaurants","malaysian"=>"Restaurants","mediterranean"=>"Restaurants","mexican"=>"Restaurants","mideastern"=>"Restaurants","modern_european"=>"Restaurants","mongolian"=>"Restaurants","moroccan"=>"Restaurants","pakistani"=>"Restaurants","persian"=>"Restaurants","peruvian"=>"Restaurants","pizza"=>"Restaurants","polish"=>"Restaurants","portuguese"=>"Restaurants","russian"=>"Restaurants","sandwiches"=>"Restaurants","scandinavian"=>"Restaurants","seafood"=>"Restaurants","singaporean"=>"Restaurants","soulfood"=>"Restaurants","soup"=>"Restaurants","southern"=>"Restaurants","spanish"=>"Restaurants","steak"=>"Restaurants","sushi"=>"Restaurants","taiwanese"=>"Restaurants","tapas"=>"Restaurants","tapasmallplates"=>"Restaurants",""=>"Restaurants","tex-mex"=>"Restaurants","thai"=>"Restaurants","turkish"=>"Restaurants","ukrainian"=>"Restaurants","vegan"=>"Restaurants","vegetarian"=>"Restaurants","vietnamese"=>"Restaurants");
	
	//$unsigned_url2 = "http://api.yelp.com/v2/search?location=Orlando&category_filter=arts,beautysvc,education,eventservices,food,hotelstravel,nightlife,pets,publicservicesgovt,religiousorgs,restaurants,shopping";
	
	$consumer_key 		= "2rAJbVGZCjq4lkOJeM5lyw";
	$consumer_secret	= "b9RCOfJy0XLZ59r3BBUVzMaCKI8";
	$token 				= "d6MQYMqKCuTfQtvEHUSakg7p2C2jqNTt";
	$token_secret 		= "Z6ZVBvYk-O7UOEbvk15CHLGk69U";
	
	$token 				= new OAuthToken($token, $token_secret);
	$consumer 			= new OAuthConsumer($consumer_key, $consumer_secret);
	$signature_method 	= new OAuthSignatureMethod_HMAC_SHA1();
	
	mysql_query("TRUNCATE TABLE venues_temp");
	
	foreach ( $cats as $filter => $flt) {
		$unsigned_url2 = "http://api.yelp.com/v2/search?location=". $city2 ."&category_filter=$filter";
		
		for ( $p=0;$p<50;$p++) {
		
			$offset = $p * 20;
			$unsigned_url3 = $unsigned_url2 . "&offset=$offset";
			$oauthrequest2 = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url3);
			$oauthrequest2->sign_request($signature_method, $consumer, $token);
			$signed_url2 = $oauthrequest2->to_url();
		
			$ch = curl_init($signed_url2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$data2 = curl_exec($ch); 
			curl_close($ch);
			
			$response2 = json_decode($data2);
			$records2 = $response2->businesses;
			
			if ( count($records2) > 0 ) {
			
				echo 'Iteration: ' . $p . ' - Total: ' . count($records2) .'<br>';
				
				foreach ($records2 as $obj ) {
					
					$location_id 		= "YP-".$obj->id;
					$name				= DBin($obj->name);
					$address			= DBin($obj->location->display_address[0]);
					$city				= $obj->location->city;
					$state				= $obj->location->state_code;
					$zip				= $obj->location->postal_code;
					$phone				= $obj->display_phone;
					$rating				= $obj->rating_img_url;
					$categories			= $obj->categories;
					$lati 				= $obj->location->coordinate->latitude;
					$long 				= $obj->location->coordinate->longitude;
					$image 				= $obj->image_url;
					$neighbor      		= DBin($obj->location->neighborhoods[0]);
					
					$rating = str_replace('http://media4.px.yelpcdn.com/static/201012163106483837/i/ico/stars/stars_','',$rating);
					
					$rating = str_replace('.png','',$rating);
					$r = explode("_",$rating);
					if ( count( $r) > 1 )
						$rating = $r[0] . '.5';
					else
						$rating = $r[0];	
					
					if ( is_array($categories) ) {
						
						foreach ( $categories as $cat) {
							$cate .= DBin($cat[0]) . ',';
						}
					}
					
					if ( mysql_query("insert ignore into venues_temp (source_id,venue_type,venue_name,venue_address,  
									venue_city,venue_state,venue_zip,venue_lng,venue_lat,categories,averagerating,tags,phone, 
									neighbor,image) VALUES ('$location_id','$flt','$name','$address','$city','$state','$zip','$long','$lati','$cate',
									'$rating','$tag','$phone','$neighbor','$image') ") ) 
					{
						$total_added++;
					} else {
						echo mysql_error() . '<br>';
					}	
				} 
			} else {
				break;
			}	
		}
		
		// update event venues
		// add space at the end of all addresses
		$sql = "update venues_temp set venue_address=CONCAT(venue_address,' ') ";
		mysql_query($sql);
		
		// Step 1:
	
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'St.','Street') WHERE venue_address LIKE '% St. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'St','Street') WHERE venue_address LIKE '% St %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Ste.','Street') WHERE venue_address LIKE '% Ste. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Ste','Street') WHERE venue_address LIKE '% Ste %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Dr','Drive') WHERE venue_address LIKE '% Dr %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Dr.','Drive') WHERE venue_address LIKE '% Dr. %'";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Rd.','Road') WHERE venue_address LIKE '% Rd. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Rd','Road') WHERE venue_address LIKE '% Rd %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Ave','Avenue') WHERE venue_address LIKE '% Ave %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Ave.','Avenue') WHERE venue_address LIKE '% Ave. %'";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Blvd.','Boulevard') WHERE venue_address LIKE '% Blvd. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Blvd','Boulevard') WHERE venue_address LIKE '% Blvd %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Bldg','Building') WHERE venue_address LIKE '% Bldg %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Bldg.','Building') WHERE venue_address LIKE '% Bldg. %'";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Trl','Trail') WHERE venue_address LIKE '% Trl %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'Trl.','Trail') WHERE venue_address LIKE '% Trl. %'";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'E.','East') WHERE venue_address LIKE '% E. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'E','East') WHERE venue_address LIKE '% E %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'W.','West') WHERE venue_address LIKE '% W. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'W','West') WHERE venue_address LIKE '% W %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'S.','South') WHERE venue_address LIKE '% S. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'S','South') WHERE venue_address LIKE '% S %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'N.','North') WHERE venue_address LIKE '% N. %' ";
		mysql_query($sql);
		$sql = "update venues_temp set venue_address=REPLACE(venue_address,'N','North') WHERE venue_address LIKE '% N %' ";
		mysql_query($sql);
		
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
					mysql_query("DELETE from venues_temp where id='". $ven_id ."'");
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
					mysql_query("DELETE from venues_temp where id='". $ven_id ."'");
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
							mysql_query("DELETE from venues_temp where id='". $ven_id ."'");
						}	
					}	
				}
			}
		}
		
		mysql_query("insert ignore into venues select NULL,source_id,venue_type,venue_name,venue_address, 
					venus_radius,venue_lng,venue_lat,add_date,status,del_status,venue_city,venue_state, 
					venue_country,venue_zip,categories,tags,averagerating,phone,neighbor,image from venues_temp");
		
	}
}

?>
</div>
<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added.</h1>';
</script>

<?php  include_once('footer.php')?>