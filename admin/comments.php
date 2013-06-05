<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_name	=	$_POST["name"];
$bc_email	=	$_POST["email"];
$bc_message	=	$_POST["message"];
$bc_section	=	"Blog";
$bc_post_id	=	$_POST["post_id"];
$bc_status	=	$_POST["status"];

$bc_date_posted	=	date("Y-m-d H:i:s");

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["name"] == "")
	$errors[] = "Name: can not be empty";
if ($_POST["email"] == "")
	$errors[] = "Email: can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into comments (name,email,message,section,post_id,status,date_posted) values ('" . $bc_name . "','" . $bc_email . "','" . $bc_message . "','" . $bc_section . "','" . $bc_post_id . "','" . $bc_status . "','" . $bc_date_posted . "')";
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
			$sql	=	"update comments set name = '" . $bc_name . "', email = '" . $bc_email . "', message = '" . $bc_message . "', section = '" . $bc_section . "', post_id = '" . $bc_post_id . "', status = '" . $bc_status . "', date_posted = '" . $bc_date_posted . "' where id=$frmID";
			$res	=	mysql_query($sql);
			if ($res) {
				
				if( $bc_status == 1 ) {
					
					$article_name = attribValue("blogs", "title",    "where id='$bc_post_id' limit 1");
					$article_seo  = attribValue("blogs", "seo_name", "where id='$bc_post_id' limit 1");
					$toFrom = attribValue("bc_admin", "email", "where id=1 limit 1");
					$toSubject = 'New Blog comment is posted';
					
					$toBody  = 'A new comment is posted for the Blog: <a href="'.ABSOLUTE_PATH.'blog/'.$article_seo.'.html">'.$article_name.'</a><br /><br />';
					$toBody .= '<strong>Comment:</strong> '.$bc_message.'<br /><br />';
					$toBody .= 'Regards!<br />';
					$toBody .= 'infospace Team';
					
					$q_mail = mysql_query("select name,email from comments where post_id='$bc_post_id' and updates_email='1' order by id asc");
					while( $r_mail = mysql_fetch_assoc($q_mail) ) {
						$name = $r_mail['name'];
						$toMail = $r_mail['email'];
						$toName  = 'Hi, '.$name.'<br />' . $toBody;
						
						sendMail($toMail, $toFrom, $toSubject, $toName);
					}
				}
				
				
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
$sql	=	"select * from comments where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_name	=	$row["name"];
		$bc_email	=	$row["email"];
		$bc_message	=	$row["message"];
		$bc_section	=	$row["section"];
		$bc_post_id	=	$row["post_id"];
		$bc_status	=	$row["status"];
		$bc_date_posted	=	date("M d, Y H:i:s", strtotime($row["date_posted"]));
	} 
	$action = "edit";
} 

?>
<script language="JavaScript" type="text/javascript" src="js/cal2.js"></script>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr  class="bc_heading">
<td colspan="2" align="left">Vistor Comments</td>
 </tr>
<tr><td colspan="2" align="center" >&nbsp;</td></tr>

<?php if ($sucMessage) { ?>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<?php } ?>
<tr>
<td width="17%" align="right" class="bc_label">Name:</td>
<td width="83%" align="left" class="bc_label"><input type="text" name="name" id="name" class="bc_input" value="<?php echo $bc_name; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">Email:</td>
<td align="left" class="bc_label"><input type="text" name="email" id="email" class="bc_input" value="<?php echo $bc_email; ?>"/></td>
</tr>

<tr>
<td align="right" class="bc_label">Message:</td>
<td align="left" class="bc_label">
<textarea  name="message" id="message" class="bc_input" style="width:550px;height:100px;" /><?php echo $bc_message; ?></textarea>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Blog Post:</td>
<td align="left" class="bc_label">
<select name="post_id"  style="width:150px;" >
<?php	 echo getDropDown("blogs", $bc_post_id,"title");	?>
</select>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Status:</td>
<td align="left" class="bc_label">
	<select name="status">
	<?php
	if($bc_status == "1")
		$yes = "selected";
	else
		$no = "selected";
	?>
	<option value="1" <?=$yes?> >Yes</option>
	<option value="0" <?=$no?> >No</option>
	</select>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Date Posted:</td>
<td align="left" class="bc_label">
<?php	 echo $bc_date_posted;	?>


</td>
</tr>

<tr>
<td colspan="2" align="center">
<input name="submit" type="submit" value="Save" class="bc_button <?php echo $saved_class;?>" /></td>
</tr>
</table>
</form>

<?php 
require_once("footer.php"); 
?>