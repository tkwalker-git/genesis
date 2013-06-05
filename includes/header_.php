<?php
	
	require_once('admin/database.php');
	
	$file_name = basename($_SERVER['SCRIPT_FILENAME']);
	
	$meta_query 	= "select * from default_settings";
	$meta_res 		= mysql_query($meta_query);	
	if ( $meta_row 		= mysql_fetch_assoc($meta_res) ) {
		$dmeta_title 		= DBout($meta_row['meta_title']);
		$dmeta_desc 		= DBout($meta_row['meta_desc']);
		$dmeta_keywords 	= DBout($meta_row['meta_keywords']);
	}
		
	$meta_descrp 	= ($meta_desc != '') ? $meta_desc : $dmeta_desc ;
	$meta_kwords 	= ($meta_keywords != '') ? $meta_keywords : $dmeta_keywords ;
	$meta_title 	= ($meta_title != '') ? $meta_title : $dmeta_title ; 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<meta name="description" content="<?php echo $meta_descrp; ?>" />
<meta name="keywords" content="<?php echo $meta_kwords; ?>" />
<title><?php echo $meta_title; ?></title>
	
	
<?php //echo $meta_descrp ?>

<!--<title><?php //echo "Eventgrabber".$subcat_title; ?></title> -->


<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>style.css"/>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/common_bc.js"></script>
</head>

<body >
<div id="page-bg" class="translucent"></div>
<div class="subscribe-overlayer" id="overlayer" align="center"></div>

<div> 

	<!--Start Top Part -->
	<div class="headerOut">
		<!--Start Tab -->
		<div class="tabCon">		
			<div class="tabLeft">
 		<ul>
	
		 
	  	 			<li><a href="<?php echo ABSOLUTE_PATH; ?>index.php" <?php if($file_name == 'index.php' || $file_name == '') echo 'class="sel"';?>><span>HOME</span></a></li>   
				 	<li><a href="<?php echo ABSOLUTE_PATH;?>about-us.php" <?php if($file_name == 'about-us.php') echo 'class="sel"';?>><span>ABOUT</span></a></li>
			
					<li><a href="<?php echo ABSOLUTE_PATH;?>features.php" <?php if($file_name == 'features.php') echo 'class="sel"';?>><span>FEATURES</span></a></li>
					
					<?php if($_SESSION['usertype']=='2')
					{ ?>
					<li><a href="<?php echo ABSOLUTE_PATH;?>eventmanager.php" <?php if($file_name == 'eventmanager.php' || $file_name == 'citypulse.php' || $file_name == 'statgrabber.php') echo 'class="sel"';?>><span>EVENT MANAGER</span></a></li>
					<?php }?>
					
					
					<li><a href="<?php echo ABSOLUTE_PATH;?>category.php" <?php if($file_name == 'category.php') echo 'class="sel"';?>><span>VIEW EVENTS</span></a></li>
			
					<li><a href="<?php echo ABSOLUTE_PATH;?>myeventwall-a.php" <?php if(strstr($file_name, 'myeventwall') /*== 'myeventwall-a.php'*/) echo 'class="sel"';?>><span>MY EVENTWALL</span></a></li>
					
					<li><a href="<?php echo ABSOLUTE_PATH;?>contact-us.php" <?php if($file_name == 'contact-us.php') echo 'class="sel"';?>><span>CONTACT US</span></a></li>
					
				 
				</ul>
			</div>
			<div class="tabRight">
			<?php if(isset($_SESSION['logedin']) && $_SESSION['logedin']==1){ ?>
		<a href="<?php echo ABSOLUTE_PATH;?>logout.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Logout</a> &nbsp;<a href="<?php echo ABSOLUTE_PATH;?>contact-us.php"><img src="<?php echo IMAGE_PATH;?>mail_icon.gif" alt="" />contact us</a>
		<?php }else { ?>
			
			<a href="<?php echo ABSOLUTE_PATH;?>login.php"><img src="<?php echo IMAGE_PATH;?>login_icon.gif" alt="" /> Login</a> | <a href="<?php echo ABSOLUTE_PATH;?>signupselection.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Sign Up</a> &nbsp;<a href="<?php echo ABSOLUTE_PATH;?>contact-us.php"><img src="<?php echo IMAGE_PATH;?>mail_icon.gif" alt="" />contact us</a>
			
			<?php }?>	


		</div>
			
			<div class="clr"></div>
			
			</div>
			<div class="header">
			<div class="logo"><a href="<?php echo ABSOLUTE_PATH;?>"><img src="<?php echo IMAGE_PATH;?>logo.jpg" alt="" border="0" /></a></div>
			<div class="logoTag"><!--<a href="#"><img src="images_site/change_btn.gif" alt="" border="0" class="vAlign" /></a>--></div>
			<div class="headerRight">
				<div class="topBtn">
				
				
			<?php 
				
			if($_SESSION['LOGINflag']!=''){	?>
				
			 <a href="<?php echo ABSOLUTE_PATH;?>add_event.php"><img src="<?php echo IMAGE_PATH;?>add_event_btn.gif" alt="" border="0" /></a> <?php } ?></div>
				<div class="search">
				
				
				<form name='searchfrm' id='searchfrm' method='get' action='<?php echo ABSOLUTE_PATH; ?>search.php'>
				
				<?php 
				
				if($_POST['searchtext']=='' || $_POST['searchtext']=='Search events with event name'){
					
					$val='Search events with event name';
				}
				else{
				
					$val = $_GET['term'];
				}
				
				
				?>
				<input type="text" name='term'  id='term' class="searchInput" value="<?php echo $val;?>"  onfocus="this.value=''"  />
				<input type="image" name='search' src="<?php echo IMAGE_PATH;?>search_btn.gif" /> 
				
				</form>
				</div>
			</div>
			<div class="clr"></div>
		</div>
		<!--End Header -->
	</div>
	
		<!--End Tab -->

   