<?php
// menuItem class
include_once(getabspath("include/menuitem.php"));
include(getabspath("include/testing.php"));

class XTempl
{
	var $xt_vars=array();
	var $xt_stack;
	var $xt_events=array();
	var $template;
	var $charsets=array();
	var $testingFlag=false;
	
	function getvar($name)
	{
		return xt_getvar($this,$name);

	}
	function recTesting(&$arr)
	{
		global $testingLinks;
		foreach($arr as $k=>$v)
			if(is_array($v))
				$this->recTesting($arr[$k]);
			else
				if(array_key_exists($k,$testingLinks))
					$arr[$k].=" func=\"".$testingLinks[$k]."\"";
	}
	
	function Testing()
	{
		if(!$this->testingFlag)
			return;
		$this->recTesting($this->xt_vars);
	}
	
	function report_error($message)
	{
		echo $message;
		exit();
	}
	
	function XTempl()
	{
		$this->xt_vars=array();
		$this->xt_stack=array();
		$this->xt_stack[]=&$this->xt_vars;
		xtempl_include_header($this,"header","include/header.php");
		xtempl_include_header($this,"footer","include/footer.php");
		//$this->assign_function("event","xt_doevent",array('xt'=>&$this));
		$this->assign_method("event",$this, "xt_doevent",array());
		$this->assign_function("label","xt_label",array());
		$this->assign_function("custom","xt_custom",array());
		$this->assign_function("caption","xt_caption",array());
		$this->assign_function("mainmenu","xt_displaymenu",array());
		$this->assign_function("quickjump_options","xt_displaymenu",array("quickjump"=>true));
		
		$mlang_charsets=array();
		
$mlang_charsets["English"]="Windows-1252";;
		$this->charsets = &$mlang_charsets;
		
		$order = $this->getReadingOrder();
		$html_attrs = '';
		if($order=='RTL')
		{
			$this->assign("RTL_block",true);
			$html_attrs .= 'dir="RTL" ';
		}
		else
			$this->assign("LTR_block",true);
		if(mlang_getcurrentlang() == 'English')
				$html_attrs .= 'lang="en"';
		$this->assign("html_attrs",$html_attrs);	
	}
	
	function getReadingOrder()
	{
		if(@$_REQUEST["language"])
			$charset = $this->charsets[$_REQUEST["language"]];
		else if(@$_SESSION["language"])
			$charset = $this->charsets[$_SESSION["language"]];
		else
			$charset = $this->charsets['English'];
			
		$cp = strtolower($charset);
		if($cp == 'windows-1256' || $cp == 'windows-1255')
			return 'RTL';
		else
			return 'LTR';
	}

function assign($name,$val)
{
	$this->xt_vars[$name]=$val;
}
function assignbyref($name,&$var)
{
	$this->xt_vars[$name]=&$var;
}

function assign_section($name,$begin,$end)
{
	$arr = array();
	$arr["begin"]=$begin;
	$arr["end"]=$end;
	$this->xt_vars[$name]=&$arr;
}

function assign_loopsection($name,&$data)
{
	$arr = array();
	$arr["data"]=&$data;
	$this->xt_vars[$name]=&$arr;
}


function assign_function($name,$func,$params)
{
	$this->xt_vars[$name]=array("func"=>$func,"params"=>$params);
}

function assign_method($name,&$object,$method,$params)
{
	$this->xt_vars[$name]=array("method"=>$method,"params"=>$params);
	$this->xt_vars[$name]["object"]=&$object;
}

function assign_event($name,&$object,$method,$params)
{
     $this->xt_events[$name]=array("method"=>$method,"params"=>$params);
	 $this->xt_events[$name]["object"]=&$object;
}

function xt_doevent($params)
{
	if (array_key_exists(@$params["custom1"], $this->xt_events))
	{
		$eventArr = $this->xt_events[@$params["custom1"]];
				
		if(array_key_exists("method",$eventArr))
		{
			$params=array();
			if(array_key_exists("params",$eventArr))
				$params=$eventArr["params"];
			$method=$eventArr["method"];
//			if(method_exists($eventArr["object"],$method))
				$eventArr["object"]->$method($params);
			return;
		}		
	}
     
	execute_events($params);
}

	function fetch_loaded($filtertag="")
	{
		ob_start();
		$this->display_loaded($filtertag);
		$out=ob_get_contents();
		ob_end_clean();
		return $out;
	}

	function fetch_loaded_before($filtertag)
	{
		$pos1=strpos($this->template,"{BEGIN ".$filtertag."}");
		if($pos1===false)
			return;
		$str=substr($this->template,0,$pos1);
		ob_start();
		$this->Testing();
		xt_process_template($this,$str);
		$out=ob_get_contents();
		ob_end_clean();
		return $out;
	}

	function fetch_loaded_after($filtertag)
	{
		$pos2=strpos($this->template,"{END ".$filtertag."}");
		if($pos2===false)
			return;
		$str=substr($this->template,$pos2+strlen("{END ".$filtertag."}"));
		ob_start();
		$this->Testing();
		xt_process_template($this,$str);
		$out=ob_get_contents();
		ob_end_clean();
		return $out;
	}

	
	function call_func($var)
	{
		return xtempl_get_func_output($var);
	}
	
	function load_template($template)
	{
//	read template file
		$this->template = myfile_get_contents(getabspath("templates/".$template));
	}

	function display_loaded($filtertag="")
	{
		$str=$this->template;
		if($filtertag)
		{
			$pos1=strpos($this->template,"{BEGIN ".$filtertag."}");
			$pos2=strpos($this->template,"{END ".$filtertag."}");
			if($pos1===false || $pos2===false)
				return;
			$pos2+=strlen("{END ".$filtertag."}");
			$str=substr($this->template,$pos1,$pos2-$pos1);
		}
		$this->Testing();
		xt_process_template($this,$str);
	}
	function display($template)
	{
		$this->load_template($template);
		$this->Testing();
		xt_process_template($this,$this->template);
	}
	
	
	function processVar(&$var, &$varparams)
	{
		if(!is_array($var))
		{
		//	just display a value
			echo $var;
		}
		elseif(array_key_exists("func",$var))
		{
		//	call a function
			$params=array();
			if(array_key_exists("params",$var))
				$params=$var["params"];
			foreach($varparams as $key=>$val)
				$params["custom".$key]=$val;
			$func=$var["func"];
			xtempl_call_func($func,$params);
		}
		elseif(array_key_exists("method",$var))
		{
			$params=array();
			if(array_key_exists("params",$var))
				$params=$var["params"];
			foreach($varparams as $key=>$val)
				$params["custom".$key]=$val;
			$method=$var["method"];
//			if(method_exists($var["object"],$method))
				$var["object"]->$method($params);
		}
		else
		{
			$this->report_error("Incorrect variable value - ".$var_name);
			return;
		}
		
	}
}
$menuparams=array();
function xt_displaymenu($params)
{
	global $strTableName, $pageName,$menuparams;	
	$menuparams=$params;
	include(getabspath("include/displaymenu.php"));
}
//	BuildEditControl wrapper
function xt_buildeditcontrol(&$params)
{
	$field=$params["field"];
	if($params["mode"]=="edit")
		$mode=MODE_EDIT;
	else if($params["mode"]=="add")
	{
		$mode=MODE_ADD;
	}
	else if($params["mode"]=="inline_edit")
		$mode=MODE_INLINE_EDIT;
	else if($params["mode"]=="inline_add")
		$mode=MODE_INLINE_ADD;
	else
		$mode=MODE_SEARCH;
	$fieldNum=0;
	if(@$params["fieldNum"])
		$fieldNum=$params["fieldNum"];
	$id="";
	if(@$params["id"]!=="")
		$id=$params["id"];
	$format=GetEditFormat($field);
	if(@$params["format"]!="")
		$format=$params["format"];
	$append="";

	if(($mode==MODE_EDIT || $mode==MODE_ADD || $mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $format==EDIT_FORMAT_READONLY)
	{
		global $readonlyfields;
		echo $readonlyfields[$field];
	}
	if($mode==MODE_SEARCH)
	{			
		$format=@$params["format"];
	}
	$validate = array();
	if(count(@$params["validate"]))
		$validate = @$params["validate"];
		
	$additionalCtrlParams = array();
	if(count(@$params["additionalCtrlParams"]))
		$additionalCtrlParams = @$params["additionalCtrlParams"];
		
	$pageObj = (isset($params["pageObj"]) ? $params["pageObj"] : null);
		
	BuildEditControl($field,@$params["value"],$format,$mode,$fieldNum,$id,$validate,$additionalCtrlParams, $pageObj);
}

function xt_showchart($params)
{
$width=700;
$height=530;
if(array_key_exists("custom1",$params))
	$width=$params["custom1"];
if(array_key_exists("custom2",$params))
	$height=$params["custom2"];
$refresh=GetTableData($params["table"],".ChartRefreshTime",10)*60000;
?>
<div id='<?php echo $params["chartname"] ?>'>
<noscript>
	<object id="<?php echo $params['chartname'];?>" 
			name="<?php echo $params['chartname'];?>" 
			classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
			width="100%" 
			height="100%" 
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
		<param name="movie" value="libs/swf/Preloader.swf" />
		<param name="bgcolor" value="#FFFFFF" />
		<param name="wmode" value="opaque" />
		<param name="allowScriptAccess" value="always" />
		<param name="flashvars" value="swfFile=<?php echo 'dchartdata.php%3Fchartname%3D'.$params['chartname'] ?>" />
		
		<embed type="application/x-shockwave-flash" 
			   pluginspage="http://www.adobe.com/go/getflashplayer" 
			   src="libs/swf/Preloader.swf" 
			   width="100%" 
			   height="100%" 
			   id="<?php echo $params['chartname'];?>" 
			   name="<?php echo $params['chartname'];?>" 
			   bgColor="#FFFFFF" 
			   allowScriptAccess="always" 
			   flashvars="swfFile=<?php echo 'dchartdata.php%3Fchartname%3D'.$params['chartname'] ?>" />
	</object>				
</noscript>
<script type="text/javascript">
	//<![CDATA[
	document.write('<center>');
	document.write("You need to have Adobe Flash Player 9 (or above) to view the chart.<br /><br />");
	document.write("<a href=\"http://www.adobe.com/go/getflashplayer\"><img border=\"0\" src=\"http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif\" /></a><br />");
	document.write('</center>');
	//]]>
</script>
<script type="text/javascript" language="javascript" src="libs/js/AnyChart.js"></script>
<script type="text/javascript" language="javascript" src="libs/js/AnyChartHTML5.js"></script>
<script type="text/javascript" language="javascript">
AnyChart.renderingType = anychart.RenderingType.FLASH_PREFERRED;
var svgSupported = window.SVGAngle != undefined;
if (!svgSupported)
{
 	//<![CDATA[
	document.write('<center>');
	document.write("You need to have Adobe Flash Player 9 (or above) to view the chart.<br /><br />");
	document.write("<a href=\"<?php echo $http?>://www.adobe.com/go/getflashplayer\"><img border=\"0\" src=\"<?php echo $http?>://www.adobe.com/images/shared/download_buttons/get_flash_player.gif\" /></a><br />");
	document.write('</center>');
	//]]>
}	
</script>
<script type="text/javascript" language="javascript">
	//<![CDATA[
	var chart = new AnyChart('libs/swf/AnyChart.swf','libs/swf/Preloader.swf');
	chart.width = '<?php echo $width; ?>';
	chart.height = '<?php echo $height; ?>';
	chart.wMode='opaque';

	var xmlFile = 'dchartdata.php?chartname=<?php echo jsreplace($params["chartname"]);?>';
	xmlFile += '&ctype=<?php echo $params["ctype"];?>';
	chart.setXMLFile(xmlFile);
	chart.write('<?php echo $params["chartname"];?>');
	if("<?php echo $refresh?>"!="0")
		setInterval('refreshChart()',<?php echo $refresh?>);
	function refreshChart()
	{
		page='dchartdata.php?chartname=<?php echo jsreplace($params["chartname"]);?>';
		params={
				action:'refresh',
				rndval:Math.random()
				};
		$.get(page,params,function(xml)
			{
				var arr = new Array();
				arr=xml.split("\n");
				for(i=0; i<arr.length;i+=2)
				{
					chart.removeSeries(arr[i]);
					chart.addSeries(arr[i+1]);
					chart.updatePointData(arr[i]+"_gauge",arr[i]+"_point",{value: arr[i+1]});
				}
				chart.refresh();
			});

	}
	//]]>
</script>
</div>
<?php
	if(function_exists($params["chartname"]))
		eval($params["chartname"]."();");}

function xt_include($params)
{
	if(file_exists(getabspath($params["file"])))
		include(getabspath($params["file"]));
}



function xt_label($params)
{
	echo htmlspecialchars(GetFieldLabel($params["custom1"],$params["custom2"]));
}

function xt_custom($params)
{
	echo GetCustomLabel($params["custom1"]);
}

function xt_caption($params)
{
	echo GetTableCaption($params["custom1"]);
}

function xtempl_get_func_output(&$var)
{
	if(!strlen(@$var["func"]))
		return "";
	ob_start();	
	$params=$var["params"];
	$func=$var["func"];
	xtempl_call_func($func,$params);
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
?>
