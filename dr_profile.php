<?php

	if ( isset($_POST['continue']) )
	{
		
		$fname 			=	DBin($_POST['fname']);
		$lname 			=	DBin($_POST['lname']);
		$email 			=	DBin($_POST['email']);
		$gender			=	DBin($_POST['gender']);
		$dob 			=	$_POST['dob'];
		
		$phone 			=	DBin($_POST['phone']);
		$address		=	DBin($_POST['address']);
		$city			=	DBin($_POST['city']);
		$zip 			=	DBin($_POST['zip']);
		
		$password 		=	DBin($_POST['password']);
		$cpassword 		=	DBin($_POST['cpassword']);
		$com_website	=	DBin($_POST['com_website']);
		
		$company_name	=	$_POST['company_name'];
		$venue_name		=	$_POST['venue_name'];
		//	$dob			=	str_replace("/","-", $dob);
		$dob 			= date("Y-m-d", strtotime($dob));

		
		if ( trim($fname) == '' )
			$errors[] = 'Please enter First Name';
		if ( trim($lname) == '' )
			$errors[] = 'Please enter Last Name';
		
		if ( trim($password) != '' || trim($cpassword) != '' ) {
			if ( trim($password) != trim($cpassword))
				$errors[] = 'Confirm Password does not match.';			
		} 
		
		if ( !validateEmail($email) ) 
			$errors[] = 'Email Address is invalid.';
			
		if ( trim($phone) == '' )
			$errors[] = 'Please enter your Phone Number';	
		
		if ( trim($address) == '' )
			$errors[] = 'Please enter your Mailing Address';	
		
		if ( trim($city) == '' )
			$errors[] = 'Please enter your Mailing City';
		
		if ( trim($zip) == '' )
			$errors[] = 'Please enter your Mailing Zipcode';
		
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			
			if ( trim($password) != '' && trim($cpassword) != '' ) 
				$pwd = "password='". $password ."',";
			else
				$pwd = "";
			
			$bc_image	= time() . "_" . $_FILES["image_name"]["name"] ;
			$bc_image	= str_replace(" ","_", $bc_image);
			if (isset($_FILES["image_name"]) && !empty($_FILES["image_name"]["tmp_name"])){
				move_uploaded_file($_FILES["image_name"]["tmp_name"], 'images/members/' .$bc_image);
					makeThumbnail($bc_image, 'images/members/', '', 275, 375,'th_');
					makeThumbnailFixWidthHeight($bc_image, 'images/members/th_', 'images/group/icon/', 77, 77);
					makeThumbnailFixWidthHeight($bc_image, 'images/members/th_', 'images/group/icon/big', 281, 280);
			//		makeRoundedThumbnail($bc_image, 'images/group/icon/', 'images/frameOver.gif');
				$sql_img = ", image_name = '$bc_image' ";
			}
			
			
			$bc_logo_image  = time() . "_" . $_FILES["logo_name"]["name"];
			$bc_logo_image	= str_replace(" ","_", $bc_logo_image);
			if (isset($_FILES["logo_name"]["name"]) && !empty($_FILES["logo_name"]["tmp_name"])){
				move_uploaded_file($_FILES["logo_name"]["tmp_name"], 'images/members/' .$bc_logo_image);
					makeThumbnail($bc_logo_image, 'images/members/', '', 275, 375,'th_');
					makeThumbnailFixWidthHeight($bc_logo_image, 'images/members/th_', 'images/group/icon/', 77, 77);
					makeThumbnailFixWidthHeight($bc_logo_image, 'images/members/th_', 'images/group/icon/big', 281, 280);
			//		makeRoundedThumbnail($bc_image, 'images/group/icon/', 'images/frameOver.gif');
				$sql_company_logo = " , company_logo = '$bc_logo_image' ";
			}
			
			
			
	$sql = "update doctors set first_name='$fname',last_name='$lname',email='$email',sex='$gender',dob='$gender' , username = '$username',".  $pwd ."dob='$dob'  where id='". $member_id ."'";
			if ( mysql_query($sql) ){
				//echo "update `clinics` set `city`='$city', `zip`='$zip', `address`='$address'  where `user_id`='".$member_id."'";
				mysql_query("update `clinics` set `city`='$city', `zip`='$zip', `address1`='$address'  where `user_id`='".$member_id."'");
				}
		}	
			
	}
	
	
	$sql = "select * from doctors where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 		= DBout($row['first_name']);
		$lname 		= DBout($row['last_name']);
		$email  	= DBout($row['email']);
		
		$sql_clinic = "SELECT * FROM clinics WHERE user_id = '". $member_id ."'";
		$res_clinic = mysql_query($sql_clinic);
		$row_clinic = mysql_fetch_assoc($res_clinic);
		
		$phone  	= DBout($row_clinic['phone1']);
		$address 	= DBout($row_clinic['address1']);
		$city 		= DBout($row_clinic['city']);
		$zip 		= DBout($row_clinic['zip']);
		$gender 	= DBout($row['sex']);
		$usern  	= DBout($row['username']);
		$dob  		= DBout($row['dob']);
		$password	= DBout($row['password']);
		$af_code	= DBout($row['affiliatemarketingcode']);
		$sub_type	= DBout($row['subscription']);
		
		if ( $dob == '0000-00-00' )
			$dob = '';
		
		if ( $dob != '' )
			$dob = date("m/d/Y",strtotime($dob));
		
		$image	= DBout($row['image_name']);
		$bc_image  = $image;
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img align="center" '. $img .' />';
		} else
			$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';
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
	

	<div class="ew-heading">Personal Information</div>

	<form action="" method="post" name='profrm' enctype="multipart/form-data">

      <div class="clr"></div>
    <div class="editProox">
      <div class="evField">First Name</div>
      <div class="evLabal">
        <input type="text" name="fname" class="evInput" style="width:300px; height:20px" value='<?php echo $name;?>' />
      </div>
      <div class="clr"></div>
      
      <div class="evField">Last Name</div>
      <div class="evLabal">
        <input type="text" name="lname" class="evInput" style="width:300px; height:20px" value='<?php echo $lname;?>' />
      </div>
      <div class="clr"></div>
      
      	<div class="evField">Email</div>
      <div class="evLabal">
        <input type="text" name="email" class="evInput" style="width:300px; height:20px" value='<?php echo $email;?>'/>
      </div>      
      <div class="clr"></div>
      
      <div class="evField">Phone Number</div>
      <div class="evLabal">
        <input type="text" name="phone" class="evInput" style="width:300px; height:20px" value='<?php echo $phone;?>'/>
      </div>      
      <div class="clr"></div>
      
  
	  <div class="evField">Birth Date</div>
      <div class="evLabal">
        <input type="text" name="dob" id="dob" readonly="true" class="evInput" style=" width:200px; height:20px"  value="<?php echo $dob;?>" />
        <script type="text/javascript" src="js/jquery-ui_1.8.7.js"></script>
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />
        <script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
        <script>
				$(function() {
					$( "#dob" ).datepicker({
											dateFormat: "mm/dd/yy",
											changeMonth: true,
											changeYear: true,
											yearRange: '1940:1995'
											});
							});
										
		</script>
      </div>
      <div class="clr"></div>
	  
	  <div class="evField">Gender</div>
      <div class="evLabal">
        <select id="gender" class="evInput" name="gender" style="padding:3px; width:200px">
          <option value="">Select Gender</option>
          <option <?php echo ( ($gender == 'Male') ? 'selected="selected"' : '') ?> value="Male" >Male</option>
          <option <?php echo ( ($gender == 'Female') ? 'selected="selected"' : '') ?> value="Female" >Female</option>
        </select>
      </div>
      <div class="clr"></div>
	  
      <div class="evField">Profile Picture</div>
      <div class="evLabal">
        <?php 
		
			if ($bc_image != '' && file_exists(DOC_ROOT . 'images/members/' . $bc_image ) ) {
					$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $bc_image,127,2000 );
				echo '<img '.$img.' align="left" style="padding-right:10px;" /><br>';
				
				}
			else
				echo '<img src="admin/images/no_image.png" class="dynamicImg"width="75" height="76" align="left" style="padding-right:10px;" />';
		 ?><br /><br />
		 <div style="font-size:15px; padding-bottom:4px">Update Your Profile Picture</div>
        <input type="file" class="addEInput" name="image_name" id="image_name"  />
      </div>
      <div class="clr"></div>
      
      <br /><br />
	

	<div class="ew-heading">Mailing Address</div>
	
	 <div class="evField">Street Address</div>
      <div class="evLabal">
        <input type="text" name="address" class="evInput" style="width:300px; height:20px" value='<?php echo $address;?>' />
      </div>
      <div class="clr"></div>
      <div class="evField">City</div>
      <div class="evLabal">
        <input type="text" name="city" class="evInput" style="width:300px; height:20px" value='<?php echo $city;?>' />
      </div>
      <div class="clr"></div>
      
      <div class="evField">Zipcode</div>
      <div class="evLabal">
        <input type="text" name="zip" class="evInput" style="width:300px; height:20px" value='<?php echo $zip;?>'/>
      </div>
      <div class="clr"></div><br /><br />
	  
	<?php if($sub_type == 1){ ?>

	<div class="ew-heading">Affiliate Code</div>
	
	 <div class="evField">Affiliate Code</div>
      <div class="evLabal">
        <input type="text" name="af_code" class="evInput" readonly="readonly" style="width:300px; height:20px" value='<?php echo $af_code;?>' />
      </div>
      <div class="clr"></div><br /><br />
	  
	      <?php  } ?>
	
	<div class="ew-heading">Change Password</div>
	<div class="evField">Password</div>
      <div class="evLabal">
	  	<input type="text" name="password" class="evInput" style="width:300px; height:20px" value="" />
        <br>
        <font size="-1" color="red">Leave blank if you don't want to change</font>
	  </div>
	  
	  <div class="evField">Confirm Password</div>
      <div class="evLabal">
	  	<input type="text" name="cpassword" class="evInput" style="width:300px; height:20px;" value="" />
	  </div>

	
    </div>
 
	<div align="right"><br /><br />
		
		<input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" align="right" name="continue" value="Save & Continue" style="padding:10px 0 0 10px;" />
		<!-- <a href="?p=patint-notifcations"><img src="<?php echo IMAGE_PATH; ?>skip.png" align="right"  style="padding:10px 0 0 10px;" /></a> -->
		<input type="hidden" name="continue" value="Save & Continue" />&nbsp; 
		  <br class="clr" />&nbsp;
	</div>
	
	</form>
</div>