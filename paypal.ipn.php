<?php

session_start();
//echo $_SESSION['LOGIN_SESSION_ID'];
require_once('admin/database.php');
include_once('paypal.inc.php');

$paypal=new paypal();
/// optionally enable logging
// $paypal->log=1;
// $paypal->logfile='/absolute/path/to/logfile.txt';

print_r($_POST);
if($paypal->validate_ipn())
{
echo $paypal->payment_success;

    if($paypal->payment_success==1)
    {
       print_r($paypal->posted_data);
		//payment is successfull
        //use the item id to identify for which product the payment was made 
        $id=intval($paypal->posted_data['item_number']);
		$trans_id	=	$paypal->posted_data['txn_id'];
		
		$a = $paypal->posted_data['payment_date'];
		$todayDateTime = date("Y-m-d H:i:s",strtotime($a));
		$paidAmount = number_format($paypal->posted_data['mc_gross'] , 2 , "." , " ");
		




mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, 'PayPal Insert Transactions', '0', '0')");






	//	$insertTransactions = "INSERT INTO transactions (`customer_id` , `transaction_id` , `payment_type` , `amount` , `date`) VALUES ('". $paypal->posted_data['custom'] ."' , '". $trans_id ."' , '". $paypal->posted_data['item_name'] ."' , '". $paidAmount ."' , '". $todayDateTime ."'  )";
	//	$save_transction = mysql_query($insertTransactions);
//		
//		if($save_transction):
//		
//			$last_credit = attribValue("credit","credit","WHERE customer_id = '". $paypal->posted_data['custom'] ."' ");
//			$customer_id = attribValue("credit","customer_id","WHERE customer_id = '". $paypal->posted_data['custom'] ."' ");
//			
//			if($customer_id != ""):
//			
//				$credit_update = $last_credit + $paidAmount;
//				mysql_query("UPDATE credit SET credit = '". $credit_update ."' WHERE customer_id = '". $paypal->posted_data['custom'] ."' ");
//				echo $trans_id;
//				
//			else:
//			
//				$last_credit = 0.00;
//				$credit_update = $last_credit + $paidAmount;
//				mysql_query("INSERT INTO credit (`customer_id` , `credit`) VALUES ('". $paypal->posted_data['custom'] ."' , '". $credit_update ."') ");
//				//echo $trans_id;
//			endif;
//			
//		endif; //end if $save_transction
    }
    else
    {
        echo "not_success";//payment not successful and/or subcsription cancelled
    }
}
else
{
	echo "ipn_error"; //not valid PIPN  log

}
