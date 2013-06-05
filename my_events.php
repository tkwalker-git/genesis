<?php require_once('admin/database.php'); ?>
<script type="text/javascript" charset="utf-8">
		   window.onload = function () {
            var container = $('div.sliderGallery2');
            var ul = $('ul', container);
            
            var itemsWidth = ul.innerWidth() - container.outerWidth();
            
            $('.slider2', container).slider({
                min: 0,
                max: itemsWidth,
                handle: '.handle2',
                stop: function (event, ui) {
                    ul.animate({'left' : ui.value * -1}, 500);
                },
                slide: function (event, ui) {
                    ul.css('left', ui.value * -1);
                }
            });
        };
    </script>
<?php
		require_once('includes/header.php');
		if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
			echo "<script>window.location.href='login.php';</script>";

?>

<div class="topContainer">
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="marketPlaceTop" style="padding-bottom: 18px;">
      <?php include("eventwallNav.php"); ?>
      <div class="clear"></div>
    </div>
    <div class="wallTitle">My Events</div>
    <div class="flayerCenter" style="float:left; width:auto">
      <div class="menu">
        <ul>
          <li class="firstOver" id="first"> <a class="flayerMenuActive" href="#">Today</a> </li>
          <li><a href="#">This Week</a></li>
          <li><a href="#">This Weekend</a></li>
          <li class="last" id="last2"><a onMouseOver="document.getElementById('last2').className='lastOver';" onMouseOut="document.getElementById('last2').className='last';" href="#">All</a></li>
        </ul>
      </div>
      <div class="clr" style="height:14px">&nbsp;</div>
    </div>
    <div class="clr"></div>
    <div class="frndBoxTop">
      <div class="frndBoxBottom">
        <div class="frndBoxMiddle">
          <div class="sliderGallery2">
            <ul>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
              <li> <a href="#"><img src="<?= EVENT_IMAGE_PATH; ?>flayerThumb.gif" /></a><img src="event_images/flayerThumbShad.png" style="border:0; margin-top:1px;display:block" /></li>
            </ul>
            <div class="slider2">
              <div class="handle2"></div>
            </div>
          </div>
          <div class="clr"></div>
        </div>
      </div>
    </div>
    <div class="clr"></div>
    <div class="ev_fltlft"><img src="<?= IMAGE_PATH; ?>digital_flayer.png" alt="" title="" /></div>
    <div class="ev_fltlft" style="padding-left:16px;">
      <?php include("flayer.php"); ?>
    </div>
    <div class="clr" style="height:38px">&nbsp;</div>
    <div class="wallTitle">My Deals</div>
    <div class="flayerCenter" style="float:left; width:auto">
      <div class="menu">
        <ul>
          <li class="firstOver" id="first4"> <a class="flayerMenuActive" href="#" style="padding:0 40px;">Purchased</a> </li>
          <li class="last" id="last4"><a onMouseOver="document.getElementById('last4').className='lastOver';" onMouseOut="document.getElementById('last4').className='last';" href="#" style="padding:0 40px;">Browse Marketplace</a></li>
        </ul>
      </div>
      <div class="clr" style="height:14px">&nbsp;</div>
    </div>
    <div class="clr"></div>
    <div class="frndBoxTop">
      <div class="frndBoxBottom">
        <div class="frndBoxMiddle">
          <div class="bx"><img src="<?= IMAGE_PATH; ?>demo_deal1.gif" alt="" title="" align="left" /> <span class="dealTitle"><br />
            Deal Title</span><br />
            <br />
            <span class="dealDesc">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</span>
            <div class="clr"></div>
          </div>
          <div class="bx"><img src="<?= IMAGE_PATH; ?>demo_deal2.gif" alt="" title="" align="left" /> <span class="dealTitle"><br />
            Deal Title</span><br />
            <br />
            <span class="dealDesc">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</span>
            <div class="clr"></div>
          </div>
          <div class="clr"></div>
              </div>
      </div>
    </div>
    <div class="wallTitle" style="float:none; width:auto">Event Manager<br />
      <br />
      <a href="#"><img src="<?= IMAGE_PATH; ?>import_facebook.gif" alt="" title="" align="left" /></a>
	  <a href="#"><img src="<?= IMAGE_PATH; ?>add_events.gif" alt="" title="" align="left" style="padding-left:25px;" /></a>
	  <a href="#"><img src="<?= IMAGE_PATH; ?>manage_events.gif" alt="" title="" align="left" style="padding-left:25px;" /></a>
	  <a href="#"><img src="<?= IMAGE_PATH; ?>reviews_&_ratings.gif" alt="" title="" align="left" style="padding-left:25px;" /></a>
      <div class="clr"></div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>
<script src="<?= ABSOLUTE_PATH ;?>js/jquery-1.2.6.js" type="text/javascript"></script>
<script src="<?= ABSOLUTE_PATH ;?>js/jquery-ui-full-1.5.2.min.js" type="text/javascript"></script>
