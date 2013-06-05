<?php
if (isset($_POST["eventname"])) {
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
	
	@list($image_width, $image_height, $image_type) = getimagesize($_FILES["event_image"]["tmp_name"]);
		
			
	
	if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
			$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
			$tmp_bc_name	=	str_replace(" ","_", $tmp_bc_name);
			move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
			$_SESSION['UPLOADED_TMP_NAME'] = $tmp_bc_name;
	}
	
	$occurrences	=	$_POST['occurrences'];
	
	
	$bc_event_source 		= 	($_SESSION['usertype']==2) ? 'Promoter' : 'User';

	$bc_event_name			=	$_POST["eventname"];
	$bc_event_description	=	DBin($_POST["event_description"]);
	$bc_category_id			=	$_POST["category_id"];
	$bc_subcategory_id		=	$_POST["subcategory_id"];
	$bc_event_age_suitab	=	$_POST['min_age_allow'];
	$bc_men_preferred_age	=	$_POST['men_preferred_age'];
	$bc_women_preferred_age	=	$_POST['women_preferred_age'];
	$bc_event_music			=	$_POST['event_music'];
	$bc_occupation_target	=	$_POST['occupation_target'];
	$bc_gallery				=	$_POST['gallery'];
	$bc_video_name			=	$_POST['video_name'];
	
		if($bc_video_name=='Enter the name of your video')
		$bc_video_name = '';
		
	$bc_video_embed			=	$_POST['video_embed'];
	
	$bc_venu_id				=	$_POST['venue_id'];
	$zipcode	= getSingleColumn("venue_zip","select * from `venues` where `id`='$bc_venu_id'");
	
	$bc_event_type			=	$_POST['event_type'];
	$bc_modify_date			=	date("Y-m-d");
	$bc_free_event			=	$_POST['free_event'];
	$bc_specials			=	$_POST['specials'];
	$bc_event_end_time		=	$_POST['event_end_time'];
	$bc_alter_url			=	$_POST['alter_url'];
	
	if($bc_alter_url){
		if ( substr($bc_alter_url,0,7) == 'http://' || substr($bc_alter_url,0,8) == 'https://' )
			$bc_alter_url = $bc_alter_url;
		else
			$bc_alter_url = 'http://'.$bc_alter_url;
	}
	
	if($bc_event_end_time=='Add End Time here')
		$bc_event_end_time='';
	$bc_seo_name			=	make_seo_names($bc_event_name,"events","seo_name","");
	
	
	if($bc_added_by==''){
		$usertype		= getSingleColumn('usertype',"select * from `users` where `id`='$user_id'");
		if($usertype==2){
			$bc_added_by		= getSingleColumn('business_name',"select * from `promoter_detail` where `promoterid`='$user_id'");
			}
		if($usertype==1 || $bc_added_by==''){
			$name		= getSingleColumn('firstname',"select * from `users` where `id`='$user_id'");
			$lname		= getSingleColumn('lastname',"select * from `users` where `id`='$user_id'");
			$bc_added_by = $name." ".$lname;
			}
		}
	
// FOR TICKET //
	$mainTitle		= $_POST["mainTitle"];
	$mainTitleOp	= $_POST['mainTitleOp'];
	
	if($mainTitleOp == 'Other')
		$mainTitle = $_POST['mainTitle'];
	else
		$mainTitle = $_POST['mainTitleOp'];
		
		
		
		
	$mainPrice					=	$_POST["mainPrice"];
	$event_ticket_id			=	$_POST['event_ticket_id'];
	
	if($_POST['split_fee']=='prometer'){
		$bc_prometer_service_fee	= 0;
		$bc_buyer_service_fee		= 100;
	}
	elseif($_POST['split_fee']=='buyer'){
		$bc_prometer_service_fee	= 100;
		$bc_buyer_service_fee		= 0;
	}
	else{
		$bc_prometer_service_fee	=	$_POST["prometer_service_free"];
		$bc_buyer_service_fee		=	$_POST["buyer_service_free"];
	}
	$start_sales_date			= 	$_POST['start_sales_date'];
	$end_sales_date				=	$_POST['end_sales_date'];
	$quantity_available			=	$_POST['quantity_available'];
	
	
	if($start_sales_date){
		$start_sales_date			=	date('Y-m-d', strtotime($_POST['start_sales_date']));
	 	$startTime					=	$_POST['start_sales_hrs'].":".$_POST['start_sales_min']." ".$_POST['start_sales_ampm'];
		$start_sales_time			=	date('H:i', strtotime($startTime));
	}
	else{
	//	$start_sales_date			=	date('Y-m-d');
	//	$start_sales_time			=	date('H:i');
	}
	
	if($end_sales_date){
	$end_sales_date					=	date('Y-m-d', strtotime($_POST['end_sales_date']));
	$endTime						=	$_POST['end_sales_hrs'].":".$_POST['end_sales_min']." ".$_POST['end_sales_ampm'];
	$end_sales_time					= 	date('H:i', strtotime($endTime));
	}
	
	$ticket_description				=	$_POST['ticket_description'];
	if($ticket_description == 'Access for one person to the launch celebration for the new website Eventgrabber'){
	$ticket_description = '';
	}
// FOR TICKET END//
	
	if($bc_gallery=='Create a name for your image gallery (i.e. Dress Code)'){
	$bc_gallery='';
	}
	
	$bc_event_cost			=	$_POST['event_cost'];

	
	
		if($bc_occupation_target){
	$bc_occupation_targets	=	'';
	for($d=0;$d<count($bc_occupation_target);$d++){
	
	$bc_occupation_targets.=	$bc_occupation_target[$d].",";
	
	}}
	
		if($frmID){
		if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
			$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
			if($bc_event_type==0){
				makeThumbnail($bc_image, 'event_images/', '', 163, 200,'');
				makeThumbnail($bc_image, 'event_images/', '', 163, 200,'th_');
			}
			else{
				makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			}
			$sql_img = " event_image = '$bc_image' , ";
			$_SESSION['UPLOADED_TMP_NAME']='';
		}
		
		$bs	=	getSingleColumn('id',"select * from `events` where `event_name`='$bc_event_name' && `id`='$frmID'");
		if($bs){
			$bc_seo_name	=	'';
		}
		else{
			$bc_seo_name	=	"`seo_name` = '$bc_seo_name', ";
		}
		
$sql = "UPDATE `events` SET `category_id` = '$bc_category_id', `subcategory_id` = '$bc_subcategory_id', `event_name` = '$bc_event_name',  $bc_seo_name `event_description` = '$bc_event_description', `event_cost` = '$bc_event_cost', $sql_img `event_age_suitab` = '$bc_event_age_suitab', `event_status` = '0', `added_by`='$bc_added_by', `men_preferred_age` = '$bc_men_preferred_age', `women_preferred_age` = '$bc_women_preferred_age', `occupation_target` = '$bc_occupation_targets', `event_end_time` = '$bc_event_end_time', `type` = 'draft', `free_event`='$bc_free_event', `event_type`='$bc_event_type', `alter_url`='$bc_alter_url', `zipcode`='$zipcode' WHERE `id` = '$frmID'";
 
 	$res = mysql_query($sql);
		
		if($res){
		$event_id	=	$frmID;
				
		
		$re = mysql_query("select * from `event_dates` where `event_id`='$event_id'");
		while($ro = mysql_fetch_array($re)){
		$date_id = $ro['id'];
		mysql_query("DELETE FROM `event_times` WHERE `date_id`='$date_id'");
		}
		
		mysql_query("DELETE from `event_dates` where `event_id`='$event_id'");
		mysql_query("DELETE from `event_music` where `event_id`='$event_id'");
		mysql_query("DELETE from `venue_events` where `event_id`='$event_id'");		
		mysql_query("UPDATE `event_gallery` SET `name` = '$bc_gallery' WHERE `event_id` = '$event_id'");
		
		/////// UPDATE DATE && TICKETS START ///////
		$bc_event_ticket_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$frmID'");	
			$t_id = $bc_event_ticket_id;
			if($bc_event_ticket_id){
			
			$mainTckId = $_POST['mainTckId'];
			$mainDescription	= $_POST['mainDescription'];
				mysql_query("UPDATE `event_ticket` SET `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `ticket_description` = '$ticket_description', `buyer_event_grabber_fee` = '$bc_buyer_service_fee', `prometer_event_grabber_fee` = '$bc_prometer_service_fee' WHERE `id` = '$t_id'");
				$ticket_id = $t_id;
				if($mainTckId){
					mysql_query("UPDATE `event_ticket_price` set  `title`='$mainTitle', `price`='$mainPrice', `desc`='$mainDescription' WHERE `id`='$mainTckId'");
					}
				else{
					mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id', '$mainDescription');");
					}
				
				 if ( is_array($_POST['ticketTypesOp']) ) {
				 
						for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
							
							if($_POST['ticketTypesOp'][$i] == 'Other')
								$title	= $_POST['ticketTypes'][$i];
							else
								$title	= $_POST['ticketTypesOp'][$i];
								
								$price				= $_POST['ticketPrices'][$i];
								$id					= $_POST['ticketIds'][$i];
								$delT				= $_POST['delT'][$i];
								$ticketDescription	= $_POST['ticketDescription'][$i];
							
							if($id && $delT!=1){
								mysql_query("UPDATE `event_ticket_price` set  `title`='$title', `price`='$price', `desc`='$ticketDescription' WHERE `id`='$id'");
							} // if($id)
							elseif($delT==1){
								mysql_query("DELETE FROM `event_ticket_price` WHERE `id` = '$id'");
							}
							else{
								if($title!='' && $price!=''){
									mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`) VALUES (NULL, '$title', '$price', '$ticket_id', '$ticketDescription')");
								} // END IF $title!='' && $price!=''
							} // END else
						} // END FOR
					} // END IF is_array($_POST['ticketTypesOp']
				
			}
			elseif($quantity_available!='' && $mainTitle!='' && $mainPrice!=''){
			
			$ticket_sql = "INSERT INTO `event_ticket` (`id`, `quantity_available`, `start_sales_date`, `start_sales_time`, `end_sales_date`, `end_sales_time`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$quantity_available', '$start_sales_date', '$start_sales_time', '$end_sales_date', '$end_sales_time', '$ticket_description', '$bc_buyer_service_fee', '$bc_prometer_service_fee', '$event_id')";
					$res = mysql_query($ticket_sql);
					$ticket_id	=	mysql_insert_id();
					
				if($ticket_id){		
				$mainDescription	= $_POST['mainDescription'];
					mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id', '$mainDescription')");
					
					if ( is_array($_POST['ticketTypesOp']) ) {
						$ticketTypesOp ='';
						for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
							
							if($_POST['ticketTypesOp'][$i] == 'Other')
								$title		= $_POST['ticketTypes'][$i];
							else
								$title				= $_POST['ticketTypesOp'][$i];
								$price				=	$_POST['ticketPrices'][$i];
								$ticketDescription	= $_POST['ticketDescription'][$i];
							
							if($title!='' && $price!=''){
								mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`) VALUES (NULL, '$title', '$price', '$ticket_id', '$ticketDescription')");
							} // END IF $title!='' && $price!=''
						} // END FOR
					} // END IF is_array($_POST['ticketTypesOp']
				} // END IF ($ticket_id)
			} // end else
		
		/////// UPDATE DATE && TICKETS START ///////
		
	$gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$frmID'");
	
	if($gallery_id==''){
	mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
	$gallery_id	=	mysql_insert_id();
	}	

	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
				$einame = str_replace(' ', '_',$einame);
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 120, 92,'th_');
					makeThumbnail($ei_image, 'event_images/gallery/', '', 199, 140,'sub_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
		
		
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
			if($bc_event_type==0){
				makeThumbnail($bc_image, 'event_images/', '', 163, 200,'');
				makeThumbnail($bc_image, 'event_images/', '', 163, 200,'th_');
			}
			else{
				makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			}
			$sql_img = " event_image = '$bc_image' , ";
		}
		
	
	
		$bc_source_id	=	"USER-".rand(); 
		$bc_publishdate	=	 date("Y-m-d");
		
		
		$bc_event_status = 0;
		
		
		
	$sql	=	"insert into events (event_source,source_id,userid,category_id,subcategory_id,event_name,seo_name,musicgenere_id,event_description,event_cost,event_image,	event_sell_ticket,event_age_suitab,event_status,publishdate,averagerating,modify_date,del_status,added_by,men_preferred_age,women_preferred_age,occupation_target,video_name,video_embed,repeat_event,repeat_freq,tags,privacy,pending_approval,event_end_time,type,free_event,event_type,is_expiring,alter_url,zipcode) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_seo_name . "','" . $bc_musicgenere_id . "','" . $bc_event_description . "','" . $bc_event_cost . "','" . $bc_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "','".$bc_men_preferred_age. "','".$bc_women_preferred_age. "','" . $bc_occupation_targets . "','".$bc_video_name. "','".$bc_video_embed. "','".$repeat."','".$frequency."','','". $privacy ."','0','$bc_event_end_time','draft','$bc_free_event','$bc_event_type','1','$bc_alter_url','$zipcode')";
		
		$res	=	mysql_query($sql);
		$frmID	=	mysql_insert_id();
		$event_id 	=	$frmID;
		
		if ($res) {
		
		
// FOR ADD TICKET START // 
	if($bc_event_type!=0 && $quantity_available!='' && $mainTitle!='' &&$mainPrice!=''){
		$ticket_sql = "INSERT INTO `event_ticket` (`id`, `quantity_available`, `start_sales_date`, `start_sales_time`, `end_sales_date`, `end_sales_time`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$quantity_available', '$start_sales_date', '$start_sales_time', '$end_sales_date', '$end_sales_time', '$ticket_description', '$bc_buyer_service_fee', '$bc_prometer_service_fee', '$event_id')";
	$res = mysql_query($ticket_sql);
	$ticket_id	=	mysql_insert_id();
		}
	if($ticket_id){
	mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id')");
		
				if ( is_array($_POST['ticketTypesOp']) ) {
		$ticketTypesOp ='';
			for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
				
				if($_POST['ticketTypesOp'][$i] == 'Other')
					$title		= $_POST['ticketTypes'][$i];
				else
					$title		= $_POST['ticketTypesOp'][$i];
		
				$price				=	$_POST['ticketPrices'][$i];
		
				if($title!='' && $price!=''){
				
				mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$title', '$price', '$ticket_id')");
				} // END IF $title!='' && $price!=''
			} // END FOR
		} // END IF is_array($_POST['ticketTypesOp']
	} // END IF ($ticket_id)
		
	
		
		
// FOR ADD TICKET END // 	
		
// FOR DATE & TIME //

	
	
// start main gallery & image upload ///
	if($bc_gallery){
	mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
	$gallery_id	=	mysql_insert_id();
	}
	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 107, 92,'th_');
					makeThumbnail($ei_image, 'event_images/gallery/', '', 199, 140,'sub_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
		} else {
			$sucMessage = "Error: Please try Later";
		}
		
	}
	
	
	
	
if($res){

	//if($bc_specials!=''){
//		$bc_specials_id	=	getSingleColumn('id',"select * from `special_event` where `event_id`='$frmID'");
//		if($bc_specials_id){
//			mysql_query("UPDATE `special_event` SET `specials_id` = '$bc_specials' WHERE `id` = '$bc_specials_id'");
//		}
//		else{
//			mysql_query("INSERT INTO `special_event` (`id`, `event_id`, `specials_id`) VALUES (NULL, '$frmID', '$bc_specials')");
//		}
//	}
	
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
		if($endTime!='' && $endTime!="00:00:00"){
			if($end_am_pm[$i]==0)
				$endTime .= " AM";
			else
				$endTime .= " PM";
			$endTime = date("H:i", strtotime($endTime));
		}
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
	} // end if $res
	}
}

	require_once('includes/header.php'); 
	
?>

<div style="width:960px; margin:auto;">
	<div class="welcomeBox"></div>
	<div class="eventDetailhd"><span>Event Saved</span></div>
	<div class="clr">&nbsp;</div>

	<div style="width:960px; margin:auto; padding-bottom:10px; height:300px; padding-top:20px; font-size:16px; line-height:25px">
		<strong>Your event is Saved as Draft. &nbsp;  <a href="event_manager.php" style="text-decoration:underline; color:#0066FF">Event Manager</a></strong>
		
		<br />
		
	</div>
</div>
<?php
	include('includes/footer.php');
	?>