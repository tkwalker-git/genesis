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
		var output = {
			miscellaneous : {
				type           : $("img.selected").attr("id"),
				print_friendly : $("#pfpc").attr("checked").toString(),
				lines_num      : $("#npp").val()
			}
		};
		
		return JSON.stringify(output);		
	}
	
	$("#rl > img").click(function(){
		$("#rl > img").each(function(){
			$(this).removeClass("selected");
		});
		$(this).addClass("selected");
	});

	$("img#stepped").addClass("selected");	
	
	$("#row5")
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
	$("#nextbtn, #backbtn, td[id^=row], #savebtn, #saveasbtn, #previewbtn").click(function(){
		var URL = "webreport.php";
		if( this.id == "nextbtn" )
			URL = "webreport6.php";
		if( this.id == "backbtn" )
			URL = "webreport4.php";
		if( this.id == "saveasbtn" )
			URL = "webreport8.php?saveas=1";			
		if( this.id.substr(0,3)=="row" && this.id != "row5" )
			URL = "webreport" + this.id.replace("row", "") + ".php";
		if( this.id == "row10" )
			URL = "webreport.php";
		if( this.id == "row11" )
			URL = "menu.php";
		if ( this.id == "row7" )
			URL = "dreport.php?edit=style&rname='.@$_SESSION['webreports']['settings']['name'].'";			
	
		var output = collect_input_data();
		var_save=0;
		if( this.id == "savebtn")
			var_save=1;
		if( this.id == "savebtn" || this.id == "previewbtn") {
			id=this.id;
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					save: var_save,
					web: "webreports",
					name: "miscellaneous",
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
		if(this.id != "row5" && this.id !="savebtn" && this.id !="previewbtn") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "miscellaneous",
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

if(count($_SESSION['webreports']['group_fields'])>1)
{
	$strTemplate="<img src=\"images/layout_stepped.png\" id=\"stepped\" border=\"0\" alt=\"Stepped Layout\" />
                  <img src=\"images/layout_block.png\" id=\"block\" border=\"0\" alt=\"Block Layout\" />
                  <img src=\"images/layout_align.png\" id=\"align\" border=\"0\" alt=\"Align Layout\" />
                  <img src=\"images/layout_outline.png\" id=\"outline\" border=\"0\" alt=\"Outline Layout\" />";
}
else
{
	$strTemplate="<img src=\"images/layout_tabular.png\" id=\"stepped\" border=\"0\" alt=\"Tabular Layout\" />";
	$b_includes .= '<script type="text/javascript">$("img#stepped").addClass("selected");</script>';
}
$xt->assign("img_template", $strTemplate);

$arr = $_SESSION['webreports']['miscellaneous'];

if ( !empty( $arr ) ) {

	$b_includes .= '<script type="text/javascript">
		$(document).ready(function(){
			$("#rl > img").each(function(){
				$(this).removeClass("selected");
			});
			$("img#' . $arr["type"] . '").addClass("selected");
			$("#pfpc").get(0).checked = ' . $arr["print_friendly"] . ';
			$("#npp").val("' . $arr["lines_num"] . '");
		});
	</script>';
}
$xt->assign("report_name_preview",$_SESSION['webreports']['settings']['name']);
$xt->assign("b_includes", $b_includes);

$templatefile = "webreport5.htm";
$xt->display($templatefile);
?>