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
	
	$eventDate	= "&& `publishdate` between '".$_GET['dateFrom']."' and '".$_GET['dateTo']."'";
	
	$basic		= getSingleColumn('tot',"select count(*) as tot from events where event_type=0 ".$eventDate);
	$featured	= getSingleColumn('tot',"select count(*) as tot from events where event_type=1 ".$eventDate);
	$premium	= getSingleColumn('tot',"select count(*) as tot from events where event_type=2 ".$eventDate);

	$data = array("Basic"=>$basic, "Featured"=>$featured, "Premium"=>$premium); 

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