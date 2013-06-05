<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

include("include/reportfunctions.php");

	if(!@$_SESSION["UserID"])
	{
		$_SESSION["MyURL"]="webreport.php";
		header("Location: login.php?message=expired");
		return;
	}

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

$arr_tables_db = DBGetTablesListByGroup("db");
$arr_tables_project = DBGetTablesListByGroup("project");
$arr_tables_custom = DBGetTablesListByGroup("custom");

$h_includes = "";
$b_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/jquery-ui.css" type="text/css">
	<link rel="stylesheet" href="include/css/dstyle.css" type="text/css">
	<link rel="stylesheet" href="include/style.css" type="text/css">
	<link rel="stylesheet" href="include/css/jquery.fancybox.css" type="text/css" media="screen">
	
	<script type="text/javascript" src="include/js/jquery.min.js"></script>
	<script type="text/javascript" src="include/js/jquery.dimensions.pack.js"></script>
	<script type="text/javascript" src="include/js/jquery.easing.js"></script>
    <script type="text/javascript" src="include/js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="include/js/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/json.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$_SESSION["webreport".GoodFieldName($_SESSION["webreports"]["settings"]["name"])."_search"]="";

$b_includes .= '

<style type="text/css">
	#my_tabs .ui-corner-top 
	{
		-moz-border-radius-topleft:3px;
		-moz-border-radius-topright:3px;
	}
	#my_tabs .ui-tabs, #my_tabs .ui-tabs-nav, #my_tabs li 
	{
		float:left;
		left:0px;
		margin:0 0.2em -1px 0;
		position:relative;
	}
	#my_tabs .ui-state-default
	{
		padding-left:15;
		padding-right:15;
		padding-left:15;
		padding-right:15;
		border-bottom-width:0 !important;
		border-bottom: none;
		border: 1px solid white;
		background:url("include/img/but_middle_2.gif") repeat-x scroll 50% 50% #E6E6E6;
		font-weight:normal;
		outline:medium none;
	}
	#my_tabs .ui-tabs-selected
	{
		padding-left:15;
		padding-right:15;
		padding-left:15;
		padding-right:15;
		border: 1px solid white;
		border-bottom: none;
		background:url("include/img/but_middle_1.gif") repeat-x scroll 50% 50% #E6E6E6;
		font-weight:normal;
		outline:medium none;
	}
	#my_tabs .ui-widget,#my_tabs .ui-widget-content,#my_tabs .ui-corner-all 
	{
		background:none;
		color:black;
		border:0px solid black;
	}
</style>

<script type="text/javascript">'."\r\n";

if (is_wr_db() && count($arr_tables_db)) {
	$b_includes .= '
	var NEXT_PAGE_URL = "webreport1.php",
		PREV_PAGE_URL = "webreport.php";
	'."\r\n";
} else {
	$b_includes .= '
	var NEXT_PAGE_URL = "webreport3.php",
		PREV_PAGE_URL = "webreport.php";
	'."\r\n";	
}

$b_includes .= '
var timeout	= 200,
	closetimer	= 0;

$(document).ready(function(){';
if($wr_is_standalone)
	$b_includes .= '$("#radio_project").hide();';

$b_includes .= '
		$("a#sql_query").fancybox({
		"hideOnOverlayClick": false,
		"frameWidth" : 800,
		"frameHeight" : 550,
		"overlayShow": true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});
	
	li_selected="db";
	$("#tl").html("Select table which you will use to create the report:");
	$(function() {
		$("#radio_select_table").tabs();
	});
	
	$("#radio_db,#radio_project,#radio_custom").click(function(){
		$("#add_new_query").hide();
		$("#tables").empty();
		if($(this).attr("id")=="radio_db")
		{
			li_selected="db";
			$("#tl").html("Select table which you will use to create the report:");
			NEXT_PAGE_URL = "webreport1.php";
			PREV_PAGE_URL = "webreport.php";';
	$b_includes .= '$("td[id=row1], td[id=row2]").show();'."\r\n";			
	foreach ($arr_tables_db as $tbl) 
	{
		$selected="";
		if ( !empty( $_SESSION['webreports']['tables'] ) )
			if ( in_array( $tbl, $_SESSION['webreports']['tables'] ) )
				$selected = "selected";
						$b_includes .= "$('<option></option>').attr('value', '".jsreplace($tbl)."').attr('selected','".$selected."').html('".jsreplace($tbl)."').appendTo($('#tables'));"."\r\n";
	}		
		$b_includes .= '
		}
		else if($(this).attr("id")=="radio_project")
		{
			$("#add_new_query").hide();
			li_selected="project";
			$("#tl").html("Select table which you will use to create the report:");
			NEXT_PAGE_URL = "webreport3.php";
			PREV_PAGE_URL = "webreport.php";';
			$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\r\n";
	foreach ($arr_tables_project as $tbl) 
	{
		$selected="";
		if ( !empty( $_SESSION['webreports']['tables'] ) )
			if ( in_array( $tbl, $_SESSION['webreports']['tables'] ) )
				$selected = "selected";
				$b_includes .= "$('<option></option>').attr('value', '".jsreplace($tbl)."').attr('selected','".$selected."').html('".jsreplace(getCaptionTable($tbl)). (getCaptionTable($tbl)!=$tbl ? '&nbsp;('.jsreplace($tbl).')' : '' )."').appendTo($('#tables'));"."\r\n";
	}		
		
	$b_includes .= '
	}
	else
	{
			$("#add_new_query").show();
			$("#tl").html("Select SQL query which you will use to create the report:");
			li_selected="custom";';
		if(isWRAdmin() && ($_SESSION['webreports']['tmp_active']=="x" || @$_SESSION['webreports']['settings']['title']==""))
			$b_includes .= '$("#add_new_query").show();';
		else
			$b_includes .= '$("#add_new_query").hide();';
	$b_includes .= '
	
			NEXT_PAGE_URL = "webreport3.php";
			PREV_PAGE_URL = "webreport.php";';
			$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\r\n";
	foreach ($arr_tables_custom as $tbl) 
	{
		$selected="";
		if ( !empty( $_SESSION['webreports']['tables'] ) )
			if ( in_array( $tbl, $_SESSION['webreports']['tables'] ) )
				$selected = "selected";
			if(postvalue("sqlname")==$tbl)
				$selected = "selected";
			$b_includes .= "$('<option></option>').attr('value', '".jsreplace($tbl)."').attr('selected','".$selected."').html('".jsreplace(getCaptionTable($tbl))."').appendTo($('#tables'));"."\r\n";
	}		
	$b_includes .= '
	}
	if($("#tables").get(0).selectedIndex==-1)
		$("#tables").get(0).selectedIndex=0;
	empty_table_list();
	});
	
	$("#alert").dialog({
		title: "Message",
		draggable: false,
		resizable: false,
		bgiframe: true,
		autoOpen: false,
		modal: true,
		buttons: {
			Ok: function() {
				$(this).dialog("close");
			}
		}
	});
	
	function empty_table_list()
	{
		if($("#tables").val()==null)
		{
			$("#nextbtn,#sqlbtn,#jumpto").css("display","none");
			$("#nextbtn,#sqlbtn,#jumpto").parent("span").css("display","none");
		}
		else
		{
			$("#nextbtn,#sqlbtn,#jumpto").css("display","");
			$("#nextbtn,#sqlbtn,#jumpto").parent("span").css("display","");

		}
	}
	
	function collect_input_data() {
		var output = {};
		
		output.table_type=li_selected;
		output.tables = [];
		$(":selected").each(function(i){
			output.tables[i] = $(this).val();
		});
		return JSON.stringify(output);
	}
	
	$("#sqlbtn").click(function(){
		
		var output = collect_input_data();
		
		$.ajax({
			type: "POST",
			url: "save-state.php",
			data: {
				name: "tables",
				web: "webreports",
				str_xml: output,
				rnd: (new Date().getTime())
			},
			success: function(msg){
				if ( msg == "OK" ) {
					$("#sql_query").click();
				} else {
					$("#alert").html("<p>"+msg+"</p>").dialog("open");
				}
			}
		});
	});
	
	$("#row0")
		.css("cursor", "default")
		.css("font-weight", "bold");

	$("td[id^=row]").mouseover(function(){
		for(var i=0; i<=11; i++) {
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

if (count(GetUserGroups()) < 2 || $_SESSION['webreports']['settings']['status'] != "public" ) {
	$b_includes .= '$("td[id=row9]").hide();'."\r\n";
}

if (is_wr_project()  || is_wr_custom()) {
	$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\n";
}
	
if($wr_is_standalone)
	$b_includes .= '$("td[id=row11]").hide();'."\n";	


$b_includes .= '
	$("a#a_addsql").fancybox({
		"hideOnOverlayClick": false,
		"frameWidth" : 850,
		"frameHeight" : 550,
		"overlayShow": true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});
	
	$("#addsql").click(function(){
		$("#a_addsql").click();
	});
	
	$("#nextbtn, #backbtn, td[id^=row], #savebtn").click(function(){
		var URL = "webreport.php";
		if( this.id == "nextbtn" )
			URL = NEXT_PAGE_URL;
		if( this.id == "backbtn" )
			URL = PREV_PAGE_URL;
		if( this.id.substr(0,3)=="row" && this.id != "row0" )
			URL = "webreport" + this.id.replace("row", "") + ".php";
		if( this.id == "row10" )
			URL = "webreport.php";
		if( this.id == "row11" )
			URL = "menu.php";
		if ( this.id == "row7" )
			URL = "dreport.php?edit=style&rname='.@$_SESSION['webreports']['settings']['name'].'";
		if (this.id == "backbtn" || this.id == "row10" || this.id == "row11") {
			window.location = URL;
			return;
		}
		
		var output = collect_input_data();

		thisid=this.id;

		if(this.id !="row0") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "tables",
					web: "webreports",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						window.location = URL;
					} else {
						$("#alert").html("<p>"+msg+"</p>").dialog("open");
						if( thisid == "row10" || thisid == "row11")
							window.location=URL;
					}
				}
			});
		}
	});
	
	if ($("option:selected").length == 0) {
		$("select").get(0).selectedIndex = 0;
	}
	empty_table_list();';

	$tables = "";

if (is_wr_db() && count($arr_tables_db)) 
{
	$arr_tables = $arr_tables_db;
} 
elseif(count($arr_tables_project) && is_wr_project())
{
	$arr_tables = $arr_tables_project;
}
else
{
	$arr_tables = $arr_tables_custom;
}

if(postvalue("sqlname"))
	$b_includes .= '$("#radio_custom").click();
					li_selected="custom";
					$("#tl").html("Select SQL query which you will use to create the report:");
					';
else
{	
	if(is_wr_db() && count($arr_tables_db))
		$b_includes .= '$("#radio_db").click();
						li_selected="db";
						$("#tl").html("Select table which you will use to create the report:");';
	elseif(is_wr_project() && count($arr_tables_project))
		$b_includes .= '$("#radio_project").click();
						li_selected="project";
						$("#tl").html("Select table which you will use to create the report:");';
	else
		$b_includes .= '$("#radio_custom").click();
						li_selected="custom";
						$("#tl").html("Select SQL query which you will use to create the report:");';
}

					
$b_includes .= '});
</script>'."\r\n";


$xt->assign("b_includes", $b_includes);


foreach ($arr_tables as $tbl) {
	$selected = "";
	if ( !empty( $_SESSION['webreports']['tables'] ) ){
		if ( in_array( $tbl, $_SESSION['webreports']['tables'] ) )
			$selected = "selected";
	}

		$tables .= '<option ' . $selected . ' value="' . htmlspecialchars($tbl) . '">'.(!is_wr_project() ? $tbl : getCaptionTable($tbl)) . (is_wr_project() ? (getCaptionTable($tbl)!=$tbl ? "&nbsp;(".$tbl.")" : "" ) : "").'</option>'."\r\n";
}

if (@$_SESSION['webreports']['settings']['title'] != "") {
	$xt->assign("report_title",", Title: ".@$_SESSION['webreports']['settings']['title']);
} else {
	$xt->assign("report_title","");
}
$xt->assign("tables", $tables);

if(!count($arr_tables_db) && !count($arr_tables_project) || !count($arr_tables_custom) && !count($arr_tables_project) || !count($arr_tables_custom) && !count($arr_tables_db))
	$xt->assign("radio_style","style='display:none';");
	
if(count($arr_tables_db))
	$xt->assign("view_radio_db",true);
if(count($arr_tables_project))
	$xt->assign("view_radio_project",true);



$templatefile = "webreport0.htm";
$xt->display($templatefile);
?>
