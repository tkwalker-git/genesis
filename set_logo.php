<?php

	$bc_clinic_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
	
	
	

	if ( isset($_POST['continue']) )
	{
	
			if ($_FILES["image_name"]["name"] != '') {
			list($w, $h, $type, $attr) = getimagesize($_FILES["image_name"]["tmp_name"]);
			if ($w >=310  && $h >= 100 ){
			$err = 'Max image size is:( "300px * 95px")';
			}
			else {	
				
			 $bc_image	= time() . "_" . $_FILES["image_name"]["name"] ;			
			$bc_image	= str_replace(" ","_", $bc_image);
			
			if (isset($_FILES["image_name"]) && !empty($_FILES["image_name"]["tmp_name"])){
				move_uploaded_file($_FILES["image_name"]["tmp_name"], 'images/logos/' .$bc_image);	
							
				 $sql = "update users set logo_img='$bc_image' where id='".$bc_clinic_id."'";
				
				mysql_query($sql);

			}	
			}
	}
				
	}	
			

	
	
	$sql = "select * from users where id='". $bc_clinic_id."'";
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$image	= DBout($row['logo_img']);
		$bc_image  = $image;
		if ($image != '' && file_exists(DOC_ROOT . 'images/logos/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/logos/' . $image,211,253 );
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
<div class="yellow_bar"> &nbsp; CHANGE YOUR LOGO </div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error" align="center" style="color:#FF0000; text-align:center;"><?php echo $err; ?></div>
	
	<strong>CHANGE LOGO</strong><br />
	
	
	<br />
	<form action="" method="post" name='logo' enctype="multipart/form-data">      
	  
      
      <div class="evLabal">
        <?php 
		//$as=file_exists(DOC_ROOT . 'images/logos/' . $bc_image );
		echo $as;
			if ($bc_image != '' ) {
					 $img = returnImage( ABSOLUTE_PATH . 'images/logos/' . $bc_image,127,200 );
				echo  '<img '.$img.' align="left" style="padding-right:10px;" /><br>';
				}
			else
				echo '<img src="'.ABSOLUTE_PATH.'admin/images/no_image.png" class="dynamicImg"width="75" height="76" align="left" style="padding-right:10px;" />';
		 ?><br /><br />
		 <div style="font-size:15px; padding-bottom:4px">Change Logo Image</div>
         <input type="file" class="addEInput" name="image_name" id="image_name"  /><br />
		 
      </div>
      <div class="clr"></div><br />
      <div><b>Max logo dimension is 300px x 95px</b></div>
      <br />
	
	
 
	<div align="center"><br /><br />
		
		<input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" align="right" name="continue" value="Save & Continue" style="padding:10px 0 0 10px;" />
		
		<input type="hidden" name="continue" value="Save & Continue" />&nbsp; 
		  <br class="clr" />&nbsp;
	</div>
	
	</form>
</div>