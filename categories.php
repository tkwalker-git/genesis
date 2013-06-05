<?php

require_once('admin/database.php');
require_once('site_functions.php');

$event_count = getSingleColumn('tot',"select count(*) as tot from events " );
	
include_once('includes/header.php');
?>

<div id="container-outer" style="width:970px; margin:auto;">
	<div id="event_container">
	<table width="970" cellpadding="5" cellspacing="0">
		<tr>
			<td align="left" class="viewevents_title">All <strong>Categories</strong></td>
			<td align="left" width="169" class="event_count"><?php echo $event_count;?></td>
		</tr>
	</table>
	<table width="970" border="0">
	  <tr>
		<td>

		
	  		<ul class="allcategories">
			
			<?php
				$query = "select * from categories "; 
				$res = mysql_query($query);
				while ($r = mysql_fetch_assoc($res)){
					$category_id 		= $r['id'];
					$category_seo_name	= $r['seo_name'];
					echo '<li><a href="'. ABSOLUTE_PATH . 'category/' . DBout($r['seo_name']) .'.html"><span>'. DBout($r['name']) . '</span></a><ul>';
					
					$sql 	= "select * from sub_categories where categoryid='". $category_id ."' and (select count(*) from events where subcategory_id=sub_categories.id) > 0" ;
					//$sql 	= "select * from sub_categories where categoryid='". $category_id ."' " ;
					$res2	= mysql_query($sql);
					while ($rows = mysql_fetch_assoc($res2) ) {
						$sub_cat_id			= $rows['id'];
						$sub_cat_name 		= DBout($rows['name']);
						$sub_cat_seo_name 	= DBout($rows['seo_name']);
						
						$sub_cat_all_url	= ABSOLUTE_PATH . 'category/' . $category_seo_name . '/' . $sub_cat_seo_name . '.html';
						
						echo '<li><a href="'. $sub_cat_all_url . '"><span>'. $sub_cat_name . '</span></a>';
					}	
					
					echo '</ul></li><li style="float:none; clear:both">&nbsp;</li>';	
				}
			?>
			
		</ul>			

		</td>
	  </tr>
	</table>
	</div>
	<!--end of event_container-->
</div>
<div class="clr"></div>

<?php include_once('includes/footer.php'); ?>