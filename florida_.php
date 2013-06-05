<?php
include_once('admin/database.php'); 
include_once('site_functions.php');
?>
<style>
body{
	background:#e8e8e8;
	}

.sponsor{
		width:606px;
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
    width: 100%;
	text-align:left
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
include_once('includes/header.php');
?>

<style>
.header{ display:none!important;}
.headerOut { height:40px!important}
</style>

<script src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui-full-1.5.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?= ABSOLUTE_PATH; ?>js/jquery.flip.min.js"></script>
<script type="text/javascript" src="<?= ABSOLUTE_PATH; ?>js/script.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.voting_menu ul li').click(function(){
		$('.voting_menu ul li').removeClass('active');
		$('.voting_menu ul li img').remove();
		$(this).addClass('active');
		$(this).append('<img src="<?php echo IMAGE_PATH; ?>arrow1.png" width="24" height="7" border="0" />');
	});
	
	$('ul.featuredEventMenusUl li').click(function(){
		$('ul.featuredEventMenusUl li').removeClass('active');
		$(this).addClass('active');
	});
	
});


$(document).ready(function(){
$('#clickhere').css('cursor','pointer');
});

</script>
<div class="mainWrapper">
  <div class="main2Wrapper">
    <!--Header will be put here-->
    <div class="contentWrapper">
      <div class="poweredBy"> <span class="text">The 2011 Florida Classic Event Guide</span> <span class="text1"><img src="<?php echo IMAGE_PATH; ?>powered_by.png" width="225" height="26" border="0" /></span> </div>
      <!-- end poweredBy-->
      <div class="whitePages">
        <div class="whitePagesHead"> <span class="featureEventText">Featured Events</span>
          <div class="shareArea"> <span>Share The Florida Classic Guide!</span> <img src="<?php echo IMAGE_PATH; ?>shareicons.png" width="245" height="36" border="0" /> </div>
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
					while($row = mysql_fetch_array($res)){
					
					$specials_array = getSpecialsEvents($row['event_id'],'flyer');  // flyer or simple event
					if($specials_array['id']!=''){
					$z++;
					if($z==1){
						$event_id		=	$specials_array['id'];
						$first_special_event_image	=	$specials_array['event_image'];
						}
					?>
<li <?php if ($z==1){ echo 'class="active"'; } ?> onClick="getFlayer('<?php echo ABSOLUTE_PATH; ?>','<?php echo $specials_array['id']; ?>','annual');"> <span class="femheading"><?php echo $specials_array['event_name']; ?></span> <span class="femdate">
                <?php  $event_dateT = getEventStartDateFB($specials_array['id']);
							echo date('D, F d, Y', strtotime($event_dateT[0]));
							 ?>
                at
                <?php
							 $event_time = getEventTime($event_dateT[1]);
							 echo date('h:i A', $event_time['start_time']);
							 ?>
                </span> </li>
              <?php } }?>
            </ul>
          </div>
          <!--end featuredEventMenus-->
          <div class="eventDisplayMonitor">
            <div class="monitor_top"></div>
            <!--end monitor_top-->
            <div class="monitor_center">
			<div id="flayermain">
			<div style="position:relative;">
                <div class="sponsor" id="spons">
   					 <div class="sponsorFlip">
					 
				 <div id="clickhere">
                  <?php
					echo	getFlayerImage($first_special_event_image,'','','601');
					?>
                  </div>
				  </div>
              
              <div class="sponsorData">
                <?php
					 	include("sp_details.php");
					  ?>
              </div>
			  </div></div></div>
            </div>
            <!--end monitor_center-->
            <div class="monitor_bottom"></div>
            <!--end monitor_bottom-->
          </div>
          <!--end eventDisplayMonitor-->
          <div class="clr"></div>
        </div>
        <!--end featuredEventContents-->
        <div class="sponsorsContents"> <span><img src="<?php echo IMAGE_PATH; ?>sponsors_heading.png" width="298" height="57" border="0" /></span>
          <ul>
            <li><img src="<?php echo IMAGE_PATH; ?>coors.png" width="133" height="95" border="0" /></li>
            <li><img src="<?php echo IMAGE_PATH; ?>mcdonald.png" width="107" height="98" border="0" />"</li>
            <li><img src="<?php echo IMAGE_PATH; ?>florida.png" width="131" height="103" border="0" /></li>
            <li><img src="<?php echo IMAGE_PATH; ?>orange.png" width="92" height="106" border="0" /></li>
            <li><img src="<?php echo IMAGE_PATH; ?>the_home.png" width="114" height="112" border="0" /></li>
            <li><img src="<?php echo IMAGE_PATH; ?>tobacoo.png" width="167" height="78" border="0" /></li>
          </ul>
        </div>
        <!--end sponsorsContents-->
        <div class="eventGuideContents">
          <div class="eventGuideLeft"> <span><img src="<?php echo IMAGE_PATH; ?>event_guide_heading.png" width="298" height="57" border="0" /></span>
            <ul>
			 <?php
					$res = mysql_query("select * from `special_event` where `specials_id`='1'");
					while($row = mysql_fetch_array($res)){
					$specials_array = getSpecialsEvents($row['event_id'],'simple');  // flyer or simple
					if($specials_array['id']!=''){
					$eventUrl = getEventURL($specials_array['id'])
			?>
              <li> <span class="eventGuideLeftHeading"><a href="<?php echo $eventUrl; ?>"><?php echo $specials_array['event_name']; ?></a></span>
			  <span class="eventGuideLeftDate"><?php  $event_dateT = getEventStartDateFB($specials_array['id']);
							echo date('D, F d, Y', strtotime($event_dateT[0]));
							 ?>
                at
                <?php
							 $event_time = getEventTime($event_dateT[1]);
							 echo date('h:i A', $event_time['start_time']);
							 ?></span> </li>
			  <?php } }?>
            </ul>
            <img src="<?php echo IMAGE_PATH; ?>viewall_btn.png" width="110" height="42" border="0" /> </div>
          <!--end eventGuideLeft-->
          <div class="eventGuideRight">
            <div class="votingArea">
              <div class="voting_contents">
                <div class="voting_person">
                  <ul>
                    <li><img src="<?php echo IMAGE_PATH; ?>pic.png" width="183" height="177" border="0" /></li>
                  </ul>
                  <span>Lorem Ipsum</span> </div>
                <!--end voting_person-->
                <div class="voting_person">
                  <ul>
                    <li><img src="<?php echo IMAGE_PATH; ?>pic.png" width="183" height="177" border="0" /></li>
                  </ul>
                  <span>Lorem Ipsum</span> </div>
                <!--end voting_person-->
                <div class="clr"></div>
              </div>
              <!--end voting_contents-->
              <div class="voting_menu">
                <ul>
                  <li class="active">Football Game<img src="<?php echo IMAGE_PATH; ?>arrow1.png" width="24" height="7" border="0" /></li>
                  <li>Battle of the Band</li>
                  <li>Stepshow - Girls</li>
                  <li>Stepshow - Boys</li>
                  <li>Nothing like...</li>
                </ul>
              </div>
              <!--end voting_menu-->
            </div>
            <!--end votingArea-->
            <div class="voting_option_btns"> <img src="<?php echo IMAGE_PATH; ?>friend_invit_vote_btn.png" width="193" height="42" border="0" align="left" /> <img src="<?php echo IMAGE_PATH; ?>seeallresult_btn.png" width="146" height="42" border="0" align="right" />
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
<!--end mainWrapper-->
</body></html>