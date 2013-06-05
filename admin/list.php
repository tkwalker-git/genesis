<?php
require_once("database.php"); 
require_once("header.php");

$max_records_per_page = 50;

$cFilter = '';
if ( isset($_GET['category_id']) ) {
	$cFilter = ' AND category_id=' . $_GET['category_id'] ;
	if ( $_GET['subcategory_id'] > 0 ) 
		$cFilter .= ' AND subcategory_id=' . $_GET['subcategory_id'] ;
	$max_records_per_page = 1000;	
}


if ( $_SESSION['PREVIOUS_OPEN_LINK'] != $_GET['link'] ) {
	$_SESSION['SORT_DIRECTION'] = '';
	$_SESSION['ORDER_BY'] = '';
	$_SESSION['SEARCH_FIELD'] = '';
	$_SESSION['SEARCH_TERM'] = '';
}
$link 		= $_GET['link'];
$sucMessage = $_GET['msg'];
$pNum = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = ' limit 0 , ' . $max_records_per_page;
$page   			= $pages[$link]['page'];
$extrapage   		= $pages[$link]['extrapage'];
$extrapage2   		= $pages[$link]['extrapage2'];
$extrapage3   		= $pages[$link]['extrapage3'];
$table  			= $pages[$link]['table'];
$filter 			= $pages[$link]['filter'];
$delete 			= $pages[$link]['delete'];
$addBtn 			= $pages[$link]['add'];
$addExtraBtn 		= $pages[$link]['addExtraBtn'];
$addExtraBtn2 		= $pages[$link]['addExtraBtn2'];
$addExtraBtn3 		= $pages[$link]['addExtraBtn3'];
$title  			= $pages[$link]['title'];
$default_order		= $pages[$link]['default_orderby_column'];
$show   			= $pages[$link]['show'];
$add_new_button 	= $pages[$link]['add_new_button'];
$add_extra_button 	= $pages[$link]['add_extra_button'];
$add_extra_button2 	= $pages[$link]['add_extra_button2'];
$add_extra_button3 	= $pages[$link]['add_extra_button3'];
$searchFieldsList 	= $pages[$link]['searchFieldsList'];
$orderFieldsList 	= $pages[$link]['orderFieldsList'];
$customSQL 			= $pages[$link]['customSQL'];
$group_by 			= $pages[$link]['group_by'];
$add_sort 			= $pages[$link]['add_sort_button'];
$edit 				= $pages[$link]['edit']; // work for value "no"
$viewpage 			= $pages[$link]['viewpage'];
$download 			= $pages[$link]['download'];
$downloadticket 	= $pages[$link]['downloadticket'];
$downloadlink		= $pages[$link]['downloadlink'];
$bulk_delete		= $pages[$link]['bulk_delete']; // work for value "yes"
$images				= $pages[$link]['images']; // only for make images links in home listing
$can_not_delete_ids = $pages[$link]['can_not_delete_ids'];

if ( !is_array($can_not_delete_ids) )
	$can_not_delete_ids = array();

if ( isset($_POST['btnDelete']) ) {
	$tmp_ids = $_POST['delid'];
	$del_ids = implode(",",$tmp_ids);

	$qry = "delete from " . $table . " where id IN (". $del_ids .")";
	@mysql_query($qry);
}


if ( isset($_POST['btnEnable']) ) {
	$tmp_ids = $_POST['delid'];
	$del_ids = implode(",",$tmp_ids);

	$qry = "update " . $table . " set event_status='1' where id IN (". $del_ids .")";
	@mysql_query($qry);
}

if ( isset($_POST['btnDisable']) ) {
	$tmp_ids = $_POST['delid'];
	$del_ids = implode(",",$tmp_ids);

	$qry = "update " . $table . " set event_status='0' where id IN (". $del_ids .")";
	@mysql_query($qry);
}


$orderBy = '';



$eFilter = '';



if ($_SESSION['SORT_DIRECTION'] == '' )

	$_SESSION['SORT_DIRECTION'] = ' DESC ';



if ($_GET['direction'] != '' ) {

	if ($_GET['direction'] == 'asc')

		$_SESSION['SORT_DIRECTION'] = ' ASC ';

	else

		$_SESSION['SORT_DIRECTION'] = ' DESC ';	

}



if ($_SESSION['ORDER_BY'] == '' )

	$_SESSION['ORDER_BY'] = $default_order;



if ($_GET['order'] != '') 

	$_SESSION['ORDER_BY'] =  $_GET['order'];





if ($_SESSION['ORDER_BY'] != '' )

	$orderBy = ' ORDER BY ' . $_SESSION['ORDER_BY'] . $_SESSION['SORT_DIRECTION'];







if ($_GET['search'] != '' && $_GET['term'] != '') {



	$_SESSION['SEARCH_FIELD'] =  $_GET['search'];

	 $_SESSION['SEARCH_TERM']  =  str_replace("'","&#039;",$_GET['term']);



} else {



	$_SESSION['SEARCH_FIELD'] =  '';

	$_SESSION['SEARCH_TERM']  =  '';



}





if ($_SESSION['SEARCH_FIELD'] != '' && $_SESSION['SEARCH_TERM'] != '' ) {

	$eFilter = '';

	if ($_SESSION['SEARCH_FIELD'] == 'all' ) {

//	echo "DESC $table";
		 $res1 = mysql_query("DESC $table"); 

		 while ($row = mysql_fetch_assoc($res1) ) {
		 	if ( $row['Field'] != 'id' )
			 	$eFilter .= "`".$row['Field']."`" . " LIKE '%". $_SESSION['SEARCH_TERM'] ."%' OR ";
		}	
	
			 $eFilter = substr($eFilter,0,-3);

	} else {

		$eFilter = $_SESSION['SEARCH_FIELD'] . " LIKE '%". $_SESSION['SEARCH_TERM'] ."%'";

	}



} else {

	$eFilter = '';

}

if ( $customSQL != '' ) {
	if ( $eFilter != '' )
		$filter = " AND (" . $eFilter . ") " ;
} else {
	if ( $eFilter != '' )
		$filter = " where " . $eFilter;	
}

/*
if ($eFilter != "") {

	$re = substr_count($customSQL, 'where');

 	if (substr_count($customSQL, 'where') == 1){
		$filter = " AND " . $filter ;
	}else{
		$filter = " where " . $filter ;
	}

	if ($eFilter != "")
		$filter = $filter . ' AND ('. $eFilter .')';

} else {

	if ($eFilter != "")
		if (substr_count($customSQL, 'where') == 1){
			$filter = " AND " . $eFilter;
		}else{
			$filter = " where " . $eFilter;
		}
}
*/


$group_by_sql = '';

if ($group_by != '')
	$group_by_sql = ' GROUP BY ' . $group_by . ' ';

if ($customSQL == '' ) 
	$sql = "select * from " . $table . $filter  . $group_by_sql . $orderBy;
else
	 $sql = $customSQL  . $filter . $cFilter . $orderBy;


$paging = array();


$link1 = 'list.php?link=' .$link;

$paging = generatePaging($sql,$link1,$pNum,$max_records_per_page);

$sql = $sql . $paging['limit'];

$res = mysql_query($sql) ;





?>





<table width="100%" border="0" cellpadding="0" cellspacing="0">

  <tr class="bc_heading">

    <td width="60%"><?php echo $title; ?></td>

	<td width="40%" style="font-size:12px;background-position:right;" align="right">

	&nbsp;

	<?php

		if (count($orderFieldsList) > 0 ) {

	?>

	Order By: &nbsp;

	<select name="field" id="field" style="width:100px" onchange="setOder(this.value)">

      <?php

	  foreach ($orderFieldsList as $key => $value) {

	  	$sel = '';

		if ( $value == $_SESSION['ORDER_BY'])

			$sel = ' selected="selected" ';



	  	echo '<option value="'. $value .'" '. $sel .'>' . $key . '</option>';

	  }

	?>

    </select>&nbsp;

	

	<?php

		if ( $_SESSION['SORT_DIRECTION'] == ' ASC ' ) {

			$dirc = 'desc';

			$dimg = 'asc1.gif';

			$txt  = ' Descending ';

		} else {

			$dirc = 'asc';

			$dimg = 'desc1.gif';

			$txt  = ' Ascending ';

			

		}	

	?>

	<a style="color:#FFFFFF; text-decoration:none" href="list.php?link=<?=$link?>&direction=<?=$dirc?>"><?=$txt?><img src="images/<?=$dimg?>" width="21" height="15" border="0" align="absmiddle" /></a>

	

	<?php } ?>

	&nbsp;

	</td>

  </tr>

  

  <tr>

    <td height="25" colspan="2" bgcolor="#efe9db" align="center">

	<table border="0" cellspacing="0" cellpadding="5" width="100%">

  	<tr>

	<td align="center" style="height:36px;">

    

	<?php

	  	if (count($searchFieldsList) > 0 ) {

	?>

	

    	<strong>Search For: </strong>

        <input type="text" name="term" id="term" style="width:250px" value="<?php echo $_SESSION['SEARCH_TERM']; ?>" />

      	<strong> &nbsp; &nbsp; &nbsp; In: &nbsp; </strong>

        <select name="field1" id="field1" style="width:120px">

      <option value="all">All</option>
     
		
      <?php
		
	  foreach ($searchFieldsList as $key => $value) {

	 	$sel = '';

		if ( $value == $_SESSION['SEARCH_FIELD'])

			$sel = ' selected="selected" ';

               
             
	  		echo '<option value="'. $value .'" '. $sel .'>' . $key . '</option>';

	  }

	?>

    </select> &nbsp;

    <img src="images/search.png" onclick="setSearch()" align="absbottom" style="cursor:pointer;" />

    

	<?php } ?>

    <?php if ( $addBtn != 'no' )  {  ?>

  			<input value="<?=$add_new_button?>" type="button" onclick="document.location.href='<?php echo $page; ?>'" class="addBtn" />

  		<?php  }  ?>
		
		    <?php if ( $addExtraBtn == 'yes' )  {  ?>

  			<input value="<?=$add_extra_button?>" type="button" onclick="document.location.href='<?php echo $extrapage; ?>'" class="addBtn" />

  		<?php  }  
		
		if ( $addExtraBtn2 == 'yes' )  {  ?>

  			<input value="<?=$add_extra_button2?>" type="button" onclick="document.location.href='<?php echo $extrapage2; ?>'" class="addBtn" />

  		<?php  }  
		
		if ( $addExtraBtn3 == 'yes' )  {  ?>

  			<input value="<?=$add_extra_button3?>" type="button" onclick="document.location.href='<?php echo $extrapage3; ?>'" class="addBtn" />

  		<?php  }  
		?>
		
		

        <?php if( $add_sort )  {  ?>

  			<input value="Sort <?=$title?>" type="button" onclick="document.location.href='<?=$add_sort?>?link=<?=(int)$_GET['link']?>'" class="addBtn" />

  		<?php  }  ?>

    </td>

  </tr>
	<?php if ( $table == 'events' ) { ?>
	<tr>
	<td align="center" style="height:36px; border-top:#FFF solid 5px">
		<form method="get" action="" enctype="multipart/form-data" name="frm3" id="frm3">
		<input type="hidden" name="link" value="<?php echo $_GET['link'];?>" />
		<div style="float:left; margin-left:50px">
    	<strong>Show Only Category </strong>
        <select name="category_id" id="category_id" style="width:120px" onchange="dynamic_Select('subcategory.php', this.value, 0 )">
      		<option value="">All</option>
			<?php 
				$cat_q = "select * from categories order by id ASC";
				$cat_res = mysql_query($cat_q);
				while($cat_r=mysql_fetch_assoc($cat_res)){ 
			?>
			<option value="<?php echo $cat_r['id']; ?>" <?php if($cat_r['id'] == $_GET['category_id']){?> selected="selected" <?php }?>><?php echo $cat_r['name']; ?></option>
			<?php } ?>
	    </select></div> &nbsp;
 		<div id="subcategory_id" style="float:left;margin-left:20px">
		<select name="subcategory_id" class="bc_input">
			<option selected="selected" value="">Select Category First</option>
		</select>
		</div>
		<div style="float:left; margin-left:20px">
		<input value="Apply" type="button" onclick="document.frm3.submit()" class="addBtn" />
		</div>
		</form>
    </td>

  </tr>
  <?php 
  		
		if ( $_GET['category_id'] > 0 ) {
			$sb = ($_GET['subcategory_id'] > 0) ? $_GET['subcategory_id'] : 0;
			echo "<script>dynamic_Select('subcategory.php', ". $_GET['category_id'] .", ". $sb ." );</script>";
		}
		
  } 
  ?>

</table>

	</td>

  </tr>

  

  <?php

  if ($res)



  {



 ?>

 <tr>

      <td colspan="2" align="left"  class="success">

	  <?php

	  if ($sucMessage != "" )

	  	echo '<br>' . $sucMessage . '<br>';

	  ?>

	  </td>

  </tr>

	<tr><td height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>

  <tr>

    <td colspan="2">

	<?php 

		

		if ($bulk_delete == 'yes' ) 

			echo '<form name="blkDeleteForm" action="" method="post" enctype="multipart/form-data" onsubmit="return checkDelSection()">';

	?>

	<table id="tablesorter" width="99%" cellspacing="0" rules="rows" align="left">

		<thead> 

			<tr>

			<?php

				if ($bulk_delete == 'yes' ) {

				?>

					<th style="width:25px" class="{sorter: false}" ><input type="checkbox" name="sel_all" value="" style="width:auto" onclick="checkAll(this)" /></th>

				<?php	

				}

				foreach ($show as $key => $value) {
				
				?>

					<th style="text-align:left"> &nbsp; <?=$key?></th>

				<?php	

				}

			?>

			<th align="center" class="{sorter: false} action">Action</th>

			</tr>

		</thead> 

		<tbody>

 <?php	  



	  while ($row = mysql_fetch_assoc($res) )

	  {



		$id		   	= $row['id'];

		$eLink	   	= $page . "?id=" . $id;	



		$dLink = '';



		if ($delete == "yes" && !in_array($id,$can_not_delete_ids) ) {
			$ur	   	= "delete.php?id=$id&table=$table&link=$link&page=$pNum" ;
			$dLink	   = '<a href="javascript:void(0)" onclick="removeAlert(\''. $ur .'\')" class="bc_menu"><img src="images/icon_delete.gif" border="0" title="Delete Page" align="absmiddle" /></a>';
				
		} else

			$dLink	   = '<img src="images/icon_delete_dis.jpg" border="0" title="Delete Disabled for this entery" align="absmiddle" />' ;

		

		if ($viewpage != '')

			$viewpagelink = '<a target="_blank" href="../'. $viewpage . '?id=' . $id .'"><img src="images/icon_view.gif" border="0" align="absmiddle" title="Live Preview" /></a>';
		
		if ($download != '')

			$downloadlink = '<a target="_blank" href="'. $download . '?type=rsvp&event_id=' . $id .'"><img src="images/icon_download.png" border="0" align="absmiddle" title="Download RSVP" /></a>';
			
			
			if ($downloadticket != '')

			$downloadlink = '<a target="_blank" href="'. $downloadticket . '?id=' . base64_encode($id) .'"><img src="images/icon_download.png" border="0" align="absmiddle" title="Download Tickets" /></a>';
			
			
		if ($edit == "no") {

			$edLink = "<span style='color:#CCCCCC'>Edit</span>";

			$eLink = '';

		} else

			$edLink = '<a href="' . $eLink . '"><img src="images/icon_edit.png" border="0" align="absmiddle" title="Edit Page" /></a>';



		if( $images == 'yes' ) {

			$link_images = '<a href="home_images.php?id='.$id.'"><img src="images/images.png" border="0" align="absmiddle" title="View Images" /></a>';

		}

  ?>

  	<tr class="trdef">

	<?php

		if ($bulk_delete == 'yes' ) {

		?>

			<td bgcolor="#e4ddd5"><input type="checkbox" name="delid[]" value="<?php echo $row['id'];?>" style="width:auto" /></td>

		<?php	

		}

		foreach ($show as $key => $value) {
			if( $value == 'net_total' )
				$valDisp = '$'.number_format($row[$value], 2);
			else if( $value == 'date_submitted' )
				$valDisp = date('M d Y H:i', strtotime($row[$value]) );
			else if( $value == 'status' )
				$valDisp = ucwords($row[$value]);
			if( $value != 'categoryid' )
			{
				$valDisp = $row[$value];
			}
			else 
			{
				$valDisp = parent_name($row[$value]);	
			}
			?>

			<td style="cursor:pointer" onclick="window.location.href='<?=$eLink?>'"> &nbsp; <?=$valDisp?></td>

			<?php

		}

	?>

		<td class="actionMenu" align="center" ><?=$link_images.' &nbsp; '.$downloadlink.' &nbsp; '.$viewpagelink.' &nbsp; '.$edLink.' &nbsp; '.$dLink?></td>

	</tr>

	



  <?php



  	}

echo '</tbody>';	

	if ($bulk_delete == 'yes' ) {

?>

	<tr><td colspan="6" align="left" style="background-color:#efe9db;border:0px;padding:0px;margin:0px;">

		<input type="submit" name="btnDelete" value="Delete Selected" />
		&nbsp;
		<input type="submit" name="btnEnable" value="Enable Selected" />
		&nbsp;
		<input type="submit" name="btnDisable" value="Disable Selected" />

	</td></tr>

	<?php } ?>



</table>

<?php 

	if ($bulk_delete == 'yes' ) {

		echo '</form>';

	}	

	echo '<strong>' . $paging['pagingString'] . '</strong>';

?>

  	</td>

  </tr>  

<?php



  }



  else



  {



  ?>



  <tr>



    <td colspan="2" align="center" style="padding:10px" class="success">No Current Record</td>



  </tr>



  <?php



  }



  ?>



    <tr>



      <td colspan="2" align="center" style="padding:10px" class="success">



	  <?php 



	  if ($sucMessage != "" )



	  	echo '<br>' . $sucMessage . '<br>';



	  ?>



	  </td>



    </tr>



</table>



<?php

require_once("footer.php"); 



$_SESSION['PREVIOUS_OPEN_LINK'] = $_GET['link'];



?>



<script type="text/javascript">







function removeAlert(url) {

	var con = confirm("Are you sure to delete this entry?")

	if (con) 

		window.location.href = url;

}



function setOder(val) {



	url  = document.location.href;

	st = url.indexOf("&order=");

	if (st != -1 )

		url = url.substring(0,st);

	document.location.href = url + '&order=' + val;

}



function setSearch() {

	fld = document.getElementById("field1").value;

	val = document.getElementById("term").value;

	url  = document.location.href;

	st = url.indexOf("&search=");

	if (st != -1 )

		url = url.substring(0,st);
		
		


    
	document.location.href = encodeURI(url + '&search=' + fld + '&term='+val);

}





function checkAll(elem) {

	elems = document.getElementsByName("delid[]");

	if (elem.checked == true) {

		for (i=0;i<elems.length;i++) {

			elems[i].checked=true;

		}

	} else {

		for (i=0;i<elems.length;i++) {

			elems[i].checked=false;

		}

	}	

}



function checkDelSection()

{

	elems = document.getElementsByName("delid[]");

	for (i=0;i<elems.length;i++) 

		if ( elems[i].checked == true )

			return true;

	

	alert('Please select at least one row to delete.');

	return false;		

	

}



$(".trdef").hover(

	function() {

		$(this).css("background-color", "#F7F7F7");

	},

	function() {

		$(this).css('background-color', '#f2f1ec');

	}

);



</script>