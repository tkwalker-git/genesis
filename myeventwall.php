<?php 

	require_once('admin/database.php');
	require_once('site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

if($_GET['test']!='adil')
	echo "<script>window.location.href='dashboard.php';</script>";
		
	$meta_title	= 'My Event Wall';
	require_once('includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='$member_id'");
	
	if ( ( isset ($_GET['going']) || isset ($_GET['d']) ) && isset($_GET['id']) ) {
		
		if ( $_GET['going'] == 'yes')
			$go = 1;
		else if ( $_GET['going'] == 'no')
			$go = 0;
		else
			$go = -1;
		
		if ( isset($_GET['d']) && !isset($_GET['going'])  ) 
			mysql_query("update event_wall set going='-1' where event_id=" . $_GET['id']);
		else
			mysql_query("update event_wall set going='". $go ."' where event_id=" . $_GET['id']);
			
		if ( isset($_GET['n']) ) 
			mysql_query("insert into event_wall VALUES (NULL,'". $_GET['id'] ."','". $member_id ."','". date("Y-m-d") ."','". $go ."',1,1)");

		echo "<script>window.location.href='myeventwall.php';</script>";
	}
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 	= DBout($row['firstname']);
		$email  = DBout($row['email']);
		
		$image	= DBout($row['image_name']);
		
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img class="userImage" align="center" '. $img .' />';	
		} else
			$img = '<img class="userImage" src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		$total_rated 	= getSingleColumn('tot',"select count(*) as tot from comment where c_type='event' AND by_user=" . $member_id);
		$total_rated_v 	= getSingleColumn('tot',"select count(*) as tot from comment where c_type='venue' AND by_user=" . $member_id);
		$total_onwall 	= getSingleColumn('tot',"select count(*) as tot from event_wall where userid=" . $member_id);
		
		$total_comments = getSingleColumn('tot',"select count(*) as tot from comment where by_user=" . $member_id);
		$helpfull_rev	= getSingleColumn('tot',"SELECT count(*) FROM review_helpfull WHERE status=1 and review_id IN (select id from comment where userid='" . $member_id. "')");
		
		$total_friends_invited = getSingleColumn('tot',"select count(*) as tot from member_referals where ref_member_id=" . $member_id);
		
		$helpfull_percent = 0 ;
		if ( $total_comments > 0 )  
			$helpfull_percent = ceil( ($helpfull_rev/$total_comments) * 100);
	}
	
?>
<style>
.nav{
	width: 908px;
	}
.navigation_bar{
	width:910px;
	}
.recommendedBlock{
	width:901px;
	}
ul.recommend_ul li {
	margin: 0 22px 20px;
	}
.categoryEventBlock, #events {
	width:907px;
}
.categoryEventBlock_left {
	width:182px;
	}
	
	
</style>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"></div>
    <?php  echo dashboardTab('myeventwall'); ?>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
            <!--<span class="ew-heading"></span>-->
			 <div class="db_box" style="margin-right:14px">
              <div class="head">Hello <?php echo $member_full_name; ?><a title="Edit Profile" href="<?php echo ABSOLUTE_PATH; ?>dashboard.php#tab"><img src="<?php echo IMAGE_PATH; ?>db_edit_profile.png" align="right"></a></div>
             <div class="active_event">
                <div class="db_showEvn" style="background:none">
				  <?php
				  $member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
				
					$image = getSingleColumn('image_name',"select * from users where id=" . $member_id);
					
				if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
					$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,127,2000 );
					$img = '<img align="center" '. $img .' style="border:#e5e5e5 solid 6px" />';
				} else
					$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="" width="127" border="0" style="border:#e5e5e5 solid 6px" />';
					
					echo $img;
					
				?>                  
                </div>
                <div class="db_detailEvn">
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Upcoming Events:</div>
                    <div class="db_fl2">
					<?php
						echo getSingleColumn('tot',"select count(*) as tot from events where `event_status`='1' && `is_expiring`='1' && `userid`='$member_id'");
					?>
					</div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Friends Invited:</div>
                    <div class="db_fl2"><?php
					echo getSingleColumn('tot',"select count(*) as tot from `member_referals` where `ref_member_id`='$member_id'");
					?>
					</div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Events Rated:</div>
                    <div class="db_fl2"><?php
						echo eventsRated($member_id);
					?>
					</div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Events Posted:</div>
                    <div class="db_fl2"><?php
						echo getSingleColumn('tot',"select count(*) as tot from events where `userid`='$member_id'");
					?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Calendar Status:</div>
                    <div class="db_fl2">Synced</div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Profile Status:</div>
                    <div class="db_fl2"><?php
						$prof_status = getSingleColumn('enabled',"select * from users where `id`='$member_id'");
						if($prof_status == 1)
							echo "Active";
						else
							echo "Inactive";
					//	status
					?>
					</div>
                    <div class="clr"></div>
                  </div>
                </div>
                <!-- end db_detailEvn -->
              </div>
            </div>
            <!-- end db_box -->
			<?php
			if($_SESSION['usertype']=='2'){?>
            <div class="db_box">
			 <?php
				  	
					//$event_id = '17360'; // 17449
					$sql = "select * from `events` where `userid`='$member_id' && `event_status`='1' && `is_expiring`='1' && `event_type`!='0' ORDER BY `id` DESC LIMIT 0,1";
					$res = mysql_query($sql);
					while($rows2 = mysql_fetch_array($res)){
						$event_id		= $rows2['id'];
						$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
						$full_name		= DBout($rows2['event_name']);
						$event_date 	= getEventStartDates($rows2['id']);
						$source			= $rows2['event_source'];
						$event_image	= getEventImage($rows2['event_image'],$source,'1');
						$event_url		= getEventURL($rows2['id']);	
						$pType			= $rows2['event_type'];
							
						if($pType==1)
							$pType = 'Featured';
						elseif($pType==2)
							$pType = 'Premium';
						elseif($pType==3)
							$pType = 'Custom';
						else
							$pType = 'Basic';
					}
				?>
              <div class="head">Active Event <a href="<?php echo ABSOLUTE_PATH; ?>create_event.php?id=<?php echo $event_id; ?>"><img src="<?php echo IMAGE_PATH; ?>edit_event.png" align="right"></a></div>
              <div class="active_event">
                <div class="db_showEvn">
                 
                  <div class="db_nameEvn"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></div>
                  <div class="db_dateEvn"><?php echo $event_date; ?></div>
                  <div style="padding-top:11px;"><a style="display:block; height:155px; overflow:hidden;" href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a></div>
                </div>
                <div class="db_detailEvn">
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Tickets Sold :</div>
                    <div class="db_fl2"><?php echo getTicketsRecord($event_id,'Sold'); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Gross Sales :</div>
                    <div class="db_fl2"><?php echo "$".getTicketsRecord($event_id,'Gross Sales'); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%"># of Tickets left:</div>
                    <div class="db_fl2"><?php echo getTicketsRecord($event_id,'Tickets Left'); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Promotion Type:</div>
                    <div class="db_fl2"><?php echo $pType; ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">RSVPs:</div>
                    <div class="db_fl2"><?php echo getSingleColumn('tot',"select count(*) as tot from events_rsvp where `event_id`='$event_id'");?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Time till Event:</div>
                    <div class="db_fl2">
                      <?php
						echo timeTillEvent($event_id);
						?>
                    </div>
                    <div class="clr"></div>
                  </div>
                </div>
                <!-- end db_detailEvn -->
              </div>
            </div>
            <!-- end db_box -->
           <?php }
		   else{ ?>
		    <div class="db_box">
			
			 <div class="head">Event Activity Feed <a href="#"><img src="<?php echo IMAGE_PATH; ?>edit_settings.png" align="right"></a></div>
              <div class="active_event">
                <div class="db_showEvn">
				</div>
			  </div>
			</div>
			  <!-- end db_box -->
			<?php } ?>
            <div class="clr"><br />&nbsp;</div>
			
			<div class="navigation_bar">
				<div class="nav">
				<div class="menu_heading heading_colored_14"><a href="<?php echo ABSOLUTE_PATH; ?>dashboard.php?pg=e_preferences#tab">Edit Preferences</a></div><!--end menu_heading-->
					<div class="menuBlock">
						<ul>
							<?php if ( $_GET['type'] == 'reviews' ) { ?>
							<li class="libutton"><a href="?type=eventwall"><span>My Eventwall</span></a></li>
							<li class="libutton"><a href="" class="a_active"><span class="span_active">Reviews and Ratings</span></a></li>
							<li class="libutton"><a href="?type=recomemded"><span>Recommended Events</span></a></li>
							<li class="libutton"><a href="manage_event.php"><span>Event Manager</span></a></li>
							<?php } else if ( $_GET['type'] == 'eventwall' ) { ?>
							<li class="libutton"><a href="" class="a_active"><span class="span_active">My Eventwall</span></a></li>
							<li class="libutton"><a href="?type=reviews"><span>Reviews and Ratings</span></a></li>
							<li class="libutton"><a href="?type=recomemded"><span>Recommended Events</span></a></li>
							<li class="libutton"><a href="manage_event.php"><span>Event Manager</span></a></li>
							<?php	} else { ?>
							<li class="libutton"><a href="?type=eventwall"><span>My Eventwall</span></a></li>
							<li class="libutton"><a href="?type=reviews"><span>Reviews and Ratings</span></a></li>
							<li class="libutton"><a href="" class="a_active"><span class="span_active">Recommended Events</span></a></li>
							<li class="libutton"><a href="manage_event.php"><span>Event Manager</span></a></li>
							
							<?php } ?>	
						</ul>
					</div><!--end menu-->
				</div><!--end nav-->
			</div> <!-- end navigation_bar -->
			<?php 
						
						if ( $_GET['type'] == 'reviews' )
							include_once("widget_reviews.php");
						else if ( $_GET['type'] == 'eventwall' )
							include_once("widget_wall.php");	
						else
							include_once("widget_recomemded.php");
							 
					?>
            <div class="clr"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>