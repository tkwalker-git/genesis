<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

	
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	
	$sql = "select * from deals ";
	$rsd = mysql_query($sql);
	
	$total_rec		= mysql_num_rows($rsd);
	$total_pages 	= ceil($total_rec/2);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * 2 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 2 ; 
	}
			
	$limit = ' LIMIT '. $start . ' , 2';
	
	$sql = "select * from deals order by id " . $limit;
//	$sql = "select * from `deals` ORDER BY `id` LIMIT 0,1";
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
		
		//If the description is more than 200 characters
		if (strlen($desc) > 240) {
		//Take the first 200 characters...
			$view_desc = substr($desc, 0, 240);
			//Look for the last space in the description
			$temp = strrpos($view_desc, ' ');
			//And cut everything after that point, and add three dots to show there's more
			$view_desc = substr($desc, 0, $temp). '...';
		} else {
			//If the description is <= 200 chars, show the whole description
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
	
<?php }?>	



 <div align="right">
 <?php if ($pagenum > 1) { ?>
			<a href="javascript:loadDeals2('/','prev',<?php echo $pagenum;?>)"><img src="<?php echo IMAGE_PATH;?>prev.png" alt="" title=""></a>
		<?php } else { ?>
			<a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>prev.png" alt="" title=""></a>
		<?php } ?>
		
		&nbsp; &nbsp;
		
		
		<?php if ( $pagenum < $total_pages ) { ?>
		<a href="javascript:loadDeals2('/','next',<?php echo $pagenum;?>)"><img src="<?php echo IMAGE_PATH;?>nxt.png" alt="" title=""></a>
		<?php } else { ?>
			<a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>nxt.png" alt="" title=""></a>
		<?php } ?>