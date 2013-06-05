<?php
	
	require_once('admin/database.php');
	require_once('site_functions.php');
	
	?>
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