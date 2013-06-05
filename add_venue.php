<?php



require_once("admin/database.php"); 

require_once("site_functions.php");



if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)

		echo "<script>window.location.href='login.php';</script>";

		

		

$user_id			= $_SESSION['LOGGEDIN_MEMBER_ID'];

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



$errors = array();



$checZipValid = checkValidZip($bc_venue_zip);

		





if ($_POST["venue_name"] == "")

	$errors[] = "Venue_name: can not be empty";

if ($_POST["venue_address"] == "")

	$errors[] = "Venue_address: can not be empty";

if($checZipValid=='0')

	$errors[] = "Zip is Invalid";

if ($_FILES["image"]['name'] == "")

	$errors[] = "Image: can not be empty";

	

	

	

	



$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';

for ($i=0;$i<count($errors); $i++) {

	$err .= '<li>' . $errors[$i] . '</li>';

}

$err .= '</ul></td></tr></table>';	



if (isset($_POST["submit"]) ) {



	if (!count($errors)) {

		

		$latlng = getGeoLocation($faddress);

		

		

		if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

		 	$bc_image  = time() . "_" . $_FILES["image"]["name"] ;

			if ($action1 == "edit") {

				deleteImage($frmID,"venues","image");

			}

			move_uploaded_file($_FILES["image"]["tmp_name"], 'venue_images/'.$bc_image);

			makeThumbnail($bc_image, 'venue_images/', '', 250, 250,'th_');

			@unlink('venue_images/'.$bc_image);

			$sql_img = " image = '$bc_image' , ";

		}

		

		if (isset($_FILES["setting_plan"]) && !empty($_FILES["setting_plan"]["tmp_name"])) {

		 	$bc_setting_plan  = time() . "_" . $_FILES["setting_plan"]["name"] ;

			if ($action1 == "edit") {

				deleteImage($frmID,"venues","setting_plan");

			}

			move_uploaded_file($_FILES["setting_plan"]["tmp_name"], 'venue_images/'.$bc_setting_plan);

			makeThumbnail($bc_setting_plan, 'venue_images/', '', 250, 250,'th_');

			@unlink('venue_images/'.$bc_setting_plan);

			$sql_setting_plan = " setting_plan = '$bc_setting_plan' , ";

		}

		

		

		

		

		

		 if (1) {

		 	$bc_source_id	=	'User'.rand();

			$sql	=	"insert into venues (source_id,venue_type,venue_name,venue_address,venus_radius,venue_lng,venue_lat,add_date,status,del_status,venue_city,venue_state,venue_country,venue_zip,categories,averagerating,tags,phone,neighbor,image,setting_plan,user_id) values ('" . $bc_source_id . "','" . $bc_venue_type . "','" . $bc_venue_name . "','" . $bc_venue_address . "','" . $bc_venus_radius . "','" . 

			$latlng['lng'] . "','" . $latlng['lat'] . "','" . $bc_add_date . "','" . $bc_status . "','" . $bc_del_status . "','" . $bc_venue_city . "','" . $bc_venue_state . "','" . $bc_venue_country . "','" . $bc_venue_zip . "','" . $bc_categories . "','" . $bc_averagerating . "','" . $bc_tags . "','" . $bc_phone . "','" . $bc_neighbor . "','". $bc_image ."','". $bc_setting_plan ."','". $user_id ."')";

			$res	=	mysql_query($sql);

			$frmID = mysql_insert_id();

			

			if ($res) {

				$sucMessage = "Record Successfully inserted.";

				$saved=1;

				?>

				<script>

					window.opener.document.getElementById("venue_name").value='<?php echo $bc_venue_name;?>';

					window.opener.document.getElementById("venue_id").value=<?php echo $frmID;?>;

					window.opener.document.getElementById("ev_address1").value='<?php echo $bc_venue_address;?>';

					window.opener.document.getElementById("ev_city").value='<?php echo $bc_venue_city;?>';

					window.opener.document.getElementById("ev_zip").value='<?php echo $bc_venue_zip;?>';

					window.close();

				</script>

				<?php

			} else {

				$sucMessage = "Error: Please try Later";

			} // end if res

		} // end if



	} // end if errors



	else {

		$sucMessage = $err;

	}

} // end if submit

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



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Add New Venue</title>



<link rel="shortcut icon" href="<?php echo IMAGE_PATH; ?>favicon.ico" type="image/x-icon" />

<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>style.css"/>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>

<style>



.dynamicImg {

	float: left;

	padding: 5px;

	margin-right: 10px;

	background-color: #FFFFFF;

	border: 1px solid #C5BEAE;

}

.success {

	padding: 10px;

	color: #CC0000;

	font-weight: bold;

	text-align: center;

}



</style>



</head>



<body style="background-image:url(<?php echo IMAGE_PATH; ?>headar_bg.jpg); background-repeat:repeat-x; width:600px!important; min-width:600px!important; height:500px">



<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >



<table width="550" border="0" cellspacing="0" cellpadding="5" align="center">

<tr>

    <td height="55" colspan="2" valign="top">

		<span class="viewevents_title" style="color:#FFFFFF">Add New Venue</span>

	</td>

  </tr>

  <tr>

    <td align="left" colspan="2" >

    	<img src="<?php echo IMAGE_PATH; ?>logo_transparent.png" width="359" height="61" />

	</td>

  </tr>

<tr>

<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>

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

<select name="venue_state" id="venue_state" class="bc_input">

<option value="" selected="selected">Select State</option>

<?php 

$rs5 = mysql_query("select * from usstates");

while ($r5 = mysql_fetch_assoc($rs5) ) {

	if ($r5['abv'] == $bc_venue_state)

		$sele = 'selected="selected"';

	else

		$sele = '';	

	echo '<option value="'.$r5['abv'] .'" '. $sele .'>'.ucwords(strtolower($r5['state'])) .'</option>';

}

?>

</select>



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

	echo '<img src="venue_images/th_'.$bc_setting_plan .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';

	$image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="venues|image|'.$bc_setting_plan.'|venue_images/" />';

}

else

	echo '<img src="admin/images/no_image.png" class="dynamicImg" width="75" height="76" />';

?>

<input type="file" name="setting_plan" id="setting_plan" /><br />

<?php echo $image_del?>

</td></tr>









<tr>

<td align="right" class="bc_label">Image:</td>

<td align="left" class="bc_input_td">

<?php 

if( $bc_image != '' ) {

	echo '<img src="venue_images/th_'.$bc_image .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';

	$image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="venues|image|'.$bc_image.'|venue_images/" />';

}

else

	echo '<img src="admin/images/no_image.png" class="dynamicImg" width="75" height="76" />';

?>

<input type="file" name="image" id="image" /><br />

<?php echo $image_del?>

</td></tr>



<tr>

<td align="right" class="bc_label">Neighbor:</td>

<td align="left" class="bc_input_td">

<input type="text" name="neighbor" id="neighbor" class="bc_input" style="width:350px" value="<?php echo $bc_neighbor; ?>"/></td>

</tr>



<tr>

<td>&nbsp;</td><td align="left">

	<?php if ( $saved == 1 ) { ?>

		<input name="close" type="button" value="Close" onClick="self.close()" class="bc_button" />

	<?php } else { ?>

		<input name="submit" type="submit" value="Save" class="bc_button" />

	<?php } ?>

	

</td>

</tr>

</table>

</form>



<script type="text/javascript">

/*

$(".delImg").click(function() {

	var con = confirm("Are you sure you want to delete this image?");

	if( con == true ) {

		var imgID = $(this).attr('id');

		var imgInfo = $(this).attr('rel').split('|');

		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );

		$(this).hide();

		$("#delImg_" + imgInfo[1]).attr("src", "admin/images/no_image.png");

	}

});*/





</script>



</body>

</html>