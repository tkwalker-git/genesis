<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1); 
require_once('admin/database.php');
require_once('site_functions.php');

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

$sql = "select * from mnoviforms where `FormName`='Female Health Assessment'";
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
		$sql="INSERT INTO `mnoviforms` (`ID` ,`FormName`) VALUES (NULL , 'Female Health Assessment');";
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
				if(1) 
				{
					$sql = "select * from mnoviforms_response where PangiaId=".$member_id." and `mnoviforms_id`=".$form_id;
					$res = mysql_query($sql);
					$show_flg=true;
					if($res)
					{
						if(mysql_num_rows($res)>0)
						{
							$row= mysql_fetch_array($res);
							if($row['response_id']!='')
								$show_flg = false;
						}
					}
					if($show_flg)
					{
				?>
								<iframe src=" http://pangeaifa.com/NoviSurvey/n/DrRondaFemaleHealthHistory.aspx?PangiaID=<?php echo $member_id; ?>" frameborder=0 width="900px" height="1800px" ></iframe>
							
				
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
<script type='text/javascript'>
/* alert('Because of security measures, you may be asked to login again. This is a standard procedure. '); */
</script>
<?php include_once('includes/footer.php'); ?>