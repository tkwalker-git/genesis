<?php 
require_once('admin/database.php');
require_once('site_functions.php');

$event_id		= $_GET['id'];
$event_id		= getSingleColumn('id',"select * from `events` where `id`='$event_id'");
$event_status	= getSingleColumn('event_status',"select * from `events` where `id`='$event_id'");
$event_type		= getSingleColumn('event_type',"select * from `events` where `id`='$event_id'");

$is_private		= getSingleColumn('is_private',"select * from `events` where `id`='$event_id'");

$pym = attribValue("orders","total_price"," where main_ticket_id='$event_id' && `type`='flyer' ORDER BY `id` DESC limit 0,1 ");

$valid = validEventTicketSaleTime($event_id);

	if(!$is_private){
		if($event_id=='' || $valid!='yes' || $event_status!=1 || $event_type==0){
				echo "<script>window.location.href='index.php';</script>";
		}
	}

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];


$sql = "select * from events where id='". $event_id ."' && `type`!='draft'";
$res = mysql_query($sql);
if ( mysql_num_rows($res) ) {
if ( $row = mysql_fetch_assoc($res) ) {
	
	$c_event_id			= $row["id"];
	$fb_event_id		= $row["fb_event_id"];
	$userid				= $row["userid"];
	$category			= attribValue("categories","name","where id=" . $row["category_id"] );
	$subcategory_id		= DBout($row["subcategory_id"]);
	$subcategory		= attribValue("sub_categories","name","where id=" . $row["subcategory_id"] );;
	$event_name			= DBout($row["event_name"]);
	
	$event_start_time	= $row["event_start_time"];
	$event_end_time		= $row["event_end_time"];
	$event_start_am_time= $row["event_start_am_time"];
	$event_end_am_time	= $row["event_end_am_time"];
	$event_description	= DBout($row["event_description"]);
	$event_cost			= DBout($row["event_cost"]);
	$event_image		= $row["event_image"];
	$event_sell_ticket	= $row["event_sell_ticket"];
	$event_age_suitab	= $row["event_age_suitab"];
	$event_status		= $row["event_status"];
	$publishdate		= $row["publishdate"];
	$averagerating		= $row["averagerating"];
	$modify_date		= $row["modify_date"];
	$added_by			= $row["added_by"];
	$source				= $row['event_source'];
	$event_seo_name		= $row['seo_name'];
	$event_date			= getEventDates($event_id);
	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
//	$time				= $event_start_time . ' - ' . $event_end_time;
	$cost				= $event_cost;
	
	$venue_info 		= $venue_attrib[1];
	
	
	$event_dateT		= getEventStartDateFB($event_id);
	$event_time			= getEventTime($event_dateT[1]);
	
	if ( $event_time['start_time'] != '' && $event_time['start_time'] != '00:00:00' ) 
		$times = date("h:i A", strtotime($event_time['start_time']));
		
	if ( $event_time['end_time'] != '' && $event_time['end_time'] != '00:00:00' ) 
		$times = $times . ' - ' . date("h:i A", strtotime($event_time['end_time']));	
	$meta_title			= $event_name;
	
	$event_description_s = strip_tags($event_description);
	}
	}
	
	$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
		while($row = mysql_fetch_array($res)){
			$bc_ticket_id			=	$row['id'];
			$bc_name				=	$row['name'];
			$bc_price				=	$row['price'];
			$bc_ticket_id			=	$row['id'];
			$bc_service_fee_type	=	$row['service_fee_type'];
			$bc_service_fee			=	$row['service_fee'];
			$bc_quantity_available	=	$row['quantity_available'];
		}
	
$meta_title	= 'Buy Tickets';
include_once('includes/header.php');
?>
<script>

var abs_url		= '<?php echo ABSOLUTE_PATH; ?>';

function orderTickets(){

	var selectedQty = new Array();
	jQuery.each(jQuery("select[name='qty[]']"), function() {
		selectedQty.push(jQuery(this).val());
	});
	
	var availQty = new Array();
	jQuery.each(jQuery("input[name='availQty[]']"), function() {
		availQty.push(jQuery(this).val());
	});
	
	var titles = new Array();
	jQuery.each(jQuery("input[name='title[]']"), function() {
		titles.push(jQuery(this).val());
	});
	
	valid = 0;
	
	for(var i=0; i < selectedQty.length; i++) {
		if(selectedQty[i] > 0){
		
			if(selectedQty[i] > Number(availQty[i])){
				alert(titles[i]+' - Quantity Available '+availQty[i]+' tickets');
				return false;
			}
			
			valid = 1;
		}
	}
	if(valid == 1){
		return true;
		}
	else{
		alert('Please select ticket Qty');
		return false;
		}
}
</script>
<div class="topContainer">
  <div class="welcomeBox"></div>
 
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Buy Tickets</div>
    <div class="clr"></div>
    <div class="gredBox">
      
    
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
             
			 
			 	<!--Start new code-->
				<span class="ew-heading"><?php echo $event_name; ?></span><!--end ew-heading-->
				<span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo date('M d, Y',strtotime($event_dateT[0])); ?></span>
				<span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo $venue_attrib[1]['venue_name']; ?></span>
				<span class="dotted-separator"></span>
				<form method="post" action="<?php echo ABSOLUTE_PATH_SECURE; ?>buy_tickets_step2.php">
				<table cellpadding="10" cellspacing="0" width="100%">
          <tr bgcolor="#e4f0d8" style="font-size:24px">
            <td width="28%">TICKET TYPE</td>
            
            <td width="18%" align="center">PRICE</td>
            <td width="18%" align="center">FEE</td>
			<td width="18%" align="center">DATE / TIME</td>
            <td width="18%" align="center">QUANTITY</td>
          </tr>
		  <?php
		  $res = mysql_query("select * from `event_ticket_price` where `ticket_id`='$bc_ticket_id'");
			if(mysql_num_rows($res)){
			$s = 0;
			$bg = '#e4f0d8';
			while($row = mysql_fetch_array($res)){
			$bc_ticket_description	=	$row['desc'];
			if($bg == '#e4f0d8'){
				$bg = '#d1e5c0';
			}
			else{
				$bg = '#e4f0d8';
			}
			?>
          <tr bgcolor="<?php echo $bg; ?>" style="font-size:20px">
            <td>
			<input type="hidden" name="id[]" value="<?php echo $row['id'];?>" />
			<input type="hidden" name="title[]" value="<?php echo $row['title'];?>" />
	        <?php echo $row['title']; ?>
              <small style="font-size:12px;"><?php echo $bc_ticket_description; ?></small></td>
           
            <td align="center">$<?php echo number_format($row['price'], 2,'.',''); ?></td>
            <td align="center">$<?php
			
			$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
			
				$buyer_service_free_after_percent		=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
				$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
				
			
				echo $finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');?></td>
			<td align="center"><?php
			$dates	= getEventDatesInArray2($event_id);
			$dt		= $dates['date'];
			$dtIds	= $dates['id'];
			
			echo "<select name='date[]' style='width:100px'>";
			for ($i=0;$i<count($dt);$i++){
			$event_time = getEventTime($dtIds[$i]);
			if ( $event_time['start_time'] != '' ) 
		$time = date("h:i A", strtotime($event_time['start_time']));
		
		echo "<option value='".$dtIds[$i]."'>".date('M d, Y', strtotime($dt[$i]))." - ".$time ."</option>";
			}
			echo "</select>";
			 ?></td>
            <td align="center">
			<input type="hidden" name="availQty[]" value="<?php echo $row['qty']-countSoldTickets($row['id']); ?>" />
			<select name="qty[]">
                <option value="0">0</option>
                <?php
				  for ($i=1;$i<=30;$i++){
					  echo '<option value="'.$i.'"';
					  if($_POST['qty'][$s]==$i){
					 	 echo 'selected="selected"';
					  }
					  echo '>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>';
				  } ?>
            </select>
            </td>
          </tr>
		<?php } }
		
		if($bg == '#e4f0d8'){
				$bg = '#d1e5c0';
			}
			else{
				$bg = '#e4f0d8';
			}
		?>  
		   
		   <tr bgcolor="<?php echo $bg; ?>">
            <td colspan="5" align="right" ><span class="addDiscountCode" style="display:block; float:right" onclick="$('#discount_code').css('display','block')">Add Discount Code</span><input type="text" id="discount_code" name="discount_code" style="display:none;height: 20px; width: 104px; float:right" /></td>
			</tr>
			<?php
			if($bg == '#e4f0d8'){
				$bg = '#d1e5c0';
			}
			else{
				$bg = '#e4f0d8';
			}
			?>
		   <tr bgcolor="<?php echo $bg; ?>">
		  	<td>&nbsp;</td>
			<td colspan="4" align="right" valign="bottom">
			<img src="<?php echo IMAGE_PATH; ?>cards.png" border="0"  style="padding:8px" />
			
			<input type="image" src="<?php echo IMAGE_PATH; ?>order-now.png" onClick="return orderTickets();" name="orderNow" value="Order Now" align="right">
			<input type="hidden" name="orderNow" value="Order Now">
              <input type="hidden" name="ticket_id" value="<?php echo $bc_ticket_id; ?>" />
              <input type="hidden" name="event_id" id="event_id" value="<?php echo $event_id; ?>" />
			  </td>
			</tr>
					<tr bgcolor="#ffffff">
						<td colspan="3" align="left" valign="top">
							<span class="ew-heading">Event Detail</span>
							<br><br>
							<?php echo $event_description_s; ?>						</td>
						<td colspan="3" align="left" valign="top">
							
							<div class="ew-when-where" style="margin-top:0;">
							
								<span class="ew-when-heading" style="margin-top:0;">When</span>
								<span><?php echo date('l, F dS',strtotime($event_dateT[0]));
							echo "<br>".$times; ?></span>
							 </div> <!--end ew-when-where-->
						
							<div class="ew-when-where">
							
								<span class="ew-when-heading">Where</span>
								<span>
									<?php echo $event_locations; ?>								</span>							</div> <!--end ew-when-where-->
							<!--<img src="<?php echo IMAGE_PATH; ?>map.png" border="0" height="204" width="298">-->
							<div id="map22" class="map" style="background-color: #E5E3DF; border: 8px solid #EFEFEF; float: none; height: 300px;overflow: hidden; position: relative; width: 300px;"></div>
							<div><img src="<?php echo IMAGE_PATH; ?>map_shad.gif" width="315"></div>
							</td>
					</tr>
				</table>
				</form>
			 	<!--End new code-->
			 
			 
            </div>
          </div>
        </div>
        <div class="create_event_submited">
       
        </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>

<link href="http://code.google.com/apis/maps/documentation/javascript/examples/standard.css" rel="stylesheet" type="text/css" />
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
	function initialize() {
		var VenueLocation = new google.maps.LatLng(<?php echo $venue_info['venue_lat'];?>, <?php echo $venue_info['venue_lng'];?>);
		var panoramaOptions = {
			center: VenueLocation,
			zoom:15,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		var map = new google.maps.Map(document.getElementById("map22"), panoramaOptions);
		
		var contentString = '<div style="color:#000;width:210px; height:100px;font-size:11px;">'+
							'<div style="padding-bottom:5px"><strong>Address</strong></div>'+
							'<div style="width:210px;float:left;">'+
							//'<img src="<?php echo $venue_img;?>" height="60" width="120" align="left" style="margin-right:10px" />' +
							'<?php echo $event_locations;?></div>'+
							'<br clear="all" /><br>'+
							'Get Directions: <a target="_blank" style="font-weight:normal; color:#0033FF" href="http://www.google.com/maps?source=uds&daddr=<?php echo $raw_address;?>&iwstate1=dir:to">To here</a>'+
							' - <a target="_blank" style="font-weight:normal; color:#0033FF" href="http://www.google.com/maps?source=uds&saddr=<?php echo $raw_address;?>&iwstate1=dir:from">From here</a>'+
							'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		var marker = new google.maps.Marker({
			  position: VenueLocation, 
			  map: map,
			   title:"<?php echo $event_locations;?>"
		});
		
		google.maps.event.addListener(marker, 'mouseover', function() {
		  infowindow.open(map,marker);
		});
		
	}
	window.onload=initialize;
	
</script>