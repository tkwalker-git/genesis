<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$active='buy';
	
	$event_id		= $_SESSION['orderMember']['event_id'];
	
	
	
	$u = getEventURL($event_id);
    $event_name	=	getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");
    $fbu = 'http://www.facebook.com/sharer.php?u=' . urlencode($u) . '&t=' . urlencode($event_name);
    $twu = 'http://twitter.com/intent/tweet?url='. urlencode($u). '&via='. urlencode('EventGrabber').'&text='. urlencode($event_name);
	
	
	
	include("../flayerMenuFB.php");
	$_SESSION['orderMember']='';
	$_SESSION['ticketOrder']='';
	?>

<div class="inrDiv">  <br />
        <div class="progresbar5"></div>
      
        <br />
        <div class="new_flayer_title">Share this event with friends</div>
        <div class="new_flayer_share_box_bg">
          <table cellpadding="0" cellspacing="0" width="96%" align="center" style="">
            <tr>
              <td height="94" valign="top"><a href="javascript:void(0)" onclick="windowOpener(500,500,'Modal Window','<?php echo $fbu;?>')"><img src="<?php echo IMAGE_PATH; ?>post_on_fb.png" /></a></td>
              <td align="right" valign="top"><a href="javascript:void(0)" onclick="windowOpener(500,500,'Modal Window','<?php echo $twu;?>')"><img src="<?php echo IMAGE_PATH; ?>post_on_twit.png" /></a></td>
            </tr>
            <tr>
              <td height="" valign="top"><a href="javascript:void(0)" onclick="windowOpener(520,540,'Modal Window','<?php echo ABSOLUTE_PATH;?>send_mail_flyer.php?id=<?php echo $event_id; ?>')"><img src="<?php echo IMAGE_PATH; ?>send_mail.png" /></a></td>
              <td align="right" valign="top"><a href="<?php echo ABSOLUTE_PATH;?>signup.php" target="_blank"><img src="<?php echo IMAGE_PATH; ?>eventgrabber_signup.png" /></a></td>
            </tr>
          </table>
        </div>
        
</div>