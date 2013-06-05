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

$cMaxTitleLength=30;
$conn=db_connect();

$_SESSION["back_to_menu"]="true";

include('include/xtempl.php');
$xt = new Xtempl();

$h_includes = "";
$b_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/dstyle.css" type="text/css">
	<link rel="stylesheet" href="include/style.css" type="text/css">
	<link rel="stylesheet" href="include/css/jquery-ui.css" type="text/css" media="screen">
	
	<script type="text/javascript" src="include/js/jquery.min.js"></script>
	<script type="text/javascript" src="include/js/jquery.dimensions.pack.js"></script>
    <script type="text/javascript" src="include/js/jquery-ui.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);
$arr_UserGroups = GetUserGroup();

$b_includes .= '<script type="text/javascript">'."\r\n";
$b_includes .= '
$(document).ready(function(){
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


	$(".view").each(function(){
		var type = $(this).attr("type");
		var scriptName = (type == "report") ? "dreport.php?rname=" : "dchart.php?cname=";
		var name = $(this).parent("span").attr("id");
		this.href = scriptName + name;
	});
	
	
	$(".del").click(function(){
		var type = $(this).attr("type"),
			scriptName = (type == "report") ? "dreport" : "dchart",
			name = $(this).parent("span").attr("id");
		
		$("#alert")
			.html("<p>Do you really want to delete " + type + " \'"+name+"\' ?</p>")
			.dialog("option", "buttons", {
				"No": function() { $(this).dialog("close"); },
				"Delete": function() {
					$.ajax({
						type: "POST",
						url: "save-state.php",
						data: {
							del: 1,
							web: "web"+type+"s",
							name: ""+name,
							owner: (type == "report") ? reportsList[name]["owner"] : chartsList[name]["owner"],
							rnd: (new Date().getTime())
						},
						success: function(msg){
							if ( msg == "OK" ) {
								window.location.reload();
							} else {
								$("#alert").html("<p>"+msg+"</p>").dialog("open");
							}
						},
						error: function() {
							$("#alert").html("<p>Try again later</p>").dialog("open");
						}
					});				
				}
			})
			.dialog("open");		
	});
	
	$("#return_app").click(function(){
		window.location = "menu.php";
    });';
	
	if(!count($arr_UserGroups))
	{
		$b_includes .= '$("#admin_sql").click(function(){
			$("#alert").dialog("option","title","Enter password");
			$("#alert")
				.html("Password:&nbsp;<input type=password id=admin_password size=30 value=\"\">")
				.dialog("option", "buttons", {
					"Cancel": function() { $(this).dialog("close"); },
					"Ok": function() {
						$.ajax({
							type: "POST",
							url: "save-admin.php",
							data: {
								name: "password",
								password: $("#admin_password").val(),
								rnd: (new Date().getTime())
							},
							success: function(msg){
								if ( msg == "OK" ) {
									window.location="webreport_sql.php";
								} else {
									$("#alert").dialog("option", "buttons", {"Cancel": function() { $(this).dialog("close");}});
									$("#alert").html("<p>Wrong password</p>").dialog("open");
								}
							}
						});				
					}
				})
				.dialog("open");	
		});';
	}
	else
	{
		$b_includes .= '$("#admin_sql").click(function(){
			window.location = "webreport_sql.php";
		});';
	}
	
	if($wr_is_standalone)
		$b_includes .= '$("#users_list").click(function(){
			window.location = "webreport_users_list.php";
		});';

	if(!count($arr_UserGroups))
	{
		$b_includes .= '$("#admin_page").click(function(){
			$("#alert").dialog("option","title","Enter password");
			$("#alert")
				.html("Password:&nbsp;<input type=password id=admin_password size=30 value=\"\">")
				.dialog("option", "buttons", {
					"Cancel": function() { $(this).dialog("close"); },
					"Ok": function() {
						$.ajax({
							type: "POST",
							url: "save-admin.php",
							data: {
								name: "password",
								password: $("#admin_password").val(),
								rnd: (new Date().getTime())
							},
							success: function(msg){
								if ( msg == "OK" ) {
									window.location="webreport_admin.php";
								} else {
									$("#alert").dialog("option", "buttons", {"Cancel": function() { $(this).dialog("close");}});
									$("#alert").html("<p>Wrong password</p>").dialog("open");
								}
							}
						});				
					}
				})
				.dialog("open");	
		});';
	}
	else
	{
		$b_includes .= '$("#admin_page").click(function(){
		window.location = "webreport_admin.php";});';
	}

	$b_includes .= '$(".edit").click(function(){
		var type = $(this).attr("type");
		var scriptName = (type == "report") ? "webreport0" : "webchart0";
		
		$.ajax({
			type: "POST",
			url: "get-state.php",
			data: {
				type: "open",
				web: "web"+type+"s",
				name: $(this).parent("span").attr("id"),
				rnd: (new Date().getTime())
			},
			success: function(msg){
				if ( msg == "OK" ) {
					window.location = scriptName + ".php";
				} else {
					$("#alert").html("<p>"+msg+"</p>").dialog("open");
				}
			},
			error: function() {
				$("#alert").html("<p>Try again later</p>").dialog("open");
			}
		});
	});

	$("#report_createbtn, #chart_createbtn").click(function(){
		var type = $(this).attr("wtype");
		var scriptName = (type == "report") ? "webreport0" : "webchart0";	
	
		$.ajax({
			type: "POST",
			url: "get-state.php",
			data: {
				type: "new",
				web: "web"+type+"s",
				rnd: (new Date().getTime())
			},
			success: function(msg){
				if ( msg == "OK" ) {
					window.location = scriptName + ".php";
				} else {
					$("#alert").html("<p>"+msg+"</p>").dialog("open");					
				}
			},
			error: function() {
				$("#alert").html("<p>Try again later</p>").dialog("open");				
			}
		});
	});
});'."\r\n";

$shared_reports = "";
$private_reports = "";
$shared_charts = "";
$private_charts = "";

$arr_reports = array();
$arrPrivateReports = array();
$arrSharedReports = array();
$arr_charts = array();
$arrPrivateCharts = array();
$arrSharedCharts = array();

$arr_reports = GetReportsList();
foreach ( $arr_reports as $rpt ) {
	if ( $rpt["owner"] != @$_SESSION["UserID"] || $rpt["owner"] == "" ) {
		$arrSharedReports[] = $rpt;
	} elseif ( $rpt["owner"] == @$_SESSION["UserID"] ) {
		$arrPrivateReports[] = $rpt;
	}
}

$arr_charts = GetChartsList();
foreach ( $arr_charts as $chart ) {
	if ( $chart["owner"] != @$_SESSION["UserID"] || $chart["owner"] == "" ) {
		$arrSharedCharts[] = $chart;
	} elseif ( $chart["owner"] == @$_SESSION["UserID"] ) {
		$arrPrivateCharts[] = $chart;
	}
}

$arr_tables_db = DBGetTablesListByGroup("db");
$arr_tables_project = DBGetTablesListByGroup("project");
$arr_tables_custom = DBGetTablesListByGroup("custom");

	foreach ( $arrSharedReports as $rpt ) {
		if ( $rpt["status"] == "public" && ($rpt['view'] || $rpt['edit'])) {
			$shared_reports .= '<div style="margin-bottom:5px;">';
			$shared_reports .= '<span class="ritem" id="' . $rpt['name'] . '">';
			$shared_reports .= ( strlen( $rpt['title'] ) > $cMaxTitleLength+5 ) ? substr( $rpt['title'], 0, $cMaxTitleLength  ) . "..." : $rpt['title'];
		// if @BUILDER.bDynamicPermissions
			if ( $rpt['view'] ) {
				$shared_reports .= '<a class="action view" type="report" href="#">[View]</a>';
			}
			if ( $rpt['edit'] ) {
				if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
					$shared_reports .= '<a class="action edit" type="report" href="#">[Edit]</a>';
				$shared_reports .= '<a class="action del" type="report" href="#">[Delete]</a>';
			}
			$shared_reports .= '</span>';
			$shared_reports .= '</div>'."\r\n";
		}
	}
	foreach ( $arrSharedCharts as $chart ) {
		if ( $chart["status"] == "public" && ($chart['view'] || $chart['edit']) ) {
			$shared_charts .= '<div style="margin-bottom:5px;">';
			$shared_charts .= '<span class="ritem" id="' . $chart['name'] . '">';
			$shared_charts .= ( strlen( $chart['title'] ) > $cMaxTitleLength+5 ) ? substr( $chart['title'], 0, $cMaxTitleLength  ) . "..." : $chart['title'];
		// if @BUILDER.bDynamicPermissions
			if ( $chart['view'] ) {
				$shared_charts .= '<a class="action view" type="chart" href="#">[View]</a>';
			}
			if ( $chart['edit'] ) {
				if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
					$shared_charts .= '<a class="action edit" type="chart" href="#">[Edit]</a>';
				$shared_charts .= '<a class="action del" type="chart" href="#">[Delete]</a>';
			}
			$shared_charts .= '</span>';		
			$shared_charts .= '</div>'."\r\n";
		}
	}

foreach ( $arrPrivateReports as $rpt ) {
	if ( $rpt["status"] == "public" ) {
		$private_reports .= '<div style="margin-bottom:5px;">';
		$private_reports .= '<span class="ritem" id="' . $rpt['name'] . '">';
		$private_reports .= '<img src="images/unlock16.png" title="public report"/>';
		$private_reports .= ( strlen( $rpt['title'] ) > $cMaxTitleLength+5 ) ? substr( $rpt['title'], 0, $cMaxTitleLength  ) . "..." : $rpt['title'];
		$private_reports .= '<a class="action view" type="report" href="#">[View]</a>';
		if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
			$private_reports .= '<a class="action edit" type="report" href="#">[Edit]</a>';
		$private_reports .= '<a class="action del" type="report" href="#">[Delete]</a>';
		$private_reports .= '</span>';
		$private_reports .= '</div>'."\r\n";
	} else {
		$private_reports .= '<div style="margin-bottom:5px;">';
		$private_reports .= '<span class="ritem" id="' . $rpt['name'] . '">';
		$private_reports .= '<img src="images/lock16.png" title="[private report"/>';
		$private_reports .= ( strlen( $rpt['title'] ) > $cMaxTitleLength+5 ) ? substr( $rpt['title'], 0, $cMaxTitleLength  ) . "..." : $rpt['title'];
		$private_reports .= '<a class="action view" type="report" href="#">[View]</a>';
		if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
			$private_reports .= '<a class="action edit" type="report" href="#">[Edit]</a>';
		$private_reports .= '<a class="action del" type="report" href="#">[Delete]</a>';
		$private_reports .= '</span>';
		$private_reports .= '</div>'."\r\n";
	}
}

foreach ( $arrPrivateCharts as $chart ) {
	if ( $chart["status"] == "public" ) {
		$private_charts .= '<div style="margin-bottom:5px;">';
		$private_charts .= '<span class="ritem" id="' . $chart['name'] . '">';
		$private_charts .= '<img src="images/unlock16.png" title="public chart"/>';
		$private_charts .= ( strlen( $chart['title'] ) > $cMaxTitleLength+5 ) ? substr( $chart['title'], 0, $cMaxTitleLength  ) . "..." : $chart['title'];
		$private_charts .= '<a class="action view" type="chart" href="#">[View]</a>';
		if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
			$private_charts .= '<a class="action edit" type="chart" href="#">[Edit]</a>';
		$private_charts .= '<a class="action del" type="chart" href="#">[Delete]</a>';
		$private_charts .= '</span>';		
		$private_charts .= '</div>'."\r\n";
	} else {
		$private_charts .= '<div style="margin-bottom:5px;">';
		$private_charts .= '<span class="ritem" id="' . $chart['name'] . '">';
		$private_charts .= '<img src="images/lock16.png" title="private chart"/>';
		$private_charts .= ( strlen( $chart['title'] ) > $cMaxTitleLength+5 ) ? substr( $chart['title'], 0, $cMaxTitleLength  ) . "..." : $chart['title'];
		$private_charts .= '<a class="action view" type="chart" href="#">[View]</a>';
		if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
			$private_charts .= '<a class="action edit" type="chart" href="#">[Edit]</a>';
		$private_charts .= '<a class="action del" type="chart" href="#">[Delete]</a>';
		$private_charts .= '</span>';		
		$private_charts .= '</div>'."\r\n";
	}
}

$b_includes .= 'var reportsList = new Array();'."\r\n";
$b_includes .= 'var chartsList = new Array();'."\r\n";

foreach ($arr_reports as $rpt) {
	$b_includes .= 'reportsList["' . $rpt['name'] . '"] = new Array();'."\r\n";
	$b_includes .= 'reportsList["' . $rpt['name'] . '"]["status"] = "' . $rpt['status'] . '";'."\r\n";
	$b_includes .= 'reportsList["' . $rpt['name'] . '"]["owner"] = "' . $rpt['owner'] . '";'."\r\n";
}

foreach ($arr_charts as $chart) {
	$b_includes .= 'chartsList["' . $chart['name'] . '"] = new Array();'."\r\n";
	$b_includes .= 'chartsList["' . $chart['name'] . '"]["status"] = "' . $chart['status'] . '";'."\r\n";
	$b_includes .= 'chartsList["' . $chart['name'] . '"]["owner"] = "' . $chart['owner'] . '";'."\r\n";
}

$b_includes .= '</script>'."\r\n";
$xt->assign("b_includes", $b_includes);

if((isWRAdmin() || !count($arr_UserGroups)) && $_SESSION["UserID"]!="Guest")
	$xt->assign("admin_page", "<span class=buttonborder><input type=\"button\" id=\"admin_page\" name=\"admin_page\" class=\"button\" value=\"Admin page\"></span>&nbsp;&nbsp;&nbsp;");
if(!$wr_is_standalone)	
	$xt->assign("back_to_app", "<span class=buttonborder><input type=\"button\" id=\"return_app\" name=\"return_app\" class=\"button\" value=\"Back to main application\"></span>");
if($wr_is_standalone && isWRAdmin())	
	$xt->assign("users_list_page", "<span class=buttonborder><input type=\"button\" id=\"users_list\" name=\"users_list\" class=\"button\" value=\"Users list\"></span>&nbsp;&nbsp;&nbsp;");

$strLogin="Logged on as <b>".$_SESSION["UserID"]."</b>&nbsp;&nbsp;&nbsp;<A class=tablelinks href=\"login.php?a=logout\">Log out</A>";
if($_SESSION["UserID"]!="Guest")
	$strLogin.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class=tablelinks href=\"changepwd.php\">Change password</a>";
$xt->assign("login_mess",$strLogin);		

if((isWRAdmin() || !count($arr_UserGroups)) && $_SESSION["UserID"]!="Guest")
	$xt->assign("admin_sql", "<span class=buttonborder><input type=\"button\" id=\"admin_sql\" name=\"admin_sql\" class=\"button\" value=\"Custom SQL\"></span>&nbsp;&nbsp;&nbsp;");
else	
	$xt->assign("admin_sql",false);
	
if(count($arr_tables_db) || count($arr_tables_project) || count($arr_tables_custom))	
{                                                        
	$create_butt="<span class=buttonborder><input type=\"button\" id=\"report_createbtn\" name=\"report_createbtn\" wtype=\"report\" class=\"button\" value=\"&nbsp;Create Report&nbsp;\"></span>&nbsp;&nbsp;&nbsp;&nbsp;";
    $create_butt.="<span class=buttonborder><input type=\"button\" id=\"chart_createbtn\" name=\"chart_createbtn\" wtype=\"chart\" class=\"button\" value=\"&nbsp;Create Chart&nbsp;\"></span>&nbsp;&nbsp;&nbsp;";
	$xt->assign("create_report_chart", $create_butt);
}
else
{	
	if($wr_is_standalone && !isWRAdmin())
		$xt->assign("create_report_chart", "<b>You do not have permissions to create reports and charts. Contact administrator in this regard.</b>");
}

if($_SESSION["UserID"]=="Guest" && $wr_is_standalone)
	$xt->assign("create_report_chart", "<b>You do not have permissions to create reports and charts. Contact administrator in this regard.</b>");

$xt->assign("shared_reports", $shared_reports);
$xt->assign("private_reports", $private_reports);
$xt->assign("shared_charts", $shared_charts);
$xt->assign("private_charts", $private_charts);

$templatefile = "webreport.htm";
$xt->display($templatefile);
?>