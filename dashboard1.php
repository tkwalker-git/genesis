<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
$meta_title	= 'Dashboard';
include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-1.4.min.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a#profileImage").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	
	
	$('.video').click(function() {
		var vSrc	= $(this).attr('src');
		var vName	= vSrc.split('/hqdefault.jpg');
		vName		= vName[0].split('vi/');
		vName		= vName[1];
		$('#videoArea').slideDown(1000);
		$("#videoArea span").html('<iframe width="824" height="540" src="http://www.youtube.com/embed/'+vName+'" frameborder="0" allowfullscreen></iframe>');
		$('html,body').animate({
			        scrollTop: $("#videoArea").offset().top},
        			'slow');
	});
	
	$('.closeVd').click(function() {
		$('#videoArea').slideUp(1000);
	});	
	
});	// end $(document).ready
	
</script>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
		 <div style="padding-bottom:50px; overflow:hidden;">
            <div class="db_box" style="margin-right:14px;">
              <div class="head">Hello <?php echo $member_full_name; ?></div>
              <div class="active_event">
                <div class="db_showEvn2">
                  <?php
				  $member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
				
					$image = getSingleColumn('image_name',"select * from users where id=" . $member_id);
					
				if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
					$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,97,2000 );
					$img = '<a href="/images/members/'.$image.'" id="profileImage" ><img align="center" '. $img .' /></a>';
				} else
					$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="109px" width="97" border="0" />';
					
					echo $img;
				
				echo "<span id='name'>".$member_full_name."</span>";
				if($_SESSION['usertype']==2)
					$type = "ORGANIZER";
				else
					$type = "USER";
				echo "<span id='type'>".$type."</span>";
				?>
				
                </div>
                <div class="db_detailEvn">
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Upcoming Events</div>
                    <div class="db_fl2">
                      <?php
						echo getSingleColumn('tot',"select count(*) as tot from events where `event_status`='1' && `is_expiring`='1' && `userid`='$member_id'");
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Friends Invited</div>
                    <div class="db_fl2">
                      <?php
					echo getSingleColumn('tot',"select count(*) as tot from `member_referals` where `ref_member_id`='$member_id'");
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Events Rated</div>
                    <div class="db_fl2">
                      <?php
						echo eventsRated($member_id);
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Events Posted</div>
                    <div class="db_fl2">
                      <?php
						echo getSingleColumn('tot',"select count(*) as tot from events where `userid`='$member_id'");
					?>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Calendar Status</div>
                    <div class="db_fl2">Synced</div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow" style="border:none">
                    <div class="ev_fltlft" align="right" style="width:56%">Profile Status</div>
                    <div class="db_fl2">
                      <?php
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
            <?php
			
			$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
			$fildes = 'name,lname,email,username,password,image_name,zip,gender,dob';
			$filde = explode(",", $fildes);
			$total_flds = count($filde);
			$profile_completeness = 0;
			foreach($filde as $colum){
				$rec = getSingleColumn($colum,"select * from `users` where id=$member_id");
				if($rec!='')
					$profile_completeness++;
			}
			
			$profile_completeness	= ($profile_completeness*100)/$total_flds;
			$completeness = number_format($profile_completeness,2);
			
			?>
            <div class="db_box">
				<div class="head">
					<a href="#"><img src="<?php echo IMAGE_PATH; ?>get_started.png" align="right" /></a>SET UP YOUR ACCOUNT
					
				</div>
              <div style="font-size: 14px;padding: 3px 11px 0;"> <strong class="ev_fltlft">Completeness</strong> <strong class="ev_fltrght"><?php echo $completeness; ?>%</strong>
                <div class="clr"></div>
                <div style="margin-top: 3px;">
                  <div style="  background:url(images/prgBar.gif) no-repeat; height:18px; width:<?php echo $completeness; ?>%"></div>
                </div>
              </div>
              <div style="width:100%; margin:auto; font-size: 15px; font-weight: bold; padding:1px 0 4px 0">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td width="45" align="center" height="53" style="border-bottom:#CBCBCB solid 1px;">
					<img src="<?php echo IMAGE_PATH; if($profile_completeness > 30) { echo 'gd.gif'; } else{ echo 'crs.gif';}?>" /> </td>
                    <td style="border-bottom:#CBCBCB solid 1px;">1. Complete Your Profile <span>(<?php echo number_format($profile_completeness,2); ?>% complete)</span></td>
                  </tr>
                  <tr>
                    <td height="43" align="center" style="border-bottom:#CBCBCB solid 1px;"><img src="<?php echo IMAGE_PATH; ?>crs.gif" /> </td>
                    <td style="border-bottom:#CBCBCB solid 1px;">2. Sync Your Calendar <span>(0% complete)</span></td>
                  </tr>
                  <tr>
                    <td height="43" align="center" style="border-bottom:#CBCBCB solid 1px;"><img src="<?php echo IMAGE_PATH; ?>crs.gif" /> </td>
                    <td style="border-bottom:#CBCBCB solid 1px;">3. Setup Notifications <span>(0% complete)</span></td>
                  </tr>
                  <tr>
                    <td height="43" align="center" style="border-bottom:#CBCBCB solid 1px;"><img src="<?php echo IMAGE_PATH; ?>crs.gif" /> </td>

                    <td style="border-bottom:#CBCBCB solid 1px;">4. Invite Your Friends <span>(0% complete)</span></td>
                  </tr>
                  <tr>
                    <td height="48" align="center"><img src="<?php echo IMAGE_PATH; ?>crs.gif" /> </td>
                    <td >5. Network With Us <span>(0% complete)</span></td>
                  </tr>
                </table>
              </div>
            </div>
			<!-- /db_box -->
			</div>
				
			<div class="head_new">EVENTGRABBER LEARNING LIBRARY</div>
			
			<div class="videos_boxs">
				<div class="vBox">
					<span>GETTING STARTED 1</span>
					<div>
						<img src="http://i2.ytimg.com/vi/eSYJsgIM6Cs/hqdefault.jpg" width="186" class="video" height="104">
					</div>
				</div>
				
				<div class="vBox">
					<span>GETTING STARTED 2</span>
					<div>
						<img src="http://i2.ytimg.com/vi/eSYJsgIM6Cs/hqdefault.jpg" width="186" class="video" height="104">
					</div>
				</div>
				
				<div class="vBox">
					<span>GETTING STARTED 3</span>
					<div>
						<img src="http://i2.ytimg.com/vi/eSYJsgIM6Cs/hqdefault.jpg" width="186" class="video" height="104">
					</div>
				</div>
				
				<div class="vBox last">
					<span>GETTING STARTED 4</span>
					<div>
						<img src="http://i2.ytimg.com/vi/eSYJsgIM6Cs/hqdefault.jpg" width="186" class="video" height="104">
					</div>
				</div>
				<br class="clear" />
			</div>
			<!-- /videos_boxs -->
			<br class="clear" />
			<div align="center" id="videoArea">
				<span></span>
				<img src="<?php echo IMAGE_PATH; ?>delete.png" align="right" class="closeVd" title="Close" />
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>