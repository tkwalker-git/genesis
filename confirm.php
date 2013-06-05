<?php 
require_once('admin/database.php'); 
include_once('facebook.php');

include_once('includes/header.php'); 




if($_GET['v']){

	$v = base64_decode($_GET['v']);
	$a = explode("&", $v);
	$b = explode("=", $a[0]);	
	$id = $b[1];
	
	$c = explode("=", $a[1]);
	$email = $c[1];
	
	if($b[0]=='id' && $c[0]=='email'){
	
		$res = mysql_query("select * from `subcribe_nba` where `id`='$id' && `email`='$email'");
		if(mysql_num_rows($res)){
		$status	= getSingleColumn('status',"select * from `subcribe_nba` where `id`='$id'");
			if($status==0){
				$res = mysql_query("UPDATE `subcribe_nba` SET `status` = '1' WHERE `id` = '$id'");
				if($res)
					$msg = "Successfully verified your email address";
					else
						$msg = "<strong>ERROR:</strong> Try again later";
			}
			else
				$msg = "This email is already verified";
		}
		else
			$msg = "<strong>ERROR:</strong> Try again later";
	}
	else{
		$msg = "<strong>ERROR:</strong> Try again later";
	}
}

?>
<style>

	.addEInput
	{
		width:225px!important;
		height:30px!important;
	}
	
	

</style>

<div id="main">
<div id="login-main">
				
					<div id="login-main-top"></div>
						<div id="login-main-middle">
							<div id="login-head">
								<div id="heading"></div>
								
							</div><!--login-head -->
							<!--<div id="shadow"></div>-->
							<div class="error"><?php echo $err; ?></div>
							<div id="fb-root"></div>
							
								<div id="form" style="font-size:15px; text-align:center"><br>
									<br>
									<?php
									if($msg){
									echo "<strong>".$msg."</strong>";
									}
									else{?>
								<strong>Your Account is Cancelled successfully</strong>
								<?php } ?>
									<br>
									<br>
									<br>
									<br>
									<br>
									<br>
									<br>
									&nbsp;
										
								</div>
							</div><!-- login-main-middle-->
					<div id="login-main-bottom"></div>	
				
			</div><!-- login-main -->
</div>
<div class="clr"><br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
<?php 
/*if(isset($_SESSION['logedin']) && $_SESSION['usertype'] != ''){
	header ("Location: view_events.php");
}*/
?>
<?php include_once('includes/footer.php'); ?>		