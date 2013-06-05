<?php
$preview =1;
?><div class="nav_new">
  <ul>
    <li <?php if ($active=='details' || $active=='my_eventwall' || $active==''){ echo 'class="active"'; } ?> id="first3"> <a <?php if ($active=='details' || $active=='my_eventwall' || $active==''){ echo 'class="active"'; } ?> href="javascript:void(0)" onClick="getPages('/','loaddetails.php','flayer','<?php echo  $event_id; ?>','');">Details</a> </li>
 
   <li <?php if ($active=='photovideo'){ echo 'class="active"'; } ?>><a  href="javascript:void(0)" onClick="getPages('/','loadphotovideo.php','flayer','<?php echo  $event_id; ?>','');" >Photos/Videos</a></li>
	
    <li <?php if ($active=='location'){ echo 'class="active"'; } ?>><a  href="javascript:void(0)" onClick="getPages('/','loadlocation.php','flayer','<?php echo  $event_id; ?>','');"  >Location</a></li>
 
	<?php
	if($bc_alter!=0){?>
		<li <?php if ($active=='buy'){ echo 'class="active"'; } ?> id="last3"><a href="<?php echo $bc_alter_url; ?>" target="_top">Buy Ticket</a></li>
	<?php
	}
	else{
	if(validEventTicketSaleTime($event_id)!='no'){?>
	<li <?php if ($active=='buy'){ echo 'class="active"'; } ?> id="last3"><a href="javascript:void(0)"
	onClick="getPages('/','loadbuy.php','flayer','<?php echo $event_id; ?>','');">Buy Ticket</a></li>
	<?php } }
	?>
	
	
  </ul>
</div>
<div class="clr" style="height:28px">&nbsp;</div>