<?php	
	require_once('admin/database.php');
	require_once('site_functions.php');
	include('rssreader.php');
	include("nba_table_packages.php");

	if(isset($_POST['subcribeNb'])){
		$email		= $_POST['subcribeNbaEmail'];
		$already	= getSingleColumn('id',"select * from `subcribe_nba` where `email`='$email'");
		if($already > 0){
			$emailMsg = "<div class='head'>Error</div><div class='as_subc_text'>Email address is already subscribed</div>";
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
				
				$emailMsg = "<div class='head'>Thank you for Subscribing</div><div class='as_subc_text'>Confirmation link Has Been Sent<br>To Your Email Address </div>";}
			else{
				$emailMsg = "<div class='head'>Error</div><div class='as_subc_text'>Try again later.</div>";
				}
		}
	}

	$meta_title 	=	"NBA ALLSTAR - Events";
	$meta_kwords 	=	"NBA ALLSTAR";
	
	$videos = array(
		"HwRql1cuL0E" => "NBA All-Star Weekend 2011 Highlights",
		"jshtZrytf2k" => "NBA All-Star Weekend Dance-Off",
		"dGaPynVHsqM" => "NBA All Star Weekend 2011 Mixtape",
		"OApevA5lJDY" => "Rick Ross party at NBA All-Star Weekend",
		"VZPdjfVjAOw" => "Dwight Howard All-Star Weekend After Party",
		"p6qU_jILgnk" => "Chris Brown live at Diddy's NBA Allstar Party",
		"_REUpQ7Eahg" => "Derrick Rose Season MVP Mix",
		"GoDhpSyUFG0" => ""
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
		$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/load_na_event.php",  
			data:"day="+id, 
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#as_events").html(resp);
				
			},
			complete: function()
			{
				hideOverlayer();
			}
		});	
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
		if(id=='FACEBOOK'){
			$('#as_twit_widget').hide();
			$('#as_fb_widget').fadeIn(1000);
		}
		else{
			$('#as_fb_widget').hide();
			$('#as_twit_widget').fadeIn(1000);
		}
		$(social).removeClass("active");
		$(this).addClass("active");	
	});
	
	


 
 $('span.slideRight').live("mouseenter",function(){
  	//$(this).find('.imgToMove').animate({top:'100%',queue:false}, 500 , function () { $('.imgToMove').css("display","none") }); // Slide right
	$(this).find('.imgToMove').stop(true,true).fadeOut(700);
 });
 
 $('span.slideRight').live("mouseleave",function(){
 	$(this).find('.imgToMove').stop(true,true).fadeIn(800);
 /* $(this).find('.imgToMove').css("display","block");
  $(this).find('.imgToMove').animate({top:'0px'},{queue:false,duration:500}); // Slide right*/
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
	
});


$(document).ready(function(){

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
			var right = 235;
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
			var right = 235;
			right = right*current;
			$('#rec').animate({'right' : right}, 500);
			if(current==(numbrRecord-4)){
				$('.as-right-control').css('display','none');
			}
		}
	});
});



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
<body>
<div class="as_mainWrapper">
  <div class="as_main2Wrapper">
    <!--Header will be put here-->
    <div class="as_east">
      <div class="as_contentWrapper">
		 <div class="as_contentBoxTop">
			<a href="http://www.weareallstarweekend.com" class="banner" target="_blank"><img src="<?php echo ABSOLUTE_PATH; ?>nba/weareallstarweekend_banner_eventgrabber.png"></a>
          <div class="as_contentBoxBottom">
            <div class="as_contentBoxCenter">
              <div class="as_innr">
                <div class="as_innr_left">
                  <div class="as_innr_left1">
                    <div class="as_featuredEventMenus">
                      <ul class="as_featuredEventMenusUl">
                        <?php
							$specials_id = $_GET['id'];
							
							$res = mysql_query("SELECT s.event_id, s.specials_id, e.event_image, e.event_name, d.event_date, d.event_id, d.id, t.date_id, t.start_time FROM  event_dates d, event_times t, special_event s, events e WHERE  s.specials_id=$specials_id && e.id=s.event_id && e.is_expiring=1 && e.event_type!=0 && d.expired=0 && s.event_id=d.event_id && d.id=t.date_id GROUP BY e.id ORDER BY d.event_date,t.start_time ASC");
							
							$z=0;
							while($row = mysql_fetch_array($res)){
							$z++;
							if($z==1){
								$event_id					=	$row['event_id'];
								$first_special_event_image	=	$row['event_image'];
							}
							?>
						<li <?php if ($z==1){ echo 'class="active"'; } ?> onClick="getFlyer1('<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $row['event_id']; ?>&type=nba');"> <span class="as_femheading">
                          <?php										 
						  if(strlen($row['event_name']) > 19)
							  echo substr($row['event_name'],0,19)."...";
						  else
							  echo $row['event_name'];
						  ?>
                          </span> <span class="as_femdate">
                          <?php
							echo date('D, M d, Y', strtotime($row['event_date']));
							?>
                          at
                          <?php
							$event_time = getEventTime($event_dateT[1]);
							echo date('h:i A', strtotime($row['start_time']));
							?>
                          </span>
						  </li>
                        <?php }?>
                      </ul>
                    </div>
                    <!-- end as_featuredEventMenus -->
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
                              <iframe id="flyFrame" src="<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $event_id;?>&type=nba" width="520" height="803" scrolling="auto" frameborder="0" style="border:none"></iframe>
                              <?php } ?>
                            </div>
                            <!-- end sponsorFlip -->
                            <div class="sponsorData">
                              <?php
										//	 	include("sp_details.php");
											  ?>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- end flayermain -->
                      <!--<div class="as_best_of" style=""><a target="_blank" href="<?php echo ABSOLUTE_PATH; ?>ads.php">Best of Orlando</a></div>-->
                    </div>
                    <div class="as_clear"></div>
                  </div>
                  <!-- end as_innr_left1 -->
                </div>
                <!-- end as_innr_left -->
                <div class="as_innr_right">
                  <div class="sa_allstar_converstation"> <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/tw.png" align="left"></a> <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/fb.png" align="left"></a> <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/in.png" align="left"></a> <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>nba/gm.png" align="left"></a> </div>
                  <div class="as_social_bar">
                    <ul>
                      <li class="" id="FACEBOOK"><a href="javascript:void(0);">FACEBOOK <img src="<?php echo ABSOLUTE_PATH; ?>nba/fb.png" align="right"></a></li>
                      <li class="active" id="TWITTER"><a href="javascript:void(0);">TWITTER<img src="<?php echo ABSOLUTE_PATH; ?>nba/tw.png" align="right"></a></li>
                    </ul>
                  </div>
                  <div class="as_twit_widget" id="as_twit_widget">
                    <?php
										include("twitter2/config.php");
									?>
                    <script type="text/javascript" src="http://platform.twitter.com/anywhere.js?id=<?=CONSUMER_KEY?>&v=1"></script>
                    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                    <script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>twitter2/js/twitter-widget.js"></script>
                    <script type="text/javascript">
										twttr.anywhere(function (T) {
										   T("#tweet").hovercards({ expanded: true });
										 });
									</script>
                    <div class="twitterfeed">
                      <div id="tweet"></div>
                    </div>
                  </div>
                  <!-- end as_twit_widget -->
                  <div class="as_fb_widget" id="as_fb_widget"  style="display:none;">
                    <?php
									require_once("facebook2/config.php");
									$output = json_decode(stripslashes(file_get_contents("./facebook2/cache/facebook-json.txt")));
								?>
                    <div class="twiending">
                      <div class="facebookWall">
                        <ul>
                          <? foreach($output as $myfeed){ ?>
                          <li> <img src="https://graph.facebook.com/<?=$myfeed->from->id?>/picture" class="avatar" />
                            <div class="status">
                              <h2> <a href="http://www.facebook.com/profile.php?id=<?=$myfeed->from->id?>" target="_blank">
                                <?=$myfeed->from->name?>
                                </a></h2>
                              <p class="message">
                                <?=$myfeed->message?>
                              </p>
                              <? if($myfeed->type == "link"){ ?>
                              <div class="attachment">
                                <? if($myfeed->picture){?>
                                <img class="picture" src="<?=$myfeed->picture?>" />
                                <? } ?>
                                <div class="attachment-data">
                                  <p class="name"><a href="<?=$myfeed->link?>" target="_blank">
                                    <?=$myfeed->name?>
                                    </a></p>
                                  <p class="caption">
                                    <?=$myfeed->caption?>
                                  </p>
                                  <p class="description">
                                    <?=$myfeed->description?>
                                  </p>
                                </div>
                              </div>
                              <? } ?>
                            </div>
                          </li>
                          <? } ?>
                        </ul>
                      </div>
                      <!-- end facebookWall -->
                    </div>
                    <!-- end twiending -->
                  </div>
                  <!-- end as_fb_widget -->
                  <div align="center"><a href="<?php echo ABSOLUTE_PATH; ?>ads.php" target="_blank"><img src="<?php echo IMAGE_PATH; ?>best_spots_in_orlando.png" ></a></div>
                </div>
                <!-- end as_innr_right -->
                <div class="as_clear"></div>
              </div>
              <!-- end as_innr -->
              <div class="as_event_listing">
                <div class="as_event_liting_head_bg"> <span class="head">EVENT LISTING</span>
                  <ul>
                    <?php
							$daysArray = getDays($specials_id);
							$i=0;
							foreach($daysArray as $dy){
							if($dy!=''){
								$i++;
								if($i==1)
									$first_day = $dy;
							?>
                    <li <?php if ($i=='1'){ echo 'class="active"'; } ?> id="<?php echo $dy; ?>"><a href="javascript:void(0)"><?php echo $dy; ?></a></li>
                    <?php
								}
							} 
							?>
                  </ul>
                  <span class="as_see_more"><a href="#">See More</a></span> </div>
                <!-- end as_event_liting_head_bg -->
                <ul class="as_events" id="as_events">
                  <?php
						$res = mysql_query("select * from `special_event` where `specials_id`='$specials_id'");
						while($row	= mysql_fetch_array($res)){
							$t_event_id		= $row['event_id'];
							$event_name		= getSingleColumn('event_name',"select * from `events` where `id`='$t_event_id'");
							$event_type		= getSingleColumn('event_type',"select * from `events` where `id`='$t_event_id'");
								$event_dateT	= getEventStartDateFB($t_event_id);
								$event_date		= $event_dateT[0];
								$event_time		= getEventTime($event_dateT[1]);
								
								 $event_start_day	= strtoupper(date('l', strtotime($event_dateT[0])));
								
								if($event_type=='0' && $event_start_day==$first_day){
									if(strlen($event_name) > 38)
										$event_name = substr($event_name,0,38)."...";
							?>
                  <li> <span class="title"><a href="<?php  echo getEventUrl($t_event_id); ?>" target="_blank"><?php echo $event_name; ?></a></span> <span class="date"><?php echo date('D. M d',strtotime($event_date))." &nbsp;".date('h:m A',strtotime($event_time['start_time'])); ?></span> </li>
                  <?php
									} // end if $event_type==0
									
							} // end 
							?>
                </ul>
                <div class="as_clear"></div>
              </div>
              <!-- end as_event_listing -->
              <div class="as_event_listing">
                <div class="as_vid_left">
                  <div class="as_event_liting_head_bg2"> <span class="head">
                    <ul class="as_vid_gal_tab">
                      <li class="active" id="VIDEOS"><a href="javascript:void(0)">VIDEOS</a></li>
                      <li id="GALLERY"><a href="javascript:void(0)">PHOTO GALLERY</a></li>
                    </ul>
                    </span> </div>
                  <!-- end as_event_liting_head_bg2 -->
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
                      <li <?php echo $style; ?>> <a class="video" href="http://www.youtube.com/v/<?php echo $video_id; ?>?fs=1&autoplay=1" title="<?php echo $video_name; ?>"> <img src="http://i2.ytimg.com/vi/<?php echo $video_id; ?>/hqdefault.jpg" width="136" height="96"> </a> <br>
                        <a class="video" href="http://www.youtube.com/v/<?php echo $video_id; ?>?fs=1&autoplay=1" title="<?php echo $video_name; ?>"> <?php echo $video_name; ?></a></li>
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
												$style = 'style="padding-right:0; height:96px;"';
											else
												$style = ' style="height:96px;"';?>
                      <li <?php echo $style; ?>> <a class="fancybox" href="<?php echo IMAGE_PATH; ?>annual/<?php echo $row['image']; ?>"> <img src="<?php echo IMAGE_PATH; ?>annual/th_<?php echo $row['image']; ?>"> </a> </li>
                      <?php }
									}else{
									echo "No Image Found";
									} ?>
                    </ul>
                    <div class="as_clear"></div>
                  </div>
                </div>
                <!-- end as_vid_left -->
                <div class="as_vid_right">
                  <div class="head">Click to Reserve Table</div>
                   <div class="srol" id="srol">	
	<span class="transitionStyle slideRight">
    <div class="highendMain" style="position:absolute; z-index:1; ">
    <div id="floorplan">
						<img src="<?php echo IMAGE_PATH; ?>floorplan_Shletair.png" id="floorplanbackground" width="270px">      
						<br class="clear">
						<?php
						foreach ($table as $index => $value){
						$already_tabl	= mysql_query("select * from `orders` where `type`='table' && `total_price`!='' && `main_ticket_id`='$index'");
						$already_tabl	= mysql_num_rows($already_tabl);
						?>
							<a <?php if ($already_tabl==0 && !in_array($index, $reserved_tables)){?> href="<?php echo ABSOLUTE_PATH_SECURE; ?>reserve-table.php?id=<?php echo $sidl=base64_encode($index); ?>" <?php } else{ ?>
							href="javascript:void(0)"
							<?php } ?>>
								<div id="tablenumber<?php echo $index; ?>" class="tableicon<?php
							
							 if ($already_tabl==0 && !in_array($index, $reserved_tables)){ echo ' available'; } 
							 ?>" style="height: <?php echo $value['height']; ?>; left: <?php echo $value['left']; ?>;top: <?php echo $value['top']; ?>; width: <?php echo $value['width']; ?>;">
							<div class="tooltip" id=""><img src="<?php echo IMAGE_PATH; ?>tootiparrow.gif" class="ttarrow">Table: <b><?php echo $index; ?></b><br />
							Minimum Spend: $<?php echo $value['spend']; ?><br />
							<?php echo $value['tickets']; ?> complimentary admission tickets<br>
							are included with this table
							</div>
							</div>
							</a>
						<?php } ?>
					</div>
					<!--/div floorplan-->
    </div>

    <img class="imgToMove left" style="border:none" src="<?php echo IMAGE_PATH; ?>1328156300.png"/>

   </span>
					
		  
		  
					
					
					
				   </div>
                  </div>
                <!-- end as_vid_right -->
                <div class="as_clear"></div>
              </div>
              <!-- end as_event_listing -->
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
                <div class="as_fb_box as_subscribe">
                  <div class="head">SIGNUP FOR EVENTGRABBER</div>
                  <br>
                  <br>
                  <div align="center"><a href="<?php echo ABSOLUTE_PATH;?>signup.php" target="_blank"><img src="<?php echo IMAGE_PATH; ?>eventgrabber_signup.png" alt="" title=""></a></div>
                  <br>
                  <br>
                  &nbsp; </div>
                <div class="as_fb_box as_subscribe">
                  <?php
							if($emailMsg){
								echo $emailMsg;
								}
							else{?>
                  <div class="head">SUBSCRIBE TO NBA ALLSTAR UPDATES</div>
                  <div class="as_subc_text"> Sign-up with your Email to<br>
                    receive newest update on<br>
                    NBA ALL-STARS </div>
                  <div class="as_search">
                    <form method="post" onSubmit="return checkemailValid();">
                      <input type="text" name="subcribeNbaEmail" id="subcribeNbaEmail" value="Email Address" onFocus="if(this.value=='Email Address'){this.value='';}" onBlur="if(this.value==''){this.value='Email Address';}" class="input">
                      <input type="image" src="<?php echo ABSOLUTE_PATH; ?>nba/as_searchSubmit.png" value="Submit" name="subcribeNb">
                      <input type="hidden" value="Submit" name="subcribeNb" >
                      <br>
                      &nbsp;
                    </form>
                  </div>
                  <!-- end as_search -->
                  <?php
							}?>
                </div>
                <div class="as_clear"></div>
              </div>
              <!-- end as_bottom_boxs -->
            </div>
            <!-- end as_contentBoxCenter -->
          </div>
          <!-- end as_contentBoxBottom -->
        </div>
        <!-- end as_contentBoxTop -->
      </div>
      <!--end as_contentWrapper-->
      <div class="clr"></div>
      <div class="as_footer"> &copy; 2012 eventgrabber.com </div>
    </div>
    <!-- end as_east-->
  </div>
  <!--end as_main2Wrapper-->
</div>
<!--end as_mainWrapper-->
<input type="hidden" id="absolute" value="<?php echo ABSOLUTE_PATH; ?>">
</body>
</html><style>
.fbConnectWidgetFooter{
	display:none;
	}
</style>
