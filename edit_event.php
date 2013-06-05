<?php 

include_once('admin/database.php'); 
include_once('site_functions.php'); 

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

if( isset($_GET["id"]) ) {
	$frmID	=	$_GET["id"];
} else {
	echo "<script>window.location.href='profile_setting.php';</script>";
}

$bc_userid	= $_SESSION['LOGGEDIN_MEMBER_ID'];
$action		= 'edit';
		
if (isset($_POST["submit"]) || isset($_POST["submit_x"]) ) {

	$bc_event_source 		= 	($_SESSION['usertype']==2) ? 'Promoter' : 'User';
	
	$tmp = explode("|",$_POST["category_id"]);
	
	$bc_category_id			=	$tmp[0];
	$bc_subcategory_id		=	$tmp[1];

	$bc_event_name			=	$_POST["eventname"];
	$bc_musicgenere_id		=	$_POST["musicgenere_id"];
	$bc_event_start_time	=	$_POST["event_start_time"];
	$bc_event_music			=	$_POST["event_music"];
	
	$bc_event_end_time		=	$_POST["event_end_time"];
	$bc_event_description	=	$_POST["event_description"];
	$bc_event_cost			=	$_POST["event_cost"];
	$bc_event_image			=	$_FILES['event_image']['name'];
	$bc_event_sell_ticket	=	$_POST["event_sell_ticket"];
	$bc_event_age_suitab	=	$_POST["event_age_suitab"];
	$bc_event_status		=	'2';
	
	$bc_averagerating		=	$_POST["averagerating"];
	$bc_modify_date			=	date("Y-m-d");
	$bc_del_status			=	$_POST["del_status"];
	$bc_added_by			=	$_POST["added_by"];
	
	$bc_dates				=   $_POST['selected_dates'];
	$bc_venu_id				=	DBin($_POST['venue_id']);
	
	$frequency				=	$_POST['frequency'];
	$repeat					=	$_POST['erepeat'];
	
	$privacy				= $_POST['privacy'];
	$tags					= DBin($_POST['tags']);
	
	$dates_sel = array();

	if ( $_POST['selected_dates'] != '' ) {
		$dates_sel = explode(",", $_POST['selected_dates']);
		for($i=0; $i<count($dates_sel); $i++)
			$dates .= "'". $dates_sel[$i] . "',";
	}
	$sucMessage = "";
	
	$errors = array();

	if ( trim($bc_event_name) == '' )
		$errors[] = 'Please enter Eevent Name';
	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please Select Event Type';
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Event Description is empty.';
	if ( trim($bc_event_age_suitab) == '' )
		$errors[] = 'Please Select Age Suitable';
	if ( trim($bc_event_start_time) == '' )
		$errors[] = 'Please Select Start Time';
	if ( trim($bc_event_end_time) == '' )
		$errors[] = 'Please Select End Time';
	if ( $bc_venu_id == '' )
		$errors[] = 'Please select a Event Venue';

	if ( $frequency != '' && !is_numeric($frequency) )
		$errors[] = 'Frequency should be numeric';
	
	if ( $frequency > 0 && count( $dates_sel) > 1 && $repeat != '' )
		$errors[] = 'Select single date for repeat events.';
		
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}

	
	if (!count($errors)) {
          
		 if ( $frequency > 0 && count( $dates_sel) == 1 && $repeat != ''  ) {
			
			$date = date("Y-m-d",strtotime($dates_sel[0]));
			
			for ( $l=1;$l<$frequency;$l++)
				$dates_sel[$l] = date("m/d/Y",strtotime( date("Y-m-d", strtotime($date) ) . " +".$l . " ". $repeat) );
				
		}
		
		$bc_image = '';
		if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["event_image"]["name"] ;
			move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' .$bc_image);
			makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		
		$bc_modify_date	=	 date("Y-m-d");
		 
		$sql	=	"update events set category_id = '" . $bc_category_id . "', subcategory_id = '" . $bc_subcategory_id . "', event_name = '" . $bc_event_name . "', musicgenere_id = '" . $bc_musicgenere_id . "', event_start_time = '" . $bc_event_start_time . "', event_end_time = '" . $bc_event_end_time . "', event_description = '" . $bc_event_description . "', event_cost = '" . $bc_event_cost . "', ". $sql_img ." event_sell_ticket = '" . $bc_event_sell_ticket . "', event_age_suitab = '" . $bc_event_age_suitab . "', modify_date = '" . $bc_modify_date . "',  added_by = '" . $bc_added_by . "', repeat_event = '" . $repeat . "', repeat_freq = '" . $frequency . "',pending_approval='". $pending_approval ."' where id='$frmID'";
		
		$res	=	mysql_query($sql);
		
		if ( $res ) {
			$event_id 	= $frmID;
			
			if (isset($_POST['selected_dates']) && $_POST['selected_dates'] != ''){
				
				$remove_dates = "delete from event_dates where event_id = '$frmID'";
				mysql_query($remove_dates) ;
				
				for($i=0; $i<count($dates_sel); $i++){
					$sql_date = "insert into event_dates (event_id, event_date) values('" . $event_id . "','" . date("Y-m-d",strtotime($dates_sel[$i])) . "')";			
					mysql_query($sql_date) ;
				} 
				
			}
			
			if ($bc_venu_id != ''){
				$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $event_id . "')";
				mysql_query($sql_venue);	
			}
			
			foreach($bc_event_music as $bc_event_music_value){
				$sql_event_music = "insert ignore into event_music (event_id, music_id) values('" . $event_id . "','" . $bc_event_music_value . "')";			
				mysql_query($sql_event_music);
			}  

			$sucMessage = "Event Successfully Saved.";
		} else {
			$sucMessage = "Error: Please try Later";
		}	
	} 

	else {
		$sucMessage = $err;
	}
} 



$sql	=	"select * from events where id='$frmID'";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_event_source 		=	$row["event_source"]; 
		$bc_source_id			=	$row["source_id"];
		$bc_fb_event_id			=	$row["fb_event_id"];
		$bc_userid				=	$row["userid"];
		$bc_category_id			=	$row["category_id"];
		$bc_subcategory_id		=	$row["subcategory_id"];
		$bc_event_name			=	$row["event_name"];
		
		$bc_event_start_time	=	$row["event_start_time"];
		$bc_event_end_time		=	$row["event_end_time"];
		$bc_event_start_am_time	=	$row["event_start_am_time"];
		$bc_event_end_am_time	=	$row["event_end_am_time"];
		$bc_event_description	=	$row["event_description"];
		$bc_event_cost			=	$row["event_cost"];
		$bc_image				=	$row["event_image"];
		$bc_event_sell_ticket	=	$row["event_sell_ticket"];
		$bc_event_age_suitab	=	$row["event_age_suitab"];
		$bc_event_status		=	$row["event_status"];
		$bc_publishdate			=	$row["publishdate"];
		$bc_averagerating		=	$row["averagerating"];
		$bc_modify_date			=	$row["modify_date"];
		$bc_del_status			=	$row["del_status"];
		$bc_added_by			=	$row["added_by"];
		$frequency				=	$row['repeat_freq'];
		$repeat					=	$row['repeat_event'];
		$pending_approval		=	$row['pending_approval'];
		
		$catee = $bc_category_id . '|' . $bc_subcategory_id;
		
		$bc_musicgenere_id = array();
		$r4 = mysql_query("select * from event_music where event_id='$frmID'");
		while ( $ro4 = mysql_fetch_assoc($r4) )
			$bc_musicgenere_id[] =	$ro4["music_id"];

		$event_id = $frmID;
		
		$action = "edit";
	} // end if row

	$sq2	=   "select venue_name from venues where id = (select venue_id from venue_events where event_id = '$frmID' )";
	$res2	=   mysql_query($sq2);
	if ($res2)
		if ($row2 = mysql_fetch_assoc($res2) ) 
			$bc_venue_name = $row2["venue_name"];
	
	$get_event_id	=   "select venue_id from venue_events where event_id = '$frmID'";
	$run_get_event_id	=   mysql_query($get_event_id);
	if ($run_get_event_id){
		if ($row2 = mysql_fetch_assoc($run_get_event_id) ) 
			$bc_venue_id = $row2["venue_id"];
	} 
			
} // end if 


if ($action == "edit" || isset($_POST["submit"])) {
    $dates_q = "select * from event_dates where event_id = '$frmID' ORDER BY event_date ASC";
	$dates_res = mysql_query($dates_q);
	$first_date = "";
	$dates = "";
	$i = 0;
	while($dates_r = mysql_fetch_assoc($dates_res)){
			if(mysql_num_rows($dates_res) > 0){
				$date = date("m/d/Y",strtotime($dates_r['event_date']));
				if($i<1){ $first_date = $date; $i++;}
				$dates = $dates."'".$date."', ";
			}else{
				$date = $dates_r['event_date'];
				$first_date = $date;
				$dates = "'".date("m/d/Y",strtotime($date))."'";
			}
	}
}
if($first_date != ''){
	$yr = date("Y",strtotime($first_date));
	$mon = date("m",strtotime($first_date));
	$mon1 = $mon - 1;
	$dy = date("d",strtotime($first_date));
	$first_date = $yr.", ".$mon1.", ".$dy;
}


$cat_q = "select * from categories order by id ASC";
$cat_res = mysql_query($cat_q);

$age_q = "select * from age order by id ASC";
$age_res = mysql_query($age_q);

$music_q = "select * from music";
$music_res = mysql_query($music_q);


include_once('includes/header.php');

?>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>/js/jquery-ui_1.8.7.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/redmond/jquery-ui.css" type="text/css" media="all" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>


<script type="text/javascript">
	$(function() {
		// multi-months
		$('#multi-months').multiDatesPicker({
			numberOfMonths: 3,

			<?php if ($event_id != "" && $dates != "" )  { ?>
				addDates: [<?php echo $dates; ?>], 
			<?php }?>


			onSelect: function(dateText, inst) {
				var dates = $('#multi-months').multiDatesPicker('getDates');
				document.getElementById("selected_dates").value = dates;
							
			}
		});
		
		$('#multi-months').datepicker('setDate', new Date(<?php echo $first_date; ?>));
	});
	
	 function dynamic_Select(ajax_page, category_id,sub_category)      
	 {  
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "cat=" + category_id + "&subcat=" + sub_category,  
			//data: "subcat=" + subcat_id,
			dataType: "text/html",  
			success: function(html){       $("#subcategory_id").html(html);     }  
	   	}); 
	  }  
	
	$(document).ready(function() {
		$("#venue_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>admin/get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					$("#venue_id").attr("value", data[0]);
				}
			}).setOptions({
				max: '100%'
			});
});


function checkCategory(val)
{
	if ( val == -1 ) {
		alert("You can't select Parent Category");
		document.getElementById("category_id").selectedIndex=0;
	}	
}
	
</script>

<style>

.addEInput
{
	width:225px!important;
	height:30px!important;
}


table.ui-datepicker-calendar {border-collapse: separate;}
.ui-datepicker-calendar td {border: 1px solid transparent;}

.hasDatepicker .ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
	background: #743620 none;
	color: white;
}

#ui-datepicker-div {display:none;}

</style>



 <div  class="topContainer" style="padding-top:20px;">
	<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
	<?php if ($event_id != "" && $dates != "" )  { ?>
		<input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="<?php echo substr(str_replace("'","",trim($dates)),0,-1); ?>" />
	<?php } else {?>
	<input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="" />
	<?php } ?>
	<!--	<input type='hidden' name='venueeditid' id='venueeditid' value=''> -->
		
		<div class="eventDetailhd"><!--<span>add an <strong>event</strong></span>--></div>
		<div class="clr gap"></div>

	<!-- Start Middle-->
	<div id="middleContainer">
		<div class="eventMdlBg">
			<div class="eventMdlMain">				
				<!--Start Left Part -->
				<div class="eventLft">
					<div><img src="<?php echo IMAGE_PATH; ?>event_tpcone.gif" alt="" /></div>
					<div class="eventMdlData">
						<!--Start Event Details -->

						<div class="eventMainCat">
						
							<div class="evntBlkHdMain">
							
								<div class="fl"><img src="<?php echo IMAGE_PATH; ?>event_black_lftcone.gif" alt="" /></div>
								<div class="fr"><img src="<?php echo IMAGE_PATH; ?>event_black_rtcone.gif" alt="" /></div>
								<div class="eventsBlackHd">Event Details <span>(<span class="blueClr">*</span>required)</span></div>								<div class="clr"></div>
							</div>
							<div class="error" style="color:red; font-weight:bold; padding:10px"><?php echo $sucMessage; ?></div>

							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Name</div>
							<div class="evLabal">
								<input type="text" maxlength="100" name="eventname" id="eventname" class="evInput" required="Empty" value='<?php echo $bc_event_name; ?>'/>
							</div>
							
							
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Type</div>
							<div class="evLabal">
								<select class="bc_input" name="category_id" id="category_id" style="width:200px" onchange="checkCategory(this.value);">
								<option value="" >Select Category</option>
								<?php $sqlParent = "SELECT name,id FROM categories ";
								$resParent = mysql_query($sqlParent);
								$totalRows=mysql_num_rows($resParent);
								while($rowParent = mysql_fetch_array($resParent))
								{	
								?>
								<option style="font-weight:bold; color:#990000" value="-1"><?=$rowParent['name']?></option>
								
									  <?php 
										$subcat_q = "SELECT * FROM sub_categories WHERE categoryid = '". $rowParent['id'] ."' ORDER BY id ASC";
										$res = mysql_query($subcat_q) ;
										while( $r = mysql_fetch_assoc($res) ){  
											if ( $rowParent['id'].'|'. $r['id'] == $catee )
												$sele = 'selected="selected"';
											else
												$sele = '';	
									  ?>
									 <option style="font-weight:normal; padding-left:10px" <?php echo $sele;?> value="<?php echo $rowParent['id'].'|'. $r['id']; ?>"><?php echo $r['name']; ?></option>  
								
							   <?php } } ?>
								</select>

							
							</div>
							<div class="clr"></div>
							<div class="evField">Music Genre:</div>
							<div class="evLabal">
							
								<ul style="list-style:none; margin:0px; padding:0px">
								<?php 
								
								$totalMusic= mysql_num_rows($music_res);
								$no = 0;
								while($rowMusic=mysql_fetch_assoc($music_res))
								{	
									if ( in_array($rowMusic['id'],$bc_musicgenere_id) )
										$che = 'checked="checked"';
									else
										$che = '';	
								?>
									<li style="width:170px; float:left; ">
										<input <?php echo $che;?>  type="checkbox" style="float:left" name="event_music[]" value="<?php echo $rowMusic['id']?>"   />
										<div style="float:left; margin-right:5px"><?php echo $rowMusic['name']?></div>
									</li>
								<?php $no++;} ?>
								</ul>
							
							</div>
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Description</div>
							<div class="evLabal"><textarea name="event_description" id="event_description" cols="2" rows="4" style="height:60px;" class="evInput"><?php echo $bc_event_description; ?></textarea></div>
							<div class="clr"></div>
							<div class="evField">Cost</div>

							<div class="evLabal">
								<input type="text" name="event_cost"   id="event_cost" class="evInput" value="<?php echo $bc_event_cost;?>" style="width:300px;" />
							</div>
							<div class="clr"></div>
							<div class="evField">Image</div>
							<div class="evLabal">
								<div class="imgBox" id="upload_area1">
								<?php 
									if( $bc_image != ''  ) {
										if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
											$bc_image1 = ABSOLUTE_PATH . '/event_images/'.$bc_image;
										else
											$bc_image1 = $bc_image;	
											
										echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_event_image" width="66" height="66" />';
										$image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="events|event_image|'.$bc_image.'|../event_images/" />';
									}
									else
										echo '<img src="images/upload.gif"  alt="upload" />';
																
									 ?>
								</div>
								<div class="fl"><input type="file" name="event_image" id="filename1"  /><br><?php echo $image_del;?>
								</div>
								<div class="clr"></div>

							</div>
							<div class="clr"></div>
							<div class="evField">Selling Tickets</div>
							<div class="evLabal"><input name="event_sell_ticket" <?php if($bc_event_sell_ticket == 'No'){?> checked="checked"<?php }?>  class="radio" type="radio" value="No"  />No <span style="padding-left:30px;"><input <?php if($bc_event_sell_ticket == 'Yes'){?> checked="checked"<?php }?>  name="event_sell_ticket" class="radio" type="radio" value="Yes"  />Yes</span></div>
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Age Suitability</div>

							<div class="evLabal">
							<select class="bc_input" name="event_age_suitab" id="event_age_suitab">
								<option value="">-Select age-</option>
								<?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
							<option value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'selected'; }?>><?=$rowAge['name']?></option>
							<?php } ?>
							</select>
							</div>
							<div class="clr"></div>
						</div>
						<!--End Event Details -->

					
						<!--Start Event Date and Time -->
						<div class="eventMainCat">
							<div class="evntBlkHdMain">
								<div class="fl"><img src="images/event_black_lftcone.gif" alt="" /></div>
								<div class="fr"><img src="images/event_black_rtcone.gif" alt="" /></div>
								<div class="eventsBlackHd">Event Date &amp; Location <span>(<span class="blueClr">*</span>required)</span></div>							
								<div class="clr"></div>

							</div>
							
							<div class="evField" style="text-align:left!important; margin-left:20px; width:90px"><span>1.</span><span class="redClr"><font color='red'>*</font></span> Event Time</div>
							<div class="evLabal">
							<!--<input type="text"  maxlength="5" name="eventtime" id="eventtime" class="evInput" style="width:60px;" /> -->
							
								<select class="bc_input" name="event_start_time" id="eventtime" style="width:90px;">
								<option value="">select</option>
									<?php 
									$bc_arr_event_start_time = array("12:00 AM"=>"12:00 AM", "12:30 AM"=>"12:30 AM", "1:00 AM"=>"01:00 AM", "1:30 AM"=>"01:30 AM", "2:00 AM"=>"02:00 AM", "2:30 AM"=>"02:30 AM", "3:00 AM"=>"03:00 AM", "3:30 AM"=>"03:30 AM", "4:00 AM"=>"04:00 AM", "5:00 AM"=>"05:00 AM", "5:30 AM"=>"05:30 AM", "6:00 AM"=>"06:00 AM", "6:30 AM"=>"06:30 AM", "7:00 AM"=>"07:00 AM", "7:30 AM"=>"07:30 AM", "8:00 AM"=>"08:00 AM", "8:30 AM"=>"8:30 AM", "9:00 AM"=>"09:00 AM", "9:30 AM"=>"09:30 AM", "10:00 AM"=>"10:00 AM", "10:30 AM"=>"10:30 AM", "11:00 AM"=>"11:00 AM", "11:30 AM"=>"11:30 AM", "12:00 PM"=>"12:00 PM", "12:30 PM"=>"12:30 PM", "1:00 PM"=>"01:00 PM", "1:30 PM"=>"01:30 PM", "2:00 PM"=>"02:00 PM", "2:30 PM"=>"02:30 PM", "3:00 PM"=>"03:00 PM", "3:30 PM"=>"03:30 PM", "4:00 PM"=>"04:00 PM", "5:00 PM"=>"05:00 PM", "5:30 PM"=>"05:30 PM", "6:00 PM"=>"06:00 PM", "6:30 PM"=>"06:30 PM", "7:00 PM"=>"07:00 PM", "7:30 PM"=>"07:30 PM", "8:00 PM"=>"08:00 PM", "8:30 PM"=>"08:30 PM", "9:00 PM"=>"09:00 PM", "9:30 PM"=>"09:30 PM", "10:00 PM"=>"10:00 PM", "10:30 PM"=>"10:30 PM", "11:00 PM"=>"11:00 PM", "11:30 PM"=>"11:30 PM"); 
									foreach($bc_arr_event_start_time as $key => $val)
									{
											
										if ($key == DBout($bc_event_start_time))
											$sel = 'selected="selected"';
										else
											$sel = "";	
									?>
									<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
								<?php } ?>
							</select>
					
								<span class="featureRtHd"><strong>to</strong></span>
				
							
							<select class="bc_input" name="event_end_time" id="eventtime1" style="width:90px;">
								<option value="">select</option>
								<?php 
									$bc_arr_event_end_time = $bc_arr_event_start_time;
									foreach($bc_arr_event_start_time as $key => $val)
									{
											
										if ($key == DBout($bc_event_end_time))
											$sel = 'selected="selected"';
										else
											$sel = "";	
									?>
									<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
								<?php } ?>
							</select>
							
							
							
								<span class="featureRtHd"></span>
							</div>
							
							<div class="clr"></div>

							<div class="evField" style="text-align:left!important; margin-left:20px; width:90px"><span>2.</span><span class="redClr"><font color='red'>*</font></span> Event Date</div>
								<div class="clenderCon" >
									
									<div class="clenderBox" >
										<div>
											<div id="multi-months"></div>
											<font color="#FF0000"><strong>Click on date to select OR unselect</strong></font>
										</div>
									</div>	

								</div>								

							<div class="clr"></div>
							
							<div class="clr"></div>
							
							<div class="evField" style="text-align:left!important; margin-left:20px; width:90px"><span>3.</span> Repeat</div>
							<div class="evLabal">
								<?php $repeat_arr = array("day" => "Event Happens Daily","week"=>"Event Happens Weekly","month"=>"Event Happens Monthly"); ?>
								<select id="erepeat" name="erepeat" class="bc_input" style="width:150px">
								<option value="" selected="selected">Select</option>
								<?php 
									foreach ( $repeat_arr as $k1 => $v1 ) {
										
										if ( $repeat == $k1 )
											$sele = 'selected="selected"';
										else
											$sele = '';	
								?>
									<option <?php echo $sele;?> value="<?php echo $k1;?>"><?php echo $v1;?></option>
								<?php } ?>
								</select>
							
								<span class="featureRtHd"></span>
							</div>
							
							<div class="clr"></div>
							
							<div class="evField" style="text-align:left!important; margin-left:20px; width:90px"><span>4.</span> Frequency </div>
							<div class="evLabal">
								<input type="text" name="frequency" id="frequency" class="evInput" style="width:170px;" value="<?php echo $frequency; ?>"/>
								<br>
								<font color="#FF0000" style="font-size:11px">Number Only. Enter how many times you want to repeat the event.</font>
								<span class="featureRtHd"></span>
							</div>
							
							<div class="clr"></div>
							
							<div class="evField" style="text-align:left!important; margin-left:20px; width:90px"><span>5.</span> Location </div>
							<div class="evLabal">
								<input type="text" name="venue_name" id="venue_name" class="evInput" style="width:170px;" value="<?php echo $venue_name; ?>"/>
								&nbsp;
								<a href="javascript:void(0)" style="color:#0066FF; text-decoration:underline" onclick="windowOpener(525,645,'Add New Location','add_venue.php')">
									Can't find your location? Add it here
								</a>
								<input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venue_id; ?>" />
								<br>
								<font color="#FF0000" style="font-size:11px">Start Typing Location Name.</font>
								<span class="featureRtHd"></span>
							</div>
							
							<div class="clr"></div>
							
						</div>
						

					
					
						<!--Start Review and Finish -->
						<div class="eventMainCat">
							<div class="evntBlkHdMain">
								<div class="fl"><img src="images/event_black_lftcone.gif" alt="" /></div>
								<div class="fr"><img src="<?php echo IMAGE_PATH; ?>event_black_rtcone.gif" alt="" /></div>
								<div class="eventsBlackHd">Final Steps </div>							
								<div class="clr"></div>
							</div>
							
							<div class="clr"></div>
							
							<div class="evField" >Privacy </div>
							<div class="evLabal">
								<select name="privacy" class="bc_input">
									<option selected="selected" value="Public">Public</option>
									<option value="Private">Private</option>
								</select>
								<span class="featureRtHd"></span>
							</div>
							
							<div class="clr"></div>
							<div class="evField">Tags</div>

							<div class="evLabal"><input type="text" name="tags"  id="tags" class="evInput" value=""  maxlength="100" /></div>
							
							<div class="clr"></div>
							
							<div class="fr" style="padding-right:20px; padding-top:20px">
								<input type="image" src="<?php echo IMAGE_PATH; ?>submit_event_btn.gif" name="submit" value="submit" />
							</div>
							<div class="clr"></div>
						</div>
						<!--End Review and Finish -->
					</div>

					<div><img src="images/event_btcone.gif" alt="" /></div>
				</div>
				<!--End Left Part -->
				<!--Start Right Part -->
				<div class="myeventRtMain">
					<div class="eventRtconBg">
						<div class="eventTpBg">
							<div class="featurePromoters" style="font-size:14px;">features for <strong>promoters</strong></div>

							<div class="featureMdl">
								<div align="center"><img src="<?php echo IMAGE_PATH; ?>feature_promoters_img.jpg" alt="" vspace="2" /></div>
								<div class="eventgrabberTxt"><strong>Eventgrabber </strong>provides promoters withe custom tools that automate the leg work for your events and more.</div>
								<strong>Features of our Event Manager are:</strong><br /><br />
								<div class="featureCatSec">
									<div class="featureLftIcon"><img src="<?php echo IMAGE_PATH; ?>city_palus.gif" alt="" /></div>
									<div class="featureRt">

										<div class="featureRtHd">City<strong>pulse:</strong></div>
										Keeps you in sync with your client's likes, dislikes and demands.									</div>
									<div class="clr"></div>
								</div>
								<div class="featureCatSec">
									<div class="featureLftIcon"><img src="<?php echo IMAGE_PATH; ?>stat_brobber.gif" alt="" width="30" height="23" /></div>
									<div class="featureRt">

										<div class="featureRtHd">stat<strong>grabber:</strong></div>
										Statistical breakdown of your marketing impact, demographics and trends.									</div>
									<div class="clr"></div>
								</div>
								<div class="featureCatSec">
									<div class="featureLftIcon"><img src="<?php echo IMAGE_PATH; ?>quick_add.gif" alt="" width="30" height="23" /></div>
									<div class="featureRt">Quickly add, edit and manage your events.</div>

									<div class="clr"></div>
								</div>
								<div class="manyMore">And many more features coming soon..</div>
							</div>
						</div>
					<div><img src="<?php echo IMAGE_PATH; ?>myevent_rtbtm_con.gif" alt="" /></div>
					
					</div>
					
			</div>

			
		  </div>
				<!--End Right Part -->
				<div class="clr"></div>
	  </div>	
	  </div>
		
</form>

</div>

<script>

$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide();
		$("#delImg_" + imgInfo[1]).attr("src", "images/upload.gif");
	}
});

</script>

<?php include_once('includes/footer.php');?>