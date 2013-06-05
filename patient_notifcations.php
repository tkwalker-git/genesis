<?php

	if ( isset($_POST['continue']) )
	{
		
		$new_recommended	= $_POST['new_recommended'];
		$events_calendar	= $_POST['events_calendar'];
		$free_tickets		= $_POST['free_tickets'];
		$my_profile			= $_POST['my_profile'];
		$new_features		= $_POST['new_features'];
		$special_event		= $_POST['special_event'];
		$do_not				= $_POST['do_not'];
		
		$action	= getSingleColumn("id","select * from `notifications` where `user_id`='$member_id'" );
		
		if($action == ''){
			$res = mysql_query("INSERT INTO `notifications` (`id`, `new_recommended`, `events_calendar`, `free_tickets`, `my_profile`, `new_features`, `special_event`, `do_not`,`user_id`) VALUES (NULL, '$new_recommended', '$events_calendar', '$free_tickets', '$my_profile', '$new_features', '$special_event', '$do_not', '$member_id');");
			}
		else{
			$res = mysql_query("UPDATE `notifications` SET `new_recommended` = '$new_recommended', `events_calendar` = '$events_calendar', `free_tickets` = '$free_tickets', `my_profile` = '$my_profile', `new_features` = '$new_features', `special_event` = '$special_event' , `do_not`='$do_not' WHERE `user_id` = '$member_id'");
		}
		
		
		if($res){
			echo "<script>window.location.href='?p=patient-recommend'</script>";
		}
	
	
	
	}
	
	
	$sql = "select * from notifications where user_id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$new_recommended 	= $row['new_recommended'];
		$events_calendar	= $row['events_calendar'];
		$free_tickets		= $row['free_tickets'];
		$my_profile			= $row['my_profile'];
		$new_features		= $row['new_features'];
		$special_event		= $row['special_event'];
		$do_not				= $row['do_not'];
	
	}
?>
<style>
.whiteMiddle .evField {
	
	}

.whiteMiddle .evField {
	text-align:left;
	font-size:15px;
	width:134px;
	}
	
.evLabal{
	font-size:15px;
	}
	
.evInput{
	font-size:14px;
	}
	
</style>
<div class="yellow_bar"> &nbsp; STEP 3 - SETUP NOTIFICATIONS</div>
<!-- /yellow_bar -->
<form method="post">
<div style="padding:0 10px;"><br />
	
	<strong>EMAIL NOTIFICATIONS</strong><br /><br />
	Email me when:<br /><br />
	<div style="padding-left:10px; line-height:25px;">
		<!-- <label><input type="checkbox" name="new_recommended" <?php if ($do_not ==1){ echo 'disabled="disabled"'; }?> class="ck" <?php if($new_recommended==1){ echo 'checked="checked"'; } ?> value="1" /> New recommended events come out</label><br /> -->
		<label><input type="checkbox" name="events_calendar" <?php if ($do_not ==1){ echo 'disabled="disabled"'; }?> class="ck" <?php if($events_calendar==1){ echo 'checked="checked"'; } ?> value="1" /> New Clinic Events Come out </label><br />
		<label><input type="checkbox" name="free_tickets" <?php if ($do_not ==1){ echo 'disabled="disabled"'; }?> class="ck" <?php if($free_tickets==1){ echo 'checked="checked"'; } ?> value="1" /> Give-aways are being offered</label><br />
		
	</div>
	<br /><br />
	<!--
<strong>TEXT NOTIFICATIONS</strong><br /><br />
	<div style="padding-left:10px; line-height:25px;">
		<label><input type="checkbox" name="special_event" <?php if ($do_not ==1){ echo 'disabled="disabled"'; }?> class="ck" <?php if($special_event==1){ echo 'checked="checked"'; } ?> value="1" /> I would like to receive special event offerings via text</label><br />
	</div>
	<br /><br />
	
	<strong></strong><br /><br />
-->
	<div style="padding-left:10px; line-height:25px;">
		<label><input type="checkbox" id="donot" name="do_not" <?php if($do_not==1){ echo 'checked="checked"'; } ?> value="1" /> Please DO NOT send me any notifications. I understand I may miss out on great offers</label><br />
	</div>
	
	<div align="right"><br /><br />
		
		<input type="image" src="<?php echo IMAGE_PATH; ?>save_&_continue.png" align="right" name="continue" value="Save & Continue" style="padding:10px 0 0 10px;" />
		<a href="?p=patient-profile"><img src="<?php echo IMAGE_PATH; ?>back_gray.png" align="right"  style="padding:10px 0 0 10px;" /></a>
		<input type="hidden" name="continue" value="Save & Continue" />&nbsp; 
		  <br class="clr" />&nbsp;
	</div>
	</div>
</form>
<script>
$('#donot').click(function(){

	if(this.checked == true){
		$('.ck').attr('checked',false);
		$('.ck').attr('disabled',true);
	}
	else{
		$('.ck').attr('disabled',false);
		}

});
</script>