<?php 
require_once('admin/database.php');
require_once('site_functions.php');


// echo $_SESSION['usertype'];

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";


$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}else {
$member_full_name = attribValue('patients', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
$sex = attribValue('patients', 'sex', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}
 $pdo = getSingleColumn("clinicid","select * from `patients` where `id`='".$_SESSION['LOGGEDIN_MEMBER_ID']."'");
 $tdate=date("Y-m-d");
 
  $sdfc ="select * from schedule_dates where patient_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' &&  clinic_id ='$pdo' && cons_date >='$tdate' order by cons_date ASC limit 1";	
$gdate = mysql_query($sdfc);
while($get_date=mysql_fetch_array($gdate)){
 $pdate = $get_date['cons_date'];
}


$meta_title	= 'Pre Visit Form';
include_once('includes/header.php');


/* $link="https://pangeaifa.com:8443/PatientForms/DisplayForm.jsp?encountertemplate=Followup&schemaname=pangeafinal2&patientId=".$member_id."&encounterDate=".$pdate; */

 $link="https://yourhealthsupport.com:8443/PatientForms/DisplayForm.jsp?encountertemplate=Followup&schemaname=pangeafinal2&patientId=".$member_id."&encounterDate=".$pdate;


?>
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
				<iframe src="<?php echo $link; ?>" height="1200px" width="900px"></iframe></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>