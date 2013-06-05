<?php require_once('admin/database.php');

/////////////////////////////////////////////////
/////////////Begin Script below//////////////////
/////////////////////////////////////////////////

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

/////////////////////////////////////

	
////////////////////////////////////
// assign posted variables to local variables
$item_name = $_POST['item_name'];
$tour_id = $_POST['custom'];  // Event Id
$paycost = $_POST['amount'];
$paycost = $_REQUEST['amount'];

$business = $_POST['business'];
$payment_status = $_POST['payment_status'];
$mc_gross = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$receiver_id = $_POST['receiver_id'];
$quantity = $_POST['quantity'];
$num_cart_items = $_POST['num_cart_items'];
$payment_date = $_POST['payment_date'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$payment_type = $_POST['payment_type'];
$payment_status = $_POST['payment_status'];
$payment_gross = $_POST['payment_gross'];
$payment_fee = $_POST['payment_fee'];
$settle_amount = $_POST['settle_amount'];
$memo = $_POST['memo'];
$payer_email = $_POST['payer_email'];
$txn_type = $_POST['txn_type'];
$payer_status = $_POST['payer_status'];
$address_street = $_POST['address_street'];
$address_city = $_POST['address_city'];
$address_state = $_POST['address_state'];
$address_zip = $_POST['address_zip'];
$address_country = $_POST['address_country'];
$address_status = $_POST['address_status'];
$tax = $_POST['tax'];
$option_name1 = $_POST['option_name1'];
$option_selection1 = $_POST['option_selection1'];
$option_name2 = $_POST['option_name2'];
$option_selection2 = $_POST['option_selection2'];
$for_auction = $_POST['for_auction'];
$invoice = $_POST['invoice'];
$custom = $_POST['custom'];
$notify_version = $_POST['notify_version'];
$verify_sign = $_POST['verify_sign'];
$payer_business_name = $_POST['payer_business_name'];
$payer_id =$_POST['payer_id'];
$mc_currency = $_POST['mc_currency'];
$mc_fee = $_POST['mc_fee'];
$exchange_rate = $_POST['exchange_rate'];
$settle_currency  = $_POST['settle_currency'];
$parent_txn_id  = $_POST['parent_txn_id'];
$pending_reason = $_POST['pending_reason'];
$reason_code = $_POST['reason_code'];


// subscription specific vars

$subscr_id = $_POST['subscr_id'];
$subscr_date = $_POST['subscr_date'];
$subscr_effective  = $_POST['subscr_effective'];
$period1 = $_POST['period1'];
$period2 = $_POST['period2'];
$period3 = $_POST['period3'];
$amount1 = $_POST['amount1'];
$amount2 = $_POST['amount2'];
$amount3 = $_POST['amount3'];
$mc_amount1 = $_POST['mc_amount1'];
$mc_amount2 = $_POST['mc_amount2'];
$mc_amount3 = $_POST['mcamount3'];
$recurring = $_POST['recurring'];
$reattempt = $_POST['reattempt'];
$retry_at = $_POST['retry_at'];
$recur_times = $_POST['recur_times'];
$username = $_POST['username'];
$password = $_POST['password'];
$dft_id = $_POST['dft_id'];
$custom = $_POST['custom'];

//auction specific vars

$for_auction = $_POST['for_auction'];
$auction_closing_date  = $_POST['auction_closing_date'];
$auction_multi_item  = $_POST['auction_multi_item'];
$auction_buyer_id  = $_POST['auction_buyer_id'];



//DB connect creds and email 
$notify_email =  "service@dubaitourplan.com";         //email address to which debug emails are sent to
//$DB_Server = "localhost"; //your MySQL Server
//$DB_Username = "londonlady"; //your MySQL User Name
//$DB_Password = "Asdf1234"; //your MySQL Password
//$DB_DBName = "londonlady"; //your MySQL Database Name

if (!$fp) {
// HTTP ERROR
} else {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);
if (strcmp ($res, "VERIFIED") == 0) {



/*//create MySQL connection
$Connect = @mysql_connect($DB_Server, $DB_Username, $DB_Password)
or die("Couldn't connect to MySQL:<br>" . mysql_error() . "<br>" . mysql_errno());


//select database
$Db = @mysql_select_db($DB_DBName, $Connect)
or die("Couldn't select database:<br>" . mysql_error(). "<br>" . mysql_errno());

*/



$fecha = date("m")."/".date("d")."/".date("Y");
$fecha = date("Y").date("m").date("d");

//check if transaction ID has been processed before
$checkquery = "select `txnid` from `paypal_payment_info` where `txnid`='".$txn_id."'";
$sihay = mysql_query($checkquery) or die("Duplicate txn id check query failed:<br>" . mysql_error() . "<br>" . mysql_errno());
$nm = mysql_num_rows($sihay);
if ($nm == 0){

//execute query
$res = mysql_query("select * from `tour_plan` where `tour_id`='$tour_id'");
while($row = mysql_fetch_array($res)){
$date = date('Y-m-d');
$title = $row['title'];
$type = $row['type'];
$time = date('H:i');
$dateFrom = $row['dateFrom'];
$dateTo = $row['dateTo'];
$user_id = $row['user_id'];
$tour_id = $row['tour_id'];
$cost = $row['cost'];
$session_key = $row['session_key'];
}
mysql_query("INSERT INTO  `tour_plan_booked` (
`booked_plan_id` ,
`date` ,
`title` ,
`type` ,
`time` ,
`dateFrom` ,
`dateTo` ,
`user_id` ,
`tour_id` ,
`cost` ,
`status`
)
VALUES (
NULL ,  '$date',  '$title',  '$type',  '$time',  '$dateFrom',  '$dateTo',  '$user_id',  '$tour_id', '$cost',  '0'
);");
$booked_plan_id = mysql_insert_id();
$planTitle = $title;
mysql_query("DELETE FROM `tour_plan` WHERE `tour_id` = '$tour_id'");
mysql_query("DELETE FROM `temp_tour_plan` WHERE `session_key` = '$session_key'");
mysql_query("DELETE FROM `temp_tour_entertainments` WHERE `session_key` = '$session_key'");
mysql_query("DELETE FROM `temp_tour_events` WHERE `session_key` = '$session_key'");
mysql_query("DELETE FROM `temp_tour_hotels` WHERE `session_key` = '$session_key'");
mysql_query("DELETE FROM `temp_tour_hotels_visit` WHERE `session_key` = '$session_key'");
mysql_query("DELETE FROM `temp_tour_malls` WHERE `session_key` = '$session_key'");
mysql_query("DELETE FROM `temp_tour_places` WHERE `session_key` = '$session_key'");

//setcookie("session_key", "", time()-3600);

$p = mysql_query("select * from `notes` where `title`='percent'");
while($s = mysql_fetch_array($p)){
$percent = $s['value'];
}
$rs = mysql_query("select * from `user` where `user_id` = '$user_id'");
while($rz = mysql_fetch_array($rs)){
$email = $rz['email'];
$username = $rz['username'];
}
$to= $email;
	$content = file("myadmin/emailtemplates/bookeduser.txt");
	$data = implode($content);
	$d = explode("</title>", $data);
	$title1 = $d[0];
	$d2 = explode("<title>", $title1);
	$title = $d2[1];
	$subject = $title;
	$paycost = $cost*$percent/100;
	$message = str_replace("%ptitle%",$planTitle, $d[1]);
	$message = str_replace("%user%",$username, $message);
	$message = str_replace("%percent%",$percent."%", $message);
	$message = str_replace("%tcost%",$cost." AED", $message);
	$message = str_replace("%pcost%",$paycost." AED", $message);
	$message = str_replace("%sdate%",date('d-M-Y', strtotime($dateFrom)), $message);
	$message = str_replace("%edate%",date('d-M-Y', strtotime($dateTo)), $message);

	
	$headers = 'From: Dubai Tour Plan <service@dubaitourplan.com>' . "\r\n" .
 'Reply-To: service@dubaitourplan.com' . "\r\n" .
 'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);

	$r = mysql_query("select * from `emailalerts`");
	while($ro = mysql_fetch_array($r)){
	$bookedTour = $ro['bookedTour'];
	$email = $ro['email'];
	}
if($bookedTour==1){
	$to= $email;
	$content = file("myadmin/emailtemplates/bookedadmin.txt");
	$data = implode($content);
	$d = explode("</title>", $data);
	$title1 = $d[0];
	$d2 = explode("<title>", $title1);
	$title = $d2[1];
	$subject = $title;
	$message = str_replace("%ptitle%",$planTitle, $d[1]);
	$message = str_replace("%user%",$username, $message);
	$message = str_replace("%percent%",$percent."%", $message);
	$message = str_replace("%tcost%",$cost." AED", $message);
	$message = str_replace("%pcost%",$paycost." AED", $message);
	$message = str_replace("%sdate%",date('d-M-Y', strtotime($dateFrom)), $message);
	$message = str_replace("%edate%",date('d-M-Y', strtotime($dateTo)), $message);
	$headers = 'From: Dubai Tour Plan <service@dubaitourplan.com>' . "\r\n" .
 'Reply-To: service@dubaitourplan.com' . "\r\n" .
 'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);
	}


mysql_query("UPDATE  `tour_plan_booked` SET  `paycost` =  '$paycost' WHERE `booked_plan_id` = '$booked_plan_id'");

$strQuery = "insert into paypal_payment_info(paymentstatus,buyer_email,firstname,lastname,street,city,state,zipcode,country,mc_gross,mc_fee,memo,paymenttype,paymentdate,txnid,pendingreason,reasoncode,tax,datecreation,user_id,tour_id) values ('".$payment_status."','".$payer_email."','".$first_name."','".$last_name."','".$address_street."','".$address_city."','".$address_state."','".$address_zip."','".$address_country."','".$mc_gross."','".$mc_fee."','".$memo."','".$payment_type."','".$payment_date."','".$txn_id."','".$pending_reason."','".$reason_code."','".$tax."','".$fecha."','".$user_id."','".$item_number."')";

     $result = mysql_query($strQuery) or die("Cart - paypal_payment_info, Query failed:<br>" . mysql_error() . "<br>" . mysql_errno());
	 }}

// if the IPN POST was 'INVALID'...do this


else if (strcmp ($res, "INVALID") == 0) {
// log for manual investigation

mail($notify_email, "INVALID IPN", "$res\n $req");
}
}
fclose ($fp);
}
?>