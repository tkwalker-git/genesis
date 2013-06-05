<?php
require_once('admin/database.php');
require_once('site_functions.php');
$page = $_GET['page'];
$bc_patient_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
$check = getSingleColumn("type","select type from pt_inr_setting where patient_id='".$bc_patient_id."'");
if($check)
	$type = $check;
else
	$type = $_POST['issue'];
if($_POST['save']=='Save'){
		$type = $_POST['issue'];
		$page = $_POST['page'];
		if($check){
			mysql_query("update pt_inr_setting set type='".$type."' where patient_id='".$bc_patient_id."' ") or die(mysql_error());
			echo '<script>
					window.location.href = "'.$page.'";
				</script>';
		}
		else{
			mysql_query("INSERT INTO pt_inr_setting set type='".$type."' , patient_id='".$bc_patient_id."' ") or die(mysql_error());;
			echo '<script>
					window.location.href = "'.$page.'";
				</script>';
		}
	}
if($type=='1')
	$selected_one = 'checked="checked"';
else
	$selected_one = ' ';
if($type=='2')
	$selected_two = 'checked="checked"';
else
	$selected_two = ' ';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery.ui.datepicker.js"></script>
<link rel="stylesheet" media="all" type="text/css" href="<?php echo ABSOLUTE_PATH;?>js/jquery-ui-timepicker.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-ui-timepicker.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/new_modules.js"></script>
<style type="text/css">
/* Overlay */
	#simplemodal-overlay {
		background-color:#000;
		cursor:wait;
	}
	/* Container */
	#simplemo {
		color:#000;
		-moz-border-radius:10px;
		border-radius:10px;
		-webkit-border-radius:10px;
		-moz-box-shadow:0 1px 3px #777;
		-webkit-box-shadow:0 2px 3px #777;
		box-shadow:0 2px 3px #777;
		background:#FFF;
		padding:12px;
		behavior: url(http://www.eventgrabber.com/css/PIE.htc);
	}

	#simplemodal-data {
		padding:30px;;
	}
	#simplemo code {
		background:#141414;
		border-left:3px solid #65B43D;
		color:#bbb;
		display:block;
		font-size:12px;
		margin-bottom:12px;
		padding:4px 6px 6px;
	}
	#simplemo a {
		color:#88A906;
		text-decoration:none;
		font-size:13px;
	}
	#close {
		width:25px;
		height:29px;
		display:inline;
		z-index:3200;
		position:absolute;
		top:-15px;
		right:-16px;
		cursor:pointer;
	}
#simplemo h1 {
		margin: 0;
		padding: 10px;
		text-align:left;
		color:#88A906;
}
#simplemo p {
		margin: 0;
		padding: 10px;
		text-align:left
	}

.border{
	border:1px solid #F5F5F5; !important;
	
	}
.out_div{
	left: -50px;
    height: 190px;
    width: 520px;
    position: absolute;
    top: 120px
	}
.button_save {
 	background: none repeat scroll 0 0 #ED711F;
    border: 1px #ED711F solid;
    font-weight: bold;
    height: 30px;
    width: 70px;
	border-radius:5px;
	font-size:12px;
	cursor:pointer;
	color:#FFF;
	
}
</style>
</head>
<body>
<div id="ds" >
<div id="simplemo" class="simplemo border out_div">
<div id="basic-modal-content" class="basic-modal-content">
<div id="close"><img src="images/x.png" onClick="hide()" /></div>
<form  class="savemeta" action="pt_inr_type.php" method="post">
<h1>Set Your Type</h1>
<p>Indicating your type of diabetes will help us create accurate monthly reports you can share with your doctor.</p>
<table cellpadding="2" cellspacing="2" border="0" style="float:left;padding-left:10px;">
    <tr>
        <td>Type of PT/INR:</td>
        <td valign="top"><input id="issue_1" type="radio" <?php echo $selected_one; ?> value="1" name="issue"></td><td>Type 1</td>
        <td valign="top"><input id="issue_2" type="radio" value="2"  <?php echo $selected_two; ?> name="issue"></td><td>Type 2</td>
     </tr>
     <tr><td colspan="5">&nbsp;</td></tr>
     <tr>
     <td colspan="5"><input  type="submit" class="button_save" name="save" value="Save">&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onClick="hide()" class="print_edit">Cancel</a></td>
     </tr>
   </table>
   <input type="hidden" value="<?php echo $page;?>" name="page" />
</form>
</div></div></div>
</body>
</html>