<?php include_once('admin/database.php'); 


 $centerid=31;
 
 $res = mysql_query("select * from `request_appt` where `id`='". $centerid ."'");
 while($get_val = mysql_fetch_array($res)){
 $pid 	 = $get_val['patient_id'];
 $cid	 = $get_val['clinic_id'];
 $sdate	 = $get_val['date_requested'];
 $stime	 = $get_val['time_requested'];
 $reason = $get_val['reason'];
 $rdate	 = $get_val['date_created'];

     echo  $cbyd ="select * from `schedule_patient` where `patient_id`='".$pid."',`clinic_id`='".$cid."'";
 
 	 $chk_sdf = mysql_query($cbyd);
	 
	 echo $have_alr_sch = mysql_num_rows($chk_sdf);
    
	if($have_alr_sch){ 
	 $sql_date	=	mysql_query("insert into schedule_dates (patient_id,clinic_id,cons_date,start_time, end_time) values('" . $pid . "','" . $cid . "','" . $sdate . "','" . $stime . "','" . $endTime . "')"); 
 	
	 }
	 else {
	  $sql	=	"insert into schedule_patient (patient_id,clinic_id,comments,date) values ('" . $pid . "','" . $cid . "','" .  $reason . "','" . $rdate . "')";		 
	 $res	=	mysql_query($sql);
	 
	  $sql_date	=	mysql_query("insert into schedule_dates (patient_id,clinic_id,cons_date,start_time, end_time) values('" . $pid . "','" . $cid . "','" . $sdate . "','" . $stime . "','" . $endTime . "')"); 
	  
	 }
	$stats_pat	=	 getSingleColumn("status","select * from `patients` where `id`='".$pid."'");
	
	if($stats_pat==1){
		mysql_query("update patients set status='2' where id='".$pid."'");
	}
	
	//mysql_query("delete from `request_appt` where `id`='$centerid'");
 }

echo "success";
 


?>

