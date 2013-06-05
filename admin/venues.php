<?php

require_once("database.php"); 
require_once("header.php"); 

// add extra images

$bc_venue_type		=	$_POST["venue_type"];
$bc_venue_name		=	$_POST["venue_name"];
$bc_venue_address	=	$_POST["venue_address"];
$bc_venus_radius	=	$_POST["venus_radius"];
$bc_venue_lng		=	$_POST["venue_lng"];
$bc_venue_lat		=	$_POST["venue_lat"];
$bc_add_date		=	$_POST["add_date"];
$bc_status			=	$_POST["status"];
$bc_del_status		=	$_POST["del_status"];
$bc_venue_city		=	$_POST["venue_city"];
$bc_venue_state		=	$_POST["venue_state"];
$bc_venue_country	=	"US";
$bc_venue_zip		=	$_POST["venue_zip"];
$bc_categories		=	$_POST["categories"];
$bc_averagerating	=	$_POST["averagerating"];
$bc_tags			=	$_POST["tags"];
$bc_phone			=	$_POST["phone"];
$bc_neighbor		=	$_POST["neighbor"];
$bc_setting_plan	=	$_POST['setting_plan'];

$faddress = urlencode($bc_venue_address.', ' . $bc_venue_city . ', ' .  $bc_venue_state);

if(isset($_GET["id"])){
	$frmID	=	$_GET["id"];
}
if(isset($_GET["event_id"])){
	$event_id = $_GET["event_id"];
}

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

	

if (isset($_POST["submit"]) ) {

$errors = array();

		if ($_POST["venue_name"] == "")
			$errors[] = "Venue_name: can not be empty";
		if ($_POST["venue_address"] == "")
			$errors[] = "Venue_address: can not be empty";
			
		if ($_FILES["image"]["name"] == "" && $action1=='save')
			$errors[] = "Image: can not be empty";
		elseif($_FILES["image"]["name"]=='' && $_POST['hasImg']=='')
			$errors[] = "Image: can not be empty";

	$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
	for ($i=0;$i<count($errors); $i++) {
		$err .= '<li>' . $errors[$i] . '</li>';
	}
	$err .= '</ul></td></tr></table>';


	if (!count($errors)) {
		
		if ( $bc_venue_lng == '' || $bc_venue_lat == '' )
			$latlng = getGeoLocation($faddress);
		else {
			$latlng['lng'] = $bc_venue_lng;
			$latlng['lat'] = $bc_venue_lat;
		}
		
		if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["image"]["name"] ;
			if ($action1 == "edit") {
				deleteImage($frmID,"venues","image");
			}
			move_uploaded_file($_FILES["image"]["tmp_name"], '../venue_images/'.$bc_image);
			makeThumbnail($bc_image, '../venue_images/', '', 250, 250,'th_');
			$sql_img = " image = '$bc_image' , ";
		}
		
		if (isset($_FILES["setting_plan"]) && !empty($_FILES["setting_plan"]["tmp_name"])) {
		 	$bc_setting_plan  = time() . "_" . $_FILES["setting_plan"]["name"] ;
			if ($action1 == "edit") {
				deleteImage($frmID,"venues","setting_plan");
			}
			move_uploaded_file($_FILES["setting_plan"]["tmp_name"], '../venue_images/'.$bc_setting_plan);
			makeThumbnail($bc_setting_plan, '../venue_images/', '', 250, 250,'th_');
			$sql_setting_plan = " setting_plan = '$bc_setting_plan' , ";
		}
		
		
		
		 if ($action1 == "save") {
		 	$bc_source_id	=	'Admin'.rand();
			$sql	=	"insert into venues (source_id,venue_type,venue_name,venue_address,venus_radius,venue_lng,venue_lat,add_date,status,del_status,venue_city,venue_state,venue_country,venue_zip,categories,averagerating,tags,phone,neighbor,image,setting_plan) values ('" . $bc_source_id . "','" . $bc_venue_type . "','" . $bc_venue_name . "','" . $bc_venue_address . "','" . $bc_venus_radius . "','" . $latlng['lng'] . "','" . $latlng['lat'] . "','" . $bc_add_date . "','" . $bc_status . "','" . $bc_del_status . "','" . $bc_venue_city . "','" . $bc_venue_state . "','" . $bc_venue_country . "','" . $bc_venue_zip . "','" . $bc_categories . "','" . $bc_averagerating . "','" . $bc_tags . "','" . $bc_phone . 
"','" . $bc_neighbor . "','". $bc_image ."','".$bc_setting_plan."')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			
			if($event_id != ''){
				$venue_events_Q = "insert into venue_events (venue_id, event_id) values('" . $frmID . "','" . $event_id . "')";
				mysql_query($venue_events_Q);
				$venue_event_id = mysql_insert_id();
				
				if($venue_event_id > 0){
					echo "<script type='text/javascript'> window.location.href='events.php?id=".$event_id."'; </script>";
				}	
				
			}
				
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update venues set  venue_name = '" . $bc_venue_name . "',venue_type='". $bc_venue_type ."', venue_address = '" . $bc_venue_address . "', venus_radius = '" . $bc_venus_radius . "', venue_lng = '" . $latlng['lng'] . "', venue_lat = '" . $latlng['lat'] . "', add_date = '" . $bc_add_date . "', status = '" . $bc_status . "', del_status = '" . $bc_del_status . "', venue_city = '" . $bc_venue_city . "', venue_state = '" . $bc_venue_state . "', venue_country = '" . $bc_venue_country . "', venue_zip = '" . $bc_venue_zip . "', categories = '" . $bc_categories . "', averagerating = '" . $bc_averagerating . "', tags = '" . $bc_tags . "', phone = '" . $bc_phone . "',". $sql_img . $sql_setting_plan." neighbor = '" . $bc_neighbor . "' where id=$frmID";
			$res	=	mysql_query($sql) ;
			if ($res) {
				$sucMessage = "Record Successfully updated.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		
		if ( is_array($_FILES['eimage']) ) {
			for($i=0;$i< count($_FILES['eimage']); $i++) {
				$einame = $_FILES['eimage']['name'][$i];
				$etname = $_FILES['eimage']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, '../venue_images/'.$ei_image);
					makeThumbnail($ei_image, '../venue_images/', '', 315, 300,'th_');
					//@unlink('../venue_images/'.$ei_image);
					if ( $ei_image != '' && $frmID > 0 )
						mysql_query("insert into venue_images (venue_id,image) VALUES ('". $frmID ."','". $ei_image ."') ");
				}		
			}
		}
		
	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit

if ( $_GET['delete'] > 0 ) {
	$r = mysql_query("select * from venue_images where id='". $_GET['delete'] ."'");
	if ( $rr = mysql_fetch_assoc($r) ) {
		@unlink('../venue_images/'.$rr['image']);
		@unlink('../venue_images/th_'.$rr['image']);
		@unlink('../venue_images/'.$rr['setting_plan']);
		@unlink('../venue_images/th_'.$rr['setting_plan']);
	}	
	mysql_query("delete from venue_images where id='". $_GET['delete'] ."'");
	?>
	<script>window.location.href="venues.php?id=<?php echo $frmID;?>";</script>
	<?php
}

$sql	=	"select * from venues where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_source_id		=	$row["source_id"];
		$bc_venue_type		=	$row["venue_type"];
		$bc_venue_name		=	$row["venue_name"];
		$bc_venue_address	=	$row["venue_address"];
		$bc_venus_radius	=	$row["venus_radius"];
		$bc_venue_lng		=	$row["venue_lng"];
		$bc_venue_lat		=	$row["venue_lat"];
		$bc_add_date		=	$row["add_date"];
		$bc_status			=	$row["status"];
		$bc_venue_city		=	$row["venue_city"];
		$bc_venue_state		=	$row["venue_state"];
		$bc_venue_country	=	$row["venue_country"];
		$bc_venue_zip		=	$row["venue_zip"];
		$bc_categories		=	$row["categories"];
		$bc_averagerating	=	$row["averagerating"];
		$bc_tags			=	$row["tags"];
		$bc_phone			=	$row["phone"];
		$bc_neighbor		=	$row["neighbor"];
		$bc_image			=	$row["image"];
		$bc_setting_plan	=	$row['setting_plan'];
	} // end if row
	$action = "edit";
} // end if 

?>
<script type="text/javascript" src="../js/jquery-ui_1.8.7.js"></script>
<script type='text/javascript' src='js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
<script type="text/javascript">
$().ready(function() {
	$("#venue_name").autocomplete("get_venue_list.php", {
		width: 260,
		matchContains: true,
		//mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
});
</script>


<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<input type="hidden" name="venue_type"  value="<?php echo $bc_venue_type; ?>" />
<input type="hidden" name="venue_lng"  value="<?php echo $bc_venue_lng; ?>" />
<input type="hidden" name="venue_lat"  value="<?php echo $bc_venue_lat; ?>" />
<input type="hidden" name="venus_radius"  value="<?php echo $bc_venus_radius; ?>" />
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Add/Edit Venue</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>

<tr>
<td width="20%" align="right" class="bc_label">Venue Type:</td>
<td width="80%" align="left" class="bc_input_td">
<input type="text" name="venue_type" id="venue_type" class="bc_input" style="width:350px" value="<?php echo $bc_venue_type; ?>" /></td>
</tr>

<tr>
<td align="right" class="bc_label">Venue Name:</td>
<td align="left" class="bc_input_td">
<input type="text" name="venue_name" id="venue_name" class="bc_input" style="width:350px" value="<?php echo $bc_venue_name; ?>" /></td>
</tr>

<tr>
<td align="right" class="bc_label">Venue Address:</td>
<td align="left" class="bc_input_td">
<input type="text" name="venue_address" id="venue_address" class="bc_input" style="width:350px" value="<?php echo $bc_venue_address; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">City:</td>
<td align="left" class="bc_input_td">
<input type="text" name="venue_city" id="venue_city" class="bc_input" value="<?php echo $bc_venue_city; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">State:</td>
<td align="left" class="bc_input_td">
<input type="text" name="venue_state" id="venue_state" class="bc_input" value="<?php echo $bc_venue_state; ?>"/>
<font color="red">2 char. State Code</font>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Zip:</td>
<td align="left" class="bc_input_td">
<input type="text" name="venue_zip" id="venue_zip" class="bc_input" value="<?php echo $bc_venue_zip; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">Phone:</td>
<td align="left" class="bc_input_td">
<input type="text" name="phone" id="phone" class="bc_input" value="<?php echo $bc_phone; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">Longitude/Latitude:</td>
<td align="left" class="bc_input_td">
<?php echo $bc_venue_lng; ?>, <?php echo $bc_venue_lat; ?>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Setting Plan:</td>
<td align="left" class="bc_input_td">
<?php 
if( $bc_image != '' ) {
	echo '<img src="../venue_images/th_'.$bc_setting_plan .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="venues|setting_plan|'.$setting_plan.'|../venue_images/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
?>
<input type="file" name="setting_plan" id="image" /><br />
<?=$image_del?>
</td></tr>



<tr>
<td align="right" class="bc_label">Main Image:</td>
<td align="left" class="bc_input_td">
<?php 
if( $bc_image != '' ) {
	echo '<img src="../venue_images/th_'.$bc_image .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="venues|image|'.$bc_image.'|../venue_images/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
?>
<input type="hidden" name="hasImg" value="<?php echo $bc_image; ?>" />
<input type="file" name="image" id="image" /><br />
<?=$image_del?>
</td></tr>

<tr>
<td colspan="2" align="center"  > <!--main-->

<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center" id="add_url_ist">
<?php 

if($frmID) { 
	$msql	=	"select * from venue_images where venue_id = $frmID";
	$mres	=	mysql_query($msql);
	$count = 0;
	if ( mysql_num_rows($mres) > 0 ) {
	?>
	<tr >
	<td align="right" width="20%" class="bc_label">Extra Images(s):</td>
	<td align="left" width="80%" class="bc_input_td">
	<?php
	while ($mrow = mysql_fetch_assoc($mres))
	{
		$count ++;
		$bce_image = $mrow['image'];
		echo '<div style="float:left; margin-right:10px"><img src="../venue_images/th_'.$bce_image .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	?>
	<a href="javascript:deleteExtraImage(<?php echo $mrow['id'] ;?>)"><img src="images/delete.png" border="0" ></a>
	</div>
	<?php } ?>
</td>
</tr>
<?php } ?>
<?php } ?>

<tr id="image_tr_1">
<td align="right" width="20%" class="bc_label">Extra Images(s):</td>
<td width="80%" align="left" class="bc_input_td">
<input type="hidden" value="1" />
<input type="file" name="eimage[]" id="eimage" class="bc_input" value=""/>
<span id="add_more_btn_1"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage(1);">&nbsp;&nbsp;Add More</span></span>
</td>
</tr>

</table>


</td>
</tr>

<tr>
<td align="right" class="bc_label">Average Rating:</td>
<td align="left" class="bc_input_td">
<input type="text" name="averagerating" id="averagerating" class="bc_input" value="<?php echo $bc_averagerating; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">Tags:</td>
<td align="left" class="bc_input_td">
<input type="text" name="tags" id="tags" class="bc_input" style="width:350px" value="<?php echo $bc_tags; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">Neighborhood:</td>
<td align="left" class="bc_input_td">
<input type="text" name="neighbor" id="neighbor" class="bc_input" style="width:350px" value="<?php echo $bc_neighbor; ?>"/></td>
</tr>

<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" value="Save" class="bc_button" />
</td>
</tr>
</table>
</form>

<script type="text/javascript">

$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide();
		$("#delImg_" + imgInfo[1]).attr("src", "images/no_image.png");
	}
});

function add_newImage(id)
{
	var next_tr = id+1;
	var new_url_feild = '<tr id="image_tr_'+next_tr+'"><td align="right" width="20%"  class="bc_label">Extra Images(s):</td><td width="80%" align="left" class="bc_input_td"><input type="hidden" value="'+next_tr+'" /><input type="file" name="eimage[]" id="eimage" class="bc_input" /><span id="add_more_btn_'+next_tr+'"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage('+next_tr+');">&nbsp;&nbsp;Add More</span></span></td></tr>';
	$('#add_more_btn_'+id).html('&nbsp;&nbsp;<img src="images/delete.png" onclick="remove_image('+id+')" style="cursor:pointer">');
	$('#add_url_ist').append(new_url_feild);
	
}
function deleteExtraImage(id)
{
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		window.location.href= 'venues.php?id=<?php echo $frmID;?>&delete='+id;
	}
}

</script>

<?php 
require_once("footer.php"); 
?>
