<?php
if (isset($_REQUEST["eventname"])) {
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";



$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;

if($_POST['evntIdForDraft'])
	$frmID	=	$_POST['evntIdForDraft'];


	

	
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
	$bc_event_age_suitab	=	$_POST['min_age_allow'];
	$bc_men_preferred_age	=	$_POST['men_preferred_age'];
	$bc_women_preferred_age	=	$_POST['women_preferred_age'];
	$bc_event_music			=	$_POST['event_music'];
	$bc_occupation_target	=	$_POST['occupation_target'];
	$bc_gallery				=	$_POST['gallery'];
	$bc_video_name			=	$_POST['video_name'];
	$bc_video_embed			=	$_POST['video_embed'];
	$bc_venu_id				=	$_POST['venue_id'];
	$bc_event_type			=	$_POST['event_type'];
	$bc_free_event			=	$_POST['free_event'];
	$bc_modify_date			=	date("Y-m-d");
	
	
	if($bc_gallery=='Create a name for your image gallery (i.e. Dress Code)'){
	$bc_gallery='';
	}
	
	if($_POST['noTicket']){
	$bc_event_cost			=	$_POST['event_cost'];
	}
	else{
	$bc_event_cost			=	'';
	}
	
	
	if($bc_occupation_target){
	$bc_occupation_targets	=	'';
	for($d=0;$d<count($bc_occupation_target);$d++){
	
	$bc_occupation_targets.=	$bc_occupation_target[$d].",";
	
	}}
	
		if($frmID){
		if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
			$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
			makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		
		
 $sql = "UPDATE `events` SET `category_id` = '$bc_category_id', `subcategory_id` = '$bc_subcategory_id', `event_name` = '$bc_event_name', `event_description` = '$bc_event_description', `event_cost` = '$bc_event_cost', $sql_img `event_age_suitab` = '$bc_event_age_suitab', `men_preferred_age` = '$bc_men_preferred_age', `women_preferred_age` = '$bc_women_preferred_age', `occupation_target` = '$bc_occupation_targets', `video_name` = '$bc_video_name', `video_embed` = '$bc_video_embed',  `type` = 'draft', `free_event` = '$bc_free_event'  WHERE `id` = '$frmID'";
 
 	$res = mysql_query($sql);
		
		
		
		if($res){
		$event_id	=	$frmID;
		
		$t_id	=	$_SESSION['event_ticket_id'];
		if($_POST['noTicket']){
				
		$bc_event_ticket_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$frmID'");	
		if($bc_event_ticket_id){
		$t_id	=	$bc_event_ticket_id;
		}
		if($t_id){
			mysql_query("DELETE FROM `event_ticket` WHERE `id` = '$t_id'");
		}}
		
		else{
			mysql_query("UPDATE `event_ticket` SET `event_id` = '$event_id' WHERE `id` = '$t_id'");
		}
		
		$_SESSION['event_ticket_id']='';
		$_SESSION['event_ticket_id_for_ticket']='';
		
		$re = mysql_query("select * from `event_dates` where `event_id`='$event_id'");
		while($ro = mysql_fetch_array($re)){
		$date_id = $ro['id'];
		mysql_query("DELETE FROM `event_times` WHERE `date_id`='$date_id'");
		}
		mysql_query("DELETE from `event_dates` where `event_id`='$event_id'");
		mysql_query("DELETE from `event_music` where `event_id`='$event_id'");
		mysql_query("DELETE from `venue_events` where `event_id`='$event_id'");		
		mysql_query("UPDATE `event_gallery` SET `name` = '$bc_gallery' WHERE `event_id` = '$event_id'");
		
	$gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$frmID'");	

	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 120, 92,'th_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
		
		$sucMessage = "Event Successfully updated";
		}
		else{
			$sucMessage = "Error: Please try Later";
		}
		}	
	else{
		
		
		$bc_image = '';
		//if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
			$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
			makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		
	
	
		$bc_source_id	=	"USER-".rand(); 
		$bc_publishdate	=	 date("Y-m-d");
		
		
	$sql	=	"insert into events (event_source,source_id,userid,category_id,subcategory_id,event_name,musicgenere_id,event_description,event_image,event_sell_ticket,event_age_suitab,
		event_status,publishdate,averagerating,modify_date,del_status,added_by,men_preferred_age,women_preferred_age,occupation_target,video_name,video_embed,repeat_event,repeat_freq,tags,privacy,pending_approval,type,free_event,event_type,is_expiring) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_musicgenere_id . "','" . $bc_event_description . "','" . $bc_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "','".$bc_men_preferred_age. "','".$bc_women_preferred_age. "','" . $bc_occupation_targets . "','".$bc_video_name. "','".$bc_video_embed. "','".$repeat."','".$frequency."','','". $privacy ."','0','draft','$bc_free_event','$bc_event_type','1')";
		
		$res	=	mysql_query($sql);
		$frmID	=	mysql_insert_id();
		
		
		if ($res) {
		$event_id 	=	mysql_insert_id();
		$t_id		=	$_SESSION['event_ticket_id'];
		
		if($_POST['noTicket']){
		if($t_id){
			mysql_query("DELETE FROM `event_ticket` WHERE `id` = '$t_id'");
		}}
		
		else{
			mysql_query("UPDATE `event_ticket` SET `event_id` = '$event_id' WHERE `id` = '$t_id'");
		}
			
			$_SESSION['event_ticket_id']='';
			$_SESSION['event_ticket_id_for_ticket']='';
		
		////// FOR DATE & TIME //////

	
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
					makeThumbnail($ei_image, 'event_images/gallery/', '', 120, 92,'th_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}}}}
		
		
		if ($bc_venu_id != ''){		
			$bc_venu_id_old	=	getSingleColumn('id',"select * from `venue_events` where `event_id`='$frmID'");	
				if($bc_venu_id_old){
				$sql_venue = "UPDATE `venue_events` SET `venue_id` = '$bc_venu_id' WHERE `event_id` = '$frmID'";
				}
				else{
				$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $event_id . "')";
				}
			mysql_query($sql_venue);			
			}
			
	}
	require_once('includes/header.php'); 
	
?>

<div style="width:960px; margin:auto;">
	<div class="welcomeBox"></div>
	<div class="eventDetailhd"><span>Event Saved</span></div>
	<div class="clr">&nbsp;</div>

	<div style="width:960px; margin:auto; padding-bottom:10px; height:300px; padding-top:20px; font-size:16px; line-height:25px">
		<strong>Your event is Saved as Draft. &nbsp;  <a href="manage_event.php" style="text-decoration:underline; color:#0066FF">Manage Events</a></strong>
		
		<br />
		
	</div>
</div>
<?php
	include('includes/footer.php');
	?>