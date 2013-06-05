<?php
	
require_once('admin/database.php');
require_once('site_functions.php');


$term = urldecode($_GET['term']);



include_once('includes/header.php');

if($_SESSION['userZip'])
	$userZip =	"and zipcode in (".$_SESSION['userZip'].")";
	
?>

<div id="container-outer" style="width:970px; margin:auto;">
	<div id="event_container">
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
							$tot_ev = mysql_num_rows($res);
							while ($r = mysql_fetch_assoc($res)){
								if ( DBout($r['seo_name']) == $category_seo_name ) {
									echo '<li class="libutton" style="border-left:none;"><a href="#" class="a_active"><span class="span_active">'. DBout($r['name']) .'</span></a></li>';
									$category_id = $r['id'];
								} else
									echo '<li class="libutton"><a href="'. ABSOLUTE_PATH . 'category/' . DBout($r['seo_name']) .'.html"><span>'. DBout($r['name']) . '</span></a></li>';				
							}
						?>
						<!-- <li class="libutton" style="border-left:none;"><a href="" class="a_active"><span class="span_active">Live Entertainment</span></a></li>
						<li class="libutton"><a href="#"><span>Festivals</span></a></li>
						<li class="libutton"><a href="#"><span>Nightlife</span></a></li>
						<li class="libutton"><a href="#"><span>Networking</span></a></li>
						<li class="libutton"><a href="#"><span>Sports</span></a></li>
						<li class="libutton"><a href="#"><span>Kid Friendly</span></a></li>-->
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
		<table width="958" border="0" style="margin-top:20px; background-image:url(<?php echo IMAGE_PATH;?>event_top.png);background-position:top; background-repeat:no-repeat; margin-left:3px; ">
	  <tr>
	   <td width="591"><a href="" style="border:none;"><!--<img src="<?php echo IMAGE_PATH;?>calender.png" align="right" style="padding-top:5px; border:none;" /></a>--></td>
		<td width="161"><!--<h1>Switch to Calender view</h1>--></td>
		<td width="7"><!--<img src="<?php echo IMAGE_PATH;?>seperator2.png" style="margin-top:5px;" />--></td>
		<td width="212">
			<!--<select class="styled" style="width:200px;">
				<option>Sort Event By...</option>
				<option>Comedy Shows</option>
				<option>Stage Play</option>
				<option>Copncerts</option>
				<option>Poetry</option>
			</select>-->
	</td>
		<td width="7"><img src="<?php echo IMAGE_PATH;?>seperator2.png" style="margin-top:5px;"/></td>
		<td width="173">
			<div class="add_event">
			<?php if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ) { ?>
			<a href="<?php echo ABSOLUTE_PATH;?>create_event.php"><img src="<?php echo IMAGE_PATH;?>add_event3.png" /></a>
			<?php } ?>
			</div>
		</td>
	  </tr>
	  
	 <?php 
			
		$sql1 = "select * from `events` where `event_status`='1' ".$userZip." AND `is_expiring`=1 AND (`event_name` LIKE '%". $term ."%' || `event_description` LIKE '%". $term ."%') ";
			//$sql1 = "select *,(select event_date from event_dates where event_date>DATE_SUB(CURDATE(),INTERVAL 1 DAY) LIMIT 1) as event_date from events where event_status='1' AND MATCH(event_name) AGAINST ('". $term ."') ORDER by event_source";
			$res1 = mysql_query($sql1);
			$tot_events = mysql_num_rows($res1);
	  ?>
	  <tr>
		<td colspan="6">
		<div id="event_round">
		<table width="932" border="0" >
	  <tr>
		<td>
			<h2 class="txt1" style="width:800px; margin-top:10px">
				Search Results for "<?php echo $term;?>"
				<span style="font-size:11px!important; color:#999999!important;"> ( <?php echo $tot_events;?> Event(s) found. )</span>
			</h2>
			
		</td>
	  </tr>
	  <tr>
		<td><div class="topimg"></div></td>
		</tr>
		</table>
	  <?php 
	  	
	if ( $tot_events > 0 ) {

		while ($rows1 = mysql_fetch_assoc($res1) )
	  	{
			
			$event_name 	= breakStringIntoMaxChar(DBout($rows1['event_name']),25);
			$full_name		= DBout($rows1['event_name']);
			$event_date 	= getEventStartDates($rows1['id']); 
			$source			= $rows1['event_source'];
			$event_image	= getEventImage($rows1['event_image'],$source);
				
			//$event_url		= ABSOLUTE_PATH . 'category/' . $category_seo_name . '/' . $subcategory_seo_name . '/' . $rows1['id'] . '.html';
			$event_url		= getEventURL($rows1['id']);
			
	  ?>
	  <table style="width:233px; float:left;">
	  <tr>
		<td width="233">
			<div class="txt2"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_name;?></a></div>
			<div class="date"><?php echo $event_date;?></div>
			<div class="imag" ><div style="overflow:hidden; width:163px; height:200px"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_image;?></a></div></div>
			<div class="add_event2">
				<!-- <a href="<?php echo $event_url;?>"><img src="<?php echo IMAGE_PATH;?>add_event22.png" /></a> -->
				<?php getAddToWallButton($rows1['id']); ?>
			</div>
		</td>
	  </tr>
	  <tr>
		<td align="center"><?php getEventRatingAggregate($rows1['id']);?></td>
	  </tr>
	  </table>

	  	<?php 
	  		}
		} else {
		?>
		<table width="100%">
		  <tr>
			<td height="100" ><div class="txt2" style="color:#990000!important; text-align:center; width:80%!important">
				No event found for your serach criteria.
			</div></td>
		  </tr>
		  </table>
		<?php
		}
		?>
	 
	</div>
	</td>
	  </tr>
	 
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