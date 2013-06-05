<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

include("include/reportfunctions.php");

if(!isWRAdmin())
{
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: webreport.php");
	return;
}

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();
$_SESSION["webobject"]["table_type"]="custom";
$b_includes = "";
$h_includes = "";

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
	<script type="text/javascript" src="include/jsfunctions.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$b_includes .= '<script type="text/javascript">'."\r\n";
  
$b_includes .= '
$(document).ready(function(){
	$("a#a_editsql").fancybox({
		"hideOnOverlayClick": false,
		"frameWidth" : 850,
		"frameHeight" : 550,
		"overlayShow": true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});
	$("a#a_resultsql").fancybox({
		"hideOnOverlayClick": false,
		"frameWidth" : 850,
		"frameHeight" : 550,
		"overlayShow": true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});
	$("a#a_addsql").fancybox({
		"hideOnOverlayClick": false,
		"frameWidth" : 850,
		"frameHeight" : 550,
		"overlayShow": true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});';

$b_includes .= alertDialog();
$b_includes .= '$("#backbtn").click(function(){
			window.location = "webreport.php";
			return;
		});
	$("#fancy_overlay").unbind();
	$("#addsql").click(function(){
		$("#a_addsql").click();
	});
	$("#editsql").click(function(){
		$.ajax({
			type: "POST",
			url: "save-admin.php",
			data: {
				name: "getcustomsql",
				output: $("#sql_list option:selected").val(),
				rnd: (new Date().getTime())
			},
			success: function(msg)
			{
				$("#a_editsql").click();
			}
		});
		
	});
	$("#deletesql").click(function(){
		$("#sql_list").change();
		$("#alert")
			.html("<p>Do you really want to delete custom query \''.$_SESSION["nameSQL"].'\' ?</p>")
			.dialog("option", "buttons", {
				"No": function() { $(this).dialog("close"); },
				"Delete": function() {
					$.ajax({
						type: "POST",
						url: "save-admin.php",
						data: {
							name: "deletesql",
							idsql: $("#sql_list option:selected").val(),
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
	$("#resultsql").click(function(){
		$.ajax({
			type: "POST",
			url: "web_query.php",
			data: {
				name: "resultsql",
				output: $("#sql_list option:selected").val(),
				rnd: (new Date().getTime())
			},
			success: function(msg)
			{
				$("#a_resultsql").click();
			}
		});
		
	});
	$("#sql_list").change(function(){
		$.ajax({
			type: "POST",
			url: "save-admin.php",
			data: {
				name: "getcustomsql",
				output: $("#sql_list option:selected").val(),
				rnd: (new Date().getTime())
			},
			success: function(msg)
			{
				$("#sql_content").html(msg);
			}
		});
	});';

if(postvalue("name"))
	$b_includes .= '
	$("#sql_list option").each(function(i){
		if($(this).text()=="'.postvalue("name").'")
			$(this).attr("selected","yes");
	});
	';
else
	$b_includes .= '
	$("#sql_list").get(0).selectedIndex=0;
	$("#sql_list option:first").attr("selected", "yes");
	';

	
$b_includes .= '
	$("#sql_list").change();';


$arr_custom=WRGetListCustomSQL();

$sql_list="<select name=sql_list id=sql_list size=20 style='width:500px;font-size:11pt;'>";
foreach($arr_custom as $value)
	$sql_list.="<option value=\"".$value["id"]."\">".htmlspecialchars($value["sqlname"])."</option>";
$sql_list.="</select>";

if(!count($arr_custom))
	$b_includes .= '
		$("#editsql,#deletesql,#resultsql").attr("disabled","disabled")
											.css("color","#847C7C")
											.css("cursor","default");
		
	';

$b_includes .= '
});
</script>'."\r\n";
$xt->assign("b_includes", $b_includes);

if($wr_is_standalone)
	$xt->assign("saveexit",false);
else
	$xt->assign("saveexit",true);
	
$xt->assign("sql_list",$sql_list);
$templatefile = "webreport_sql.htm";
$xt->display($templatefile);
?>
