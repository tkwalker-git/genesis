<?php
require_once('admin/database.php');
include_once('site_functions.php');
include_once('includes/header.php');
?>

<link rel="stylesheet" type="text/css" href="style.css" />

<!--Start Banner Part -->
	<div class="homeBanner">
		<div class="bannerLeft">
			<div class="bannerImg"><img src="<?php echo IMAGE_PATH; ?>home_banner_img.gif" alt="" /></div>			
		</div>
		<div class="bannerRight">
			<div class="bannerHd">Welcome to Eventgrabber</div>
			<div class="bannerText">Where we match events with your personality allowing you to just click-and-go</div>
			<div class="fl"><a href="<?php echo ABSOLUTE_PATH;?>add_event.php"><img src="<?php echo IMAGE_PATH; ?>add_your_event.gif" alt="" border="0" vspace="25" /></a></div>
			<div class="fr"><a href="features.php" alt="features"><img src="<?php echo IMAGE_PATH; ?>learn_more_btn.gif" alt="features" border="0" vspace="25" /></a></div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
	</div>
	<!--End Banner Part -->
	<!--Start Middle Part -->
	<div class="middleConOu">
		<div class="middleContainer">
			<!--Start Events -->
			<div class="homeMidTop">
				<span class="mainHd">What events would you like to see? <a style="font-size:14px" href="<?php echo ABSOLUTE_PATH;?>categories.php">See All Categories</a></span>
				<div class="eventCon">
					<div class="eventRightBg">
						<div class="eventLeftBg">
							
								<?php 
									$q 			= "select * from categories LIMIT 6";
									$res 		= mysql_query($q);
									$total_cat 	= mysql_num_rows($res);
									$i=1;
									while($r = mysql_fetch_assoc($res)){
										$i++;
										$cat_id 		= DBout($r['id']);
										$cat_name 		= DBout($r['name']);
											
								?>
								<?php if($total_cat -- == 1 ){ ?>	
								  <div class="eventBox last">
								<?php }else{ ?>
								  <div class="eventBox">
								<?php } ?>
									<?php showCatList($cat_id); ?>
									 <?php if($cat_id == 16){ ?>
										<div align="center"><img src="<?php echo IMAGE_PATH; ?>live_entertainment_img.gif" alt="" /></div>
									<?php } ?>
									<?php if($cat_id == 17){ ?>
										<div align="center"><img src="<?php echo IMAGE_PATH; ?>fastival_img.gif" alt="" /></div>
									<?php } ?>
									<?php if($cat_id == 19){ ?>
										<div align="center"><img src="<?php echo IMAGE_PATH; ?>night_life_img.gif" alt="" /></div>
									<?php } ?>
									<?php if($cat_id == 18){ ?>
										<div align="center"><img src="<?php echo IMAGE_PATH; ?>networking_img.gif" alt="" /></div>
									<?php } ?>
									<?php if($cat_id == 21){ ?>
										<div align="center"><img src="<?php echo IMAGE_PATH; ?>sports_img.gif" alt="" /></div>
									<?php } ?>
									<?php if($cat_id == 22){ ?>
										<div align="center"><img src="<?php echo IMAGE_PATH; ?>kid_friendly_img.gif" alt="" /></div>
									<?php } ?>
									</div>
								<?php } ?>
							
							
						<!--</div>-->
					</div>
				</div>
			</div>
			<!--End Events -->
			<div class="homeBotCon">
				<!--Start We Match Banner -->
				<div class="weMatchBanner">
					<div class="wmbText">
					We Match Events With Your Personailty.
					<span>No more searching multiple sites to find the right event for you.</span>
					</div>
					<div class="learnMore"><a href="<?php echo ABSOLUTE_PATH;?>demo.php" title="Eventgrabber Demo">view demo</a></div>
				</div>
				<!--End We Match Banner -->
				<!--Start getstarted -->
				<div class="homeBlueBox">
					<div class="homeBlueBoxBot">
						<div class="homeBlueBoxTop">
							<span class="eventWallHd">get<span>started</span></span>
							<div class="stepCon">
								<div class="stepLeftBox"><img src="<?php echo IMAGE_PATH; ?>step1_img.gif" alt="" /> Step 1:</div>
								<div class="stepRightBox">Sign up for free!</div>
								<div class="clr"></div>
							</div>
							<div class="stepCon">
								<div class="stepLeftBox"><img src="<?php echo IMAGE_PATH; ?>step2_img.gif" alt="" /> Step 2:</div>
								<div class="stepRightBox">Tell us a few of your preferences.</div>
								<div class="clr"></div>
							</div>
							<div class="stepCon" style="border:none;">
								<div class="stepLeftBox"><img src="<?php echo IMAGE_PATH; ?>step3_img.gif" alt="" /> Step 3:</div>
								<div class="stepRightBox">Allow Eventgrabber to work for you.</div>
								<div class="clr"></div>
							</div>
							<div class="signupBtn"><a href="<?php echo ABSOLUTE_PATH;?>signup.php"><img src="<?php echo IMAGE_PATH; ?>signup_btn_new.gif" alt="" border="0" /></a></div>
						</div>
					</div>
				</div>
				<!--End getstarted -->
				<div class="clr"></div>
			</div>
			<!--Start Twitter -->
			<div class="homeBotCon">
				<div class="twitterHd">Follow @eventgrabber on twitter</div>
				<div class="twitterText">
					<?php $twitter = getTwitterStatus("eventgrabber"); ?>
					<?php 
						$tweet = $twitter['tweet'];
						$en = strpos($tweet,"http://ht");
						echo $tweet;
					?>
					<span><?php echo $twitter['date'];?></span>
					<a href="http://www.twitter.com/eventgrabber"><img src="<?php echo IMAGE_PATH; ?>twitter_icon.gif" alt="twitterimg" style="position:absolute; right:0; top:-11px;" border="0"/></a>
				</div>
				<div class="clr"></div>
			</div>
			<!--End Twitter -->
		</div>
	</div>
	
<?php include_once('includes/footer.php');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        