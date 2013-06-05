<?php

require_once("database.php"); 
require_once("header.php"); 

//$bc_source_id	=	"Admin-".rand();
$bc_event_source = 'Admin';

if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];

$action = "save";
$sucMessage = "";

$repeat_arr = array("day" => "Event Happens Daily","week"=>"Event Happens Weekly","month"=>"Event Happens Monthly");


$errors = array();
$bc_musicgenere_id  = array();
if ( isset($_POST['submit']) ) {
	
	$bc_add_venue			=	$_POST['addvenue'];
	$bc_fb_event_id			=	DBin($_POST["fb_event_id"]);
	$bc_userid				=	DBin($_POST["userid"]);
	$bc_category_id			=	DBin($_POST["category_id"]);
	$bc_subcategory_id		=	DBin($_POST["subcategory_id"]);
	$bc_event_name			=	DBin($_POST["event_name"]);
	
	$bc_event_start_time	=	DBin($_POST["event_start_time"]);
	$bc_event_end_time		=	DBin($_POST["event_end_time"]);
	$bc_dates				=   $_POST['selected_dates'];
	$bc_event_description	=	DBin($_POST["event_description"]);
	$bc_event_cost			=	DBin($_POST["event_cost"]);
	$bc_image				=	$_FILES['event_image']['name'];
	$bc_event_video			=	DBin($_POST["event_video"]);
	$bc_event_sell_ticket	=	DBin($_POST["event_sell_ticket"]);
	$bc_event_age_suitab	=	DBin($_POST["event_age_suitab"]);
	$bc_event_status		=	($bc_add_venue == "clicked" ? $_POST["event_status"] = "0" : DBin($_POST["event_status"]));
	$bc_venue_name			=   DBin($_POST["venue_name"]);
	$bc_averagerating		=	DBin($_POST["averagerating"]);
	$bc_modify_date			=	date("Y-m-d");
	$bc_del_status			=	DBin($_POST["del_status"]);
	$bc_added_by			=	DBin($_POST["added_by"]);
	$bc_venu_id				=	DBin($_POST['vanue_id']);
	
	$bc_event_host			=	DBin($_POST['event_host']);
	$bc_host_description	=	DBin($_POST['host_description']);
	

	$frequency				=	$_POST['frequency'];
	$repeat					=	$_POST['erepeat'];
	$pending_approval		=	$_POST['pending_approval'];

	$dates_sel = array();
	if ( $_POST['selected_dates'] != '' )
		$dates_sel = explode(",", $_POST['selected_dates']);

	$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

	
	if ( trim($bc_event_name) == '' )
		$errors[] = 'Please enter Eevent Name';
	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please Select Event Type';
	if ( trim($bc_subcategory_id) == '' )
		$errors[] = 'Please Select Event Sub Category';	
	/*if ( trim($bc_dates) == '' )
		$errors[] = 'Please Select Event Date(s)';		*/
	if ( trim($bc_event_description) == '' ){
			$errors[] = 'Event Description is empty.';
	   }
	/*if ( trim($bc_event_age_suitab) == '' ){
			$errors[] = 'Please Select Age Suitable';
	   }*/
	if ( trim($bc_event_start_time) == '' )
		$errors[] = 'Please Select Start Time';
	
	if ( trim($bc_event_end_time) == '' )
		$errors[] = 'Please Select End Time';
	
	
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
	//if (0==1) {
       
	    if ( $frequency > 0 && count( $dates_sel) == 1 && $repeat != ''  ) {
			
			$date = date("Y-m-d",strtotime($dates_sel[0]));
			
			for ( $l=1;$l<$frequency;$l++)
				$dates_sel[$l] = date("m/d/Y",strtotime( date("Y-m-d", strtotime($date) ) . " +".$l . " ". $repeat) );
				
		}
		
		$bc_image = '';
		if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["event_image"]["name"] ;
			if ($action1 == "edit") {
				deleteImage($frmID,"events","event_image");
			}
			move_uploaded_file($_FILES["event_image"]["tmp_name"], '../event_images/' .$bc_image);
			makeThumbnail($bc_image, '../event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		  
		 if ($action1 == "save") {
		     $bc_source_id		=	"Admin-".rand(); 
			 $bc_publishdate	=	 date("Y-m-d");
			 
			
			
			$sql	=	"insert into events (event_source,source_id,fb_event_id,userid,category_id,subcategory_id,event_name,event_start_time,event_end_time,event_description,event_cost,event_image,event_video,event_sell_ticket,event_age_suitab,event_status,publishdate,averagerating,modify_date,del_status,added_by,repeat_event,repeat_freq) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_fb_event_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_event_start_time . "','" . $bc_event_end_time . "','" . $bc_event_description . "','" . $bc_event_cost . "','" . $bc_image . "','" . $bc_event_video . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "','".$repeat."','".$frequency."')";
			$res	=	mysql_query($sql);
			
			$event_id = mysql_insert_id();
			
			
			//print_r($_FILES['eimage']['name']);
			if ( is_array($_FILES['eimage']) ) {
			for($i=0;$i< count($_FILES['eimage']); $i++) {
				$einame = $_FILES['eimage']['name'][$i];
				$etname = $_FILES['eimage']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, '../event_images/'.$ei_image);
					makeThumbnail($ei_image, '../event_images/', '', 123, 85,'th_');
					if ( $ei_image != '' && $event_id > 0 ){
						mysql_query("INSERT INTO `event_images` (`id`, `image`, `event_id`) VALUES (NULL, '$ei_image', '$event_id')");
						}
				}		
			}
		}
			
			if ( $event_id > 0 && $bc_event_host != '' ) {
			$sql_host = "insert into event_hosts (source_id, event_id, host_name, host_description) values('Promoter-". $bc_userid ."','" . $event_id . "','" . DBin($bc_event_host) . "','".$bc_host_description."')";
			
				mysql_query($sql_host);	
			}
			
			if($bc_add_venue == 'clicked'){	
				$event_id = $frmID = mysql_insert_id();
			}else{
				$frmID = mysql_insert_id();
			}
			
			if ( is_array($_POST["musicgenere_id"]) ) {
				foreach ( $_POST["musicgenere_id"] as $mus)
					mysql_query("insert ignore into event_music (event_id,music_id) VALUES('". $frmID ."','". $mus ."')");
			}
			
		if(isset($_POST['selected_dates']) && $_POST['selected_dates'] != ''){
		
			
			for($i=0; $i<count($dates_sel); $i++){
				$sql_date = "insert into event_dates (event_id, event_date) values('" . $frmID . "','" . date("Y-m-d",strtotime($dates_sel[$i])) . "')";			
				mysql_query($sql_date) ;
			} 
			
			if($bc_venue_name != ''){	
				if($bc_venu_id != ''){
					$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $frmID . "')";
				 	mysql_query($sql_venue);	
				}
				/*
				$tmp = explode("|",$bc_venue_name);
				$venue_id = $tmp[1];
				 $sql_venue = "insert into venue_events (venue_id, event_id) values('" . $venue_id . "','" . $frmID . "')";
				 mysql_query($sql_venue);
				 */
			}	 
		  }
		  
		  	if( $bc_add_venue == 'clicked' && $event_id != ''){
 				echo "<script type='text/javascript'> window.location.href='venues.php?event_id=".$event_id."'; </script>";
			}
		  
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update events set category_id = '" . $bc_category_id . "', subcategory_id = '" . $bc_subcategory_id . "', event_name = '" . $bc_event_name . "', musicgenere_id = '" . $bc_musicgenere_id . "', event_start_time = '" . $bc_event_start_time . "', event_end_time = '" . $bc_event_end_time . "', event_description = '" . $bc_event_description . "', event_cost = '" . $bc_event_cost . "', ". $sql_img ." event_sell_ticket = '" . $bc_event_sell_ticket . "', event_age_suitab = '" . $bc_event_age_suitab . "', event_status = '" . $bc_event_status . "', event_video = '" . $bc_event_video . "', averagerating = '" . $bc_averagerating . "', modify_date = '" . $bc_modify_date . "', del_status = '" . $bc_del_status . "', added_by = '" . $bc_added_by . "', repeat_event = '" . $repeat . "', repeat_freq = '" . $frequency . "',pending_approval='". $pending_approval ."' where id='$frmID'";
			$res	=	mysql_query($sql) ;
			
			
			
			if ( is_array($_FILES['eimage']) ) {
			for($i=0;$i< count($_FILES['eimage']); $i++) {
				$einame = $_FILES['eimage']['name'][$i];
				$etname = $_FILES['eimage']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, '../event_images/'.$ei_image);
					makeThumbnail($ei_image, '../event_images/', '', 123, 85,'th_');
					if ( $ei_image != '' && $frmID > 0 ){
						mysql_query("INSERT INTO `event_images` (`id`, `image`, `event_id`) VALUES (NULL, '$ei_image', '$frmID')");
						}
				}		
			}
		}
			

			
			
			$rew = mysql_query("select * from `event_hosts` WHERE `event_id` = '$frmID'");
			if(mysql_num_rows($rew)){
			mysql_query("UPDATE `event_hosts` SET  `host_name` =  '$bc_event_host',`host_description` =  '$bc_host_description' WHERE `event_id` = '$frmID'");
			}
			else{
				$sql_host = "insert into event_hosts (source_id, event_id, host_name, host_description) values('Promoter-". $bc_userid ."','" . $event_id . "','" . DBin($bc_event_host) . "','".$bc_host_description."')";
				mysql_query($sql_host);
			}
			
			if( is_array($_POST["musicgenere_id"]) ) {
				foreach ( $_POST["musicgenere_id"] as $mus)
					mysql_query("insert ignore into event_music (event_id,music_id) VALUES('". $frmID ."','". $mus ."')");
			}
			
			
			
			$r4 = mysql_query("select * from event_music where event_id='$frmID'");
			while ( $ro4 = mysql_fetch_assoc($r4) ) {
				if ( !in_array($ro4['music_id'],$_POST["musicgenere_id"]) )
					mysql_query("delete from event_music where event_id='". $frmID ."' and music_id='". $ro4['music_id'] ."'");
			}
			
			if(isset($_POST['selected_dates']) && $_POST['selected_dates'] != '' ){
					//$dates_sel = explode(",", $_POST['selected_dates']);
					$remove_dates = "delete from event_dates where event_id = '$frmID'";
					mysql_query($remove_dates) ;
					for($i=0; $i<count($dates_sel); $i++){
						$sql_date = "insert into event_dates 
							(event_id, event_date) values('" . $frmID . "','" . date("Y-m-d",strtotime($dates_sel[$i])) . "')";			
						mysql_query($sql_date);
				   }
				}
			  
			if($bc_venue_name != ''){	
					
					if($bc_venu_id != ''){
						$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $frmID . "')";
						mysql_query($sql_venue);	
					}
					/*$venue_check = "select * from venues where venue_name = '$bc_venue_name'";
					$vanue_check_res = mysql_query($venue_check);  
					$total_rec_found = mysql_num_rows($vanue_check_res);
					if($total_rec_found > 0){
						$venue_chk_r = mysql_fetch_assoc($vanue_check_res);
						$venue_id = $venue_chk_r['id'];
					  
					 	$sql_venue = "update venue_events set venue_id ='$venue_id', event_id = '$frmID' where event_id = '$frmID'";
					  	mysql_query($sql_venue);
			   		}*/	 
			 	 }
						
			if ($res) {
				$sucMessage = "Record Successfully updated.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit 

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
		$bc_event_video			=	$row["event_video"];
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
		
		$bc_musicgenere_id = array();
		$r4 = mysql_query("select * from event_music where event_id='$frmID'");
		while ( $ro4 = mysql_fetch_assoc($r4) )
			$bc_musicgenere_id[] =	$ro4["music_id"];

		
		$action = "edit";
	} // end if row

	$sq2	=   "select venue_name,venue_address,venue_city,venue_zip from venues where id = (select venue_id from venue_events where event_id = '$frmID' )";
	$res2	=   mysql_query($sq2);
	if ($res2)
		if ($row2 = mysql_fetch_assoc($res2) ) 
			$bc_venue_name		 = $row2["venue_name"];
			$bc_venue_address	= $row2["venue_address"];
			$bc_venue_city		 = $row2["venue_city"];
			$bc_venue_zip		 = $row2["venue_zip"];
	
	$get_event_id	=   "select venue_id from venue_events where event_id = '$frmID'";
	$run_get_event_id	=   mysql_query($get_event_id);
	if ($run_get_event_id){
		if ($row2 = mysql_fetch_assoc($run_get_event_id) ) 
			$bc_venue_id = $row2["venue_id"];
	} 
			
} // end if 


//echo $frmID;
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

//$subCat_q = "select * from sub_categories";
//$subCat_res = mysql_query($subCat_q);
$age_q = "select * from age order by id ASC";
$age_res = mysql_query($age_q);

$music_q = "select * from music";
$music_res = mysql_query($music_q);



if ( $_GET['delete'] > 0 ) {
	$r = mysql_query("select * from event_images where id='". $_GET['delete'] ."'");
	if ( $rr = mysql_fetch_assoc($r) ) {
		@unlink('../event_images/'.$rr['image']);
		@unlink('../event_images/th_'.$rr['image']);
	}	
	mysql_query("delete from event_images where id='". $_GET['delete'] ."'");
	?>
	<script>window.location.href="events.php?id=<?php echo $frmID;?>";</script>
	<?php
}

?>
<script type="text/javascript" src="../js/jquery-ui_1.8.7.js"></script>
<script type='text/javascript' src='js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />
<!-- loads mdp -->
<script type="text/javascript" src="../js/jquery-ui.multidatespicker.js"></script>
<script type="text/javascript">
			$(function() {
				// multi-months
				$('#multi-months').multiDatesPicker({
					numberOfMonths: 3,
					
					<?php if ($action == "edit" && $dates != "" )  { ?>
						addDates: [<?php echo $dates; ?>], 
					<?php }?>
					
					//addDates: [ '05/01/2011', '05/14/2011'],
					onSelect: function(dateText, inst) {
						
						var dates = $('#multi-months').multiDatesPicker('getDates');
						document.getElementById("selected_dates").value = dates;
					}
				});
				
				$('#multi-months').datepicker('setDate', new Date(<?php echo $first_date; ?>));
				
			});
			
			
			
		</script>
<style>


table.ui-datepicker-calendar {border-collapse: separate;}
.ui-datepicker-calendar td {border: 1px solid transparent;}

.hasDatepicker .ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
	background: #743620 none;
	color: white;
}

#ui-datepicker-div {display:none;}

</style>
<script type="text/javascript">
/*
$(document).ready(function() {
	$("#venue_name").autocomplete("get_venue_list.php", {
		width: 260,
		matchContains: true,
		//mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
		});
});
*/
$(document).ready(function() {
		$("#venue_name").autocomplete("get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					$("#vanue_id").attr("value", data[0]);
				}
			}).setOptions({
				max: '100%'
			});
});

function add_newImage(id)
{
	var next_tr = id+1;
	var new_url_feild = '<tr id="image_tr_'+next_tr+'"><td align="right" width="20%"  class="bc_label">Extra Images(s):</td><td width="80%" align="left" class="bc_input_td"><input type="hidden" value="'+next_tr+'" /><input type="file" name="eimage[]" id="eimage'+next_tr+'" class="bc_input" /><span id="add_more_btn_'+next_tr+'"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage('+next_tr+');">&nbsp;&nbsp;Add More</span></span></td></tr>';
	$('#add_more_btn_'+id).html('&nbsp;&nbsp;<img src="images/delete.png" onclick="remove_image('+id+')" style="cursor:pointer">');
	$('#add_url_ist').append(new_url_feild);
	
}
function remove_image(id){
if(id==1){
var id2='';}
else{
var id2 = id;
}
var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
document.getElementById('eimage'+id2).value='';
document.getElementById('image_tr_'+id).style.display='none';
}}


function deleteExtraImage(id)
{
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		window.location.href= 'events.php?id=<?php echo $frmID;?>&delete='+id;
	}
}

</script>
<form method="post" name="bc_form" enctype="multipart/form-data" action="" autocomplete="off" >
  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <?php if ($event_id != "" && $dates != "" )  { ?>
  <input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="<?php echo substr(str_replace("'","",trim($dates)),0,-1); ?>" />
  <?php } else {?>
  <input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="" />
  <?php } ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr class="bc_heading">
      <td colspan="2" align="left">Add/Edit Event</td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
    </tr>
    <tr>
      <td width="22%" align="right" class="bc_label">Category:</td>
      <td width="78%" align="left" class="bc_input_td"><select name="category_id" class="bc_input"  onchange="dynamic_Select('subcategory.php', this.value, 0 )">
          <option value="">-- Category --</option>
          <?php while($cat_r=mysql_fetch_assoc($cat_res)){ ?>
          <option value="<?php echo $cat_r['id']; ?>" <?php if($cat_r['id'] == $bc_category_id){?> selected="selected" <?php }?>><?php echo $cat_r['name']; ?></option>
          <!--<input type="text" name="category_id" id="category_id" class="bc_input" value="<?php //echo $bc_category_id; ?>"/>-->
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Sub Category:</td>
      <td align="left" class="bc_input_td"><div id="subcategory_id">
          <select name="subcategory_id" class="bc_input">
            <option selected="selected" value=""></option>
          </select>
        </div>
        <!--<input type="text" name="subcategory_id" id="subcategory_id" class="bc_input" value="<?php //echo $bc_subcategory_id; ?>"/>--></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Name:</td>
      <td align="left" class="bc_input_td"><input type="text" name="event_name" id="event_name" class="bc_input" style="width:350px" value="<?php echo $bc_event_name; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Music Genere:</td>
      <td align="left" class="bc_input_td"><select name="musicgenere_id[]" class="bc_input" style="width:250px; height:100px" multiple="multiple">
          <option value="">-- Music Genre --</option>
          <?php while($music_r=mysql_fetch_assoc($music_res)){ ?>
          <option value="<?php echo $music_r['id']; ?>" <?php if( in_array($music_r['id'],$bc_musicgenere_id) ){?> selected="selected" <?php }?>><?php echo $music_r['name']; ?></option>
          <?php } ?>
        </select>
        <!--<input type="text" name="musicgenere_id" id="musicgenere_id" class="bc_input" value="<?php //echo $bc_musicgenere_id; ?>"/>--></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Start Time:</td>
      <td align="left" class="bc_input_td"><select name="event_start_time" id="event_start_time" class="bc_input" >
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
        </select></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event End Time:</td>
      <td align="left" class="bc_input_td"><?php //echo $bc_event_end_time; ?>
        <select name="event_end_time" id="event_end_time" class="bc_input" >
          <option value="">select</option>
          <?php 
$bc_arr_event_end_time = array("12:00 AM"=>"12:00 AM", "12:30 AM"=>"12:30 AM", "1:00 AM"=>"01:00 AM", "1:30 AM"=>"01:30 AM", "2:00 AM"=>"02:00 AM", "2:30 AM"=>"02:30 AM", "3:00 AM"=>"03:00 AM", "3:30 AM"=>"03:30 AM", "4:00 AM"=>"04:00 AM", "5:00 AM"=>"05:00 AM", "5:30 AM"=>"05:30 AM", "6:00 AM"=>"06:00 AM", "6:30 AM"=>"06:30 AM", "7:00 AM"=>"07:00 AM", "7:30 AM"=>"07:30 AM", "8:00 AM"=>"08:00 AM", "8:30 AM"=>"8:30 AM", "9:00 AM"=>"09:00 AM", "9:30 AM"=>"09:30 AM", "10:00 AM"=>"10:00 AM", "10:30 AM"=>"10:30 AM", "11:00 AM"=>"11:00 AM", "11:30 AM"=>"11:30 AM", "12:00 PM"=>"12:00 PM", "12:30 PM"=>"12:30 PM", "1:00 PM"=>"01:00 PM", "1:30 PM"=>"01:30 PM", "2:00 PM"=>"02:00 PM", "2:30 PM"=>"02:30 PM", "3:00 PM"=>"03:00 PM", "3:30 PM"=>"03:30 PM", "4:00 PM"=>"04:00 PM", "5:00 PM"=>"05:00 PM", "5:30 PM"=>"05:30 PM", "6:00 PM"=>"06:00 PM", "6:30 PM"=>"06:30 PM", "7:00 PM"=>"07:00 PM", "7:30 PM"=>"07:30 PM", "8:00 PM"=>"08:00 PM", "8:30 PM"=>"08:30 PM", "9:00 PM"=>"09:00 PM", "9:30 PM"=>"09:30 PM", "10:00 PM"=>"10:00 PM", "10:30 PM"=>"10:30 PM", "11:00 PM"=>"11:00 PM", "11:30 PM"=>"11:30 PM"); 
foreach($bc_arr_event_end_time as $key => $val)
{
	if ($key == DBout($bc_event_end_time))
		$sel = 'selected="selected"';
	else
		$sel = "";	
?>
          <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Dates:</td>
      <td align="left" class="bc_input_td" style="font-size:10px!important"><div id="multi-months"></div>
        <font color="#000"><strong>Click on date to select OR unselect</strong></font> </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Repeats:</td>
      <td align="left" class="bc_input_td"><select id="erepeat" name="erepeat" class="bc_input" style="width:150px" onchange="freq_desc(this.value)">
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
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Repeat Frequency:</td>
      <td align="left" class="bc_input_td"><input type="text" name="frequency" id="frequency" class="bc_input" value="<?php echo $frequency; ?>"/>
        <br />
        <script>
	function freq_desc(val)
	{
		$("#freq_desc").html('Enter the number of '+ val +'s this event repeats');
	}
</script>
        <font color="#000" id="freq_desc"></font> </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Description:</td>
      <td align="left" class="bc_input_td"><textarea name="event_description" id="event_description" class="bc_input" style="width:400px; height:200px"><?php echo $bc_event_description; ?></textarea>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Cost:</td>
      <td align="left" class="bc_input_td"><input type="text" name="event_cost" id="event_cost" class="bc_input" value="<?php echo $bc_event_cost; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Image:</td>
      <td align="left" class="bc_input_td"><?php 
if( $bc_image != ''  ) {
	if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
		$bc_image1 = ABSOLUTE_PATH . 'event_images/'.$bc_image;
	else
		$bc_image1 = $bc_image;	
		
	echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_event_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="events|event_image|'.$bc_image.'|../event_images/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
							
 ?>
        <input type="file" name="event_image" id="event_image" class="bc_input" value="<?php echo $bc_image; ?>"/>
        <br />
        <?php echo $image_del;  ?> </td>
    </tr>
    <tr>
      <td colspan="2"  align="center"><table width="100%" border="0" cellspacing="0" cellpadding="5" align="center" id="add_url_ist">
          <?php 


if($frmID || $event_id) {
if($event_id!=''){
$frmID	=	$event_id;
}
	$msql	=	"select * from `event_images` where `event_id` = '$frmID'";
	$mres	=	mysql_query($msql);
	$count = 0;
	if ( mysql_num_rows($mres) > 0 ) {
	?>
          <tr >
            <td align="right" width="22%" class="bc_label">Extra Images(s):</td>
            <td align="left" width="78%" class="bc_input_td"><?php
	while ($mrow = mysql_fetch_assoc($mres))
	{
		$count ++;
		$bce_image = $mrow['image'];
		echo '<div style="float:left; margin-right:10px"><img src="'.EVENT_IMAGE_PATH.'th_'.$bce_image .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	?>
              <a href="javascript:deleteExtraImage(<?php echo $mrow['id'] ;?>)"><img src="images/delete.png" border="0" ></a>
              </div>
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php } ?>
          <tr id="image_tr_1">
            <td align="right" width="22%" class="bc_label">Extra Images(s):</td>
            <td width="78%" align="left" class="bc_input_td"><input type="hidden" value="1" />
              <input type="file" name="eimage[]" id="eimage" class="bc_input" value=""/>
              <span id="add_more_btn_1"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage(1);">&nbsp;&nbsp;Add More</span></span> </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Video:</td>
      <td align="left" class="bc_input_td"><input type="text" name="event_video" id="event_video" class="bc_input" style="width:350px" value="<?php echo $bc_event_video; ?>"/>
        <br />
        (Example: http://www.youtube.com/watch?v=o5cFlq-vkhI)</td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Sell Ticket:</td>
      <td align="left" class="bc_input_td"><span class="bc_label"> Yes </span>
        <input type="radio" name="event_sell_ticket" id="event_sell_ticket" class="bc_input" value="yes"
 <?php if(trim($bc_event_sell_ticket)!= 'no'){echo 'checked="checked';}?>/>
        &nbsp; <span class="bc_label"> No </span>
        <input type="radio" name="event_sell_ticket" id="event_sell_ticket" class="bc_input" value="no" 
 <?php if(trim($bc_event_sell_ticket)!='yes'){echo 'checked="checked"';}?> />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Age Suitab:</td>
      <td align="left" class="bc_input_td"><select name="event_age_suitab" id="event_age_suitab" class="bc_input">
          <option value="">-- Age --</option>
          <?php while($age_r=mysql_fetch_assoc($age_res)){ ?>
          <option value="<?php echo $age_r['id']; ?>" <?php if($age_r['id'] == (int)trim($bc_event_age_suitab)){?> selected="selected" <?php }?>><?php echo $age_r['name']; ?></option>
          <?php } ?>
        </select>
    </tr>
    <!--
<tr>
<td align="right" class="bc_label">Pending Approval:</td>
<td align="left" class="bc_input_td">

<select name="pending_approval" id="pending_approval" class="bc_input">

	<option value="">--Approval--</option>
	<option value = "0" <?php if($pending_approval == '0'){ echo 'selected="selected"'; }?> >Active</option>
	<option value = "1" <?php if($pending_approval == '1'){ echo 'selected="selected"'; } ?> >Pending</option>
	
</select>

</td>
</tr>
-->
    <tr>
      <td align="right" class="bc_label">Event Status:</td>
      <td align="left" class="bc_input_td"><select name="event_status" id="event_status" class="bc_input">
          <option value="">-- Status --</option>
          <option value = "0" <?php if($bc_event_status == '0'){ echo 'selected="selected"'; }?> >Not Active</option>
          <option value = "1" <?php if($bc_event_status == '1'){ echo 'selected="selected"'; } ?> >Active</option>
          <option value = "2" <?php if($bc_event_status == '2'){ echo 'selected="selected"'; } ?> >Pending Approval</option>
        </select>
        <!--<input type="text" name="event_status" id="event_status" class="bc_input" value="<?php //echo $bc_event_status; ?>"/>-->
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Location:</td>
      <td align="left" class="bc_input_td"><input type="text" name="venue_name" id="venue_name" class="inp" value="<?php echo $bc_venue_name; ?>" style="margin-bottom:2px">
        <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venue_id; ?>" />
        <?php if($action != "edit"){?>
        <input type="button" value="Save Event &amp; Add New Venue"  onclick="addVenue();" style="background:#eff2dd; font-weight:bold;"/>
        <input type="hidden" name="addvenue" value=""  id="addvenue"/>
        <?php } ?>
        <br />
        <font color="#000">Type Venue Name above. It will show options</font> </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Address:</td>
      <td align="left" class="bc_input_td"><input type="text" name="address1" id="ev_address1" class="inp" value="<?php echo $bc_venue_address; ?>" />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">City:</td>
      <td align="left" class="bc_input_td"><input type="text" name="city" id="ev_city" class="inp" value="<?php echo $bc_venue_city; ?>" />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Zip / Postal Code:</td>
      <td align="left" class="bc_input_td"><input type="text" name="zip" id="ev_zip" class="inp2" value="<?php echo $bc_venue_zip; ?>" />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Organization Name:</td>
      <td align="left" class="bc_input_td"><input  type="text" class="bc_input" style="width:350px" value="<?php echo $bc_event_host; ?>" id="event_host" name="event_host"  />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Organization Description:</td>
      <td align="left" class="bc_input_td"><textarea name="host_description" id="host_description" class="bc_input" style="width:500px; height:200px"><?php echo $bc_host_description; ?></textarea>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Average Rating:</td>
      <td align="left" class="bc_input_td"><input type="text" name="averagerating" id="averagerating" class="bc_input" value="<?php echo $bc_averagerating; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Added By:</td>
      <td align="left" class="bc_input_td"><input type="text" name="added_by" id="added_by" class="bc_input" value="<?php echo $bc_added_by; ?>"/></td>
    </tr>
    <?php if($action != "save"){?>
    <tr>
      <td align="right" class="bc_label">Publishdate:</td>
      <td align="left" class="bc_input_td"><?php echo date("m/d/Y", strtotime($bc_publishdate)); ?> </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Modify Date:</td>
      <td align="left" class="bc_input_td"><?php echo date("m/d/Y", strtotime($bc_modify_date)); ?> </td>
    </tr>
    <?php } ?>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><input name="submit" type="submit" value="Save" class="bc_button" id="submit" />
      </td>
    </tr>
  </table>
</form>
<?php if ( $bc_subcategory_id > 0 && $bc_category_id > 0 ) { 
	echo "<script>dynamic_Select('subcategory.php', ". $bc_category_id .", ". $bc_subcategory_id ." );</script>";
} ?>
<?php 
require_once("footer.php"); 
?>
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">

$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide();
		$("#delImg_" + imgInfo[1]).attr("src", "images/no_image.png");
	}
});

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
	plugins : 'inlinepopups,imagemanager',
});
</script>
<script type="text/javascript">
	function addVenue(){ 
		document.getElementById('addvenue').value="clicked";
	 	 document.getElementById('submit').click();
	}
	
	
	
		$(document).ready(function() {
		$("#venue_name").autocomplete("get_venue_list.php", {
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
			});});
	
	
		
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
	plugins : 'inlinepopups,imagemanager',
});
</script>
