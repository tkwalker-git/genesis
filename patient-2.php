<?php 

require_once('admin/database.php');
require_once('site_functions.php');

if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'patients',$_GET["id"]) =='false')
        echo "<script>window.location.href='clinic_manager.php';</script>";
		
$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

     $dt=date('Y-m-d');
	 
	
if($_POST['comm']){
$comment=$_POST['comment'];
$date=date("Y-m-d");
mysql_query("insert into patient_comments set patient_id='".$_GET['id']."',clinic_id ='".$_SESSION['LOGGEDIN_MEMBER_ID']."',comment_date='".$date."',comment ='".$comment."'");
$stats_patient	=	 getSingleColumn("status","select * from `patients` where `id`='".$_GET['id']."'");
if($stats_patient==2){
	mysql_query("update patients set status='3' where id='".$_GET['id']."'");
}
} 

if($_POST['final']){
 $f_rel=$_POST['release'];
mysql_query("update patients set status='".$f_rel."' where id='".$_GET['id']."'");
} 
	
	 
	if($_POST['upload']){
	$f_name = $_POST['f_name'];	
	if ($_FILES["p_img"]["name"] != "") {
			$bc_p_image  = time() . "_" . $_FILES["p_img"]["name"] ;		
			move_uploaded_file($_FILES["p_img"]["tmp_name"], "patient_images/" .$bc_p_image);
			/*makeThumbnail($bc_book_image, '../images/books/', $thDir, '100', '100', $th='re_');*/
			mysql_query("insert into patient_images set Patient_ID='".$_GET['id']."',Image_Name='$f_name',File_name='$bc_p_image',Added_On='$dt',clinic_id='$member_id',status='1'");
			
		} 
	
	}   
      
	  if($_GET['del'] && $_GET['type']=='images'){	
	mysql_query("delete from patient_images where ID='".$_GET['del']."'");
	$succ=1;
	
	}    


include_once('includes/header.php');

if($_GET['id']){
    $id=$_GET['id'];
    $res = mysql_query("select * from patients where id='$id'");
    if($row=mysql_fetch_array($res)){
        $fname  = $row['firstname'];
        $lname  = $row['lastname'];
        $sex    = $row['sex'];
        $dob    = $row['dob'];
        $address= $row['address'];
        $comments= DBout($row['comments']);
        $city   = $row['city'];
        $state  = $row['state'];
        $zip    = $row['zip'];
        $phone  = $row['phone'];
        $email  = $row['email'];
        $emrid  = $row['genensysuserid'];
    }
    
    ///////get patient tests
    
    $res1 = mysql_query("select * from patient_tests where patient_id='$id'");
        if(mysql_num_rows($res1)){
            while($row1=mysql_fetch_array($res1)){
            $tid= $row1['test_id'];
                
                $res2 = mysql_query("select * from tests where id='$tid'");
                while($row2=mysql_fetch_array($res2)){
                $testname.= $row2['test_name']."<br />";        
                }
        
            }
    
        }

//get plan name and life style changes and dietery changes

$get_data = mysql_query("select * from `plan` where `patient_id`='$id' && clinic_id='".$member_id."' order by id DESC limit 1");
while($sho_get_data= mysql_fetch_array($get_data)){
$planname = $sho_get_data['plan_name']."<br />";    
$plan_id = $sho_get_data['id'];

        $res3 = mysql_query("select * from  plan_protocol where `plan_id`='$plan_id' limit 1");
        if(mysql_num_rows($res3)){
            while($row3=mysql_fetch_array($res3)){
            $poroid= $row3['protocol_id'];
                
                $res4 = mysql_query("select * from `protocols` where id='$poroid'");
                while($row4=mysql_fetch_array($res4)){
                $lifestyle_changes.= $row4['lifestyle_changes']."<br />";
                $dietary_changes.= $row4['dietary_changes']."<br />";
                         
                }
        
            }
    
        }      

}

    
        
        
        
        
        

    }

$full_address = $address . '<br>' . $city . ' ' . $state . ', ' . $zip;
$comments_s = strip_tags($comments);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
    <link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancy/jquery.fancybox.css" type="text/css" media="screen">
    <script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancy/jquery.mousewheel-3.0.6.pack.js">
</script>
    <script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancy/jquery-1.8.2.min.js">
</script>
    <script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancy/jquery.fancybox.pack.js">
</script>
    <script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancy/jquery.fancybox.js">
</script>
    <script type="text/javascript">
$(document).ready(function() {
        $("a#eventImage").fancybox({
            'titleShow'     : false,
            'transitionIn'  : 'elastic',
            'transitionOut' : 'elastic'
        });
        

    });


    $(document).ready(function() {
        $(".fancybox").fancybox();
    });



    </script>
    <style type="text/css">
.nav_new {
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 5px 5px 5px 5px;
    box-shadow: 0 0 7px 2px #C7C7C7;
    overflow:hidden;
    margin:0;
    }

    .nav_new ul li {
    border-right: 1px solid #B4B4B4;
    float: left;
    list-style: none outside none;
    }

    .nav_new ul li a {
    color: #000000;
    float: left;
    font-size: 15px;
    font-weight: bold;
    padding: 13px 24px;
    text-decoration: none;
    }
    .basic-modal-content {
    /*  display:none;*/
    }
    /* Overlay */
    #simplemodal-overlay {
    background-color:#000;
    cursor:wait;
    }
    /* Container */
    #simplemodal-container {
    height:250px;
    width:400px;
    color:#000;
    -moz-border-radius:10px;
    border-radius:10px;
    -webkit-border-radius:10px;
    -moz-box-shadow:0 1px 3px #777;
    -webkit-box-shadow:0 2px 3px #777;
    box-shadow:0 2px 3px #777;
    background: #E7FCFF;
    background:-webkit-gradient(linear, left bottom, left top, color-stop(0.4, #E7FCFF), color-stop(0.70, #FFFFFF));
    background:-moz-linear-gradient(center bottom, #E7FCFF 40%, #FFFFFF 70%) repeat scroll 0 0 transparent;
    -pie-background:linear-gradient(90deg, #E7FCFF, #FFFFFF 30px);
    padding:12px;
    behavior: url(http://www.eventgrabber.com/css/PIE.htc);
    }
    #simplemodal-container .simplemodal-data {
    padding:8px;
    }
    #simplemodal-container code {
    background:#141414;
    border-left:3px solid #65B43D;
    color:#bbb;
    display:block;
    font-size:12px;
    margin-bottom:12px;
    padding:4px 6px 6px;
    }
    #simplemodal-container a {
    color:#ddd;
    }
    #simplemodal-container a.modalCloseImg {
    background:url(<?php echo IMAGE_PATH; ?>x.png) no-repeat;
    width:25px;
    height:29px;
    display:inline;
    z-index:3200;
    position:absolute;
    top:-15px;
    right:-16px;
    cursor:pointer;
    }
    #simplemodal-container h3 {
    background: none repeat scroll 0 0 #F2F2F2;
    border-left: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
    border-top: 1px solid #CCCCCC;
    color: #84B8D9;
    margin: 0;
    padding: 10px;
    }
    .formfield{
    overflow:hidden;
    padding-top: 8px;
    }
    .formfield label{
    float: left;
    color: #666666;
    font-weight: bold;
    padding-right: 10px;
    text-align: right;
    width:115px;
    }
    .formfield input,.formfield select{
    width:232px;
    border: 1px solid #BDC7D8;
    height:18px;
    font-size: 11px;
    }

    .formfield select{
    height: 22px;
    padding: 2px;
    width: 234px;
    }
    .rsvpBox{
    border: 1px solid #C1C1C1;
    background:#FFFFFF;
    min-height:150px;
    padding-top:10px;
    }
    </style><!--<link type='text/css' href='<?php echo ABSOLUTE_PATH;?>css/basic.css' rel='stylesheet' media='screen' />    -->
    <!--[if lt IE 7]>

    <link type='text/css' href='<?php echo ABSOLUTE_PATH;?>css/basic_ie.css' rel='stylesheet' media='screen' />

<![endif]-->
    <link type="text/css" rel="stylesheet" media="screen, projection" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css">
    <script type='text/javascript' src='<?php echo ABSOLUTE_PATH;?>js/jquery.simplemodal.js'>
</script>
    <style type="text/css">
#simplemodal-container {
    /*       width:  440px;
        height: 360px; */
    }
    .page_like_box {
        z-index: 10001;
        position: absolute;
        width: 420px;
        height: 340px;
        display: none;
    }
    </style>
    <script type='text/javascript'>
function showPopup(){
        $('#basic-modal-content').modal();
    }
    </script><?php $view=420; ?>
    <link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">

    <title></title>
</head>

<body>
    <div class="topContainer">
        <div class="welcomeBox">
            <?php
              if($_SESSION['admin_user']){?><a href="<?php echo ABSOLUTE_PATH; ?>admin/events.php?id=<?php echo $event_id; ?>" target="_blank" style="color:#ff4e1f; font-weight:normal">Edit Event</a> <?php } ?>
        </div><!--End Hadding -->
        <!-- Start Middle-->

        <div id="middleContainer">
            <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"></div>

            <div class="clr"></div>

            <div class="gredBox">
                <?php include('dashboard_menu_tk.php'); ?>

                <div class="whiteTop">
                    <div class="whiteBottom">
                        <div class="whiteMiddle" style="padding-top:1px;">
                            <!--Start new code-->

                            <div class="buyTicketp1">
                                <div style="overflow:hidden; float:left">
                                    <div class="thumb-1"><img src="<?php echo ABSOLUTE_PATH; ?>images/sim-demo.png" alt=""></div><!--end thumb-1-->

                                    <div style="text-align:center">
                                    
                                    
                                        <a href="<?php echo ABSOLUTE_PATH; ?>schedule_patients.php"><input style="padding:4px 8px;" type="button" name="plan" value="Schedule Appointment"></a>
                                        <!-- <a target="_blank" href="<?php echo ABSOLUTE_PATH; ?>simulation.php?clinicid=<?php echo $member_id;?>"><input style="padding:4px 8px;" type="button" name="simulation" value="View Simulation">&nbsp;</a> <a href="<?php echo ABSOLUTE_PATH; ?>create_plan.php"> -->
                                        <input style="padding:4px 8px;" type="button" name="plan" value="Create Plan"></a> 
                                        <!-- <input style="padding:4px 8px;" type="button" name="closeout" value="Closeout"></a>  -->
                                       
                                         <!-- <a href="view_plan_report_user.php?id='.$row['id'].'"
                                        <input style="padding:4px 8px;" type="button" name="plan" value="Print Plan" /></a> -->
                                         <?php 
                                                                
                                                            //  echo $viw;
                                                                if($viw < 420){
                                                                echo "<div class='clr'><br /></div>";
                                                                }
                                                                 ?>
                                    </div><span id="lk"></span>
                                </div>

                                <div class="thumb-1-detail" style="">
                                    <div class="ew-heading">
                                        <?php echo $fname.'&nbsp;'.$lname; ?>
                                    </div><?php
                                                     //date in mm/dd/yyyy format; or it can be in other formats as well
                                                     $birthDate = $dob;
                                                     //explode the date to get month, day and year
                                                     $birthDate = explode("/", $birthDate);
                                                     //get age from date or birthdate
                                                     $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
                                                  ?>

                                    <div class="hosted_by" style="float: left;">
                                        Gender: <?php echo $sex; ?>
                                    </div>

                                    <div class="hosted_by" style="float: left; padding-left: 80px;">
                                        Age: <?php echo $age; ?>
                                    </div>

                                    <div class="hosted_by" style="float: right;">
                                        Phone: <?php echo $phone; ?>
                                    </div>

                                    <div class="clr"></div>

                                    <div class="ew-price-area">
                                        <span class="ew-heading-a">Current Plan:&nbsp;<span style="color:#ff4e1f;"><?php echo $planname; ?></span></span>
                                    </div><!--
      <div class="clr"></div>
                <div class="ew-when-where"> <span class="ew-when-heading">Patient Health Concerns</span> 
                <span>
                  <?php
                            $comments_s = strip_tags($comments);
                            if(strlen($comments_s) > 200){
                                echo substr($comments_s,0,200).'... <a href="?type=concerns#l">[More]</a>';
                            }
                            else{
                                echo $comments_s;
                            }
                            ?>
                <?php  //echo $event_description; ?>
              </span> </div>
-->

<!-- <br /><br /> -->
											<div><strong>STATUS:&nbsp;</strong>
											<!-- insert upcoming patients script-start -->
											<?php
											$stats_pat	=	 getSingleColumn("status","select * from `patients` where `id`='".$_GET['id']."'");
											
											 /*?>$today=date("Y-m-d");
											
											$scheduled = getSingleColumn("cons_date","select * from `schedule_dates` where `patient_id`='".$row['id']."' && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."'");
											$notes	<?php */

											?>
											
											<!-- insert upcoming patients script-end -->
											
											
											  
											<?php if ($stats_pat){
												
											?>
												<span style="color:<?php echo patient_status_color($stats_pat); ?>; font-weight:bold;"><?php echo patient_status($stats_pat); ?></span>
												
												<?php if ($is_private){
													
												?>
														<i style="font-weight:normal">(Private)<i>
												<?php
												 } ?>
												 
												 
											<?php
											 }
											 
											 
											 
											 
											else{
												
											?>
												<span style="color:#a80233">Request</span>
											<?php 
											}

											if($row['type']=='draft'){
												
											?>
												<font color="red"> (Draft)</font>
											<?php
											 } ?>
											 
											 
											 </div>   

                                    <div class="clr"></div>

                                    <div class="ew-when-where">
                                        <span class="ew-when-heading">Lifestyle Changes</span> <span><?php
                                                                                                        $lifestyle_changes_s = strip_tags($lifestyle_changes);
                                                                                                        if(strlen($lifestyle_changes_s) > 300){
                                                                                                            echo substr($lifestyle_changes_s,0,300).'... [More]';
                                                                                                        }
                                                                                                        else{
                                                                                                            echo $lifestyle_changes_s;
                                                                                                        }
                                                                                                        ?></span>
                                    </div>

                                    <div class="clr"></div>

                                    <div class="ew-when-where">
                                        <span class="ew-when-heading">Dietary Changes</span> <span><?php
                                                                                                        $dietary_changes_s = strip_tags($dietary_changes);
                                                                                                        if(strlen($dietary_changes_s) > 300){
                                                                                                            echo substr($dietary_changes_s,0,300).'... [More]';
                                                                                                        }
                                                                                                        else{
                                                                                                            echo $dietary_changes_s;
                                                                                                        }
                                                                                                        ?></span>
                                    </div>
                                </div>

                                <div class="clr"></div>
                            </div><?php
                                    $type = $_GET['type'];
                                   ?>

                            <div class="ew-when-where">
                                <span class="ew-when-heading" style="padding-left: 220px!important;">S.O.A.P Notes</span><br />

                                <div class="nav_new">
                                    <ul>
                                        <li <?php if ($type=='subjective'|| $type==''){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=subjective#l">(S)Subjective</a></li>
                                        
                                        <!-- <li <?php if ($type=='reports'){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=reports#l">Reports</a></li> -->
                                        <!-- <li <?php if ($type=='concerns'){ echo 'class="active"'; } ?> ><a href="?id=<?php echo $_GET['id'];?>&type=concerns#l">Health Concerns</a></li> -->

                                        <li <?php if ($type=='objective'){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=objective#l">(O)Objective</a></li>

                                        <li <?php if ($type=='assessments'){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=assessments#l">(A)Assessment</a></li>

                                        <li <?php if ($type=='plans'){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=plans#l">(P)Plan</a></li>
                                        
                                        <li style=" width:80px; background-color:#CCCCCC; font-size: 15px;font-weight: bold;padding: 13px 24px;">&nbsp;</li>
                                        
                                        <li <?php if ($type=='images'){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=images#l">Files & Images</a></li>

                                        <li <?php if ($type=='ehr'){ echo 'class="active"'; } ?>><a href="?id=<?php echo $_GET['id'];?>&type=emr#l">EHR</a></li>
                                    </ul> 
                                </div><!--end nav_new-->
                                <br class="clear">
                                <a id="l"></a> <?php 
                                                if ( $type == 'reports' )
                                                    include_once("widget_reports.php");
                                                    
                                                else if ( $type == 'assessments' )
                                                    include_once("widget_assesments-doctors.php");
                                                        
                                                else if ( $type == 'subjective' )
                                                    include_once("widget_subjective.php");
                                                    
                                                else if ( $type == 'objective' )
                                                    include_once("widget_objective.php");
                                                    
                                                else if ( $type == 'images' )
                                                    include_once("widget_images.php");
                                                    
                                                else if ( $type == 'plans' )
                                                    include_once("widget_plans.php");
                                                    
                                                else if ( $type == 'ehr' )
                                                    include_once("widget_doctor_emr.php"); 
                                                     
                                                else
                                                    include_once("widget_subjective.php");
                                                                
                                            ?> <!--End new code-->
                            </div>
                        </div>
                    </div>

                    <div class="create_event_submited"></div>
                </div>
            </div>
        </div><?php include_once('includes/footer.php');?>
    </div>
</body>
</html>
