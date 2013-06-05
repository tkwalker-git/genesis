<?php 

require_once('admin/database.php');
require_once('site_functions.php');

$page='locations';
$meta_title	= 'Available Locations';
include_once('includes/header.php');



/*<?php echo ABSOLUTE_PATH; ?>category/live-entertainment.html*/
?>

<style>

.bc_label
{
	color: #000000;
    font-size: 13px;
    font-weight: normal;
    width: 180px;
	padding:11px 7px 11px 0;
}

.bc_input_td input
{
	border: 1px solid #CCCCCC;
    border-radius: 3px 3px 3px 3px;
    height: 20px;
    padding-left: 2px;
	width:300px;
}

</style>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Available Locations</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
           <div class="whiteMiddle" style="padding-top:1px;">
		   <?php
			   $qry		= "select * from `events` where `is_expiring`='1' && `event_status`='1' && `zipcode`!=0 GROUP BY `zipcode`";
			   $res		= mysql_query($qry);
			   $count	= mysql_num_rows($res);
			   $i=0;
			   $zipcodes ='';
			   while($row = mysql_fetch_array($res)){
			   	$i++;
			   	if($i == $count)
					$coma	= '';
				else
					$coma	= ',';
			   	$zipcodes .= $row['zipcode'].$coma;
			   }
			   
			   $res2 = mysql_query("select * from `zipcodes` where `zipcode` in (".$zipcodes.") GROUP BY `city`");
			   while($row2 = mysql_fetch_array($res2)){
			   ?>
			   	<div style="font-size:13px; padding:8px 10px; float:left; width:22%">
				   	<a href="?zipSel=<?php echo $row2['zipcode']; ?>" style="color:#0099FF"><?php echo ucwords(strtolower($row2['city'])); ?></a>
				</div>
			<?php
			   }   
			?>
			<div class="clr"></div>
			</div>
        </div>
      </div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>
