<?php 

include_once('admin/database.php'); 
include_once('site_functions.php'); 

include_once('includes/header.php');
	
$bc_event_source 		= 	'USER'; 
$bc_fb_event_id			=	$_POST["fb_event_id"];
$bc_userid				=	$_POST["userid"];
$bc_category_id			=	$_POST["category_id"];
$bc_subcategory_id		=	$_POST['subcategory_id'];
$bc_event_name			=	$_POST["eventname"];
$bc_musicgenere_id		=	$_POST["musicgenere_id"];
$bc_event_start_time	=	$_POST["event_start_time"];
$bc_event_end_time		=	$_POST["event_end_time"];
$bc_event_description	=	$_POST["event_description"];
$bc_event_cost			=	$_POST["event_cost"];
$bc_event_image			=	$_FILES['image_name']['name'];
$bc_event_sell_ticket	=	$_POST["event_sell_ticket"];
$bc_event_age_suitab	=	$_POST["event_age_suitab"];
$bc_event_status		=	$_POST["event_status"];

$bc_averagerating		=	$_POST["averagerating"];
$bc_modify_date			=	date("Y-m-d");
$bc_del_status			=	$_POST["del_status"];
$bc_added_by			=	$_POST["added_by"];

$sucMessage = "";

$errors = array();

if (isset($_POST["submit"]) ) {
	
	if ( trim($bc_event_name) == '' )
		$errors[] = 'Please enter Eevent Name';
	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please Select Event Type';
	if ( trim($bc_subcategory_id) == '' )
		$errors[] = 'Please Select Event Sub Category';		
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Event Description is empty.';
	if ( trim($bc_event_cost) == '' )
		$errors[] = 'Please Enter Event Cost';
	if ( trim($bc_event_age_suitab) == '' )
		$errors[] = 'Please Select Age Suitable';
	if ( trim($bc_event_start_time) == '' )
		$errors[] = 'Please Select Start Time';
	if ( trim($bc_event_end_time) == '' )
		$errors[] = 'Please Select End Time';
	if ( trim($_POST['selected_dates']) == '' )
		$errors[] = 'Please Set Event Date(s)';
	
		
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}

	
	if (!count($errors)) {
          
		 $bc_source_id	=	"USER-".rand(); 
		 $bc_publishdate	=	 date("Y-m-d");
		 
		$sql	=	"insert into events (event_source,source_id,fb_event_id,userid,category_id,subcategory_id,event_name,musicgenere_id,event_start_time,event_end_time,event_description,event_cost,event_image,event_sell_ticket,event_age_suitab,event_status,publishdate,averagerating,modify_date,del_status,added_by) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_fb_event_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_musicgenere_id . "','" . $bc_event_start_time . "','" . $bc_event_end_time . "','" . $bc_event_description . "','" . $bc_event_cost . "','" . $bc_event_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "')";
		$res	=	mysql_query($sql);
		$frmID 	= mysql_insert_id();
	
		$dates_sel = explode(",", $_POST['selected_dates']);
		for($i=0; $i<count($dates_sel); $i++){
			$sql_date = "insert into event_dates (event_id, event_date) values('" . $frmID . "','" . date("Y-m-d",strtotime($dates_sel[$i])) . "')";			
			mysql_query($sql_date);
		}   

		if ($res)
			$sucMessage = "Event Successfully Posted.";
		else
			$sucMessage = "Error: Please try Later";
	} 

	else {
		$sucMessage = $err;
	}
} 


$cat_q = "select * from categories order by id ASC";
$cat_res = mysql_query($cat_q);

$age_q = "select * from age order by id ASC";
$age_res = mysql_query($age_q);

$music_q = "select * from music";
$music_res = mysql_query($music_q);

?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/function.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/common.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/redmond/jquery-ui.css" type="text/css" media="all" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>


<script type="text/javascript">
	$(function() {
		// multi-months
		$('#multi-months').multiDatesPicker({
			numberOfMonths: 3,
			<?php if ($action == "edit" && $dates != "" )  { ?>
				addDates: [<?php echo $dates; ?>], 
			<?php }?>
			//var myArray = new Array();
			//addDates: [ '05/01/2011', '05/14/2011'],
			onSelect: function(dateText, inst) {
				var dates = $('#multi-months').multiDatesPicker('getDates');
				document.getElementById("selected_dates").value = dates;
							
			}
		});
		
		
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




<div> 


 <div  class="topContainer" style="padding-top:20px;">
	<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
	<input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="" />
	
	<!--	<input type='hidden' name='venueeditid' id='venueeditid' value=''> -->
		
		<div class="eventDetailhd"><!--<span>add an <strong>event</strong></span>--></div>
		<div class="clr gap"></div>
	</div>
	<!--End Hadding -->

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
							<div class="error"><?php echo $err; ?></div>

							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Name</div>
							<div class="evLabal"><input type="text" maxlength="100" name="eventname" id="eventname" class="evInput" required="Empty" value='<?php echo $bc_event_name; ?>'/>
							Please enter valid event name eg.(abc123,testevent)</div>
							
							
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Type</div>
							<div class="evLabal">

							<select class="bc_input" name="category_id" id="category_id" onchange="dynamic_Select('admin/subcategory.php', this.value, 0 );">
							<option value="" >Select Category</option>
							<?php $sqlParent = "SELECT name,id FROM categories ";
							$resParent = mysql_query($sqlParent);
							$totalRows=mysql_num_rows($resParent);
							while($rowParent = mysql_fetch_array($resParent))
							{	
							?>
							<option value="<?php echo $rowParent['id']?>"<?php if($rowParent['id']==$bc_category_id)
							{ echo 'selected'; }?>><?php echo $rowParent['name']?></option>
						   <?php } ?>
						   	</select>
							</div>
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Sub Category:</div>
							<div class="evLabal" id="subcatmsg">
                              	<div id="subcategory_id">
								<select name="subcategory_id" class="bc_input">
									<option selected="selected" value=""></option>
								</select>
								</div>
							</div>
							<div class="clr"></div>
							<div class="evField">Music Genre</div>
							<div class="evLabal">
							<select class="bc_input" name="musicgenere_id" id="musicgenere_id" style="margin-right:10px;">

							   <option value="">-Select MusicGenere-</option>
							<?php $sqlMusic = "SELECT name,id FROM music";
							$resMusic = mysql_query($sqlMusic);
							$totalMusic= mysql_num_rows($resMusic);
							while($rowMusic = mysql_fetch_array($resMusic))
							{	

							?>
							<option value="<?php echo $rowMusic['id']?>" <?php if($rowMusic['id']==$bc_musicgenere_id)
							{ echo 'selected'; }?>><?php echo $rowMusic['name']?></option>
							<?php } ?>
							  </select>
							<!--<select class="evSel" name="" style="margin-right:10px;">
								<option>Optional</option>
							</select>
							<select class="evSel" name="">
								<option>Optional</option>
							</select>-->

							</div>
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Event Description</div>
							<div class="evLabal"><textarea name="event_description" id="event_description" cols="2" rows="4" style="height:60px;" class="evInput"><?php echo $bc_event_description; ?></textarea></div>
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Cost</div>

							<div class="evLabal">$<input type="text" name="event_cost"  maxlength="5" id="event_cost" class="evInput" value="<?php echo $bc_event_cost;?>" style="width:170px;" /></div>
							<div class="clr"></div>
							<div class="evField">Image</div>
							<div class="evLabal">
								<div class="imgBox" id="upload_area1"><img src="images/upload.gif"  alt="upload" /></div>
								<div class="fl"><input type="file" name="event_image" id="filename1"  />
								</div>
								<div class="clr"></div>

							</div>
							<div class="clr"></div>
							<div class="evField">Selling Tickets</div>
							<div class="evLabal"><input name="event_sell_ticket"  class="radio" type="radio" value="No"  />No <span style="padding-left:30px;"><input name="event_sell_ticket" class="radio" type="radio" value="Yes"  />Yes</span></div>
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
							<option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'selected'; }?>><?php echo $rowAge['name']?></option>
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
								<div class="eventsBlackHd">Event Date &amp; Time <span>(<span class="blueClr">*</span>required)</span></div>							
								<div class="clr"></div>

							</div>
							<div class="evField"><span>1.</span><span class="redClr"><font color='red'>*</font></span> Event Time</div>
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
											
										if ($key == DBout($bc_event_start_time))
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

							<div class="evField"><span>2.</span><span class="redClr"><font color='red'>*</font></span> Event Date</div>
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
						</div>
						<!--End Event Date and Time -->
					
						<!--Start Event Location -->
						<div class="eventMainCat">
							<div class="evntBlkHdMain">
								<div class="fl"><img src="<?php echo IMAGE_PATH; ?>event_black_lftcone.gif" alt="" /></div>
								<div class="fr"><img src="<?php echo IMAGE_PATH; ?>event_black_rtcone.gif" alt="" /></div>

								<div class="eventsBlackHd">Event Location <span>(<span class="blueClr">*</span>required)</span></div>							
								<div class="clr"></div>
							</div>
							<div class="locationLeft">
								<div><strong class="lightBlueClr">Search for your Venue</strong></div>
								<div class="locationField"><span class="redClr">*</span>Venue Name</div>

								<div class="locationLabal">
								<input type="text" name="venuname" id="venuname" class="evInput" value="venue name" style="width:280px;" maxlength="100" onfocus="clearText(this)"  /></div>
								<div class="clr"></div>
								<div class="locationField">Where</div>
								<div class="locationLabal">
								<input type="text" name="address" class="evInput" id="address" style="width:280px;" value="City, State, Zipcode" onfocus="clearText(this)" maxlength="100"  /></div>
								<div class="clr"></div>
								<div class="locationField">Within</div>

								<div class="locationLabal"><input type="text" name="Within" id="Within" class="evInput" 
								style="width:280px;" maxlength="4" value="10" onfocus="clearText(this)"  /></div>
								<div class="clr"></div>
								<div class="locationField"></div>
							  <div class="locationLabal"><input name="" 
								type="image" src="<?php echo IMAGE_PATH; ?>search_btn_smll.gif"  onclick="showAddress2(0);return false"/>
								   <input type="hidden" id="lng" name="lng" value="" />
								  <input type="hidden" id="lat" name="lat" value="" />
							  </div>
								<div class="clr"></div>
							</div>

						<div class="clr"></div>
							<div id='showvenu'></div>
							<div id='showvenudetail'></div>
							<div class="clr"></div>
							
							<div class="clr"></div>
							<div class="locationMap" id="map" style="width:470px; height:285px"></div>
							<div class="clr"></div>
						</div>
						<!--End Event Location -->

					
					
						<!--Start Review and Finish -->
						<div class="eventMainCat">
							<div class="evntBlkHdMain">
								<div class="fl"><img src="images/event_black_lftcone.gif" alt="" /></div>
								<div class="fr"><img src="<?php echo IMAGE_PATH; ?>event_black_rtcone.gif" alt="" /></div>
								<div class="eventsBlackHd">Review and Finish <span>(<span class="blueClr">*</span>required)</span></div>							
								<div class="clr"></div>

							</div>
							<div class="aeBotLT">By clicking the Submit button you agree to our <a href="terms-of-use.php" class="lightBlueClr" target="_blank">Terms of Use</a></div>
							<div class="fr">
							<input type="image" name="submit" value="submit" src="<?php echo IMAGE_PATH; ?>submit_event_btn.gif" onclick="" /></div>
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


<?php include_once('includes/footer.php');?>