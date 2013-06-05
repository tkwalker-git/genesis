<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	?>

<div class="flayerCenter" style="float:left; width:auto">
  <div class="menu">
    <ul>
      <li class="first" id="first4"> <a class="" onMouseOver="document.getElementById('first4').className='firstOver';" onMouseOut="document.getElementById('first4').className='first';"  href="javascript:void(0)" onclick="loadPurchased('<?php echo ABSOLUTE_PATH; ?>');" style="padding:0 40px;">Purchased</a> </li>
      <li class="lastOver" id="last4"><a class="flayerMenuActive" href="javascript:void(0)" onclick="findDeals('<?php echo ABSOLUTE_PATH; ?>');" style="padding:0 40px;">Find Deals</a></li>
    </ul>
  </div>
  <div class="clr" style="height:14px">&nbsp;</div>
</div>
<div class="clr"></div>
<div class="frndBoxTop">
  <div class="frndBoxBottom">
    <div class="frndBoxMiddle" style="min-height:246px;"> <span id="purchasedpeals">
      <div style="width:38%; margin:auto"> <span class="ev_fltlft" style="padding-right:10px">
        <input type="text" class="new_input" name="dealtext" id="dealtext" style="width:250px">
        </span> <span class="ev_fltlft"><img src="<?php echo IMAGE_PATH; ?>find.png" onClick="searchDeals('<?php echo ABSOLUTE_PATH; ?>',$('#dealtext').val());" style="cursor:pointer"></span> </div>
      </span>
	  <div class="clr"></div>
	  <span id="showSearchDealsResult" style="display:block;padding-top:20px"></span>
	  
	  </span>
  </div>
</div>