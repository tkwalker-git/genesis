<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

if(!$_GET['id'] || !is_numeric($_GET['id']))
	echo "<script>window.location.href='index.php';</script>";
		
$event_id = $_GET['id'];

$package_price	= 79;
$per_day_rate	= 2.99;
$month_total	= $per_day_rate * 30;

$package_total	= $package_price + $month_total;

$userid			=	getSingleColumn('userid',"select * from `events` where `id`='$event_id'");
$event_type		=	getSingleColumn('event_type',"select * from `events` where `id`='$event_id'");
$event_status	=	getSingleColumn('event_status',"select * from `events` where `id`='$event_id'");

$pym = attribValue("orders","id"," where main_ticket_id='". $event_id ."'");

if ( $event_type != '1'  ) {
	$sucMessage = '<strong>This is not a paid event.</strong>';
	$suc = 1;
}

if ( $pym > 0 ) {
	$sucMessage = '<strong>You have already made the payment of this event.</strong>';
	$suc = 1;
}	
		
if (isset($_POST["submit"]) || isset($_POST["submit"]) ) {	

	$bc_name			=	$_POST["name"];
	$bc_card_type		=	$_POST['cardType'];
	$bc_month			=	$_POST['month'];
	$bc_year			=	$_POST['year'];
	$bc_card_number		=	$_POST['number'];
	$bc_securityCode	=	$_POST['securityCode'];
	
	
	if ( trim($bc_name) == '' )
		$errors[] = 'Name on Card is required.';
	if ( trim($bc_card_type) == '' )
		$errors[] = 'Credit Card Type is required.';
		
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
		
		$customer_details['first_name'] 		=	$bc_firstname;
		$customer_details['last_name'] 			=	$bc_lastname;
		$customer_details['address'] 			=	$b_address;
		$customer_details['zip'] 				=	$bc_zip_code;
		$customer_details['city'] 				=	$b_city;
		$customer_details['state'] 				=	$b_state;
		$customer_details['country'] 			=	$b_country;
		
		$customer_details['card_number'] 		=	$bc_card_number;
		$customer_details['card_code'] 			=	$bc_securityCode;
		$customer_details['card_expiry_month'] 	=	$_POST["month"];
		$customer_details['card_expiry_year'] 	=	$_POST["year"];

		$customer_details['totalAmount']		=	$package_total;
			
		include_once("includes/authorize.net.php");
		$response = authorize_process($customer_details);	
		list($processed, $response_arr) = authorize_isProcessed($response);
		
		if (!$processed) {

			$sucMessage = '<table border="0" width="90%"><tr><td class="error" ><ul><li>' . $response_arr[3] . '</li></ul></td></tr></table>';
	
		} else {
		
			$date		=	date('Y-m-d');
			$res = mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`, `main_ticket_id`) VALUES (NULL, '$user_id', '99', '$date', 'flyer', '$event_id')");
			$order_id	=	mysql_insert_id();
			if($res){
			
					$length		= strlen($bc_card_number);
					$characters	= 4;
					$start		= $length - $characters;
					$card_number	= substr($bc_card_number , $start ,$characters);
					$card_number	= "XXXX".$card_number;
		
		
				mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`) VALUES (NULL, '$order_id', '$bc_name', '$bc_card_type', '$bc_month', '$bc_year', '$card_number', '$bc_securityCode')");
				mysql_query("UPDATE `events` SET `event_status` = '1' WHERE `id` = '$event_id'");
				$sucMessage = '<strong>Congratulations! Your flyer is now published.</strong>';
				$suc = 1;
			} else {
				$sucMessage = "Error: Please try Later";
			}
		}
	
	}
	else{
		$sucMessage = $err;
	}
		
		}
		

 require_once('includes/header.php');
 ?>

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
          <div class="whiteMiddle"> <span class="title ev_fltlft">Cart Information</span> <a href="<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=" class="fancybox2"><span class="preview_glass ev_fltlft">Preview</span></a>
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
                      <td width="16%"><span class="txt">Integrate to Facebook</span> <img src="<?php echo IMAGE_PATH; ?>yes.png"></td>
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
                      <td>Showcase Creator Container Setup Fee</td>
                      <td align="center">$79.00</td>
                    </tr>
                    <tr bgcolor="#d1e5c0">
                      <td>Social Media Integration Fee (@$2.99/day for 30 days)</td>
                      <td align="center">$89.70</td>
                    </tr>
                    <tr bgcolor="#e4f0d8">
                      <td class="maintitl">Package Total </td>
                      <td class="red" align="center"><b style="font-size:14px">$168.70</b></td>
                    </tr>
                  </table></td>
              </tr>
            </table>
            <div class="borderDoted"></div>
            <br>
            <div class="title"> Payment Information</div>
            <div class="clr"></div>
            <div class="pymnt_details"><br>
              <br>
			  <?php
			  echo $sucMessage;
			//  $suc=0;
			  if ( $suc != 1 ){ ?>
			  	<form method="post" name="bc_form" enctype="multipart/form-data" action=""> 
              <div class="evField">Name on Card:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
              <div class="evField">Credit Card Type:<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <select style="width:150px" name="cardType">
                  <option value="">--Select--</option>
                  <option value="visa" <?php if($bc_card_type=="visa"){ echo 'selected="selected"'; }?>>Visa</option>
                  <option value="mastercard"<?php if($bc_card_type=="mastercard"){ echo 'selected="selected"'; }?>>MasterCard</option>
                  <option value="AMEX"<?php if($bc_card_type=="AMEX"){ echo 'selected="selected"'; }?>>AMEX</option>
                  <option value="jcb"<?php if($bc_card_type=="jcb"){ echo 'selected="selected"'; }?>>JCB</option>
                </select>
              </div>
              <div class="clr"></div>
              <div class="evField">Expiration date (Month/Year):<font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <select name="month" style="width:75px" id="dalete1">
                  <option value="">Month</option>
                  <?php
	  for($i=1;$i<13;$i++){
		?>
                  <option value="<?php echo $i; ?>" <?php if($bc_month==$i){ echo 'selected="selected"'; }?>><?php echo str_pad($i,2,0,STR_PAD_LEFT); ?></option>
                  <?php } ?>
                </select>
                &nbsp;
                <select name="year" style="width:85px" id="delete2">
                  <option value="">Year</option>
                  <?php
					for($i=date('Y');$i<date('Y')+10;$i++){
						?>
                  <option value="<?php echo $i; ?>" <?php if($bc_year==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="clr"></div>
              <div class="evField">Credit Card Number: <font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="number" value="<?php echo $bc_card_number; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
              <div class="evField">Security Code (CVV): <font color="#FF0000">*</font></div>
              <div class="evLabal" style="width:320px">
                <input type="text" maxlength="100" id="eventname"  name="securityCode" value="<?php echo $bc_securityCode; ?>" style="width:300px" />
              </div>
              <div class="clr"></div>
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td width="33%"><div class="AuthorizeNetSeal" style="padding:3px 0;">
                      <script>
		var ANS_customer_id="66c2e366-3b5b-46ac-8044-75be4c0c6909";
	</script>
                      <script type="text/javascript" language="javascript" src="http://verify.authorize.net/anetseal/seal.js" ></script>
                    </div></td>
                  <td width="33%" align="center"><input type="image" src="<?php echo IMAGE_PATH; ?>submit_btn.gif" name="submit" value="Submit">
                    <input type="hidden" name="submit" value="Submit"></td>
                  <td width="33%"><img src="<?php echo IMAGE_PATH; ?>cc_cards.png" /></td>
                </tr>
              </table>
			  </form>
			  <?php }
			  else { 
					$event_url = 'flyer_preview.php?id=' . $event_id;
				?>
				<br />
				<br />
				View your event <a href="<?php echo $event_url;?>" >Here</a>.
				<?php } ?>
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
            </div>
            <div class="clr"></div>
            <div class="borderDoted"></div>
            <br>
            <div class="title"> Time To Let The World Know</div>
            <br>
            <br>
            <br>
            <table width="85%" border="0" cellspacing="0" cellpadding="10" align="center" class="timeToLet">
              <tr class="titleRow">
                <td>CAMPAIGN CHANNELS</td>
                <td>ACTIONS</td>
                <td>STATUS</td>
              </tr>
              <tr bgcolor="#ecf7e2">
                <td><img src="<?php echo IMAGE_PATH; ?>fb2.png" align="left"><span>Facebook Fan Page</span><br />
                  Integrates into your Facebook Fan Page</td>
                <td class="red">Publish on My Facebook Fan Page</td>
                <td><img src="<?php echo IMAGE_PATH; ?>no.png" align="left" /> Unpublished</td>
              </tr>
              <tr bgcolor="#f9f9f9">
                <td><img src="<?php echo IMAGE_PATH; ?>twit2.png" align="left"><span>Twitter Page</span><br />
                  Integrates into your Twitter Page</td>
                <td class="red">Publish on My Twitter Page</td>
                <td><img src="<?php echo IMAGE_PATH; ?>no.png" align="left" /> Unpublished</td>
              </tr>
              <tr bgcolor="#ecf7e2">
                <td><img src="<?php echo IMAGE_PATH; ?>web2.png" align="left"><span>Website Button</span><br />
                  Integrates into your website as a thumbnail</td>
                <td class="red">Add to my website as a thumbnail</td>
                <td><img src="<?php echo IMAGE_PATH; ?>no.png" align="left" /> Unpublished</td>
              </tr>
              <tr bgcolor="#f9f9f9">
                <td><img src="<?php echo IMAGE_PATH; ?>micro2.png" align="left"><span>Microsite</span><br />
                  Integrates into your website as a microsite</td>
                <td class="red">Publish as mini page on my website</td>
                <td><img src="<?php echo IMAGE_PATH; ?>no.png" align="left" /> Unpublished</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php require_once('includes/footer.php');?>
