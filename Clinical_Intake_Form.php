<?php 
require_once('admin/database.php');
require_once('site_functions.php');


// echo $_SESSION['usertype'];

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='user_login.php';</script>";


$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}else {
$member_full_name = attribValue('patients', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
$sex = attribValue('patients', 'sex', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}
$meta_title	= 'Clinical Intake Form';
include_once('includes/header.php');

if($sex=="Male" || $sex=="male")
{
	$link="https://pangeaifa.com:8443/PatientForms/DisplayForm.jsp?encountertemplate=Male%20Initial&schemaname=pangeafinal2&patientId=".$member_id;
}
else
{
	$link="https://pangeaifa.com:8443/PatientForms/DisplayForm.jsp?encountertemplate=Female%20Initial&schemaname=pangeafinal2&patientId=".$member_id;
}

?>
<!--<script language="javascript">

setTimeout('reload_iframe()', 15000);

function reload_iframe(){
document.getElementById("para1").src='$link';
}
</script>-->

<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">

<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu_tk.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
			<div style="padding-bottom:50px; overflow:hidden;">
				<iframe src="<?php echo $link; ?>" id="para1" frameborder=0 width="900px" height="2200px" ></iframe>			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>