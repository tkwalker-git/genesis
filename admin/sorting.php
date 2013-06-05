<?php

require_once("database.php");

require_once("header.php");

require_once("config.php");



$link   = (int)$_GET['link'];

$table	= $pages[$link]['table'];

$field  = 'sort_order';

$title  = $pages[$link]['add_sort_Title'];

$filter = '';

if( $table == 'banner_ads' ) {
	$bc_arr_dimensions	=	array("980*180"=>"980*180","160*600"=>"160*600","300*250"=>"300*250");
	$bc_dimensions		=	urldecode($_GET["dim"]);
	
	if ($bc_dimensions != '' )
		$filter = " where dimension = '". $bc_dimensions ."'";	
}

?>

<script>

function applyFilter(val)
{
	window.location.href = 'sorting.php?link=<?php echo $link;?>&dim=' + encodeURI(val);
}

</script>

<div id="info">&nbsp;</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0">    

    <tr class="bc_heading"><td height="10" colspan="2">Sort <?=$pages[$link]['title']?></td></tr>
	
	<?php if( $table == 'banner_ads' ) { ?>
		<tr>
			<td style="padding:20px" colspan="2">
				<table>
					<tr>
						<td class="bc_label"><strong>Apply Filter:</strong> 
							<select name="dimensions" id="dimensions" class="bc_input" onchange="applyFilter(this.value)" >	
							<option value="" selected="selected">All</option>
							<?php 
							foreach($bc_arr_dimensions as $key => $val)
							{
								if ($key == $bc_dimensions)
									$sel = "selected";
								else
									$sel = "";	
							?>
							<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
							<?php } ?>
							 </select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<?php } ?>
    <tr><td style="padding: 10px;"><font color="red">Drag and Drop to sort items. It will save the order automatically.</font></td></tr>

    <tr>

    <td colspan="2">

        <ul id="reOrder">

            <?php
				
			 $sq = "SELECT * FROM $table ". $filter ." ORDER BY $field asc";

                $q = mysql_query($sq);

                while( $r = mysql_fetch_assoc($q) ) {

                    $id = $r['id'];

                    $name = DBout($r["$title"]);
					
					echo '<li id="li_'.$id.'"><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" /> '. $name.'</li>';
						

                }

            ?>

        </ul>

    </td>

    </tr>

</table>



<?php require_once("footer.php"); ?>

<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>

<script type="text/javascript" src="js/dragable.js"></script>

<script type="text/javascript">

  $(document).ready(function() {

    $("#reOrder").sortable({

      handle : '.handle',

      update : function () {

		  var order = $('#reOrder').sortable('serialize');

  		$("#info").load("reOrder.php?tbl=<?=$table?>&fld=<?=$field?>&" + order);

      }

    });

});

</script>

<style type="text/css">

#info {

	display: block;

}

#reOrder {

	padding: 0px;

    width: 100%;

	list-style: none;

    background-color: fuchsia;

}

#reOrder li {

    float: left;

	padding: 5px 10px;

    margin-bottom: 1px;

    margin-right: 1px;

    width: 22%;

	background-color: #efefef;

	height: 50px;

}

#reOrder li img.handle {

	margin-right: 20px;

	cursor: move;

}

</style>