<?php

require_once("admin/database.php");

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
if ($_SESSION['usertype']!=2)
		echo "<script>window.location.href='dashboard.php';</script>";
		

$bc_code			=	DBin($_POST["code"]);
$bc_flat_value		=	DBin($_POST["flat_value"]);
$bc_percent_value	=	DBin($_POST["percent_value"]);
$bc_valid_from		=	DBin(strtotime($_POST["valid_from"]));

$bc_use_limit		=	DBin($_POST["use_limit"]);


if ($_POST["expires"] > 0 )
	$bc_expiration_date	=	strtotime ($_POST["expiration_date"]) + ( (int)$_POST["expires"] * 86400);
else
	$bc_expiration_date	=	DBin(strtotime ($_POST["expiration_date"]));
		
$bc_expires			=	DBin($_POST["expires"]);
$bc_active			=	DBin($_POST["active"]);

$bc_coupon_type		=	$_POST["coupon_type"];
$bc_arr_coupon_type	=	array("1" => "One Time Only","2" => "Never Expire","3" => "Date Based","4" => "Usage Limit");

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();

if ( !isset($_GET['id']) ) {
	if ( trim($bc_code) == '' || isCouponValid($frmID,$bc_code)==0 )
		$errors[] = 'Coupon Code is Emtpy or Invalid';
}
		
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
		 	$created_by = $_SESSION['LOGGEDIN_MEMBER_ID'];
			$sql	=	"insert into coupons (code,flat_value,percent_value,valid_from,expiration_date,expires,active,coupon_type,created_by,use_limit) values ('" . $bc_code . "','" . $bc_flat_value . "','" . $bc_percent_value . "','".$bc_valid_from ."','". $bc_expiration_date . "','" . $bc_expires . "','" . $bc_active . "','". $bc_coupon_type ."','". $created_by ."','". $bc_use_limit ."')";
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
			$sql	=	"update coupons set coupon_type='". $bc_coupon_type ."', flat_value = '" . $bc_flat_value . "', percent_value = '" . $bc_percent_value . "', valid_from = '" . $bc_valid_from ."', expiration_date = '" . $bc_expiration_date . "', expires = '" . $bc_expires . "', active = '" . $bc_active . "',use_limit='". $bc_use_limit ."' where id=$frmID";
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
		$bc_use_limit		=	DBout($row["use_limit"]);
	} // end if row
	$action = "edit";
	$cDisable = 'disabled="disabled"';
} // end if 

function isCouponValid($id,$code)
{
if($id)
	$ids = " && id!=$id";
	$res = mysql_query("select * from coupons where code='". $code ."'". $ids ."");
	
	if ( mysql_num_rows($res) > 0 )
		return 0;
		
	return 1;
}


	$meta_title	= 'Create Coupon';
	require_once('includes/header.php');
?>
<script>

function enableDisableControls(val)
{

	if (val == 3 ) {
		document.getElementById("valid_from").disabled=false;
		document.getElementById("expiration_date").disabled=false;
		document.getElementById("expires").disabled=false;
		document.getElementById("use_limit").disabled=true;
	} else if (val == 4 ) {
		document.getElementById("valid_from").disabled=true;
		document.getElementById("expiration_date").disabled=true;
		document.getElementById("expires").disabled=true;
		document.getElementById("use_limit").disabled=false;		
	} else {
		document.getElementById("valid_from").disabled=true;
		document.getElementById("expiration_date").disabled=true;
		document.getElementById("expires").disabled=true;
		document.getElementById("use_limit").disabled=true;
	}
	
}

</script>
<script language="JavaScript" type="text/javascript" src="admin/js/cal2.js"></script>
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
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Create Coupons</div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
            <form method="post" name="bc_form" enctype="multipart/form-data" action="">
              <input type="hidden" name="bc_form_action" class="new_input" value="<?php echo $action; ?>"/>
              <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
              
                <tr>
                  <td colspan="2" align="center" >&nbsp;</td>
                </tr>
                <?php if ($sucMessage) { ?>
                <tr>
                  <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td width="27%" align="right" class="bc_label"><strong>Code</strong>:</td>
                  <td width="73%" align="left" class="bc_input_td"><input type="text" name="code" id="code" class="new_input" size="50" <?php echo $cDisable;?> value="<?php echo $bc_code; ?>"/>
                  </td>
                </tr>
                <tr>
                  <td align="right" class="bc_label"><strong>Coupon Type</strong>:</td>
                  <td align="left" class="bc_input_td"><select name="coupon_type" id="coupon_type" onchange="enableDisableControls(this.value)" class="new_input" style="width:150px" >
                      <option value="" selected="selected">Select</option>
                      <?php 
foreach($bc_arr_coupon_type as $key => $val)
{
	if ($key == $bc_coupon_type)
		$sel = "selected";
	else
		$sel = "";	
?>
                      <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>
                      <?php } ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td align="right" class="bc_label"><strong>Flat Value ($)</strong>:<br></td>
                  <td align="left" class="bc_input_td" valign="top"><input type="text" name="flat_value" id="flat_value" class="new_input" size="50" value="<?php echo $bc_flat_value; ?>"/><br />
<i>If this value is greater than 0 then it will be considered for discount calulation otherwise % value will be used.</i>
                  </td>
                </tr>
                <tr>
                  <td align="right" class="bc_label"><strong>Percent Value (%)</strong>:</td>
                  <td align="left" class="bc_input_td"><input type="text" name="percent_value" id="percent_value" class="new_input" size="50" value="<?php echo $bc_percent_value; ?>"/>
                  </td>
                </tr>
				
				<tr>
				<td align="right" class="bc_label"><strong>Usage Limit</strong>:</td>
				<td align="left" class="bc_input_td">
				<input type="text" name="use_limit" id="use_limit" class="new_input" size="50" value="<?php echo $bc_use_limit; ?>"/>
				</td>
				</tr>
				
                <tr>
                  <td align="right" class="bc_label"><strong>Valid From</strong>:</td>
                  <td align="left" class="bc_input_td"><input type="text" name="valid_from" <?php if ($bc_coupon_type!=3){ echo 'disabled="disabled"'; } ?> id="valid_from" class="new_input" size="10" readonly="readonly"  value="<?php $bc_valid_from = ($bc_valid_from)? $bc_valid_from:time(); echo date("m/d/Y",$bc_valid_from) ?>"/>
                  </td>
                </tr>
                <tr>
                  <td align="right" class="bc_label"><strong>Expiration Date</strong>:</td>
                  <td align="left" class="bc_input_td"><input type="text" name="expiration_date" <?php if ($bc_coupon_type!=3){ echo 'disabled="disabled"'; } ?> id="expiration_date" class="new_input" size="10" readonly="readonly"  value="<?php $bc_expiration_date = ($bc_expiration_date)? $bc_expiration_date:time(); echo date("m/d/Y",$bc_expiration_date) ?>"/>
                  </td>
                </tr>
                <tr>

                  <td align="right" class="bc_label"><strong>Expires relative</strong>:
                     </td>
                  <td align="left" class="bc_input_td" valign="top"><input type="text" name="expires" id="expires" <?php if ($bc_coupon_type!=3){ echo 'disabled="disabled"'; } ?> class="new_input" size="10" value="<?php echo $bc_expires; ?>"/><br />
					<i>Number of days
                    from now. 
                    If you set this, 
                    the expiration date
                    will be over-written
                    with  the relative date.</i>
                  </td>
                </tr>
                <tr>
                  <td align="right" class="bc_label"><strong>Active</strong>:</td>
                  <td align="left" class="bc_input_td"><?php $is_active = ($bc_active =='yes')? 'checked="checked"':''; ?>
                    <input type="checkbox" name="active" id="active" class="" value="yes" <?php echo $is_active ; ?> />
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td align="left">
				  <a href="<?php echo ABSOLUTE_PATH; ?>coupons.php"><img src="<?php echo IMAGE_PATH; ?>back2.gif" align="left"></a> &nbsp; 
				  <input type="image" src="<?php echo IMAGE_PATH; ?>submit_btn.jpg" name="submit" value="Save" />
				  <input type="hidden" name="submit" value="Save" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
require_once("includes/footer.php"); 
?>

<link href="calendar/jquery.ui.datepicker.css" rel="stylesheet" type="text/css">
<link href="calendar/jquery.ui.theme.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="js/jquery-ui_1.8.7.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script>
	$(function() {
		$( "#expiration_date" ).datepicker({
			dateFormat: "mm/dd/yy",
			changeMonth: true,
			changeYear: true
		});
	});
	$(function() {
		$( "#valid_from" ).datepicker({
			dateFormat: "mm/dd/yy",
			changeMonth: true,
			changeYear: true
		});
	});
</script>