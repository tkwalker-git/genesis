<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
if($_SESSION['order_id']!=''){
$_SESSION['order_id']='';
echo "<script>window.location.href='index.php';</script>";
exit();
}
$meta_title = "Order Tickets";
$folderName	=	time();
	
if($_REQUEST['paypal']){}

else{
	echo "Direct access to this page is not allowed.";
	exit();
	}



require_once('includes/header.php');
?>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="eventMdlBg" style="background:none">
      <div class="eventMdlMain">
        <div style="min-height:300px">
          <?php

if(isset($_POST['paypal'])){
	include_once('paypal.inc.php');
	$paypal = new paypal();
	
	$bc_event_id	=	$_POST['event_id'];
	$id				=	$_POST['id'];
	$qty			=	$_POST['qty'];
	
	$r = mysql_query("SELECT * FROM  `tickets_cart` where `user_id`='$user_id'");
	while($ro = mysql_fetch_array($r)){
	$bc_ticket_id	=	$ro['ticket_id'];
	$bc_total_price	=	$ro['total_price'];
	$bc_quantity	=	$ro['quantity'];
	$bc_date		=	$ro['date'];
	$bc_event_id	=	$ro['event_id'];
	}
	$event_name		=	getSingleColumn('event_name',"select * from `events` where id=$bc_event_id");
	$event_url = getEventURL($bc_event_id);		
	
	//	$form['cmd']			=	'_xclick';
	//	$form['business']		=	'seller_1260447824_biz@bluecomp.net';
	//	$form['cancel_return']	=	$event_url;
		$form['notify_url']		=	ABSOLUTE_PATH.'paypal.ipn.php';
	//	$form['return']			=	ABSOLUTE_PATH.'tkank_you.php';
	//	$form['currency_code']	=	'USD';
	//	$form['amount']			=	$total_price;
	//	$form['item_name']		=	$event_name;
		
		 $paypal = new paypal();
 
 //optionally disable page caching by browsers
 //$paypal->headers_nocache(); //should be called before any output
 
 //set the price
 $paypal->price= $bc_total_price;
 
 $paypal->ipn= " " .ABSOLUTE_PATH . "paypal-ipn"; //full web address to IPN script
 
 //OR one-time payment
 $paypal->enable_payment();
 
 $payer_id = $user_id;
 
 //change currency code
 $paypal->add('currency_code','USD');
 
 $paypal->add('custom', $payer_id);
 
 //your paypal email address
 $paypal->add('business', 'seller_1260447824_biz@bluecomp.net');
 
 $paypal->add('item_name', $event_name);
 
// $paypal->add('item_number','0000');
// $paypal->add('quantity',1);
 $paypal->add('return',ABSOLUTE_PATH . 'paypal.ipn.php'); //view-payment?success=1
 $paypal->add('cancel_return',$event_url);
 echo $paypal->output_form();

	}
	//end if isset($_POST['paypal'])
?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>