<?php
	if($extra==''){
		$extra		=	$_POST['extra'];
	}

	$assessment_url	= getSingleColumn("assessment_url","select * from `events` where `id`='". $event_id ."'");
?>
<div class="nav_new">
  <ul>
    <li <?php if ($active=='details' || $active=='my_eventwall' || $active==''){ echo 'class="active"'; } ?> id="first3"> <a <?php if ($active=='details' || $active=='my_eventwall' || $active==''){ echo 'class="active"'; } ?> href="javascript:void(0)" onClick="getPages('/','loaddetailsFB.php','flayer','<?php echo  $event_id; ?>','<?php echo $extra; ?>');">Bio</a> </li>
    <li <?php if ($active=='photovideo'){ echo 'class="active"'; } ?>><a  href="javascript:void(0)" onClick="getPages('/','loadphotovideoFB.php','flayer','<?php echo  $event_id; ?>','<?php echo $extra; ?>');" >Video / Picture</a></li>
    <li <?php if ($active=='location'){ echo 'class="active"'; } ?>><a  href="javascript:void(0)" onClick="getPages('/','loadlocationFB.php','flayer','<?php echo  $event_id; ?>','<?php echo $extra; ?>');" >Location</a></li>
	<li <?php if ($active=='assessment'){ echo 'class="active"'; } ?> id="last3"><a  href="javascript:void(0)" onClick="getPages('/','loadassessmentFB.php','flayer','<?php echo  $event_id; ?>','<?php echo $extra; ?>');"  >Assessment</a></li>
  </ul>
</div>
<div class="clr" style="height:28px">&nbsp;</div>