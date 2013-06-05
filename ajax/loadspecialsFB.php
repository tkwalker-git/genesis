<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$event_id	=	$_POST['event_id'];
	$active='specials';
?>
        <?php include("../flayerMenuFB.php"); ?>
        <div class="event-name-big2"><strong>Event Specials</strong></div>
        <div id="deals">
          <?php
						$sql = "select * from deals order by id limit 0,2";
						$rsd = mysql_query($sql);
						while($row1=mysql_fetch_array($rsd))
						{
							$subtitle 	= $row1['subtitle'];
							$dealurl 	= $row1['linktype'];
							$price 		= str_replace("?","&#8364;",$row1['price']);
							$value 		= str_replace("?","&#8364;",$row1['value']);
							$saving		= $row1['saving'];
							$image 		= $row1['imageurl'];
							$desc 		= strip_tags(DBout($row1['description']));
							$view_desc 	= '';
							
						
							$source		= $row1['source'];

							if (strlen($desc) > 240) {
								$view_desc 	= substr($desc, 0, 240);
								$temp 		= strrpos($view_desc, ' ');
								$view_desc 	= substr($desc, 0, $temp). '...';
							} else {
								$view_desc = $desc;
							}
							
							if ($source == 'living') {
								$dealurl = $dealurl . '?aff_id=3481&offer_id=4';
								$padding = '';
							} else {
								$dealurl = $dealurl . '?utm_medium=afl&utm_campaign=5241250&utm_source=rvs';	
								
								$price	 = (int)$price/100;
								$value	 = (int)$value/100;
								
								$saving	 = (int)$saving/100;
								$saving  = ( ($saving * 100)/$value);
								
								$price   = '$' . number_format($price,2);
								$value   = '$' . number_format($value,2);
								
								$saving  = ceil($saving) . '%';
								
								$padding = '<br>';
								
								
							}
						?>
       
            <div class="event_specials"> <img src="<?php echo $image;?>" alt="" title="" align="left"> <span class="froziHeading" style="color:#005683"><?php echo $subtitle;?></span>
             <p style="min-height:100px"><?php echo $view_desc;?></p>
              <div class="clr"></div>
              <div align="right"><a href="<?php echo $dealurl;?>" target="_blank"><img src="<?php echo IMAGE_PATH;?>buy_special2.png" alt="" title=""></a></div>
    <div class="clr"></div>
         </div> 
            
          
<?php } ?> <div align="right" style="width:100%"><a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>prev.png" alt="" title=""></a>
		
		&nbsp; &nbsp;
		
		
		<a href="javascript:loadDeals2('/','next',1)"><img src="<?php echo IMAGE_PATH;?>nxt.png" alt="" title=""></a></div> </div>
		  <div class="clr"></div>