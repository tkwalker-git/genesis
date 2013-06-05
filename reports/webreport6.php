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

if (is_wr_project()) 
	include("include/" . GetTableURL( $_SESSION['webreports']['tables'][0] ) . "_variables.php");

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

if (@$_SESSION['webreports']['settings']['title'] != "") {
	$title=@$_SESSION['webreports']['settings']['title'];
	if(strlen($title)>25)
		$title=substr($title,25)."...";
	$xt->assign("report_title",", Title: ".$title);
} else {
	$xt->assign("report_title","");
}
if (@$_SESSION['webreports']['tables'][0] != "") {
	$stable=@$_SESSION['webreports']['tables'][0];
	if(strlen($stable)>25)
		$stable=substr($stable,25)."...";

	$xt->assign("report_table",", Table: ".$stable);
} else {
	$xt->assign("report_table","");
}

$xt->assign("b_is_report_save",($_SESSION['webreports']['tmp_active'] != "x"));	
$xt->assign("b_is_report_name",($_SESSION['webreports']['settings']['name'] != ""));
$xt->assign("report_name",$_SESSION['webreports']['settings']['name']);

$arr_tables = getReportTablesList();
$sort_fields=array();
foreach($arr_tables as $t)
{
	$tfields=WRGetNBFieldsList($t);
	foreach($tfields as $f)
	{
		if(is_wr_db())
			$sort_fields[]=$t.".".$f;
		else
			$sort_fields[]=$f;
	}
}
$aSelSortFields=array();

$arr = $_SESSION['webreports']['group_fields'];
if ( !empty( $arr ) ){

	for ( $i=0; $i < count($arr)-1; $i++ ) {
		$aSelSortFields[$i]["name"] = $arr[$i]["name"];
		$aSelSortFields[$i]["desc"] = "false";
	}
}

$arr = $_SESSION['webreports']['sort_fields'];
if ( !empty( $arr ) ){

	for ( $i=0; $i < count($arr); $i++ ) {
		$aCheckSortFields[$i]["name"] = $arr[$i]["name"];
		$aCheckSortFields[$i]["desc"] = $arr[$i]["desc"];
	}
}

$h_includes = "";
$b_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/dstyle.css" type="text/css">
	<link rel="stylesheet" href="include/style.css" type="text/css">
	<link rel="stylesheet" href="include/css/jquery.fancybox.css" type="text/css" media="screen">
	<link rel="stylesheet" href="include/css/jquery-ui.css" type="text/css" media="screen">
	
	<script type="text/javascript" src="include/js/jquery.min.js"></script>
	<script type="text/javascript" src="include/js/jquery.dimensions.pack.js"></script>
	<script type="text/javascript" src="include/js/jquery.easing.js"></script>
    <script type="text/javascript" src="include/js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="include/js/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/json.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$b_includes .= '
<script type="text/javascript">
var timeout	= 200;
var closetimer	= 0;
var relation_stack = [];

$(document).ready(function(){
	$("a#sql_query").fancybox({
		"hideOnContentClick": false,
		"frameWidth" : 800,
		"frameHeight" : 550,
		"overlayShow": true,
		"hideOnContentClick" : true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});
	if($.browser.msie)
		$("a#preview").fancybox({
			"hideOnContentClick": false,
			"frameWidth" : 890,
			"frameHeight" : 730,
			"overlayShow": true,
			"hideOnContentClick" : true,
			"easingIn" : "easeOutBack",
			"easingOut" : "easeInBack"
		});
	else
		$("a#preview").fancybox({
			"hideOnContentClick": false,
			"frameWidth" : 820,
			"frameHeight" : 660,
			"overlayShow": true,
			"hideOnContentClick" : true,
			"easingIn" : "easeOutBack",
			"easingOut" : "easeInBack"
		});
	';
	$b_includes .= alertDialog();
	$b_includes .= '
	
	function collect_input_data() {
		var output = {};
		output.sort_fields = [];
		$("tbody > tr", "#tsf").each(function(i){
			if ( $("select[id^=field]",this).val() != "" ) {
				output.sort_fields[i] = {
					name : $("select[id^=field]",this).val(),
					desc : $("input[id^=desc]",this).attr("checked").toString()
				};
			}
		});
		
		return JSON.stringify(output);		
	}
	
	$("#sqlbtn").click(function(){
		
		var output = collect_input_data();
		
		$.ajax({
			type: "POST",
			url: "save-state.php",
			data: {
				name: "sort_fields",
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
	
	$("#row6")
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

if (count(GetUserGroups()) < 2 || isset( $_SESSION['webreports']['settings']['status'] )
	&& $_SESSION['webreports']['settings']['status'] == "private" ) {
	$b_includes .= '$("td[id=row9]").hide();'."\r\n";
}
if (is_wr_project() || is_wr_custom()) {
	$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\r\n";
}
if($wr_is_standalone)
	$b_includes .= '$("td[id=row11]").hide();'."\n";
	
if ($_SESSION['webreports']['settings']['title'] == "") {
	$b_includes .= '
		for (var i=2; i<=9; i++){
			$("td[id=row" + i + "]").hide();
		};
	'."\r\n";
}
    
$b_includes .= '
	$("#nextbtn, #backbtn, td[id^=row],#savebtn,#saveasbtn,#previewbtn").click(function(){
		var URL="webreport.php";
		if( this.id == "nextbtn" )
			URL = "dreport.php?edit=style&rname='.@$_SESSION['webreports']['settings']['name'].'";
		if( this.id == "backbtn" )
			URL = "webreport5.php";
		if( this.id == "saveasbtn" )
			URL = "webreport8.php?saveas=1";
		if( this.id.substr(0,3)=="row" && this.id !="row6" )
			URL = "webreport"+this.id.replace(\'row\',\'\')+".php";
		if( this.id =="row10" )
			URL = "webreport.php";
		if( this.id =="row11" )
			URL = "menu.php";
		if ( this.id == "row7" )
			URL = "dreport.php?edit=style&rname='.@$_SESSION['webreports']['settings']['name'].'";			
		
		var output = collect_input_data();
		var_save=0;
		if( this.id == "savebtn")
			var_save=1
		if( this.id == "savebtn" || this.id == "previewbtn") {
			id=this.id;
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					save: var_save,
					web: "webreports",
					name: "sort_fields",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						if(id=="savebtn")
						{
							$("#alert")
								.html("<p>Report Saved</p>")
								.dialog("option", "close", function(){
									window.location = "webreport.php";
								})
								.dialog("open");
						}
						else
							$("#preview").click();
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
		if(this.id != "row6" && this.id !="savebtn" && this.id !="previewbtn") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "sort_fields",
					web: "webreports",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						window.location = URL;
					} else {
						$("#alert").html("<p>"+msg+"</p>").dialog("open");
						if( thisid == "row10" || thisid == "row11" )
							window.location=URL;	
					}
				}
			});
		}
	});
});
</script>'."\r\n";

$sSortFields = "";
foreach ($sort_fields as $fld) {
   $sSortFields .= "<option value=\"" . htmlspecialchars($fld) . "\">" . $fld . "</option>"."\r\n";
}

for ( $i=0; $i < 5; $i++ ) {
   $aSortFields[] = $sSortFields;
   $aChecked[] = "";
}

$arr = $aSelSortFields;
if ( !empty($aCheckSortFields) ) {
	$tmpOut = array_slice($aCheckSortFields, count($arr));
}
if ( !empty($tmpOut) ) {
	$arr = array_merge($arr, $tmpOut);
}


for ( $i=0; $i < count($arr); $i++ ) {
	$sSortFields = "";
	foreach ($sort_fields as $fld) {
		$selected = ( $fld == $arr[$i]["name"] ) ? "selected" : "";
		$sSortFields .= "<option " . $selected . " value=\"" . htmlspecialchars($fld) . "\">" . $fld . "</option>"."\r\n";
	}
	$aSortFields[$i] = $sSortFields;
	$aChecked[$i] = ( $arr[$i]["desc"] == "true" ) ? "checked" : "";
}

for ( $i=0; $i < count($aSortFields); $i++ ) {
	$xt->assign("sortFields" . ($i+1), $aSortFields[$i]);
	$xt->assign("desc" . ($i+1), $aChecked[$i]);
}

for ( $i=0; $i < count($aSelSortFields); $i++ ) {
	$b_includes .= '<script type="text/javascript">
		$(document).ready(function(){'."\n";
	$b_includes .= '$("tbody > tr", "#tsf").eq(' . $i . ').find("td").eq(0).find("select").get(0).disabled = true;'."\n";
	$b_includes .= '$("tbody > tr", "#tsf").eq(' . $i . ').find("td").eq(1).find("input").get(0).disabled = true;'."\n";
	$b_includes .= '});
	</script>';
}
$xt->assign("report_name_preview",$_SESSION['webreports']['settings']['name']);
$xt->assign("b_includes", $b_includes);

$templatefile = "webreport6.htm";
$xt->display($templatefile);
?>