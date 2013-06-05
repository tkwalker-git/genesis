<?php
	
	require_once('admin/database.php');
	require_once('site_functions.php');
	if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 )
		$logged_in_member_name = attribValue("users","firstname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
	
	$caturl = getViewEventURL();
	
	if(isset($_POST['subcribeNb'])){
		$email		= $_POST['subcribeNbaEmail'];
		$already	= getSingleColumn('id',"select * from `subcribe_nba` where `email`='$email'");
		if($already > 0){
			$emailMsg = "<div class='head'>Error</div><div class='sp_subc_text'>Email address is already subscribed</div>";
		}
		else{
			$res = mysql_query("INSERT INTO `subcribe_nba` (`id`, `email`) VALUES (NULL, '$email');");
			if($res){
			$id = mysql_insert_id();
				$v = base64_encode("id=".$id."&email=".$email);
				$contents = ABSOLUTE_PATH.'confirm.php?v='.$v;
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: "EventGrabber" <info@eventgrabber.com>' . "\r\n";
				$subject = "EventGrabber - Email Confirmation";
				
				@mail($email,$subject,$contents,$headers);
				
				$emailMsg = "<div class='head'>Thank you for Subscribing</div><div class='sp_subc_text'>Confirmation link Has Been Sent<br>To Your Email Address </div>";}
			else{
				$emailMsg = "<div class='head'>Error</div><div class='sp_subc_text'>Try again later.</div>";
				}
		}
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html style="background-attachment: scroll;" xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>EventGrabber Sponsors Page</title>

<link rel="stylesheet" type="text/css" href="ads/ads_styles.css">

<script type="text/javascript" src="ads/js/jquery.js"></script>
<script type="text/javascript" src="ads/js/jquery-ui.js"></script>
<script type="text/javascript" src="ads/js/jquery_002.js"></script>
<script type="text/javascript" src="ads/js/script.js"></script>
<script>
function checkemailValid(){
	var email = $('#subcribeNbaEmail').val();
	if(email=='' || email=='Email Address'){
		alert('Please enter email address');
		return false;
		}
	var str = email;
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		testresults=true;
	else{
		alert("Please input a valid email address");
		return false;
		}
}
</script>
</head>
<body>
<div class="sp_main">
  <div class="sp_header_main">
    <div class="sp_menu">
      <div class="sp_inrWidth">
        <ul>
          <li><a href="<?php echo ABSOLUTE_PATH; ?>index.php">HOME</a></li>
          <li><a href="<?php echo ABSOLUTE_PATH;?>about-us.php">ABOUT</a></li>
          <li><a href="<?php echo ABSOLUTE_PATH;?>features.php">FEATURES</a></li>
          <li><a href="<?php echo $caturl; ?>">VIEW EVENTS</a></li>
          <li><a href="<?php echo ABSOLUTE_PATH;?>myeventwall.php">MY EVENTWALL</a></li>
          <li style="border-right:none"><a href="<?php echo ABSOLUTE_PATH;?>contact-us.php">CONTACT US</a></li>
		  <li class="login">
		  <?php if(isset($_SESSION['logedin']) && $_SESSION['logedin']==1 && $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ){ ?>
			<select onchange="window.location.href=this.value" style="margin-top:5px;">
				<option value="#" selected="selected"><?php echo $logged_in_member_name;?></option>
				<option value="<?php echo ABSOLUTE_PATH;?>profile_setting.php" >My Profile</option>
				<option value="<?=ABSOLUTE_PATH."logout.php"?>" >Logout</option>
			</select>
		 <?php } else{ ?>
		 <span>
			 <a href="<?php echo ABSOLUTE_PATH;?>login.php">Login</a> | <a href="<?php echo ABSOLUTE_PATH;?>signup.php">Sign up</a></span>
		  <?php } ?></li>
        </ul>
      </div>
      <!-- end sp_inrWidth -->
    </div>
    <!-- end sp_menu -->
    <div class="sp_header_bg">
      <div class="sp_inrWidth">
        <div class="sp_header">
		
			<div class="hdr_lft">
				
			</div>
			<div class="hdr_ctr">
			<div class="head">Eventgrabber presents:  Orlando Allstars</div>
			
			<div class="txt">This wall includes the city's top allstars in the categories of: 
			<?php
				$rs	=	mysql_query("select * from `slot_category`");
				$i = 1;
				$num = mysql_num_rows($rs);
				while ($row = mysql_fetch_assoc($rs) ) {
				
					echo $row['cat'];
					if($i!=$num && $i != $num-1)
						echo  ", ";
					if($i == $num-1)
						echo " And ";
				$i++;
				}
			?>.</div>
			</div>
			
			<div class="hdr_rgt">
			
			</div>
			
		
		</div>
        <!-- end sp_header -->
      </div>
      <!-- end sp_inrWidth -->
    </div>
    <!-- end sp_header_bg -->
  </div>
  <!-- sp_header_main -->
  <div class="sp_body">
	<?php
	
	$slt_cat_rs = mysql_query("select * from `slot_category`");
	while($slt_row=mysql_fetch_array($slt_cat_rs)){
		$slt_cat_id = $slt_row['id'];?>
		<div class="sp_catBar"> <?php echo $slt_row['cat']; ?> </div>
		<div class="sp_ads_main_box">
		<?php
		
		$sql	=	"select * from `slots` where `cat_id`='$slt_cat_id' order by `id`";
		$rs	=	mysql_query($sql);
		while ($row = mysql_fetch_assoc($rs) ) {
			
			$sid = $row['id'];
			
			$sql	=	"select * from sold_slots where slot_id=$sid AND status=1 AND '". date("Y-m-d") ."' BETWEEN  start_date AND end_date";
			$res	=	mysql_query($sql);
			
			if ( mysql_num_rows($res) > 0 ) {
				if ($value = mysql_fetch_assoc($res) ) {
					
					$url = $value['url'];
					if ( substr($url,0,7) == 'http://' || substr($url,0,8) == 'https://' )
						$url = $url;
					else
						$url = 'http://'.$url;
					
			?>
	
					<div class="sponsor" title="Click to flip">
						<div class="sponsorFlip">
							<img src="<?php echo $value['image'];?>" alt="<?php echo $value['title'];?>">
						</div>
						
						<div class="sponsorData">
							<div class="sponsorDescription">
								<?php echo $value['descr'];?>
							</div>
							<div class="sponsorURL">
								<a href="<?php echo $url;?>" target="_blank">Visit Website</a>
							</div>
						</div>
					</div>
			<?php 
				} 
			} else {
				$sidl=base64_encode($sid);
			?>
				
					<div class="sponsor" title="Click to Buy Slot">
						<div class="sponsorFlip1">
							<a href="<?php echo ABSOLUTE_PATH_SECURE; ?>buy-slots.php?id=<?php echo $sidl;?>"><img src="ads/images/available.png" alt="Available" border="0"></a>
						</div>
					</div>
				
		<?php	
			}
		}	
		?>
		<div class="clear"></div>
		</div>  <div class="clear"></div>
		<?php 
		} // end slot_category while
		?>
    	</div>
  <!-- end sp_body -->
  <div class="clear"></div>
  <div class="sp_bottom">
    <div class="sp_inrWidth">
      <div class="sp_bottom_box"> <span class="head"><strong>FEATURED EVENTS</strong></span>
        <ul>
         <li><a href="http://www.eventgrabber.com/category/sports/basketball/nba-all-star-e-recycling.html" title="NBA All-Star E-Recycling">NBA All-Star E-Recycling</a></li>
		<li><a href="http://www.eventgrabber.com/category/sports/nba-allstar-weekend/nba-all-star-jam-session.html" title="NBA All-Star Jam Session">NBA All-Star Jam Session</a></li>
		<li><a href="http://www.eventgrabber.com/category/sports/nba-allstar-weekend/nba-all-star-practice.html" title="NBA All-Star Practice">NBA All-Star Practice</a></li>
		<li><a href="http://www.eventgrabber.com/category/sports/nba-allstar-weekend/nba-all-star-game.html" title="NBA All-Star Game">NBA All-Star Game</a></li>
		<li><a href="http://www.eventgrabber.com/category/sports/nba-allstar-weekend/nba-rising-stars-challenge.html" title="NBA Rising Stars Challenge">NBA Rising Stars Challenge</a></li>
        </ul>
      </div>
      <!-- end sp_bottom_box -->
    </div>
    <!-- end sp_inrWidth -->
    <div class="sp_inrWidth">
      <div class="sp_bottom_box"> <span class="head"><strong>EVENTGRABBER CATEGORIES</strong></span>
        <ul>
          <li><a href="http://www.eventgrabber.com/category/live-entertainment.html">Live Entertainment</a></li>
          <li><a href="http://www.eventgrabber.com/category/festivals.html">Festivals</a></li>
          <li><a href="http://www.eventgrabber.com/category/nightlife.html">Nightlife</a></li>
          <li><a href="http://www.eventgrabber.com/category/community.html">Professionals</a></li>
          <li><a href="http://www.eventgrabber.com/category/kid-family.html">Kids &amp; Family</a></li>
        </ul>
      </div>
      <!-- end sp_bottom_box -->
    </div>
    <!-- end sp_inrWidth -->
    <div class="sp_subscribe">
      <?php
			if(isset($emailMsg)){
				echo $emailMsg;
			}
			else{?>
      <div class="head"> SUBSCRIBE TO NBA ALLSTAR UPDATES </div>
      <div class="sp_subc_text"> Sign-up with your Email to<br>
        receive newest update on<br>
        NBA ALL-STARS </div>
      <div class="sp_search">
        <form method="post" onSubmit="return checkemailValid();">
          <input type="text" name="subcribeNbaEmail" id="subcribeNbaEmail" value="Email Address" onFocus="if(this.value=='Email Address'){this.value='';}" onBlur="if(this.value==''){this.value='Email Address';}" class="input">
          <input type="image" src="ads/images/sp_searchSubmit.png" value="Submit" name="subcribeNb">
          <input type="hidden" value="Submit" name="subcribeNb" >
        </form>
      </div>
      <!-- end sp_search -->
      <?php
				  }?>
    </div>
  </div>
  <!-- end sp_bottom -->
  
  <div class="sp_footer">
  	<div class="sp_footerInner">
		
	
	</div> <!-- end sp_footerInner -->
  </div> <!-- end sp_footer -->
</div>
<!-- end sp_main -->
</body>
</html>