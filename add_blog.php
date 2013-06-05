<?php

	require_once('admin/database.php');
	require_once('site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
	
	require_once('includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 	= DBout($row['firstname']);
		$email  = DBout($row['email']);
		
		$image	= DBout($row['image_name']);
		
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img align="center" '. $img .' />';	
		} else
			$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		
		$total_events_grabbed = 0;
	}
	
	if ( isset($_POST['continue1_x']) || isset($_POST['continue1']) )
	{
		$tmp = explode("|",$_POST["category_id"]);
	
		$main_cate		=	$tmp[0];
		$sub_categ		=	$tmp[1];
		
		$blog_title			=	$_POST['title'];
		$blog_description	=	$_POST['blog_description'];
		
		$errors = array();
		
		if ( trim($blog_title) == '' )
			$errors[] = "Blog Title can't be empty";
		if ( trim($main_cate) == '' )
			$errors[] = 'Please Select Category';
		if ( trim($blog_description) == '' )
			$errors[] = "Post contents can't be empty";
		
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="100%">
						<tr>
							<td class="error" >
								<ul>';
									for ($i=0;$i<count($errors); $i++) {
										$err .= '<li style="text-align: left;">' . $errors[$i] . '</li>';
									}
						$err .= '</ul>
							</td>
						</tr>
					</table>';	
		}
			
		if ( !count( $errors)) {
			$seo_name = getRewriteString($blog_title); 
			$new_post = "insert into  blog_posts (user_id,cat_id,sub_cat_id,title,contents,status,seo_name) values ('".$member_id."','".$main_cate."','".$sub_categ."','".$blog_title."','".$blog_description."',2,'". $seo_name ."')";
			$run_new_post = mysql_query($new_post);
			
			if($run_new_post){
				$sucMessage = "Your blog post is saved and will be visible on the site once it is approved by the admin.";
			}else{
				$sucMessage = "Please try again later!";
			}
		}else{
		
			$sucMessage = $err;
		}
	}
	
	
	
?>



<div class="topContainer">
		<div>
			<div class="profileBox">
				<div class="fl"><?php echo $img;?></div>
				<div class="profileDetail">
				<strong class="lightBlueClr">&nbsp;</strong><br />
				<strong class="lightBlueClr"><u><?php echo $logged_in_member_name;?></u></strong><br />
				Events Grabbed: <strong class="lightBlueClr"><?php echo $total_events_grabbed;?></strong><br />
				Events Posted: <strong class="lightBlueClr"><?php echo $total_events;?></strong><br />
				Reviews: <strong class="lightBlueClr"><?php //echo showuserreviewevents();?></strong><br />
               <a href="profile_setting.php" class="lbLink">Profile Setting</a>
				</div>
				<div class="clr"></div>
			</div>
         
			<div class="friendsCon">

			</div>

			<!-- code added ends-->
			<div class="clr"></div>
		</div>
	</div>
	<!--End Banner Part -->
	<!--Start Middle Part -->
	<div class="middleConOu">
		<div class="middleContainer">
			<div class="tacConBot">
				<div class="ProfileSettingTab">
					<?php userSubMenu("add_blog");?>
				</div>
				<div class="fr"><!--<a href="add_event.php"><img src="images/add_event_btn.gif" alt="" border="0" /></a>--></div>
				<div class="clr"></div>
			</div>
		
		<div class="grayRoundBox">
				<div class="grayLBC">
					<div class="grayRTC">
						<div class="grayLTC">
							
							<?php if (  $err != '' ) { ?>
								<div class="error"><?php echo $err; ?></div>
							<?php } ?>
								
							<?php if ( $sucMessage != '' ) { ?>
								<font color='green' style="padding:10px; text-align:center; display:block"><strong><?php echo $sucMessage;?> </strong></font>
							<?php } ?>
							
							<form method="post" name="bc_form" action=""  >
								<div class="editProBox">
									
									<div class="evField"><span class="redClr"><font color='red'>*</font></span>Blog Title: </div>
									<div class="evLabal"><input type="text" name="title" id="title" class="evInput" value=""/></div>
									<div class="clr"></div>
											
											
									<div class="evField"><span class="redClr"><font color='red'>*</font></span>Category: </div>
									<div class="evLabal">
		
									<select class="bc_input" name="category_id" id="category_id" style="width:200px" onchange="checkCategory(this.value);">
										<option value="" >Select Category</option>
										<?php $sqlParent = "SELECT name,id FROM categories ";
										$resParent = mysql_query($sqlParent);
										$totalRows=mysql_num_rows($resParent);
										while($rowParent = mysql_fetch_array($resParent))
										{	
										?>
										<option style="font-weight:bold; color:#990000" value="-1"><?=$rowParent['name']?></option>
										
											  <?php 
												$subcat_q = "SELECT * FROM sub_categories WHERE categoryid = '". $rowParent['id'] ."' ORDER BY id ASC";
												$res = mysql_query($subcat_q) ;
												while( $r = mysql_fetch_assoc($res) ){  
													if ( $r['id'] == $_GET['subcat'] )
														$sele = 'selected="selected"';
													else
														$sele = '';	
											  ?>
											 <option style="font-weight:normal; padding-left:10px" <?php echo $sele;?> value="<?php echo $rowParent['id'].'|'. $r['id']; ?>"><?php echo $r['name']; ?></option>  
										
									   <?php } } ?>
										</select>
									</div>
											
									<div class="clr"></div>
									
									
									<div class="evField"><span class="redClr"><font color='red'>*</font></span>Blog Contents:</div>
									<div class="evLabal">
										<textarea name="blog_description" id="blog_description" cols="80" rows="25" style="height:300px;!important" class="evInput"><?php echo $bc_event_description; ?></textarea>
									</div>
											
									<div class="clr"></div>
									
									<div class="evField"></div>
									<div class="evLabal"><a href="myeventwall.php">
										<img src="images/back_event_well_btn.gif" class="vAlign" vspace="10" hspace="10" border="0"/></a>	
										<input name="continue1" id="continue1" type="image" src="images/save_setting_btn_new.gif" class="vAlign" vspace="10" hspace="10" />
										
									</div>	
									
									<div class="clr"></div>
								</div>
							</form>
							
							
						</div>
					</div>
				</div>
			</div>
		
		</div>
	</div>

<div class="clr"></div>
<?php require_once('includes/footer.php');?>