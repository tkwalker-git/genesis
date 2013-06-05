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

$sql = "select * from mnoviforms where ID=".$_GET['id'];
$res = mysql_query($sql);
if($res)
{
	if(mysql_num_rows($res)>0)
	{
		$row= mysql_fetch_array($res);
		$link = $row['View_Link'].$member_id;
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
				<iframe src="<?php echo $link; ?>" frameborder=0 width="900px" height="1800px" ></iframe>
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>