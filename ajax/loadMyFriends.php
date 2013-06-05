<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
?>
<script type="text/javascript" charset="utf-8">

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
			

    </script>
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
      <div class="sliderGallery"> <?php echo getFriends($user_id); ?>
        <div class="slider">
          <div class="handle"></div>
        </div>
      </div>
	  </div>
      <div class="clr"></div>
    </div>
  </div>
</div>
