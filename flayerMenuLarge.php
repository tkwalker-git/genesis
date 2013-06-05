<div class="menu" style="width:380px!important">
  <ul>
    <li <?php if ($active=='details' || $active=='my_eventwall' || $active==''){ echo 'class="firstOver"'; }else{?>
	class="first" onMouseOver="document.getElementById('first3').className='firstOver';" onMouseOut="document.getElementById('first3').className='first';"
	<?php
	}?> id="first3"> <a <?php if ($active=='details' || $active=='my_eventwall' || $active==''){ echo 'class="flayerMenuActive"'; } ?> href="javascript:void(0)" onClick="getPages('<?php echo  ABSOLUTE_PATH; ?>','loaddetailsLarg.php','flayer','<?php echo  $event_id; ?>');">Details</a> </li>
 
   <li><a  href="javascript:void(0)" onClick="getPages('<?php echo  ABSOLUTE_PATH; ?>','loadphotovideoFB.php','flayer','<?php echo  $event_id; ?>');" <?php if ($active=='photovideo'){ echo 'class="flayerMenuActive"'; } ?>>Photos/Videos</a></li>
	
    <li><a  href="javascript:void(0)" onClick="getPages('<?php echo  ABSOLUTE_PATH; ?>','loadlocationFB.php','flayer','<?php echo  $event_id; ?>');" <?php if ($active=='location'){ echo 'class="flayerMenuActive"'; } ?> >Location</a></li>
 
  <!--<li <?php if ($active=='buy'){ echo 'class="lastOver"'; }else{ ?> onMouseOver="document.getElementById('last3').className='lastOver';" onMouseOut="document.getElementById('last3').className='last';" class="last" <?php } ?> id="last3"><a <?php if ($active=='buy'){ echo 'class="flayerMenuActive"'; } ?> href="javascript:void(0)"
	onClick="getPages('<?php echo ABSOLUTE_PATH; ?>','loadbuyFB.php','flayer','<?php echo $event_id; ?>');">Tickets</a></li>-->
	
	<li <?php if ($active=='specials'){ echo 'class="lastOver"'; }else{ ?> onMouseOver="document.getElementById('last3').className='lastOver';" onMouseOut="document.getElementById('last3').className='last';" class="last" <?php } ?> id="last3"><a <?php if ($active=='specials'){ echo 'class="flayerMenuActive"'; } ?> href="javascript:void(0)"
	onClick="getPages('<?php echo ABSOLUTE_PATH; ?>','loadspecialsFB.php','flayer','<?php echo $event_id; ?>');">Specials</a></li>
	
  </ul>
</div>
<div class="clr" style="height:28px">&nbsp;</div>