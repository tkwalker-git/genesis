<?php 



require_once("database.php");




$_SESSION['admin_user'] = '';



unset($_SESSION['admin_user']);



if (isset($_GET['errormessage'])) {



	$msg = $_GET['errormessage'];



}







if(isset($_SESSION['logedout'])) {



    $_SESSION['logedout'];



    $_SESSION['logedout'] = '';



    unset($_SESSION['logedout']);



}



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

   <title>Admin Panel Login</title>

	

<script>



function MM_swapImgRestore() { //v3.0

  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;

  	

	try {

		

		$('#fb_message_bt').stop(true, true).slideUp('slow');

		$('#tw_message_bt').stop(true, true).slideUp('slow');

	} catch(e) {}

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

  var i,j=0,x,a=MM_swapImage.arguments;

  

  document.MM_sr=new Array; 

  for(i=0;i<(a.length-2);i+=3)

   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}

  	

	try {

		if (a[0] == 'smi8') {

			$('#fb_message_bt').slideDown('slow');	  

		}

		if (a[0] == 'smi9') {

			$('#tw_message_bt').slideDown('slow');	  

		}

	} catch(e) {}	

}





</script>

	

</head>

<body onload="document.getElementById('txtUser').focus();">

<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH;?>/admin/lb_styles.css" />



<div style="width:544px; text-align:right; margin:100px auto 0px;">

	

<div class="rounded">

	<div class="inner">

<form action="ProcPass.php?action=Login" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

			  <tr>

				<td align="left" valign="top" width="7"><img src="<?php echo ABSOLUTE_PATH;?>/ajax/images/white_tl.png" width="7" height="8" /></td>

				<td align="left" valign="top" style="background-color:#FFFFFF"><img src="<?php echo ABSOLUTE_PATH;?>/ajax/images/white.png" width="8" height="8" /></td>

				<td align="right" valign="top" width="7"><img src="<?php echo ABSOLUTE_PATH;?>/ajax/images/white_tr.png" width="7" height="8" /></td>

			  </tr>

			  <tr>

				<td colspan="3" align="left" valign="top" class="headingsub" >

					Admin Login</td>

			  </tr>

			  <tr>

				<td colspan="3" align="center" style="padding:10px 27px; background-color:#393833;font-size:30px; font-weight:bold;  color:#FFFFFF ">

					<img src="<?php echo ABSOLUTE_PATH;?>images/users_image1.png" width="67" height="68" align="absmiddle" /> Admin Login

				</td>

			  </tr>

			  <tr>

				<td colspan="3" valign="top" style="padding:27px; font-family:Arial; font-size:13px; color:#010000">

					<table width="285" border="0" cellspacing="0" cellpadding="0" align="center">

					  <tr>

						<td colspan="2">&nbsp;</td>

					  </tr>

					  <tr>

						<td width="85" align="left" >Email:

						<input type="hidden" name="rd_url" id="rd_url" value="<?php echo  $rd_url;?>" />

						  <input type="hidden" name="current_url" id="current_url" class="chkout_input" style="width:340px!important"  value="<?php echo  $_SERVER['REQUEST_URI'];?>"/>

						</td>

						<td width="290" >

						 

						  <input type="text" name="txtUser" id="txtUser" class="chkout_input" style="width:260px"  />

					    </td>

					  </tr>

					  

					  <tr>

						<td colspan="2">&nbsp;</td>

					  </tr>

					  <tr>

						<td align="left" >Password:</td>

						<td ><input type="password" name="txtPass" id="txtPass" class="chkout_input" style="width:260px" /></td>

					  </tr>



					  <tr>

					  	<td>&nbsp;</td>

						<td style="padding-top:2px; color:#020000;" align="left">Can't remember your password? <a href="<?=ABSOLUTE_PATH?>admin/forgot_password.php" style="color: #020000;float: right;margin-right: 15px;text-decoration: none;">Click here</a><br/>

			<span id="login_processing"></span>

						</td>

					  </tr>

					  

					  <tr>

						<td colspan="2" ><span id="login_error" style="float:left; color:#990033"><strong><?php echo $msg;?></strong></span></td>

					  </tr>

					</table>

				</td>

			  </tr>	

			  <tr>

				<td colspan="3" align="left" style="padding:27px; padding-top:10px" >

					<table width="100%" border="0" cellspacing="0" cellpadding="0">

					  <tr>

						<td style="font-size:11px; color:#5A5959; line-height:14px;">&nbsp;

							

						</td>

						<td align="right">

							<a href="#" ><input type="image" src="images/login.png" name="Image4" width="72" height="26" border="0" id="Image49" onClick="javascript:login_user('<?php echo  ABSOLUTE_PATH;?>','')"/></a>

						</td>

					  </tr>

					</table>



					

				</td>

			  </tr> 

			</table>





</form>

</div>

</div>

</div>

</body>

</html>