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
}
$meta_title	= 'Survey';
include_once('includes/header.php');

$sql = "select * from mnoviforms where `FormName`='Restoration Health Assessment'";
$res = mysql_query($sql);
$form_id = null;
if($res)
{
	if(mysql_num_rows($res)>0)
	{
		$row= mysql_fetch_array($res);
		$form_id = $row['ID'];
	}
	else
	{
		$sql="INSERT INTO `mnoviforms` (`ID` ,`FormName`) VALUES (NULL , 'Restoration Health Assessment');";
		$res = mysql_query($sql);
		$form_id = mysql_insert_id($res);
	}
}
if(isset($_GET['resid']) && $_GET['resid']!='')
{
	$res_id = $_GET['resid'];
}
else
{
	$res_id = null;
	$sql = "select * from mnoviforms_response where PangiaId=".$member_id." and `mnoviforms_id`=".$form_id;
	$res = mysql_query($sql);
	if($res)
	{
		if(mysql_num_rows($res)>0)
		{
			$row=mysql_fetch_array($res);
			$res_id=$row['response_id'];
		}
	}
}
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
				<iframe src="https://pangeaifa.com/NoviSurvey/ViewReport.aspx?doid=c60769a0eac241628210f405d69fb585&re=<?php echo $res_id; ?>" frameborder=0 width="900px" height="2400px" ></iframe>
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>