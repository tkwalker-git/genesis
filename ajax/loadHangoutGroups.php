<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
?>
<script src="<?= ABSOLUTE_PATH ;?>js/jquery-1.2.6.js" type="text/javascript"></script>
<script src="<?= ABSOLUTE_PATH ;?>js/jquery-ui-full-1.5.2.min.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">

			 var container			=	$('div.sliderGallery');
			 var ul					=	$('ul', container);
			 var li					=	$('li', ul);
			 var list_inner			=	$('div.list_inner', li);
			 var list_inner_size	=	list_inner.size();
			 if(list_inner_size	>	4){
			 var productWidth		=	li.innerWidth()+47;
			$(ul).css('width',list_inner_size*productWidth-47);
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

    </script>
<style type="text/css">
.sliderGallery,.sliderGallery2 {
	height: 270px;
    margin-left: 12px;
    overflow: hidden;
    position: absolute;
    width: 940px;
	}
			
.sliderGallery ul,.sliderGallery2 ul {
	height: 250px;
    margin: 0 0 0 38px;
    overflow: hidden;
    position: absolute;
	padding:0;
    width: 940px;
	}
        
.sliderGallery ul li,.sliderGallery2 ul li{
	float:left;
	text-align:center;
	overflow:hidden;
	display:inline;
	list-style:none;
	margin-left:36px; 
	}
		
.slider,.slider2 {
	background: url(images/productb.png) no-repeat scroll center bottom transparent;
	height: 16px;
	margin-left: 5px;
	padding: 1px;
	position: relative;
	right: 4px;
	top: 250px;
	}
        
.handle,.handle2 {
	background: url(images/productc.png) no-repeat scroll 0 0 transparent;
    cursor: move;
    height: 18px;
    position: absolute;
    top: 0;
    width: 75px;
    z-index: 100;
	}
	
.list_inner,list_inner2{
	background-image: url("images/img_back.png");
    background-repeat: no-repeat;
    height: 225px;
	padding:8px;
    width: 178px;
	}

</style>
<div class="wallTitle">Hangout Groups</div>
<div class="flayerCenter" style="float:left; width:auto">
  <div class="menu">
    <ul>
      <li class="first" id="first"> <a onMouseOver="document.getElementById('first').className='firstOver';" onMouseOut="document.getElementById('first').className='first';" class="" href="javascript:void(0)" onclick="getMyFriends('<?php echo ABSOLUTE_PATH; ?>');">My Friends</a> </li>
      <li ><a href="javascript:void(0)" onclick="getHangoutGroups('<?php echo ABSOLUTE_PATH; ?>');" class="flayerMenuActive">Hangout Groups</a></li>
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
        <?php echo getGroups($user_id,''); ?>
        <div class="slider">
          <div class="handle"></div>
        </div>
      </div>
      <div class="clr"></div>
    </div>
  </div>
  </div>
</div>