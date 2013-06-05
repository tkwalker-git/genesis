<?php
if(isset($_POST['occurrences'])){
if(is_array($_POST['occurrences'])!=''){
$oldArray	=	array();
$occurrences	=	$_POST['occurrences'];
$oldArray[]	=	"empty";
foreach ($occurrences as $k => $v) {


$date[]			=	$v['date'];
$start_time[]	=	$v['start_time'];
$start_am_pm[]	=	$v['start_am_pm'];  // AM = 0, PM = 1
// $end_time[]		=	$v['end_time'];
// $end_am_pm[]	=	$v['end_am_pm'];	// AM = 0, PM = 1
}

for($i=0;$i<count($date);$i++){

////// FOR START & END Time //////
$startTime	=	'';
$endTime	=	'';
$startTime = $start_time[$i];
if($start_am_pm[$i]==0)
$startTime .= " AM";
else
$startTime .= " PM";
$startTime = date("H:i", strtotime($startTime));

//$endTime = $end_time[$i];
//if($endTime!=''){
//if($end_am_pm[$i]==0)
//$endTime .= " AM";
//else
//$endTime .= " PM";
//$endTime = date("H:i", strtotime($endTime));
//}

if($endTime!="00:00" && $endTime!="" && $startTime>=$endTime){
echo "The end time you have selected for one or more occurrences occurs before the start time. You must set an end time that occurs after the set start time.";
return false;
}

if($startTime=='00:00'){
echo "Please enter dates and times in the proper format";
return false;
}

////// FOR Same DATE & TIME //////


if(array_search($date[$i], $oldArray)){
$duplicatedId	=	array_search($date[$i], $oldArray);
$duplicatedId	=	$duplicatedId-1;
/////// For Get Duplicated Entry START Time ///////////
$startTimeD = $start_time[$duplicatedId];
if($start_am_pm[$duplicatedId]==0)
$startTimeD .= " AM";
else
$startTimeD .= " PM";

$startTimeD = date("H:i", strtotime($startTimeD));
////////////////////////////////////////////////


if($startTime==$startTimeD){
echo "You cannot have multiple occurrences at the same exact date and time.";
return false;
}
}

$oldArray[]	=	$date[$i];

}
//print_r($oldArray);


//if(in_array($value, $date_array)==1){
//
////echo $value;
//}
//
//$date_array[]	=	$value;


}
}
?>