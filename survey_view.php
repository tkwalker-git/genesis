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

$form_id = $_GET['frmID'];

$url = attribValue('mnoviforms', 'View_Link', "where ID='". $form_id ."'");
$s_value = attribValue('mnoviforms', 's_value', "where ID='". $form_id ."'");

if(isset($_GET['resid']) && $_GET['resid']!='')
	$res_id = $_GET['resid'];
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
<style>
.graph_area {
margin-top:10px;
margin-bottom:30px;
}
.graph_bars {
width:500px;
margin:auto;
}
</style>
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
		  
		<?php    $nid		=	$_GET['frmID'];
				 if($_GET['patient']){
					 $pid		=	$_GET['patient'];
				 }else {
					 $pid		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
				 }
				$sel_res_id	=	mysql_query("select * from `mnoviforms_response` where `mnoviforms_id`='".$nid."' && `patient_id`='".$pid."'");
				 $hav_res_id	=	mysql_num_rows($sel_res_id);
				if($hav_res_id){
					while($get_res_id	=	mysql_fetch_array($sel_res_id)){
					$res_id	=	$get_res_id['response_id'];
						
						$sel_res_val	=	mysql_query("select * from `mnoviforms_response_detail` where `response_id`='".$res_id."' && `Value` != 'NA'");
						 $have_res_id	=	mysql_num_rows($sel_res_val);
						if($have_res_id){
							//$get_res_val	=	mysql_fetch_array($sel_res_val);
									$n=1;				
								while($get_res_val	=	mysql_fetch_array($sel_res_val)){
									
									if($get_res_val['Key'] != 'Response ID' && $get_res_val['Key'] != 'Date started' && $get_res_val['Key'] != 'Date completed' && $get_res_val['Key'] != 'Response time' && $get_res_val['Key'] != 'Language' && $get_res_val['Key'] != 'Last page saved' && $get_res_val['Key'] !='Partial save' && $get_res_val['Key'] !='PangiaID'){	
										$res_ex	=	explode('|',$get_res_val['Key']);
										$res_val=count($res_ex);
											if($res_val != 3 || $res_val != 3){
												 
												 
												 if($_GET['frmID']==2 || $_GET['frmID']==3){
												 	 $fin_gr	=	explode('%',$get_res_val['Key']);
														 $res_fin_gr	=	count($fin_gr);
														if($res_fin_gr >= 2 ){
															$graph_res[$n]=$get_res_val['Key']."==".$get_res_val['Value'];
															$n++;
															}
													}
													
													
													
											}	
										}						
								
								}					
							
						}
					}
				}		?>
		  
		  <?php if($_GET['frmID'] ==2 || $_GET['frmID'] == 3){ ?>
				<div class="graph_area">
				<div style="width:500px; background:#EEEEEE; margin:auto; padding-top:15px; padding-bottom:25px; padding-left:25px;">
				<div><h2 style="text-align:center; font-size:18px; font-family:Arial, Helvetica, sans-serif; line-height:30px;">
				<?php if($_GET['frmID'] ==2){echo "Take Health Assessment Top Dysfunctions";} ?><?php if($_GET['frmID'] ==3){echo "Restoration Health Assessment Top Dysfunctions";} ?>
				</h2></div>
					<div class="graph_bars">
					<?php  $count_bars = count($graph_res); 
					$b=1;
					$i=1;
					while($i <= $count_bars){
					$abcd = $graph_res[$b];
					$acd_res	=	explode('==',$abcd); 
					?>
					<div style=" background:#DDDDDD; width:500px;">
						<div style="background:url(images/prgBarOutline.png) repeat-x; height:15px;">
							<div style="background:url(images/prgBar.gif) repeat-x; height:15px; width:<?php echo round(trim(str_replace('%','',$acd_res[1])))*5; ?>px;"></div>
						</div>
						<div style="line-height:20px; padding-bottom:2px; padding-left:10px;"><?php echo str_replace('(%)','',$acd_res[0]); ?>&nbsp;&nbsp;<?php echo $acd_res[1]; ?></div>
					</div>
					
					
					<?php $i++; $b++;  	}  	?>
					</div>
				</div>
				</div>	
				<?php } ?>
			<div style="padding-bottom:50px; overflow:hidden;">
				<!-- <iframe src="<?php echo $url . $res_id; ?>&s=<?php echo $s_value; ?>" frameborder=0 width="900px" height="2400px" ></iframe> -->		
				
				<iframe src="https://pangeaifa.com/NoviSurvey/ShowResponse.aspx?doid=<?php echo $res_id; ?>&s=<?php echo $s_value; ?>" frameborder=0 width="900px" height="10530px" ></iframe>
				
				
				
				
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>