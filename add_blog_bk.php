<?php 

include_once('admin/database.php'); 
include_once('site_functions.php'); 

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='login.php';</script>";

$user_id =	$_SESSION['LOGGEDIN_MEMBER_ID'];


if(isset($_POST['submit'])){

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
		$new_post = "insert into  blog_posts (user_id,cat_id,sub_cat_id,title,contents,status,seo_name) values ('".$user_id."','".$main_cate."','".$sub_categ."','".$blog_title."','".$blog_description."',2,'". $seo_name ."')";
		$run_new_post = mysql_query($new_post);
		
		if($run_new_post){
			$sucMessage = 1;
		}else{
			$sucMessage = "Please try again later!";
		}
	}else{
	
		$sucMessage = $err;
	}
	
}

include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/function.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/common.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/redmond/jquery-ui.css" type="text/css" media="all" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>


<script type="text/javascript">
	
	function checkCategory(val)
	{
		if ( val == -1 ) {
			alert("You can't select Parent Category");
			document.getElementById("category_id").selectedIndex=0;
		}	
	}
	
</script>

<style>

.addEInput
{
	width:225px!important;
	height:30px!important;
}


table.ui-datepicker-calendar {border-collapse: separate;}
.ui-datepicker-calendar td {border: 1px solid transparent;}

.hasDatepicker .ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
	background: #743620 none;
	color: white;
}

#ui-datepicker-div {display:none;}

.submit_btn{
	background: url("images/submit_btn.jpg") no-repeat scroll 0 0 transparent;
    border: medium none;
    cursor: pointer;
    font-size: 0;
    padding: 15px 40px;}

</style>




<div> 

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>ckeditor/_samples/sample.js"></script>
<link href="<?php echo ABSOLUTE_PATH; ?>ckeditor/_samples/sample.css" rel="stylesheet" type="text/css" />


 <div  class="topContainer" style="padding-top:20px;">
	
	
	<!--	<input type='hidden' name='venueeditid' id='venueeditid' value=''> -->
		
		<div class="eventDetailhd"><!--<span>add an <strong>event</strong></span>--></div>
		<div class="clr gap"></div>
	</div>
	<!--End Hadding -->

	<!-- Start Middle-->
	<div id="middleContainer">
		<div class="eventMdlBg">
			<div class="eventMdlMain">				
				<!--Start Left Part -->
				<?php if ( $sucMessage != 1 ) { ?>
					<form method="post" name="bc_form" action=""  >
				<div class="eventLft">
					<div><img src="<?php echo IMAGE_PATH; ?>event_tpcone.gif" alt="" /></div>
					<div class="eventMdlData">
						<!--Start Event Details -->

						<div class="eventMainCat">
						
							<div class="evntBlkHdMain">
							
								<div class="fl"><img src="<?php echo IMAGE_PATH; ?>event_black_lftcone.gif" alt="" /></div>
								<div class="fr"><img src="<?php echo IMAGE_PATH; ?>event_black_rtcone.gif" alt="" /></div>
								<div class="eventsBlackHd">Create Blog Post</div>
								<div class="clr"></div>
							</div>
							<div class="error">
								<?php echo $sucMessage; ?></div>

							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Blog Title: </div>
							<div class="evLabal"><input type="text" name="title" id="title" class="evInput" value=""/>
							</div>
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
								<option style="font-weight:bold; color:#990000" value="-1"><?php echo $rowParent['name']?></option>
								
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
							
							<div class="clr"></div>
							<div class="evField"><span class="redClr"><font color='red'>*</font></span>Blog Contents:</div>
							<div class="evLabal"><textarea name="blog_description" id="blog_description" cols="80" rows="25" style="height:300px;!important" class="evInput"><?php echo $bc_event_description; ?></textarea>
						
													
							
							</div>
							
							
							
						</div>
						<!--End Event Details -->

						<!--Start Review and Finish -->
						
							<div class="aeBotLT"><!--By clicking the Submit button you agree to our <a href="terms-of-use.php" class="lightBlueClr" target="_blank">Terms of Use</a>--></div>
							<div class="fr">
							<input type="submit" name="submit" value="submit" class="submit_btn"  />
							
							</div>
							<div class="clr"></div>
						</div>
						

					<div><img src="images/event_btcone.gif" alt="" /></div>
				</div>
				</form>
				<?php } else { ?>
					
					<div style="padding:20px; font-size:20px; font-weight:bold; color:#003300">
						<br /><br />
						Your blog post is saved and will be visible on the site once it is approved by the admin.
						<br /><br />
						Thanks,
						<br />
						EventGrabber.com
					</div>
					
				<?php } ?>
				<!--End Left Part -->
				<!--Start Right Part -->
				<div class="myeventRtMain">
					<div class="eventRtconBg">
						<div class="eventTpBg">
							<div class="featurePromoters" style="font-size:14px;">features for <strong>promoters</strong></div>

							<div class="featureMdl">
								<div align="center"><img src="<?php echo IMAGE_PATH; ?>feature_promoters_img.jpg" alt="" vspace="2" /></div>
								<div class="eventgrabberTxt"><strong>Eventgrabber </strong>provides promoters withe custom tools that automate the leg work for your events and more.</div>
								<strong>Features of our Event Manager are:</strong><br /><br />
								<div class="featureCatSec">
									<div class="featureLftIcon"><img src="<?php echo IMAGE_PATH; ?>city_palus.gif" alt="" /></div>
									<div class="featureRt">

										<div class="featureRtHd">City<strong>pulse:</strong></div>
										Keeps you in sync with your client's likes, dislikes and demands.									</div>
									<div class="clr"></div>
								</div>
								<div class="featureCatSec">
									<div class="featureLftIcon"><img src="<?php echo IMAGE_PATH; ?>stat_brobber.gif" alt="" width="30" height="23" /></div>
									<div class="featureRt">

										<div class="featureRtHd">stat<strong>grabber:</strong></div>
										Statistical breakdown of your marketing impact, demographics and trends.									</div>
									<div class="clr"></div>
								</div>
								<div class="featureCatSec">
									<div class="featureLftIcon"><img src="<?php echo IMAGE_PATH; ?>quick_add.gif" alt="" width="30" height="23" /></div>
									<div class="featureRt">Quickly add, edit and manage your events.</div>

									<div class="clr"></div>
								</div>
								<div class="manyMore">And many more features coming soon..</div>
							</div>
						</div>
					<div><img src="<?php echo IMAGE_PATH; ?>myevent_rtbtm_con.gif" alt="" /></div>
					
					</div>
					
			</div>

			
		  </div>
				<!--End Right Part -->
				<div class="clr"></div>
	  </div>	
	  </div>
		


</div>

<script type="text/javascript" src="admin/tinymce/tiny_mce.js"></script>

<script>

tinyMCE.init({
	mode : "exact",
	elements : "blog_description",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,forecolor,backcolor,bullist,numlist,outdent,indent,blockquote,anchor,cleanup",
	theme_advanced_buttons2 : "cut,copy,paste,styleselect,formatselect,fontselect,fontsizeselect,hr,code,image",
	theme_advanced_font_sizes: "10px,11px,12px,13px,14px,15px,16px,17,18px,19px,20px,22px,24px,26px,28px,30px,36px",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	remove_script_host : false,
    convert_urls : false,
	content_css : "site_styles.css?1",
	plugins : 'inlinepopups,imagemanager',
});

</script>

<?php include_once('includes/footer.php');?>

			