<?php 

require_once('admin/database.php');
require_once('site_functions.php');
include("nba_table_packages.php");


$id			= base64_decode($_GET['id']);
$table_id	= $id;

if(!is_numeric($table_id))
	die('Error: Broken link');
	
$is_table_reserve = attribValue('orders', 'id', "where `type`='table' && `total_price`!='' && `main_ticket_id`='$table_id'");


if ( $is_table_reserve > 0 && in_array($table_id, $reserved_tables))
	die('Table already reserved');
	

$price		= $table[$id]['spend'];
$tax		= $price*9/100;
$gratuity	= $price*18/100;
$surcharge	= $price*5/100;
$tickets	= $table[$id]['tickets'];

$total = $price+$tax+$gratuity+$surcharge;



if (isset($_POST["submit"])) {	

	$bc_fname			=	$_POST['fname'];
	$bc_lname			=	$_POST['lname'];
	$bc_email			=	$_POST['email'];
	$bc_address1		=	$_POST['address1'];
	$bc_address2		=	$_POST['address2'];
	$bc_city			=	$_POST['city'];
	$bc_zip				=	$_POST['zip'];
	$bc_country			=	$_POST['country'];
	$bc_name			=	$_POST["name"];
	$bc_card_type		=	$_POST['cardType'];
	$bc_month			=	$_POST['month'];
	$bc_year			=	$_POST['year'];
	$bc_card_number		=	$_POST['number'];
	$bc_securityCode	=	$_POST['securityCode'];
	$bc_t_buyer_name	=	$_POST['t_buyer_name'];
	$bc_t_buyer_email	=	$_POST['t_buyer_email'];
	
	
	if ( trim($bc_fname) == '' )
		$errors[] = 'First Name is required.';
	if ( trim($bc_lname) == '' )
		$errors[] = 'Last Name is required.';
	if(! validateEmail($bc_email) )
		$errors[] = 'Email is not valid.';
	if ( trim($bc_address1) == '' )
		$errors[] = 'Address Line 1 is required.';
	if ( trim($bc_city) == '' )
		$errors[] = 'City is required.';
	if ( trim($bc_zip) == '' )
		$errors[] = 'Postal Code is required.';
		
		
		/// ERR FOT TICKET BUYER START ///

	if ( trim($bc_t_buyer_name)=='')
		$errors[] = 'Full Name is required. (Ticket Information)';
	if(!validateEmail($bc_t_buyer_email))
		$errors[] = 'Email Address is invalid. (Ticket Information)';


	
	/// ERR FOT TICKET BUYER END ///
	
	
	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
		$date				= date('Y-m-d');
		$custom_order_id	= time();
		$event_id			= '21742';
	
		$main_ticket_id = getSingleColumn("id","select * from `event_ticket` where `event_id`='$event_id'");
		$ticket_id		= getSingleColumn("id","select * from `event_ticket_price` where `ticket_id`='$main_ticket_id'");

		
		$res = mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`, `main_ticket_id`, `coupon_code`, `order_id`) VALUES (NULL, '$user_id', '0', '$date', 'table', '$table_id', '$coupon_code', '$custom_order_id')");
			$order_id	=	mysql_insert_id();
			
			if($res){
			mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`, `f_name`, `l_name`, `address1`, `address2`, `city`, `zip`, `country`, `email`) VALUES (NULL, '$order_id', '$bc_name', '$bc_card_type', '$bc_month', '$bc_year', '$card_number', '$bc_securityCode', '$bc_fname' , '$bc_lname' , '$bc_address1' , '$bc_address2' , '$bc_city' , '$bc_zip' , '$bc_country','$bc_email')");
			for($i=0;$i<$tickets;$i++){
				$maxValue		= getMaxValue("order_tickets","ticket_number");
				$ticket_number	= $maxValue+1;
				mysql_query("INSERT INTO `order_tickets` (`id`, `ticket_id`, `price`, `quantity`, `date`, `order_id`, `t_time`, `name`, `email`, `ticket_number`) VALUES (NULL, '$ticket_id', '$price', '1', '2012-02-24', '$order_id', '22:00:00', '$bc_t_buyer_name', '$bc_t_buyer_email', '$ticket_number');");
				} // end for
			
			?>
			<form name="buyAd" action="<?php echo PAYPAL_URL; ?>" method="post">
				<input type="hidden" name="cmd" value="_xclick" />
				<input type="hidden" name="business" value="<?=BUSINESS_EMAIL?>" />
				<input type="hidden" name="item_name" value="Reserve Table (Table# <?php echo $table_id; ?>)" />
				<input type="hidden" name="item_number" value="1" />
				<input type="hidden" name="amount" value="<?=$total?>" />
				<input type="hidden" name="currency_code" value="USD" />
				<input type="hidden" name="shipping" value="0.00" />
				<input type="hidden" name="custom" value="BT-<?php echo $custom_order_id?>" />
				<input type="hidden" name="shipping2" value="0.00" />
				<input type="hidden" name="handling" value="0.00" />
				<input type="hidden" name="undefined_quantity" value="0" />
				<input type="hidden" name="receiver_email" value="<?=BUSINESS_EMAIL?>" />
				<input type="hidden" name="no_shipping" value="1" />
				<input type="hidden" name="no_note" value="0" />
				<input type="hidden" name="notify_url" value="<?=IPN_URL?>">
				<input type="hidden" name="return" value="<?php echo $event_url; ?>">
				<input type="hidden" name="cancel_return" value="<?php echo $event_url; ?>">
				<input  type="submit" value="Click here if you are not redirected within 5 seconds" />
			</form>
			<script language="javascript">
				setTimeout("SubForm()", 0); 

				function SubForm() {
					document.buyAd.submit();
				}
			</script>
			<?php
			} // end if($res)
			else{
			$sucMessage = "ERROR: Try again later";		
			}
		
	}
	else{
		$sucMessage = $err;
		}



}


$meta_title	= 'Reserve Table';
include_once('includes/header.php');
?>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Reserve Table</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:1px;"> <span class="ew-heading"></span> <br />
            <br />
            <table cellpadding="10" cellspacing="0" width="100%">
              <tr bgcolor="#d1e5c0" style="font-size:22px">
                <td width="45%">Description</strong></td>
                <td width="15%" align="center">Price</td>
              </tr>
              <tr bgcolor="#e4f0d8" style="font-size:20px">
                <td>Table: <?php echo $table_id; ?> <br />
				<em style="font-size:13px;"><br />
				<?php echo $tickets; ?> complimentary admission tickets are included with your table purchase</em>
				</td>
                <td align="center"><?php echo "$".number_format($price,2); ?></td>
                <!-- <strong style="color:#ec0000"> -->
              </tr>
              <tr bgcolor="#d1e5c0" style="font-size:20px">
                <td align="right">Tax</td>
                <td align="center"><?php echo "$".number_format($tax,2); ?></td>
              </tr>
              <tr bgcolor="#e4f0d8" style="font-size:20px">
                <td align="right">Gratuity</td>
                <td align="center"><?php echo "$".number_format($gratuity,2); ?></td>
              </tr>
			  <tr bgcolor="#d1e5c0" style="font-size:20px">
                <td align="right">Surcharge</td>
                <td align="center"><?php echo "$".number_format($surcharge,2); ?></td>
              </tr>
			  <tr bgcolor="#e4f0d8" style="font-size:22px">
                <td align="right">Total</td>
                <td align="center"><strong style="color:#ec0000">
				<?php
					echo "$".number_format($total,2);
				?>
				</strong>
				</td>
              </tr>
            </table><br />

			<em>NOTE: We strive to get the table you want, but cannot always guarantee it. </em>
            <div class="borderDoted"></div>
            <div class="pymnt_details"><br>
              <?php 
			  	
				echo $sucMessage;
				
			  if($_GET['order']){
			  
			  $file_name = base64_decode($_GET['order']);
			  $dowlodUrlPath = base64_encode($file_name);
			  
			  ?>
              <script>
				$(document).ready(function(){
					$('#download').click(function(){
					window.open("download.php?fn=<?php echo $dowlodUrlPath; ?>");
					$('#btn').html('<a style="color:#0033FF; text-decoration:underline" href="<?php echo ABSOLUTE_PATH; ?>nba-allstar">Return Home</a>');
					});
				});
			</script>
              <h2>Your purchase has been completed.</h2>
              <?php
				$id = $_GET['i'];
				$fname		= getSingleColumn('fname',"select * from `nba_package_order` where `id`='$id'");
				$lname		= getSingleColumn('lname',"select * from `nba_package_order` where `id`='$id'");
//				nba_package_order
				?>
              <strong>Package name:</strong> <?php echo $package_name; ?><br />
              <strong>Purchaser Name:</strong> <?php echo ucwords($fname)." ".ucwords($lname); ?><br />
              <strong>Purchase Date:</strong> <?php echo date('d M Y'); ?><br />
              <strong>Cost:</strong> <?php echo number_format($total,2); ?><br />
              <p>Please click on the button below to download your exclusive vouchers.  We will also send these vouchers to your email as confirmation of your purchase.</p>
              <br />
              <br />
              <span id="btn"><img src="<?php echo IMAGE_PATH; ?>new_flayer_downloadButton.png" id="download" style="cursor:pointer" align="middle" /></span>
              <?php
			  }
			  else{
			  ?>
              <form method="post" name="bc_form" enctype="multipart/form-data" action="">
                <div class="title"> Cardholder Information</div>
                <div class="clr"></div>
                <div class="evField">First Name:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="fname" value="<?php echo $bc_fname; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Last Name:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="lname" value="<?php echo $bc_lname; ?>" style="width:300px; height:25px" />
                </div>
				<div class="clr"></div>
                <div class="evField">Email:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="email"  name="email" value="<?php echo $bc_email; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Address Line 1:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="address1" value="<?php echo $bc_address1; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Address Line 2:</div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">City:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="city" value="<?php echo $bc_city; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Postal Code:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="eventname"  name="zip" value="<?php echo $bc_zip; ?>" style="width:70px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Country:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <select style="padding:2px; width:300px; padding:3px" name="country" id="country">
                    <option <?php if ($b_country=='US' || $b_country==''){ echo 'selected="selected"'; } ?> value="US">United States</option>
                  </select>
                </div>
                <div class="clr"></div>
                <!-- Ticket Information Start -->
                <div class="title"> Ticket Information</div>
                <div class="clr">&nbsp;</div>
               
                <div class="clr"></div>
                <div class="evField">Full Name:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="" name="t_buyer_name" value="<?php echo $bc_t_buyer_name; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
                <div class="evField">Email Address:<font color="#FF0000">*</font></div>
                <div class="evLabal" style="width:320px">
                  <input type="text" maxlength="100" id="" name="t_buyer_email" value="<?php echo $bc_t_buyer_email; ?>" style="width:300px; height:25px" />
                </div>
                <div class="clr"></div>
               <!-- Ticket Information End -->
               
                <div class="clr"></div>
                <div class="evField">&nbsp;</div>
                <div class="ev_fltlft" style="width:308px" align="right">
				<input type="image" src="<?php echo IMAGE_PATH; ?>paypal_checkout.png" name="submit" value="Submit">
                <input type="hidden" name="submit" value="Submit">
				<br><br>
				<img src="<?php echo IMAGE_PATH; ?>paypal_icons.gif" />
				  
				  
				  
                </div>
                <div class="clr"></div>
              </form>
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
              <div class="AuthorizeNetSeal" style="padding:3px 0; margin:auto">
                <script>
		var ANS_customer_id="66c2e366-3b5b-46ac-8044-75be4c0c6909";
	</script>
                <script type="text/javascript" language="javascript" src="https://verify.authorize.net/anetseal/seal.js" ></script>
              </div>
              <div style="text-align:center"><img src="<?php echo IMAGE_PATH; ?>cc_cards.png" /></div>
            </div>
            <div class="clr"></div>
          </div>
        </div>
      </div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>
