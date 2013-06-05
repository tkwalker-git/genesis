<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$mainPrice				=	$_GET['mainPrice'];
	$prometer_service_free	=	$_GET['prometer_service_free'];
	$buyer_service_free		=	$_GET['buyer_service_free'];
	$event_fee				=	$_GET['event_fee'];
	$mainTitle				=	$_GET['mainTitle'];
	$additionalTitles		=	$_GET['additionalTitles'];
	$additionalPrices		=	$_GET['additionalPrices'];


if($mainTitle!='' && $mainTitle!='undefined' && $mainPrice!='' && $mainPrice!='undefined'){

	$buyer_service_free_after_percent		=	$mainPrice*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer_service_free/100;
		
	$prometer_service_free_after_percent	=	$mainPrice*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$prometer_service_free_after_percent	=	$prometer_service_free_after_percent*$prometer_service_free/100;
?>

<div>
  <div class="ev_fltlft" style="width:195px; padding-right:10px"> <?php echo $mainTitle; ?> &nbsp;</div>
  <div class="ev_fltlft" style="width:133px; padding-right:10px; text-align:center">
    <?php if ($mainPrice!='undefined' && $mainPrice!=''){echo "$".number_format($mainPrice, 2,'.','');} ?>
    &nbsp;</div>
  <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center">$ <?php echo number_format($prometer_service_free_after_percent, 2,'.',''); ?> &nbsp;</div>
  <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center">$ <?php echo number_format($buyer_service_free_after_percent, 2,'.',''); ?> &nbsp;</div>
  <div class="ev_fltlft" style="width:143px; padding-right:10px; text-align:center">$ <?php echo  number_format($mainPrice+$buyer_service_free_after_percent, 2,'.',''); ?> &nbsp;</div>
  <div class="clr" style="height:5px"></div>
</div>
<?php
}
if($additionalTitles!='' && $additionalPrices!=''){

$titles = explode(",", $additionalTitles);
$prices = explode(",", $additionalPrices);
if(count($titles)>0){
for ($i=0;$i<count($titles);$i++){
if($prices[$i]!='' && $titles[$i]!=''){
	$buyer_service_free_after_percent		=	$prices[$i]*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer_service_free/100;
	
	$prometer_service_free_after_percent	=	$prices[$i]*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
	$prometer_service_free_after_percent	=	$prometer_service_free_after_percent*$prometer_service_free/100;
?>
<div>
  <div class="ev_fltlft" style="width:195px; padding-right:10px"> <?php echo  $titles[$i]; ?> &nbsp;</div>
  <div class="ev_fltlft" style="width:133px; padding-right:10px; text-align:center">
    <?php if ($prices[$i]!=''){echo "$".number_format($prices[$i], 2,'.','');}?> &nbsp;</div>
  <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center">$ <?php echo  number_format($prometer_service_free_after_percent, 2,'.',''); ?> &nbsp;</div>
  <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center">$ <?php echo  number_format($buyer_service_free_after_percent, 2,'.',''); ?> &nbsp;</div>
  <div class="ev_fltlft" style="width:143px; padding-right:10px; text-align:center">$ <?php echo  number_format($prices[$i]+$buyer_service_free_after_percent, 2,'.',''); ?> &nbsp;</div>
  <div class="clr" style="height:5px"></div>
</div>
<?php
}}}}
?>