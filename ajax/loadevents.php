<?php

	require_once('../admin/database.php');
	require_once('../site_functions.php');


	$category_id 	= $_POST['category_id'];
	$sub_id 		= $_POST['sub_category_id'];
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	
	if($_SESSION['userZip'])
		$userZip =	"and zipcode in (".$_SESSION['userZip'].")";
			
	$rs 			= mysql_query("select id from events where event_status='1' ".$userZip." and subcategory_id='". $sub_id ."' ");
	$total_rec		= mysql_num_rows($rs);
	$total_pages 	= ceil($total_rec/4);
	
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * 4 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 4 ; 
	}
			
	$limit = ' LIMIT '. $start . ' , 4';
	
	$sql1 = "select *,(select event_date from event_dates where expired=0 AND event_id=events.id ORDER by event_date ASC LIMIT 1) as event_date from events where event_status='1'  ".$userZip." and subcategory_id='". $sub_id ."' AND is_expiring =1 ORDER by featured DESC, event_date " . $limit;
	
	$res1 = mysql_query($sql1);
	$tot_events = mysql_num_rows($res1);
	
	if ( $tot_events > 0 ) {
		while ($rows1 = mysql_fetch_assoc($res1) )
	  	{
			$event_name 	= breakStringIntoMaxChar(DBout($rows1['event_name']),25);
			$full_name		= DBout($rows1['event_name']);
			$event_date 	= getEventStartDates($rows1['id']);
			$source			= $rows1['event_source'];
			$event_image	= getEventImage($rows1['event_image'],$source);
			$event_seo_name = $rows1['seo_name'];
			$event_id		= $rows1['id'];
			
			$category_seo_name 	= attribValue("categories","seo_name","where id=" . $category_id );
			$sub_cat_seo_name	= attribValue("sub_categories","seo_name","where id=" . $sub_id );
			
			$event_url		= ABSOLUTE_PATH . 'category/' . $category_seo_name . '/' . $sub_cat_seo_name . '/' . $event_seo_name . '.html';	
			 ?>
			  <table style="width:233px; float:left;">
			  <tr>
				<td width="233">
					<div class="txt2"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_name;?></a></div>
					<div class="date"><?php echo $event_date;?></div>
					<div class="imag" >
						<div style="position:relative;z-index:9996; width:163px; height:200px">
							<?php 
								if ( $rows1['featured'] == 1 ) 
									$corner_image = 'featured_event.png';
								else if ( $rows1['free_event'] == 1 ) 
									$corner_image = 'free_event.png';
								else
									$corner_image = '';
								
								if ( $corner_image != '' ) {		
							?>
							<div style="position:absolute; bottom:-3px; right:-3px; z-index:999999; width:80px; height:75px">
								<img src="<?php echo ABSOLUTE_PATH;?>images/<?php echo $corner_image;?>" />
							</div>
							<?php		
								}
							?>
							<a style="overflow:hidden; width:163px; height:200px; display:block; background-color:#FFFFFF" href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_image;?></a>
						</div>
					</div>
					<div class="add_event2"><?php echo getAddToWallButton($event_id,''); ?></div>
				</td>
			  </tr>
			  <tr>
				<td align="center"><?php getEventRatingAggregate($event_id);?></td>
			  </tr>
			  </table>
	  	<?php 
	  		}
		?>
		<table width="100%" cellpadding="0" cellspacing="0" style="float:none; clear:both">
		  <tr>
			<td width="823"><div class="prev">
				<?php if ($pagenum > 1) { ?>
				<a href="javascript:loadNextSubCategory('<?php echo ABSOLUTE_PATH;?>','<?php echo $category_id;?>','<?php echo $sub_id;?>','prev',<?php echo $pagenum;?>)">
					<img src="<?php echo IMAGE_PATH;?>prev_btn.gif" />
				</a>
				<?php } else { ?>
					<a href="javascript:void(0)">
					<img src="<?php echo IMAGE_PATH;?>prev_disabled.png" />
				</a>
				<?php } ?>
				</div> 
				</td>
			<td width="84"><div class="next">
			<?php if ( $pagenum < $total_pages ) { ?>
			<a href="javascript:loadNextSubCategory('<?php echo ABSOLUTE_PATH;?>','<?php echo $category_id;?>','<?php echo $sub_id ;?>','next',<?php echo $pagenum;?>)"><img src="<?php echo IMAGE_PATH;?>next.png" /></a>
			<?php } else { ?>
			<a href="javascript:void(0)">
					<img src="<?php echo IMAGE_PATH;?>next_disabled.png" />
				</a>
				<?php } ?>
			</div></td>
		  </tr>
		</table>
		<?php	
		}	
		?>
