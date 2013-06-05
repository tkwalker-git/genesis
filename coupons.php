<?php

require_once("admin/database.php"); 


if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
if ($_SESSION['usertype']!=2)
		echo "<script>window.location.href='dashboard.php';</script>";

$member_id 			= 	$_SESSION['LOGGEDIN_MEMBER_ID'];
$bc_code			=	DBin($_POST["code"]);
$bc_flat_value		=	DBin($_POST["flat_value"]);
$bc_percent_value	=	DBin($_POST["percent_value"]);
$bc_valid_from		=	DBin(strtotime ($_POST["valid_from"]));

if ($_POST["expires"] > 0 )
	$bc_expiration_date	=	strtotime ($_POST["expiration_date"]) + ( (int)$_POST["expires"] * 86400);
else
	$bc_expiration_date	=	DBin(strtotime ($_POST["expiration_date"]));
		
$bc_expires			=	DBin($_POST["expires"]);
$bc_active			=	DBin($_POST["active"]);

$bc_coupon_type		=	$_POST["coupon_type"];
$bc_arr_coupon_type	=	array("1" => "One Time Only","2" => "Never Expire","3" => "Date Based");

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ( trim($bc_code) == '' || isCouponValid($bc_code)==0 )
	$errors[] = 'Coupon Code is Emtpy or Invalid';
if ( trim($bc_coupon_type) == '' )
	$errors[] = 'Select Coupon Type';	
if (!( $bc_flat_value > 0 || $bc_percent_value > 0 ) )
	$errors[] = 'Please enter at least one discount value';
	
	

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into coupons (code,flat_value,percent_value,valid_from,expiration_date,expires,active,coupon_type) values ('" . $bc_code . "','" . $bc_flat_value . "','" . $bc_percent_value . "','".$bc_valid_from ."','". $bc_expiration_date . "','" . $bc_expires . "','" . $bc_active . "','". $bc_coupon_type ."')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
				$saved_class = 'saved_class';
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update coupons set coupon_type='". $bc_coupon_type ."', code = '" . $bc_code . "', flat_value = '" . $bc_flat_value . "', percent_value = '" . $bc_percent_value . "', valid_from = '" . $bc_valid_from ."', expiration_date = '" . $bc_expiration_date . "', expires = '" . $bc_expires . "', active = '" . $bc_active . "' where id=$frmID";
			$res	=	mysql_query($sql);
			if ($res) {
				$sucMessage = "Record Successfully updated.";
				$saved_class = 'saved_class';
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit


$sql	=	"select * from coupons where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_code			=	DBout($row["code"]);
		$bc_flat_value		=	DBout($row["flat_value"]);
		$bc_percent_value	=	DBout($row["percent_value"]);
		$bc_valid_from		=	DBout($row["valid_from"]);
		$bc_expiration_date	=	DBout($row["expiration_date"]);
		$bc_expires			=	DBout($row["expires"]);
		$bc_active			=	DBout($row["active"]);
		$bc_coupon_type		=   $row['coupon_type'];
	} // end if row
	$action = "edit";
} // end if 

function isCouponValid($code)
{
	$res = mysql_query("select * from coupons where code='". $code ."'");
	
	if ( mysql_num_rows($res) > 0 )
		return 0;
		
	return 1;
}





if ( $_GET['delete'] > 0 ) {
		
		$dEvent_id = $_GET['delete'];
		if(mysql_query("delete from coupons where id='$dEvent_id' AND created_by='". $member_id ."'"))
			$sucMessage = 'Coupon is deleted successfully.<br />';
		else
			$sucMessage = '<strong>Error</strong>: Try again later.<br />';
			
		}
		
	
	
	$meta_title	= 'Coupons';
	require_once('includes/header.php');
?>
<script>
function removeAlert(url) {

	var con = confirm("Are you sure to delete this coupon?")

	if (con) 

		window.location.href = url;

}


function enableDisableControls(val)
{

	if (val == 3 ) {
		document.getElementById("valid_from").disabled=false;
		document.getElementById("expiration_date").disabled=false;
		document.getElementById("expires").disabled=false;
	} else {
		document.getElementById("valid_from").disabled=true;
		document.getElementById("expiration_date").disabled=true;
		document.getElementById("expires").disabled=true;
	}
	
}

</script>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard.css" rel="stylesheet" type="text/css">
<style>
.new_input{
	background:#FFFFFF;
	color:#000000
	}
</style>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Coupons</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:1px;">
				 
				 <div style="margin:10px 2px" class="ev_fltlft"> <a href="<?php echo ABSOLUTE_PATH;?>create_coupon.php"><img src="<?php echo IMAGE_PATH; ?>create_coupon.gif" /></a> </div>
                
                <div class="clr"></div>
                <font color='green'><?php echo $sucMessage;?> </font> <br>
                <div style="background-color:#EEEEEE; border-bottom:#CCCCCC solid 2px; border-top:#CCCCCC solid 2px; font-size:14px; font-weight:bold">
                  <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                      <td width="48%">Code</td>
                      <td width="21%">Coupon Type</td>
                      <td width="11%">Active</td>
                      <td width="20%" align="center">Action</td>
                    </tr>
                  </table>
                </div>
				<?php
			$res = mysql_query("select * from `coupons` where `created_by`='$member_id'");
			if(mysql_num_rows($res)){
				$i=0;
				while($row = mysql_fetch_array($res)){ 
				$i++;				
				if( ($i%2) == 0)
					 $class='class="preferenceBlueBox"';  
				 else
					  $class='class="preferenceWhtBox"';
										  ?>
				
				<div style="line-height:20px; min-height:30px" <?=$class?>>
                  <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                      <td width="48%"><?php echo $row['code']; ?></td>
                      <td width="21%"><?php 
					  if($row['coupon_type'] == 1)
					  	echo "One Time Only";
					elseif($row['coupon_type'] == 2)
						echo "Never Expire";
					elseif($row['coupon_type'] == 3)
						echo "Date Based";
						?></td>
                      <td width="11%"><?php
					  if($row['active'])
					  	echo ucwords($row['active']);
					else
						echo "No";
					  ?></td>
                      <td width="20%" align="center" ><a href="<?php echo ABSOLUTE_PATH; ?>create_coupon.php?id=<?php echo $row['id']; ?>" style="color:#0066FF">Edit</a> - <a onclick="removeAlert('coupons.php?delete=<?php echo $row['id'];?>')" href="javascript:void(0)" style="color:#0066FF">Delete</a></td>
                    </tr>
                  </table>
                </div>
				<?php
				}
			}
			else{
				echo "<br /><br /> <strong>No Coupon Found</strong>";
			}
				?>
		   </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
require_once("includes/footer.php"); 
?>