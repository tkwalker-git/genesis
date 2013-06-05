<?php

	if ( isset($_POST['continue']) )
	{
		
		$subscription_type 	=	DBin($_POST['subscription_type']);
		
		
		
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			
			
			
			
			
			
			
			
	$sql = "update patients set subscription_type='$subscription_type' where id='". $member_id ."'";
			if ( mysql_query($sql) ){
				echo "<script>window.location.href='?p=subscriptions'</script>";
				}
		}	
			
	}
	
	if(isset($_GET["cancel"])){
	
		$sql = "update patients set subscription_type='' where id='". $member_id ."'";
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
		
		if($subscription_type == 1)
			$checked_subscription_type_1 = 'checked="checked"';
		
		if($subscription_type == 2)
			$checked_subscription_type_2 = 'checked="checked"';
			
		if($subscription_type == 3)
			$checked_subscription_type_3 = 'checked="checked"';
		
		
	}
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




<div class="yellow_bar"> &nbsp; UPDATE YOUR PROFILE</div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error"><?php echo $err; ?></div>
	

	<div class="ew-heading">Update Billing</div>

	<form action="" method="post" name='profrm' enctype="multipart/form-data">

       <div class="clr"></div>
    <div class="editProox">
      <div class="evField">Name on Card</div>
      <div class="evLabal">
        <input type="text" name="fname" class="evInput" style="width:300px; height:20px" value='<?php echo $name;?>' />
      </div>
      <div class="clr"></div>
      
      <div class="evField">Card Number</div>
      <div class="evLabal">
        <input type="text" name="lname" class="evInput" style="width:300px; height:20px" value='<?php echo $lname;?>' />
      </div>
      <div class="clr"></div>
      
      	<div class="evField">Exp. Date</div>
      <div class="evLabal">
	  	<select style="width:100px">
			<option selected="selected">Month</option>
			<option >Jan</option>
			<option >Feb</option>
			<option >Mar</option>
		</select>
		
        <select style="width:100px">
			<option selected="selected">Year</option>
			<option >2013</option>
			<option >2014</option>
		</select>
      </div>      
      <div class="clr"></div>
      
      <div class="evField">CVV</div>
      <div class="evLabal">
        <input type="text" name="phone" class="evInput" style="width:300px; height:20px" value='<?php echo $phone;?>'/>
      </div>      
      <div class="clr"></div>
	
	</form>
</div>