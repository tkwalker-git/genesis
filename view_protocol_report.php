<?php
	include_once('admin/database.php'); 
	include_once('site_functions.php');

	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
		if($_GET["id"]){	
			if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'protocols',$_GET["id"]) =='false'){
				echo "<script>window.location.href='clinic_manager.php?p=protocols';</script>";
				}
		}

	

	$meta_title	= "Protocol Report";

	

?>



	<link rel="shortcut icon" href="<?php echo ABSOLUTE_PATH; ?>images/favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>style.css"/>

<style>
	#acco h3 {
		border-bottom: 1px solid #89C76F;
		border-radius: 5px 5px 5px 5px;
		color: #FFFFFF;
		cursor: pointer;
		background: none repeat scroll 0 0 #43BB9A;
		font-size: 18px;
		font-weight: bold;
		margin: 0;
		padding: 10px;
	}
	h3 {
	font-size:20px;
	}
	h4 {
	font-size:15px;
	padding:2px 0 2px 0;
	margin:0;
	text-decoration:underline;
	}
	h5 {
	font-size:11px;
	padding:5px 0 5px 0;
	margin:0;
	}
	.lst {	
	}
	.lst ul {
	padding:5px 0 5px 20px;
	margin:0;
	}
	.lst ul li {
	padding:2px 0 2px 0;
	margin:0;
	}
	.supp_box {
	margin-top:10px;
	line-height:20px;
	}
</style>
<div class="topContainer">
<div class="logo">
	<?php if($_SESSION['LOGGEDIN_MEMBER_ID']){?>
	<a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>"><img src="<?php echo ABSOLUTE_PATH.logo(); ?>" alt="" border="0"  /></a>
	<?php } ?>
</div>
<br /><br /><br />
<div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Protocol Report</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
    
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="" style="padding-left:20px;">
			  <?php if($_GET["id"]){		
					$frmID=$_GET["id"];
					$sql=mysql_query("select * from protocols where id='".$frmID."'");
					while($row=mysql_fetch_array($sql)){
						$protocols_name	 =	$row['protocols_title'];
						?>
			
		 		 <h3> <?php echo $plan_name; ?></h3>
				 
				 <?php 				 
				 $sql1=mysql_query("select * from `plan_protocol` where `protocol_id`='".$frmID."'");
				 while($row1=mysql_fetch_array($sql1)){
				 $proto_id		=	 $row1['protocol_id'];
				 
				 $sql2=mysql_query("select * from `protocols` where id='".$proto_id."'"); 
				 while($row2=mysql_fetch_array($sql2)){?>
				 
				 <div id="box" class="box">
                  <h4><?php echo $row2['protocol_title']; ?></h4>  
				   <h5>LifeStyle Changes</h5>
				   <div class="lst"><?php echo $row2['lifestyle_changes'];  ?></div>				   
				   <h5>Dietary Changes</h5>
				   <div class="lst"><?php echo $row2['dietary_changes'];  ?></div>				                   
                </div>				 
				 <?php }// end protocols loop
				 }// end plan_protocols loop			 
				 ?>
			
				 
				 
				  <h4 style="margin-top:10px;">Supplements</h4>
				   <div class="supp_box"> <?php 				 
				 $sql4	=	mysql_query("select * from `protocol_supplements` where `protocol_id`='".$frmID."'");
				 while($row4=mysql_fetch_array($sql4)){
				  $supp_id=$row4['supplement_id'];
				  
				 $sql3	=	mysql_query("select * from `supplement` where id='".$supp_id."'"); 
				 while($row3=mysql_fetch_array($sql3)){?>				
				
				 <strong><?php echo $row3['supplement_name']; ?></strong> : &nbsp;&nbsp;<?php echo $row3['dosage'];  ?><br />
				 
				 			 
				<?php  } } ?>                
				</div>		
					
				
				
				
				<?php  // end protocol loop
				}// end plan loop
				}// end if ?>
              
				
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
            <input type="image" src="<?php echo IMAGE_PATH; ?>print_new.png" onclick="window.print();" name="submit" value="Create Test" align="right" /><br /><br /><br />
            
        </div>

    </div>
  </div>
</div>

