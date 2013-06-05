<?php

require_once('admin/database.php');
require_once('site_functions.php');

if($_SESSION['userZip'])
	$userZip =	"and zipcode in (".$_SESSION['userZip'].")";
		
$category_seo_name 	= isset($_GET['seo_name']) ? $_GET['seo_name'] : $_GET['category'];
$cat_id 			= attribValue('categories', 'id', "where seo_name='$category_seo_name'");
$sub_cate 	= $_GET['sub_cat'];
$page_url	= ABSOLUTE_PATH . 'category-all/' . $category_seo_name . '/' . $sub_cate . '.html';
$catRow			= getCompleteRow("sub_categories", "where seo_name='$sub_cate' and categoryid = '".$cat_id."'");
$sub_cat_id 	= $catRow['id'];
$scatName		= DBout($catRow['name']);
$scatDesc		= DBout($catRow['descr']);

$meta_title		= ($catRow['meta_title']!='') ? DBout($catRow['meta_title']) : $scatName;
$meta_kwords	= DBout($catRow['meta_keywords']);
$meta_descrp	= ($catRow['meta_description']!='') ? DBout($catRow['meta_description']) : breakStringIntoMaxChar(strip_tags($scatDesc),150);


$dFilters = array(""=>"All","today"=>"Today","week"=>"This Week","weekend"=>"This Weekend","month"=>"This Month");

if ( isset($_GET['df']) ) {
	$date_filter = $_GET['df'];
	
	$rr = mysql_query('SELECT DATE_FORMAT( curdate( ) , "%w" ) as cud ');
	if ( $rro = mysql_fetch_assoc($rr) )
		$cu_day = (int)$rro['cud'];
	
	$weekend_day	= 6 - $cu_day;
	$weekend_day1	= 7 - $cu_day;
	$thisSunday 	= date('Y-m-d', strtotime('sunday'));
	$newdate		= strtotime ( '-2 day' , strtotime ( $thisSunday ) ) ;
	$thisFriday 	= date ( 'Y-m-d' , $newdate );
	
	if ( $date_filter == 'today')
		$df  = '( select DATE_FORMAT(event_date,"%Y-%c-%d") from event_dates where expired=0 AND event_id=events.id and event_date>=curdate() ORDER by event_date ASC LIMIT 1) = DATE_FORMAT( curdate(),"%Y-%c-%d") ';
	else if ( $date_filter == 'week')
		$df  = '(( select event_date from event_dates where expired=0 AND event_id=events.id and `event_date`<=\''.$thisSunday.'\' ORDER by event_date DESC LIMIT 1) AND is_expiring=1 ) ';
	else if ( $date_filter == 'weekend')
		$df  = '(( select event_date from event_dates where expired=0 AND event_id=events.id and `event_date`<=\''.$thisSunday.'\' and `event_date`>=\''.$thisFriday.'\' ORDER by event_date DESC LIMIT 1) AND is_expiring=1 )';
	else if ( $date_filter == 'month')
		$df  = '( ( select DATE_FORMAT(event_date,"%Y-%c") as event_date from event_dates where expired=0 AND event_id=events.id ORDER by event_date DESC LIMIT 1) = DATE_FORMAT(CURDATE(),"%Y-%c") AND is_expiring =1 ) ';
	else
		$df = 'is_expiring =1';
		
}	


		
if($df=='')
	$df = 'is_expiring =1';
	
$event_count = getSingleColumn('tot',"select count(*) as tot from events where event_status='1' ".$userZip." AND ". $df ." and subcategory_id=" . $sub_cat_id);

include_once('includes/header.php');

?>

<div id="container-outer" style="width:970px; margin:auto;">
	<div id="event_container">
	<table width="970" cellpadding="5" cellspacing="0">
		<tr>
			<td align="left" class="viewevents_title">View <strong>Events</strong></td>
			<td align="left" width="169" class="event_count"><?php echo $event_count;?></td>
		</tr>
	</table>	
	<table width="970" border="0">
	  <tr>
		<td>
		<div class="event_header">
		<table border="0">
	  <tr>
	  <td>
	  
	  <div class="navigation_bar">
		
			<div class="nav">
				
				<div class="menuBlock">
				
					<ul>
						<?php
							$query = "select * from categories LIMIT 6"; 
							$res = mysql_query($query);
							while ($r = mysql_fetch_assoc($res)){
								if ( DBout($r['seo_name']) == $category_seo_name ) {
									echo '<li class="libutton" style="border-left:none;"><a href="'. ABSOLUTE_PATH . 'category/' . DBout($r['seo_name']) .'.html" class="a_active"><span class="span_active">'. DBout($r['name']) .'</span></a></li>';
									$category_id = $r['id'];
								} else
									echo '<li class="libutton"><a href="'. ABSOLUTE_PATH . 'category/' . DBout($r['seo_name']) .'.html"><span>'. DBout($r['name']) . '</span></a></li>';				
							}
						?>
					
					</ul>
				
				</div><!--end menu-->
				
			</div><!--end nav-->
			
		</div><!--end navigation_bar-->
		
	</td>
	  </tr>
	</table>
	</div>
	<!--end of header class-->
		<div class="event_center">
		<table width="958" border="0" style="margin-top:25px; background-image:url(<?php echo IMAGE_PATH;?>event_top.png);background-position:top; background-repeat:no-repeat; margin-left:3px; ">
	  <tr>
	   <td  colspan="6"><table width="100%" align="center" cellpadding="5" cellspacing="0">
		<tr>
			<td width="200" nowrap="nowrap"><strong>Show only events happening:</strong></td>
			<td nowrap="nowrap">
				<?php 
				
				
				foreach ($dFilters as $dKey => $dVal) {
					
					if ( $dKey == '' )
						$page_url1 = $page_url;
					else
						$page_url1 = $page_url . '?df='. $dKey;	
					
					if ( $date_filter == $dKey ) { 
						echo '<strong>'.$dVal.'</strong>';
					} else {
				?>
				<a href="<?php echo $page_url1;?>" style="color:#0066FF; text-decoration:underline"><?php echo $dVal;?></a>
				<?php } ?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>

			</td>
			<td width="150" >
				<div class="add_event"><a href="<?php echo ABSOLUTE_PATH;?>create_event.php"><img src="<?php echo IMAGE_PATH;?>add_event3.png" /></a></div>
			</td>
		</tr>
		</table>
		</td>
	  </tr>
	  
	  <?php if ( trim($scatDesc) != '' ) { ?>
	  	
		<tr>
		<td colspan="6" style="padding:20px; padding-top:0px">
			<?php echo $scatDesc;?>
		</td>
		</tr>
		
	  <?php } ?>
	  
	 <?php 
	 	
	$sql 	= "select * from sub_categories where seo_name='". $sub_cate ."' and categoryid = '".$cat_id."' and (select count(*) from events where event_status='1' ".$userZip." and subcategory_id=sub_categories.id) > 0" ;
		$res	= mysql_query($sql);
		$tot 	= mysql_num_rows($res);
		
		if ( $tot > 0 ) {
		
			while ($rows = mysql_fetch_assoc($res) ) {
				$sub_cat_id			= $rows['id'];
				$sub_cat_name 		= DBout($rows['name']);
				$sub_cat_seo_name 	= DBout($rows['seo_name']);
				
				$sub_cat_all_url	= ABSOLUTE_PATH . 'category-all/' . $category_seo_name . '/' . $sub_cat_seo_name . '.html';
				
				$sql1 = "select *,(select event_date from event_dates where expired=0 AND event_id=events.id ORDER by event_date ASC LIMIT 1) as event_date from events where event_status='1' ".$userZip." and subcategory_id='". $sub_cat_id ."' AND " . $df . " ORDER by featured DESC, event_date ";
				$res1 = mysql_query($sql1);
				$tot_events = mysql_num_rows($res1);
	  ?>
	  <tr>
		<td colspan="6" >
		<div id="event_round">
		<table width="932" border="0" >
	  <tr>
		<td colspan="3"><h2 class="txt1"><?php echo $sub_cat_name. ' ('. $tot_events .')';?></h2></td>
		<td width="833">&nbsp;</td>
	  </tr>
	  <tr>
		<td colspan="4"><div class="topimg"></div></td>
		</tr>
		</table>
	 
	 	<?php returnFormatedEvents($res1,$tot_events,$category_id,$sub_cat_id,'all'); ?>
	 
	</div>
	</td>
	  </tr>
	  <?php } } else { ?>
	  	<tr>
		<td colspan="6" >
			<div style="height:400px; color:#990000; font-size:18px; padding-top:100px; text-align:center">
				Oops.. We can't find any event in this category. Please search some time later.
			</div>
	</td>
	  </tr>	
	  <?php } ?>
	</table>
	
		  </div>
		  <!--end of center class-->
		<div class="event_bottom"></div>
		</td>
	  </tr>
	</table>
	</div>
	<!--end of event_container-->
</div>
<div class="clr"></div>

<?php include_once('includes/footer.php'); ?>