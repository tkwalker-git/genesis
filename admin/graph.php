<?php

require_once("database.php");

include("phpgraphlib.php");


if($_GET['w'] && $_GET['h']){
	$imgWidth	= $_GET['w'];
	$imgHeight	= $_GET['h'];
	}
else{
	$imgWidth	= 375;
	$imgHeight	= 200;
	}
	
	
$graph=new PHPGraphLib($imgWidth,$imgHeight);



$title		= $_GET['title'];
$dateFrom	= date('d M Y', strtotime($_GET['dateFrom']));
$dateTo		= date('d M Y', strtotime($_GET['dateTo']));

if($_GET['dateFrom'] && $_GET['dateTo']){
//	$title = $dateFrom."  TO  ".$dateTo;
	
	$f_dateFrom	= date('Y-m-d', strtotime($_GET['dateFrom']));
	$f_dateTo	= date('Y-m-d', strtotime($_GET['dateTo']));
	
	$user 			= "&& `memberdate` between '".$f_dateFrom."' and '".$f_dateTo."'";
	$eventDate		= "&& `publishdate` between '".$f_dateFrom."' and '".$f_dateTo."'";
	$soldTicketDate	= "&& `date` between '".$f_dateFrom."' and '".$f_dateTo."'";
	}
	
	$Members	= getSingleColumn('tot',"select count(*) as tot from users where `usertype`='1' ".$user."");
	$Promoters	= getSingleColumn('tot',"select count(*) as tot from users where `usertype`='2' ".$user."");
	$m_events	= getSingleColumn('tot',"select count(*) as tot from events where event_source='User' ".$eventDate."");
	$p_events	= getSingleColumn('tot',"select count(*) as tot from events where event_source='Promoter' ".$eventDate."");
	$actv_events= getSingleColumn('tot',"select count(*) as tot from events where is_expiring=1 ".$eventDate."");

	$res = mysql_query("select * from `orders` where `type`='ticket' && `total_price`!=0 ".$soldTicketDate."");
	$sold = 0;
	while($row = mysql_fetch_array($res)){
		$order_id = $row['id'];
		$sold = getSingleColumn('tot',"select SUM(quantity) as tot from `order_tickets` where `order_id`='$order_id'") + $sold;
	}
	
	$data = array("A"=>$Members, "B"=>$Promoters, "C"=>$m_events, "D"=>$p_events, "E"=>$actv_events, "F"=>$sold); 

	//configure graph
	$graph->addData($data);
	$graph->setTitle($title);
//	$graph->setupXAxis(10, "blue");
	$graph->setDataValues(true);
	$graph->setXValuesHorizontal(TRUE);
	$graph->setGradient("lime", "green");
	// $graph->setupXAxis(0, 'black');
	$graph->setBarOutlineColor("black");
	$graph->createGraph();
	
?>