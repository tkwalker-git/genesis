<?php

	if ( isset($_POST['continue']) )
	{
		
		$subscription_type 	=	DBin($_POST['subscription_type']);
		
		echo $card_number		=	DBin($_POST['card_number']);
		$fname_oncard		=	DBin($_POST['fname_oncard']);
		$lname_oncard		=	DBin($_POST['lname_oncard']);
		$exp_mon			=	DBin($_POST['exp_mon']);
		$exp_year			=	DBin($_POST['exp_year']);
		$exp_date			=	$exp_year.'-'.$exp_mon;
		$cvv				=	DBin($_POST['cvv']);
		$address			=	DBin($_POST['address']);
		$city				=	DBin($_POST['city']);
		$state				=	DBin($_POST['state']);
		
		$product_name				=	attribValue("subsc_packages" , "name" , "where id = '". $subscription_type ."'");
		$product_price				=	attribValue("subsc_packages" , "price" , "where id = '". $subscription_type ."'");
		$subscrId					=	attribValue("patients" , "auth_subscription_id" , "where id = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
		
		if($card_number)
			$errors[] = "Card Number is empty!";
			
		if($fname_oncard)
			$errors[] = "First Name on card is empty!";
			
		if($lname_oncard)
			$errors[] = "Last Name on card is empty!";
			
		if($exp_mon == '' || $exp_year == '' )
			$errors[] = "Please set Expiry date!";
			
		if($address == '')
			$errors[] = "Address is empty!";
			
		if($city == '')
			$errors[] = "City is empty!";
			
		if($state == '')
			$errors[] = "State is empty!";
		
		
		
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			
			
			require('includes/AuthnetARB.class.php');
			try
			{

				$subscription = new AuthnetARB('48zC2wJr2S8p', '69Cx7SKRf4bJ826V', AuthnetARB::USE_DEVELOPMENT_SERVER);
				$subscription->setParameter('amount', $product_price);
				$subscription->setParameter('cardNumber', $card_number);
				$subscription->setParameter('expirationDate', $exp_date);
				$subscription->setParameter('firstName', $fname_oncard);
				$subscription->setParameter('lastName', $lname_oncard);
				$subscription->setParameter('address', $address);
				$subscription->setParameter('city', $city);
				$subscription->setParameter('state', $state);
				$subscription->setParameter('zip', '38000');
				$subscription->setParameter('subscrName', str_replace("&" , "and" , $product_name));
				
				$subscription->setParameter('subscrId', $subscrId);

				// update the subscription
				$subscription->updateAccount();
				
				//echo $subscription->getResponse();
				
				// Check the results of our API call
				
				if ($subscription->isSuccessful())
				{
					$sql = "update patients set subscription_type='$subscription_type' where id='". $member_id ."'";
					$res = mysql_query($sql);
					if($res)
						$err = "Subscruption udated successfully";
					
					}
				else
				{
					// The subscription was not created!
					$err = "Subscription does not Updated";
					
					$excptn = new AuthnetARBException();
					echo $excptn;
				}
			}
			catch (AuthnetARBException $e)
			{
				$err = $e;
				$err .= $subscription;
			} // catch

				
			
			
			
			
			
			
					
					
		}	
			
	}
	
	if(isset($_GET["cancel"])){
	
		
			if ( mysql_query($sql) )
				echo "<script>window.location.href='?p=subscriptions'</script>";
	}		
		
	
	
	$checked_subscription_type_1 = '';
	$checked_subscription_type_2 = '';
	$checked_subscription_type_2 = '';
	$sql = "select `subscription_type` from `patients` where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$subscription_type = $row["subscription_type"];
		
		
		
		
	}
	
$arr_state	=	array();
$arrRES = mysql_query("select id,state from usstates");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$arr_state[$bc_row["id"]] = $bc_row["state"];



?>
<style>
.whiteMiddle .evField {
	
	}

.whiteMiddle .evField {
	text-align:left;
	font-size:15px;
	width:134px;
	}
	
.evLabal{
	font-size:15px;
	}
	
.evInput{
	font-size:14px;
	}
	
</style>

<style type="text/css">
.ew-heading{
	color: #49BA8D;
    font-size: 24px;}
	
.ew-heading a{
	color: #FF7A57;
    float: right;
    font-size: 14px;
	text-decoration:underline;}

.ew-heading-behind{
	color: #6EB432;
    font-size: 24px;}

.ew-heading-behind span{}

.ew-heading-a{
	color: #212121;
    font-size: 20px;}
</style>




<div class="yellow_bar"> &nbsp; UPDATE YOUR SUBSCRIPTION</div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error" style="color:red; font-size:18px; text-align:center;"><?php echo $err; ?></div>
	

	<div class="ew-heading">Subscriptions <a href="?p=patient-profile&cancel_subscription=1">Cancel Subscriptions</a></div>

	<form action="" method="post" name='profrm' enctype="multipart/form-data">

      <div class="clr"></div>
    <div class="editProox">
      
     
    	<div style="padding: 20px 45px; margin-bottom:25px">
    
            <?php 
				$sql_subsc = "SELECT * FROM subsc_packages WHERE `status` = 1";
				$res_subsc = mysql_query($sql_subsc);
				$cou_subsc = mysql_num_rows($res_subsc);
				$subsc_row = 1;
				while($subsc = mysql_fetch_assoc($res_subsc)){
					
					if($subsc_row == $cou_subsc)
						$subsc_text = "Subcribe To Both";
					else
						$subsc_text = "Subcribe To";
						
					if($subsc_row == $subscription_type)
						$subsc_checked = 'checked="checked"';
					else
						$subsc_checked = '';
			?>
        		<div style="line-height:30px; width:75%; float:left">
                	<input style="margin:0 5px;" type="radio" name="subscription_type" value="<?php echo  $subsc["id"] ; ?>"  <?php echo $subsc_checked; ?> />
                    <strong><?php echo $subsc_text .' '. $subsc["name"] ; ?></strong>
                </div>
        		<div style="line-height:30px; width:20%; float:left; color:#990000;">$<?php echo  $subsc["price"] ; ?>/Month</div>
            <?php $subsc_row++; } ?>
            	
        	</div>
            
            <div class="clr"></div>
            <div class="editProox" style="padding:0 50px;">
              <div class="evField">Card Number</div>
              <div class="evLabal" style="width:450px;">
                <input type="text" name="card_number" class="evInput" style="width:300px; height:20px" value="<?php echo $card_number;?>" />
              </div>
              <div class="clr"></div>
              
              <div class="evField">First Name on Card</div>
              <div class="evLabal" style="width:450px;">
                <input type="text" name="fname_oncard" class="evInput" style="width:300px; height:20px" value="<?php echo $fname_oncard;?>" />
              </div>
              <div class="clr"></div>
              
              <div class="evField">Last Name on Card</div>
              <div class="evLabal" style="width:450px;">
                <input type="text" name="lname_oncard" class="evInput" style="width:300px; height:20px" value="<?php echo $lname_oncard;?>" />
              </div>
              <div class="clr"></div>
              
                <div class="evField">Exp. Date</div>
              <div class="evLabal" style="width:450px;">
                <select class="new_input" style="width:100px" name="exp_mon">
                    <option selected="selected">Month</option>
                    <option <?php if($exp_mon==1){?> selected="selected" <?php } ?>value="1">1 - Jan</option>
                    <option <?php if($exp_mon==2){?> selected="selected" <?php } ?>value="2">2 - Feb</option>
                    <option <?php if($exp_mon==3){?> selected="selected" <?php } ?>value="3">3 - Mar</option>
                    <option <?php if($exp_mon==4){?> selected="selected" <?php } ?>value="4">4 - Apr</option>
                    <option <?php if($exp_mon==5){?> selected="selected" <?php } ?>value="5">5 - May</option>
                    <option <?php if($exp_mon==6){?> selected="selected" <?php } ?>value="6">6 - Jun</option>
                    <option <?php if($exp_mon==7){?> selected="selected" <?php } ?>value="7">7 - Jul</option>
                    <option <?php if($exp_mon==8){?> selected="selected" <?php } ?>value="8">8 - Aug</option>
                    <option <?php if($exp_mon==9){?> selected="selected" <?php } ?>value="9">9 - Sep</option>
                    <option <?php if($exp_mon==10){?> selected="selected" <?php } ?>value="10">10 - Oct</option>
                    <option <?php if($exp_mon==11){?> selected="selected" <?php } ?>value="11">11 - Nov</option>
                    <option <?php if($exp_mon==12){?> selected="selected" <?php } ?>value="12">12 - Dec</option>
                </select>
                
                <select class="new_input" style="width:100px" name="exp_year">
                    <option selected="selected">Year</option>
                    <option <?php if($exp_year==2013){?> selected="selected" <?php } ?>value="2013">2013</option>
                    <option <?php if($exp_year==2014){?> selected="selected" <?php } ?>value="2014">2014</option>
                    <option <?php if($exp_year==2015){?> selected="selected" <?php } ?>value="2015">2015</option>
                    <option <?php if($exp_year==2016){?> selected="selected" <?php } ?>value="2016">2016</option>
                    <option <?php if($exp_year==2017){?> selected="selected" <?php } ?>value="2017">2017</option>
                    <option <?php if($exp_year==2018){?> selected="selected" <?php } ?>value="2018">2018</option>
                    <option <?php if($exp_year==2019){?> selected="selected" <?php } ?>value="2019">2019</option>
                    <option <?php if($exp_year==2020){?> selected="selected" <?php } ?>value="2020">2020</option>
                    <option <?php if($exp_year==2021){?> selected="selected" <?php } ?>value="2021">2021</option>
                    <option <?php if($exp_year==2022){?> selected="selected" <?php } ?>value="2022">2022</option>
                </select>
              </div>      
              <div class="clr"></div>
              
              <div class="evField">CVV</div>
              <div class="evLabal" style="width:450px;">
                <input type="text" name="cvv" class="evInput" style="width:300px; height:20px" value='<?php echo $cvv;?>'/>
              </div>      
              <div class="clr"></div>
              
              <div class="evField">Address</div>
              <div class="evLabal" style="width:450px;">
                <input type="text" name="address" class="evInput" style="width:300px; height:20px" value='<?php echo $address;?>' />
              </div>
              <div class="clr"></div>
              
              <div class="evField">City</div>
              <div class="evLabal" style="width:450px;">
                <input type="text" name="city" class="evInput" style="width:300px; height:20px" value='<?php echo $city;?>' />
              </div>
              <div class="clr"></div>
              
              
               <div class="evField">State</div>
              <div class="evLabal" style="width:450px;">
                <select style="width:200px" class="new_input" name="state">
                <option value="">Select State</option>
                <?php
                foreach($arr_state as $key => $val)
                {
                if ($key == $bc_clinic_state)
                $sel = "selected";
                else
                $sel = "";
                ?>
                <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo ucwords(strtolower($val)); ?> </option>
                <?php } ?>
                </select>
            
            	</div>
              <div class="clr"></div>
            
            
            </div>
 
	<div align="right"><br /><br />
		
		<input type="image" src="<?php echo IMAGE_PATH; ?>save_&_continue.png" align="right" name="continue" value="Save & Continue" style="padding:10px 0 0 10px;" />
		<a href="?p=patint-notifcations"><img src="<?php echo IMAGE_PATH; ?>skip.png" align="right"  style="padding:10px 0 0 10px;" /></a>
		<input type="hidden" name="continue" value="Save & Continue" />&nbsp; 
		  <br class="clr" />&nbsp;
	</div>
	
	</form>
</div>