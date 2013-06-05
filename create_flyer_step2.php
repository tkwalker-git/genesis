<?php

require_once('admin/database.php');
include_once('site_functions.php');


// $_SESSION['discount_amout']=''; $_SESSION['discount_code']='';


if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

if(!$_GET['id'] || !is_numeric($_GET['id']))
	echo "<script>window.location.href='index.php';</script>";


function deics($ccode,$amount){
	if(calculateDiscount('create',$ccode,$amount)!=0){
		$_SESSION['discount_code']	= $ccode;
		$_SESSION['discount_amout']	= calculateDiscount('create',$ccode,$amount);
	}
}


$disc	=  getSingleColumn("percent_value","select * from `coupons` where `code`='".$_SESSION['discount_code']."'");

	if($disc == 100)
		$fullDisc	= 1;
	else
		$fullDisc	= 0;
		
$event_id	= $_GET['id'];
$er			= 0;

$start_campaign		=	getSingleColumn('start_campaign',"select * from `events` where `id`='$event_id'");
$end_campaign		=	getSingleColumn('end_campaign',"select * from `events` where `id`='$event_id'");
$bc_event_type		=	getSingleColumn('event_type',"select * from `events` where `id`='$event_id'");

if($start_campaign=='' || $start_campaign=='0000-00-00'){
	$start_campaign		= date('Y-m-d');
	mysql_query("UPDATE `events` SET `start_campaign` = '$start_campaign' WHERE `id` = '$event_id'");
	}

if($end_campaign=='' || $end_campaign=='0000-00-00'){
	$end_campaign		= getSingleColumn("event_date","select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` DESC LIMIT 0,1");
	mysql_query("UPDATE `events` SET `end_campaign` = '$end_campaign' WHERE `id` = '$event_id'");
	}


$days = (strtotime($end_campaign) - strtotime($start_campaign)) / 86400;

$userid			=	getSingleColumn('userid',"select * from `events` where `id`='$event_id'");
$event_type		=	getSingleColumn('event_type',"select * from `events` where `id`='$event_id'");
$event_status	=	getSingleColumn('event_status',"select * from `events` where `id`='$event_id'");

if ( $event_type == 1 ){
	$package_price	= 14.95;
}
elseif ( $event_type == 2 ){
	$package_price	= 49.95;
}
elseif ( $event_type == 3 ){
	$package_price	= 99.95;
}
else
	echo "<script>window.location.href='index.php';</script>";
	



$package_t	= number_format($package_price,2);

if($_POST['discount']){
	deics($_POST['discount_code'],$package_t);		
}
else{
	deics($_SESSION['discount_code'],$package_t);
	}

if($_SESSION['discount_amout'])
	$package_total	= number_format(($package_t - $_SESSION['discount_amout']),2);
else
	$package_total	= $package_t;

$net_total	= $package_t;

$pym = attribValue("orders","total_price"," where main_ticket_id='". $event_id ."'");

if ( $event_type != '1' && $event_type != '2' && $event_type != '3'  ) {
	$sucMessage = '<strong class="title" style="font-size:23px">This is not a paid event.</strong>';
	$suc = 1;
	$er = 1;
}

if ( $pym > 0 ) {
	$sucMessage = '<strong class="title" style="font-size:23px">You have already made the payment of this event.</strong>';
	$alrdy = 1;
	$suc = 1;
}	
		
if (isset($_POST["submit"]) || isset($_POST["submit"]) ) {	

	$bc_fname		=	$_POST['fname'];
	$bc_lname		=	$_POST['lname'];
	$bc_address1	=	$_POST['address1'];
	$bc_address2	=	$_POST['address2'];
	$bc_city		=	$_POST['city'];
	$bc_zip			=	$_POST['zip'];
	$bc_country		=	$_POST['country'];
			
	$bc_name			=	$_POST["name"];
	$bc_card_type		=	$_POST['cardType'];
	$bc_month			=	$_POST['month'];
	$bc_year			=	$_POST['year'];
	$bc_card_number		=	$_POST['number'];
	$bc_securityCode	=	$_POST['securityCode'];
	
	
	if ( trim($bc_fname) == '' )
		$errors[] = 'First Name is required.';
	if ( trim($bc_lname) == '' )
		$errors[] = 'Last Name is required.';
	if ( trim($bc_address1) == '' )
		$errors[] = 'Address Line 1 is required.';
	if ( trim($bc_city) == '' )
		$errors[] = 'City is required.';
	if ( trim($bc_zip) == '' )
		$errors[] = 'Postal Code is required.';
//	if ( trim($bc_name) == '' )
//		$errors[] = 'Name on Card is required.';
//	if ( trim($bc_card_type) == '' )
//		$errors[] = 'Credit Card Type is required.';

		
	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	
		$res = mysql_query("select * from `users` where `id`='$user_id'");
		while($row = mysql_fetch_array($res)){
			$bc_firstname	=	$row['firstname'];
			$bc_lastname	=	$row['lastname'];
			$bc_zip_code	=	$row['zip'];
		}
		$custom_order_id = time();
		$date		=	date('Y-m-d');
		$res = mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `discount`, `net_total`, `date`, `type`, `main_ticket_id`, `coupon_code`, `order_id`) VALUES (NULL, '$user_id', '0', '".$_SESSION['discount_amout']."', '$net_total', '$date', 'flyer', '$event_id', '".$_SESSION['discount_code']."', '$custom_order_id')");
		$order_id	=	mysql_insert_id();
		if($res){
			mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`, `f_name`, `l_name`, `address1`, `address2`, `city`, `zip`, `country`) VALUES (NULL, '$order_id', '$bc_name', '$bc_card_type', '$bc_month', '$bc_year', '$card_number', '$bc_securityCode', '$bc_fname' , '$bc_lname' , '$bc_address1' , '$bc_address2' , '$bc_city' , '$bc_zip' , '$bc_country')");
			$_SESSION['discount_amout']	= '';
			$_SESSION['discount_code']	= '';
			$event_url = getEventURL($event_id);
			
			
				if($fullDisc == 1){
					$orderRes	= mysql_query("update `orders` set `total_price`='0.01' where `id`='$order_id'");
					if($orderRes){
						mysql_query("update `events` set `event_status`='1' where `id`='$event_id'");

						$event_url	= getEventURL($event_id);
						
						header("Location: ".$event_url);						
					
					} // end if($$orderRes)
				
				}
				else{
					?>
				
				<form name="buyAd" action="<?php echo PAYPAL_URL;?>" method="post">
					<input type="hidden" name="cmd" value="_xclick" />
					<input type="hidden" name="business" value="<?=BUSINESS_EMAIL?>" />
					<input type="hidden" name="item_name" value="Create Event" />
					<input type="hidden" name="item_number" value="1" />
					<input type="hidden" name="amount" value="<?=$package_total?>" />
					<input type="hidden" name="currency_code" value="USD" />
					<input type="hidden" name="shipping" value="0.00" />
					<input type="hidden" name="custom" value="CF-<?php echo $custom_order_id?>" />
					<input type="hidden" name="shipping2" value="0.00" />
					<input type="hidden" name="handling" value="0.00" />
					<input type="hidden" name="undefined_quantity" value="0" />
					<input type="hidden" name="receiver_email" value="<?=BUSINESS_EMAIL?>" />
					<input type="hidden" name="no_shipping" value="1" />
					<input type="hidden" name="no_note" value="0" />
					<input type="hidden" name="notify_url" value="<?=IPN_URL?>">
					<input type="hidden" name="return" value="<?php echo $event_url; ?>">
					<input type="hidden" name="cancel_return" value="http://www.eventgrabber.com">
					<input  type="submit" value="Click here if you are not redirected within 5 seconds" />
				</form>
				<script language="javascript">
					setTimeout("SubForm()", 0); 
	
					function SubForm() {
						document.buyAd.submit();
					}
				</script>
				
				<?php
				} // end else	
			}
			else{
				$sucMessage = "<span style='color:#ff0000'><strong>Error:</strong> Please try Later</span><br />&nbsp;";
			}
	
	}
	else{
		$sucMessage = $err;
		}
		}

require_once('includes/header.php');

if(($days==0 || $_SESSION['campaignErr']!='') && $campaignAdded!=1){ ?>
<script>
//	showPage('/','campaign.php','<?php echo $event_id; ?>');
</script>
<?php } ?>
<input type="hidden" id="campaignss" name="campaign" />
<div class="topContainer">

  <div class="welcomeBox"></div>
  <link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
  <script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
  <script language="javascript">
	
	function submitform(){	
			
	document.forms["searchfrmdate"].submit();

	}
	
	$(document).ready(function() {
		$(".fancybox2").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'width'			: 540,
			'height'		: 700,
			'type'			: 'iframe'
		});
	});
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle">
		  <?php
		 if ( $alrdy==1 ){
		   echo $sucMessage;
		  	$event_url = 'http://www.eventgrabber.com/flyer_preview.php?id=' . $event_id;
				?>
				<div class="clr"></div>
				<?php
				if($er!=1){?>
				<span class="title" style="font-size:15px; font-weight:bold; color:#000000">View your event <a style="text-decoration:underline;" href="<?php echo $event_url;?>" >Here</a>.</span>
				<?php } 
		  }
		  else{
		  ?>
		   <span class="title ev_fltlft">Cart Information</span> <a href="<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $event_id; ?>" class="fancybox2"><span class="preview_glass ev_fltlft">Preview</span></a> 
		   <a href="create_event.php?r=py&id=<?php echo $event_id; ?>" ><span class="preview_glass ev_fltlft" style="background:none; padding-left:0; margin-left:25px;">Edit Event</span></a> 
            <div class="clear"></div>
            <table cellpadding="0" cellspacing="0" width="100%" class="flerStp2">
              <tr bgcolor="#e4f0d8">
                <td><table width="100%" border="0" cellspacing="15" cellpadding="0" class="first">
                    <tr>
                      <td width="16%"><strong>Key Features</strong>
                        <select>
                          <option value="featured">Featured</option>
                        </select>
                        <br>
                        <a href="#" class="red_link">See All Features</a></td>
                      <td width="15%"><span class="txt">Embed Video</span> <img src="<?php echo IMAGE_PATH; ?>yes.png"></td>
                      <td width="16%"><span class="txt">Integrate to Facebook</span> 
					  	<?php
						if($bc_event_type==3)
							$bfAccess = 'yes';
						else
							$bfAccess = 'no';
						?>
					  <img src="<?php echo IMAGE_PATH.$bfAccess; ?>.png"></td>
                      <td width="15%"><span class="txt">Sell Tickets</span> <img src="<?php echo IMAGE_PATH; ?>yes.png"></td>
                      <td width="12%"><span class="txt">Social Media Analytics</span> <img src="<?php echo IMAGE_PATH; ?>yes.png"></td>
                      <td width="14%"><span class="txt">Integrate to Twitter</span><img src="<?php echo IMAGE_PATH; ?>no.png"></td>
                      <td width="12%"><span class="txt">Integrate to Your Site</span><img src="<?php echo IMAGE_PATH; ?>no.png"></td>
                    </tr>
                  </table></td>
              </tr>
              <tr>
                <td valign="top"><table cellpadding="15" cellspacing="0" width="100%" class="scnd">
                    <tr bgcolor="#d1e5c0">
                      <td width="85%" class="maintitl">CART DETAILS</td>
                      <td width="15%" class="maintitl" align="center">PRICE</td>
                    </tr>
                    <tr bgcolor="#e4f0d8">
                      <td style="font-size:15px;">Showcase Creator Container Setup Fee</td>
                      <td align="center" class="maintitl">$<?php echo number_format($package_price,2); ?></td>
                    </tr>
                  
					<?php
			  if($_SESSION['discount_amout']!=''){?>
				<tr bgcolor="#d1e5c0">
				<td style="font-size:15px;">Discount</td>
				<td class="maintitl" align="center" >
					<?php echo "$".number_format($_SESSION['discount_amout'],2);?>
				</td>
				</tr>
			<?php } ?>
                    <tr bgcolor="<?php if ($_SESSION['discount_amout']==''){ echo '#d1e5c0';}else{ echo '#e4f0d8';}?>">
                      <td class="maintitl">Package Total </td>
                      <td align="center" class="maintitl"><div class="red">$<?php echo $package_total; ?></div></td>
                    </tr>
                  </table>
				  </td>
              </tr>
			  <?php
			  if($_SESSION['discount_amout']==''){?>
				<tr bgcolor="<?php if ($_SESSION['discount_amout']==''){ echo '#e4f0d8';}else{ echo '#d1e5c0';}?>">
				<td colspan="2" align="right" style="padding:10px" >
					<form method="post">
						<strong class="red">Discount Code</strong>
						<input type="text" id="discount_code" name="discount_code" style="width: 104px;" />
						<input type="submit" name="discount" value="Apply" />
					</form>
				</td>
				</tr>
			<?php } ?>
            </table>
            <div class="borderDoted"></div>
            <br>
            
            <div class="clr"></div>
            <div class="pymnt_details"><br>
			  <?php
			  echo $sucMessage;
			//  $suc=0;
			  if ( $suc != 1 ){ ?>
			    	<form method="post" name="bc_form" enctype="multipart/form-data" action=""> 
			  <div class="title">Cardholder Information</div>
			  <div class="clr"></div>
			  
			  <div class="evField">First Name:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="fname" value="<?php echo $bc_fname; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
			  
			  <div class="evField">Last Name:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="lname" value="<?php echo $bc_lname; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
			  
			  <div class="evField">Address Line 1:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="address1" value="<?php echo $bc_address1; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
			  
			  <div class="evField">Address Line 2:</div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
			  
			  <div class="evField">City:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="city" value="<?php echo $bc_city; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
			  
			   <div class="evField">Postal Code:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="zip" value="<?php echo $bc_zip; ?>" style="width:70px" />
              </div>
              <div class="clr"></div>
			  
			   <div class="evField">Country:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
               <select style="padding:2px; width:300px" name="country" id="country">
			   	<?php
				if($b_country=='')
					$b_country = 'US';
					
					foreach($countries as $conIndex => $conValue){?>

                   <option <?php if ($b_country==$conIndex){ echo 'selected="selected"'; } ?> value="<?php echo $conIndex; ?>"><?php echo $conValue; ?></option>
				   <?php
				   }
				   ?>
                </select>
              </div>
              <div class="clr"></div>
			  
			  <div class="evField">&nbsp;</div>
              <div class="ev_fltlft" style="width:308px" >
			  <input type="image" src="<?php echo IMAGE_PATH; ?>paypal_checkout.png" name="submit" value="Submit">
                <input type="hidden" name="submit" value="Submit">
				<br><br>
				<img src="<?php echo IMAGE_PATH; ?>paypal_icons.gif" />
				
              </div>
              <div class="clr"></div>
			  
              </form>
			  <?php }?>
            </div>
            <div class="securtyText">You can shop at <strong>www.eventgrabber.com</strong> with confidence. We have partnered with <a href="http://www.authorize.net" target="_blank">Authorize.Net</a>, a leading payment gateway since 1996, to accept credit cards and electronic check payments safely and securely for our customers.<br />
              <br />
              The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks. See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="_blank">online payments diagram</a> to see how it works.<br />
              <br />
              The company adheres to strict industry standards for payment processing, including:
              <ul>
                <li>128-bit Secure Sockets Layer (SSL) technology for secure Internet Protocol (IP) transactions.</li>
                <li>Industry leading encryption hardware and software methods and security protocols to protect customer information.</li>
                <li>Compliance with the Payment Card Industry Data Security Standard (PCI DSS).</li>
              </ul>
              For additional information regarding the privacy of your sensitive cardholder data, please read the <a href="http://www.authorize.net/company/privacy/" target="_blank">Authorize.Net Privacy Policy</a>. <strong>www.eventgrabber.com</strong> is registered with the Authorize.Net Verified Merchant Seal program. <br>
              <br>
				<div class="AuthorizeNetSeal" style="padding:3px 0; margin:auto">
					<script>
						var ANS_customer_id="66c2e366-3b5b-46ac-8044-75be4c0c6909";
					</script>
					<script type="text/javascript" language="javascript" src="https://verify.authorize.net/anetseal/seal.js" ></script>
				</div>
				<div style="text-align:center"><img src="<?php echo IMAGE_PATH; ?>cc_cards.png" /></div>
            </div>
            <div class="clr"></div>
			<?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php require_once('includes/footer.php');?>