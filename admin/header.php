<?php

@session_start();

if ( !isset($_SESSION['admin_user']) || $_SESSION['admin_user'] == '' ) {

	header('Location: login.php');

	exit;

}

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>:: Admin Control Panel :: </title>

<link rel="stylesheet" href="css/admin.css" type="text/css" media="print, projection, screen" />

<script type="text/javascript" src="js/jquery.min.js"></script> 

<script type="text/javascript" src="js/jquery.table.js"></script> 

<script type="text/javascript" src="js/jquery.metadata.js"></script>

<script type="text/javascript" src="../js/ev_functions.js"></script>

<script type="text/javascript">
/*
$(document).ready(function() {

	$("#tablesorter").tablesorter();

});
*/
</script>
	<script type="text/javascript" src="js/colorpicker.js"></script>
    <script type="text/javascript" src="js/eye.js"></script>
    <script type="text/javascript" src="js/utils.js"></script>
    <script type="text/javascript" src="js/layout.js?ver=1.0.2"></script>
	<script type="text/javascript" src="../js/validate.decimal.js"></script>
	<script language="javascript">
		$(function() {
		var dates = $( "#start_sales_date, #end_sales_date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat:'dd-M-yy',
			minDate:'<?php echo date('d-M-Y',strtotime(' +1 day')) ?>',
		/*		dateFormat:'yy-m-dd',
			minDate:'<?php// echo date('Y-m-d');?>',*/
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "start_sales_date" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
</script>
	<link rel="stylesheet" href="css/colorpicker.css" type="text/css" />
	
	<!--data picker --> 
<style type="text/css">
@import "js/date_picker/jquery.datepick.css";
</style>  
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>-->
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-1.4.min.js"></script>
<script type="text/javascript" src="js/date_picker/jquery.datepick.js"></script>
<script type="text/javascript">
<!--
$(function() {
	$('#expiration_date').datepick();
	$('#valid_from').datepick();
	
});

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
  
 <script>  
	 function dynamic_Select(ajax_page, category_id,sub_category)  
	 {  
		 $.ajax({  
		 type: "GET",  
		 url: ajax_page,  
		 data: "cat=" + category_id + "&subcat=" + sub_category,  
		 //data: "subcat=" + subcat_id,
		 dataType: "text/html",  
		 success: function(html){       $("#subcategory_id").html(html);     }  
	   
	   }); }  
</script>  
  
</head>

<body onload="MM_preloadImages('images/signout_new_over.png')">
<div id="main">
<table cellpadding="0" cellspacing="0" align="center" id="tbl">

	<tr>

    	<td>

        	<table cellpadding="0" cellspacing="0" width="100%" align="center" style="padding:0px;">
                <tr>

                	<td colspan="2" height="90" style="background-image:url(images/header_bg.png); background-repeat:repeat-x">

                	<table width="100%" cellpadding="0" cellspacing="0">

                    	<tr>

                        	<td width="33%" id="logo"><img src="images/logo.png" align="absmiddle" style="margin-left:50px" /></td>
							
							<td width="34%" align="center" style="color:#DBDBDB; font-family:Arial, Helvetica, sans-serif; font-size:13px" >&nbsp;</td>

		                    <td width="33%" id="loggedin">
								<a href="#">Welcome <?=$_SESSION['admin_user']?></a><br />
						  <a href="ProcPass.php?action=logout" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','images/signout_new_over.png',1)"><img src="images/signout_new.png" name="Image3" width="91" height="34" border="0" id="Image3" /></a></td>

                        </tr>

                    </table>

                    </td>

                </tr>

                <tr><td colspan="2" height="10"></td></tr>

                <tr valign="top">

                    <td width="200" style="padding:0px 10px; background-image:url(images/menu_bg_new.png); background-repeat:repeat-y"><?php require_once("menu.php"); ?></td>

<td height="400" style="padding-right:10px;">