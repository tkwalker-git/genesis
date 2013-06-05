<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$meta_title = "Order Tickets";
$folderName	=	time();
	
	
$bc_event_id	=	$_POST['event_id'];
$res = mysql_query("select * from `event_ticket` where `event_id`='$bc_event_id'");
while($row = mysql_fetch_array($res)){
	$bc_service_fee_type	=	$row['service_fee_type'];
	$bc_service_fee			=	$row['service_fee'];
	$main_ticket_id			=	$row['id'];
}

if($_POST['orderTickets']){

	$bc_event_id	=	$_POST['event_id'];
for($i=0;$i< count($_POST['id']); $i++) {
		
	$qty	=	$_POST['qty'][$i];
	
	if($qty!='' && $qty!='0'){	
	
	$date	=	date('Y-m-d', strtotime($_POST['date'][$i]));
	$qty	=	$_POST['qty'][$i];
	$id		=	$_POST['id'][$i];
	$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");
	$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$bc_event_id");
	while($row = mysql_fetch_array($res)){
		
	$buyer_service_free_after_percent		=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
	$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');
		
	$ttl 	= $qty*$row['price'];
	
	$fnl	= number_format($ttl+($finalServiceCharges*$qty), 2,'.','');
	$r = mysql_query("select * from `tickets_cart` where `ticket_id` = '$id' && `date` = '$date' && `user_id`='$user_id'");
	if(mysql_num_rows($r)){
	while($ro = mysql_fetch_array($r)){
	$tickets_cart_id	=	$ro['id'];
	$old_quantity		=	$ro['quantity'];
	$old_total_price	=	$ro['total_price'];
	$qty				=	$old_quantity + $qty;
	$total_price		=	$old_total_price + $fnl;
	mysql_query("UPDATE `tickets_cart` SET `total_price` = '$total_price', `quantity` = '$qty' WHERE `id` = '$tickets_cart_id'");
	}
	}
	else{
		mysql_query("INSERT INTO `tickets_cart` (`id`, `ticket_id`, `total_price`, `quantity`, `date`, `user_id`, `event_id`) VALUES (NULL, '$id', '$fnl', '$qty', '$date', '$user_id', '$bc_event_id')");
		} // end else
		
		} // end while
	
	} // end if
		
		} // end for

} // end if $_POST['orderTickets']


if($_REQUEST['submit']){	

	$event_id			=	$_POST['event_id'];
	$b_country			=	$_POST['b_country'];
	$b_address			=	$_POST['b_address'];
	$b_city				=	$_POST['b_city'];
	$b_state			=	$_POST['b_state'];
	$bc_name			=	$_POST["name"];
	$bc_card_type		=	$_POST['cardType'];
	$bc_month			=	$_POST['month'];
	$bc_year			=	$_POST['year'];
	$bc_card_number		=	$_POST['number'];
	$bc_securityCode	=	$_POST['securityCode'];
	$id					=	$_POST['id'];
	$qty				=	$_POST['qty'];
	$date				=	$_POST['date'];

	
	if ( trim($b_country) == '' )
		$errors[] = 'Country is required.';
	if ( trim($b_address) == '' )
		$errors[] = 'Address is required.';
	if ( trim($b_city) == '' )
		$errors[] = 'City is required.';
	if ( trim($b_state) == '' )
		$errors[] = 'State / Province is required.';
	if ( trim($bc_name) == '' )
		$errors[] = 'Cardholder name is required.';
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
	
	$r = mysql_query("SELECT * FROM  `tickets_cart` where `user_id`='$user_id'");
	while($ro = mysql_fetch_array($r)){
	$bc_ticket_id	=	$ro['ticket_id'];
	$bc_total_price	=	$ro['total_price'];
	$bc_quantity	=	$ro['quantity'];
	$bc_date		=	$ro['date'];
	$bc_event_id	=	$ro['event_id'];
	}
	
	$re = mysql_query("select * from `event_ticket_price` where `id`='$bc_ticket_id'");
	while($ro = mysql_fetch_array($re)){
	$title = $ro['title'];
	}
	
$grand_total	=	number_format($bc_total_price, 2,'.','');

	
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

			$customer_details['totalAmount']		=	$grand_total;
			
			include_once("includes/authorize.net.php");
			$response = authorize_process($customer_details);	
			list($processed, $response_arr) = authorize_isProcessed($response);
			
			if (!$processed) {
			
		$sucMessage = '<table border="0" width="90%"><tr><td class="error" ><ul><li>' . $response_arr[3] . '</li></ul></td></tr></table>';
		
			} else {
	
	$date		=	date('Y-m-d');
	
	mkdir("tickets/".$folderName, 0700);
mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`, `main_ticket_id`) VALUES (NULL, '$user_id', '$grand_total', '$date', 'ticket', '$main_ticket_id')");
	
	$order_id	=	mysql_insert_id();
	
	$total_price ='';
	
	$r = mysql_query("SELECT * FROM  `tickets_cart` where `user_id`='$user_id'");
	while($ro = mysql_fetch_array($r)){
	$bc_ticket_id	=	$ro['ticket_id'];
	$bc_total_price	=	$ro['total_price'];
	$bc_quantity	=	$ro['quantity'];
	$bc_date		=	$ro['date'];
	$bc_event_id	=	$ro['event_id'];
	
	
	$forTicketOne	=	number_format($bc_total_price/$bc_quantity, 2,'.','');
	
	
	
	mysql_query("INSERT INTO `order_tickets` (`id`, `ticket_id`, `price`, `quantity`, `date`, `order_id`) VALUES (NULL, '$id', '$priceAfterQty', '$qty', '$date', '$order_id');");
	$ticket_id_for_ticket	=	mysql_insert_id();
	
	



$qty = $bc_quantity;
for ($d=1;$d<=$qty;$d++){
	
$rt = mysql_query("select * from `events` where `id`='$bc_event_id'");
	while($row = mysql_fetch_array($rt)){
	$event_name			=	$row['event_name'];
	$event_image		=	$row['event_image'];
	$event_start_time	=	$row['event_start_time'];
	}

	if($qty>1){
	$ticket_number	=	str_pad($ticket_id_for_ticket."_".$d, 12, "0", STR_PAD_LEFT);
	}
	else{
	$ticket_number	=	str_pad($ticket_id_for_ticket, 10, "0", STR_PAD_LEFT);
	}
	
	 $values = array(
					'event_name'	=>	$event_name,
					'ticket_number'	=>	$ticket_number,
					'event_date'	=>	$bc_date,
					'time'			=>	$event_start_time,
					'recipi'		=>	$_POST['name'],
					'state' 		=>	$_POST['b_state'],
					'address'		=>	$_POST['b_address'],
					'city' 			=>	$_POST['b_city'],
					'expire_date'	=>	$date,
					'ticket_type'	=>	$title,
					'event_image'	=>	$event_image,
					'ticket_price'	=>	$forTicketOne
					);
					
//generateTicket($values);
		$event_name		=	$values['event_name'];
		$ticket_number	=	$values['ticket_number'];
		$event_date		=	$values['event_date'];
		$time			=	$values['time'];
		$recipi			=	$values['recipi'];
		$state			=	$values['state'];
		$address		=	$values['address'];
		$city			=	$values['city'];
		$zip			=	$values['zip'];
		$expire_date	=	$values['expire_date'];
		$event_image	=	$values['event_image'];
		$ticket_type	=	$values['ticket_type'];
		$ticket_price	=	$values['ticket_price'];
			
		$width			=	800;
		$height			=	436;
	
		$arialbd		=	"arialbd.ttf";
		$arial			=	"arial.ttf";
				
		$im		=	imagecreatefrompng("blankticket.png");
	//	if(file_exists(DOC_ROOT .  'event_images/' . $event_image )){
//		if ( !file_exists(DOC_ROOT .  'event_images/ico_' . $event_image )) {
//		makeThumbnail($event_image, 'event_images/', '', 106, 2000,'ico_');
//		}
//		}
//		else{
//		$event_image	=	"noimg.gif";
//		}
		
	//	if(file_exists(DOC_ROOT .  'event_images/' . $event_image )){
		if ( !file_exists(DOC_ROOT .  'event_images/ico_' . $event_image )) {
	//	makeThumbnail($event_image, 'event_images/', '', 106, 2000,'ico_');
		$event_image	=	"noimg.gif";
		}
	
		
		
		list($eventImageWidth, $eventImageHeight, $eventImageType, $attr) = getimagesize("event_images/ico_".$event_image."");
		if($eventImageType==1){
		$src	=	imagecreatefromgif("event_images/ico_".$event_image."");
		}elseif($eventImageType==2){
		$src	=	imagecreatefromjpeg("event_images/ico_".$event_image."");
		}elseif($eventImageType==3){
		$src	=	imagecreatefrompng(ABSOLUTE_PATH."event_images/ico_".$event_image."");
		}
		
		$black	=	imagecolorallocate($im, 0, 0, 0);
		$yellow	=	imagecolorallocate($im, 238, 175, 12);
		$blue	=	imagecolorallocate($im, 1, 100, 165);
		
		
		if ($bc_service_fee_type==2){
        $t_price	=	$ticket_price+$bc_service_fee;
		}
		else{
		$t_price	=	$ticket_price;
		}
					  
					
		imagettftext($im, 10, 0, 583, 68, $black, $arialbd, "Ticket # ".$ticket_number);
		
		imagettftext($im, 10, 0, 201, 120, $yellow, $arialbd, "Event");
		imagettftext($im, 13, 0, 201, 140, $black, $arialbd, $event_name);
		imagettftext($im, 9, 0, 201, 164, $black, $arialbd, "DATE:");
		imagettftext($im, 9, 0, 242, 164, $blue, $arialbd, date('l, F d, Y', strtotime($event_date)));
		imagettftext($im, 9, 0, 201, 186, $black, $arialbd, "TIME:");
		imagettftext($im, 9, 0, 242, 186, $blue, $arialbd, date('h:i A', strtotime($time)));
		imagettftext($im, 9, 0, 201, 207, $black, $arialbd, "TYPE:");
		imagettftext($im, 9, 0, 242, 207, $blue, $arialbd, $ticket_type);
		imagettftext($im, 9, 0, 201, 228, $black, $arialbd, "PRICE:");
		imagettftext($im, 9, 0, 242, 228, $blue, $arialbd, "$".round($t_price, 2));
		imagettftext($im, 9, 0, 201, 261, $black, $arialbd, "RECIPIENTS:");
		imagettftext($im, 9, 0, 201, 275, $blue, $arialbd, $recipi);
		imagettftext($im, 9, 0, 302, 261, $black, $arialbd, "REDEEM AT:");
		imagettftext($im, 9, 0, 302, 275, $blue, $arialbd, $state);
		imagettftext($im, 8, 0, 302, 288, $blue, $arial, $address);
		imagettftext($im, 8, 0, 302, 300, $blue, $arial, $city.", ");
		imagettftext($im, 8, 0, 362, 300, $blue, $arial, $zip);
		imagettftext($im, 9, 0, 419, 261, $black, $arialbd, "TICKET EXPIRES:");
		imagettftext($im, 9, 0, 419, 275, $blue, $arialbd, date('D, M. d, Y', strtotime($event_date)));
		imagecopymerge($im, $src, 89, 109, 0, 0, $eventImageWidth, $eventImageHeight, 100);
		
	//	header ("Content-type: image/png");

		
		
		imagepng($im, 'tickets/'.$folderName.'/ticket'.rand().'.png');
	//	imagedestroy($im);	
	}
	
	
	}
	
$zipFileNam = "tickets/".$folderName.".zip";

$zip = new ZipArchive();

if ($zip->open($zipFileNam, ZIPARCHIVE::CREATE)!==TRUE) 
    die("cannot open $folderName\n");

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("tickets/$folderName/"));
foreach ($iterator as $key=>$value)
{
	$zip->addFile(realpath($key), $key) or die ("ERROR: Could not add file: $key");
}
$zip->close();


$dirname	=	"tickets/".$folderName;
if (is_dir($dirname))
$dir_handle = opendir($dirname);
if (!$dir_handle)
return false;
while($file = readdir($dir_handle)) {
if ($file != "." && $file != "..") {
if (!is_dir($dirname."/".$file)){
unlink($dirname."/".$file);

}
else{

delete_directory($dirname.'/'.$file);
}
}
}
closedir($dir_handle);
//chmod($dirname, 0666);
//rmdir($folderName);

$zipNam = $folderName.".zip";
mysql_query("INSERT INTO `tickets_record` (`id`, `order_id`, `user_id`, `file_name`, `status`) VALUES (NULL, '$order_id', '$user_id', '$zipNam', '0');");

mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`) VALUES (NULL, '$order_id', '$bc_name', '$bc_card_type', '$bc_month', '$bc_year', '$bc_card_number', '$bc_securityCode')");
mysql_query("INSERT INTO `billing_information` (`id`, `order_id`, `country`, `address`, `city`, `state`) VALUES (NULL, '$order_id', '$b_country', '$b_address', '$b_city', '$b_state');");
	$sucMessage = "<b style='color:#ff0000'>Your order submitted successfully</b><br>&nbsp;";
	$_SESSION['order_id']=$order_id;
	mysql_query("DELETE FROM `tickets_cart` WHERE `user_id` = '$user_id'");
	}}
	else{
	$sucMessage = $err;
	}
	

}
	
 require_once('includes/header.php');
?>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	
	
	$(document).ready(function(){
		$('#payNowButton').click(function(){
			$('#payDiv').css('display','none');
			$('#payNow').css('display','block');	
		});
		
		$('#paypalButton').click(function(){
			$('#payDiv').html('<h4>Redirecting to PayPal...</h4>');
			document.bc_form.submit();
		});
			
	});
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="eventMdlBg">
      <div class="eventMdlMain"> <span class="viewevents_title">Order Information</span>
        <div class="featuresBoxNew">
          <div class="featuresBotBgNew">
            <div class="featuresTopBgNew">
              <div class="creatProduct">
                <form method="post" name="bc_form" enctype="multipart/form-data" action="">
                  <div class="error"><?php echo $sucMessage; if($_SESSION['order_id']){ echo "<a href='".ABSOLUTE_PATH."tickets.php' style='color:#ff0000'>Click Here</a> for download your ordered tickets<br>&nbsp;";}?></div>
                  <div>
                    <h3><u>Order Summary</u></h3>
                  </div>
                  <table cellpadding="0" cellspacing="0" border="1" bordercolor="#E1E1E1" style="color:#FFFFFF" width="100%" align="center">
                    <tr bgcolor="#383838">
                      <td width="37%" height="30" align="center"><strong>Ticket Type</strong></td>
                      <td width="13%" align="center"><strong>Quantity</strong></td>
                      <td width="13%" align="center"><strong>Date</strong></td>
                      <td width="22%" align="center"><strong>Price</strong></td>
                    </tr>
                   
                    <?php
					
					$res = mysql_query("select * from `tickets_cart` where `user_id`='$user_id'");
					$record = 0;
					if(mysql_num_rows($res)){
					while($row = mysql_fetch_array($res)){
					$record++;
					$ticket_id		=	$row['ticket_id'];
					$qty			=	$row['quantity'];
					$date			=	$row['date'];
					$total_price	=	$row['total_price'];
		

		$re = mysql_query("select * from `event_ticket_price` where `id`='$ticket_id'");
			while($ro = mysql_fetch_array($re)){
			$title = $ro['title'];
			}
?>
                    <tr style="color:#000000">
                      <td width="37%" height="30" align="center"><?php echo $title; ?></td>
                      <td width="13%" align="center"><?php echo $qty; ?></td>
                      <td width="13%" align="center"><?php echo date('d M Y', strtotime($date)); ?></td>
                      <td width="22%" align="center"><?php echo $total_price; ?></td>
                    </tr>
                    <?php 
					$total_Amount_Due = $total_price + $total_Amount_Due;
					 } ?>
                    <tr style="color:#000000">
                      <td colspan="6" align="right" style="padding:10px;">Total Amount Due:&nbsp; &nbsp; <?php echo $total_Amount_Due; ?> </td>
                    </tr>
                    <?php }
					else{
					echo '<tr><td colspan="6" align="center" style="padding:10px;" class="clr"><strong>Cart is Empty</strong></td></tr>';
					}
					?>
                  </table>
                  <div id="payNow" style=" <?php if (!$_REQUEST['submit']){ echo 'display:none'; } ?>">
                    <div>
                      <h3> <u>Billing Information</u> </h3>
                    </div>
                    <div class="evField" style="width:190px">Country <font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <select  class="inp" style="width:306px" name="b_country" id="bill_country">
                        <option value="">Select a Country</option>
                        <option <?php if ($b_country=='AF'){ echo 'selected="selected"'; } ?> value="AF">Afghanistan</option>
                        <option <?php if ($b_country=='AX'){ echo 'selected="selected"'; } ?> value="AX">Aland Islands</option>
                        <option <?php if ($b_country=='DZ'){ echo 'selected="selected"'; } ?> value="AL">Albania</option>
                        <option <?php if ($b_country=='AF'){ echo 'selected="selected"'; } ?> value="DZ">Algeria</option>
                        <option <?php if ($b_country=='AS'){ echo 'selected="selected"'; } ?> value="AS">American Samoa</option>
                        <option <?php if ($b_country=='AD'){ echo 'selected="selected"'; } ?> value="AD">Andorra</option>
                        <option <?php if ($b_country=='AO'){ echo 'selected="selected"'; } ?> value="AO">Angola</option>
                        <option <?php if ($b_country=='AI'){ echo 'selected="selected"'; } ?> value="AI">Anguilla</option>
                        <option <?php if ($b_country=='AQ'){ echo 'selected="selected"'; } ?> value="AQ">Antarctica</option>
                        <option <?php if ($b_country=='AG'){ echo 'selected="selected"'; } ?> value="AG">Antigua and Barbuda</option>
                        <option <?php if ($b_country=='AR'){ echo 'selected="selected"'; } ?> value="AR">Argentina</option>
                        <option <?php if ($b_country=='AM'){ echo 'selected="selected"'; } ?> value="AM">Armenia</option>
                        <option <?php if ($b_country=='AW'){ echo 'selected="selected"'; } ?> value="AW">Aruba</option>
                        <option <?php if ($b_country=='AU'){ echo 'selected="selected"'; } ?> value="AU">Australia</option>
                        <option <?php if ($b_country=='AT'){ echo 'selected="selected"'; } ?> value="AT">Austria</option>
                        <option <?php if ($b_country=='AZ'){ echo 'selected="selected"'; } ?> value="AZ">Azerbaijan</option>
                        <option <?php if ($b_country=='BS'){ echo 'selected="selected"'; } ?> value="BS">Bahamas</option>
                        <option <?php if ($b_country=='BH'){ echo 'selected="selected"'; } ?> value="BH">Bahrain</option>
                        <option <?php if ($b_country=='BD'){ echo 'selected="selected"'; } ?> value="BD">Bangladesh</option>
                        <option <?php if ($b_country=='BB'){ echo 'selected="selected"'; } ?> value="BB">Barbados</option>
                        <option <?php if ($b_country=='BY'){ echo 'selected="selected"'; } ?> value="BY">Belarus</option>
                        <option <?php if ($b_country=='BE'){ echo 'selected="selected"'; } ?> value="BE">Belgium</option>
                        <option <?php if ($b_country=='BZ'){ echo 'selected="selected"'; } ?> value="BZ">Belize</option>
                        <option <?php if ($b_country=='BJ'){ echo 'selected="selected"'; } ?> value="BJ">Benin</option>
                        <option <?php if ($b_country=='BM'){ echo 'selected="selected"'; } ?> value="BM">Bermuda</option>
                        <option <?php if ($b_country=='BT'){ echo 'selected="selected"'; } ?> value="BT">Bhutan</option>
                        <option <?php if ($b_country=='BO'){ echo 'selected="selected"'; } ?> value="BO">Bolivia</option>
                        <option <?php if ($b_country=='BA'){ echo 'selected="selected"'; } ?> value="BA">Bosnia and Herzegovina</option>
                        <option <?php if ($b_country=='BW'){ echo 'selected="selected"'; } ?> value="BW">Botswana</option>
                        <option <?php if ($b_country=='BV'){ echo 'selected="selected"'; } ?> value="BV">Bouvet Island</option>
                        <option <?php if ($b_country=='BR'){ echo 'selected="selected"'; } ?> value="BR">Brazil</option>
                        <option <?php if ($b_country=='BQ'){ echo 'selected="selected"'; } ?> value="BQ">British Antarctic Territory</option>
                        <option <?php if ($b_country=='IO'){ echo 'selected="selected"'; } ?> value="IO">British Indian Ocean Territory</option>
                        <option <?php if ($b_country=='VG'){ echo 'selected="selected"'; } ?> value="VG">British Virgin Islands</option>
                        <option <?php if ($b_country=='BN'){ echo 'selected="selected"'; } ?> value="BN">Brunei</option>
                        <option <?php if ($b_country=='BG'){ echo 'selected="selected"'; } ?> value="BG">Bulgaria</option>
                        <option <?php if ($b_country=='BF'){ echo 'selected="selected"'; } ?> value="BF">Burkina Faso</option>
                        <option <?php if ($b_country=='BI'){ echo 'selected="selected"'; } ?> value="BI">Burundi</option>
                        <option <?php if ($b_country=='KH'){ echo 'selected="selected"'; } ?> value="KH">Cambodia</option>
                        <option <?php if ($b_country=='CM'){ echo 'selected="selected"'; } ?> value="CM">Cameroon</option>
                        <option <?php if ($b_country=='CA'){ echo 'selected="selected"'; } ?> value="CA">Canada</option>
                        <option <?php if ($b_country=='CT'){ echo 'selected="selected"'; } ?> value="CT">Canton and Enderbury Islands</option>
                        <option <?php if ($b_country=='CV'){ echo 'selected="selected"'; } ?> value="CV">Cape Verde</option>
                        <option <?php if ($b_country=='KY'){ echo 'selected="selected"'; } ?> value="KY">Cayman Islands</option>
                        <option <?php if ($b_country=='CF'){ echo 'selected="selected"'; } ?> value="CF">Central African Republic</option>
                        <option <?php if ($b_country=='TD'){ echo 'selected="selected"'; } ?> value="TD">Chad</option>
                        <option <?php if ($b_country=='CL'){ echo 'selected="selected"'; } ?> value="CL">Chile</option>
                        <option <?php if ($b_country=='CN'){ echo 'selected="selected"'; } ?> value="CN">China</option>
                        <option <?php if ($b_country=='CX'){ echo 'selected="selected"'; } ?> value="CX">Christmas Island</option>
                        <option <?php if ($b_country=='CC'){ echo 'selected="selected"'; } ?> value="CC">Cocos Islands</option>
                        <option <?php if ($b_country=='CO'){ echo 'selected="selected"'; } ?> value="CO">Colombia</option>
                        <option <?php if ($b_country=='KM'){ echo 'selected="selected"'; } ?> value="KM">Comoros</option>
                        <option <?php if ($b_country=='CG'){ echo 'selected="selected"'; } ?> value="CG">Congo - Brazzaville</option>
                        <option <?php if ($b_country=='CD'){ echo 'selected="selected"'; } ?> value="CD">Congo - Kinshasa</option>
                        <option <?php if ($b_country=='CK'){ echo 'selected="selected"'; } ?> value="CK">Cook Islands</option>
                        <option <?php if ($b_country=='CR'){ echo 'selected="selected"'; } ?> value="CR">Costa Rica</option>
                        <option <?php if ($b_country=='HR'){ echo 'selected="selected"'; } ?> value="HR">Croatia</option>
                        <option <?php if ($b_country=='CU'){ echo 'selected="selected"'; } ?> value="CU">Cuba</option>
                        <option <?php if ($b_country=='CY'){ echo 'selected="selected"'; } ?> value="CY">Cyprus</option>
                        <option <?php if ($b_country=='CZ'){ echo 'selected="selected"'; } ?> value="CZ">Czech Republic</option>
                        <option <?php if ($b_country=='DK'){ echo 'selected="selected"'; } ?> value="DK">Denmark</option>
                        <option <?php if ($b_country=='DJ'){ echo 'selected="selected"'; } ?> value="DJ">Djibouti</option>
                        <option <?php if ($b_country=='DM'){ echo 'selected="selected"'; } ?> value="DM">Dominica</option>
                        <option <?php if ($b_country=='DO'){ echo 'selected="selected"'; } ?> value="DO">Dominican Republic</option>
                        <option <?php if ($b_country=='NQ'){ echo 'selected="selected"'; } ?> value="NQ">Dronning Maud Land</option>
                        <option <?php if ($b_country=='TL'){ echo 'selected="selected"'; } ?> value="TL">East Timor</option>
                        <option <?php if ($b_country=='EC'){ echo 'selected="selected"'; } ?> value="EC">Ecuador</option>
                        <option <?php if ($b_country=='EG'){ echo 'selected="selected"'; } ?> value="EG">Egypt</option>
                        <option <?php if ($b_country=='SV'){ echo 'selected="selected"'; } ?> value="SV">El Salvador</option>
                        <option <?php if ($b_country=='GQ'){ echo 'selected="selected"'; } ?> value="GQ">Equatorial Guinea</option>
                        <option <?php if ($b_country=='ER'){ echo 'selected="selected"'; } ?> value="ER">Eritrea</option>
                        <option <?php if ($b_country=='EE'){ echo 'selected="selected"'; } ?> value="EE">Estonia</option>
                        <option <?php if ($b_country=='ET'){ echo 'selected="selected"'; } ?> value="ET">Ethiopia</option>
                        <option <?php if ($b_country=='FK'){ echo 'selected="selected"'; } ?> value="FK">Falkland Islands</option>
                        <option <?php if ($b_country=='FO'){ echo 'selected="selected"'; } ?> value="FO">Faroe Islands</option>
                        <option <?php if ($b_country=='FJ'){ echo 'selected="selected"'; } ?> value="FJ">Fiji</option>
                        <option <?php if ($b_country=='FI'){ echo 'selected="selected"'; } ?> value="FI">Finland</option>
                        <option <?php if ($b_country=='FR'){ echo 'selected="selected"'; } ?> value="FR">France</option>
                        <option <?php if ($b_country=='GF'){ echo 'selected="selected"'; } ?> value="GF">French Guiana</option>
                        <option <?php if ($b_country=='PF'){ echo 'selected="selected"'; } ?> value="PF">French Polynesia</option>
                        <option <?php if ($b_country=='TF'){ echo 'selected="selected"'; } ?> value="TF">French Southern Territories</option>
                        <option <?php if ($b_country=='FQ'){ echo 'selected="selected"'; } ?> value="FQ">French Southern and Antarctic Territories</option>
                        <option <?php if ($b_country=='GA'){ echo 'selected="selected"'; } ?> value="GA">Gabon</option>
                        <option <?php if ($b_country=='GM'){ echo 'selected="selected"'; } ?> value="GM">Gambia</option>
                        <option <?php if ($b_country=='GE'){ echo 'selected="selected"'; } ?> value="GE">Georgia</option>
                        <option <?php if ($b_country=='DE'){ echo 'selected="selected"'; } ?> value="DE">Germany</option>
                        <option <?php if ($b_country=='GH'){ echo 'selected="selected"'; } ?> value="GH">Ghana</option>
                        <option <?php if ($b_country=='GI'){ echo 'selected="selected"'; } ?> value="GI">Gibraltar</option>
                        <option <?php if ($b_country=='GR'){ echo 'selected="selected"'; } ?> value="GR">Greece</option>
                        <option <?php if ($b_country=='GL'){ echo 'selected="selected"'; } ?> value="GL">Greenland</option>
                        <option <?php if ($b_country=='GD'){ echo 'selected="selected"'; } ?> value="GD">Grenada</option>
                        <option <?php if ($b_country=='GP'){ echo 'selected="selected"'; } ?> value="GP">Guadeloupe</option>
                        <option <?php if ($b_country=='GU'){ echo 'selected="selected"'; } ?> value="GU">Guam</option>
                        <option <?php if ($b_country=='GT'){ echo 'selected="selected"'; } ?> value="GT">Guatemala</option>
                        <option <?php if ($b_country=='GG'){ echo 'selected="selected"'; } ?> value="GG">Guernsey</option>
                        <option <?php if ($b_country=='GN'){ echo 'selected="selected"'; } ?> value="GN">Guinea</option>
                        <option <?php if ($b_country=='GW'){ echo 'selected="selected"'; } ?> value="GW">Guinea-Bissau</option>
                        <option <?php if ($b_country=='GY'){ echo 'selected="selected"'; } ?> value="GY">Guyana</option>
                        <option <?php if ($b_country=='HT'){ echo 'selected="selected"'; } ?> value="HT">Haiti</option>
                        <option <?php if ($b_country=='HM'){ echo 'selected="selected"'; } ?> value="HM">Heard Island and McDonald Islands</option>
                        <option <?php if ($b_country=='HN'){ echo 'selected="selected"'; } ?> value="HN">Honduras</option>
                        <option <?php if ($b_country=='HK'){ echo 'selected="selected"'; } ?> value="HK">Hong Kong SAR China</option>
                        <option <?php if ($b_country=='HU'){ echo 'selected="selected"'; } ?> value="HU">Hungary</option>
                        <option <?php if ($b_country=='IS'){ echo 'selected="selected"'; } ?> value="IS">Iceland</option>
                        <option <?php if ($b_country=='IN'){ echo 'selected="selected"'; } ?> value="IN">India</option>
                        <option <?php if ($b_country=='ID'){ echo 'selected="selected"'; } ?> value="ID">Indonesia</option>
                        <option <?php if ($b_country=='IR'){ echo 'selected="selected"'; } ?> value="IR">Iran</option>
                        <option <?php if ($b_country=='IQ'){ echo 'selected="selected"'; } ?> value="IQ">Iraq</option>
                        <option <?php if ($b_country=='IE'){ echo 'selected="selected"'; } ?> value="IE">Ireland</option>
                        <option <?php if ($b_country=='IM'){ echo 'selected="selected"'; } ?> value="IM">Isle of Man</option>
                        <option <?php if ($b_country=='IL'){ echo 'selected="selected"'; } ?> value="IL">Israel</option>
                        <option <?php if ($b_country=='IT'){ echo 'selected="selected"'; } ?> value="IT">Italy</option>
                        <option <?php if ($b_country=='CI'){ echo 'selected="selected"'; } ?> value="CI">Ivory Coast</option>
                        <option <?php if ($b_country=='JM'){ echo 'selected="selected"'; } ?> value="JM">Jamaica</option>
                        <option <?php if ($b_country=='JP'){ echo 'selected="selected"'; } ?> value="JP">Japan</option>
                        <option <?php if ($b_country=='JE'){ echo 'selected="selected"'; } ?> value="JE">Jersey</option>
                        <option <?php if ($b_country=='JT'){ echo 'selected="selected"'; } ?> value="JT">Johnston Island</option>
                        <option <?php if ($b_country=='JO'){ echo 'selected="selected"'; } ?> value="JO">Jordan</option>
                        <option <?php if ($b_country=='KZ'){ echo 'selected="selected"'; } ?> value="KZ">Kazakhstan</option>
                        <option <?php if ($b_country=='KE'){ echo 'selected="selected"'; } ?> value="KE">Kenya</option>
                        <option <?php if ($b_country=='KI'){ echo 'selected="selected"'; } ?> value="KI">Kiribati</option>
                        <option <?php if ($b_country=='KW'){ echo 'selected="selected"'; } ?> value="KW">Kuwait</option>
                        <option <?php if ($b_country=='KG'){ echo 'selected="selected"'; } ?> value="KG">Kyrgyzstan</option>
                        <option <?php if ($b_country=='LA'){ echo 'selected="selected"'; } ?> value="LA">Laos</option>
                        <option <?php if ($b_country=='LV'){ echo 'selected="selected"'; } ?> value="LV">Latvia</option>
                        <option <?php if ($b_country=='LB'){ echo 'selected="selected"'; } ?> value="LB">Lebanon</option>
                        <option <?php if ($b_country=='LS'){ echo 'selected="selected"'; } ?> value="LS">Lesotho</option>
                        <option <?php if ($b_country=='LR'){ echo 'selected="selected"'; } ?> value="LR">Liberia</option>
                        <option <?php if ($b_country=='LY'){ echo 'selected="selected"'; } ?> value="LY">Libya</option>
                        <option <?php if ($b_country=='LI'){ echo 'selected="selected"'; } ?> value="LI">Liechtenstein</option>
                        <option <?php if ($b_country=='LT'){ echo 'selected="selected"'; } ?> value="LT">Lithuania</option>
                        <option <?php if ($b_country=='LU'){ echo 'selected="selected"'; } ?> value="LU">Luxembourg</option>
                        <option <?php if ($b_country=='MO'){ echo 'selected="selected"'; } ?> value="MO">Macau SAR China</option>
                        <option <?php if ($b_country=='MK'){ echo 'selected="selected"'; } ?> value="MK">Macedonia</option>
                        <option <?php if ($b_country=='MG'){ echo 'selected="selected"'; } ?> value="MG">Madagascar</option>
                        <option <?php if ($b_country=='MW'){ echo 'selected="selected"'; } ?> value="MW">Malawi</option>
                        <option <?php if ($b_country=='MY'){ echo 'selected="selected"'; } ?> value="MY">Malaysia</option>
                        <option <?php if ($b_country=='MV'){ echo 'selected="selected"'; } ?> value="MV">Maldives</option>
                        <option <?php if ($b_country=='ML'){ echo 'selected="selected"'; } ?> value="ML">Mali</option>
                        <option <?php if ($b_country=='MT'){ echo 'selected="selected"'; } ?> value="MT">Malta</option>
                        <option <?php if ($b_country=='MH'){ echo 'selected="selected"'; } ?> value="MH">Marshall Islands</option>
                        <option <?php if ($b_country=='MQ'){ echo 'selected="selected"'; } ?> value="MQ">Martinique</option>
                        <option <?php if ($b_country=='MR'){ echo 'selected="selected"'; } ?> value="MR">Mauritania</option>
                        <option <?php if ($b_country=='MU'){ echo 'selected="selected"'; } ?> value="MU">Mauritius</option>
                        <option <?php if ($b_country=='YT'){ echo 'selected="selected"'; } ?> value="YT">Mayotte</option>
                        <option <?php if ($b_country=='MX'){ echo 'selected="selected"'; } ?> value="MX">Mexico</option>
                        <option <?php if ($b_country=='FM'){ echo 'selected="selected"'; } ?> value="FM">Micronesia</option>
                        <option <?php if ($b_country=='MI'){ echo 'selected="selected"'; } ?> value="MI">Midway Islands</option>
                        <option <?php if ($b_country=='MD'){ echo 'selected="selected"'; } ?> value="MD">Moldova</option>
                        <option <?php if ($b_country=='MC'){ echo 'selected="selected"'; } ?> value="MC">Monaco</option>
                        <option <?php if ($b_country=='MN'){ echo 'selected="selected"'; } ?> value="MN">Mongolia</option>
                        <option <?php if ($b_country=='ME'){ echo 'selected="selected"'; } ?> value="ME">Montenegro</option>
                        <option <?php if ($b_country=='MS'){ echo 'selected="selected"'; } ?> value="MS">Montserrat</option>
                        <option <?php if ($b_country=='MA'){ echo 'selected="selected"'; } ?> value="MA">Morocco</option>
                        <option <?php if ($b_country=='MZ'){ echo 'selected="selected"'; } ?> value="MZ">Mozambique</option>
                        <option <?php if ($b_country=='MM'){ echo 'selected="selected"'; } ?> value="MM">Myanmar</option>
                        <option <?php if ($b_country=='NA'){ echo 'selected="selected"'; } ?> value="NA">Namibia</option>
                        <option <?php if ($b_country=='NR'){ echo 'selected="selected"'; } ?> value="NR">Nauru</option>
                        <option <?php if ($b_country=='NP'){ echo 'selected="selected"'; } ?> value="NP">Nepal</option>
                        <option <?php if ($b_country=='NL'){ echo 'selected="selected"'; } ?> value="NL">Netherlands</option>
                        <option <?php if ($b_country=='AN'){ echo 'selected="selected"'; } ?> value="AN">Netherlands Antilles</option>
                        <option <?php if ($b_country=='NT'){ echo 'selected="selected"'; } ?> value="NT">Neutral Zone</option>
                        <option <?php if ($b_country=='NC'){ echo 'selected="selected"'; } ?> value="NC">New Caledonia</option>
                        <option <?php if ($b_country=='NZ'){ echo 'selected="selected"'; } ?> value="NZ">New Zealand</option>
                        <option <?php if ($b_country=='NI'){ echo 'selected="selected"'; } ?> value="NI">Nicaragua</option>
                        <option <?php if ($b_country=='NE'){ echo 'selected="selected"'; } ?> value="NE">Niger</option>
                        <option <?php if ($b_country=='NG'){ echo 'selected="selected"'; } ?> value="NG">Nigeria</option>
                        <option <?php if ($b_country=='NU'){ echo 'selected="selected"'; } ?> value="NU">Niue</option>
                        <option <?php if ($b_country=='NF'){ echo 'selected="selected"'; } ?> value="NF">Norfolk Island</option>
                        <option <?php if ($b_country=='KP'){ echo 'selected="selected"'; } ?> value="KP">North Korea</option>
                        <option <?php if ($b_country=='MP'){ echo 'selected="selected"'; } ?> value="MP">Northern Mariana Islands</option>
                        <option <?php if ($b_country=='NO'){ echo 'selected="selected"'; } ?> value="NO">Norway</option>
                        <option <?php if ($b_country=='OM'){ echo 'selected="selected"'; } ?> value="OM">Oman</option>
                        <option <?php if ($b_country=='QO'){ echo 'selected="selected"'; } ?> value="QO">Outlying Oceania</option>
                        <option <?php if ($b_country=='PC'){ echo 'selected="selected"'; } ?> value="PC">Pacific Islands Trust Territory</option>
                        <option <?php if ($b_country=='PK'){ echo 'selected="selected"'; } ?> value="PK">Pakistan</option>
                        <option <?php if ($b_country=='PW'){ echo 'selected="selected"'; } ?> value="PW">Palau</option>
                        <option <?php if ($b_country=='PS'){ echo 'selected="selected"'; } ?> value="PS">Palestinian Territory</option>
                        <option <?php if ($b_country=='PA'){ echo 'selected="selected"'; } ?> value="PA">Panama</option>
                        <option <?php if ($b_country=='PZ'){ echo 'selected="selected"'; } ?> value="PZ">Panama Canal Zone</option>
                        <option <?php if ($b_country=='PG'){ echo 'selected="selected"'; } ?> value="PG">Papua New Guinea</option>
                        <option <?php if ($b_country=='PY'){ echo 'selected="selected"'; } ?> value="PY">Paraguay</option>
                        <option <?php if ($b_country=='PE'){ echo 'selected="selected"'; } ?> value="PE">Peru</option>
                        <option <?php if ($b_country=='PH'){ echo 'selected="selected"'; } ?> value="PH">Philippines</option>
                        <option <?php if ($b_country=='PN'){ echo 'selected="selected"'; } ?> value="PN">Pitcairn</option>
                        <option <?php if ($b_country=='PL'){ echo 'selected="selected"'; } ?> value="PL">Poland</option>
                        <option <?php if ($b_country=='PT'){ echo 'selected="selected"'; } ?> value="PT">Portugal</option>
                        <option <?php if ($b_country=='PR'){ echo 'selected="selected"'; } ?> value="PR">Puerto Rico</option>
                        <option <?php if ($b_country=='QA'){ echo 'selected="selected"'; } ?> value="QA">Qatar</option>
                        <option <?php if ($b_country=='RE'){ echo 'selected="selected"'; } ?> value="RE">Reunion</option>
                        <option <?php if ($b_country=='RO'){ echo 'selected="selected"'; } ?> value="RO">Romania</option>
                        <option <?php if ($b_country=='RU'){ echo 'selected="selected"'; } ?> value="RU">Russia</option>
                        <option <?php if ($b_country=='RW'){ echo 'selected="selected"'; } ?> value="RW">Rwanda</option>
                        <option <?php if ($b_country=='BL'){ echo 'selected="selected"'; } ?> value="BL">Saint Barthélemy</option>
                        <option <?php if ($b_country=='SH'){ echo 'selected="selected"'; } ?> value="SH">Saint Helena</option>
                        <option <?php if ($b_country=='KN'){ echo 'selected="selected"'; } ?> value="KN">Saint Kitts and Nevis</option>
                        <option <?php if ($b_country=='LC'){ echo 'selected="selected"'; } ?> value="LC">Saint Lucia</option>
                        <option <?php if ($b_country=='MF'){ echo 'selected="selected"'; } ?> value="MF">Saint Martin</option>
                        <option <?php if ($b_country=='PM'){ echo 'selected="selected"'; } ?> value="PM">Saint Pierre and Miquelon</option>
                        <option <?php if ($b_country=='VC'){ echo 'selected="selected"'; } ?> value="VC">Saint Vincent and the Grenadines</option>
                        <option <?php if ($b_country=='WS'){ echo 'selected="selected"'; } ?> value="WS">Samoa</option>
                        <option <?php if ($b_country=='SM'){ echo 'selected="selected"'; } ?> value="SM">San Marino</option>
                        <option <?php if ($b_country=='ST'){ echo 'selected="selected"'; } ?> value="ST">Sao Tome and Principe</option>
                        <option <?php if ($b_country=='SA'){ echo 'selected="selected"'; } ?> value="SA">Saudi Arabia</option>
                        <option <?php if ($b_country=='SN'){ echo 'selected="selected"'; } ?> value="SN">Senegal</option>
                        <option <?php if ($b_country=='RS'){ echo 'selected="selected"'; } ?> value="RS">Serbia</option>
                        <option <?php if ($b_country=='CS'){ echo 'selected="selected"'; } ?> value="CS">Serbia and Montenegro</option>
                        <option <?php if ($b_country=='SC'){ echo 'selected="selected"'; } ?> value="SC">Seychelles</option>
                        <option <?php if ($b_country=='SL'){ echo 'selected="selected"'; } ?> value="SL">Sierra Leone</option>
                        <option <?php if ($b_country=='SG'){ echo 'selected="selected"'; } ?> value="SG">Singapore</option>
                        <option <?php if ($b_country=='SK'){ echo 'selected="selected"'; } ?> value="SK">Slovakia</option>
                        <option <?php if ($b_country=='SI'){ echo 'selected="selected"'; } ?> value="SI">Slovenia</option>
                        <option <?php if ($b_country=='SB'){ echo 'selected="selected"'; } ?> value="SB">Solomon Islands</option>
                        <option <?php if ($b_country=='SO'){ echo 'selected="selected"'; } ?> value="SO">Somalia</option>
                        <option <?php if ($b_country=='ZA'){ echo 'selected="selected"'; } ?> value="ZA">South Africa</option>
                        <option <?php if ($b_country=='GS'){ echo 'selected="selected"'; } ?> value="GS">South Georgia and the South Sandwich Islands</option>
                        <option <?php if ($b_country=='KR'){ echo 'selected="selected"'; } ?> value="KR">South Korea</option>
                        <option <?php if ($b_country=='ES'){ echo 'selected="selected"'; } ?> value="ES">Spain</option>
                        <option <?php if ($b_country=='LK'){ echo 'selected="selected"'; } ?> value="LK">Sri Lanka</option>
                        <option <?php if ($b_country=='SD'){ echo 'selected="selected"'; } ?> value="SD">Sudan</option>
                        <option <?php if ($b_country=='SR'){ echo 'selected="selected"'; } ?> value="SR">Suriname</option>
                        <option <?php if ($b_country=='SJ'){ echo 'selected="selected"'; } ?> value="SJ">Svalbard and Jan Mayen</option>
                        <option <?php if ($b_country=='SZ'){ echo 'selected="selected"'; } ?> value="SZ">Swaziland</option>
                        <option <?php if ($b_country=='SE'){ echo 'selected="selected"'; } ?> value="SE">Sweden</option>
                        <option <?php if ($b_country=='CH'){ echo 'selected="selected"'; } ?> value="CH">Switzerland</option>
                        <option <?php if ($b_country=='SY'){ echo 'selected="selected"'; } ?> value="SY">Syria</option>
                        <option <?php if ($b_country=='TW'){ echo 'selected="selected"'; } ?> value="TW">Taiwan</option>
                        <option <?php if ($b_country=='TJ'){ echo 'selected="selected"'; } ?> value="TJ">Tajikistan</option>
                        <option <?php if ($b_country=='TZ'){ echo 'selected="selected"'; } ?> value="TZ">Tanzania</option>
                        <option <?php if ($b_country=='TH'){ echo 'selected="selected"'; } ?> value="TH">Thailand</option>
                        <option <?php if ($b_country=='TL'){ echo 'selected="selected"'; } ?> value="TL">Timor Leste</option>
                        <option <?php if ($b_country=='TG'){ echo 'selected="selected"'; } ?> value="TG">Togo</option>
                        <option <?php if ($b_country=='TK'){ echo 'selected="selected"'; } ?> value="TK">Tokelau</option>
                        <option <?php if ($b_country=='TO'){ echo 'selected="selected"'; } ?> value="TO">Tonga</option>
                        <option <?php if ($b_country=='TT'){ echo 'selected="selected"'; } ?> value="TT">Trinidad and Tobago</option>
                        <option <?php if ($b_country=='TN'){ echo 'selected="selected"'; } ?> value="TN">Tunisia</option>
                        <option <?php if ($b_country=='TR'){ echo 'selected="selected"'; } ?> value="TR">Turkey</option>
                        <option <?php if ($b_country=='TM'){ echo 'selected="selected"'; } ?> value="TM">Turkmenistan</option>
                        <option <?php if ($b_country=='TC'){ echo 'selected="selected"'; } ?> value="TC">Turks and Caicos Islands</option>
                        <option <?php if ($b_country=='TV'){ echo 'selected="selected"'; } ?> value="TV">Tuvalu</option>
                        <option <?php if ($b_country=='PU'){ echo 'selected="selected"'; } ?> value="PU">U.S. Miscellaneous Pacific Islands</option>
                        <option <?php if ($b_country=='VI'){ echo 'selected="selected"'; } ?> value="VI">U.S. Virgin Islands</option>
                        <option <?php if ($b_country=='UG'){ echo 'selected="selected"'; } ?> value="UG">Uganda</option>
                        <option <?php if ($b_country=='UA'){ echo 'selected="selected"'; } ?> value="UA">Ukraine</option>
                        <option <?php if ($b_country=='AE'){ echo 'selected="selected"'; } ?> value="AE">United Arab Emirates</option>
                        <option <?php if ($b_country=='GB'){ echo 'selected="selected"'; } ?> value="GB">United Kingdom</option>
                        <option <?php if ($b_country=='US'){ echo 'selected="selected"'; } ?> value="US">United States</option>
                        <option <?php if ($b_country=='UM'){ echo 'selected="selected"'; } ?> value="UM">United States Minor Outlying Islands</option>
                        <option <?php if ($b_country=='UY'){ echo 'selected="selected"'; } ?> value="UY">Uruguay</option>
                        <option <?php if ($b_country=='UZ'){ echo 'selected="selected"'; } ?> value="UZ">Uzbekistan</option>
                        <option <?php if ($b_country=='VU'){ echo 'selected="selected"'; } ?> value="VU">Vanuatu</option>
                        <option <?php if ($b_country=='VA'){ echo 'selected="selected"'; } ?> value="VA">Vatican</option>
                        <option <?php if ($b_country=='VE'){ echo 'selected="selected"'; } ?> value="VE">Venezuela</option>
                        <option <?php if ($b_country=='VN'){ echo 'selected="selected"'; } ?> value="VN">Vietnam</option>
                        <option <?php if ($b_country=='WK'){ echo 'selected="selected"'; } ?> value="WK">Wake Island</option>
                        <option <?php if ($b_country=='WF'){ echo 'selected="selected"'; } ?> value="WF">Wallis and Futuna</option>
                        <option <?php if ($b_country=='EH'){ echo 'selected="selected"'; } ?> value="EH">Western Sahara</option>
                        <option <?php if ($b_country=='YE'){ echo 'selected="selected"'; } ?> value="YE">Yemen</option>
                        <option <?php if ($b_country=='ZM'){ echo 'selected="selected"'; } ?> value="ZM">Zambia</option>
                        <option <?php if ($b_country=='ZW'){ echo 'selected="selected"'; } ?> value="ZW">Zimbabwe</option>
                      </select>
                    </div>
                    <div class="clr"></div>
                    <div class="evField" style="width:190px">Address<font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <input type="text" class="inp" name="b_address" value="<?php echo $b_address; ?>" style="width:306px">
                    </div>
                    <div class="clr"></div>
                    <!-- <div class="evField" style="width:190px">Address</div>
                  <div class="evLabal">
                 <input type="text" class="inp" style="width:306px">
                  </div>
                  <div class="clr"></div>-->
                    <div class="evField" style="width:190px">City <font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <input type="text" maxlength="100" class="inp" id="eventname"  name="b_city" value="<?php echo $b_city; ?>" style="width:300px" />
                    </div>
                    <div class="clr"></div>
                    <div class="evField" style="width:190px"> State / Province <font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <input type="text" maxlength="100" class="inp" id="eventname"  name="b_state" value="<?php echo $b_state; ?>" style="width:300px" />
                    </div>
                    <div class="clr"></div>
                    <div>
                      <h3> <u>Payment Information</u> </h3>
                    </div>
                    <div class="evField" style="width:190px">Name of Cardholder <font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <input type="text" maxlength="100" class="inp" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
                    </div>
                    <div class="clr"></div>
                    <div class="evField" style="width:190px">Credit Card Type<font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <select class="inp" style="width:306px" name="cardType">
                        <option value="">--Select--</option>
                        <option value="visa" <?php if($bc_card_type=="visa"){ echo 'selected="selected"'; }?>>Visa</option>
                        <option value="mastercard"<?php if($bc_card_type=="mastercard"){ echo 'selected="selected"'; }?>>MasterCard</option>
                        <option value="AMEX"<?php if($bc_card_type=="AMEX"){ echo 'selected="selected"'; }?>>AMEX</option>
                        <option value="jcb"<?php if($bc_card_type=="jcb"){ echo 'selected="selected"'; }?>>JCB</option>
                      </select>
                    </div>
                    <div class="clr"></div>
                    <div class="evField" style="width:190px">Expiration date (month/year)<font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <select name="month" class="inp2" style="width:148px" id="dalete1">
                        <option value="">Select</option>
                        <?php
	  for($i=1;$i<13;$i++){
		?>
                        <option value="<?php echo $i; ?>" <?php if($bc_month==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
                        <?php } ?>
                      </select>
                      &nbsp;
                      <select name="year" class="inp2" style="width:148px" id="delete2">
                        <option value="">Select</option>
                        <?php
		for($i=date('Y');$i<date('Y')+10;$i++){
		?>
                        <option value="<?php echo $i; ?>" <?php if($bc_year==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="clr"></div>
                    <div class="evField" style="width:190px">Credit Card Number <font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <input type="text" maxlength="100" class="inp" id="eventname"  name="number" value="<?php // echo $bc_card_number; ?>5105105105105100" style="width:300px" />
                    </div>
                    <div class="clr"></div>
                    <div class="evField" style="width:190px">Security Code <font color="#FF0000">*</font></div>
                    <div class="evLabal">
                      <input type="text" maxlength="100" class="inp" id="eventname"  name="securityCode" value="<?php echo $bc_securityCode; ?>" style="width:300px" />
                    </div>
                    <div class="clr"></div>
                    <div class="ev_submit">
                      <input type="image" src="<?php echo IMAGE_PATH; ?>submit_order.gif" name="submit" value="submit" />
                      <input type="hidden" name="submit" value="submit">
                    </div>
                  </div>
                </form>
                <?php
				if($record!=0){?>
                <div class="pay" id="payDiv" <?php if ($_REQUEST['submit']){ echo "style='display:none'"; } ?>>
                  <div class="ev_fltlft">
                  <!--  <form method="post" action="paypal.php">
                      <input type="hidden" name="event_id" value="<?php echo $_POST['event_id']; ?>" />
                      <input type="image" src="<?php echo IMAGE_PATH; ?>paypal_button.png" id="" value="paypal" name="paypal" />
                      <input type="hidden" value="paypal" name="paypal"  />
                    </form>-->
                  </div>
                  <div class="ev_fltlft"><img src="<?php echo IMAGE_PATH; ?>payNow.gif" id="payNowButton" /> </div>
                  <div class="clr"></div>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>
