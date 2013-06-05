<?php

	require_once('admin/database.php');
	require_once('site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
	$meta_title	= 'Event Manager';
	require_once('includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 	= DBout($row['firstname']);
		$email  = DBout($row['email']);
		
		$image	= DBout($row['image_name']);
		
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img align="center" '. $img .' />';	
		} else
			$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		
		$total_events_grabbed = 0;
	}
	
	if ( $_GET['delete'] > 0 ) {
		
		$dEvent_id = $_GET['delete'];
		if ( mysql_query("delete from events where id='$dEvent_id' AND userid='". $member_id ."'") ) {
			mysql_query("delete from event_dates where event_id='$dEvent_id'");
			mysql_query("delete from event_music where event_id='$dEvent_id'");
			mysql_query("delete from event_wall where event_id='$dEvent_id'");
			mysql_query("delete from venue_events where event_id='$dEvent_id'");
			$ticket_id	=	getSingleColumn('id',"select * from event_ticket where `event_id` = '$dEvent_id'");
			if($ticket_id){
			mysql_query("delete from event_ticket where event_id='$dEvent_id'");
			mysql_query("delete from event_ticket_price where ticket_id='$ticket_id'");
			}
			
			$sucMessage = 'Event is deleted successfully.';
			
		}
		
	}
	
?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script>
function removeAlert(url) {

	var con = confirm("Are you sure to delete this event? Your event will also be deleted from Event's Wall of all other members.")

	if (con) 

		window.location.href = url;

}

$(document).ready(function() {
		$(".ticket").fancybox({
			'titleShow'				:	false,
			'transitionIn'			:	'elastic',
			'transitionOut'			:	'elastic',
			'width'					:	800,
			'height'				:	1250,
			'type'					:	'iframe',
			'hideOnOverlayClick'	:	false
		});
	});
	
	
	
</script>


<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Event Manager</div>
	
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
           <div class="whiteMiddle" style="padding-top:1px;">
				 <form action="" method="post" name="manage_events" id="manage_events">
				 <div style="margin:10px 2px" class="ev_fltlft"> <a href="<?php echo ABSOLUTE_PATH;?>create_event.php"><img src="<?php echo IMAGE_PATH;?>add_event3.png" /></a> </div>
				 
				  <div style="margin:10px 2px" class="ev_fltrght"> <a href="<?php echo ABSOLUTE_PATH;?>coupons.php"><img src="<?php echo IMAGE_PATH;?>coupons.gif" /></a> </div>
				
                <div style="padding:20px 0 0 30px" class="ev_fltlft">
                  <div class="ev_fltlft" style="padding-top:2px"> <strong>Show Only:</strong> </div>
                  <div class="ev_fltlft" style="padding:0 10px;">
                  <!--  <select name="show" onchange="alert(this.value)">-->
				  <script>
					  function chng(value){
						   if(value == 'flyers'){
							   document.getElementById('filtr').disabled	=	false;
							   document.getElementById('filtr').value		=	'all';
						   }
						   else{
							   document.getElementById('filtr').disabled	=	true;
						   }
					   }
				  </script>
				   <select name="show" onchange="chng(this.value);">
                      <option value="all">All</option>
                      <option value="flyers" <?php if ($_POST['show'] == 'flyers'){ echo 'selected="selected"';}?>>Flyers</option>
                      <option value="standard" <?php if ($_POST['show'] == 'standard'){ echo 'selected="selected"';}?>>Standard</option>
                    </select>
                  </div>
                  <div class="ev_fltlft" style="padding-top:2px"> <strong>Filter Events:</strong> </div>
                  <div class="ev_fltlft" style="padding:0 10px;">
				  
                    <select name="filtr" id="filtr" <?php if ($_POST['show']!='flyers'){ echo 'disabled="disabled"'; } ?>>
                      <option value="all">All</option>
                      <option value="successful_payment" <?php if ($_POST['filtr'] == 'successful_payment'){ echo 'selected="selected"';}?>>Successful Payment</option>
                      <option value="pending_payment" <?php if ($_POST['filtr'] == 'pending_payment'){ echo 'selected="selected"';}?>>Pending Payment</option>
                      <option value="has_ticket" <?php if ($_POST['filtr'] == 'has_ticket'){ echo 'selected="selected"';}?>>Has Ticket</option>
                    </select>
                  </div>
                  <div class="ev_fltlft" style="padding:0 10px;">
                    <input type="submit" value="submit" name="filter" />
                  </div>
                  <div class="clr"></div>
                </div>
				</form>
                <div class="clr"></div>
                <font color='green'><?php echo $sucMessage;?> </font> <br>
                <div style="background-color:#EEEEEE; border-bottom:#CCCCCC solid 2px; border-top:#CCCCCC solid 2px; font-size:14px; font-weight:bold">
                  <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                      <td width="43%">Event Name</td>
                      <td width="12%">Category</td>
                      <td width="11%">Event Type</td>
                      <td width="9%">Published</td>
                      <td width="9%">Payment</td>
                      <td width="16%" align="center">Action</td>
                    </tr>
                  </table>
                </div>
                <?php  
						if($_POST['filter'] && $_POST['filter']!='all'){
							if($_POST['show'] == 'standard'){
								$sql = "select * from events where userid=" . $member_id . " && `event_type` = '0' order by publishdate DESC";
							}
							elseif($_POST['show'] == 'flyers'){
								$sql = "select * from events where userid=" . $member_id . " && `event_type` = '1' order by publishdate DESC";
							}
							else{
							$sql = "select * from events where userid=" . $member_id . " order by publishdate DESC";	
							}
						}
						else{
						$sql = "select * from events where userid=" . $member_id . " order by publishdate DESC";
						}
						$res = mysql_query($sql);
						$i=0;
								if ( mysql_num_rows($res) > 0 ) {
									while ( $row = mysql_fetch_assoc($res) ) {
										
										$event_id		= $row['id'];
										$event_name 	= DBout($row['event_name']);
										$category 		= attribValue('categories', 'name', "where id='". $row['category_id'] ."'");
										$scategory	 	= attribValue('sub_categories', 'name', "where id='". $row['subcategory_id'] ."'");
										$date			= date("m/d/Y",strtotime($row['publishdate']));
										$event_url		= getEventURL($event_id);
										
										if ( $row['event_type'] == 0 ) {
											$eType = 'Standard';
											$payment = '--';
										} else {
											$ticket_id 	= getSingleColumn('id',"select * from event_ticket where `event_id` = '$event_id'");
										//	if($row['event_type']==1)
												$eType = 'E-Flyer';	
										//	elseif($row['event_type']==2)
										//		$eType = 'Premium';
											$pym = attribValue("orders","total_price"," where main_ticket_id='$event_id' && `type`='flyer' && `total_price`!=''");
											if ( $pym > 0 ) {
												$payment = 'Received.';
											} else {	
												$payment = '<a style="color:blue; text-decoration:underline" href="'.ABSOLUTE_PATH_SECURE.'create_flyer_step2.php?id='. $event_id .'"><strong>Pay Now</strong></a>';
											}
										}
										
										if ( $row['event_status'] == 1 ) 
											$published = 'Yes';
										else
											$published = 'No';	
										
											
	
									 if( ($i%2) == 0)
										   $class='class="preferenceWhtBox"';
									 else
										  $class='class="preferenceBlueBox"';
										  
								
								if($_POST['filtr'] == 'successful_payment'){
									$successful_payment = attribValue("orders","id"," where main_ticket_id='$event_id' && `type`='flyer' && `total_price`!=''");
								}
								elseif($_POST['filtr'] == 'pending_payment'){
									$pending_payment = attribValue("orders","id"," where main_ticket_id='". $event_id ."' && `type`='flyer' && `total_price`!=''");
								}
								elseif($_POST['filtr'] == 'has_ticket'){
									$ticket_id 	= getSingleColumn('id',"select * from event_ticket where `event_id`='". $event_id ."'");
									if($ticket_id){
									$has_ticket = '1';
									}
									else{
									$has_ticket = '0';
									}
								}
								
								if(($_POST['filtr'] == 'has_ticket' && $has_ticket == '1') || ($_POST['filtr'] == 'successful_payment' && $successful_payment) || ($_POST['filtr']!= 'successful_payment' && $_POST['filtr']!= 'pending_payment' && $_POST['filtr']!= 'has_ticket') || ($_POST['filtr'] == 'pending_payment' && !$pending_payment)){
						echo $pending_payment;		?>
                <div style="line-height:20px; min-height:30px" <?=$class?>>
                  <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                      <td width="43%"><a href="<?php echo $event_url; ?>" ><?php echo $event_name;?></a>
                        <?php if($row['type']=='draft'){?>
                        <font color="red"> (Draft)</font>
                        <?php } ?></td>
                      <td width="12%"><?php echo $scategory;?></td>
                      <td width="11%"><?php echo $eType;?></td>
                      <td width="9%"><?php echo $published;?></td>
                      <td width="9%"><?php if($row['type']!='draft'){ echo $payment; }?></td>
                      <td width="16%" align="right" >
					  <select style="width:110px" onchange="if(this.value!=''){if(this.value=='delete'){removeAlert('manage_event.php?delete=<?php echo $event_id;?>')}else{window.location.href=this.value}};">
					  	<option value="">Select</option>
						<?php
						if($row['free_event']!='1'){
						?>
						<option value="load_xls.php?type=rsvp&event_id=<?php echo $event_id;?>">Download RSVP</option>
						<option value="close_rsvp.php?id=<?php echo $event_id;?>">Close RSVP</option>
						<?php
						}
						if($row['event_type'] != 0){
						?>
							<option value="load_xls.php?type=ticket&event_id=<?php echo $event_id;?>">Download Purchased</option>
							<option value="create_event.php?t=2&id=<?php echo $event_id; ?>#2">Tickets</option>
						<?php } ?>
						
						<option value="create_event.php?id=<?php echo $event_id;?>">Edit</option>
						<option value="delete">Delete</option>
					</select>
					  
					 </td>
                    </tr>
                  </table>
                </div>
                <?php $i++;
						}}
							} else { ?>
                <div class="preferenceWhtBox">
                  <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                      <td width="80%" align="center" style="font-size:20px; font-weight:bold; padding:60px 0px"> You have not created any event yet. You can add new events <a style="color:#0066FF; text-decoration:underline" href="create_event.php">here</a>.</td>
                    </tr>
                  </table>
                </div>
                <?php } ?>
                <div align="right"> <a href="myeventwall.php"><img src="images/back_event_well_btn.gif" class="vAlign" vspace="10" hspace="10" border="0"/></a> </div>
		   </div> <!-- end whiteMiddle -->
        </div>
      </div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>