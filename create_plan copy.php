<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

if($_GET["id"]){	
	if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'plan',$_GET["id"]) =='false'){
		echo "<script>window.location.href='clinic_manager.php?p=plans';</script>";
		}
}
	


$bc_clinic_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
$bc_patient_id		=	$_POST["patient_id"];
$bc_plan_name		=	$_POST["plan_name"];
$bc_plan_detail		=	$_POST["plan_detail"];
$bc_tests			=	$_POST["tests"];
$bc_supplements		=	$_POST["supplements"];
$bc_start_date		=	$_POST["start_date"];
$bc_end_date		=	$_POST["end_date"];
$bc_protocol		=	$_POST["protocol"];
$bc_plan_date		=	date("Y-m-d");


	$frmID	=	$_GET["id"];
	

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($bc_clinic_id == "")
	$errors[] = "clinic id can not be empty";
if ($_POST["patient_id"] == "")
	$errors[] = "patient id can not be empty";
if ($_POST["plan_name"] == "")
	$errors[] = "plan name can not be empty";
if ($_POST["plan_detail"] == "")
	$errors[] = "plan detail can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into plan (clinic_id,patient_id,plan_name,plan_detail,plan_date) values ('" . $bc_clinic_id . "','" . $bc_patient_id . "','" . $bc_plan_name . "','" . $bc_plan_detail . "','" . $bc_plan_date . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			
			if($bc_tests){
			if(is_array($bc_tests)){
				foreach($bc_tests as $bc_test_id){
					mysql_query("INSERT INTO `plan_test` (`id`, `test_id`, `patient_id`,`plan_id`) VALUES (NULL, '". $bc_test_id ."', '". $bc_patient_id ."', '". $frmID ."');");
				}
			}
		}
		
		if (is_array($bc_protocol) ) {

			for($f=0;$f <count($bc_protocol);$f++) {
				$bc_protocola =	$bc_protocol[$f];
				$start_date	=	date('Y-m-d',strtotime($bc_start_date[$f])); 
				$end_date	=	date('Y-m-d',strtotime($bc_end_date[$f]));
				
					if($bc_protocola!=''){
						mysql_query("INSERT INTO `plan_protocol` (`id`, `plan_id`, `patient_id`, `protocol_id`,`start_date`,`end_date`) VALUES (NULL, '$frmID', '$bc_patient_id', '$bc_protocola','$start_date','$end_date')");
					}
				}
			}


		if($bc_supplements){
			if(is_array($bc_supplements)){
				foreach($bc_supplements as $bc_supplement_id){
					mysql_query("INSERT INTO `plan_supplement` (`id`, `supplement_id`, `patient_id`,`plan_id`) VALUES (NULL, '". $bc_supplement_id ."', '". $bc_patient_id ."', '". $frmID ."');");
				}
			}
		}
			
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update plan set clinic_id = '" . $bc_clinic_id . "', patient_id = '" . $bc_patient_id . "', plan_name = '" . $bc_plan_name . "', plan_detail = '" . $bc_plan_detail . "', plan_date = '" . $bc_plan_date . "' where id=$frmID";
			$res	=	mysql_query($sql);
			
			
			if($bc_tests){
				
			if(is_array($bc_tests)){
			mysql_query("delete from `plan_test` where plan_id='$frmID'");
				foreach($bc_tests as $bc_test_id){
					mysql_query("INSERT INTO `plan_test` (`id`, `test_id`, `patient_id`,`plan_id`) VALUES (NULL, '". $bc_test_id ."', '". $bc_patient_id ."', '". $frmID ."');");
				}
			}
		}
		
		if (is_array($bc_protocol) ) {
		
			mysql_query("delete from `plan_protocol` where plan_id='$frmID'");

			for($f=0;$f <count($bc_protocol);$f++) {
				$bc_protocola =	$bc_protocol[$f];
				$start_date	=	date('Y-m-d',strtotime($bc_start_date[$f])); 
				$end_date	=	date('Y-m-d',strtotime($bc_end_date[$f]));
				
					if($bc_protocola!=''){
						mysql_query("INSERT INTO `plan_protocol` (`id`, `plan_id`, `patient_id`, `protocol_id`,`start_date`,`end_date`) VALUES (NULL, '$frmID', '$bc_patient_id', '$bc_protocola','$start_date','$end_date')");
					}
				}
			}


		if($bc_supplements){
			
			if(is_array($bc_supplements)){
			mysql_query("delete from `plan_supplement` where plan_id='$frmID'");
				foreach($bc_supplements as $bc_supplement_id){
					mysql_query("INSERT INTO `plan_supplement` (`id`, `supplement_id`, `patient_id`,`plan_id`) VALUES (NULL, '". $bc_supplement_id ."', '". $bc_patient_id ."', '". $frmID ."');");
				}
			}
		}
		
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


$sql	=	"select * from `plan` where `id`=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		 $bc_clinic_id		=	$row["clinic_id"];
		 $bc_patient_id		=	$row["patient_id"];
		 $bc_plan_name		=	$row["plan_name"];
		 $bc_plan_detail	=	$row["plan_detail"];
		 $bc_plan_date		=	$row["plan_date"];
	} // end if row
	$action = "edit";
} // end if

 $sql = "select * from `patients` where `id`='$bc_patient_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_patient_address		=	$ro['address'];
	$bc_patient_name		=	$ro['username'];
	$bc_patient_city		=	$ro['city'];
	 $bc_patient_zip		=	$ro['zip'];
	}
	
$sql = "select * from `plan_test` where `plan_id`='$frmID'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_tests[]		=	$ro['test_id'];

	}
	$sql = "select * from `plan_supplement` where `plan_id`='$frmID'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_supplements[]		=	$ro['supplement_id'];
	}
	
	$bc_protocol		=	array();
	$bc_start_date		=	array();
	$bc_end_date		=	array();
	
	$sql = "select * from `plan_protocol` where `plan_id`='$frmID'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_protocol[]			=	$ro['protocol_id'];
	$bc_start_date[]		=	date('m/d/Y',strtotime($ro['start_date']));
	$bc_end_date[]			=	date('m/d/Y',strtotime($ro['end_date']));

	}



$meta_title	= "Create Plan";

include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />

<script>
    $(document).ready(function() {

        $('.savedate').datepicker();
		     
    });
    </script>
	

<script src="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_default.js?0" type="text/javascript"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yuiloader/yuiloader-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/event/event-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dom/dom-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/calendar/calendar-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dragdrop/dragdrop-min.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_event_edit.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />
<link href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_event_edit.css?0" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.accordion.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.tipsy.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.core.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.theme.css">
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.css">
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.js"></script>
<style>
.savedate {
background: none repeat scroll 0 0 #FFFFFF;
    color: #000000;
	 border: 1px solid #B8B8B8;
    border-radius: 5px 5px 5px 5px;
	font-size: 15px;
    padding: 5px 3px;
}
</style>
<script type="text/javascript">


function remove(value){
	$('#row'+value).remove();
	}






function add_more_protocol(id){
	
	var limitprotocols = 10;
	if(id!=limitprotocols){  
	var next_row 	= id+1;
	var new_url_feild = '<div id="proto'+next_row+'"><div style="float:left; width:240px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Start Date</div><input type="text" name="start_date[]" id="start_date'+next_row+'" class="start_date'+next_row+' savedate" style="width:200px;"></div><div style="float:left; width:240px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Select Protocol</div><select style="width:180px;" class="new_input" name="protocol[]" id="protocol"> <?php $res = mysql_query("select * from `protocols` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");while($row = mysql_fetch_array($res)){?><option value="<?php echo $row['id']; ?>" <?php if(is_array($bc_protocols) && in_array($row['id'],$bc_protocols)){ echo 'selected="selected"'; } ?>><?php echo $row['protocol_title']; ?></option><?php } ?></select></div><div style="float:left; width:250px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">End Date</div><input type="text" name="end_date[]" id="end_date'+next_row+'" class="end_date'+next_row+' savedate" style="width:200px;"><img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onclick="remove_protocol('+next_row+')"></div> <div class="clr"></div></div></div>';
	$('#add_more_protocol_area').append(new_url_feild);	
	$('#add_more_protocol_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_protocol('+next_row+')" />');
	}
	else{
	alert("You can not selcet more then "+limitprotocols+" Protocol");
	}
	$('.savedate').datepicker();

}




function remove_protocol(id){
	$('#proto'+id).remove();
	var a = $('#add_more_protocol_btn').html();

	var b = a.split('add_more_video(');
	b = b[1].split(')');
	c = b[0]-1;

	$('#add_more_protocol_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_protocol('+c+')" />');
	
}





function save(){
	$err	=	$('#check_errors').val();
if($err == 1){
$errText	=	$('#dErrors').val();
	alert($errText);
	$('.box').css('display','none');
	$('#box1').css('display','block');
return false;
}
	$("#z_listing_event_form").attr("action", "");
	$("#z_listing_event_form").submit();
} 

$(document).ready(function() {
			$("#patient_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>get_patient_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					if(data[0]){
					$("#patient_id").attr("value", data[0]);
					$('#patient_id').css('color', '#000');
					}
					if(data[2]){
					$("#pt_address1").attr("value", jQuery.trim(data[2]));
					$('#pt_address1').attr('readonly', 'readonly');
					$('#patient_id').css('color', '#000');
					}
					if(data[3]){
					$("#pt_city").attr("value", jQuery.trim(data[3]));
					$('#pt_city').attr('readonly', 'readonly');
					$('#patient_id').css('color', '#000');
					}
					if(data[4]){
					$("#pt_zip").attr("value", jQuery.trim(data[4]));
					$('#pt_zip').attr('readonly', 'readonly');
					$('#patient_id').css('color', '#000');
					}
				}
			}).setOptions({
				max: '100%'
		});
});




<?php if($_GET['t']){?>
$(document).ready(function(){
	$('.box').css('display','none');
	$('#box<?php echo $_GET["t"]; ?>').css('display','block');
});
<?php } ?>

</script>
<style>

.ev_title input{
	color: #808080;
	font-weight:normal;
	}

.new_ticket_right td{
	height:48px;
	padding:0 16px;
	}
	
.ev_new_box_center{
	margin:auto;
	width:936px;
	}
	
.ev_new_box_center .basic_box, .ev_new_box_center .featured_box, .ev_new_box_center .premium_box, .ev_new_box_center .custom_box{
	width:234px;
	height:528px;
	float:left;
	position:absolute
	}
	
	
.ev_new_box_center .basic_box ul, .ev_new_box_center .featured_box ul, .ev_new_box_center .premium_box ul, .ev_new_box_center .custom_box ul{
	padding:10px 0 0 18px;
	margin:0
}

.ev_new_box_center .basic_box ul li, .ev_new_box_center .featured_box ul li, .ev_new_box_center .premium_box ul li, .ev_new_box_center .custom_box ul li{
	list-style:circle;
	font-size:12px
}

.ev_new_box_center .basic_box{
	background:url(images/basic_box.gif) no-repeat;
	}

.ev_new_box_center .featured_box{
	background:url(images/featured_box.gif) no-repeat;
	left:234px;
	}
	
.ev_new_box_center .premium_box{
	background:url(images/premium_box.gif) no-repeat;
	left:468px;
	}
	
.ev_new_box_center .custom_box{
	background:url(images/custom_box.gif) no-repeat;
	left:702px;
	}
	
	
.ev_new_box_center .basic_box .black, .ev_new_box_center .featured_box .black, .ev_new_box_center .premium_box .black, .ev_new_box_center .custom_box .black{	
	filter:alpha(opacity=15);
	-ms-filter:alpha(opacity=15);
	-moz-opacity:0.15;
	opacity:0.15;
	background:#000000;
	width:234px;
	height:528px;
	position:absolute;
	}
	
	
.ev_new_box_center .black:hover{
	display:none;
	}
	
.ev_new_box_center .basic_box:hover > .black, .ev_new_box_center .featured_box:hover > .black, .ev_new_box_center .premium_box:hover > .black, .ev_new_box_center .custom_box:hover > .black{
	display:none;
	}
	
	
.ev_new_box_center .basic_box:hover, .ev_new_box_center .featured_box:hover, .ev_new_box_center .premium_box:hover, .ev_new_box_center .custom_box:hover{
	z-index:9999;
	-moz-box-shadow:0px 0px 7px 2px #464646;
	-webkit-box-shadow:0px 0px 7px 2px #464646;
	-khtml-box-shadow:0px 0px 7px 2px #464646;
	box-shadow:0px 0px 7px 2px #464646;
	filter: progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=0),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=90),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=180),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=270);
	}
	.ev_new_box_center .detail{
	padding:132px 10px 0;
	height:280px;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	line-height:18px;
}
	
#showimg1,#showimg3{
	padding: 5px 0 5px 20px;
	width: 45%
	}

#showimg2,#showimg4{
	padding: 5px 0 5px 27px;
	width:45%;
	}

</style>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">



function showTh(value,id){
	if(value == 'Other')
		$('#'+id).show();
	else
		$('#'+id).hide();
	}
</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Create Plan</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
		<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
        <?php include('dashboard_menu_tk.php'); ?>
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">
                <h3>STEP 1 | <span>ADD Protocols to plan</span></h3>
                <div id="box" class="box">
                  <div id="head">Plan Name </div>
                  <div class="ev_title">
                    <input type="text" name="plan_name" class="new_input" value="<?php if ($bc_plan_name){echo $bc_plan_name;}  ?>" id="plan_name" >
                  </div>
                  <div id="head">Patient Name</div>
                  <div>
                    <input type="text" name="patient_name" id="patient_name" class="new_input" value="<?php if ($bc_patient_name){ echo $bc_patient_name; }else{ echo "Start Typing Patient Name"; } ?>" onfocus="removeText(this.value,'Start Typing Patient Name','patient_name');" onblur="returnText('Start Typing Patient Name','patient_name');" style="margin-bottom:2px; width:200px" />
                    <input type="hidden" name="patient_id" id="patient_id" value="<?php if($bc_patient_id){echo $bc_patient_id;}else { echo $bc_arr_patient_id; }  ?>" />
                    &nbsp;&nbsp;
                    <input type="text" name="address1" disabled="disabled" id="pt_address1" class="new_input" value="<?php if ($bc_patient_address){ echo $bc_patient_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','pt_address1');" onBlur="returnText('Address','ev_address1');" style="width:200px">
                    &nbsp;&nbsp;
                    <input type="text" name="city" disabled="disabled" id="pt_city" class="new_input" value="<?php if ($bc_patient_city){ echo $bc_patient_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','pt_city');" onBlur="returnText('City','pt_city');" style="width:200px">
                    &nbsp;&nbsp;
                    <input type="text" name="zip" id="pt_zip" disabled="disabled" class="new_input" value="<?php if ($bc_patient_zip){ echo $bc_patient_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','pt_zip');" onBlur="returnText('Zip / Postal Code','pt_zip');" style="width:190px">
                    <br>
                    <div class="clr"></div>
                  </div>
                  <div id="head">Add Protocols</div>
				  
                  <div>
				  
				<?php    $i=0;
				
					if(is_array($bc_protocol) && count($bc_protocol) > 0){
					
					  $aid = count($bc_protocol);
					 for ($z=0;$z <$aid;$z++){

						
						 $bc_protocol1		= $bc_protocol[$z];
						 $start_date		= $bc_start_date[$z];
						 $end_date			= $bc_end_date[$z];
						 $i++;
							
							if( $i <=1 ){?>
							
<div id="proto">
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">Start Date</div>	
   <input type="text" name="start_date[]" id="start_date<?php echo $i; ?>" class="start_date<?php echo $i; ?> savedate" value="<?php if($start_date){echo $start_date;}?>" style="width:200px;">
  </div>
  
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">Select Protocol</div>
				<select style="width:180px;" class="new_input" name="protocol[]" id="protocol">
                      <?php
							$res = mysql_query("select * from `protocols` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
							while($row = mysql_fetch_array($res)){
							?>
                      <option value="<?php echo $row['id']; ?>" <?php if($bc_protocol1== $row['id']){ echo 'selected="selected"'; } ?>><?php echo $row['protocol_title']; ?></option>
                      <?php } ?>
                    </select>
  </div>
	
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">End Date</div>	
   <input type="text" name="end_date[]" value="<?php if($end_date){echo $end_date;}?>" class="end_date<?php echo $i; ?> savedate" id="end_date<?php echo $i; ?>" style="width:200px;">
  </div>
   
  <div class="clr"></div>
</div>
	 <div class="clr"></div>						
							<?php  } else {?>
							
							<div id="proto<?php echo $i; ?>">
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">Start Date</div>	
   <input type="text" name="start_date[]" id="start_date<?php echo $i; ?>" class="start_date<?php echo $i; ?> savedate" value="<?php if($start_date){echo $start_date;}?>" style="width:200px;">
  </div>
  
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">Select Protocol</div>
				<select style="width:180px;" class="new_input" name="protocol[]" id="protocol<?php echo $i; ?>">
                      <?php
					  
							$res = mysql_query("select * from `protocols` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
							while($row = mysql_fetch_array($res)){
							?>
                      <option value="<?php echo $row['id']; ?>" <?php if($bc_protocol1== $row['id']){ echo 'selected="selected"'; } ?>><?php echo $row['protocol_title']; ?></option>
                      <?php } ?>
                    </select>
  </div>
	
  <div style="float:left; width:250px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">End Date</div>	
   <input type="text" name="end_date[]" value="<?php if($end_date){echo $end_date;}?>" class="end_date1 savedate" id="end_date1" style="width:200px;">
   &nbsp; &nbsp;
    <?php if($i>1){?>
      <img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onClick="remove_protocol(<?php echo $i; ?>)">
    <?php  } ?>
  </div>
 
 
 
   
  <div class="clr"></div>
</div>
 <div class="clr"></div>
  
							
							<?php }
							} //end for
							} // end is arary
							else { ?>
							
							
 <div id="proto">
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">Start Date</div>	
   <input type="text" name="start_date[]" id="start_date1" class="start_date1 savedate" style="width:200px;">
  </div>
  
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">Select Protocol</div>
				<select style="width:180px;" class="new_input" name="protocol[]" id="protocol">
                      <?php
							$res = mysql_query("select * from `protocols` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
							while($row = mysql_fetch_array($res)){
							?>
                      <option value="<?php echo $row['id']; ?>" <?php if(is_array($bc_protocols) && in_array($row['id'],$bc_protocols)){ echo 'selected="selected"'; } ?>><?php echo $row['protocol_title']; ?></option>
                      <?php } ?>
                    </select>
  </div>
	
  <div style="float:left; width:240px; margin-right:20px">
    <div id="head" style="padding:16px 0 12px; font-size:22px">End Date</div>	
   <input type="text" name="end_date[]" class="end_date1 savedate" id="end_date1" style="width:200px;">
  </div>
   
  <div class="clr"></div>
</div>  
 <div class="clr"></div>
<?php  }?>	

	
	
		   <span id="add_more_protocol_area"></span> 
		    <div class="clr"></div>
				   
				   <div align="right" style="margin-top:10px;"><span id="add_more_protocol_btn"><img src="<?php echo IMAGE_PATH; ?>add_more.png" style="cursor:pointer" id="" onClick="add_more_protocol(<?php if ($aid){echo $aid;} else{ echo "1"; } ?>)" /></span></div>
					
                  </div>
				  
				   <div id="head">Add Tests</div>
                     <div>
                       <select style="width:300px;" class="new_input" name="tests[]" id="tests" multiple="multiple">
                         <?php
								$res = mysql_query("select * from `tests` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
								while($row = mysql_fetch_array($res)){
							?>
                         <option value="<?php echo $row['id']; ?>" <?php if(is_array($bc_tests) && in_array($row['id'],$bc_tests)){ echo 'selected="selected"'; } ?>><?php echo $row['test_name']; ?></option>
                         <?php } ?>
                       </select>
                     </div>
				  
                  <div id="head">Plan Details</div>
                  <div>
                    <textarea name="plan_detail" id="plan_detail" class="bc_input" style="width:825px; height:250px"><?php echo $bc_plan_detail; ?></textarea>
                  </div>
                </div>
               
              
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
		
		<input type="hidden" name="submit" value="1" />
		<input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" /> 
       
        
         
          <div class="clr"></div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
(function($, Z){
  $('#z_listing_event_form').event_form({
    listing_class: 'QueuedEvent',
    listing_id: '1120455984',
    session_user: true,
    is_premium_listing: false,
    is_published_event: false,
    skip_campaign_redirect: false,
    partner_id: 0,
    internal_user: false,
    enhanced_paid_for: false,
    promoted_paid_for: false
  });
})($ZJQuery, Zvents);
//]]>
</script>
<?php include_once('includes/footer.php');?>
<script>

	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "simple",
		elements : "plan_detail",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "style.css",
	});
	
	
$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
//		alert(imgID);
//		return false;
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide()
	// ("#delImg_" + imgInfo[4]).css("display", "none");
	$("#"+imgInfo[4]).attr("src", "admin/images/no_image.png");
	if(imgInfo[5]=='showfile'){
	$('#'+imgInfo[6]).css('display','block');
	$('#showimg'+imgInfo[6]).html('<input type="file" name="images[]" />');
	
	}
	}
});


  $(function() {
   /* $('#example-1').tipsy();
    $('#north').tipsy({gravity: 'n'});
    $('#south').tipsy({gravity: 's'});
    $('#east').tipsy({gravity: 'e'});*/
    $('.info').tipsy({gravity: 'w', fade: true});
   /* $('#auto-gravity').tipsy({gravity: $.fn.tipsy.autoNS});
    $('#example-fade').tipsy({fade: true});
    $('#example-custom-attribute').tipsy({title: 'id'});
    $('#example-callback').tipsy({title: function() { return this.getAttribute('original-title').toUpperCase(); } });
    $('#example-fallback').tipsy({fallback: "Where's my tooltip yo'?" });
    $('#example-html').tipsy({html: true });*/
  });
</script>
