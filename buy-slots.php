<?php 

require_once('admin/database.php');
require_once('site_functions.php');

//define("IPN_URL","http://www.eventgrabber.com/ipn.php");
//define("BUSINESS_EMAIL","seller_1260447824_biz@bluecomp.net");

$slot_id = base64_decode($_GET['id']);

 
 
$is_active_slot = attribValue('sold_slots', 'id', "where slot_id='$slot_id' AND status=1 AND '". date("Y-m-d") ."' BETWEEN  start_date AND end_date");

if ( $is_active_slot > 0 )
	die('Slot alreat taken');

$bc_amount 	= attribValue('slots', 'price', "where id='$slot_id'");

$today			= date("Y-m-d");
$todayF			= date("M d, Y",strtotime($today));
$rs = mysql_query("select DATE_ADD(now(), INTERVAL 1 MONTH) as nextMonth");
$ro = mysql_fetch_assoc($rs);

$nextmonth	= date("Y-m-d",strtotime($ro['nextMonth']));
$nextmonthF = date("M d, Y",strtotime($nextmonth));

$bc_title		=	$_POST["title"];
$bc_descr		=	$_POST["descr"];
$bc_url			=	$_POST["url"];
$bc_image		=	$_POST["image"];
$bc_fname		=	$_POST["fname"];
$bc_lname		=	$_POST["lname"];
$bc_email		=	$_POST["email"];
$bc_address		=	$_POST["address"];
$bc_city		=	$_POST["city"];
$bc_state		=	$_POST["state"];
$bc_zip			=	$_POST["zip"];


$sucMessage = "";

$errors = array();

if (isset($_POST["submit"]) ) {
	
	if ($_POST["title"] == "")
		$errors[] = "Title can not be empty";
	if ($_POST["descr"] == "")
		$errors[] = "Description can not be empty";
	if ($_POST["url"] == "")
		$errors[] = "Url can not be empty";
	if ($_FILES["image"] == "")
		$errors[] = "Image can not be empty";
	if ($_POST["fname"] == "")
		$errors[] = "First Name can not be empty";
	if ($_POST["lname"] == "")
		$errors[] = "Last Name can not be empty";
	if ($_POST["email"] == "")
		$errors[] = "Email can not be empty";
	
	if ( $_FILES["image"]["name"] != '') {
		list($w, $h, $type, $attr) = getimagesize($_FILES["image"]["tmp_name"]);
		if ($w != 140 || $h != 140 )
			$errors[] = 'Logo Image size is not equal to allowed sizes. ( 140px * 140px)';
	}
	
	$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
	for ($i=0;$i<count($errors); $i++) {
		$err .= '<li>' . $errors[$i] . '</li>';
	}
	$err .= '</ul></td></tr></table>';	

	
	if (!count($errors)) {
		
		$order_id = time();

		$bc_ad_image = "ads/images/". time() . "_" . $_FILES["image"]["name"];
		move_uploaded_file($_FILES["image"]["tmp_name"], $bc_ad_image);
				
		$sql	=	"insert into sold_slots (title,descr,url,image,fname,lname,email,address,city,state,zip,slot_id,amount,start_date,end_date,order_id,status) values ('" . $bc_title . "','" . $bc_descr . "','" . $bc_url . "','" . $bc_ad_image . "','" . $bc_fname . "','" . $bc_lname . "','" . $bc_email . "','" . $bc_address . "','" . $bc_city . "','" . $bc_state . "','" . $bc_zip . "','" . $slot_id . "','0','". $today ."','". $nextmonth ."','". $order_id ."',0)";
		$res	=	mysql_query($sql);
		if ($res){
		?>
		
			<form name="buyAd" action="<?php echo PAYPAL_URL;?>" method="post">
				<input type="hidden" name="cmd" value="_xclick" />
				<input type="hidden" name="business" value="<?=BUSINESS_EMAIL?>" />
				<input type="hidden" name="item_name" value="Ad Slot" />
				<input type="hidden" name="item_number" value="1" />
				<input type="hidden" name="amount" value="<?=$bc_amount?>" />
				<input type="hidden" name="currency_code" value="USD" />
				<input type="hidden" name="shipping" value="0.00" />
				<input type="hidden" name="custom" value="Ad-<?php echo $order_id?>" />
				<input type="hidden" name="shipping2" value="0.00" />
				<input type="hidden" name="handling" value="0.00" />
				<input type="hidden" name="undefined_quantity" value="0" />
				<input type="hidden" name="receiver_email" value="<?=BUSINESS_EMAIL?>" />
				<input type="hidden" name="no_shipping" value="1" />
				<input type="hidden" name="no_note" value="0" />
				<input type="hidden" name="notify_url" value="<?=IPN_URL?>">
				<input type="hidden" name="return" value="http://www.eventgrabber.com/ads.php">
				<input type="hidden" name="cancel_return" value="http://www.eventgrabber.com/ads.php">
				<input  type="submit" value="Click here if you are not redirected within 5 seconds" />
			</form>
			
			<script language="javascript">
				setTimeout("SubForm()", 0); 

				function SubForm() {
					document.buyAd.submit();
				}
			</script>
		
		<?php			
		}
		 
	} else {
		$sucMessage = $err;
	}
} 
		

$meta_title	= 'Buy Ad Slots';
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
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Slot Checkout</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:1px;">
            
            <span class="ew-heading">Package Information</span>
            
            <br /><br />
            
			<table cellpadding="10" cellspacing="0" width="100%">
                <tr bgcolor="#d1e5c0" style="font-size:22px">
                  <td width="45%">SLOT TYPE</strong></td>
                  <td width="20%" align="center">START DATE</strong></td>
                  <td width="20%" align="center">END DATE</strong></td>
                  <td width="15%" align="center">PRICE</td>
                </tr>
			
                <tr bgcolor="#e4f0d8" style="font-size:20px">
                  <td>Standard</td>
                  <td align="center"><?php echo $todayF;?></td>
                  <td align="center"><?php echo $nextmonthF;?></td>
				  <td align="center"><strong style="color:#ec0000">$<?php echo number_format($bc_amount,2); ?></strong></td>
                </tr>
               
              </table>
			
			
            <div class="borderDoted"></div>
            <div class="pymnt_details"><br>
              
			  <?php 
			  	
				echo $sucMessage;
				
			  ?>
			  
			  <form method="post" name="bc_form" enctype="multipart/form-data" action=""  >

				<div class="title"> Advertisement Information</div>
                <div class="clr"></div>
				
				<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
				<tr>
				<td  align="right" class="bc_label">Title:</td>
				<td  align="left" class="bc_input_td">
				<input type="text" name="title" id="title"  value="<?php echo $bc_title; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Description:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="descr" id="descr"  value="<?php echo $bc_descr; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Url:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="url" id="url"  value="<?php echo $bc_url; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label" valign="top">Logo:</td>
				<td align="left" class="bc_input_td" valign="top">
				<input name="image" id="image" type="file" />
				<br><font color="red" style="font-size:10px">Should be 140px * 140px</font>
				</td>
				</tr>
				</table>
				
				<div class="title"> Personal Information</div>
                <div class="clr"></div>
				
				<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
				<tr>
				<td align="right" class="bc_label">First Name:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="fname" id="fname"  value="<?php echo $bc_fname; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Last Name:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="lname" id="lname"  value="<?php echo $bc_lname; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Email Address:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="email" id="email"  value="<?php echo $bc_email; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Address:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="address" id="address"  value="<?php echo $bc_address; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">City:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="city" id="city"  value="<?php echo $bc_city; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">State:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="state" id="state"  value="<?php echo $bc_state; ?>"/>
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Zip:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="zip" id="zip"  value="<?php echo $bc_zip; ?>"/>
				</td>
				</tr>
				</table>
				
				<!--<div class="title"> Payment Information</div>-->
                <div class="clr"></div>
				
				<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
				<!--
				<tr>
				<td align="right" class="bc_label">Credit Card Type:</td>
				<td align="left" class="bc_input_td">

				<select style="width:150px" name="cc_type">
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
				<td align="left" class="bc_input_td">
				<input type="text" name="cc_number" id="cc_number"  value="<?php echo $bc_cc_number; ?>"/>
				</td>
				</tr>
				<tr>
				<td align="right" class="bc_label">Expairy Date:</td>
				<td align="left" class="bc_input_td">
					
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
					
				</td>
				</tr>
				
				<tr>
				<td align="right" class="bc_label">Security Code (CVV):</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="cvv2" id="cvv2" style="width:50px" value="<?php echo $bc_cvv2; ?>"/>
				</td>
				</tr>
				-->
				<tr>
				<td class="bc_label">&nbsp;</td><td align="left" >
				<!--<input name="submit" type="submit" value="Save" class="bc_button" />-->
				<input type="image" src="<?php echo IMAGE_PATH; ?>paypal_checkout.png" name="submit" value="Submit">
                <input type="hidden" name="submit" value="Submit">
				<br><br>
				<img src="<?php echo IMAGE_PATH; ?>paypal_icons.gif" />
				</td>
				</tr>
				</table>
				</form>
			  
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
