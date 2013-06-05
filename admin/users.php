<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_username	=	$_POST["username"];
$bc_password	=	$_POST["password"];
$bc_firstname	=	$_POST["firstname"];
$bc_lastname	=	$_POST["lastname"];
$bc_dob	=	$_POST["dob"];
$bc_sex	=	$_POST["sex"];
$bc_address	=	$_POST["address"];
$bc_city	=	$_POST["city"];
$bc_state	=	$_POST["state"];

$bc_arr_state	=	array();
$arrRES = mysql_query("select id as id, state as value from usstates");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$bc_arr_state[$bc_row["id"]] = $bc_row["value"];

$bc_zip	=	$_POST["zip"];
$bc_phone	=	$_POST["phone"];
$bc_createddate	=	$_POST["createddate"];
$bc_email	=	$_POST["email"];
$bc_enabled	=	$_POST["enabled"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["username"] == "")
	$errors[] = "User Name can not be empty";
if ($_POST["firstname"] == "")
	$errors[] = "Firstname can not be empty";
if ($_POST["lastname"] == "")
	$errors[] = "Lastname can not be empty";
if ($_POST["dob"] == "")
	$errors[] = "DOB can not be empty";
if ($_POST["address"] == "")
	$errors[] = "Address can not be empty";
if ($_POST["city"] == "")
	$errors[] = "City can not be empty";
if ($_POST["state"] == "")
	$errors[] = "State can not be empty";
if ($_POST["zip"] == "")
	$errors[] = "Zip can not be empty";
if ($_POST["phone"] == "")
	$errors[] = "Phone can not be empty";
if ($_POST["email"] == "")
	$errors[] = "Email can not be empty";
if ($_POST["enabled"] == "")
	$errors[] = "Status can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into users (username,password,firstname,lastname,dob,sex,address,city,state,zip,phone,createddate,email) values ('" . $bc_username . "','" . $bc_password . "','" . $bc_firstname . "','" . $bc_lastname . "','" . $bc_dob . "','" . $bc_sex . "','" . $bc_address . "','" . $bc_city . "','" . $bc_state . "','" . $bc_zip . "','" . $bc_phone . "','" . $bc_createddate . "','" . $bc_email . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			 $sql	=	"update `users` set `username` = '" . $bc_username . "', `firstname` = '" . $bc_firstname . "', `lastname` = '" . $bc_lastname . "', `dob` = '" . $bc_dob . "', `sex` = '" . $bc_sex . "', `address` = '" . $bc_address . "', `city` = '" . $bc_city . "', `state` = '" . $bc_state . "', `zip` = '" . $bc_zip . "', `phone` = '" . $bc_phone . "', `createddate` = '" . $bc_createddate . "', `email` = '" . $bc_email . "' where `id`='". $frmID ."'";
			$res	=	mysql_query($sql);
			if ($res) {
				$sucMessage = "Record Successfully updated.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit
$sql	=	"select * from users where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_username	=	$row["username"];
		$bc_password	=	$row["password"];
		$bc_firstname	=	$row["firstname"];
		$bc_lastname	=	$row["lastname"];
		$bc_dob	=	$row["dob"];
		$bc_sex	=	$row["sex"];
		$bc_address	=	$row["address"];
		$bc_city	=	$row["city"];
		$bc_state	=	$row["state"];
		$bc_zip	=	$row["zip"];
		$bc_phone	=	$row["phone"];
		$bc_createddate	=	$row["createddate"];
		$bc_email	=	$row["email"];
		$bc_enabled	=	$row["enabled"];
	} // end if row
	$action = "edit";
} // end if 

?>
 <script>
    $(function() {
        $( "#datepicker" ).datepicker();      
    });
    </script>
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Users</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">User Name</td>
<td align="left" class="bc_input_td">
<input type="text" name="username" id="username" class="bc_input" value="<?php echo $bc_username; ?>"/>
</td>
</tr>
<?php if(!$frmID){ ?>
<tr>
<td align="right" class="bc_label">Password</td>
<td align="left" class="bc_input_td">

<input type="password" name="password" id="password" class="bc_input" value="<?php echo $bc_password; ?>" />
</td>
</tr>
<?php } ?>
<tr>
<td align="right" class="bc_label">Firstname</td>
<td align="left" class="bc_input_td">
<input type="text" name="firstname" id="firstname" class="bc_input" value="<?php echo $bc_firstname; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Lastname</td>
<td align="left" class="bc_input_td">
<input type="text" name="lastname" id="lastname" class="bc_input" value="<?php echo $bc_lastname; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">DOB</td>
<td align="left" class="bc_input_td">
<input type="text" name="dob" id="datepicker" class="bc_input" value="<?php echo $bc_dob; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Sex</td>
<td align="left" class="bc_input_td">
<input type="radio" name="sex" value="Male" <?php if($bc_sex == 'Male'){ echo 'checked="checked"'; }?>  /> Male  
<input type="radio" name="sex" value="Female" <?php if($bc_sex == 'Female'){ echo 'checked="checked"'; }?> /> Female  
</td>
</tr>

<tr>
<td align="right" class="bc_label">Address</td>
<td align="left" class="bc_input_td">
<input type="text" name="address" id="address" class="bc_input" value="<?php echo $bc_address; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">City</td>
<td align="left" class="bc_input_td">
<input type="text" name="city" id="city" class="bc_input" value="<?php echo $bc_city; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">State</td>
<td align="left" class="bc_input_td">
<select name="state" id="state" class="bc_input" >
<?php 
foreach($bc_arr_state as $key => $val)
{
	if ($key == $bc_state)
		$sel = "selected";
	else
		$sel = "";	
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo ucwords(strtolower($val)); ?> </option>
<?php } ?>
 </select>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Zip</td>
<td align="left" class="bc_input_td">
<input type="text" name="zip" id="zip" class="bc_input" value="<?php echo $bc_zip; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Phone</td>
<td align="left" class="bc_input_td">
<input type="text" name="phone" id="phone" class="bc_input" value="<?php echo $bc_phone; ?>"/>
</td>
</tr>
<?php if(!$frmID){ ?>
<tr>
<td align="right" class="bc_label">Created Date</td>
<td align="left" class="bc_input_td">
<input type="text" name="createddate" id="createddate" class="bc_input" value="<?php echo $bc_createddate; ?>"/>
</td>
</tr>
<?php  } ?>
<tr>
<td align="right" class="bc_label">Email</td>
<td align="left" class="bc_input_td">
<input type="text" name="email" id="email" class="bc_input" value="<?php echo $bc_email; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Status</td>
<td align="left" class="bc_input_td">
<input type="text" name="enabled" id="enabled" class="bc_input" value="<?php echo $bc_enabled; ?>"/>
</td>
</tr>

<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" value="Save" class="bc_button" />
</td>
</tr>
</table>
</form>

<?php 
require_once("footer.php"); 
?>

