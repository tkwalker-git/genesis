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

$sql = "select * from mnoviforms where `FormName`='Take Health Assessment'";
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
		$sql="INSERT INTO `mnoviforms` (`ID` ,`FormName`) VALUES (NULL , 'Take Health Assessment');";
		$res = mysql_query($sql);
		$form_id = mysql_insert_id($res);
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
				<?php
				if(isset($_GET['sid']) && $_GET['sid'] !="")
				{
					$sql = "SELECT * FROM `mnoviforms_response` WHERE `PangiaId`='".$member_id."' and `mnoviforms  ID` = '".$form_id."'";
					$res = mysql_query($sql);
					if($res)
					{
						if(mysql_num_rows($res)>0)
						{
							$sql = "UPDATE `mnoviforms_response` SET `Response id`='".$_GET['sid']."',`Completed on`='".date("Y-m-d H:i:s")."' WHERE `PangiaId` =".$member_id." AND `mnoviforms  ID` =".$form_id;
							$res = mysql_query($sql);
						}
						else
						{
							$sql = "INSERT INTO `mnoviforms_response` (`ID` ,`mnoviforms  ID`,`PangiaId` ,`Patient Id` ,`Response id` ,`Date ON` ,`Completed on`) VALUES (NULL , '".$form_id."', '".$member_id."', '".$member_id."', '".$_GET['sid']."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."');";
							$res = mysql_query($sql);
						}
						
					}
					else
					{
						echo 'System error, Please reload page';
					}
				}
				else
				{
					$sql = "select * from mnoviforms_response where PangiaId=".$member_id." and `mnoviforms  ID`=".$form_id;
					$res = mysql_query($sql);
					$show_flg=true;
					if($res)
					{
						if(mysql_num_rows($res)>0)
						{
							$row= mysql_fetch_array($res);
							if($row['Response id']!='')
							$show_flg = false;
						}
						else
						{
							$sql = "INSERT INTO `mnoviforms_response` (`ID` ,`mnoviforms  ID`,`PangiaId` ,`Patient Id` ,`Response id` ,`Date ON` ,`Completed on`) VALUES (NULL , '".$form_id."', '".$member_id."', '".$member_id."', NULL , '".date("Y-m-d H:i:s")."', NULL);";
							$res = mysql_query($sql);
							
						}
						
					}
					if($show_flg)
					{
				?>
								<iframe src="http://pangeaifa.com/NoviSurvey/n/zzz52.aspx?PangiaId=<?php echo $member_id; ?>" frameborder=0 width="900px" height="1800px" ></iframe>
							
				
				<?php
					}
					else
					{
						echo 'You have already completed this survey.  Please visit your Dashboard to take a new survey';
						
					}
				}
				?>
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>