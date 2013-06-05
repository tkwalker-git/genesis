<?php require_once('admin/database.php');
		
$active		=	"my_network";
$meta_title	=	"My Network";
require_once('includes/header.php');


if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='login.php';</script>";
?>
<script src="<?= ABSOLUTE_PATH; ?>js/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= ABSOLUTE_PATH; ?>js/jquery-ui-full-1.5.2.min.js" type="text/javascript" charset="utf-8"></script>
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
            });
			}
			else{
			$('.slider').css('display','none');
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
      <?php include("eventwallNav.php");?>
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
              <div class="sliderGallery"> <?php
			  echo getFriends($user_id);
			  ?>
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
	<a href="" id="group"></a>
    <div id="groupRecord">
      <?php
	  if($_GET['groupid']){
	  	$bc_group_id	=	$_GET['groupid'];
		$res = mysql_query("select * from `hangout_group` where `member_id`='$user_id' && `id`='$bc_group_id'");
		}
	else{
		$res = mysql_query("select * from `hangout_group` where `member_id`='$user_id' order by `id` ASC LIMIT 0,1");
		}
	while($row = mysql_fetch_array($res)){
	$bc_group_id	=	$row['id'];
	$bc_group_name	=	$row['name'];
	}
	?>
      <div class="wallTitle"><?php echo $bc_group_name; ?></div>
      <div class="flayerCenter" style="float:left; width:auto">
        <div class="menu" style="width:542px">
          <ul>
            <li class="firstOver" id="first"> <a class="flayerMenuActive" href="#">Group Members</a> </li>
            <li><a href="#">Suggest Event</a></li>
            <li><a href="#">Suggest Deal</a></li>
            <li class="last" id="last2"><a onMouseOver="document.getElementById('last2').className='lastOver';" onMouseOut="document.getElementById('last2').className='last';" href="#">Needs Your Response <span class="color2">(2)</span></a></li>
          </ul>
        </div>
        <div class="clr" style="height:14px">&nbsp;</div>
      </div>
      <div class="clr"></div>
      <div class="frndBoxTop">
        <div class="frndBoxBottom">
          <div class="frndBoxMiddle">
            <div class="groupMembers" id="groupMembers">
              <div><b>Click Member To See Profile</b></div>
              <div class="members" id="members">
                <?php
				getGroupMembers($bc_group_id); ?>
              </div>
            </div>
            <div class="memberProfile"> <img src="images/leftArrow.png" style="left: 23px;position: absolute;top: 41px;">
              <div class="inr" id="inr">
                <?php
			  $bc_first_member_id = attribValue("group_members","member_id","where group_id='".$bc_group_id . "' ORDER BY `id` ASC LIMIT 0,1");
			  getGroupMemberProfile($bc_first_member_id);
			  ?>
              </div>
              <?php
			  $rt = mysql_query("select * from `hangout_group` where `member_id`='$user_id'");
			  if(mysql_num_rows($rt)>1){?>
              <div align="right"><br>
                <img src="<?= IMAGE_PATH; ?>prevdisable.png"> &nbsp; &nbsp; <a href="javascript:void(0)"
				onclick="loadGroups('<?php echo ABSOLUTE_PATH;?>','next',1)"><img src="<?= IMAGE_PATH; ?>nxt.png"></a>
                <?php } ?>
              </div>
            </div>
            <div class="clr"></div>
          </div>
        </div>
      </div>
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
      <a href="javascript:void(0)" onclick="windowOpener(525,625,'Terms and Conditions','cimport/invite_friends.php')" style="display:block; clear:both;float:none; "><img src="<?php echo IMAGE_PATH; ?>invite_my_friends.gif" alt="" title="" align="left" /></a> <a href="create_hangout_group.php"><img src="<?= IMAGE_PATH; ?>create_hangout_group.gif" alt="" title="" align="left" style="padding-left:32px;" /></a>
      <div class="clr"></div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');
if($_GET['groupid']){
 	echo "<script>getHangoutGroups('".ABSOLUTE_PATH."')</script>";
	} 
?>