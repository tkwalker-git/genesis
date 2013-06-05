<?php 
	
	require_once('admin/database.php');
	include_once('site_functions.php'); 

	$cat_q = "select id, name from categories order by id ASC";
	$cat_res=mysql_query($cat_q);
	
	$business_name 		= DBin($_POST['business_name']);
	$promoter_role 		= DBin($_POST['promoter_role']);
	$category    		= $_POST['category'];
	$breifbio 	   		= DBin($_POST['breifbio']);
	$image_name	   		= $_FILES['image_name']['name'];
	$website 			= DBin($_POST['website']);
	$busines_phone	 	= DBin($_POST['busines_phone']);
    $promoter_id		= $_SESSION['insertid'];	
	
		
	if ( isset($_POST) && count($_POST) > 0 ) {
		
		if ( trim($business_name) == '' )
			$errors[] = 'Please Enter Business Name';
		if ( trim($promoter_role) == '' )
			$errors[] = 'Please Select Your Role';
		if ( trim(sizeof($category)) == '0' )
			$errors[] = 'Please Select Atleast one Category';		
		if ( trim($breifbio) == '' )
			$errors[] = 'Please Enter Brief Bio';
	   	if ( !validateUrl($website))
			$errors[] = 'Website URL is invalid.';
			  
		if( isset($_FILES['image_name'])) {
			if ((($_FILES["image_name"]["type"] == "image/gif")
					|| ($_FILES["image_name"]["type"] == "image/jpeg")
					|| ($_FILES["image_name"]["type"] == "image/pjpeg")
					|| ($_FILES["image_name"]["type"] == "image/png"))
			)
		  	{

				if ($_FILES["image_name"]["error"] > 0)
					$errors[] = "Return Code: " . $_FILES["image_name"]["error"] . "<br />";
			  	else
				{
			  		$usr_file = time() . '_' . $_FILES["image_name"]["name"];
					move_uploaded_file($_FILES["image_name"]["tmp_name"], 'images/members/' .$usr_file);
					makeThumbnail($usr_file, 'images/members/', '', 275, 375,'th_');
					makeThumbnailFixWidthHeight($bc_image, 'images/members/th_', 'images/group/icon/', 77, 77);
					makeThumbnailFixWidthHeight($bc_image, 'images/members/th_', 'images/group/icon/big', 281, 280);
					
				}
		  	} else 
				$errors[] = 'Business Image is not Valid Image file.';
		 }	   
			  
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			// write code here to insert data to the database.
			//promoter details
			$promoterDetails_insert = "insert into promoter_detail 
				(business_name, role, briefbio, com_website, busines_phone, promoterid)
				 values ('$business_name', '$promoter_role', '$breifbio', '$website', '$busines_phone', '$promoter_id') ";
			mysql_query($promoterDetails_insert) ;
			//promoter category(ies)
			for($i=0; $i<count($category); $i++){
				$promoterCate_insert = "insert into promoter_category 
										(promoterid, categoryid ) values ('$promoter_id', '$category[$i]') ";
				mysql_query($promoterCate_insert) ;
			}
			//image
			if($image_name != ''){
				$promoter_img = "update users set image_name = '$usr_file' where id = '$promoter_id' and usertype = '2' ";
				mysql_query($promoter_img)  ;
			}
			$_SESSION['insertid'] = '';
			$_SESSION['page_ref'] = '';
			
			echo "<script>window.location.href='login.php';</script>";
			
		}	
	}
	
	require_once('includes/header.php'); 
	
?>
<style>

.addEInput
{
	width:225px!important;
	height:30px!important;
}

.error
{
	text-align:left;
	float:left;
	width:100%;
}

.error ul
{
	border:#CC8968 solid 1px;
	background-color:#FFFFCC;
	padding:10px;
	background-image:url(images/error.png);
	background-repeat:no-repeat;
	background-position:5px 5px;
	padding-top:40px;
	padding-left:10px;
}

.error ul li
{
	margin-left:40px;
	color:#990000;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}

</style>


<div class="topContainer">
		<div class="signupLeft">
			<form enctype="multipart/form-data" name="promoterform" method="post" action="">
			<div class="signUpBox">
				<div class="eventDetailhd"><span>Sign <strong>Up</strong></span></div>
				<div class="proText"><span class="yellowHd">Promoter</span></div>
				<div class="fr"><a href="<?php echo ABSOLUTE_PATH; ?>login.php"><img border="0" alt="" src="<?php echo IMAGE_PATH; ?>signin_btn.gif"></a></div>		
				<div class="clr gap"></div>
				<span class="detailHd fl"><strong>Sign Up in 2 Easy Steps!</strong></span>
				<div class="fr"><img alt="" src="<?php echo IMAGE_PATH; ?>promoter_step2.gif"></div>
				<div class="clr"></div>			
			</div>
			<div class="signUpBox">	
				<div><img alt="" src="<?php echo IMAGE_PATH; ?>contact_top_bg.jpg"></div>
				<div class="signupMiddleBg bigSel">	
				<div class="error"><?php echo $err; ?></div>				
					<span style="padding-top: 0pt;" class="formText">Business Name</span>
					<input type="text" maxlength="40" class="bigInput" value="<?php echo $business_name;?>" id="business_name" name="business_name"><br>
					<span class="formText">Your Role</span>
					<select id="promoter_role" name="promoter_role">
						<option value="">----- Select One -----</option>
						<option value="Ow">Owner</option>
						<option value="M">Manager</option>
						<option value="A">Admin</option>
						<option value="Ot">Other</option>
					</select><br>
					<span class="formText clr">Event Categories You Cater to</span>
					<select multiple="multiple" id="category" name="category[]">
						<option value="">----- Add all categories that apply to your business -----</option>
						<?php 
							while($cat_r = mysql_fetch_assoc($cat_res)){
								$catId 		= DBout($cat_r['id']);
								$catName 	= DBout($cat_r['name']);
								
								if ( in_array($catId , $category) )
									$sele = 'selected="selected"';
								else
									$sele = '';	
								
						?>				
						<option  <?php echo $sele; ?> value="<?php echo $catId; ?>"><?php echo $catName; ?></option>
						
						<?php } ?>
                    </select><a onclick="selectall();" style="text-decoration: none;" href="#A">SelectAll</a>|<a style="text-decoration: none;" onclick="Unselectall();" href="#A">UnSelect</a><br>
					<span class="formText clr">Brief Bio</span>
					<textarea class="bigTextarea" id="breifbio" name="breifbio" rows="2" cols="2"><?php echo $breifbio;?></textarea><br>
					<span class="formText clr">Company Image</span>
					<img class="vAlign" alt="" src="<?php echo IMAGE_PATH; ?>upload_img.gif"> <input type="file" id="image_name" name="image_name">
					<span class="formText">Official Company Website</span>
					<input type="text" maxlength="100" class="bigInput" id="website" value="<?php echo $website;?>" name="website"><br>
					<span class="smllText">e.g. http://www.yourwebsite.com</span>
					<span class="formText">Business Phone</span>
					<input type="text" maxlength="30" class="bigInput" id="busines_phone" value="<?php echo $busines_phone;?>" name="busines_phone"><br>
					<!--<input type="hidden" value="promoter" name="action_new">-->
					<span class="smllText">e.g. 555-555-5555</span>
					<div align="center" class="formText"><input type="image" onclick="return getPromoter();" src="<?php echo IMAGE_PATH; ?>finish_signup_btn.gif" name="finish"></div>
				</div>	
				<div><img alt="" src="<?php echo IMAGE_PATH; ?>contact_bot_bg.jpg"></div>	
			</div>
			<div style="padding-top: 0pt;" class="signUpBox">
				
				<div class="clr"></div>
			</div>	
			</form>
		</div>
		<div class="signupRight">
			<div class="whyEventgrabber">
				<div class="hd">Features for Promoters</div>
				<div class="text">
				Eventgrabber provides promoters with custom tools that automates the leg work for your events and more.  Features of our EventManager tool include:
				<ul class="blueArowwText">
					<li>client's likes, dislikes and demands</li>
					<li>StatGrabber: Statistical breakdown of your marketing impact, demographics, and trends</li>
					<li>Quickly add, edit and manage your events</li>
					<li>And many more features coming soon. . .</li>
				</ul>
				</div>
			</div>
		</div>
		<div class="clr"></div>	
</div>
<?php require_once('includes/footer.php'); ?>
<script type="text/javascript">
/*Begin Select and unselect all option value */

function selectall()

{
//alert('++');
	var myselect=document.getElementById("category")
	
	for (var i=1; i<myselect.options.length; i++)
	{
		myselect.options[i].selected=true;	
	}
}

function Unselectall()

{
//alert('++');
	var myselect=document.getElementById("category")
	
	for (var i=1; i<myselect.options.length; i++)
	{
		myselect.options[i].selected=false;	
	}
}

</script>
