<?php 

include_once('admin/database.php'); 
include_once('site_functions.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];

$event_user	=	getSingleColumn('userid',"select * from `events` where `id`='$frmID'");

if($event_user!=$user_id)
			echo "<script>window.location.href='index.php';</script>";

$already_uploaded = 0;

if (isset($_POST["submit"]) || isset($_POST["submit_x"]) ) {
	
	if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
		move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
		$_SESSION['UPLOADED_TMP_NAME'] = $tmp_bc_name;
	}		
	
	
	$bc_event_source 		= 	($_SESSION['usertype']==2) ? 'Promoter' : 'User';
	
	$catee = $bc_category_id . '|' . $bc_subcategory_id;
	
	$bc_event_name			=	$_POST["eventname"];
	$bc_musicgenere_id		=	$_POST["musicgenere_id"];
	$bc_event_start_time	=	$_POST["event_start_time"];
	$bc_event_music			=	$_POST["event_music"];
	$bc_event_end_time		=	$_POST["event_end_time"];
	$bc_event_description	=	$_POST["event_description"];
	$bc_event_cost			=	$_POST["event_cost"];
	$bc_event_host			=	$_POST["event_host"];
	$bc_host_description	=	$_POST["host_description"];
	if($bc_event_host == "Enter the name of your organization"){
	$bc_event_host = '';
	}
	$bc_event_image			=	$_FILES['event_image']['name'];
	$bc_event_sell_ticket	=	$_POST["event_sell_ticket"];
	$bc_event_age_suitab	=	$_POST["event_age_suitab"];
	$bc_event_status		=	'1';
	$bc_averagerating		=	$_POST["averagerating"];
	$bc_modify_date			=	date("Y-m-d");
	$bc_del_status			=	$_POST["del_status"];
	$bc_added_by			=	$bc_event_host;
	$bc_dates				=   $_POST['selected_dates'];
	$bc_venu_id				=	DBin($_POST['venue_id']);

	$r	=	mysql_query("select * from `venues` where `id`='$bc_venu_id'");
	while($ro = mysql_fetch_array($r)){
	$bc_venue_address	=	$ro['venue_address'];
	$bc_venue_name		=	$ro['venue_name'];
	$bc_venue_city		=	$ro['venue_city'];
	$bc_venue_zip		=	$ro['venue_zip'];
	}
	

	$frequency				=	$_POST['frequency'];
	$repeat					=	$_POST['erepeat'];
	$tags					= 	DBin($_POST['tags']);
	$privacy				= 	$_POST['privacy'];
	
	if($privacy=='Public'){
	$bc_category_id			=	$_POST['category_id'];
	$bc_subcategory_id		=	$_POST['subcategory_id'];
	}else{
	$bc_category_id			=	'';
	$bc_subcategory_id		=	'';
	}
	
	$dates_sel = array();
	if ( $_POST['selected_dates'] != '' ) {
		$dates_sel = explode(",", $_POST['selected_dates']);
		for($i=0; $i<count($dates_sel); $i++)
		$dates .= "'". $dates_sel[$i] . "',";
	}
	
	$sucMessage = "";
	
	$errors = array();

	if ( trim($bc_event_name) == '' || $bc_event_name == 'Enter the name of your event' )
		$errors[] = 'Please enter Eevent Name';
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Event Description is empty.';
	if ( trim($bc_event_age_suitab) == '' )
		$errors[] = 'Please Select Age Suitable';
	if ( trim($bc_event_start_time) == '' )
		$errors[] = 'Please Select Start Time';
	if ( trim($bc_event_end_time) == '' )
		$errors[] = 'Please Select End Time';
		
	if($privacy=='Public'){
	if($bc_category_id==''){
	$errors[] = 'Please Select Category';
	}
	if($bc_subcategory_id==''){
	$errors[] = 'Please Select Sub Category';
	}
	
	
	}
	
	

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
		//if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
			$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
			makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		
		$bc_source_id	=	"USER-".rand(); 
		$bc_publishdate	=	 date("Y-m-d");
		 
		$sql	=	"UPDATE `events` SET `category_id` = '$bc_category_id', `subcategory_id` = '$bc_subcategory_id', `event_name` = '$bc_event_name', `event_start_time` = '$bc_event_start_time', `event_end_time` = '$bc_event_end_time', `event_description` = '$bc_event_description', `event_sell_ticket` = '$bc_event_sell_ticket', `event_age_suitab` = '$bc_event_age_suitab', `tags` = '$tags', `privacy` = '$privacy' WHERE `id` = '$frmID'";
		
		$res	=	mysql_query($sql);
		
		if ( $res ) {
			$event_id 	= $frmID;
			
			if($bc_event_sell_ticket=='No'){
			$delids	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$event_id'");
			mysql_query("DELETE FROM `event_ticket` WHERE `event_id` = '$event_id'");
			mysql_query("DELETE FROM `event_ticket_price` WHERE `ticket_id` = '$delids'");
			}
			
			$t_id	=	$_SESSION['event_ticket_id'];
			if($t_id){
			mysql_query("UPDATE `event_ticket` SET `event_id` = '$event_id' WHERE `id` = '$t_id'");
			$_SESSION['event_ticket_id']='';
			$_SESSION['event_ticket_id_for_ticket']='';
			}
			
			
			$_SESSION['UPLOADED_TMP_NAME'] = '';
		
			
			mysql_query("DELETE FROM `event_hosts` WHERE `event_id` = '$frmID'");
			mysql_query("INSERT INTO `event_hosts` (`id`, `source_id`, `event_id`, `host_name`, `host_description`) VALUES (NULL, 'Promoter-". time() ."', '$frmID', '$bc_event_host', '$bc_host_description')");
			
			
			if (isset($_POST['selected_dates']) && $_POST['selected_dates'] != ''){
				mysql_query("DELETE FROM `event_dates` WHERE `event_id` = '$frmID'");
				for($i=0; $i<count($dates_sel); $i++){			
			$sql_date = "insert into event_dates (event_id, event_date) values('" . $frmID . "','" . date("Y-m-d",strtotime($dates_sel[$i])) . "')";			
					mysql_query($sql_date) ;
				} 
				
			}
			
			if ($bc_venu_id != ''){
			mysql_query("DELETE FROM `venue_events` WHERE `event_id` = '$frmID'");
			$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $frmID . "')";
				mysql_query($sql_venue);	
			}
			
			mysql_query("DELETE FROM `event_music` WHERE `event_id` = '$frmID'");
			if($bc_event_music){
			foreach($bc_event_music as $bc_event_music_value){
			$sql_event_music = "INSERT INTO `event_music` (`id`, `event_id`, `music_id`) VALUES (NULL, '$frmID', '$bc_event_music_value')";			
			mysql_query($sql_event_music);
			}  
			}
			echo "<script>window.location.href='saved.php?type=event&id=".$frmID."'</script>";
			//$sucMessage = "Event Successfully Saved.";
		} else {
			$sucMessage = "Error: Please try Later";
		}	
	} 

	else {
		$sucMessage = $err;
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

$cat_q = "select * from categories order by id ASC";
$cat_res = mysql_query($cat_q);

$age_q = "select * from age order by id ASC";
$age_res = mysql_query($age_q);

$music_q = "select * from music";
$music_res = mysql_query($music_q);


function ret_music_ids_again($event_id)
{
	if($event_id != "")
	{
		$query = "select music_id from event_music where event_id = ". $event_id ." ";
		$run_query = mysql_query($query);
		$mysql_count_rows = mysql_num_rows($run_query);
		$m_id = array();
		if($mysql_count_rows > 0)
			while($ch_data = mysql_fetch_assoc($run_query))
				$m_id[]	=	$ch_data['music_id'];
	}
	
	return $m_id;	
}		

$re = ret_music_ids_again($event_id);

$ret_query = "select * from events where id = " .$event_id. " ";
$res =	mysql_query($ret_query);
if($res)
	while($ret_event_data = mysql_fetch_assoc($res))
		$event_sell_ticket	=	$ret_event_data['event_sell_ticket'];



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
		
		$bc_event_start_time	=	$row["event_start_time"];
		$bc_event_end_time		=	$row["event_end_time"];
		$bc_event_start_am_time	=	$row["event_start_am_time"];
		$bc_event_end_am_time	=	$row["event_end_am_time"];
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
		$pending_approval		=	$row['pending_approval'];
		$privacy				= 	$row['privacy'];
		$tags					= 	$row['tags'];
		
		
		
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


	//
//	$bc_event_name			=	$row["event_name"];
//	$bc_musicgenere_id		=	$row["musicgenere_id"];
//	$bc_event_start_time	=	$row["event_start_time"];
//	$bc_event_music			=	$row["event_music"];
//	$bc_event_end_time		=	$row["event_end_time"];
//	$bc_event_description	=	$row["event_description"];
//	$bc_event_cost			=	$row["event_cost"];
//	$bc_event_host			=	$row["event_host"];
//	$bc_host_description	=	$row["host_description"];
//	if($bc_event_host == "Enter the name of your organization"){
//	$bc_event_host = '';
//	}
//	$bc_event_image			=	$row['image'];
//	$bc_event_sell_ticket	=	$row["event_sell_ticket"];
//	$bc_event_age_suitab	=	$row["event_age_suitab"];
//	$bc_event_status		=	'1';
//	$bc_averagerating		=	$row["averagerating"];
//	$bc_modify_date			=	date("Y-m-d");
//	$bc_del_status			=	$row["del_status"];
//	$bc_added_by			=	$bc_event_host;
//	$bc_dates				=   $row['selected_dates'];
//	$bc_venu_id				=	DBin($row['venue_id']);
//
//	$frequency				=	$row['frequency'];
//	$repeat					=	$row['erepeat'];
//	$tags					= 	DBin($row['tags']);
//	$privacy				= 	$row['privacy'];
	
	
	}

}


include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>/js/jquery-ui_1.8.7.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>
<script type="text/javascript">
	$(function() {
		// multi-months
		$('#multi-months').multiDatesPicker({
			numberOfMonths: 3,
			<?php if ( trim($dates) != '' ) { ?>
				addDates: [<?php echo $dates; ?>], 
			<?php } ?>	
			//var myArray = new Array();
			//addDates: [ '05/01/2011', '05/14/2011'],
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
		$("#venue_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>/get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
//				alert(data);
	//			return false;
					if(data[0]){
					$("#venue_id").attr("value", data[0]);
					$('#venue_id').css('color', '#000');
					}
					if(data[2]){
					$("#ev_address1").attr("value", jQuery.trim(data[2]));
					$('#ev_address1').attr('readonly', 'readonly');
					}
					if(data[3]){
					$("#ev_city").attr("value", jQuery.trim(data[3]));
					$('#ev_city').attr('readonly', 'readonly');
					}
					if(data[4]){
					$("#ev_zip").attr("value", jQuery.trim(data[4]));
					$('#ev_zip').attr('readonly', 'readonly');
					
					}
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


function showHidePrivacy(){
if(document.getElementById('Private').checked==true){
 $("#category_id").attr("value","");
 jQuery("select[name='subcategory_id']").attr("disabled","");
 $("#category_id").attr("disabled","disabled");
 jQuery("select[name='subcategory_id']").attr("disabled","disabled");
 $("#tags").attr("disabled","disabled");

}
else{
 $("#category_id").attr("disabled","");
 $("#subcategory_id").attr("disabled","");
 $("#tags").attr("disabled","");
}
}
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
	
	
	
tinyMCE.init({
	mode : "exact",
	elements : "host_description",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,justifyleft,justifycenter,justifyright, fontsizeselect,forecolor,code",
	theme_advanced_buttons2 : "",
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



function showTicket(value){
	if(value=='Yes'){
	$("#ticket").show();
	}else{
	$("#ticket").hide();
	}}
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
<div style="padding-top:20px;">
  <form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
    <input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="" />
    <div class="creatAnEvent">
      <div class="width96">
        <div class="creatAnEventMdl"> Create an Event </div>
        <div class="topRight"><a href="#"><img src="<?php echo IMAGE_PATH; ?>preview.gif" alt="" title="Preview"></a> </div>
      </div>
    </div>
    <!-- /creatAnEvent -->
    <div class="width96" style="padding-top:23px">
      <div class="leftArea"> <?php echo $sucMessage; ?>
        <div class="step">STEP 1: ADD EVENT TITLE</div>
        <div class="ev_title">
          <input type="text" name="eventname" value="<?php if ($bc_event_name){echo $bc_event_name;} else{ echo "Enter the name of your event";} ?>" id="event" onFocus="removeText(this.value,'Enter the name of your event','event');" onBlur="returnText('Enter the name of your event','event');">
          <div class="right"></div>
        </div>
        <!-- /ev_title --><br />
        <div class="step">STEP 2: ADD EVENT DETAILS</div>
        <div>
          <textarea name="event_description" id="event_description" class="bc_input" style="width:637px; height:425px"><?php echo $bc_event_description; ?></textarea>
        </div>
        <br />
        <div class="step" style="padding-bottom:none">STEP 3: ADD WHEN</div>
        <div class="evField2">Event Time:</div>
        <div class="evLabal2">
          <select class="inp3" name="event_start_time" id="eventtime" style="width:105px;">
            <option value="">select</option>
            <?php 
									$bc_arr_event_start_time = array("12:00 AM"=>"12:00 AM", "12:30 AM"=>"12:30 AM", "1:00 AM"=>"01:00 AM", "1:30 AM"=>"01:30 AM", "2:00 AM"=>"02:00 AM", "2:30 AM"=>"02:30 AM", "3:00 AM"=>"03:00 AM", "3:30 AM"=>"03:30 AM", "4:00 AM"=>"04:00 AM", "5:00 AM"=>"05:00 AM", "5:30 AM"=>"05:30 AM", "6:00 AM"=>"06:00 AM", "6:30 AM"=>"06:30 AM", "7:00 AM"=>"07:00 AM", "7:30 AM"=>"07:30 AM", "8:00 AM"=>"08:00 AM", "8:30 AM"=>"08:30 AM", "9:00 AM"=>"09:00 AM", "9:30 AM"=>"09:30 AM", "10:00 AM"=>"10:00 AM", "10:30 AM"=>"10:30 AM", "11:00 AM"=>"11:00 AM", "11:30 AM"=>"11:30 AM", "12:00 PM"=>"12:00 PM", "12:30 PM"=>"12:30 PM", "1:00 PM"=>"01:00 PM", "1:30 PM"=>"01:30 PM", "2:00 PM"=>"02:00 PM", "2:30 PM"=>"02:30 PM", "3:00 PM"=>"03:00 PM", "3:30 PM"=>"03:30 PM", "4:00 PM"=>"04:00 PM", "4:30 PM"=>"04:30 PM", "5:00 PM"=>"05:00 PM", "5:30 PM"=>"05:30 PM", "6:00 PM"=>"06:00 PM", "6:30 PM"=>"06:30 PM", "7:00 PM"=>"07:00 PM", "7:30 PM"=>"07:30 PM", "8:00 PM"=>"08:00 PM", "8:30 PM"=>"08:30 PM", "9:00 PM"=>"09:00 PM", "9:30 PM"=>"09:30 PM", "10:00 PM"=>"10:00 PM", "10:30 PM"=>"10:30 PM", "11:00 PM"=>"11:00 PM", "11:30 PM"=>"11:30 PM"); 
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
          <select class="inp3" name="event_end_time" id="eventtime1" style="width:105px;">
            <option value="">select</option>
            <?php 
									$bc_arr_event_end_time = $bc_arr_event_start_time;
									foreach($bc_arr_event_end_time as $key => $val)
									{
											
										if ($key == DBout($bc_event_end_time))
											$sel = 'selected="selected"';
										else
											$sel = "";	
									?>
            <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
            <?php } ?>
          </select>
        </div>
        <div class="evField2">Selling Tickets:</div>
        <div class="evLabal2">
          <label for="event_sell_ticket_no">
          <input name="event_sell_ticket" id="event_sell_ticket_no" <?php if($bc_event_sell_ticket == 'No'){?> checked="checked"<?php }?>  class="radio" type="radio" value="No" onclick="showTicket(this.value);"   />
          No </label>
          <label for="event_sell_ticket_yes" style="margin-left:5px;">
          <input <?php if($bc_event_sell_ticket == 'Yes'){?> checked="checked"<?php }?>  name="event_sell_ticket" id="event_sell_ticket_yes" class="radio" type="radio" value="Yes" onclick="showTicket(this.value);"   />
          Yes</label>
        </div>
		<div class="evField2"></div>
        <div class="evLabal2">
          <div id="ticket" style="display:<?php if ($bc_event_sell_ticket=='Yes'){ echo "block";} else{ echo "none"; } ?>"> <img  style="cursor:pointer" src="<?= IMAGE_PATH; 
		  $rt = mysql_query("select * from `event_ticket` where `event_id`='$frmID' ");
		  if(mysql_num_rows($rt)){
		  $editTicket='yes';
		  echo "edit_ticket.gif";
		  }
		  else{
		  $editTicket='yes';
		  echo "create_ticket.gif";
		  }
		  ?>" onclick="loadwindow('<?php echo ABSOLUTE_PATH; ?>create_ticket.php',800,800)" /></div>
        </div>
        <div class="clr"></div>
        <div class="clr"></div>
        <div class="stepMin">Event Date:</div>
        <div>
          <div class="clenderCon" >
            <div class="clenderBox" >
              <div>
                <div id="multi-months"></div>s
                <font color="#000"><strong>Click on date to select OR unselect</strong></font><br>
                &nbsp;</div>
            </div>
          </div>
        </div>
		
		<div class="step" style="padding-bottom:5px;">STEP 6: SET PRIVACY</div>
		<div class="ev_box">
        <label for="Public">
        <input type="radio" name="privacy" value="Public" <?php if ($privacy!='Private'){ echo 'checked="checked"'; }?> id="Public" onChange="showHidePrivacy();">
        This event is public and will be listed in thw Eventgrabber directory and on search engines.</label>
        <a href="#"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt="" title=""></a><br>
        <div class="step7"> <span style="float:left">Select categories for youe event:
            <select name="category_id" class="inp3" id="category_id" <?php if ($privacy=='Private'){ echo 'disabled="disabled"'; }?> onchange="dynamic_Select('admin/subcategory.php', this.value, 0 );">
              <option value="">Select a Primary category</option>
              <?php
			$res = mysql_query("select * from `categories` ORDER BY `name` ASC");
			while($row = mysql_fetch_array($res)){?>
              <option value="<?php echo $row['id']; ?>" <?php if ($bc_category_id==$row['id']){ echo 'selected="selected"';} ?>><?php echo $row['name']; ?></option>
              <?php
			}
			?>
            </select>
          </span> <span id="subcategory_id" style="float:left">
		  <?php
		  if($bc_category_id!='' && $bc_category_id!=0){?> 
<select name="subcategory_id" class="bc_input">
	  <option value="">-- Select --</option> 
	  <?php
	  $subcat_q = "SELECT * FROM sub_categories WHERE categoryid = '$bc_category_id' ORDER BY id ASC";
		$res = mysql_query($subcat_q);
	  	while( $r = mysql_fetch_assoc($res) ){  
	  		if ( $r['id'] == $_GET['subcat'] )
				$sele = 'selected="selected"';
			else
				$sele = '';	
	  ?>
	 <option <?php echo $sele;?> value="<?php echo $r['id']; ?>" <?php if ($bc_subcategory_id==$r['id']){ echo 'selected="selected"'; ?>><?php echo $r['name']; ?></option>
<?php } ?>
 </select>  
 <?php		  }
		  }
		  else{
		  ?>
          <select disabled="disabled" name="secondary_category">
            <option value="">Select a Secondary category</option>
          </select>
		  <?php } ?>
          </span> <br>
          <br>
          Specify search terms for your event: &nbsp;
          <input type="text" <?php if ($privacy=='Private'){ echo 'disabled="disabled"'; }?>  value="<?php if ($tags){ echo $tags;} else { echo "Enter a comma separated list of keywords";}?>" name="tags" id="tags" onFocus="removeText(this.value,'Enter a comma separated list of keywords','tags');" onBlur="returnText('Enter a comma separated list of keywords','tags');">
        </div>
        <label for="Private">
        <input type="radio" name="privacy" value="Private" <?php if ($privacy=='Private'){ echo 'checked="checked"'; }?>  id="Private" onChange="showHidePrivacy();">
        This event is private and should not be listed in the directory or on search engines.</label>
      </div>
      </div>
      <!-- /leftArea -->
      <div class="rightArea">
        <div class="ev_titleBar">
          <div class="ev_fltlft">Logo</div>
          <!-- <div class="ev_fltrght"><a href="#">EDIT</a> &nbsp;| &nbsp;<a href="#">REMOVE</a> </div>-->
        </div>
        <!-- /ev_titleBar -->
        <div class="ev_sideBox"> Upload the logo for yout event: <br>
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
          <input type="file" name="event_image" id="filename1"  />
          &nbsp;<br>
          &nbsp;<br>
          <small>Must be JPG, GIF or PNG smaller than 100kb.<br>
          Dimensions are limited to 450 x 200 px. Images Larger then this will be resized.</small> </div>
        <!-- /ev_sideBox -->
        <br>
        <div class="ev_titleBar"> Music Genre </div>
        <div class="ev_sideBox">
          <ul style="list-style:none; margin:0px; padding:0px">
            <?php 
								$sqlMusic = "SELECT name,id FROM music ";
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
            <li style="width:135px; float:left; ">
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
        <br>
        <div class="ev_titleBar"> Age Suitability </div>
        <div class="ev_sideBox">
          <select class="inp3" name="event_age_suitab" style="width:230px" id="event_age_suitab">
            <option value="">-Select age-</option>
            <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
            <option value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'selected'; }?>>
            <?=$rowAge['name']?>
            </option>
            <?php } ?>
          </select>
        </div>
        <div class="step">STEP 4: ADD WHERE</div>
        <div class="ev_titleBar"> Location </div>
        <div class="ev_sideBox">
          <input type="text" name="venue_name" id="venue_name" class="inp" value="<?php if ($bc_venue_name){ echo $bc_venue_name; }else{ echo "Start Typing Location Name"; } ?>" onFocus="removeText(this.value,'Start Typing Location Name','venue_name');" onBlur="returnText('Start Typing Location Name','venue_name');" style="margin-bottom:2px">
          <br>
          <a href="javascript:void(0)" style="color:#0066FF; text-decoration:underline" onclick="windowOpener(525,645,'Add New Location','add_venue.php')"> Can't find your location? Add it here </a><br>
          <br>
          <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venue_id; ?>" />
          <input type="text" name="address1" id="ev_address1" class="inp" value="<?php if ($bc_venue_address){ echo $bc_venue_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','ev_address1');" onBlur="returnText('Address','ev_address1');">
          <input type="text" name="city" id="ev_city" class="inp" value="<?php if ($bc_venue_city){ echo $bc_venue_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','ev_city');" onBlur="returnText('City','ev_city');">
          <input type="text" name="zip" id="ev_zip" class="inp2" value="<?php if ($bc_venue_zip){ echo $bc_venue_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','ev_zip');" onBlur="returnText('Zip / Postal Code','ev_zip');">
        </div>
        <!-- /ev_sideBox -->
        <!--<div class="step">STEP 5: ADD WHEN</div>
        <div class="ev_titleBar"> EVENT STARTS </div>
        <div class="ev_sideBox">
          <input type="text" class="inp2" readonly="" name="event_start_date" id="from" style="width:76px; cursor:pointer">
          at
          <select class="inp3" name="ev_start_hrs">
            <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'">'.$i.'</option>';
		   }
		   ?>
          </select>
          :
          <select class="inp3" name="ev_start_min">
            <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'">'.$i.'</option>';
		   }
		   ?>
          </select>
          <select class="inp3" name="ev_start_amPm">
            <option value="am">AM</option>
            <option value="pm">PM</option>
          </select>
          <div class="ev_hid">
            <input type="checkbox" id="ev_startDateHide">
            Hide start date on registration page? </div>
        </div>
        <br>
        <div class="ev_titleBar"> EVENT ENDS </div>
        <div class="ev_sideBox">
          <input type="text" class="inp2" readonly="" name="event_end_date" id="to" style="width:76px; cursor:pointer">
          at
          <select class="inp3" name="ev_end_hrs">
            <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'">'.$i.'</option>';
		   }
		   ?>
          </select>
          :
          <select class="inp3" name="ev_end_min">
            <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'">'.$i.'</option>';
		   }
		   ?>
          </select>
          <select class="inp3" name="ev_end_amPm">
            <option value="am">AM</option>
            <option value="pm">PM</option>
          </select>
          <div class="ev_hid">
            <input type="checkbox" id="ev_startDateHide">
            Hide end datre on registration page? </div>
        </div>
        <br>
        <div class="ev_titleBar"> TIME ZONE </div>
        <div class="ev_sideBox">
          <select class="inp3" name="timeZone">
            <option>Pacific Time (USA)</option>
          </select>
          <a href="#" style="display:block;padding:5px 0 0 5px;">view world time zones</a> </div>
        <br>
        <div class="ev_titleBar"> EVENT REPEATS?</div>
        <div class="ev_sideBox">
          <label for="event_repeats">
          <input type="checkbox" name="event_repeats" id="event_repeats" >
          Yes, thi event repeats</label>
          <div class="clr"></div>
        </div>-->
        <div class="step">STEP 5: ADD HOST</div>
        <div class="ev_titleBar">Organization Name</div>
        <div class="ev_sideBox">
          <textarea id="event_host" name="event_host" class="inp" style="height:25px; padding:3px; margin:0" onFocus="removeText(this.value,'Enter the name of your organization','event_host');" onBlur="returnText('Enter the name of your organization','event_host');"><?php if ($bc_event_host!=''){echo $bc_event_host; } else{echo "Enter the name of your organization"; }?>
</textarea>
        </div>
        <br>
        <div class="ev_titleBar">Organization Description</div>
        <div class="ev_sideBox">
          <textarea name="host_description" id="host_description" class="bc_input" style="width:265px; height:250px"><?php echo $bc_host_description; ?></textarea>
        </div>
      </div>
      <!-- /rightArea -->
      <div class="clr"></div>
      
      <div class="ev_submit">
        <input type="image" src="<?php echo IMAGE_PATH; ?>submit_event_btn.gif" name="submit" value="submit" />
        <input type="hidden" name="submit" value="submit">
      </div>
    </div>
  </form>
</div>
<?php include_once('includes/footer.php');?>
<script type="text/javascript">
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
<div id="dwindow" style="position:absolute;background-color:#fff;cursor:hand;left:0px;top:0px;display:none; z-index:9999">
  <div  style="background:url(images/titlebar.gif) repeat-x #fff; font-size: 14px; font-weight: bold; height: 18px; padding: 5px 7px 0 7px;width: 786px; border:#000000 solid 1px; border-bottom:none;"><?php if ($editTicket=='yes'){ echo "Edit Ticket"; } else{ echo "Create Ticket"; }?><img src="<?= IMAGE_PATH;?>closePopUp.gif" onClick="closeit()" style="cursor:pointer;" title="Close" align="right"></div>
  <div id="dwindowcontent" style="height:100%">
    <iframe id="cframe" src="" width="800px" height="100%" style="border:#000 solid 1px; border-top:none; background:#fff"></iframe>
  </div>
</div>