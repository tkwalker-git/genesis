<br />
<font size='1'><table class='xdebug-error' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Notice: Undefined variable: form in C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php on line <i>46</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0157</td><td bgcolor='#eeeeec' align='right'>534832</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php' bgcolor='#eeeeec'>..\generateFormNew.php<b>:</b>0</td></tr>
</table></font>
<br />
<font size='1'><table class='xdebug-error' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Notice: Undefined variable: dbDnading in C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php on line <i>212</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0157</td><td bgcolor='#eeeeec' align='right'>534832</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php' bgcolor='#eeeeec'>..\generateFormNew.php<b>:</b>0</td></tr>
</table></font>
<br />
<font size='1'><table class='xdebug-error' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Notice: Undefined variable: fileUpd in C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php on line <i>220</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0157</td><td bgcolor='#eeeeec' align='right'>534832</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php' bgcolor='#eeeeec'>..\generateFormNew.php<b>:</b>0</td></tr>
</table></font>
<br />
<font size='1'><table class='xdebug-error' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Notice: Undefined variable: filecontents in C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php on line <i>281</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0157</td><td bgcolor='#eeeeec' align='right'>534832</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp\www\eventgrabber\granite\admin\generateFormNew.php' bgcolor='#eeeeec'>..\generateFormNew.php<b>:</b>0</td></tr>
</table></font>
<?php

require_once("database.php"); 
require_once("header.php"); 

$bc_fb_event_id	=	$_POST["fb_event_id"];
$bc_userid	=	$_POST["userid"];
$bc_category_id	=	$_POST["category_id"];
$bc_subcategory_id	=	$_POST["subcategory_id"];
$bc_event_name	=	$_POST["event_name"];
$bc_musicgenere_id	=	$_POST["musicgenere_id"];
$bc_event_start_time	=	$_POST["event_start_time"];
$bc_event_end_time	=	$_POST["event_end_time"];
$bc_event_start_am_time	=	$_POST["event_start_am_time"];
$bc_event_end_am_time	=	$_POST["event_end_am_time"];
$bc_event_description	=	$_POST["event_description"];
$bc_event_cost	=	$_POST["event_cost"];
$bc_event_image	=	$_POST["event_image"];
$bc_event_sell_ticket	=	$_POST["event_sell_ticket"];
$bc_event_age_suitab	=	$_POST["event_age_suitab"];
$bc_event_status	=	$_POST["event_status"];
$bc_publishdate	=	$_POST["publishdate"];
$bc_averagerating	=	$_POST["averagerating"];
$bc_modify_date	=	$_POST["modify_date"];
$bc_del_status	=	$_POST["del_status"];
$bc_added_by	=	$_POST["added_by"];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["category_id"] == "")
	$errors[] = "Category_id: can not be empty";
if ($_POST["subcategory_id"] == "")
	$errors[] = "Subcategory_id: can not be empty";
if ($_POST["event_name"] == "")
	$errors[] = "Event_name: can not be empty";
if ($_POST["musicgenere_id"] == "")
	$errors[] = "Musicgenere_id: can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into events (fb_event_id,userid,category_id,subcategory_id,event_name,musicgenere_id,event_start_time,event_end_time,event_start_am_time,event_end_am_time,event_description,event_cost,event_image,event_sell_ticket,event_age_suitab,event_status,publishdate,averagerating,modify_date,del_status,added_by) values ('" . $bc_fb_event_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_musicgenere_id . "','" . $bc_event_start_time . "','" . $bc_event_end_time . "','" . $bc_event_start_am_time . "','" . $bc_event_end_am_time . "','" . $bc_event_description . "','" . $bc_event_cost . "','" . $bc_event_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update events set fb_event_id = '" . $bc_fb_event_id . "', userid = '" . $bc_userid . "', category_id = '" . $bc_category_id . "', subcategory_id = '" . $bc_subcategory_id . "', event_name = '" . $bc_event_name . "', musicgenere_id = '" . $bc_musicgenere_id . "', event_start_time = '" . $bc_event_start_time . "', event_end_time = '" . $bc_event_end_time . "', event_start_am_time = '" . $bc_event_start_am_time . "', event_end_am_time = '" . $bc_event_end_am_time . "', event_description = '" . $bc_event_description . "', event_cost = '" . $bc_event_cost . "', event_image = '" . $bc_event_image . "', event_sell_ticket = '" . $bc_event_sell_ticket . "', event_age_suitab = '" . $bc_event_age_suitab . "', event_status = '" . $bc_event_status . "', publishdate = '" . $bc_publishdate . "', averagerating = '" . $bc_averagerating . "', modify_date = '" . $bc_modify_date . "', del_status = '" . $bc_del_status . "', added_by = '" . $bc_added_by . "' where id=$frmID";
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
$sql	=	"select * from events where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_fb_event_id	=	$row["fb_event_id"];
		$bc_userid	=	$row["userid"];
		$bc_category_id	=	$row["category_id"];
		$bc_subcategory_id	=	$row["subcategory_id"];
		$bc_event_name	=	$row["event_name"];
		$bc_musicgenere_id	=	$row["musicgenere_id"];
		$bc_event_start_time	=	$row["event_start_time"];
		$bc_event_end_time	=	$row["event_end_time"];
		$bc_event_start_am_time	=	$row["event_start_am_time"];
		$bc_event_end_am_time	=	$row["event_end_am_time"];
		$bc_event_description	=	$row["event_description"];
		$bc_event_cost	=	$row["event_cost"];
		$bc_event_image	=	$row["event_image"];
		$bc_event_sell_ticket	=	$row["event_sell_ticket"];
		$bc_event_age_suitab	=	$row["event_age_suitab"];
		$bc_event_status	=	$row["event_status"];
		$bc_publishdate	=	$row["publishdate"];
		$bc_averagerating	=	$row["averagerating"];
		$bc_modify_date	=	$row["modify_date"];
		$bc_del_status	=	$row["del_status"];
		$bc_added_by	=	$row["added_by"];
	} // end if row
	$action = "edit";
} // end if 

?>
<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" ></td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<tr>
<td align="right" class="bc_label">Fb_event_id:</td>
<td align="left" class="bc_input_td">
<input type="text" name="fb_event_id" id="fb_event_id" class="bc_input" value="<?php echo $bc_fb_event_id; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Userid:</td>
<td align="left" class="bc_input_td">
<input type="text" name="userid" id="userid" class="bc_input" value="<?php echo $bc_userid; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Category_id:</td>
<td align="left" class="bc_input_td">
<input type="text" name="category_id" id="category_id" class="bc_input" value="<?php echo $bc_category_id; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Subcategory_id:</td>
<td align="left" class="bc_input_td">
<input type="text" name="subcategory_id" id="subcategory_id" class="bc_input" value="<?php echo $bc_subcategory_id; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Event_name:</td>
<td align