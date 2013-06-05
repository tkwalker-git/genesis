<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";



$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;


if (isset($_POST["submit"]) || isset($_POST["submt"]) ) {

	$bc_event_name			=	$_POST["eventname"];
	$bc_event_description	=	$_POST["event_description"];
	$bc_category_id			=	$_POST["category_id"];
	$bc_subcategory_id		=	$_POST["subcategory_id"];
	$bc_min_age_allow		=	$_POST['min_age_allow'];
	$bc_men_preferred_age	=	$_POST['men_preferred_age'];
	$bc_women_preferred_age	=	$_POST['women_preferred_age'];
	$bc_event_music			=	$_POST['event_music'];
	$bc_occupation_target	=	$_POST['occupation_target'];
	$bc_gallery				=	$_POST['gallery'];
	$bc_video_name			=	$_POST['video_name'];
	$bc_video_embed			=	$_POST['video_embed'];
	$bc_venue_id			=	$_POST['venue_id'];
	
	
	$sucMessage = "";
	
	$errors = array();

	if ( trim($bc_event_name) == '' || $bc_event_name == 'Enter only the name of your event' )
		$errors[] = 'Please enter Eevent Title';
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Please enter Eevent Details';
	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please select Primary Category ';
	if ( trim($bc_subcategory_id) == '' )
		$errors[] = 'Please select Secondary Category';
	if ( trim($bc_men_preferred_age) == '' )
		$errors[] = 'Please select Preferred Age (Men)';
	if ( trim($bc_women_preferred_age) == '' )
		$errors[] = 'Please select Preferred Age (Women)';
		
	
		
		
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';
	}
	
	
	
	if (!count($errors)) {
	
	if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
		move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
		$_SESSION['UPLOADED_TMP_NAME'] = $tmp_bc_name;
	}
	
	
	
	
	
	
/////// start main gallery & image upload ///////////
	mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
	$gallery_id	=	mysql_insert_id();
	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 250, 250,'th_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
/////// end main gallery & image upload ///////////

/////// start extra gallery & image upload ///////////
/*if (is_array($_FILES['exGalName'])) {
for($i=0;$i< count($_FILES['exGalName']); $i++) {
if($_FILES['exGalName'][$i]!=''){
mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$_FILES['exGalName'][$i]', '$event_id')");
$gallery_id	=	mysql_insert_id();

if ( is_array($_FILES['images']) ) {

			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 250, 250,'th_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}


}}}*/
/////// start extra gallery & image upload ///////////





if($event_id>0){
mysql_query("INSERT INTO `event_video` (`id`, `video_name`, `video_embed`, `event_id`) VALUES (NULL, '$video_name', '$video_embed', '$event_id');");
}

		
		
		
		
		
	}
	else{
	
	$sucMessage	=	$err;
	}

}


if ($event_id != "" || isset($_POST["submit"])) {

    $dates_q = "select * from event_dates where event_id = '$event_id' ORDER BY event_date ASC";
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


include_once('includes/header.php');


?>
<script>
function dynamic_Select(ajax_page, category_id,sub_category)      
	 {  
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "cat=" + category_id + "&subcat=" + sub_category + "&class=selectBig",  
			dataType: "text/html",  
			success: function(html){
			$("#subcategory_id").html(html);
			}
	   	});
	  }	
</script>
<!--<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>/js/jquery-ui_1.8.7.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>-->
<script src="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_default.js?0" type="text/javascript"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yuiloader/yuiloader-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/event/event-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dom/dom-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/calendar/calendar-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dragdrop/dragdrop-min.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_event_edit.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />
<link href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_event_edit.css?0" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.accordion.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<script>
$(document).ready(function(){
var unique = $('input.unique');
unique.click(function(){
unique.removeAttr('checked');
$(this).attr('checked', true);
});
});




/*$(document).ready(function(){
	$('#z_calendar_container').click(function(){
		var arr	=	new Array();
			$('.z-occurrence-date').each(function() {
				var dates = $(this).val();
				arr.push(jQuery(this).val());
				var rspns = jQuery.inArray(dates, arr);
				alert(rspns);
			});
	});
});*/




function add_another_gallery(id){
//alert(id);
	var next_id = id+1;
	var new_url_feild = '<div id="main_id'+next_id+'"><div id="head" style="padding:16px 0 12px; font-size:22px">Gallery Name:<div id="info"></div><div style="display: inline-table; height: 19px; margin-left: 343px; width: 22px;" align="right"><img src="images/delete.png" style="cursor:pointer" title="Delete" onclick="deleteGallery('+next_id+');"></div></div><input type="text" name="exGalName[]" value="Create a name for your image gallery (i.e. Dress Code)" id="gname'+next_id+'" onFocus="removeText(this.value,\'Create a name for your image gallery (i.e. Dress Code)\',\'gname'+next_id+'\');" onBlur="returnText(\'Create a name for your image gallery (i.e. Dress Code)\',\'gname'+next_id+'\');" class="new_input" style="width:534px;"><div class="ev_fltlft" style="width:50%; padding:5px 0"><input type="file" name="images'+next_id+'[]" /></div><div class="ev_fltlft" style="width:50%; padding:5px 0"><input type="file" name="images'+next_id+'[]" /></div><div class="clr"></div><div class="ev_fltlft" style="width:50%; padding:5px 0"><input type="file" name="images'+next_id+'[]" /></div><div class="ev_fltlft" style="width:50%; padding:5px 0"><input type="file" name="images'+next_id+'[]" /></div><div class="clr"></div></div>';
	$('#add_more_btn').html('<img src="images/add_another_gallery.png" onclick="add_another_gallery('+next_id+');" style="cursor:pointer" title="Add onother Gallery" />');
	$('#add_url_ist').append(new_url_feild);
	
}

function deleteGallery(id){
//alert(id);
document.getElementById('main_id'+id).style.display='none';
}

$(document).ready(function() {
			$("#venue_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					if(data[0]){
					$("#venue_id").attr("value", data[0]);
					$('#venue_id').css('color', '#000');
					}
					if(data[2]){
					$("#ev_address1").attr("value", jQuery.trim(data[2]));
					$('#ev_address1').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
					if(data[3]){
					$("#ev_city").attr("value", jQuery.trim(data[3]));
					$('#ev_city').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
					if(data[4]){
					$("#ev_zip").attr("value", jQuery.trim(data[4]));
					$('#ev_zip').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
				}
			}).setOptions({
				max: '100%'
		});
});



</script>

<script>
function checkErr(){
$err	=	$('#check_errors').val();
if($err == 1){
$errText	=	$('#dErrors').val();
	alert($errText);
	$('.box').css('display','none');
	$('#box5').css('display','block');
return false;
}
return true;
}
</script>
<style>

.addEInput
{
	width:225px!important;
	height:30px!important;
}

</style>
<div style="padding-top:20px;">
  <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onsubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="id" id="queued_event_id" value="1120455984" />
    <div class="">
      <div class="width96">
        <div class="creatAnEventMdl"> Create Your Digital Flyer</div>
      </div>
    </div>
    <!-- /creatAnEvent -->
    <div class="width96"> <?php echo $sucMessage; ?>
      <div id="accordion">
        <h3>STEP 1: ADD EVENT INFORMATION</h3>
        <div id="box" class="box">
          <div id="head">Event Title</div>
          <div class="ev_title">
            <input type="text" name="eventname" value="<?php if ($bc_event_name){echo $bc_event_name;} else{ echo "Enter only the name of your event";} ?>" id="event" onFocus="removeText(this.value,'Enter only the name of your event','event');" onBlur="returnText('Enter only the name of your event','event');">
          </div>
          <div id="head">Event Details</div>
          <div>
            <textarea name="event_description" id="event_description" class="bc_input" style="width:637px; height:370px"><?php echo $bc_event_description; ?></textarea>
          </div>
        </div>
        <h3>STEP 2: CREATE TICKETS</h3>
        <div id="box" class="box">
          <div id="ticketButton"> <img src="<?php echo  IMAGE_PATH; ?>create_ticket.png" align="left" /> &nbsp;
            You can create multiple ticket types for your event</div>
          <div id="event_cost">
            <input type="checkbox"  />
            &nbsp; This is not a ticketed event &nbsp; &nbsp; &nbsp; &nbsp; 
            Event Cost:    $
            <input type="text" class="new_input" style="width:50px; font-weight:bold" />
          </div>
        </div>
        <h3>STEP 3: ADD EVENT ATTRIBUTES</h3>
        <div id="box" class="box">
          <div  class="ev_fltlft" style="width:65%">
            <div id="head" >Primary Category</div>
            <select name="category_id" id="category_id" class="selectBig" <?php if ($privacy=='Private'){ echo 'disabled="disabled"'; }?> onchange="dynamic_Select('admin/subcategory.php', this.value, 0 );">
              <option value="">-- Select Primary Category --</option>
              <?php
			$res = mysql_query("select * from `categories` ORDER BY `name` ASC");
			while($row = mysql_fetch_array($res)){?>
              <option value="<?php echo $row['id']; ?>" <?php if ($bc_category_id==$row['id']){ echo 'selected="selected"';} ?>><?php echo $row['name']; ?></option>
              <?php
			}
			?>
            </select>
          </div>
          <div  class="ev_fltlft" style="width:35%">
            <div id="head" >Secondary Category</div>
            <span id="subcategory_id">
            <?php
		  if($bc_category_id!=''){?>
            <select name="subcategory_id" class="selectBig">
              <option value="">-- Select Secondary Category --</option>
              <?php
	 $subcat_q = "SELECT * FROM sub_categories WHERE categoryid = '$bc_category_id' ORDER BY id ASC";
		$res = mysql_query($subcat_q);
	  	while( $r = mysql_fetch_assoc($res) ){
	  ?>
              <option value="<?php echo $r['id']; ?>" <?php if ($bc_subcategory_id==$r['id']){ echo 'selected="selected"'; }?>><?php echo $r['name']; ?></option>
              <?php
   }
   ?>
            </select>
            <?php
			}
		  else{
		  ?>
            <select name="subcategory_id" class="selectBig">
              <option value="">-- Select Secondary Category --</option>
            </select>
            <?php } ?>
            </span> </div>
          <div class="clr" style="height:38px">&nbsp;</div>
          <div class="stpBox">
            <div class="title">Age Requirements</div>
            <div class="data"><b>Minimum Age Allowed:</b>
              <div id="info"></div>
              <div class="age">
                <?php $sqlAge = "SELECT name,id FROM age";
						$resAge = mysql_query($sqlAge);
						$totalAge= mysql_num_rows($resAge);
						while($rowAge = mysql_fetch_array($resAge))
						{	
						?>
                <div style="float:left; width:50%;padding: 3px 0;"> &nbsp;
                  <input name="min_age_allow" class="unique" type="checkbox" value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'selected'; }?>>
                  <?php echo  $rowAge['name']; ?>
                </div>
                <?php } ?>
                <div class="clr"></div>
                <b>Preferred Age Demographic:</b>
                <div id="info"></div>
              </div>
              <div class="preferredAge"> <span>Men</span>
                <select name="men_preferred_age" style="width:104px" id="event_age_suitab">
                  <option value="">-Select age-</option>
                  <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
                  <option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'selected'; }?>>
                  <?php echo $rowAge['name']?>
                  </option>
                  <?php } ?>
                </select>
              </div>
              <div class="preferredAge"> <span>Women</span>
                <select name="women_preferred_age" style="width:104px" id="event_age_suitab">
                  <option value="">-Select age-</option>
                  <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
                  <option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'selected'; }?>>
                  <?php echo $rowAge['name']?>
                  </option>
                  <?php } ?>
                </select>
              </div>
              <div class="clr"></div>
            </div>
            <div>
              <div class="clr"></div>
            </div>
          </div>
          <div class="stpBox" style="float:right">
            <div class="title">Music Details</div>
            <div class="data">
              <ul style="list-style:none; margin:0px; padding:0 0 0 6px">
                <?php 
								$sqlMusic = "SELECT name,id FROM music";
								$resMusic = mysql_query($sqlMusic);
								$totalMusic= mysql_num_rows($resMusic);
								$no = 0;
								
								if ( !is_array($bc_event_music) )
									$bc_event_music = array();
								
								while($rowMusic = mysql_fetch_array($resMusic))
								{
									if ( in_array($rowMusic['id'],$bc_event_music) )
										$che = 'checked="checked"';
									else
										$che = '';		
								?>
                <li style="width:50%; float:left; padding:3px 0">
                  <label for="<?php echo $no; ?>">
                  <input <?php echo $che;?> id="<?php echo $no; ?>" type="checkbox" style="float:left" name="event_music[]" value="<?php echo $rowMusic['id']?>"   />
                  <div style="float:left; margin-right:5px">
                    <?php echo $rowMusic['name']?>
                  </div>
                  </label>
                </li>
                <?php $no++;} ?>
              </ul>
              <div class="clr"></div>
            </div>
            <div>
              <div class="clr"></div>
            </div>
          </div>
          <div class="clr"></div>
          <div class="occupation">
            <div class="title">Occupation Target</div>
            <div class="data">
              <?php
			$rt = mysql_query("select * from `occupation` ORDER BY `id` ASC");
			while($rw = mysql_fetch_array($rt)){
			echo '<div style="float:left; width:50%; padding:3px 0"><label><input type="checkbox" value="'.$rw['id'].'" name="occupation_target[]" /> &nbsp;'.$rw['occupation'].'</label></div>';
			}
			?>
              <div class="clr"></div>
            </div>
          </div>
        </div>
        <h3>STEP 4: ADD IMAGES AND VIDEO</h3>
        <div id="box" class="box">
          <div id="head">Main Event Image:
            <div id="info"></div>
          </div>
          <div class="ev_fltlft">
            <input type="file" name="event_image" />
          </div>
          <div class="ev_fltlft" style="padding:0 0 0 10px;">Must be JPG, GIF or PNG.<br />
            Dimensions are limited to 550 x 640px.</div>
          <div class="clr"></div>
          <div id="head">Image Galleries:
            <div id="info"></div>
          </div>
          <div class="gallery_area">
            <div id="head" style="padding:16px 0 12px; font-size:22px">Gallery Name:
              <div id="info"></div>
            </div>
            <input type="text" name="gallery" value="<?php echo "Create a name for your image gallery (i.e. Dress Code)"; ?>" id="gname" onfocus="removeText(this.value,'Create a name for your image gallery (i.e. Dress Code)','gname');" onblur="returnText('Create a name for your image gallery (i.e. Dress Code)','gname');" class="new_input" style="width:534px;" />
            <div class="ev_fltlft" style="width:50%; padding:5px 0">
              <input type="file" name="images[]" />
            </div>
            <div class="ev_fltlft" style="width:50%; padding:5px 0">
              <input type="file" name="images[]" />
            </div>
            <div class="clr"></div>
            <div class="ev_fltlft" style="width:50%; padding:5px 0">
              <input type="file" name="images[]" />
            </div>
            <div class="ev_fltlft" style="width:50%; padding:5px 0">
              <input type="file" name="images[]" />
            </div>
            <div class="clr"></div>
            <!--<div id="add_url_ist"></div>
            <div align="right"><br />
              <br />
              <span id="add_more_btn"><img src="<?php echo  IMAGE_PATH; ?>add_another_gallery.png" onclick="add_another_gallery(0);" title="Add onother Gallery" style="cursor:pointer" /></span>
		    </div>-->
          </div>
          <div id="head">Event Video:
            <div id="info"></div>
          </div>
          <div class="gallery_area">
            <div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div>
            <input type="text" name="video_name" value="<?php if ($bc_video_name){ echo $bc_video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name" onFocus="removeText(this.value,'Enter the name of your video','video_name');" onBlur="returnText('Enter the name of your video','video_name');" class="new_input" style="width:534px;">
            <div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div>
            <textarea class="new_input" name="video_embed" style="width:534px; height:130px;"><?php if ($bc_video_embed){ echo $bc_video_embed; }?>
</textarea>
          </div>
        </div>
        <h3>STEP5: ADD EVENT DATE AND TIMES</h3>
        <div id="box" class="box">
          <div id="z_listing_event_form_occurrences" class="z-group z-panel-occurrences">
            <div class="ev_fltlft">
              <div id="head">Select Event Date(s)</div>
              <a name="z_repeat_pattern_list"></a>
              <!--	<ul class="z-tabs" style="display:">
            <li class="z-current"><a href="#">Calendar View</a></li>
            <li><a href="#">Advanced View</a></li>
          </ul>-->
              <div id="z_tab_calender_view" class="z-calendar-view z-tab-content" style="display: block">
                <label><sup>&#42;</sup> Click one or more dates for your event or event series on the calendars below.</label>
                <div class="yui-skin-sam">
                  <div id="z_calendar_container"></div>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
            <div class="ev_fltrght" style="width:272px">
              <div id="head"> Select Event Time(s)
                <div id="info"></div>
              </div>
              <div style="padding:21px 0 0 0">
                <label for="z_event_start_time" class="z-inline"><sup>*</sup> Start Time</label>
                <input id="z_event_start_time" class="z-input-time" type="text" value="7:00" name="start_time"/>
                <select id="z_event_start_am_pm" name="start_time_am_or_pm">
                  <option value="0">AM</option>
                  <option selected="selected" value="1">PM</option>
                </select>
                <div class="clr" style="height:10px">&nbsp;</div>
                <label for="z_event_end_time" class="z-inline">End Time (optional)</label>
                <input id="z_event_end_time" class="z-input-time" type="text" name="end_time"/>
                <select id="z_event_end_am_pm" name="end_time_am_or_pm">
                  <option value="0">AM</option>
                  <option selected="selected" value="1">PM</option>
                </select>
              </div>
            </div>
            <div class="clr"></div>
            <div id="head">Event Preview</div>
            <div id="z_tab_advanced_view" class="z-advanced-view z-tab-content">
              <div class="z-date-range-block yui-skin-sam">
                <label class="z-input-date-label"><sup>&#42;&nbsp;</sup>Start Date</label>
                <input class="z-input-date" id="z_start_date_advanced" type="text" value="1/1/2009"  />
                <img alt="Calendar" id="z_show_popup_start_date" src="http://js.zvents.com/images/calendar.gif?0" />
                <div id="z_popup_start_date_container" class="z-popup-date" style="display: none"></div>
                <div id="z_popup_end_date_container" class="z-popup-date" style="display: none"></div>
                <div class="z-end-date-block" id="z_end_date_block" style="display: none">
                  <label class="z-input-date-label"><sup>&#42;&nbsp;</sup>End Date</label>
                  <input class="z-input-date" id="z_end_date_advanced" type="text" value="1/1/2009"  />
                  <img alt="Calendar" id="z_show_popup_end_date" src="http://js.zvents.com/images/calendar.gif?0" /> </div>
              </div>
              <div id="z_repeat_pattern">
                <p><strong>Event Recurrence</strong></p>
                <p>From your chosen start date this event:</p>
                <p>
                  <label for="repeat_type">Repeats:</label>
                  <select id="z_occurrence_repeat_type_select" name="z_occurrence_repeat_type_select">
                    <option value="z_repeat_once_layer">Occurs only once</option>
                    <option value="z_repeat_daily_layer">Daily</option>
                    <option value="z_repeat_weekly_layer">Weekly</option>
                    <option value="z_repeat_monthly_layer">Monthly</option>
                  </select>
                </p>
                <div  id="z_repeat_once_layer" ></div>
                <div  id="z_repeat_daily_layer" style="display: none;">
                  <p>
                    <label for="daily_repeat_interval" ><span class="required">&#42;&nbsp;</span>Repeat every:</label>
                    <input type="text" class="date" id="daily_repeat_interval" name="daily_repeat_interval" />
                    day(s) </p>
                </div>
                <div  id="z_repeat_weekly_layer"  style="display: none;">
                  <p>
                    <select id="z_weekly_repeat_interval">
                      <option value="1" selected="selected">Every</option>
                      <option value="2">Every other</option>
                      <option value="3">Every third</option>
                      <option value="4">Every fourth</option>
                    </select>
                  </p>
                  <p id="z_weekly_repeat_days">
                    <input type="checkbox" name="repeat_day" value="0" />
                    Su
                    <input type="checkbox" name="repeat_day" value="1" />
                    M
                    <input type="checkbox" name="repeat_day" value="2" />
                    T
                    <input type="checkbox" name="repeat_day" value="3" />
                    W
                    <input type="checkbox" name="repeat_day" value="4" />
                    Th
                    <input type="checkbox" name="repeat_day" value="5" />
                    F
                    <input type="checkbox" name="repeat_day" value="6" />
                    Sa </p>
                </div>
                <div  id="z_repeat_monthly_layer" style="display: none;">
                  <table border="0">
                    <tr>
                      <td><input type="radio" id="z_monthly_day" class="z-monthly-repeat-type" name="monthly_repeat_type" value="day" />
                      </td>
                      <td> On Day
                        <input type="text" class="z-date" id="z_monthly_day_of_month" name="Within" />
                        of every month </td>
                    </tr>
                    <tr>
                      <td><input type="radio" id="z_monthly_pattern" class="z-monthly-repeat-type" name="monthly_repeat_type" value="pattern" />
                      </td>
                      <td> On the
                        <select name="Every" id="z_monthly_pattern_period">
                          <option value="0" selected="selected">First</option>
                          <option value="1">Second</option>
                          <option value="2">Third</option>
                          <option value="3">Fourth</option>
                        </select>
                        <select name="Every" id="z_monthly_pattern_day">
                          <option value="0" selected="selected">Sunday</option>
                          <option value="1" >Monday</option>
                          <option value="2">Tuesday</option>
                          <option value="3">Wednesday</option>
                          <option value="4">Thursday</option>
                          <option value="5">Friday</option>
                          <option value="6">Saturday</option>
                        </select>
                      </td>
                    </tr>
                  </table>
                </div>
                <br />
                <ul class="clear">
                  <li>
                    <input id="z_add_repeat_date" type="button" value="+ Add to Preview List" />
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="z-clear"></div>
            </div>
            <a name="z_a_repeat_pattern_list"></a>
			<div id="z_review_errors"></div>
			<input type="hidden" id="check_errors" value=""  />
			<input type="hidden" id="dErrors" value="" />
            
            
            <div class="z-simple-box">
              <table width="100%" cellspacing="0" class="z-event-occurrences-heading">
                <tbody>
                  <tr>
                    <th width="26%" class="z-col-1">Event Date</th>
                    <th width="29%" class="z-col-2">Type of Date</th>
                    <th width="35%" class="z-col-3">Start Time</th>
                    <th width="10%" class="z-col-4">Remove</th>
                  </tr>
                </tbody>
              </table>
              <div class="z-block-event-occurrences">
                <table class="z-event-occurrences" cellspacing="0">
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="z-table-bottom">
                <div id="z_total_occurrences_block"> Total Occurrences: <span id="z_total_occurrences">0</span> </div>
                <div id="z_clear_occurrences_block"> <a href="#" id="z_clear_occurrences">Clear Occurrences</a> </div>
                <div class="z-clear"></div>
              </div>
            </div>
            <div class="z-bottom"></div>
            <script type="text/plain" id="occurrence_template">
<tr id="z_occurrence_row_<@=unique_id@>">
  <td class="z-occurrence-date-cell z-col-1">
    <input type="hidden" class="z-occurrence-id" name="occurrences[<@=unique_id@>][occurrence_id]" value="<@=occurrence_id@>" />
    <input type="hidden" class="z-occurrence-date" name="occurrences[<@=unique_id@>][date]" value="<@=date@>" />
    <@=display_date@>
  </td>
  <td class="z-occurrence-type-cell z-col-2">
    <select name="occurrences[<@=unique_id@>][date_type]" class="z-occurrence-type">
      <option value="0" <@= date_type == "0" ? "selected='SELECTED'" : '' @>>Normal</option>
      <option value="1" <@= date_type == "1" ? "selected='SELECTED'" : '' @>>Tickets on Sale</option>
      <option value="2" <@= date_type == "2" ? "selected='SELECTED'" : '' @>>Opening Night</option>
      <option value="3" <@= date_type == "3" ? "selected='SELECTED'" : '' @>>Special Event</option>
    </select>
  </td>
  <td class="z-time-cell z-col-3">
    <div class="z-occurrence-start-time-layer" >
      <input type="text" class="z-occurrence-start-time z-input-time" name="occurrences[<@=unique_id@>][start_time]" value="<@=start_time@>"  />
      <select class="z-occurrence-start-am-pm" name="occurrences[<@=unique_id@>][start_am_pm]" >
        <option value="0" <@= start_am_pm == "0" ? "selected='SELECTED'" : '' @>>AM</option>
        <option value="1" <@= start_am_pm == "1" ? "selected='SELECTED'" : '' @>>PM</option>
      </select>
    </div>
    <div class="z-occurrence-end-time-layer" <@= end_time == "" ? "style='display:none'" : '' @>>
      <input type="text" class="z-occurrence-end-time z-input-time" name="occurrences[<@=unique_id@>][end_time]" value="<@=end_time@>"  />
      <select class="z-occurrence-end-am-pm" name="occurrences[<@=unique_id@>][end_am_pm]"  >
        <option value="0" <@= end_am_pm == "0" ? "selected='SELECTED'" : '' @>>AM</option>
        <option value="1" <@= end_am_pm == "1" ? "selected='SELECTED'" : '' @>>PM</option>
      </select>
    </div>
    <a class="z-end-time-toggle">[+] end time</a>
  </td>
  <td class="z-remove-cell z-col-4">
    <a class="z-occurrence-remove"><img src="images/icon_remove.gif" alt="remove" title="remove"></a>
  </td>
</tr>
</script>
          </div>
        </div>
        <h3>STEP 6: ADD LOCATION</h3>
        <div id="box" class="box"> <br>
          <br>
          <input type="text" name="venue_name" id="venue_name" class="new_input" value="<?php if ($bc_venue_name){ echo $bc_venue_name; }else{ echo "Start Typing Location Name"; } ?>" onfocus="removeText(this.value,'Start Typing Location Name','venue_name');" onblur="returnText('Start Typing Location Name','venue_name');" style="margin-bottom:2px; width:274px" />
          <br>
          <a href="javascript:void(0)" style="color:#0066FF; text-decoration:underline" onclick="windowOpener(525,645,'Add New Location','add_venue.php')"> Can't find your location? Add it here </a><br>
          <br>
          <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venue_id; ?>" />
          <input type="text" name="address1" id="ev_address1" class="new_input" value="<?php if ($bc_venue_address){ echo $bc_venue_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','ev_address1');" onBlur="returnText('Address','ev_address1');" style="width:274px">
          <br>
          <br>
          <input type="text" name="city" id="ev_city" class="new_input" value="<?php if ($bc_venue_city){ echo $bc_venue_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','ev_city');" onBlur="returnText('City','ev_city');" style="width:274px">
          <br>
          <br>
          <input type="text" name="zip" id="ev_zip" class="new_input" value="<?php if ($bc_venue_zip){ echo $bc_venue_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','ev_zip');" onBlur="returnText('Zip / Postal Code','ev_zip');" style="width:274px">
        </div>
      </div>
      <div align="right">
        <input type="image" src="<?php echo  IMAGE_PATH; ?>publishNow.png" name="submit" value="submit" />
        <input type="hidden" name="submit" value="submit" />
      </div>
    </div>
  </form>
</div>
<script type="text/javascript">
//<![CDATA[
(function($, Z){
  $('#z_listing_event_form').event_form({
    listing_class: 'QueuedEvent',
    listing_id: '1120455984',
    session_user: true,
    is_premium_listing: false,
    is_published_event: false,
    skip_campaign_redirect: false,
    partner_id: 0,
    internal_user: false,
    enhanced_paid_for: false,
    promoted_paid_for: false
  });
})($ZJQuery, Zvents);
//]]>
</script>
<?php include_once('includes/footer.php');?>
<div id="dwindow" style="position:absolute;background-color:#fff;cursor:hand;left:0px;top:0px;display:none; z-index:9999">
  <div  style="background:url(images/titlebar.gif) repeat-x #fff; font-size: 14px; font-weight: bold; height: 18px; padding: 5px 7px 0 7px;width: 786px; border:#000000 solid 1px; border-bottom:none;">Create Ticket<img src="<?php echo  IMAGE_PATH;?>closePopUp.gif" onClick="closeit()" style="cursor:pointer;" title="Close" align="right"></div>
  <div id="dwindowcontent" style="height:100%">
    <iframe id="cframe" src="" width="800px" height="100%" style="border:#000 solid 1px; border-top:none; background:#fff"></iframe>
  </div>
</div>
<script>
tinyMCE.init({
	mode : "exact",
	elements : "event_description",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,forecolor,backcolor,bullist,numlist,outdent,indent,blockquote,anchor,cleanup",
	theme_advanced_buttons2 : "cut,copy,paste,styleselect,formatselect,fontselect,fontsizeselect,hr,code,image",
	theme_advanced_font_sizes: "10px,11px,12px,13px,14px,15px,16px,17,18px,19px,20px,22px,24px,26px,28px,30px,36px",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	remove_script_host : false,
    convert_urls : false,
	content_css : "site_styles.css?1",
	plugins : 'inlinepopups,imagemanager'
});
</script>
