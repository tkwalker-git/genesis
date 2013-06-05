<?php require_once('admin/database.php');
$active	=	'my_eventwall';
$meta_title	=	"My EvetWall";
		require_once('includes/header.php');
		if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
			echo "<script>window.location.href='login.php';</script>";

?>
<script src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui-full-1.5.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
		
	$(document).ready(function () {
			 var container			=	$('div.sliderGallery');
			 var ul					=	$('ul', container);
			 var li					=	$('li', ul);
			 var list_inner			=	$('div.list_inner', li);
			 var list_inner_size	=	list_inner.size();
			 	if(list_inner_size > 4){
			 var productWidth		=	li.innerWidth()+53;
			$(ul).css('width',list_inner_size*productWidth-53);
		/*	var showResord	=	ul.innerWidth()/container.innerWidth()+1; */
			 var showResord	=	5;
			var itemsWidth = ul.innerWidth()-(li.innerWidth()*showResord);
            $('.slider', container).slider({
                min: 0,
                max: itemsWidth,
                handle: '.handle',
                stop: function (event, ui) {
                    ul.animate({'left' : ui.value * -1}, 500);
                },
                slide: function (event, ui) {
					$('#show').val(ui.value);
                    ul.css('left', ui.value * -1);
                }
            });	}
			else{
			$('.slider').css('display','none');
			}
        });

	$(document).ready(function () {
			 var container			=	$('div.sliderGallery2');
			 var ul					=	$('ul', container);
			 var li					=	$('li', ul);
			 var list_inner			=	$('div.list_inner', li);
			 var list_inner_size	=	list_inner.size();
			if(list_inner_size > 4){
			 var productWidth		=	li.innerWidth()+47;
			$(ul).css('width',list_inner_size*productWidth-47);
		/*	var showResord	=	ul.innerWidth()/container.innerWidth()+1; */
			 var showResord	=	5;
			var itemsWidth = ul.innerWidth()-(li.innerWidth()*showResord);
            $('.slider2', container).slider({
                min: 0,
                max: itemsWidth,
                handle: '.handle2',
                stop: function (event, ui) {
                    ul.animate({'left' : ui.value * -1}, 500);
                },
                slide: function (event, ui) {
					$('#show').val(ui.value);
                    ul.css('left', ui.value * -1);
                }
            });	
			}
			else{
			$('#slider2').css('display','none');
			}
        });
		
		
	
		
</script>

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
    <div id="record1">
      <div class="wallTitle">My Friends</div>
      <div class="flayerCenter" style="float:left; width:auto">
        <div class="menu">
          <ul>
            <li class="firstOver" id="first"> <a class="flayerMenuActive" href="javascript:void(0)" onclick="getMyFriends('<?php echo ABSOLUTE_PATH; ?>');">My Friends</a> </li>
            <li ><a href="javascript:void(0)" onclick="getHangoutGroups('<?php echo ABSOLUTE_PATH; ?>');">Hangout Groups</a></li>
            <li class="last" id="last"><a onMouseOver="document.getElementById('last').className='lastOver';" onMouseOut="document.getElementById('last').className='last';" href="#">Search Friends</a></li>
          </ul>
        </div>
        <div class="clr" style="height:14px">&nbsp;</div>
      </div>
      <div class="clr"></div>
      <div class="frndBoxTop">
        <div class="frndBoxBottom">
          <div class="frndBoxMiddle">
            <div class="sliderGalleryContainer">
              <div class="sliderGallery">
                <?php echo getFriends($user_id); ?>
                <div class="slider">
                  <div class="handle"></div>
                </div>
              </div>
            </div>
            <div class="clr"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="clr"></div>
    <div class="wallTitle">
      <?php echo $logged_in_member_name; ?>
      's Eventwall</div>
    <div id="eventwall">
      <?php  echo getMyEventwall('all'); ?>
    </div>
    <div class="clr"></div>
    <div class="ev_fltlft"><img src="<?php echo IMAGE_PATH; ?>digital_flayer.png" alt="" title="" /></div>
    <div class="ev_fltlft" style="padding-left:16px;" id="flayermain">
      <?php
	  $flyers	=	getSingleColumn('id',"select * from `events` where `event_status`='1' AND `event_type`='1' AND id IN (select `event_id` from `event_wall` where `userid`='$user_id' )");
	  if($flyers){
	  include("flayer.php");
	  }else{
	  echo '<span style="padding:17px 0 25px 15px; display:block; font-size:14px"><b>You didn\'t have any flyer in your Eventwall.</b></span>';
	  }?>
    </div>
    <div class="clr" style="height:38px">&nbsp;</div>
	<div class="wallTitle"><?php echo $logged_in_member_name; ?>
      's Deals</div>
	  <span id="dealsarea">	  
    <div class="flayerCenter" style="float:left; width:auto">
      <div class="menu">
        <ul>
          <li class="firstOver" id="first4"> <a class="flayerMenuActive" href="javascript:void(0)" onclick="loadPurchased('<?php echo ABSOLUTE_PATH; ?>');" style="padding:0 40px;">Purchased</a> </li>
          <li class="last" id="last4"><a onMouseOver="document.getElementById('last4').className='lastOver';" onMouseOut="document.getElementById('last4').className='last';" href="javascript:void(0)" onclick="findDeals('<?php echo ABSOLUTE_PATH; ?>');" style="padding:0 40px;">Find Deals</a></li>
        </ul>
      </div>
      <div class="clr" style="height:14px">&nbsp;</div>
    </div>
    <div class="clr"></div>
    <div class="frndBoxTop">
      <div class="frndBoxBottom">
        <div class="frndBoxMiddle" style="min-height:246px;">
		<span id="purchasedpeals">
		<?php echo getPurchased($user_id); ?>
		  </span>
        </div>
      </div>
    </div>
	</span>
	<div class="clr"></div>
    <div class="wallTitle" style="float:none; width:auto">Network Manager<br />
      <br />
      <a href="javascript:void(0)" onclick="windowOpener(525,625,'Terms and Conditions','cimport/invite_friends.php')" style="display:block; clear:both;float:none; "><img src="<?php echo IMAGE_PATH; ?>invite_my_friends.gif" alt="" title="" align="left" /></a> <a href="create_hangout_group.php"><img src="<?php echo IMAGE_PATH; ?>create_hangout_group.gif" alt="" title="" align="left" style="padding-left:32px;" /></a>
      <div class="clr"></div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>