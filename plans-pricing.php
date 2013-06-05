<?php 

require_once('admin/database.php');
require_once('site_functions.php');

if(isset($_POST['send_mail'])){
	
	$name	= $_POST['name'];
	$email	= $_POST['email'];
	$msg	= $_POST['msg'];
	
	$to			= "info@eventgrabber.com";
	$contents= "<strong>Name:</strong> ".$name."<br><br><strong>Email:</strong> ".$email."<br><br><strong>Message:</strong><br>".$msg;
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: "EventGrabber" <info@eventgrabber.com>' . "\r\n";
	$subject = "EventGrabber - Pricing page inquiry";
					
	$mail = mail($to,$subject,$contents,$headers);
	if($mail)
		echo "<script>window.location.href='plans-pricing.php?sent=2#mail';</script>";
	else
		echo "<script>window.location.href='plans-pricing.php?sent=1#mail';</script>";

}


// your email has been sent




$meta_title	= 'Plans Pricing';
include_once('includes/header.php');
?>

<style>
.search{
	display:none;
	}
	
.bc_label
{
	color: #000000;
    font-size: 13px;
    font-weight: normal;
    width: 180px;
	padding:11px 7px 11px 0;
}

.bc_input_td input
{
	border: 1px solid #CCCCCC;
    border-radius: 3px 3px 3px 3px;
    height: 20px;
    padding-left: 2px;
	width:300px;
}
</style>

<script>
function check(){

	var name	= $('#name').val();
	var email	= $('#email').val();
	var msg		= $('#msg').val();
	
	if(name == ''){
		alert('Please enter Name');
		$('#name').focus();
		return false;
		}

	if(email == ''){
		alert('Please enter Email');
		$('#email').focus();
		return false;
		}

	var str = email;
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		testresults=true;
		else{
		alert("Please input a valid email address!");
		$('#email').focus();
		return false;
	}
		
		
	if(msg == ''){
		alert('Please enter Message');
		$('#msg').focus();
		return false;
		}
	
}
</script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.tipsy.js"></script>
<link href="<?php echo ABSOLUTE_PATH; ?>plans-pricing.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Plans & Pricing</div>
    <div class="clr"></div>
    <div class="gredBox">
		<div align="center" class="p_planc">Our plans fit any need, from small to large events</div>
    	<div class="p_features">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="bld">Features</td>
				</tr>
				<tr>
					<td>
					<div id="info1" class="info" title="Event Categories, Age Requiremen, t Music Genre(s)"></div>
					Event Information</td>
				</tr>
				<tr>
					<td class="dark">Event Image</td>
				</tr>
				<tr>
					<td><div id="info1" class="info" title="Event Categories, Age Requiremen, t Music Genre(s)"></div>
					Event Attributes
				<tr>
					<td class="dark">Standard Listing on Eventgrabber</td>
				</tr>
				<tr>
					<td>High Resolution Main Event Image</td>
				</tr>
				<tr>
					<td class="dark">Embed High Definition Video</td>
				</tr>
				<tr>
					<td>Additional High Resolution Photo Galleries</td>
				</tr>
				<tr>
					<td class="dark">Embedded Geo-Location Services</td>
				</tr>
				<tr>
					<td>Additional Location Images</td>
				</tr>
				<tr>
					<td class="dark">Featured Listing on Eventgrabber</td>
				</tr>
				<tr>
					<td>Target Customers By Occupation</td>
				</tr>
				<tr>
					<td class="dark">Embedded Group Sharing</td>
				</tr>
				<tr>
					<td>Automatic Integration to your Facebook Fanpage</td>
				</tr>
				<tr>
					<td class="dark">Offer Deals or Specials For Your Event</td>
				</tr>
				<tr>
					<td>Social Media Analytics</td>
				</tr>
				<tr>
					<td class="dark">Sell Tickets For Your Event</td>
				</tr>
				<tr>
					<td>Target Customers By Location</td>
				</tr>
				<tr>
					<td class="dark">White Label (Your company brand, not ours)</td>
				</tr>
				<tr>
					<td>Custom fields to fit your need.</td>
				</tr>
				<tr>
					<td class="dark">&nbsp;</td>
				</tr>
			</table>
		</div> <!-- end p_features-->
		<div class="p_right">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" valign="top" style="border-bottom:#dedede solid 1px;">
						<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/basic.gif"><br><br>
						<a href="<?php echo ABSOLUTE_PATH; ?>create_event.php?type=simple"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif" style="padding-bottom:20px;"></a>
					</td>
					<td  align="center" valign="top" style="border-bottom:#dedede solid 1px;">
						<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/featured.gif"><br><br>
						<a href="<?php echo ABSOLUTE_PATH; ?>create_event.php?type=flyer"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif" style="padding-bottom:20px;"></a>
					</td>
					<td  align="center" valign="top" style="border-bottom:#dedede solid 1px;">
						<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/premium.gif"><br><br>
						<a href="javascript:voild(0)"  onclick="alert('This feature is coming soon');" ><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif" style="padding-bottom:20px;"></a>
					</td>
					<td align="center" valign="top" style="border-bottom:#dedede solid 1px;">
						<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/custom.gif"><br><br>
						<a href="javascript:voild(0)"  onclick="alert('This feature is coming soon');" ><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif" style="padding-bottom:20px;"></a>
					</td>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="46"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif"></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif"></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif"></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="65"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif"></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="63"><?php
						if($i!=0){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><?php
						if($i!=0){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="65"><?php
						if($i!=0){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="63"><?php
						if($i!=0){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><?php
						if($i!=0){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="63"><?php
						if($i!=0){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="64"><?php
						if($i!=0 && $i!=1){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><?php
						if($i!=0 && $i!=1){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="64"><?php
						if($i==3){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="64"><?php
						if($i!=0 && $i!=1){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><?php
						if($i==3){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="48"><?php
						if($i!=0 && $i!=1){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="64"><?php
						if($i==3){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="64"><?php
						if($i==3){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr class="f7">
					<?php
					for($i=0;$i<4; $i++){?>
						<td height="64"><?php
						if($i==3){?>
							<img src="<?php echo ABSOLUTE_PATH; ?>prices_images/yes.gif">
						<?php } ?></td>
					<?php } ?>
				</tr>
				<tr>
					<td height="49">
					<a href="<?php echo ABSOLUTE_PATH; ?>create_event.php?type=simple"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif"></a></td>
					<td><a href="<?php echo ABSOLUTE_PATH; ?>create_event.php?type=flyer"><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif"></a></td>
					<td><a href="javascript:voild(0)"  onclick="alert('This feature is coming soon');" ><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif"></a></td>
					<td><a href="javascript:voild(0)"  onclick="alert('This feature is coming soon');" ><img src="<?php echo ABSOLUTE_PATH; ?>prices_images/start_campaign.gif"></a></td>
				</tr>
			</table>
		</div> <!-- end p_right -->
		<div style="height:20px; clear:both">&nbsp;</div>
		
		 <div class="whiteTop">
        	<div class="whiteBottom">
				<div class="whiteMiddle" style="padding-top:1px;">
					<div class="rightBorder">
						<div class="head">Frequently Asked Questions</div>
						<div class="desc">
							
							<div class="tit">Lorem ipsum dolor sit amet, consectetur adipisicing elit,</div>
							<div>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
							<br>
							<div class="tit">Lorem ipsum dolor sit amet, consectetur adipisicing elit,</div>
							<div>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
						
						
						</div> <!-- end desc -->
					
					</div> <!-- end rightBorder -->
					
					<div class="p_mail"><a id="mail"></a>
						<div class="head">Didn't find an answer?</div>
						<div class="ask">Ask Us Your Question</div>
						<?php
						if($_GET['sent']){
							if($_GET['sent']==2)
								echo "<br><br><span style='color:#ff0000'>Your email has been sent</span><br>";
							elseif($_GET['sent']==1)
								echo "<br><br><span style='color:#ff0000'><strong>Error:</strong> Try again later</span><br>";
							else
								echo "<br><br><br>";
								
						}
						?>
						<form method="post" onSubmit="return check();">
						<table cellpadding="0" cellspacing="0" width="406px">
							<tr>
								<td width="24%" height="50"><strong>Name</strong></td>
							  <td width="76%"><input type="text" class="p_input" name="name" id="name"></td>
							</tr>
							<tr>
								<td height="50"><strong>Email</strong></td>
								<td><input type="text" class="p_input" name="email" id="email"></td>
							</tr>
							<tr>
								<td valign="top" style="padding-top:10px;"><strong>Message</strong></td>
								<td><textarea name="msg" id="msg"></textarea></td>
							</tr>
							<tr>
								<td colspan="2" align="right"><br>
									<input type="image" src="<?php echo ABSOLUTE_PATH; ?>prices_images/send_mail.gif" name="send_mail" value="Send Mail">
									<input type="hidden" name="send_mail" value="Send Mail">
								</td>
							</tr>
						</table>
					</form>
					</div>
					<div class="clr"></div>
					
				</div> <!-- end whiteMiddle -->
			</div> <!-- end whiteBottom -->
		</div> <!-- end whiteTop -->
		
		</div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>
<script>
 $(function() {
    $('.info').tipsy({gravity: 'w', fade: true});
  });
 </script>