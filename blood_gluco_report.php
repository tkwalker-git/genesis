<?php
	$bc_patient_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
	///Cureent Month
	$first_date  	 	= date('Y-m-01') ;
    $last_date    	 	=  date('Y-m-t');
	$query_filter_top = "AND add_date>='".$first_date."' AND add_date<='".$last_date."'";
	$sql	=	"select * from blood_gluco where patient_id='".$bc_patient_id."' $query_filter_top";
	$res	=	mysql_query($sql);
?>
<style>
.whiteMiddle .evField {
	
	}

.whiteMiddle .evField {
	text-align:left;
	font-size:15px;
	width:100px;
	}
.evLabal{
	font-size:15px;
	width:170px!important;
	}
	
.evInput{
	font-size:14px;
	}
	
</style>
    <style type="text/css">
.ew-heading{
	color: #49BA8D;
    font-size: 24px;}
	
.ew-heading a{
	color: #FF7A57;
    float: right;
    font-size: 14px;
	text-decoration:underline;}

.ew-heading-behind{
	color: #6EB432;
    font-size: 24px;}

.ew-heading-behind span{}

.ew-heading-a{
	color: #212121;
    font-size: 20px;}
.iconimg{
	padding-top:8px;}
.save_button{
	}
#dialog-confirm{
	display:none;}
.h_over tr{
	color:#666;
	font-weight:bold;
	height:30px;
	}	
.h_over tr:hover{
	color:#2F7EC9;
	background-color:#FFD300;
	cursor:pointer;
	}
.h_over td{
	padding-left:5px;
	}	
.log_heading{
	height:35px;
	color:#2F7EC9;
	font-weight:bold;}
.log_details{
	height:30px;
}
.select_left_filter{
	background-color:#3274AD;
	color:#FFF !important;
	}
.date_filte_class{
	color:#666;
	font-weight:bold;
}
.date_filte_class td:hover{
	color:#FFF !important;
	background-color:#E07105;
	cursor:pointer;
}
.select_top_filter{
	background-color:#E07105;
	color:#FFF !important;
	}
.bk1{
	background-color:#F5F8FB;
}
.bk2{
	background-color:#FFF;
}
.heading_green{
	font-size:16px;
	color:#A1B731;
	font-weight:bold;
	}
.heading_green1{
	font-size:14px;
	color:#A1B731;
	font-weight:bold;
	}
.glo_summery_h td{
	background-color:#DDE6F0;
	color:#2D71AB;
	font-weight:bold;
	padding:10px;
	}
.glo_summery_d td{
	background-color:#F1F5F9;
	color:#0000;
	font-weight:bold;
	padding:10px;
	}

</style>
    
    
<div class="yellow_bar"> &nbsp;Reporting</div>
<div class="editProox" style="width:700px; float:left; padding:0 10px;">
	<table width="100%" cellpadding="2" cellspacing="0" border="0" align="right" class="print_edit">
		<tr><td valign="top"><span style="float:left;"><a href="javascript:void(0)" onclick="editType()">Edit Your Type</a></span><span style="float:right;"></span><span style="float:right;"><a href="javascript:void(0)" onclick="printElem();"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/print.png" /> Print this View</a></span></td></tr></table>
	</div>
	<div class="clr"></div>
<div style="padding:0 5px;"><br />
	<div class="clr"></div>
	<div class="editProox" style="width:700px; min-height:500px;float:left;" id="toPrint">
    
    <table width="100%" cellpadding="2" cellspacing="0" border="0">
            <tr class="heading_green">
                <td><span style="float:left;">Your 30 Days Blood Glucose Report</span><span style="float:right;"><?php echo date('m/d/y',strtotime($first_date))."&nbsp;-&nbsp;".date('m/d/y',strtotime($last_date));?></span></td>
            </tr>
           <?php 
		   $total_records = NULL;
		   $toal_reading = 0;
		   if(mysql_num_rows($res)>0){
			   unlink('generated_graph/glucose.png');
			   include "libchart/libchart/classes/libchart.php";
				$chart = new LineChart(650,300);
				$serie1 = new XYDataSet();
			while ($row = mysql_fetch_assoc($res) ) {
				$bc_patient_id		=	$row["patient_id"];
				$bc_add_date		=	date('d/m/Y',strtotime($row["add_date"]));
				$bc_add_time		=	$row["add_time"];
				$bc_reading			=	$row["reading"];
				$bc_meal			=	$row["meal"];
				$eating_out			=	$row["eating_out"];
				$exercise			=	$row["exercise"];
				$stress				=	$row["stress"];
				$fever				=	$row["fever"];
				$vomiting			=	$row["vomiting"];
				$bc_notes	=	$row["notes"];
				$toal_reading = $toal_reading +$bc_reading;
				$serie1->addPoint(new Point(" ", $bc_reading));
				$total_records++;
			} 
			$dataSet = new XYSeriesDataSet();
			$dataSet->addSerie("Glucose Report", $serie1);
			$chart->setDataSet($dataSet);
			$chart->setTitle("Monthly Glucose Report");
			$chart->render("generated_graph/glucose.png");
			$avg_glucose = $toal_reading/$total_records;
			$min_glucose = getSingleColumn("min_glo","select MIN(reading) as min_glo from blood_gluco where patient_id='".$bc_patient_id."' $query_filter_top");
			$max_glucose = getSingleColumn("max_glo","select MAX(reading) as max_glo from blood_gluco where patient_id='".$bc_patient_id."' $query_filter_top");
			if($max_glucose>130)
				$max_glucose = '<b style="color:#D05600">'.$max_glucose.'</b>&nbsp;mg/dl&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/fever_up.png"/>';
			else
				$max_glucose = '<b >'.$max_glucose.'</b>&nbsp;mg/dl';	
			if($min_glucose<70)
				$min_glucose = '<b style="color:#D05600">'.$min_glucose.'</b>&nbsp;mg/dl&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/fever_down.png"/>';
			else
				$min_glucose = '<b >'.$min_glucose.'</b>&nbsp;mg/dl';	
				
					
			?>
<!--------Monthly Graph Summery----------------------->              
           
            <tr>
                <td>&nbsp;</td>
            </tr>
<?php            
$check = getSingleColumn("type","select type from blood_gluco_setting where patient_id='".$bc_patient_id."'"); 
if($check=='1' || $check=='' || $check=='0')
	$text_Type = '1';
elseif($check=='2')
	$text_Type = '2';
?>       <tr>
                <td class="heading_green1">Insufficient Data (Type <?php echo $text_Type;?>)</td>
            </tr>
            
            <tr>
                <td>
                	<table width="100%" cellpadding="2" cellspacing="2" border="0" style="border:1px #DCE5F0 solid;; border-radius:5px;">
                    	<tr>
                        	<td>
                            	<div class="content">
<p>At this point, you have not provided enough blood sugar readings for us to generate a monthly report for you. To get the most benefit from this tool, you should enter all of your readings. It is important for those with type 1 diabetes to check their blood sugar before meals and before bed each day. However, you many need to check your blood sugar more often if you:</p>
<ul>
<li>Have trouble controlling your blood sugar</li>
<li>Are pregnant</li>
<li>Are ill</li>
<li>Have made a recent change to your therapy</li>
<li>Have a hard time feeling a low blood sugar</li>
<li>Have ketones from a high blood sugar</li>
</ul>
<p>Talk to your health care provider to determine how often you should check your blood sugar. You may also want to discuss any obstacles that keep you from testing. For example, if you find that pricking your finger is painful, your provider can show you how to adjust the depth of the lancet for your fingerstick, rotate sites, or possibly test on your arm or other sites. Remember to take your glucose meter everywhere. Although using the same meter all the time is preferred, you may want to consider a smaller one that you can keep in your purse or briefcase.</p>
<p>Once you've increased the frequency of your blood sugar testing, use this tool to track trends that may help you and your provider manage your blood sugar levels.</p>
</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
           
   			 <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="heading_green1">Monthly Graph</td>
            </tr>
            <tr>
                <td>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px #DCE5F0 solid;; border-radius:5px;">
                	<tr><td>
                	<img src="<?php echo ABSOLUTE_PATH;?>generated_graph/glucose.png" /></td>
                    </tr>
                </table>   
                 </td>
            </tr>
<!--------Blood Glucose Summery----------------------->              
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="heading_green1">Blood Glucose Summery</td>
            </tr>
            <tr>
                <td>
                <table width="100%" cellpadding="2" cellspacing="2" border="0" style="border:1px #DCE5F0 solid;; border-radius:5px;">
                	<tr class="glo_summery_h">
                    	<td>Average</td>
                        <td>Minimun</td>
                        <td>Maximum</td>
                        <td>% &nbsp;Abnormal</td>
                    </tr>
                    <tr class="glo_summery_d">
                    	<td><?php echo "<b>".number_format($avg_glucose,2)."</b>&nbsp;mg/dl";?></td>
                        <td><?php echo  $min_glucose;?></td>
                        <td><?php echo	$max_glucose;?></td>
                        <td><?php echo  $abn_glucose;?></td>
                    </tr>
                </table>
                </td>
            </tr>
<!--------Before/After Meal Summery----------------------->              
             <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="heading_green1">Summery of Before/After Meal Reading</td>
            </tr>
            <tr>
                <td>
                <table width="100%" cellpadding="2" cellspacing="2" border="0" style="border:1px #DCE5F0 solid;; border-radius:5px;">
                	<tr class="glo_summery_h">
                    	<td style="background:#F1F5F9 !important;">&nbsp;</td>
                    	<td>Before Meal</td>
                        <td>After Meal</td>

                    </tr>
                    <tr class="glo_summery_d">
                    	<td style="background:#DDE6F0 !important; color:#2D71AB !important;">Above Target</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="glo_summery_d">
                    	<td style="background:#DDE6F0 !important; color:#2D71AB !important;">Below Target</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                </td>
            </tr>
<!--------Glucose Factor Summery----------------------->            
             <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="heading_green1">Summery by Glucose Factor</td>
            </tr>
            <tr>
                <td>
                <table width="100%" cellpadding="2" cellspacing="2" border="0" style="border:1px #DCE5F0 solid;; border-radius:5px;">
                	<tr class="glo_summery_h">
                    	<td style="background:#F1F5F9 !important;">&nbsp;</td>
                    	<td style="background:#DFD0C2 !important;"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/eatout.png"/> &nbsp; Eating Out</td>
                        <td style="background:#C6D8E6 !important;"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/excercise.png"/>&nbsp;Excercise</td>
                        <td style="background:#F0CFBB !important;"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/stress.png"/>&nbsp;Stress</td>
                        <td style="background:#EDC0C5 !important;"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/fever.png"/>&nbsp;Fever</td>
                        <td style="background:#DEE2C4 !important;"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/vomit.png"/>&nbsp;Vomting</td>

                    </tr>
                    <tr class="glo_summery_d">
                    	<td style="background:#DDE6F0 !important; color:#2D71AB !important;">Above Target</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        
                    </tr>
                    <tr class="glo_summery_d">
                    	<td style="background:#DDE6F0 !important; color:#2D71AB !important;">Below Target</td>
                         <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                </td>
            </tr>
<!--------Log Summery----------------------->
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="heading_green1">Log</td>
            </tr>
            <tr>
                <td >
   			 		<table width="100%" cellpadding="2" cellspacing="0" border="0" style="border:1px #DCE5F0 solid;; border-radius:5px;">
                    <tr class="log_heading">
                        <td width="30%">Date/Time</td>
                        <td width="10%">Glucose</td>
                        <td width="10%">Meal</td>
                        <td width="25%">Glucose Factor</td>
                        <td width="25%">Notes</td>
                    </tr>
                   <?php 
				    $sql_new	=	"select * from blood_gluco where patient_id='".$bc_patient_id."' $query_filter_top";
					$res_new	=	mysql_query($sql_new);
                    $no = 1;
                    while ($row = mysql_fetch_assoc($res_new) ) {
                        $bc_patient_id		=	$row["patient_id"];
                        $bc_add_date		=	date('d/m/Y',strtotime($row["add_date"]));
                        $bc_add_time		=	$row["add_time"];
                        $bc_reading			=	$row["reading"];
                        $bc_meal			=	$row["meal"];
                        $eating_out			=	$row["eating_out"];
                        $exercise			=	$row["exercise"];
                        $stress				=	$row["stress"];
                        $fever				=	$row["fever"];
                        $vomiting			=	$row["vomiting"];
                        $bc_notes	=	$row["notes"];
                        //Reading
                        if($bc_reading>130)
                            $bc_glucose = '<b style="color:#D05600">'.$bc_reading.'</b>&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/fever_up.png"/>';
                        elseif($bc_reading<70)
							$bc_glucose = '<b style="color:#D05600">'.$bc_reading.'</b>&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/fever_down.png"/>';
						else	
							$bc_glucose = '<b>'.$bc_reading.'</b>';
                        ///Meal
                        if($bc_meal=='1')
                                $meal = 'Before';
                        elseif($bc_meal=='2')
                                $meal = 'After';
                        
                        //factors
                        if($eating_out=='1')
                            $eating_out = '<img src="'.ABSOLUTE_PATH.'images/icon/eatout.png" title="Eating Out"/>';
                        else
                            $eating_out = '';
                        //	
                        if($exercise=='1')
                            $exercise = '&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/excercise.png" title="Excercise"/>';
                        else
                            $exercise = '';
                        //
                        if($stress=='1')
                            $stress = '&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/stress.png" title="Stress"/>';
                        else
                            $stress = '';
                        //
                        if($fever=='1')
                            $fever = '&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/fever.png" title="Fever"/>';
                        else
                            $fever = '';
                        //
                        if($vomiting=='1')
                            $vomiting = '&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/vomit.png" title="Vomiting"/>';
                        else
                            $vomiting = '';		
                    ?>
                      <tr  class="log_details <?php if($no&1){?>bk2<?php }else{?>bk1<?php }?>">
                        <td><?php echo "<b>".$bc_add_date."</b>&nbsp;".$bc_add_time;?></td>
                        <td><?php echo $bc_glucose;?></td>
                        <td><?php echo $meal;?></td>
                        <td><?php echo $eating_out.$exercise.$stress.$fever.$vomiting;?></td>
                        <td><?php echo $bc_notes;?></td>
                       
                    </tr>
                    <?php $no++;
                    } ?>
            
            </table>
   
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
     		<?php }else{?>
            <tr><td align="center">&nbsp;</td></tr>
            <tr><td align="center" class="alert-error alert"><b>Sorry no record exist!</b></td></tr>
            <?php }?>
           </table>
           
 	  </div>	

</div>
