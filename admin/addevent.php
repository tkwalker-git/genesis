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
$bc_event_image	=	$_FILES["event_image"]["name"];
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
	$errors[] = "Event Category: can not be empty";
if ($_POST["subcategory_id"] == "")
	$errors[] = "Event Subcategory: can not be empty";
if ($_POST["event_name"] == "")
	$errors[] = "Event Name: can not be empty";
if ($_POST["musicgenere_id"] == "")
	$errors[] = "Music Genere: can not be empty";
if ($_POST["event_start_time"] == "")
	$errors[] = "Event start time: can not be empty";
if ($_POST["event_end_time"] == "")
	$errors[] = "Event end time: can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		if ($_FILES["event_image"]["name"] != "") {
			$bc_event_image  = time() . "_" . $_FILES["event_image"]["name"] ;
			if ($action1 == "edit") 
				deleteImage($frmID,"events","event_image");
			move_uploaded_file($_FILES["event_image"]["tmp_name"], "../images/eventImages" .$bc_event_image);
			$bci_event_image = ',event_image = "' . $bc_event_image . '"';
		} else {
			$bci_event_image = "";
		}

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
			$sql	=	"update events set fb_event_id = '" . $bc_fb_event_id . "', userid = '" . $bc_userid . "', category_id = '" . $bc_category_id . "', subcategory_id = '" . $bc_subcategory_id . "', event_name = '" . $bc_event_name . "', musicgenere_id = '" . $bc_musicgenere_id . "', event_start_time = '" . $bc_event_start_time . "', event_end_time = '" . $bc_event_end_time . "', event_start_am_time = '" . $bc_event_start_am_time . "', event_end_am_time = '" . $bc_event_end_am_time . "', event_description = '" . $bc_event_description . "', event_cost = '" . $bc_event_cost . "', " . $bci_event_image . "', event_sell_ticket = '" . $bc_event_sell_ticket . "', event_age_suitab = '" . $bc_event_age_suitab . "', event_status = '" . $bc_event_status . "', publishdate = '" . $bc_publishdate . "', averagerating = '" . $bc_averagerating . "', modify_date = '" . $bc_modify_date . "', del_status = '" . $bc_del_status . "', added_by = '" . $bc_added_by . "' where id=$frmID";
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

 <script>
/*get subcategries*/
function get_subcategory(cat_id,subcatid)
{

	
	var parameters='';	
	if(subcatid!='' && typeof(subcatid)!='undefined') parameters= parameters+"&subcatid="+subcatid;

	callAjax("subcatmsg", "subcat.php", {
	params:"catid="+cat_id+"&"+parameters+"&rand="+Math.random(),
	meth:"post",
	async:true,
    startfunc:"showLoading('subcatmsg');",
    endfunc:"",
    errorfunc:"" }
	);

}
/*end*/
</script>


<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
<tr class="bc_heading">
<td colspan="2" align="left" >Add Events</td>
 </tr>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>

<tr>
<td align="right" class="bc_label">Event Name:</td>
<td align="left" class="bc_input_td">
<input type="text" name="event_name" id="event_name" class="bc_input" value="<?php echo $bc_event_name; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Event Category:</td>
<td align="left" class="bc_input_td">

<select class="evSel" name="category_id" id="category_id"  onchange="get_subcategory(this.value,'');" style="width:148px;">
							<option value="">Select  Category</option>
							<?php $sqlParent = "SELECT name,id FROM categories ";
							$resParent = mysql_query($sqlParent);
							$totalRows=mysql_num_rows($resParent);
							while($rowParent = mysql_fetch_array($resParent))
							{	
							?>
							<option value="<?=$rowParent['id']?>"<?php if($rowParent['id']==$rowEvent['id'])
							{ echo 'selected'; }?>><?=$rowParent['name']?></option>
						   <?php } ?>
						   	</select>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Event Subcategory:</td>
<td align="left" class="bc_input_td">
<div class="evLabal" id="subcatmsg">

							<select name="subcategory_id" id="subcategory_id"  class="evSel">
							<option value="" >Select Sub Category</option>
							</select>
							</div>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Music Genere:</td>
<td align="left" class="bc_input_td">
<select  name="musicgenere_id" id="musicgenere_id" style="width:148px;">

							   <option value="">Select MusicGenere</option>
							<?php $sqlAge = "SELECT name,id FROM music";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	

							?>
							<option value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$_POST['musicgenere_id'])
							{ echo 'selected'; }?>><?=$rowAge['name']?></option>
							<?php } ?>
							  </select>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Event start time:</td>
<td align="left" class="bc_input_td"><select class="evSel" name="event_start_time" id="event_start_time" style="width:90px;">
								<option value="">select</option>

								<option value="12:00 AM">12:00 AM</option>
								<option value="12:30 AM">12:30 AM</option>
								<option value="01:00 AM">01:00 AM</option>
								<option value="01:30 AM">01:30 AM</option>
								<option value="02:00 AM">02:00 AM</option>
								<option value="02:30 AM">02:30 AM</option>

								<option value="03:00 AM">03:00 AM</option>
								<option value="03:30 AM">03:30 AM</option>
								<option value="04:00 AM">04:00 AM</option>
								<option value="04:30 AM">04:30 AM</option>
								<option value="05:00 AM">05:00 AM</option>
								<option value="05:30 AM">05:30 AM</option>

								<option value="06:00 AM">06:00 AM</option>
								<option value="06:30 AM">06:30 AM</option>
								<option value="07:00 AM">07:00 AM</option>
								<option value="07:30 AM">07:30 AM</option>
								<option value="08:00 AM">08:00 AM</option>
								<option value="08:30 AM">08:30 AM</option>

								<option value="09:00 AM">09:00 AM</option>
								<option value="09:30 AM">09:30 AM</option>
								<option value="10:00 AM">10:00 AM</option>
								<option value="10:30 AM">10:30 AM</option>
								<option value="11:00 AM">11:00 AM</option>
								<option value="11:30 AM">11:30 AM</option>

								<option value="12:00 PM">12:00 PM</option>
								<option value="12:30 PM">12:30 PM</option>
								<option value="01:00 PM">01:00 PM</option>
								<option value="01:30 PM">01:30 PM</option>
								<option value="02:00 PM">02:00 PM</option>
								<option value="02:30 PM">02:30 PM</option>

								<option value="03:00 PM">03:00 PM</option>
								<option value="03:30 PM">03:30 PM</option>
								<option value="04:00 PM">04:00 PM</option>
								<option value="04:30 PM">04:30 PM</option>
								<option value="05:00 PM">05:00 PM</option>
								<option value="05:30 PM">05:30 PM</option>

								<option value="06:00 PM">06:00 PM</option>
								<option value="06:30 PM">06:30 PM</option>
								<option value="07:00 PM">07:00 PM</option>
								<option value="07:30 PM">07:30 PM</option>
								<option value="08:00 PM">08:00 PM</option>
								<option value="08:30 PM">08:30 PM</option>

								<option value="09:00 PM">09:00 PM</option>
								<option value="09:30 PM">09:30 PM</option>
								<option value="10:00 PM">10:00 PM</option>
								<option value="10:30 PM">10:30 PM</option>
								<option value="11:00 PM">11:00 PM</option>
								<option value="11:30 PM">11:30 PM</option>

							</select>
					


</td>
</tr>

<tr>
<td align="right" class="bc_label">Event end time:</td>
<td align="left" class="bc_input_td">
<select  name="event_end_time" id="event_end_time" style="width:90px;">
								<option value="">select</option>
								<option value="12:00 AM">12:00 AM</option>
								<option value="12:30 AM">12:30 AM</option>
								<option value="01:00 AM">01:00 AM</option>

								<option value="01:30 AM">01:30 AM</option>
								<option value="02:00 AM">02:00 AM</option>
								<option value="02:30 AM">02:30 AM</option>
								<option value="03:00 AM">03:00 AM</option>
								<option value="03:30 AM">03:30 AM</option>
								<option value="04:00 AM">04:00 AM</option>

								<option value="04:30 AM">04:30 AM</option>
								<option value="05:00 AM">05:00 AM</option>
								<option value="05:30 AM">05:30 AM</option>
								<option value="06:00 AM">06:00 AM</option>
								<option value="06:30 AM">06:30 AM</option>
								<option value="07:00 AM">07:00 AM</option>

								<option value="07:30 AM">07:30 AM</option>
								<option value="08:00 AM">08:00 AM</option>
								<option value="08:30 AM">08:30 AM</option>
								<option value="09:00 AM">09:00 AM</option>
								<option value="09:30 AM">09:30 AM</option>
								<option value="10:00 AM">10:00 AM</option>

								<option value="10:30 AM">10:30 AM</option>
								<option value="11:00 AM">11:00 AM</option>
								<option value="11:30 AM">11:30 AM</option>
								<option value="12:00 PM">12:00 PM</option>
								<option value="12:30 PM">12:30 PM</option>
								<option value="01:00 PM">01:00 PM</option>

								<option value="01:30 PM">01:30 PM</option>
								<option value="02:00 PM">02:00 PM</option>
								<option value="02:30 PM">02:30 PM</option>
								<option value="03:00 PM">03:00 PM</option>
								<option value="03:30 PM">03:30 PM</option>
								<option value="04:00 PM">04:00 PM</option>

								<option value="04:30 PM">04:30 PM</option>
								<option value="05:00 PM">05:00 PM</option>
								<option value="05:30 PM">05:30 PM</option>
								<option value="06:00 PM">06:00 PM</option>
								<option value="06:30 PM">06:30 PM</option>
								<option value="07:00 PM">07:00 PM</option>

								<option value="07:30 PM">07:30 PM</option>
								<option value="08:00 PM">08:00 PM</option>
								<option value="08:30 PM">08:30 PM</option>
								<option value="09:00 PM">09:00 PM</option>
								<option value="09:30 PM">09:30 PM</option>
								<option value="10:00 PM">10:00 PM</option>

								<option value="10:30 PM">10:30 PM</option>
								<option value="11:00 PM">11:00 PM</option>
								<option value="11:30 PM">11:30 PM</option>
							</select>
							


</td>
</tr>


<tr>
<td align="right" class="bc_label">Event description:</td>
<td align="left" class="bc_input_td">
<textarea  name="event_description" id="event_description" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_event_description; ?></textarea>

</td>
</tr>

<tr>
<td align="right" class="bc_label">Event cost:</td>
<td align="left" class="bc_input_td">
$<input type="text" name="event_cost" id="event_cost" class="bc_input" value="<?php echo $bc_event_cost; ?>"/>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Event image:</td>
<td align="left" class="bc_input_td">
<?php
 if( $bc_event_image != '' ) { 
	echo '<img src=""../images/eventImages"'. $bc_event_image .'" class="dynamicImg" id="delImg_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="events|event_image|'. $bc_event_image .'|"../images/eventImages"" />';
} else {
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
}
?>
<input type="file" name="event_image" id="event_image" /><br>
<?=$image_del?>
</td>
</tr>

<tr>
<td align="right" class="bc_label">Event sell ticket:</td>
<td align="left" class="bc_input_td">
<select name="event_sell_ticket" id="event_sell_ticket" class="bc_input" >
<?php 
$bc_arr_event_sell_ticket = array("Yes"=>"Yes", "No"=>"No"); 
foreach($bc_arr_event_sell_ticket as $key => $val)
{
	if ($key == $bc_event_sell_ticket)
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
<td align="right" class="bc_label">Event age suitability:</td>
<td align="left" class="bc_input_td">

<select class="evSel" name="event_age_suitab" id="event_age_suitab">
								<option value="">-Select age-</option>
								<?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
							<option value="<?=$rowAge['id']?>" <?php if($rowAge['id']==$rowEvent['event_age_suitab'])
							{ echo 'selected'; }?>><?=$rowAge['name']?></option>
							<?php } ?>
							</select></td>
</tr>


<tr>
<td align="right" class="bc_label">Event Dates:</td>
<td align="left" class="bc_input_td">
<input type="text" name="event_date" id="event_date" class="bc_input" value="<?php echo $bc_event_date; ?>"/>
</td>
</tr>


<tr>
<td>&nbsp;</td><td align="left">
<input name="submit" type="submit" id="Subcategory" value="Save" class="bc_button" />
</td>
</tr>
</table>
</form>

<?php 
//require_once("footer.php"); 
?>
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "exact",
	elements : "event_description",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,forecolor,backcolor,bullist,numlist,outdent,indent,blockquote,anchor,cleanup",
	theme_advanced_buttons2 : "cut,copy,paste,styleselect,formatselect,fontselect,fontsizeselect,hr,code,image",
	theme_advanced_font_sizes: "10px,11px,12px,13px,14px,15px,16px,17,18px,19px,20px,22px,24px,26px,28px,30px,36px",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	remove_script_host : false,
    convert_urls : false,
	content_css : "site_styles.css?1",
	plugins : 'inlinepopups,imagemanager',
});


function showSubCategoryVal()
{
var ss= document.getElementById('subcategory_id');
	var len= ss.options.length;
	var str="";
	var j=0;
	for(var i=0;i<len;i++){		
		if(ss.options[i].selected == true)
		{			
		if(j == 0)
			str= ss.options[i].value;
		else
			str += ss.options[i].value;
		j++;
		}
	}
  document.getElementById('SubCategory').value=str;  
  }



</script>


