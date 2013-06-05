<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

include("include/reportfunctions.php");

	if(!@$_SESSION["UserID"])
	{
		$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
		header("Location: login.php?message=expired");
		return;
	}
if(is_wr_project())
	include("include/" . GetTableURL( $_SESSION['webcharts']['tables'][0] ) . "_variables.php");

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

if (@$_SESSION['webcharts']['settings']['title'] != "") {
	$title=@$_SESSION['webcharts']['settings']['title'];
	if(strlen($title)>25)
		$title=substr($title,25)."...";
	$xt->assign("chart_title",", Title: ".$title);
} else {
	$xt->assign("chart_title","");
}
if (@$_SESSION['webcharts']['tables'][0] != "") {
	$stable=@$_SESSION['webcharts']['tables'][0];
	if(strlen($stable)>25)
		$stable=substr($stable,25)."...";

	$xt->assign("chart_table",", Table: ".$stable);
} else {
	$xt->assign("chart_table","");
}

$xt->assign("b_is_chart_save",($_SESSION['webcharts']['settings']['name'] != ""));
$xt->assign("chart_name",$_SESSION['webcharts']['settings']['name']);

$h_includes = "";
$b_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/dstyle.css" type="text/css">
	<link rel="stylesheet" href="include/style.css" type="text/css">
	<link rel="stylesheet" href="include/css/jquery-ui.css" type="text/css" media="screen">
	
	<script type="text/javascript" src="include/js/jquery.min.js"></script>
	<script type="text/javascript" src="include/js/jquery.dimensions.pack.js"></script>
	<script type="text/javascript" src="include/js/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/json.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$b_includes .= '
<script type="text/javascript">
var timeout	= 200,
	closetimer	= 0;

$(document).ready(function(){
	
	';
	$b_includes .= alertDialog();
	$b_includes .= '
	
	function collect_input_data() {
		var output = {
			settings : {
				name   : $("#cname").val().replace(/\W/g, "_"),
				title  : $("#ctitle").val(),
				status : ( $("#cstatus")[0].checked ) ? "private" : "public"
			}
		};
		
		return JSON.stringify(output);		
	}	
	

	$("#row6")
		.css("cursor", "default")
		.css("font-weight", "bold");

	$("td[id^=row]").mouseover(function(){
		for(var i=0; i<=9; i++) {
			if(i == this.id.replace("row", "")) {
				$("td[id=row" + i + "]").css("background-color","#92BEEB");
			}
			else {
				$("td[id=row" + i + "]").css("background-color","#F4F7FB");
			}
		}
	});
 
'."\r\n";

$b_includes .= JumpTo();

if (count(GetUserGroups()) < 2 || isset( $_SESSION['webcharts']['settings']['status'] )
	&& $_SESSION['webcharts']['settings']['status'] == "private" ) {
	$b_includes .= '$("td[id=row7]").hide();'."\r\n";
}
if (is_wr_project() || is_wr_custom()) {
	$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\r\n";
}
if($wr_is_standalone)
	$b_includes .= '$("td[id=row9]").hide();'."\n";
	
if ($_SESSION['webcharts']['settings']['title'] == "") {
	$b_includes .= '
		for (var i=2; i<=7; i++){
			$("td[id=row" + i + "]").hide();
		};
	'."\r\n";
}
    
$b_includes .= '
	$("#nextbtn, #backbtn, td[id^=row], #savebtn, #saveasbtn").click(function(){
		var URL = "webreport.php";
		if( this.id == "nextbtn" )
			URL = "webchart7.php";
		if( this.id == "backbtn" )
			URL = "webchart5.php";
		if( this.id == "saveasbtn" )
			URL = "webchart6.php?saveas=1";
		if( this.id.substr(0,3)=="row" && this.id != "row6" )
			URL = "webchart" + this.id.replace("row", "") + ".php";
		if( this.id == "row8" )
			URL = "webreport.php";
		if( this.id == "row9" )
			URL = "menu.php";

		if ( $("#cname").val() == ""  && this.id!="row8" && this.id!="row9" ) {
			$("#menujump").hide();
			$("#alert").html("<p>Set chart name</p>").dialog("open");
			return false;
		}
		
		if ( $("#ctitle").val() == "" && this.id!="row8" && this.id!="row9" ) {
			$("#menujump").hide();
			$("#alert").html("<p>Set chart title</p>").dialog("open");
			return false;
		}			
	
		var output = collect_input_data();
		
		if( this.id == "savebtn" ) {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					save: 1,
					';
if(@$_REQUEST["saveas"]==1)						
	$b_includes .= 'saveas:1,';
$b_includes .= '	
					web: "webcharts",
					name: "settings",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						$("#alert")
							.html("<p>Chart Saved</p>")
							.dialog("option", "close", function(){
								window.location = "webreport.php";
							})
							.dialog("open");
					} else {
						$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
					}
				},
				error: function() {
					$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
				}
			});
		}
		thisid=this.id;
		if(this.id != "row6" && this.id !="savebtn") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "settings",
					web: "webcharts",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						window.location = URL;
					} else {
						$("#alert").html("<p>"+msg+"</p>").dialog("open");
						if( thisid == "row8" || thisid == "row9" )
							window.location=URL;
					}
				}
			});
		}
	});
';

if (count(GetUserGroups()) > 1){
	$b_includes.='
		$("#cstatus").click(function(){
			if ( this.checked == true ) {
				$("#nextbtnbox").hide();
				$("#row7").hide();
			} else {
				$("#nextbtnbox").show();
				$("#row7").show();
			}
		});
	';
}

$chart_name = ( isset( $_SESSION['webcharts']['settings']['name'] ) ) ?
	$_SESSION['webcharts']['settings']['name'] :
	GoodFieldName(@$_SESSION['webcharts']['tables'][0]).'_'.CheckLastID('chart');
$chart_title = ( isset( $_SESSION['webcharts']['settings']['title'] ) ) ?
	$_SESSION['webcharts']['settings']['title'] :
	@$_SESSION['webcharts']['tables'][0].' Chart '.CheckLastID('chart');


	$show_status = 'style="display:line;"';
	if ( isset( $_SESSION['webcharts']['settings']['status'] ) && $_SESSION['webcharts']['settings']['status'] == "private" ) 
	{
		$chart_status = 'checked';
		$b_includes .= '$("#nextbtnbox").hide();$("#nextprob").hide();';
	} 
	else 
	{
		if (count(GetUserGroups()) > 1) 
		{
			$chart_status = '';
		} 
		else 
		{
			$chart_status = '';
			$b_includes .= '$("#nextbtnbox").hide();$("#nextprob").hide();';
		}
	}

$b_includes .= '
});
</script>'."\r\n";
$xt->assign("b_includes", $b_includes);

$xt->assign("chart_name", $chart_name);
$xt->assign("chart_title", htmlspecialchars($chart_title));
$xt->assign("chart_status", $chart_status);
$xt->assign("show_status", $show_status);

$templatefile = "webchart6.htm";
$xt->display($templatefile);
?>