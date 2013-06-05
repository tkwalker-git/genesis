<?php
require_once 'database.php';
require_once("header.php");

if (isset($_GET['message']))
	$msg = $_GET['message'];
else
	$msg = "";

$sql = "select * from bc_admin where user_name='". $_SESSION['admin_user'] ."'";
$res = mysql_query($sql);
if (mysql_num_rows($res) > 0) {
	$rows = mysql_fetch_assoc($res);
	$pass = $rows['password'];
}

if (isset($_POST["submit"]) ) {
	if ( $pass ==  $_POST['cpass'] ) {
		if ($_POST['txtNewPass'] != '' || $_POST['txtPass3'] != '' ) {
			if ($_POST['txtNewPass'] == $_POST['txtPass3'] ) {
				$password = $_POST['txtNewPass'];
		
				$sql = "update bc_admin set password='". $password. "' WHERE user_name = '". $_SESSION['admin_user'] ."'";
				$res = mysql_query($sql);			
				if ($res)
					$message = "Password Changed";
				else
					$message = "Can't Edit Password";
			} else {
				$message = "Confirm Password does not match";
			}
		} else {
			$message = "Password Must Not Empty";
		}
	} else {
		$message = "Current Password does not match.";
	}	
}


?>

<form id="form1" name="form1" method="post" action="" >

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
    <tr class="bc_heading"><td colspan="2">Change Password </td></tr>
	
	<?php if ( $message != '' ) { ?>

	<tr>
	<td colspan="2" align="center" class="success" ><?php echo $message; ?></td>
	</tr>
	<?php } ?>
	
    <tr><td height="40" align="center" >&nbsp;</td></tr>
    <tr>
        <td width="25%"  align="right" valign="middle" class="bc_label">User Name: </td>
        <td width="75%"  align="left" valign="middle" class="text"> <input type="text" name="txtUser" value="<?=$_SESSION['admin_user']?>" disabled class="bc_input" align="left"/></td>
    </tr>
    <tr><td colspan="2" height="5"></td></tr>
    <tr>
        <td height="21" align="right" class="bc_label">Current Password:</td>
        <td class="text" align="left"> <input type="password" name="cpass" id="cpass" class="bc_input" /></td>
    </tr>
	 <tr><td colspan="2" height="5"></td></tr>
	<tr>
        <td height="21" align="right" class="bc_label">New Password:</td>
        <td class="text" align="left"> <input type="password" name="txtNewPass" id="txtNewPass" class="bc_input" /></td>
    </tr>
    <tr><td colspan="2" height="5"></td></tr>
    <tr>
        <td height="25" align="right" class="bc_label">Confirm Password:</td>
        <td class="text" align="left"> <input type="password" name="txtPass3" id="txtPass3" class="bc_input"/></td>
    </tr>
    <tr><td colspan="2" height="5"></td></tr>
    <tr>
        <td class="text">&nbsp;</td>
        <td class="text">
		<input name="submit" type="submit" value="Update" class="bc_button <?php echo $saved_class;?>" />
	</td>
    </tr>
    
</table>

</form>

<?php

require_once("footer.php"); 

?>