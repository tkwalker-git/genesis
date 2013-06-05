<?php	
	require_once('admin/database.php');
	require_once('site_functions.php');


$meta_title 	=	"NBA ALLSTAR - Events";
$meta_kwords 	=	"NBA ALLSTAR";

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
	
});




function view_event_guide(id,abs_url){
	$.ajax({  
			type: "POST",
			url: abs_url+"ajax/nbamore.php",  
			data: "id="+id ,
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
				$('#event_guide').html(html);
				$('#view_event_guide_bt').hide();
			}, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});
	}
	
</script>
<!--<link href="<?php echo ABSOLUTE_PATH; ?>style.css" rel="stylesheet" type="text/css">-->
<link href="<?php echo ABSOLUTE_PATH; ?>allstar/style.css" rel="stylesheet" type="text/css">
<body>
<div class="as_mainWrapper">
  <div class="as_main2Wrapper">
    <!--Header will be put here-->
    <div class="as_contentWrapper">
      <div class="as_main_title"><img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_star.png" align="left">Featured Events</div>
      <div class="as_inner">
        <div class="as_left_area">
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
          </div>
          <script>
			function getFlyer1(url){
				$("#flyFrame").attr('src', url);
			}
		</script>
          <!-- end as_featuredEventMenus -->
          <div class="as_flyer_area">
            <div id="flayermain" >
              <div style="position:relative;">
                <div class="sponsor" id="spons">
                  <div class="sponsorFlip">
                    <iframe id="flyFrame" src="<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $event_id;?>" width="520" height="803" scrolling="auto" frameborder="0" style="border:none"></iframe>
                  </div>
                  <div class="sponsorData">
                    <?php
				//	 	include("sp_details.php");
					  ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="clr"></div>
          <div class="as_more_events">
            <div class="as_moreTitle">More Events</div>
            <span id="event_guide">
            <ul>
              <?php
				$res = mysql_query("select * from `special_event` where `specials_id`='$specials_id' ORDER BY `id` ASC");
					$z = 0;
					$events_id = array();
					while($row = mysql_fetch_array($res)){
					$ev_id = $row['event_id'];
					
				$res2 = mysql_query("select * from `event_dates` where `event_id`='$ev_id' ORDER BY `event_date` ASC LIMIT 0, 1");
				
				while($row2 = mysql_fetch_array($res2)){
				
				$events_id[$ev_id] = $row2['event_date'];
				
				}}
				asort($events_id);
			
				
				foreach($events_id as $events_id => $index){
				
				
					$specials_array = getSpecialsEvents($events_id,'simple');  // flyer or simple
				$limit = 3;
					if($specials_array['id']!=''){
						$n++;
						if( $n <= $limit ){
						$eventUrl = getEventURL($specials_array['id'])
		?>
              <li class="as_more_box"> <span><a href="<?php echo $eventUrl; ?>">
                <?php 
					 if(strlen($specials_array['event_name']) > 33)
						  echo substr($specials_array['event_name'],0,33)."...";
					  else
						  echo $specials_array['event_name'];
					?>
                </a></span> <br>
                <?php
					$event_dateT = getEventStartDateFB($specials_array['id']);
					echo date('D, F d, Y', strtotime($event_dateT[0]));s
				?>
                at
                <?php
					$event_time = getEventTime($event_dateT[1]);
					echo date('h:i A', strtotime($event_time['start_time']));
				?>
                <br>
                <a href="#">more info</a> </li>
              <?php }
			  if($n > $limit){
			 	 $showButton='yes';
				}
			}
		}
		?>
            </ul>
            </span>
            <?php
			if($showButton=='yes'){?>
            <div align="right" class="clr" style="padding-top:10px"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_viewall.png" id="view_event_guide_bt" onClick="view_event_guide(<?php echo $specials_id; ?>,'<?php echo ABSOLUTE_PATH; ?>')" style="cursor:pointer"> </div>
            <?php }
				else{
					echo "<div class='clr'></div>";
				}
				?>
          </div>
          <!-- end as_more_events -->
          <div class="as_bottom_box">
            <div class="as_bottom_boxTitle">FRIEND ACTIVITY</div>
            <div class="as_bottom_boxDetail">
              <div class="as_box_enrty"> <span class="as_yellow"><strong>Jane</strong></span> <span class="as_blue"><strong>is attending Cheers The Happy Hour...</strong></span> Thurs. Feb 18, 2011 at 5:30PM </div>
              <div class="as_box_enrty"> <span class="as_yellow"><strong>Jane</strong></span> <span class="as_blue"><strong>is attending Cheers The Happy Hour...</strong></span> Thurs. Feb 18, 2011 at 5:30PM </div>
              <div class="as_box_enrty"> <span class="as_yellow"><strong>Jane</strong></span> <span class="as_blue"><strong>is attending Cheers The Happy Hour...</strong></span> Thurs. Feb 18, 2011 at 5:30PM </div>
              <div class="as_box_enrty"> <span class="as_yellow"><strong>Jane</strong></span> <span class="as_blue"><strong>is attending Cheers The Happy Hour...</strong></span> Thurs. Feb 18, 2011 at 5:30PM </div>
              <div class="as_box_enrty"> <span class="as_yellow"><strong>Jane</strong></span> <span class="as_blue"><strong>is attending Cheers The Happy Hour...</strong></span> Thurs. Feb 18, 2011 at 5:30PM </div>
              <div class="as_box_enrty"> <span class="as_yellow"><strong>Jane</strong></span> <span class="as_blue"><strong>is attending Cheers The Happy Hour...</strong></span> Thurs. Feb 18, 2011 at 5:30PM </div>
            </div>
          </div>
          <div class="as_bottom_box" style="float:right; margin-right:20px">
            <div class="as_bottom_boxTitle">NBA ALLSTARS UPDATES</div>
            <div class="as_bottom_boxDetail">
              <div class="as_box_enrty2"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_smal_logo.png" align="left"> The NBA has teamed up with the Central Florida Sports Commission </div>
              <div class="as_box_enrty2"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_smal_logo.png" align="left"> The NBA has teamed up with the Central Florida Sports Commission </div>
              <div class="as_box_enrty2"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_smal_logo.png" align="left"> The NBA has teamed up with the Central Florida Sports Commission </div>
              <div class="as_box_enrty2"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_smal_logo.png" align="left"> The NBA has teamed up with the Central Florida Sports Commission </div>
              <div class="as_box_enrty2"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_smal_logo.png" align="left"> The NBA has teamed up with the Central Florida Sports Commission </div>
              <div class="as_box_enrty2"> <img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_smal_logo.png" align="left"> The NBA has teamed up with the Central Florida Sports Commission </div>
            </div>
          </div>
          <div class="clr"></div>
        </div>
        <!-- end as_left_area -->
        <div class="as_right_sidebar">
          <div class="as_box">
            <div class="as_title"><span>SUBSCRIBE</span> TO NBA ALLSTAR UPDATES</div>
            <div class="as_search">
              <input type="text" name="srachKeyWord" value="Email Address" onFocus="if(this.value=='Email Address'){this.value='';}" onBlur="if(this.value==''){this.value='Email Address';}" class="input">
              <input type="image" src="<?php echo ABSOLUTE_PATH; ?>allstar/images/as_searchSubmit.png" value="Submit" name="search">
              <input type="hidden" value="Submit" name="search" >
              <br>
              &nbsp; </div>
          </div>
          <div class="as_box">
            <div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
			<div class="fb-like-box" data-border-width="0" data-href="https://www.facebook.com/eventgrabber" data-width="203" data-show-faces="true" data-stream="false" data-header="false"></div>
            <br>
			<!--<img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/fb_demo.png" width="203">-->
			 </div>
          <div class="as_box">
            <div class="as_title"><span>EVENTGRABBER</span> TWEETS</div>
            <br>
			<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
	version: 2,
	type: 'profile',
	rpp: 4,
	interval: 30000,
	width: 200,
	height: 300,
	theme: {
	shell: {
//	  background: '#333333',
//	  color: '#ffffff'
	},
	tweets: {
//	  background: '#000000',
//	  color: '#ffffff',
//	  links: '#4aed05'
	}
	},
	features: {
	scrollbar: false,
	loop: false,
	live: false,
	behavior: 'all'
	}
	}).render().setUser('eventgrabber').start();
</script>
			
            <!--<img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/tweet_demo.png" width="200">-->
            <div class="as_follow_tweet"><a href="#">Follow @eventgrabber on Twitter</a></div>
          </div>
          <div class="as_box">
            <div class="as_title"><span>SHARE</span> THE NBA ALLSTAR GUIDE</div>
            <br>
            <div style="text-align:center; padding-bottom:8px;">
			<span  class='st_twitter_large' ></span>
			<span  class='st_facebook_large' ></span>
			<span  class='st_email_large' ></span>
			<span  class='st_yahoo_large' ></span>
			<span  class='st_tumblr_large' ></span>
			<span  class='st_sharethis_large' ></span>
			<script type="text/javascript">var switchTo5x=true;</script><script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:'b11acf0a-9f39-486b-831c-664dfddd3a78'});</script>
			<!--<img src="<?php echo ABSOLUTE_PATH; ?>allstar/images/share_demo.png" width="200">-->
			</div>
          </div>
        </div>
        <div class="clr"></div>
      </div>
      <!-- end as_inner -->
    </div>
    <!--end as_contentWrapper-->
    <div class="clr"></div>
    <div class="as_footer"> &copy; 2012 eventgrabber.com </div>
  </div>
  <!--end as_main2Wrapper-->
</div>
<!--end as_mainWrapper-->
<input type="hidden" value="<?php echo ABSOLUTE_PATH; ?>" name="absolute" id="absolute" />
</body>
</html>
<style>
.stButton{
	margin:0;
	padding:0;
}
.twtr-hd{
	display:none;
	}
#twtr-widget-1 .twtr-doc, #twtr-widget-1 .twtr-hd a, #twtr-widget-1 h3, #twtr-widget-1 h4 {
	background:none;
	}
</style>