<?php

ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");



include("include/reportfunctions.php");

$strTableName="";
$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

$chrt_array = getChartArray(postvalue("cname"));

if(is_wr_project())
	include("include/" . $chrt_array['settings']['short_table_name'] . "_variables.php");

	if(!@$_SESSION["UserID"])
	{
		$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
		header("Location: login.php?message=expired");
		return;
	} elseif ( $chrt_array['settings']['status'] == "private" && $chrt_array['settings']['owner'] != @$_SESSION["UserID"] ) {
		echo "<p>You don't have permissions to view this chart</p>";
		exit;
	}

	if (count(GetUserGroups()) > 1)
	{
	    $arr_reports = array();
	    $arr_reports = GetChartsList();
	    foreach ( $arr_reports as $rpt ) {
		    if (( $rpt["owner"] != @$_SESSION["UserID"] || $rpt["owner"] == "") && $rpt["view"]==0 && $chrt_array['settings']['name']==$rpt["name"])
		      {
		       echo "<p>You don't have permissions to view this chart</p>";
		       exit;
		    }
	    }
	}

//	Before Process event
if(tableEventExists("BeforeProcessChart",$strTableName)) 
{
	$eventObj =&getEventObject($strTableName);
	$eventObj->BeforeProcessChart($conn);
}

$show_dchart='<script type="text/javascript" language="javascript">
	//<![CDATA[
	var chart = new AnyChart("libs/swf/AnyChart.swf","libs/swf/Preloader.swf");
	chart.width = "100%";
	chart.height = "100%";

	var xmlFile = "dchartdata.php?cname='.jsreplace(htmlspecialchars(postvalue('cname'))).'";
	xmlFile += "&ctype='.$chrt_array['chart_type']['type'].'";
	chart.setXMLFile(xmlFile);
	chart.write();
	//]]>
</script>';

$load_flash_player = '
<script type="text/javascript">
	$(document).ready(function(){
		var svgSupported = window.SVGAngle != undefined;
		var str="";
		if (!svgSupported)
		{
			str = "<center>";
			str += "You need to have Adobe Flash Player 9 (or above) to view the chart.<br /><br />";
			str += "<a href=\"http://www.adobe.com/go/getflashplayer\"><img border=\"0\" src=\"http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif\" /></a><br />";
			str += "</center>";		
		}
		if (typeof(ActiveXObject) != "undefined") {
			try { a = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");d = a.GetVariable("$version"); }
			catch (e) { d = false; }
			if (!d) {
				$("div.center_div").html( str );	
			}			
		} else if ((navigator.product == "Gecko" && window.find && !navigator.savePreferences)
			|| (navigator.userAgent.indexOf("WebKit") != -1 || navigator.userAgent.indexOf("Konqueror") != -1))
		{
			div = $("div[id*=\'__chart_generated_container__\']");
			if ( div[0] == undefined ) {
				$("div.center_div").html( str );			
			} else {
				$(div).appendTo("div.center_div");
			}
		}
	});
</script>';


$xt->assign("chart_constructor", $show_dchart);
$xt->assign("load_flash_player", $load_flash_player);
$templatefile = "dchart_preview.htm";
$xt->display($templatefile);
?>