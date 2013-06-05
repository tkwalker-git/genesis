<?php
		
	if(isset($_GET['cancle'])){
	$cancle_id = $_GET['cancle'];
	if($cancle_id == $_SESSION['LOGGEDIN_MEMBER_ID']){
	$_SESSION['logedin'] = '';
	$_SESSION['LOGGEDIN_MEMBER_ID'] = '';
	$_SESSION['LOGGEDIN_MEMBER_TYPE'] = '';
	session_destroy();
	mysql_query("UPDATE `users` SET `enabled` = '0' WHERE `id` = $cancle_id");
	echo "<script>window.location.href='confirm.php';</script>";
	}}
		
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	if ( isset($_POST['continue1_x']) || isset($_POST['continue1']) )
	{
		
		$fname 			=	DBin($_POST['fname']);
		$lname 			=	DBin($_POST['lname']);
		$email 			=	DBin($_POST['email']);
		$zip 			=	DBin($_POST['zip']);
		$gender			=	DBin($_POST['gender']);
		$password 		=	DBin($_POST['password']);
		$cpassword 		=	DBin($_POST['cpassword']);
		$dob 			=	$_POST['dob'];
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
			
			$bc_image  = time() . "_" . $_FILES["image_name"]["name"] ;
			if (isset($_FILES["image_name"]) && !empty($_FILES["image_name"]["tmp_name"])){
				move_uploaded_file($_FILES["image_name"]["tmp_name"], 'images/members/' .$bc_image);
					makeThumbnail($bc_image, 'images/members/', '', 275, 375,'th_');
					makeThumbnailFixWidthHeight($bc_image, 'images/members/th_', 'images/group/icon/', 77, 77);
					makeThumbnailFixWidthHeight($bc_image, 'images/members/th_', 'images/group/icon/big', 281, 280);
			//		makeRoundedThumbnail($bc_image, 'images/group/icon/', 'images/frameOver.gif');
				$sql_img = ", image_name = '$bc_image' ";
			}
			
			$sql = "update users set zip='$zip',firstname='$fname',lastname='$lname',email='$email',sex='$gender',".  $pwd ."dob='$dob' " . $sql_img . " where id='". $member_id ."'";
			if ( mysql_query($sql) )
				$sucMessage = 'Profile Updated Successfully';
		}	
			
	}
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 		= DBout($row['firstname']);
		$lname 		= DBout($row['lastname']);
		$email  	= DBout($row['email']);
		$zip 		= DBout($row['zip']);
		$gender 	= DBout($row['gender']);
		$usern  	= DBout($row['username']);
		$dob  		= DBout($row['dob']);
		$password	= DBout($row['password']);
		
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

<div class="clr"></div>
<div class="recommendedBlock"><br />

  <?php if (  $err != '' ) { ?>
  <div class="error"><?php echo $err; ?></div>
  <?php } ?>
  <?php if ( $sucMessage != '' || $_GET['sucMessage']!='' ) {
							if($_GET['sucMessage']!=''){
							$sucMessage	=	$_GET['sucMessage'];} ?>
  <font color='green' style="padding:10px; text-align:center; display:block"><strong><?php echo $sucMessage;?> </strong></font>
  <?php } ?>
  <form action="" method="post" name='profrm' enctype="multipart/form-data">
    <div class="editProox">
      <div class="evField">First Name</div>
      <div class="evLabal">
        <input type="text" name="fname" class="evInput" style="width:300px;" value='<?php echo $name;?>' />
      </div>
      <div class="clr"></div>
      <div class="evField">Last Name</div>
      <div class="evLabal">
        <input type="text" name="lname" class="evInput" style="width:300px;" value='<?php echo $lname;?>' />
      </div>
      <div class="clr"></div>
      <div class="evField">Your Email</div>
      <div class="evLabal">
        <input type="text" name="email" class="evInput" style="width:300px;" value='<?php echo $email;?>'/>
      </div>
      <div class="clr"></div>
      <div class="evField">Your Zipcode</div>
      <div class="evLabal">
        <input type="text" name="zip" class="evInput" style="width:300px;" value='<?php echo $zip;?>'/>
      </div>
      <div class="clr"></div>
      <div class="evField">UserName</div>
      <div class="evLabal">
        <input type="text" name="username" class="evInput" readonly="true" style="width:300px;" value='<?php echo $usern;?>'/>
      </div>
      <div class="clr"></div>
      <div class="evField">Password</div>
      <div class="evLabal">
        <input type="text" name="password" class="evInput" style="width:300px;" value="" />
        <br>
        <font size="-1" color="red">Leave blank if you don't want to change</font></div>
      <div class="clr" style='margin-left:10px;'></div>
      <div class="evField">Confirm Password</div>
      <div class="evLabal">
        <input type="text" name="cpassword" class="evInput" style="width:300px;" value="" />
      </div>
      <div class="clr"></div>
      <div class="evField">Image</div>
      <div class="evLabal">
        <?php 
			if ($bc_image != '' && file_exists(DOC_ROOT . 'images/members/' . $bc_image ) ) {
					$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $bc_image,127,2000 );
				echo '<img '.$img.' /><br>';
				}
			else
				echo '<img src="admin/images/no_image.png" class="dynamicImg"width="75" height="76" /><br>';
		 ?>
        <input type="file" class="addEInput" name="image_name" id="image_name"  />
      </div>
      <div class="clr"></div>
      <div class="evField">Gender</div>
      <div class="evLabal">
        <select id="gender" name="gender">
          <option value="">Select Gender</option>
          <option <?php echo ( ($gender == 'M') ? 'selected="selected"' : '') ?> value="M" >Male</option>
          <option <?php echo ( ($gender == 'F') ? 'selected="selected"' : '') ?> value="F" >Female</option>
        </select>
      </div>
      <div class="clr"></div>
      <div class="evField">Birth Date</div>
      <div class="evLabal">
        <input type="text" name="dob" id="dob" readonly="true" value="<?php echo $dob;?>" />
        <script type="text/javascript" src="js/jquery-ui_1.8.7.js"></script>
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />
        <script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
        <script>
										$(function() {
											$( "#dob" ).datepicker({
																	dateFormat: "mm/dd/yy",
																	changeMonth: true,
																	changeYear: true,
																	yearRange: '1940:2011'
																});
										});
										
									</script>
      </div>
      <div class="clr"></div>
      <div class="evField"></div>
      <div class="evLabal"><a href="myeventwall.php"><img src="images/back_event_well_btn.gif" class="vAlign" vspace="10" hspace="10" border="0"/></a>
        <input name="continue1" id="continue1" type="image" src="images/save_setting_btn_new.gif" class="vAlign" vspace="10" hspace="10" />
      </div>
      <div class="clr"></div>
    </div>
  </form>
</div>