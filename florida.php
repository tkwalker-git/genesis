<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

?>
<style>
body{
		background:url(<?php echo ABSOLUTE_PATH;?>images/florida_classic_bg.jpg);
	}

.sponsor{
		width:490px;
		margin:auto;
	/*	width:auto;
		height:auto;
		margin:4px;
		*/
		/* Giving the sponsor div a relative positioning: */
	/*	position:relative;
		cursor:pointer;*/
}
.sponsorFlip{
		/*  The sponsor div will be positioned absolutely with respect
			to its parent .sponsor div and fill it in entirely */
	
	/*	position:absolute; */
	left: 0;
    min-height: 300px;
    top: 0;
	margin:auto;
    width: 490px!important;
	text-align:center
	/*	height:700px;
			width:100%;
		height:100%; */
}
.sponsorData{
		/* Hiding the .sponsorData div */
		display:none;
}
</style>
<?php

$meta_title 	=	"Florida Classic 2012 - Events";
$meta_kwords 	=	"Florida Classic 2011, Stepshow, Battle of the Bands, FAMU, BCU, Bethune, Rattlers, Florida Citrus Bowl, Alphas, AKA, Deltas, Sigmas, Ques, Iotas, Kappas, Zetas, SGRhos, Roxy, House of Blues, Draft, Draft Global Lounge, Beacham, Vain, Classic Afterparty, Crave, Tavern on the Lake, Frontline Promotions, Kheep Entertainment, Torrence Lifestyles, Jade Events, Eventgrabber, Doug E Fresh, Wildcats, Event Guide, Classic Weekend";

include_once('includes/header.php');

?>
<style>
.header { display:none!important; }
.headerOut { height:40px!important; }
</style>
<link href="<?php echo ABSOLUTE_PATH; ?>florida.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.flip.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/script.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var abs_url = $('#absolute').val();
	$('.voting_menu ul li').click(function(){
		$('.voting_menu ul li').removeClass('active');
		$('.voting_menu ul li img').remove();
		var poll_id	=	$(this).attr('id');
		$(this).addClass('active');
		$('#showvoteresult').html('');
		$(this).append('<img src="<?php echo IMAGE_PATH; ?>arrow1.png" width="24" height="7" border="0" style="left: 42%; position: absolute;top:0;" />');
		$.ajax({  
			type: "GET",  
			url: abs_url+"ajax/loadmatch.php",  
			data: "poll_id=" + poll_id,  
			dataType: "text/html",  
				beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			$("#showmatch").html(html);
			}, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});
		
		
	});
	
	$('ul.featuredEventMenusUl li').click(function(){
		$('ul.featuredEventMenusUl li').removeClass('active');
		$(this).addClass('active');
	});

	$('ul.featuredEventMenusUl li').mouseover(function(){
		$(this).addClass('active2');
	});
	
	$('ul.featuredEventMenusUl li').mouseout(function(){
		$(this).removeClass('active2');
	});
	
	


	$('#clickhere').css('cursor','pointer');
	$('#clickhere').attr('title','Click to flip');
	var span	=	$('span','.voting_person');
	$(span).click(function(){
	var id		=	$(this).attr('id');
	
	var sp = id.split('-');
	
	var team_id		=	sp[0];
	var poll_id		=	sp[1];
	
	$.ajax({  
			type: "GET",  
			url: abs_url+"ajax/vote.php",  
			data: "team_id=" + team_id + "&poll_id=" + poll_id ,  
			dataType: "text/html",  
				beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			$('#showmatch').html(html);
			}, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});

});

	var container	=	$('#margin');
	var box			=	$('.voting_person', container);
	var width		=	box.innerWidth()+20;
	$(container).css('width',width*box.size());


$('#seeResult').click(function(){
	 var container	=	$('.voting_menu');
	 var ul			=	$('ul', container);
	 var active		=	$('.active', ul);
	 var poll_id	=	$(active).attr('id');
	 $.ajax({  
			type: "GET",  
			url: abs_url+"ajax/loadmatch.php?type=showresult",  
			data: "poll_id=" + poll_id,  
			dataType: "text/html",  
				beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			$("#showmatch").html(html);
			}, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});
});
	
});



function view_event_guide(id,abs_url){
	$.ajax({  
			type: "POST",
			url: abs_url+"ajax/load_event_guide.php",  
			data: "id="+id ,  
			dataType: "text/html",  
				beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
				$('#event_guide').html(html);
				$('#view_event_guide').hide();
			}, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});
	}

</script>
<div class="mainWrapper">
  <div class="main2Wrapper" align="center">
    <!--Header will be put here-->
    <div class="contentWrapper" style="text-align:left">
      <div class="poweredBy"> <span class="text">The 2012 Florida Classic Event Guide</span> <span class="text1"><img src="<?php echo IMAGE_PATH; ?>powered_by.png" width="225" height="26" border="0" /></span> </div>
      <!-- end poweredBy-->
      <div class="whitePages">
        <div class="whitePagesHead"> <span class="featureEventText">Featured Events</span>
          <div class="shareArea"> <span>Share The Florida Classic Guide!</span>
            <!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style"> <a class="addthis_button_preferred_1"></a> <a class="addthis_button_preferred_2"></a> <a class="addthis_button_preferred_9"></a> <a class="addthis_button_preferred_15"></a> <a class="addthis_button_preferred_3"></a> <a class="addthis_button_compact"></a>
              <!--<a class="addthis_counter addthis_bubble_style"></a>-->
            </div>
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4eb12a3d654aa28d"></script>
            <!-- AddThis Button END -->
            <!-- <img src="<?php echo IMAGE_PATH; ?>shareicons.png" width="245" height="36" border="0" />  -->
          </div>
          <!--end shareArea-->
        </div>
        <!--end whitePagesHead-->
        <div class="featuredEventContents">
          <div class="featuredEventMenus">
            <ul class="featuredEventMenusUl">
              <?php
			  $specials_id = $_GET['id'];
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
				
					$specials_array = getSpecialsEvents($events_id,'flyer');  // flyer or simple
					if($specials_array['id']!=''){
					$z++;
					if($z==1){
						$event_id		=	$specials_array['id'];
						$first_special_event_image	=	$specials_array['event_image'];
						}
					?>
              <li <?php if ($z==1){ echo 'class="active"'; } ?> onClick="getFlyer1('<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $specials_array['id']; ?>');"> <span class="femheading">
                <?php
			 
			  if(strlen($specials_array['event_name']) > 27)
				  echo substr($specials_array['event_name'],0,27)."...";
			  else
				  echo $specials_array['event_name'];
				  
			  ?>
                </span> <span class="femdate">
                <?php
				$event_dateT = getEventStartDateFB($specials_array['id']);
				echo date('D, F d, Y', strtotime($event_dateT[0]));
				?>
                at
                <?php
				$event_time = getEventTime($event_dateT[1]);
				echo date('h:i A', strtotime($event_time['start_time']));
				?>
                </span>
				</li>
              <?php } }?>
            </ul>
          </div>
          <!--end featuredEventMenus-->
          <div class="eventDisplayMonitor">
            <div class="monitor_top"></div>
            <!--end monitor_top-->
            <script>
			function getFlyer1(url){
			$("#flyFrame").attr('src', url);
			}
			</script>
            <div class="monitor_center">
              <div id="flayermain" >
                <div style="position:relative;">
                  <div class="sponsor" id="spons">
                    <div class="sponsorFlip">
                      <iframe id="flyFrame" src="<?php echo ABSOLUTE_PATH; ?>fbflayer/index.php?id=<?php echo $event_id;?>" width="520" height="800" scrolling="auto" frameborder="0" style="border:none"></iframe>
                    </div>
                    <div class="sponsorData">
                      <?php
					 	include("sp_details.php");
					  ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--end monitor_center-->
            <div class="monitor_bottom"></div>
            <!--end monitor_bottom-->
          </div>
          <!--end eventDisplayMonitor-->
          <div class="clr"></div>
        </div>
        <!--end featuredEventContents-->
        <!-- Sponsor Start -->
        <!-- <iframe id="flyFrame" src="<?php echo ABSOLUTE_PATH; ?>sponsor.php" scrolling="no"  height="250px" width="960px" frameborder="0" style="border:none"></iframe> -->
		
		<script type="text/javascript" src="http://promoshq.wildfireapp.com/website/302/companies/219717/widget_loader.js"></script>
		<!--<iframe frameborder="0" height="700" width="900" src="http://promoshq.wildfireapp.com/website/6/contests/170274"> </iframe>-->
        <!-- Sponsor End -->
        <!--end sponsorsContents-->
        <div class="eventGuideContents">
          <div class="eventGuideLeft"> <span><img src="<?php echo IMAGE_PATH; ?>event_guide_heading.png" width="298" height="57" border="0" /></span> <span id="event_guide">
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
				$limit = 6;
					if($specials_array['id']!=''){
						$n++;
						if( $n <= $limit ){
						$eventUrl = getEventURL($specials_array['id'])
		?>
              <li> <span class="eventGuideLeftHeading"><a href="<?php echo $eventUrl; ?>"><?php echo $specials_array['event_name']; ?></a></span> <span class="eventGuideLeftDate">
                <?php
			$event_dateT = getEventStartDateFB($specials_array['id']);
			echo date('D, F d, Y', strtotime($event_dateT[0]));
		?>
                at
                <?php
			$event_time = getEventTime($event_dateT[1]);
			echo date('h:i A', strtotime($event_time['start_time']));
		?>
                </span> </li>
              <?php }
			  if($n > $limit){
			  $showButton='yes';
			  }
			  
			   }}?>
            </ul>
            </span>
            <?php
			if($showButton=='yes'){?>
            <img src="<?php echo IMAGE_PATH; ?>viewall_btn.png" onclick="view_event_guide(1,'<?php echo ABSOLUTE_PATH; ?>')" style="cursor:pointer" width="110" height="42" border="0" />
            <?php } ?>
          </div>
          <!--end eventGuideLeft-->
          <div class="eventGuideRight">
            <div class="title">Who will win the battle?</div>
            <div id="showvoteresult" style="text-align:center"></div>
            <div class="votingArea">
              <div class="voting_contents" id="showmatch">
                <?php
			   
			   	$firstPollId	=	getSingleColumn('id',"select * from `polls` ORDER BY `id` ASC LIMIT 0, 1");
				
				echo getPollTeams($firstPollId,'');
				
				?>
              </div>
              <!-- end voting_contents -->
              <div class="voting_menu">
                <ul>
                  <?php
				$res = mysql_query("select * from `polls` ORDER BY `id` ASC");
				$k=0;
				while($row = mysql_fetch_array($res)){
				$k++;
				?>
                  <li id='<?php echo $row['id'];?>' <?php if ($k==1){echo 'class="active"'; } ?>><?php echo $row['name'];  if ($k==1){?><img src="<?php echo IMAGE_PATH; ?>arrow1.png" width="24" height="7" border="0" />
                    <?php } ?>
                  </li>
                  <?php } ?>
                  <!--<li>Battle of the Band</li>
                  <li>Stepshow - Girls</li>
                  <li>Stepshow - Boys</li>
				  <li>Nothing like...</li>-->
                </ul>
              </div>
              <!--end voting_menu-->
            </div>
            <!--end votingArea-->
            <div class="voting_option_btns"> <a href="javascript:void(0)" onclick="windowOpener(525,625,'Terms and Conditions','<?php echo ABSOLUTE_PATH; ?>cimport/invite_friends.php')" style="display:block; clear:both;float:none; "> <img src="<?php echo IMAGE_PATH; ?>friend_invit_vote_btn.png" width="193" height="42" border="0" align="left" /> </a> <img src="<?php echo IMAGE_PATH; ?>seeallresult_btn.png" id="seeResult" style="cursor:pointer" width="146" height="42" border="0" align="right" />
              <div class="clr"></div>
            </div>
            <!--end voting_option_btns-->
          </div>
          <!--end eventGuideRight-->
        </div>
        <!--end eventGuideContents-->
        <div class="clr"></div>
      </div>
      <!--end whitePages-->
      <div class="guideFooter">&copy;&nbsp;2011&nbsp;eventgrabber.com</div>
    </div>
    <!--end contentWrapper-->
  </div>
  <!--end main2Wrapper-->
</div>
<input type="hidden" value="<?php echo ABSOLUTE_PATH; ?>" name="absolute" id="absolute" />
<!--end mainWrapper-->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-9828882-1']);
_gaq.push(['_setDomainName', '.eventgrabber.com']);
_gaq.push(['_trackPageview']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>
</body></html>