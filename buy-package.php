<?php 

require_once('admin/database.php');
require_once('site_functions.php');

$v = base64_decode($_GET['v']);

$package = explode("[price]", $v);

$package_name = $package[0];
$package_price = $package[1];



if(!is_numeric($package_price) || $package_price==0){
	if($_GET['order']==''){
		echo "<script>window.location.href='index.php';</script>"; 
		}	
	}

$bc_fname		=	$_POST["fname"];
$bc_lname		=	$_POST["lname"];
$bc_email		=	$_POST["email"];
$bc_address		=	$_POST["address"];
$bc_city		=	$_POST["city"];
$bc_state		=	$_POST["state"];
$bc_zip			=	$_POST["zip"];
$bc_name_on_card=	$_POST["name_on_card"];
$bc_cc_number	=	$_POST["cc_number"];
$bc_cc_type		=	$_POST["cc_type"];
$bc_cvv2		=	$_POST["cvv2"];


$sucMessage = "";

$errors = array();

if (isset($_POST["submit"]) ) {
	

	if ($_POST["fname"] == "")
		$errors[] = "First Name can not be empty";
	if ($_POST["lname"] == "")
		$errors[] = "Last Name can not be empty";
	if ($_POST["email"] == "")
		$errors[] = "Email can not be empty";
	if(! validateEmail($_POST["email"]) && $_POST["email"]!='' )
		$errors[] = 'Email is not valid.';
	if ($_POST["cc_number"] == "")
		$errors[] = "Credit Card Number can not be empty";
	if ($_POST["cc_type"] == "")
		$errors[] = "Credit Card Type can not be empty";
	if ($_POST["month"] == "" || $_POST["year"] == "")
		$errors[] = "Expiry Date is Invalid.";
	
	$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
	for ($i=0;$i<count($errors); $i++) {
		$err .= '<li>' . $errors[$i] . '</li>';
	}
	$err .= '</ul></td></tr></table>';	

	
	if (!count($errors)) {
	
	$total_price = $package_price*10/100+$package_price;
		
		$customer_details['first_name'] 		=	$bc_fname;
		$customer_details['last_name'] 			=	$bc_lname;
		$customer_details['address'] 			=	$bc_address;
		$customer_details['zip'] 				=	$bc_zip;
		$customer_details['city'] 				=	$b_city;
		$customer_details['state'] 				=	$b_state;
		$customer_details['country'] 			=	'US';
		$customer_details['card_number'] 		=	$bc_cc_number;
		$customer_details['card_code'] 			=	$bc_cvv2;
		$customer_details['card_expiry_month'] 	=	$_POST["month"];
		$customer_details['card_expiry_year'] 	=	$_POST["year"];
		$customer_details['totalAmount']		=	$total_price;
			
		include_once("includes/authorize.net.php");
		$response = authorize_process($customer_details);	
		list($processed, $response_arr) = authorize_isProcessed($response);
		
		if (!$processed) {

			$sucMessage = '<table border="0" width="90%"><tr><td class="error" ><ul><li>' . $response_arr[3] . '</li></ul></td></tr></table>';
	
		} else {
					
			$sql	=	"INSERT INTO `nba_package_order` (`id`, `package_name`, `fname`, `lname`, `email`, `address`, `city`, `state`, `zip`, `name_on_card`, `cc_number`, `cc_type`, `cvv2`, `amount`) VALUES (NULL, '$package_name', '$bc_fname', '$bc_lname', '$bc_email', '$bc_address', '$bc_city', '$bc_state', '$bc_zip', '$bc_name_on_card', '$bc_cc_number', '$bc_cc_type', '$bc_cvv2', '$total_price');";
			$res	=	mysql_query($sql);
			$id = mysql_insert_id();
			
			$file  = generateVouchersPDF($id);
			
			if ($res) {
				echo "<script>window.location.href='?order=".base64_encode($file)."&i=".$id."&v=".$_GET['v']."';</script>";
			//	$sucMessage = "<h2>Your Add has been Created Successfully.</h2>";
			} else {
				$sucMessage = "Error: Please try Later";
			} 
		}	
	} else {
		$sucMessage = $err;
	}
} 
		

$meta_title	= 'Buy Package';
include_once('includes/header.php');
?>
<style>

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
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Event Package Checkout</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:1px;"> <span class="ew-heading">Package Information</span> <br />
            <br />
            <table cellpadding="10" cellspacing="0" width="100%">
              <tr bgcolor="#d1e5c0" style="font-size:22px">
                <td width="45%">Package</strong></td>
                <td width="15%" align="center">PRICE</td>
              </tr>
              <tr bgcolor="#e4f0d8" style="font-size:20px">
                <td><?php echo $package_name; ?></td>
                <td align="center">$<?php echo number_format($package_price,2); ?></td>
                <!-- <strong style="color:#ec0000"> -->
              </tr>
              <tr bgcolor="#d1e5c0" style="font-size:22px">
                <td align="right">Tax</td>
                <td align="center"><?php $tax = $package_price*10/100; echo "$".number_format($tax,2); ?></td>
              </tr>
              <tr bgcolor="#d1e5c0" style="font-size:22px">
                <td align="right">Total</td>
                <td align="center"><strong style="color:#ec0000"><?php
				$total = $tax+$package_price;
				 echo "$".number_format($total,2)
				?></strong></td>
              </tr>
            </table>
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
              <form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
                <div class="title"> Personal Information</div>
                <div class="clr"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
                  <tr>
                    <td align="right" class="bc_label">First Name:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="fname" id="fname"  value="<?php echo $bc_fname; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Last Name:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="lname" id="lname"  value="<?php echo $bc_lname; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Email Address:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="email" id="email"  value="<?php echo $bc_email; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Address:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="address" id="address"  value="<?php echo $bc_address; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">City:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="city" id="city"  value="<?php echo $bc_city; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">State:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="state" id="state"  value="<?php echo $bc_state; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Zip:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="zip" id="zip"  value="<?php echo $bc_zip; ?>"/>
                    </td>
                  </tr>
                </table>
                <div class="title"> Payment Information</div>
                <div class="clr"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
                  <!--<tr>
				<td align="right"  class="bc_label">Name on Card:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="name_on_card" id="name_on_card"  value="<?php echo $bc_name_on_card; ?>"/>
				</td>
				</tr>-->
                  <tr>
                    <td align="right" class="bc_label">Credit Card Type:</td>
                    <td align="left" class="bc_input_td"><select style="width:150px" name="cc_type">
                        <option value="">--Select--</option>
                        <option value="visa" <?php if($bc_cc_type=="visa"){ echo 'selected="selected"'; }?>>Visa</option>
                        <option value="mastercard"<?php if($bc_cc_type=="mastercard"){ echo 'selected="selected"'; }?>>MasterCard</option>
                        <option value="AMEX"<?php if($bc_cc_type=="AMEX"){ echo 'selected="selected"'; }?>>AMEX</option>
                        <option value="jcb"<?php if($bc_cc_type=="jcb"){ echo 'selected="selected"'; }?>>JCB</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Credit Card Number:</td>
                    <td align="left" class="bc_input_td"><input type="text" name="cc_number" id="cc_number"  value="<?php echo $bc_cc_number; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Expiration Date:</td>
                    <td align="left" class="bc_input_td"><select name="month" style="width:75px" id="dalete1">
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
                    </td>
                  </tr>
                  <tr>
                    <td align="right" class="bc_label">Security Code (CVV):</td>
                    <td align="left" class="bc_input_td"><input type="text" name="cvv2" id="cvv2" style="width:50px" value="<?php echo $bc_cvv2; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td align="left"><!--<input name="submit" type="submit" value="Save" class="bc_button" />-->
                      <input type="image" src="<?php echo IMAGE_PATH; ?>submit2.png" name="submit" value="Submit">
                      <input type="hidden" name="submit" value="Submit">
                    </td>
                  </tr>
                </table>
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
                <script type="text/javascript" language="javascript" src="http://verify.authorize.net/anetseal/seal.js" ></script>
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
