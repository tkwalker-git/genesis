<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$member_full_name = attribValue('members', 'concat(name," ",lname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
$meta_title	= 'Dashboard';
include_once('includes/header.php');
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
.menuBlock li.libutton a {
	font-size:12px;
	}
</style>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a#profileImage").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	});
</script>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard.css" rel="stylesheet" type="text/css">

<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"></div>
    <?php echo dashboardTab('dashboard'); ?>
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
				
					$image = getSingleColumn('image_name',"select * from members where id=" . $member_id);
					
				if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
					$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,127,2000 );
					$img = '<a href="/images/members/'.$image.'" id="profileImage" ><img align="center" '. $img .' style="border:#e5e5e5 solid 6px" /></a>';
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
                    <div class="db_fl2">
                      <?php
					echo getSingleColumn('tot',"select count(*) as tot from `member_referals` where `ref_member_id`='$member_id'");
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Events Rated:</div>
                    <div class="db_fl2">
                      <?php
						echo eventsRated($member_id);
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Events Posted:</div>
                    <div class="db_fl2">
                      <?php
						echo getSingleColumn('tot',"select count(*) as tot from events where `userid`='$member_id'");
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Calendar Status:</div>
                    <div class="db_fl2">Synced</div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Profile Status:</div>
                    <div class="db_fl2">
                      <?php
						$prof_status = getSingleColumn('status',"select * from members where `id`='$member_id'");
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
			<span id="activeEvent">
			<?php
			$rsd = mysql_query("select * from `events` where `userid`='$member_id' && `event_status`='1' && `is_expiring`='1' && `event_type`!='0'");
	$total_rec		= mysql_num_rows($rsd);
	


			// echo $pagenum;
			?>
            <div class="db_box">
              <div class="head">Active Event
                <div style="float:right">
					<a href="<?php echo ABSOLUTE_PATH; ?>create_event.php">
						<img src="<?php echo IMAGE_PATH; ?>create_new_event.png" align="left" style="margin-right:10px">
					</a>
					<?php
					if($total_rec > 1){?>
					<div id="img" style="width:20px; float:left">	
							<img title="" src="<?php echo IMAGE_PATH; ?>ar_lft.png" />
						
						
						<a href="javascript:loadActvEvnt('<?php echo ABSOLUTE_PATH;?>','next',1)">
							<img title="Next" src="<?php echo IMAGE_PATH; ?>ar_rght_actv.png" />
						</a>
					</div>
					<?php
					}
					?>
				</div>
              </div>
              <div class="active_event">
                <div class="db_showEvn">
                  <?php
				  	
					//$event_id = '17360'; // 17449
		$sql = "select *,(select event_date from event_dates where expired=0 AND event_id=events.id ORDER by event_date ASC LIMIT 1) as event_date from events where `userid`='$member_id' && `event_status`='1' && `is_expiring`='1' && `event_type`!='0' ORDER by event_date ASC LIMIT 0 , 1";
					$res = mysql_query($sql);
					while($rows2 = mysql_fetch_array($res)){
						$event_id		= $rows2['id'];
						$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
						$full_name		= DBout($rows2['event_name']);
						$event_date 	= getEventStartDates($rows2['id']);
						$source			= $rows2['event_source'];
						$event_image	= getEventImage($rows2['event_image'],$source,'1');
						$event_url		= getEventURL($rows2['id']);					
					}
				?>
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
                    <div class="ev_fltlft" align="right" style="width:56%">Page View:</div>
                    <div class="db_fl2"><?php echo getSingleColumn('view',"select * from `events` where `id`='$event_id'"); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Conversion Rate:</div>
                    <div class="db_fl2"><?php echo conversionRate($event_id)."%";?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Time till Event:</div>
                    <div class="db_fl2">
                      <?php
						 echo $time_till = timeTillEvent($event_id);
						
						
						?>
                    </div>
                    <div class="clr"></div>
                  </div>
                </div>
                <!-- end db_detailEvn -->
              </div>
            </div>
			</span>
            <!-- end db_box -->
            <div class="clr"></div>
            <div class="navigation_bar" id="tab">
              <div class="nav">
                <div class="menuBlock">
                  <ul style="margin:10px">
                    <li class="libutton"><a <?php if ($_GET['pg']==''){?>href="javascript:void(0);" class="a_active" <?php } else{ ?> href="dashboard.php#tab" <?php } ?>><span <?php if ($_GET['pg']==''){?> class="span_active" <?php } ?>>Profile Setting</span></a></li>
                    <li class="libutton"><a <?php if ($_GET['pg']=='e_preferences'){?>href="javascript:void(0);" class="a_active" <?php } else{ ?> href="?pg=e_preferences#tab" <?php } ?>><span<?php if ($_GET['pg']=='e_preferences'){?> class="span_active" <?php } ?>>Event Preferences</span></a></li>
                    <li class="libutton"><a <?php if ($_GET['pg']=='m_preferences'){?>href="javascript:void(0);" class="a_active" <?php } else{ ?> href="?pg=m_preferences#tab" <?php } ?>><span<?php if ($_GET['pg']=='m_preferences'){?> class="span_active" <?php } ?>>Music Preferences</span></a></li>
                    <li class="libutton"><a <?php if ($_GET['pg']=='age'){?>href="javascript:void(0);" class="a_active" <?php } else{ ?> href="?pg=age#tab" <?php } ?>><span<?php if ($_GET['pg']=='age'){?> class="span_active" <?php } ?>>Age Suitability</span></a></li>
                  </ul>
                </div>
                <!-- end menuBlock -->
              </div>
              <!-- end nav -->
            </div>
            <!-- end navigation_bar -->
            <?php
				 
				if ( $_GET['pg'] == 'e_preferences' )
					include_once("event_preference_setting.php");
				else if ( $_GET['pg'] == 'm_preferences' )
					include_once("music_preference_setting.php");	
				else if ( $_GET['pg'] == 'age' )
					include_once("age_preference_setting.php");	
				else
					include_once("profile_setting.php");
							 
					?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>
