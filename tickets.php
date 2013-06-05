<?php require_once('admin/database.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$meta_title = "Ordered Tickets";
if($_GET['file']){
$url	=	base64_decode($_GET['file']);
$split	=	explode("&id=",$url);
$zip	=	$split[0];
$id		=	$split[1];
$r = mysql_query("select * from `tickets_record` WHERE `id` = '$id' && `status` = '0'");
if(mysql_num_rows($r)){
mysql_query("UPDATE `tickets_record` SET `status` = '1' WHERE `id` = '$id'");
header("location: tickets/$zip");
}
else{
header("location: tickets.php");
}}
require_once('includes/header.php');
?>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <!-- Market Place Top Start -->
    <div class="marketPlaceTop">
      <div class="markeetPlace_title">Ordered Tickets</div>
      <div class="clear"></div>
    </div>
    <!-- Market Place Top END -->
    <!-- Markeet Place InrBody Start -->
    <div class="marketInrBody">
      <div class="markPurchasedTitle"> <span class="ev_fltlft">Purchased Date</span>
        <div class="clear"></div>
      </div>
      <div class="markPurchasedMdl">
        <?php
		
		$re = mysql_query("select * from `tickets_record` where `user_id`='$user_id' && `status`='0'");
		$numRows	=	mysql_num_rows($re);
		if($numRows){
		$i = 0;
		while($ro = mysql_fetch_array($re)){
		$order_id	=	$ro['order_id'];
		
		
		if(file_exists(DOC_ROOT .  'tickets/' . $ro['file_name'] )){
		
		
		  
		$a++;
		if( ($i%2) == 0)
			 $class='preferenceWhtBox';
			else
			$class='preferenceBlueBox';
		?>
        <div class="markPurchasedProduct <?php echo  $class; ?>" style=" <?php if ($numRows==$a){echo 'border-bottom:none;';} ?>"><span class="date" style="width: 374px;">
          <?php
			$orderDate	=	getSingleColumn('date',"select * from `orders` where `id`='$order_id'");
			 echo date('F d, Y', strtotime($orderDate)); ?>
          </span> <span class="view_special" style=" padding: 9px 0 5px 0;"> <a href="?file=<?php echo base64_encode($ro['file_name']."&id=".$ro['id']); ?>"><img src="<?php echo IMAGE_PATH; ?>download_ticket.png" alt="" title=""></a></span>
          <div class="clear"></div>
        </div>
        <?php
		$i++;}}}
		else{
			echo "<br>&nbsp; &nbsp; <strong>No Tickets</strong><br>&nbsp;";
		}
		if($i==0){
			echo "<br>&nbsp; &nbsp; <strong>No Tickets</strong><br>&nbsp;";
		}
		?>
      </div>
      <div class="markPurchasedBottom">&nbsp;</div>
    </div>
    <!-- Markeet Place InrBody End -->
  </div>
</div>
<?php require_once('includes/footer.php');?>