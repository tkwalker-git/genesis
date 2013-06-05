<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

	
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	$member_id 		= $_SESSION['LOGGEDIN_MEMBER_ID'];
	$sql = "select * from `events` where `userid`='$member_id' && `event_status`='1' && `is_expiring`='1' && `event_type`!='0'";
	$rsd = mysql_query($sql);
	
	$total_rec		= mysql_num_rows($rsd);
	$total_pages 	= ceil($total_rec);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = $pagenum-1; 
	}
	
$limit = ' LIMIT '. $start . ' , 1';
	

	
	?>
	<style>
	a:hover{
	text-decoration:none;
	}
	</style>
	<div class="db_box">
              <div class="head">Active Event
                <div style="float:right">
					<a href="<?php echo ABSOLUTE_PATH; ?>create_event.php">
						<img src="<?php echo IMAGE_PATH; ?>create_new_event.png" align="left" style="margin-right:10px">
					</a>
					<div id="img" style="width:20px; float:left">	
					<?php
					if ($pagenum > 1) { ?>
						<a href="javascript:loadActvEvnt('<?php echo ABSOLUTE_PATH;?>','prev',<?php echo $pagenum;?>)">
							<img title="Previous" src="<?php echo IMAGE_PATH; ?>ar_left_actv.png" />
						</a>
					<?php }
					else{?>
						<img title="" src="<?php echo IMAGE_PATH; ?>ar_lft.png" />
					<?php
					}
					if ( $pagenum < $total_pages ) { ?>
						<a href="javascript:loadActvEvnt('<?php echo ABSOLUTE_PATH;?>','next',<?php echo $pagenum;?>)">
							<img title="Next" src="<?php echo IMAGE_PATH; ?>ar_rght_actv.png" />
						</a>
					<?php
					} else {
					?>
					<img title="Next" src="<?php echo IMAGE_PATH; ?>ar_rght.png" />
					<?php
					} ?>
					</div>
					
				</div>
              </div>
              <div class="active_event">
                <div class="db_showEvn">
                  <?php
				  	
		
		$sql = "select *,(select event_date from event_dates where expired=0 AND event_id=events.id ORDER by event_date ASC LIMIT 1) as event_date from events where `userid`='$member_id' && `event_status`='1' && `is_expiring`='1' && `event_type`!='0' ORDER by event_date ASC ".$limit;
					$res = mysql_query($sql);
					while($rows2 = mysql_fetch_array($res)){
						$event_id		= $rows2['id'];
						$event_name 	= breakStringIntoMaxChar(DBout($rows2['event_name']),20);
						$full_name		= DBout($rows2['event_name']);
						$event_date 	= getEventStartDates($rows2['id']);
						$source			= $rows2['event_source'];
						$event_image	= getEventImage($rows2['event_image'],$source,'1');
						$event_url		= getEventURL($rows2['id']);					
					}
				?>
                  <div class="db_nameEvn"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo ucwords(strtolower($event_name));?></a></div>
                  <div class="db_dateEvn"><?php echo $event_date; ?></div>
                  <div style="padding-top:11px;"><a style="display:block; height:155px; overflow:hidden;" href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo str_replace('align="left"','',$event_image);?></a></div>
                </div>
                <div class="db_detailEvn">
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Tickets Sold :</div>
                    <div class="db_fl2"><?php echo getTicketsRecord($event_id,'Sold'); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Gross Sales :</div>
                    <div class="db_fl2"><?php echo "$".getTicketsRecord($event_id,'Gross Sales'); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%"># of Tickets left:</div>
                    <div class="db_fl2"><?php echo getTicketsRecord($event_id,'Tickets Left'); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Page View:</div>
                    <div class="db_fl2"><?php echo getSingleColumn('view',"select * from `events` where `id`='$event_id'"); ?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Conversion Rate:</div>
                    <div class="db_fl2"><?php echo conversionRate($event_id)."%";?></div>
                    <div class="clr"></div>
                  </div>
                  <div class="db_dtdRow">
                    <div class="ev_fltlft" align="right" style="width:56%">Time till Event:</div>
                    <div class="db_fl2">
                      <?php
						echo $time_till = timeTillEvent($event_id);
						?>
                    </div>
                    <div class="clr"></div>
                  </div>
                </div>
                <!-- end db_detailEvn -->
              </div>