<?php include_once('admin/database.php'); 

if($_POST) {

 $centerid=$_POST['centerid'];
 
 $abc=explode('_',$centerid);
 $val1=$abc[0];
 $val2=$abc[1];
 

 
  $get_dts = mysql_query("select * from schedule_dates where id='".$val1."'");
 while($row = mysql_fetch_array($get_dts)){
  $patient_id     = $row['patient_id'];
 $clinic_id      = $row['clinic_id'];
 $start_time     = $row['start_time'];
 $cons_date      = $row['cons_date'];
 $end_time       = $row['nd_time'];
 
 	$sdf=mysql_query("select * from `schedule_dates_status` where `patient_id`='".$patient_id."' && `clinic_id`='".$clinic_id."' && `cons_date`='".$cons_date."'");
	$hav_ghr = mysql_num_rows($sdf);
	
	
	
	if($hav_ghr){
	while($gidpc = mysql_fetch_array($sdf)){
	$idpc = $gidpc['id'];
	 $cvye="update `schedule_dates_status` set patient_id ='".$patient_id."',clinic_id ='".$clinic_id."',cons_date = '".$cons_date."',start_time ='".$start_time."', end_time ='".$end_time."', status ='".$val2."' where id='".$idpc."'";
	 $sql_date	=	mysql_query($cvye);
	}
	}
	else {
	 $vkyd = "insert into `schedule_dates_status` (patient_id,clinic_id,cons_date,start_time, end_time, status) values('" . $patient_id . "','" . $clinic_id. "','" . $cons_date . "','" . $start_time . "','" .$end_time . "', '".$val2."')";
	 $sql_date	=	mysql_query($vkyd);
	}

 
 }

 
}
?>

