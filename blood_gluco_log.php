<?php
	$bc_patient_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
	///Cureent Month
	$first_date  	 	= date('Y-m-01') ;
    $last_date    	 	=  date('Y-m-t');
	//Last 3 Month
	$last3m_start			= date("Y-m-d", mktime(0, 0, 0, date('m') - 3, date('d'), date('Y')));
	$last3m_end				= date("Y-m-d", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
	///Current Week
		$dayofweek		= date("N");
		$start			= $dayofweek - 1;
		$end			= 7- $dayofweek ;
		$start1			= strtotime("now") - $start * 86400;
		$end1			= strtotime("now") + $end * 86400;
		$week_start		= date('Y-m-d',$start1);
		$week_end 		= date('Y-m-d',$end1);
	//Filter Setting
	if($_GET['filter_left']!=''){
		$filter_left = $_GET['filter_left'];
		if($filter_left=='all'){
			$query_filter_left = " ";
			}
		elseif($filter_left=='before_m'){
				$query_filter_left = "AND meal='1'";
			}
		elseif($filter_left=='after_m'){
				$query_filter_left = "AND meal = '2'";
			}
		elseif($filter_left=='eating_out'){
				$query_filter_left = "AND eating_out = '1'";
			}
		elseif($filter_left=='exercise'){
				$query_filter_left = "AND exercise = '1'";
			}
		elseif($filter_left=='stress'){
				$query_filter_left = "AND stress = '1'";
			}
		elseif($filter_left=='fever'){
				$query_filter_left = "AND fever = '1'";
			}
		elseif($filter_left=='vomiting'){
				$query_filter_left = "AND vomiting = '1'";
			}
	}
	else{
		$filter_left = 'all';
		}
	if($_GET['filter_top']!=''){
			$filter_top = $_GET['filter_top'];
			if($filter_top=='w'){
				$query_filter_top = "AND add_date>='".$week_start."' AND add_date<='".$week_end."'";
			}
			elseif($filter_top=='m'){
				$query_filter_top = "AND add_date>='".$first_date."' AND add_date<='".$last_date."'";
			}
			elseif($filter_top=='three_m'){
				$query_filter_top = "AND add_date>='".$last3m_start."' AND add_date<='".$last3m_end."'";
			}
		}
	else{
		$filter_top = 'm';
		$query_filter_top = "AND add_date>='".$first_date."' AND add_date<='".$last_date."'";
		}
	$sql	=	"select * from blood_gluco where patient_id='".$bc_patient_id."' $query_filter_left $query_filter_top";
	$res	=	mysql_query($sql);
?>
<style type="text/css">
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
.log_details:hover{
	background-color:#F5FDD3;
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
.hideAll{
	display:none;
	cursor:pointer;
	}
#dialog-confirm{
	display:none;
}
#dialog{
	display:none;
}
</style>


<div class="yellow_bar"> &nbsp;View Log</div>
<div id="dialog-confirm" title="Confirm Deletion">
	  <span class="ui-icon ui-icon-alert" style="float:left; margin-left:0px;"></span>&nbsp;Are you sure you want to delete the selected reading? This action cannot be undone.
</div>
<div style="padding:0 10px;">
	<div class="editProox" style="width:700px; float:left; padding:0 10px;">
	<table width="100%" cellpadding="2" cellspacing="0" border="0" align="right" class="print_edit">
		<tr><td valign="top"><span style="float:left;">&nbsp;</span><span style="float:right;"></span><span style="float:right;"><a href="javascript:void(0)" onclick="printElem();"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/print.png" /> Print this View</a></span></td></tr></table>
	</div>
	<div class="clr"></div>
	<div class="editProox" style="width:680px; float:left;padding:10px;">
   		<table width="30%" cellpadding="2" cellspacing="0" border="0" align="right" class="date_filte_class">
        	<tr>
            	<td width="10%" align="center" id="w" onclick="filterTop(this.id);" <?php if($filter_top=='w') echo 'class="select_top_filter"';?>>Week</td>
                <td width="10%" align="center" id="m" onclick="filterTop(this.id);" <?php if($filter_top=='m') echo 'class="select_top_filter"';?>>Month</td>
                <td width="10%" align="center" id="three_m" onclick="filterTop(this.id);" <?php if($filter_top=='three_m') echo 'class="select_top_filter"';?>>3-Month</td>
            </tr>
        </table>
    </div>
    <div class="clr"></div>
	<div class="editProox" style="width:120px; min-height:500px; float:left;border:1px #DCE5F0 solid;; border-radius:5px;">
   	<table width="100%" cellpadding="2" cellspacing="0" border="0" class="h_over">
    	<tr id="all" onclick="filterLeft(this.id);" <?php if($filter_left=='all') echo 'class="select_left_filter"';?>>
         	<td colspan="2">All</td>

         </tr>
         <tr id="before_m" onclick="filterLeft(this.id);" <?php if($filter_left=='before_m') echo 'class="select_left_filter"';?>>
         	<td colspan="2">Before Meal</td>

         </tr>
         <tr id="after_m" onclick="filterLeft(this.id);" <?php if($filter_left=='after_m') echo 'class="select_left_filter"';?>>
         	<td colspan="2">After Meal</td>

         </tr>
	  	 <tr id="eating_out" onclick="filterLeft(this.id);" <?php if($filter_left=='eating_out') echo 'class="select_left_filter"';?>>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/eatout.png"/></td>
            <td>Eating Out</td>
         </tr>
         <tr id="exercise" onclick="filterLeft(this.id);" <?php if($filter_left=='exercise') echo 'class="select_left_filter"';?>>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/excercise.png"/></td>
            <td>Exercise</td>
         </tr>
         <tr id="stress" onclick="filterLeft(this.id);" <?php if($filter_left=='stress') echo 'class="select_left_filter"';?>>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/stress.png" /></td>
            <td>Stress</td>
         </tr>
         <tr id="fever" onclick="filterLeft(this.id);" <?php if($filter_left=='fever') echo 'class="select_left_filter"';?>>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/fever.png"/></td>
            <td>Fever</td>
         </tr>
         <tr id="vomiting" onclick="filterLeft(this.id);" <?php if($filter_left=='vomiting') echo 'class="select_left_filter"';?>>
         	<td><img src="<?php echo ABSOLUTE_PATH;?>images/icon/vomit.png" /></td>
            <td>Vomiting</td>
         </tr>

        </table>
	</div>
	<div style="width:5px; min-height:500px; float:left;"></div>
	<div class="editProox" style="width:565px; min-height:500px;float:left; padding-left:5px; border:1px #DCE5F0 solid;; border-radius:5px;" id="toPrint">

    <table width="100%" cellpadding="2" cellspacing="0" border="0">
            <tr class="log_heading">
                <td width="25%">Date/Time</td>
                <td width="10%">Glucose</td>
                <td width="10%">Meal</td>
                <td width="25%">Glucose Factor</td>
                <td width="20%">Notes</td>
                <td width="10%">&nbsp;</td>
         	</tr>
           <?php if(mysql_num_rows($res)>0){
			$no = 1;
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
				
				$bg_min_range		=	$row["bg_min_range"];
				$bg_max_range		=	$row["bg_max_range"];
				
				$bc_notes			=	$row["notes"];
				//Reading
				if($bc_reading > $bg_max_range)
					$bc_glucose = '<b style="color:#D05600">'.$bc_reading.'</b>&nbsp;<img src="'.ABSOLUTE_PATH.'images/icon/fever_up.png"/>';
				elseif($bc_reading < $bg_min_range)
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
              <tr  class="log_details <?php if($no&1){?>bk2<?php }else{?>bk1<?php }?>" onmouseover="showDisplay(<?php echo $row['id'];?>);" id="overColor<?php echo $row['id'];?>" onmouseout="hideThis(this.id);">
                <td><?php echo "<b>".$bc_add_date."</b>&nbsp;".$bc_add_time;?></td>
                <td><?php echo $bc_glucose;?></td>
                <td><?php echo $meal;?></td>
                <td><?php echo $eating_out.$exercise.$stress.$fever.$vomiting;?></td>
                <td><?php echo $bc_notes;?></td>
                <td>
                   <img id="delR<?php echo $row['id'];?>" class="hideAll" src="<?php echo ABSOLUTE_PATH;?>images/icon/del_record.png" title="Delete" onclick="Delete(<?php echo $row['id'];?>)"/>&nbsp;
                   <a id="editR<?php echo $row['id'];?>" class="hideAll" href="blood_gluco.php?id=<?php echo $row['id'];?>"><img src="<?php echo ABSOLUTE_PATH;?>images/icon/edit_record.png" title="Edit"/></a>
                 </td>
         	</tr>
            <?php $no++;
			} }else{?>
            <tr><td colspan="6" align="center">&nbsp;</td></tr>
            <tr><td colspan="6" align="center" class="alert-error alert"><b>Sorry no record exist!</b></td></tr>
            <?php }?>

            </table>

 	  </div>

</div>
<script type="text/javascript">
function filterLeft(id){
	<?php if(isset($_GET['filter_top']) && $_GET['filter_top']!=''){?>
	 window.location.href = '<?php echo ABSOLUTE_PATH;?>blood_gluco.php?p=log&filter_left='+id+"&filter_top=<?php echo $_GET['filter_top'];?>";
	 <?php }else{?>
	 	 window.location.href = '<?php echo ABSOLUTE_PATH;?>blood_gluco.php?p=log&filter_left='+id;
	 <?php }?>
	}
function filterTop(id){
	<?php if(isset($_GET['filter_left']) && $_GET['filter_left']!=''){?>
	 window.location.href = '<?php echo ABSOLUTE_PATH;?>blood_gluco.php?p=log&filter_top='+id+"&filter_left=<?php echo $_GET['filter_left'];?>";
	 <?php }else{?>
	 	 window.location.href = '<?php echo ABSOLUTE_PATH;?>blood_gluco.php?p=log&filter_top='+id;
	 <?php }?>
	}
</script>
<script type="text/javascript">
function showDisplay(id){
	$('.hideAll').hide();
	 $('#delR'+id).show();
	 $('#editR'+id).show();
	 }
function hideThis(id){
	$('.hideAll').hide();
	}
</script>
<script type="text/javascript">
function Delete(id){
	$(function() {
             	$( "#dialog-confirm" ).dialog({
					  resizable: false,
					  height:160,
					  modal: true,
					  buttons: {
						"Delete": function() {
						  $( this ).dialog( "close" );
						  $.ajax({
							url: "blood_gluco_ajex.php?id="+id,
							type: "GET",
							success: function(data){
								if(data=='1'){
									window.location.href = 'blood_gluco.php?p=log';
								}
								else if(data == '0'){
									alert("Error: Please try again!")
								}
							}
							});
						},
						"Cancel.": function() {
						  $( this ).dialog( "close" );
						}
					  }
					});
			 });

	}
</script>
