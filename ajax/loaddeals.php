<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

	
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	
	$sql = "select * from deals ";
	$rsd = mysql_query($sql);
	
	$total_rec		= mysql_num_rows($rsd);
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
	
	$sql = "select * from deals order by id " . $limit;
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
	<div class="deal_round" style="margin:0 0 5px 0; width:852px">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		  <tbody><tr>
			<td align="left" >
				<div class="deal_label" style="float:left;margin-top:5px; width:625px"><?php echo $subtitle;?></div>
				<div style="float:right; margin:5px 10px"><a href="<?php echo $dealurl;?>" target="_blank"><img src="<?php echo IMAGE_PATH;?>buynow.png"></a></div>
			</td>

		  </tr>
		  <tr>
			
			<td  valign="top" class="deal_content">
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" style="padding-left:20px"><img src="<?php echo $image;?>" ></td>
					<td valign="top" align="left" class="deal_content_td">
						<?php echo $padding;?>
						<p style="min-height:100px"><?php echo $view_desc;?></p>
					
					<table cellspacing="0" cellpadding="0" border="0" align="right">
					  <tbody><tr>
					  <!-- interchange $price and $value -->
						<td valign="top"><div class="label1">Original Price:<span>  <?php echo $value;?></span></div></td>
						<td valign="top"><div class="label1">Deal:<span> <?php echo $price;?></span></div></td>
						<td valign="top"><div class="label2">Savings:<span> <?php echo $saving;?></span></div> </td>
					  </tr>
					</tbody></table>
					</td>
				</tr>
				</table>
				<!--end of round id-->
				
				</td>
		  </tr>
		  
		</tbody></table>
		
	</div><!-- deal-round -->
<?php }?>	

<table width="100%" cellpadding="0" cellspacing="0" style="float:none; clear:both">
  <tr>
	<td width="823"><div class="prev">
		<?php if ($pagenum > 1) { ?>
			<a href="javascript:loadDeals('<?php echo ABSOLUTE_PATH;?>','prev',<?php echo $pagenum;?>)"><img src="<?php echo IMAGE_PATH;?>prev_disabled.png" /></a>
		<?php } else { ?>
			<a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>prev_disabled.png" /></a>
		<?php } ?>
		</div>	
		</td>
	<td width="100" style="padding-left:30px"><div class="next">
		<?php if ( $pagenum < $total_pages ) { ?>
		<a href="javascript:loadDeals('<?php echo ABSOLUTE_PATH;?>','next',<?php echo $pagenum;?>)"><img src="<?php echo IMAGE_PATH;?>next.png" /></a>
		<?php } else { ?>
			<a href="javascript:void(0)"><img src="<?php echo IMAGE_PATH;?>next_disabled.png" /></a>
		<?php } ?>
		</div>
	</td>
  </tr>
</table>