<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";



$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;


if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];

$event_user	=	getSingleColumn('userid',"select * from `events` where `id`='$frmID'");

if($event_user!=$user_id)
			echo "<script>window.location.href='index.php';</script>";
			

if (isset($_POST["create"]) || isset($_POST["create"]) ) {

	if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
		move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
		$_SESSION['UPLOADED_TMP_NAME'] = $tmp_bc_name;
	}
	
	$occurrences	=	$_POST['occurrences'];	
	
	$bc_event_source 		= 	($_SESSION['usertype']==2) ? 'Promoter' : 'User';

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
	$bc_venu_id				=	$_POST['venue_id'];
	$bc_modify_date			=	date("Y-m-d");
	
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
	if ( count($occurrences) < 1)
		$errors[] = 'Select single date for repeat event.';
	
//	if ( trim($bc_men_preferred_age) == '' )
//		$errors[] = 'Please select Preferred Age (Men)';
//	if ( trim($bc_women_preferred_age) == '' )
//		$errors[] = 'Please select Preferred Age (Women)';
		
		
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
		//if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
			$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
			makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		
		if($bc_occupation_target){
	$bc_occupation_targets	=	'';
	for($d=0;$d<count($bc_occupation_target);$d++){
	
	$bc_occupation_targets.=	$bc_occupation_target[$d].",";
	
	}}
	
		$bc_source_id	=	"USER-".rand(); 
		$bc_publishdate	=	 date("Y-m-d");
		
		
	$sql	=	"insert into events (event_source,source_id,userid,category_id,subcategory_id,event_name,musicgenere_id,event_description,event_image,event_sell_ticket,event_age_suitab,
		event_status,publishdate,averagerating,modify_date,del_status,added_by,men_preferred_age,women_preferred_age,occupation_target,video_name,video_embed,repeat_event,repeat_freq,tags,privacy,pending_approval) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_musicgenere_id . "','" . $bc_event_description . "','" . $bc_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "','".$bc_men_preferred_age. "','".$bc_women_preferred_age. "','" . $bc_occupation_targets . "','".$bc_video_name. "','".$bc_video_embed. "','".$repeat."','".$frequency."','','". $privacy ."','0')";
		
		$res	=	mysql_query($sql);
		
		if ( $res ) {
		
		$event_id 	= mysql_insert_id();
		$_SESSION['event_ticket_id']='';
		
		////// FOR DATE & TIME //////
	if (isset($_POST['occurrences']) && $_POST['occurrences'] != ''){
	
	$date			=	array();
	$start_time		=	array();
	$start_am_pm	=	array();
	$end_time		=	array();
	$end_am_pm		=	array();
	
	foreach($occurrences as $v){
	
	$date[]			=	$v['date'];
	$start_time[]	=	$v['start_time'];
	$start_am_pm[]	=	$v['start_am_pm'];  // AM = 0, PM = 1
	$end_time[]		=	$v['end_time'];
	$end_am_pm[]	=	$v['end_am_pm'];	// AM = 0, PM = 1
	
	}
	for($i=0;$i<count($date);$i++){
	
	$one_date	=	date('Y-m-d',strtotime($date[$i]));

////// FOR START & END Time //////
	$startTime	=	'';
	$endTime	=	'';
	$startTime = $start_time[$i];
	if($start_am_pm[$i]==0)
	$startTime .= " AM";
	else
	$startTime .= " PM";
	$startTime = date("H:i", strtotime($startTime));
	
	$endTime = $end_time[$i];
	if($end_am_pm[$i]==0)
	$endTime .= " AM";
	else
	$endTime .= " PM";
	$endTime = date("H:i", strtotime($endTime));
//////////////////////////////////
	$sql_date	=	mysql_query("insert into event_dates (event_id, event_date) values('" . $event_id . "','" . $one_date . "')");
	$date_id	=	mysql_insert_id();	
	mysql_query("INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '$startTime', '$endTime', '$date_id')");
	
	}
	}
////////////////////////// //////
	
	if ($bc_venu_id != ''){
				$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $event_id . "')";
				mysql_query($sql_venue);	
			}
			
			if($bc_event_music){
			foreach($bc_event_music as $bc_event_music_value){
				$sql_event_music = "insert ignore into event_music (event_id, music_id) values('" . $event_id . "','" . $bc_event_music_value . "')";			
				mysql_query($sql_event_music);
			} 
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

echo "<script>window.location.href='saved.php?type=event&id=".$event_id."'</script>";

		
		} else {
			$sucMessage = "Error: Please try Later";
		}	
		
		
		
	}
	else{
	
	$sucMessage	=	$err;
	}

}

if ($frmID){
	$qry	=	"select * from `events` where `id`='$frmID'";
	$res = mysql_query($qry);
	while($row = mysql_fetch_array($res)){
	
	$bc_event_source 		=	$row["event_source"]; 
		$bc_source_id			=	$row["source_id"];
		$bc_fb_event_id			=	$row["fb_event_id"];
		$bc_userid				=	$row["userid"];
		$bc_category_id			=	$row["category_id"];
		$bc_subcategory_id		=	$row["subcategory_id"];
		$bc_event_name			=	$row["event_name"];
		$bc_event_description	=	$row["event_description"];
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
		$bc_men_preferred_age	=	$row['men_preferred_age'];
		$bc_women_preferred_age	=	$row['women_preferred_age'];
		$pending_approval		=	$row['pending_approval'];
		$privacy				= 	$row['privacy'];
		$tags					= 	$row['tags'];
		$bc_occupation_target	=	$row['occupation_target'];
		$bc_video_name			=	$row['video_name'];
		$bc_video_embed			=	$row['video_embed'];
		
		
		$bc_gallery	=	getSingleColumn('name',"select * from `event_gallery` where `event_id`='$frmID'");
		
		$bc_musicgenere_id = array();
		$r4 = mysql_query("select * from event_music where event_id='$frmID'");
		while ( $ro4 = mysql_fetch_assoc($r4) )
			$bc_event_music[] =	$ro4["music_id"];
			
		
	$sql	=	"SELECT * FROM `venue_events` where `event_id`='$frmID'";
	$rt	=	mysql_query($sql);
	while($ro = mysql_fetch_array($rt)){
	$bc_venue_id	=	$ro['venue_id'];
	}
	$sql = "select * from `venues` where `id`='$bc_venue_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_venue_address	=	$ro['venue_address'];
	$bc_venue_name		=	$ro['venue_name'];
	$bc_venue_city		=	$ro['venue_city'];
	$bc_venue_zip		=	$ro['venue_zip'];
	}
	
	$qry = "select * from `event_hosts` where `event_id`='$frmID'";
	$r = mysql_query($qry);
	while($ro = mysql_fetch_array($r)){
	$bc_event_host			=	$ro['host_name'];
	$bc_host_description	=	$ro['host_description'];
	}
	
	
	
		$event_id = $frmID;
		
		$_SESSION['event_ticket_id_for_ticket']=$event_id;
		$action = "edit";
	
	
	
	
	
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

if($first_date != ''){
	$yr = date("Y",strtotime($first_date));
	$mon = date("m",strtotime($first_date));
	$mon1 = $mon - 1;
	$dy = date("d",strtotime($first_date));
	$first_date = $yr.", ".$mon1.", ".$dy;
}

	
	}

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
          <div id="ticketButton"> <img style="cursor:pointer" src="<?= IMAGE_PATH; ?>create_ticket.png" align="left" onclick="loadwindow('<?php echo ABSOLUTE_PATH; ?>create_ticket.php',800,752)"  /> &nbsp;
            You can create multiple ticket types for your event</div>
          <div id="event_cost">
            <input type="checkbox"  />
            &nbsp; This is not a ticketed event &nbsp; &nbsp; &nbsp; &nbsp;
            <!-- Event Cost:    $
            <input type="text" class="new_input" style="width:50px; font-weight:bold" />-->
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
                  <input name="min_age_allow" class="unique" type="checkbox" value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'checked'; }?>>
                  <?= $rowAge['name']; ?>
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
                  <option value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$bc_men_preferred_age)
							{ echo 'selected'; }?>>
                  <?=$rowAge['name']?>
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
                  <option value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$bc_women_preferred_age)
							{ echo 'selected'; }?>>
                  <?=$rowAge['name']?>
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
                  <input <?php echo $che;?> id="<?php echo $no; ?>" type="checkbox" style="float:left" name="event_music[]" value="<?=$rowMusic['id']?>"   />
                  <div style="float:left; margin-right:5px">
                    <?=$rowMusic['name']?>
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
			$selected = checkForSelected($rw['id'],$frmID);
			echo '<div style="float:left; width:50%; padding:3px 0"><label><input type="checkbox" '.$selected.' value="'.$rw['id'].'" name="occupation_target[]" /> &nbsp;'.$rw['occupation'].'</label></div>';
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
            <?php 
	if( $bc_image != ''  ) {
	if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
		$bc_image1 = ABSOLUTE_PATH . 'event_images/th_'.$bc_image;
	else
		$bc_image1 = $bc_image;	
		
	echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	echo $image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer"
	rel="events|event_image|'.$bc_image.'|../event_images/|delImg_image" />';
}
else
	echo '<img src="admin/images/no_image.png" class="dynamicImg"width="75" height="76" />';
	
	echo '<br>';
?>
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
            <?= $bc_gallery; ?>
            <input type="text" name="gallery" value="<?php if ($bc_gallery){echo $bc_gallery;}else{ echo "Create a name for your image gallery (i.e. Dress Code)"; } ?>" id="gname" onfocus="removeText(this.value,'Create a name for your image gallery (i.e. Dress Code)','gname');" onblur="returnText('Create a name for your image gallery (i.e. Dress Code)','gname');" class="new_input" style="width:534px;" />
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
              <span id="add_more_btn"><img src="<?= IMAGE_PATH; ?>add_another_gallery.png" onclick="add_another_gallery(0);" title="Add onother Gallery" style="cursor:pointer" /></span>
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
                    <?php
				 $res = mysql_query("select * from `event_dates` where `event_id`='$frmID'");
				 $unique_id	=	0;
				 $totalOccurrences	=	mysql_num_rows($res);
				 while($row = mysql_fetch_array($res)){
				 $unique_id++;
				 $date_id	=	$row['id'];
				 $re = mysql_query("select * from `event_times` where `date_id`='$date_id'");
				 while($ro = mysql_fetch_array($re)){
				 $start_time	=	$ro['start_time'];
				 $end_time		=	$ro['end_time'];
				 }
				 ?>
                    <tr id="z_occurrence_row_<?php echo $unique_id ?>">
                      <td class="z-occurrence-date-cell z-col-1"><input type="hidden" class="z-occurrence-id" name="occurrences[<?php echo $unique_id ?>][occurrence_id]" value="<@=occurrence_id@>" />
                <input type="hidden" class="z-occurrence-date" name="occurrences[<?php echo $unique_id ?>][date]" value="<?php echo date('m-d-Y', strtotime($row['event_date'])); ?>" />
                        <?php echo date('D, d M, Y', strtotime($row['event_date'])); ?> </td>
                      <td class="z-occurrence-type-cell z-col-2"><select name="occurrences[<?php echo $unique_id ?>][date_type]" class="z-occurrence-type">
                          <option value="0" >Normal</option>
                          <option value="1" >Tickets on Sale</option>
                          <option value="2" >Opening Night</option>
                          <option value="3" >Special Event</option>
                        </select>
                      </td>
                      <td class="z-time-cell z-col-3"><div class="z-occurrence-start-time-layer" >
                          <input type="text" class="z-occurrence-start-time z-input-time" name="occurrences[<?php echo $unique_id ?>][start_time]" value="<?php echo date('h:i',strtotime($start_time)); ?>"  />
                          <select class="z-occurrence-start-am-pm" name="occurrences[<?php echo $unique_id ?>][start_am_pm]" >
                            <option value="0" <?php if (date('A',strtotime($start_time))=='AM'){ echo "selected='selected'";}; ?>>AM</option>
                            <option value="1" <?php if (date('A',strtotime($start_time))=='PM'){ echo "selected='selected'";}; ?>>PM</option>
                          </select>
                        </div>
                        <div class="z-occurrence-end-time-layer" <?php if ($end_time=='00:00:00'){ echo 'style="display:none"'; } ?>>
						
<input type="text" class="z-occurrence-end-time z-input-time" name="occurrences[<?php echo $unique_id ?>][end_time]" value="<?php
if ($end_time!='00:00:00'){ echo date('h:i',strtotime($end_time)); }?>"  />
                          <select class="z-occurrence-end-am-pm" name="occurrences[<?php echo $unique_id ?>][end_am_pm]"  >
                            <option value="0" <?php if (date('A',strtotime($end_time))=='AM'){ echo "selected='selected'";}; ?>>AM</option>
                            <option value="1" <?php if (date('A',strtotime($end_time))=='PM'){ echo "selected='selected'";}; ?>>PM</option>
                          </select>
                        </div>
                        <a class="z-end-time-toggle">[+] end time</a> </td>
                      <td class="z-remove-cell z-col-4"><a class="z-occurrence-remove"><img src="images/icon_remove.gif" alt="remove" title="remove"></a> </td>
                    </tr>
                    <?php
}
?>
                  </tbody>
                </table>
              </div>
              <div class="z-table-bottom">
                <div id="z_total_occurrences_block"> Total Occurrences: <span id="z_total_occurrences"><?php echo $totalOccurrences; ?></span> </div>
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
        <input type="image" src="<?= IMAGE_PATH; ?>publishNow.png" name="create" value="Create Event" />
        <input type="hidden" name="create" value="Create Event" />
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
  <div  style="background:url(images/titlebar.gif) repeat-x #fff; font-size: 14px; font-weight: bold; height: 18px; padding: 5px 7px 0 7px;width: 786px; border:#000000 solid 1px; border-bottom:none;">Create Ticket<img src="<?= IMAGE_PATH;?>closePopUp.gif" onClick="closeit()" style="cursor:pointer;" title="Close" align="right"></div>
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

$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
	
		
		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide()
	// ("#delImg_" + imgInfo[4]).css("display", "none");
	$("#"+imgInfo[4]).attr("src", "admin/images/no_image.png");
	}
});
</script>
