<?php
	require_once('admin/database.php');
	require_once('site_functions.php');
	header("Content-type:text/xml");
	ini_set('max_execution_time', 600);
	$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	 $qry	= "select sp.patient_id, sp.clinic_id, sd.start_time, sd.end_time, sd.cons_date from  schedule_patient sp, schedule_dates sd where sp.patient_id=sd.patient_id && sp.clinic_id=sd.clinic_id && sp.clinic_id=$member_id && sd.clinic_id=$member_id";
	 
	  
	 $res	= mysql_query($qry);
	echo  "<data>\n";
	 $i	= 3047;
	while($row = mysql_fetch_array($res)){
		$i++;
		if($row['end_time'] == '' || $row['end_time'] == '00:00:00' || $row['end_time'] == '00:00'){
			$end_time = $row['start_time'];
		}
		else{
			$end_time = $row['end_time'];
		}
		 $bc_patient_id=$row['patient_id'];
		/* $patient_name	= getSingleColumn("username","select * from `patients` where `id`='$bc_patient_id'"); */
		$member_full_name = attribValue('patients', 'concat(firstname," ",lastname)', "where `id`='$bc_patient_id'");
		
		echo  "<event id='".$i."' start_date='".$row['cons_date']." ".$row['start_time']."' end_date='".$row['cons_date']." ".$end_time."' text='".$member_full_name."' />\n";
	}
	echo "</data>";
?>
