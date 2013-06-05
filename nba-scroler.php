<?php	
	require_once('admin/database.php');
	require_once('site_functions.php');
	include('rssreader.php');
	include("nba_packages.php");


$meta_title 	=	"NBA ALLSTAR - Events";
$meta_kwords 	=	"NBA ALLSTAR";

$videos = array(
	"HwRql1cuL0E" => "NBA All-Star Weekend 2011 Highlights",
	"jshtZrytf2k" => "NBA All-Star Weekend Dance-Off",
	"dGaPynVHsqM" => "NBA All Star Weekend 2011 Mixtape",
	"OApevA5lJDY" => "Rick Ross party at NBA All-Star Weekend",
	"VZPdjfVjAOw" => "Dwight Howard All-Star Weekend After Party",
	"p6qU_jILgnk" => "Chris Brown live at Diddy's NBA Allstar Party",
	"_REUpQ7Eahg" => "Derrick Rose Season MVP Mix"
	);
	
include_once('includes/header.php');

?>
<style>
.header { display:none!important; }
.headerOut { height:40px!important; }
.fbConnectWidgetTopmost{ border:none!important;}
</style>
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
$(document).ready(function(){

	$('.fbConnectWidgetTopmost').css('border','none');
	$('.fbConnectWidgetFooter').hide();
	
	var abs_url = $('#absolute').val();
	
	$('ul.as_featuredEventMenusUl li').click(function(){
		$('ul.as_featuredEventMenusUl li').removeClass('active');
		$(this).addClass('active');
	});

	$('ul.as_featuredEventMenusUl li').mouseover(function(){
		$(this).addClass('active2');
	});
	
	$('ul.as_featuredEventMenusUl li').mouseout(function(){
		$(this).removeClass('active2');
	});

	
	var day = $('li', '.as_event_liting_head_bg');
	$(day).click(function(){
		var id = $(this).attr('id');
		$(day).removeClass("active");
		$(this).addClass("active");	
	});

	var gal = $('li', '.as_event_liting_head_bg2');
	$(gal).click(function(){
		var id = $(this).attr('id');
		$(gal).removeClass("active");
		$(this).addClass("active");	
		if(id=='VIDEOS'){
			$('#as_gallery').hide();
			$('#as_videos').fadeIn(1000);
		}else{
			$('#as_videos').hide();
			$('#as_gallery').fadeIn(1000);
		}
	});
	
	var social = $('li', '.as_social_bar');
	$(social).click(function(){
		var id = $(this).attr('id');
		$(social).removeClass("active");
		$(this).addClass("active");	
	});
	
	
	
});


</script>

<link href="<?php echo ABSOLUTE_PATH; ?>nba.css" rel="stylesheet" type="text/css">
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" ></script>-->
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-1.4.min.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script>
$(document).ready(function(){
	
	$('ul.as_featuredEventMenusUl li').click(function(){
		$('ul.as_featuredEventMenusUl li').removeClass('active');
		$(this).addClass('active');
	});

	$('ul.as_featuredEventMenusUl li').mouseover(function(){
		$(this).addClass('active2');
	});
	
	$('ul.as_featuredEventMenusUl li').mouseout(function(){
		$(this).removeClass('active2');
	});
	
	
	$(".video").click(function() {
		$.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'transitionIn'	: 'none',
			'transitionOut'	: 'none',
			'title'			: this.title,
			'width'			: 640,
			'height'		: 385,
			'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
			'type'			: 'swf',
			'swf'			: {
			'wmode'				: 'transparent',
			'allowfullscreen'	: 'true'
			}
		});

		return false;
	});
	
	$(".fancybox").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	
	$(".pkgBox").fancybox({
		'titleShow'		: false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
	
	
	// 
	var container		=	$('.as_pkg_box');
	var numbrRecord		=	$('li', container).size();
	var innerWidth		=	$('img', container).innerWidth()+20;
	var width 			=	numbrRecord*innerWidth;
	$('#rec').css('width',width);
	$('#rec').css('position','relative');
	var current = 0;
	if(numbrRecord <= 4){
		$('.as-left-control').css('display','none');
		$('.as-right-control').css('display','none');
	}
	$('.as-left-control').css('display','none');

	var i = 0;
	$('.as-left-control').click(function(){
	if(current!=0){
		$('.as-right-control').css('display','block');
		current--;
		var right = 230;
		right = right*current;
		$('#rec').animate({'right' : right}, 500);
		if(current==0){
			$('.as-left-control').css('display','none');
		}
	}
});

$('.as-right-control').click(function(){
	if(current!=(numbrRecord-4)){
		$('.as-left-control').css('display','block');
		current++;
		var right = 230;
		right = right*current;
		$('#rec').animate({'right' : right}, 500);
		if(current==(numbrRecord-4)){
			$('.as-right-control').css('display','none');
		}
	}
});
	
	
});
</script>

<style>

.as_pkg_box{
	padding-left:0;
	}



</style>
<body>
<div class="as_mainWrapper">
  <div class="as_main2Wrapper">
    <!--Header will be put here-->
	<div class="as_east">
    <div class="as_contentWrapper">
		<div class="as_contentBoxTop">
		<span class="as-left-control"></span>						
		<span class="as-right-control"></span>
		<div style="height: 113px; margin: 0 21px; overflow: hidden; position: relative; width: 932px;">
			<div class="as-sugesstions">
				<div id="rec">
					<ul class="as_pkg_box">
						<?php
						foreach ($reservations as $index => $value){
							$i = $index+1;?>
							<li><a href="nba_package_box.php?id=<?php echo $i; ?>" class="pkgBox" ><img src="<?php echo $value['image']; ?>"></a></li>
						<?php  } ?>
						<div class="as_clear"></div>
					</ul>
				</div>
			</div>
		</div>
			<div class="as_contentBoxBottom">
				<div class="as_contentBoxCenter">
					<div class="as_innr">
						<div class="as_innr_left">
							<div class="as_innr_left1">
								<div class="as_featuredEventMenus">
									<ul class="as_featuredEventMenusUl">
										<?php
										  $specials_id = $_GET['id'];
										  
										  $res = mysql_query("select * from `special_event` where `specials_id`='$specials_id' ORDER BY `id` ASC");
										  $events_id = array();
										  while($row = mysql_fetch_array($res)){
											$ev_id = $row['event_id'];
											$res2 = mysql_query("select * from `event_dates` where `event_id`='$ev_id' ORDER BY `event_date` ASC LIMIT 0, 1");
											while($row2 = mysql_fetch_array($res2)){
												$events_id[$ev_id] = $row2['event_date'];
												}
											}
											asort($events_id);
											foreach($events_id as $events_id => $index){
												$specials_array = getSpecialsEvents($events_id,'flyer');  // flyer or simple
												if($specials_array['id']!=''){
													$z++;
													if($z==1){
														$event_id		=	$specials_array['id'];
														$first_special_event_image	=	$specials_array['event_image'];
														}
												?>
										  <li <?php if ($z==1){ echo 'class="active"'; } ?> onClick="getFlyer1('<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $specials_array['id']; ?>');"> <span class="as_femheading">
											<?php
										 
										  if(strlen($specials_array['event_name']) > 27)
											  echo substr($specials_array['event_name'],0,23)."...";
										  else
											  echo $specials_array['event_name'];
											  
										  ?>
											</span> <span class="as_femdate">
											<?php
											$event_dateT = getEventStartDateFB($specials_array['id']);
											echo date('D, M d, Y', strtotime($event_dateT[0]));
											?>
											at
											<?php
											$event_time = getEventTime($event_dateT[1]);
											echo date('h:i A', strtotime($event_time['start_time']));
											?>
											</span> </li>
										  <?php }
										  }?>
									</ul>
								</div> <!-- end as_featuredEventMenus -->
								 <script>
									function getFlyer1(url){
										$("#flyFrame").attr('src', url);
									}
								</script>
								<div class="as_flyer">
									<div id="flayermain" >
									  <div style="position:relative;">
										<div class="sponsor" id="spons">
										  <div class="sponsorFlip">
										  <?php if($event_id){ ?>
												<iframe id="flyFrame" src="<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $event_id;?>" width="520" height="803" scrolling="auto" frameborder="0" style="border:none"></iframe>
											<?php } ?>
										  </div> <!-- end sponsorFlip -->
										  <div class="sponsorData">
											<?php
										//	 	include("sp_details.php");
											  ?>
										  </div>
										</div>
									  </div>
									</div> <!-- end flayermain -->
								</div>
								<div class="as_clear"></div>
							</div> <!-- end as_innr_left1 -->						
						</div> <!-- end as_innr_left -->
						
						<div class="as_innr_right">
							<div class="sa_allstar_converstation">
								<a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/tw.png" align="left"></a>
								<a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/fb.png" align="left"></a>
								<a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/in.png" align="left"></a>
								<a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/gm.png" align="left"></a>
							</div>
							<div class="as_social_bar">
								<ul>
									<li class="" id="FACEBOOK"><a href="javascript:void(0);">FACEBOOK <img src="<?php echo ABSOLUTE_PATH; ?>nba/fb.png" align="right"></a></li>
									<li class="active" id="TWITTER"><a href="javascript:void(0);">TWITTER<img src="<?php echo ABSOLUTE_PATH; ?>nba/tw.png" align="right"></a></li>
								</ul>
							</div>
							<div class="as_twit_widget">
								<img src="<?php echo ABSOLUTE_PATH; ?>nba/twt_demo.png">
							</div>
						</div> <!-- end as_innr_right -->
						<div class="as_clear"></div>
					</div> <!-- end as_innr -->
					<div class="as_event_listing">
						<div class="as_event_liting_head_bg">
							<span class="head">EVENT LISTING</span>
							
							
							<ul>
								<li id="THURSDAY"><a href="javascript:void(0)">THURSDAY</a></li>
								<li id="FRIDAY" class="active"><a href="javascript:void(0)">FRIDAY</a></li>
								<li id="SATURDAY"><a href="javascript:void(0)">SATURDAY</a></li>
								<li id="SUNDAY"><a href="javascript:void(0)">SUNDAY</a></li>
							</ul>
							<span class="as_see_more"><a href="#">See More</a></span>
						</div> <!-- end as_event_liting_head_bg -->
						<ul class="as_events">
						<?php
						$res = mysql_query("select * from `special_event` where `specials_id`='$specials_id'");
						while($row	= mysql_fetch_array($res)){
							$t_event_id		= $row['event_id'];
							$event_name		= getSingleColumn('event_name',"select * from `events` where `id`='$t_event_id'");
							$event_type		= getSingleColumn('event_type',"select * from `events` where `id`='$t_event_id'");
							$event_dateT	= getEventStartDateFB($t_event_id);
							$event_date		= $event_dateT[0];
							$event_time		= getEventTime($event_dateT[1]);
							if($event_type=='0'){
								if(strlen($event_name) > 38)
									$event_name = substr($event_name,0,38)."...";
						?>
									<li>
									
										<span class="title"><a href="<?php  echo getEventUrl($t_event_id); ?>" target="_blank"><?php echo $event_name; ?></a></span>
										<span class="date"><?php echo date('D. M d',strtotime($event_date))." &nbsp;".date('h:m A',strtotime($event_time['start_time'])); ?></span>
									</li>
							<?php
								} // end if $event_type==0
							}
							?>
						</ul>
						<div class="as_clear"></div>
					</div> <!-- end as_event_listing -->
					<div class="as_event_listing">
						<div class="as_vid_left">
							<div class="as_event_liting_head_bg2">
								<span class="head">
									<ul class="as_vid_gal_tab">
										<li class="active" id="VIDEOS"><a href="javascript:void(0)">VIDEOS</a></li>
										<li id="GALLERY"><a href="javascript:void(0)">PHOTO GALLERY</a></li>
									</ul>
								</span>
							</div> <!-- end as_event_liting_head_bg2 -->
							<div class="as_videos" id="as_videos">
								<ul>
								<?php 
									$i=0;
									foreach($videos as $video_id => $video_name){
									$i++;
										if($i%4==0 && $i!=1)
											$style = 'style="padding-right:0"';
										else
											$style = '';?>
										<li <?php echo $style; ?>>
										<a class="video" href="http://www.youtube.com/v/<?php echo $video_id; ?>?fs=1&autoplay=1" title="<?php echo $video_name; ?>">
											<img src="http://i2.ytimg.com/vi/<?php echo $video_id; ?>/hqdefault.jpg" width="136" height="96">
										</a>
										<br>
										<a class="video" href="http://www.youtube.com/v/<?php echo $video_id; ?>?fs=1&autoplay=1" title="<?php echo $video_name; ?>">
										<?php echo $video_name; ?></a></li>
										<?php } ?>
									</ul>
									<div class="as_clear"></div>
								</div>
								
								<div class="as_videos" id="as_gallery" style="display:none">
								<ul>
								<?php 
									$i=0;
									$res = mysql_query("select * from `annual_images` where `specials_id`='$specials_id'");
									if(mysql_num_rows($res)){
										while($row = mysql_fetch_array($res)){
										$i++;
											if($i%4==0 && $i!=1)
												$style = 'style="padding-right:0"';
											else
												$style = '';?>
											<li <?php echo $style; ?>>
											<a class="fancybox" href="<?php echo IMAGE_PATH; ?>annual/<?php echo $row['image']; ?>">
											<img src="<?php echo IMAGE_PATH; ?>annual/th_<?php echo $row['image']; ?>">
												
											</a>
											</li>
									<?php }
									}else{
									echo "No Image Found";
									} ?>
									</ul>
									<div class="as_clear"></div>
								
								
								</div>
						</div> <!-- end as_vid_left -->
						<div class="as_vid_right">
							<div class="head">NBA ALLSTAR FEED</div>
							<div class="srol">
							<?php
							   $url="http://www.nba.com/magic/rss.xml";
								$rss=new rssFeed($url);
								if($rss->error){
									print "<h1>Error:</h1>\n<p><strong>$rss->error</strong></p>";
								}else{
								  $rss->parse();
								  $rss->showStories();
								}
								
								
								$url="http://www.nba.com/rss/nba_rss.xml";
								$rss=new rssFeed($url);
								if($rss->error){
									print "<h1>Error:</h1>\n<p><strong>$rss->error</strong></p>";
								}else{
								  $rss->parse();
								  $rss->showStories();
								}
								
							?>
							
							
							</div>
						</div> <!-- end as_vid_right -->
						
						
						<div class="as_clear"></div>
						
					
					</div> <!-- end as_event_listing -->
					<div class="as_bottom_boxs">
						<div class="as_fb_box">
							<div style="margin:-1px;overflow:hidden; height:145px;">
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
						<div class="fb-like-box" data-border-width="0" data-href="https://www.facebook.com/eventgrabber"  data-height="165" data-width="287px" data-show-faces="true" data-stream="false" data-header="false"></div>
							<!--<img src="<?php echo ABSOLUTE_PATH; ?>nba/fb_demo.png">-->
							</div>
							
						</div>
						
						<div class="as_fb_box as_recent_activt">
						<div style="margin:-5px -1px -1px -1px">
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
						<div class="fb-activity" data-site="www.eventgrabber.com" data-width="302" data-height="200" data-header="true" data-recommendations="false"></div>
							<!--<img src="<?php echo ABSOLUTE_PATH; ?>nba/as_fb_recent.png">-->
							</div>
						</div>
						
						<div class="as_fb_box as_subscribe">
							<div class="head">SUBSCRIBE TO NBA ALLSTAR UPDATES</div>
							<div class="as_subc_text">
							Sign-up with your Email to<br>
							receive newest update on<br>
							NBA ALL-STARS
							</div>
							<div class="as_search">
								<form method="post">
								<input type="text" name="srachKeyWord" value="Email Address" onFocus="if(this.value=='Email Address'){this.value='';}" onBlur="if(this.value==''){this.value='Email Address';}" class="input">
								<input type="image" src="<?php echo ABSOLUTE_PATH; ?>nba/as_searchSubmit.png" value="Submit" name="search">
								<input type="hidden" value="Submit" name="search" ><br>&nbsp;
								</form>
							</div> <!-- end as_search -->
						</div>
						<div class="as_clear"></div>
					</div> <!-- end as_bottom_boxs -->
				</div> <!-- end as_contentBoxCenter -->
			</div> <!-- end as_contentBoxBottom -->
		</div> <!-- end as_contentBoxTop -->
	</div>
    <!--end as_contentWrapper-->
    <div class="clr"></div>
    <div class="as_footer"> &copy; 2012 eventgrabber.com </div>
	</div> <!-- end as_east-->
  </div>
  <!--end as_main2Wrapper-->
</div>
<!--end as_mainWrapper-->
</body>
</html>
<style>
.fbConnectWidgetFooter{
	display:none;
	}
</style>