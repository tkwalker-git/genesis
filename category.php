<?php


require_once('admin/database.php');
require_once('site_functions.php');

if($_SESSION['userZip'])
	$userZip =	"and zipcode in (".$_SESSION['userZip'].")";
			
			
$category_seo_name 	= isset($_GET['seo_name']) ? $_GET['seo_name'] : $_GET['category'];

$catRow			= getCompleteRow("categories", "where seo_name='$category_seo_name'");
$category_id 	= $catRow['id'];
$catName		= DBout($catRow['name']);
$catDesc		= DBout($catRow['descr']);

$meta_title		= ($catRow['meta_title']!='') ? DBout($catRow['meta_title']) : $catName;
$meta_kwords	= DBout($catRow['meta_keywords']);
$meta_descrp	= ($catRow['meta_description']!='') ? DBout($catRow['meta_description']) : breakStringIntoMaxChar(strip_tags($catDesc),150);

$sub_cate 	= $_GET['sub_cat'];

if ( $sub_cate != '' ) 
	$sub_category = true;
else
	$sub_category = false;	

$page_url = ABSOLUTE_PATH . 'category/' . $category_seo_name . '.html';

$dFilters = array(""=>"All","today"=>"Today","week"=>"This Week","weekend"=>"This Weekend","month"=>"This Month");

$df = 'is_expiring =1';

if ( isset($_GET['df']) ) {
	$date_filter = $_GET['df'];
	
	$rr = mysql_query('SELECT DATE_FORMAT( curdate( ) , "%w" ) as cud ');
	if ( $rro = mysql_fetch_assoc($rr) )
		$cu_day = (int)$rro['cud'];
	
	$weekend_day = 6 - $cu_day;
	$weekend_day1= 7 - $cu_day;
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

$event_count = getSingleColumn('tot',"select count(*) as tot from events where type!='draft' && event_status='1' ".$userZip." AND ". $df ." and category_id=" . $category_id);
	
include_once('includes/header.php');
?>


<script type="text/javascript">
(function($){
 $.fn.extend({
 
 	customStyle : function(options) {
	  if(!$.browser.msie || ($.browser.msie&&$.browser.version>6)){
	  return this.each(function() {
	  
			var currentSelected = $(this).find(':selected');
			$(this).after('<span class="customStyleSelectBox"><span class="customStyleSelectBoxInner">'+currentSelected.text()+'</span></span>').css({position:'absolute', opacity:0,fontSize:$(this).next().css('font-size')});
			var selectBoxSpan = $(this).next();
			var selectBoxWidth = parseInt($(this).width()) - parseInt(selectBoxSpan.css('padding-left')) -parseInt(selectBoxSpan.css('padding-right'));			
			var selectBoxSpanInner = selectBoxSpan.find(':first-child');
			selectBoxSpan.css({display:'inline-block'});
			selectBoxSpanInner.css({width:selectBoxWidth, display:'inline-block'});
			var selectBoxHeight = parseInt(selectBoxSpan.height()) + parseInt(selectBoxSpan.css('padding-top')) + parseInt(selectBoxSpan.css('padding-bottom'));
			$(this).height(selectBoxHeight).change(function(){
				selectBoxSpanInner.text($(this).val()).parent().addClass('changed');
			});
			
	  });
	  }
	}
 });
})(jQuery);


$(function(){

$('.styled').customStyle();

});
</script>
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
							$query = "select * from categories "; 
							$res = mysql_query($query);
							while ($r = mysql_fetch_assoc($res)){
								
								if ( DBout($r['seo_name']) == $category_seo_name ) 
									echo '<li class="libutton" style="border-left:none;"><a href="#" class="a_active"><span class="span_active">'. DBout($r['name']) .'</span></a></li>';
								else
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
	  <td colspan="6" style="padding:15px">
	  	<table width="100%" align="center" cellpadding="5" cellspacing="0">
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
	  
	   
	  
	  <?php if ( trim($catDesc) != '' ) { ?>
	  	
		<tr>
		<td colspan="6" style="padding:20px; padding-top:0px">
			<?php echo $catDesc;?>
		</td>
		</tr>
		
	  <?php } ?>
	  
	 <?php 
	 	
		$sql 	= "select * from sub_categories where categoryid='". $category_id ."' and (select count(*) from events where subcategory_id=sub_categories.id AND is_expiring =1 ) > 0" ;
		$res	= mysql_query($sql);
		while ($rows = mysql_fetch_assoc($res) ) {
			$sub_cat_id			= $rows['id'];
			$sub_cat_name 		= DBout($rows['name']);
			$sub_cat_seo_name 	= DBout($rows['seo_name']);
			
			$sub_cat_all_url	= ABSOLUTE_PATH . 'category-all/' . $category_seo_name . '/' . $sub_cat_seo_name . '.html';
			//$_SESSION['userZip']='';
			
			
			$sql1 = "select *,(select event_date from event_dates where expired=0 AND event_id=events.id ORDER by event_date ASC LIMIT 1) as event_date from events where event_status='1' ".$userZip."  AND subcategory_id='". $sub_cat_id ."' AND " . $df . " ORDER by featured DESC, event_date";
			$res1 = mysql_query($sql1);
			$tot_events = mysql_num_rows($res1);
	  ?>
	  <tr>
		<td colspan="6">
		<div id="event_round">
		<table width="932" border="0" >
	  <tr>
		<td colspan="3"><h2 class="txt1"><?php echo $sub_cat_name . ' ('. $tot_events .')';?></h2></td>
		<td width="833"><div class="seeall_btn">
		<?php if ( $tot_events > 4 ) { ?>	
		<a href="<?php echo $sub_cat_all_url; if($_GET['df']){ echo "?df=".$_GET['df'];}?>"><img src="<?php echo IMAGE_PATH;?>seebtn.png" /></a>
		<?php } ?>
		</div>
		</td>
	  </tr>
	  <tr>
		<td colspan="4"><div class="topimg"></div></td>
		</tr>
		</table>
		
		<?php returnFormatedEvents($res1,$tot_events,$category_id,$sub_cat_id); ?>
	 
	</div>
	
	
	</td>
	  </tr>
	  <?php }?>
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