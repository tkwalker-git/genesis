<?php

	require_once('../admin/database.php');
	require_once('../site_functions.php');


	$venue_id 		= $_POST['venue_id'];
	$event_id 		= $_POST['event_id'];
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];


	$venue_event_q = "select event_id from venue_events where venue_id = '$venue_id' and event_id != '$event_id'";
	
	$venue_event_res 	= mysql_query($venue_event_q) ;
	$venue_event_total 	= mysql_num_rows($venue_event_res);
	
	$total_pages 	= ceil($venue_event_total/5);
	
	$pagenum = (int) $page;

	if ($direction == 'next') {
		$start = $pagenum * 5 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 5 ; 
	}
	
	$limit = ' LIMIT '. $start . ' , 5';
	
	$venue_name = attribValue("venues","venue_name","where id=$venue_id");
	
?>
		
		<div class="recommendedBlock" style="border: 1px solid #C1C1C1;; margin-top:10px;">
		<div class="recommended_heading heading_dark_16">
			<span style="float:left;">Other Events at <?php echo $venue_name;?></span>
			<div class="next_prev" style="margin-top:0px; float:right;">
			  <div class="prev_btn">
				  <?php if ($pagenum > 1) { ?>
					
						<a href="javascript:loadNextRecEvent('<?php echo ABSOLUTE_PATH;?>','<?php echo $venue_id;?>','<?php echo $event_id;?>','prev',<?php echo $pagenum;?>)">
							<img src="<?php echo IMAGE_PATH;?>prev.png" width="60" height="20" border="0" />
						</a>
					
					<?php }else{ ?>
						  <a href="javascript:void(0)">
							 <img src="<?php echo IMAGE_PATH;?>prev_disabled.png" />
						  </a>
					<?php } ?>
				</div>
				<div class="next_btn">
				<?php if ( $pagenum < $total_pages ) { ?>
					<a href="javascript:loadNextRecEvent('<?php echo ABSOLUTE_PATH;?>','<?php echo $venue_id;?>','<?php echo $event_id;?>','next',<?php echo $pagenum;?>)">
						<img src="<?php echo IMAGE_PATH;?>next_btn.png" width="60" height="20" border="0" />
					</a>	
				<?php }else{ ?>
				<a href="javascript:void(0)">
					<img src="<?php echo IMAGE_PATH;?>next_disabled.png" />
				</a>
				<?php } ?>
				</div>
				
				<div class="clr"></div>
			</div><!--end next_prev-->
		</div>
		
		<ul class="category_ul">
		<?php 
		
			$venue_event_q = "select event_id from venue_events where venue_id = '$venue_id' and event_id != '$event_id' " . $limit;
			$venue_event_res 	= mysql_query($venue_event_q) ;
			while($venue_event_r = mysql_fetch_assoc($venue_event_res)){
			$eventId = 	$venue_event_r['event_id'];
			$i++;
			if($i > 5){
				break;
			}
		 ?>
			<?php
				$sql2 = "select * from events where id = '$eventId' order by event_date limit 5";
				$res2 = mysql_query($sql2); 
				while ($rows2 = mysql_fetch_assoc($res2) ) {
					$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
					$full_name		= DBout($rows2['event_name']);
					$event_date 	= getEventStartDates($rows2['id']);
					$source			= $rows2['event_source'];
					$event_image	= getEventImage($rows2['event_image'],$source,1);
					$event_url		= getEventURL($rows2['id']);
			?>	
			<li>
				<div class="heading_dark_12" style="width:140px; text-align:center"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></div>
				<div class="d_style_12" style="text-align:center; width:140px;"><?php echo $event_date;?></div>
				<div class="small_event_photo_block">
					<div class="small_event_photo_border">
						<div style="overflow:hidden; width:127px; height:160px">
							<a href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a>
						</div>	
					</div><!--end big_event_photo_border-->
				</div><!--end big_event_photo_block-->
				<div class="add_to_event_btn_small"><?php getAddToWallButton($rows2['id'],1); ?></div>
				<div class="star_rating" style="text-align:center"><?php getEventRatingAggregate($rows2['id']); ?></div>
			</li>
			<?php } ?>
			<?php } ?>
		<div class="clr"></div>
		</ul>
	
	</div><!--end recommendedBlock-->
	
	</div>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
<?php	
	
/*	
	
	$rs 			= mysql_query("select *,(select event_date from event_dates where event_date>DATE_SUB(CURDATE(),INTERVAL 1 DAY) LIMIT 1) as event_date from events where subcategory_id='". $sub_id ."' ");
	$total_rec		= mysql_num_rows($rs);
	$total_pages 	= ceil($total_rec/5);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * 4 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 4 ; 
	}
			
	$limit = ' LIMIT '. $start . ' , 4';
	
	$sql1 = "select *,(select event_date from event_dates where event_date>DATE_SUB(CURDATE(),INTERVAL 1 DAY) LIMIT 1) as event_date from events where subcategory_id='". $sub_id ."' " . $limit;
	
	$res1 = mysql_query($sql1);
	$tot_events = mysql_num_rows($res1);
	
	if ( $tot_events > 0 ) {
		while ($rows1 = mysql_fetch_assoc($res1) )
	  	{
			$event_name 	= breakStringIntoMaxChar(DBout($rows1['event_name']),25);
			$full_name		= DBout($rows1['event_name']);
			$event_date 	= date("M d, Y",strtotime($rows1['event_date']));
			$event_image	= getEventImage($rows1['event_image']);
			$event_url		= ABSOLUTE_PATH . 'category/' . $category_seo_name . '/' . $sub_cat_seo_name . '/' . $rows1['id'] . '.html';	
			 ?>
			  <table style="width:233px; float:left;">
			  <tr>
				<td width="233">
					<div class="txt2"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_name;?></a></div>
					<div class="date"><?php echo $event_date;?></div>
					<div class="imag" ><div style="overflow:hidden; width:163px; height:200px"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_image;?></a></div></div>
					<div class="add_event2"><a href="<?php echo $event_url;?>"><img src="<?php echo IMAGE_PATH;?>add_event22.png" /></a></div>
				</td>
			  </tr>
			  <tr>
				<td><div class="star"></div></td>
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
					<img src="<?php echo IMAGE_PATH;?>prev.png" />
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
		
*/		
		?>
