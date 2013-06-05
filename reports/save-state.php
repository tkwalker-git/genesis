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

if ( isset( $_POST['str_xml'] ))
{
	$arr = my_json_decode(postvalue('str_xml'));
	if($arr["table_type"])
		$_SESSION["webobject"]["table_type"]=$arr["table_type"];
}


$conn=db_connect();

$xml = new xml();

if(isset( $_POST['save'] ))
	$save_name=$_SESSION["webobject"]["name"];

if($_POST["web"])
	$root=&$_SESSION[$_POST['web']];


if ( isset( $_POST['str_xml'] ) && isset( $_POST['web'] ) && !isset( $_POST['save'] ) )
{


	$arr = my_json_decode(postvalue('str_xml'));
	if(count($arr["parameters"])<2 && $_POST['web']=='webcharts' && $_POST['name']=='parameters')
	{
		echo "You must select at least one series";
		return;
	}
    
	$root=&$_SESSION[$_POST['web']];
	$rt = @$root['tables'][0];
	$ttype = @$root['table_type'];

	foreach ($arr as $key => $val) 
	{
		$root[$key] = $val;
	}
		
	if(is_wr_project()) 
		include("include/" . GetTableURL($root['tables'][0]) . "_variables.php");
	if ( $_POST['web'] == "webcharts" )
	{
        if(!is_wr_project() && (array_key_exists("table_relations", $arr) || array_key_exists("group_by_condition", $arr)))
		{
			update_chart_group_by_condition();
			update_chart_parameters();
		}
	}

	if ( $_POST['web'] == "webreports" )
	{
        if(!is_wr_project() && array_key_exists("table_relations", $arr))
		{
    	    update_report_group_fields();
			update_report_totals();
			update_report_sort_fields();
		}
		if(array_key_exists("group_fields", $arr) || array_key_exists("sort_fields", $arr))
		{
			update_report_sort_fields();
		}
	}

	if (array_key_exists("tables", $arr)) 
	{
		if(is_wr_custom())
		{
			$arr=getCustomSQLbyName($root["tables"][0]);
			$sqlcontent=$arr[2];
			$rs=db_query_safe($sqlcontent,$conn,$errstr);
			if(!$rs)
			{
				echo $errstr;
				exit();
			}
			else
			{
				$_SESSION["customSQL"]=$sqlcontent;
				$_SESSION["idSQL"]=$arr[0];
				$_SESSION["nameSQL"]=$arr[1];
				$_SESSION["object_sql"]=$sqlcontent;
			}
		}
		if ( !isset($root['settings']) ) 
		{
			if ( $_POST['web'] == "webreports" )
			{
				comlete_report_session_default_values();
				save_sql("webreports");
				$str_xml = $xml->array_to_xml( $root );
				SaveReport( $root['settings']['name'],$root['settings']['name'], $root['settings']['title'], $root['settings']['status'], $str_xml, false);
			}
			elseif ( $_POST['web'] == "webcharts" )
			{
				comlete_chart_session_default_values();
				save_sql("webcharts");
				$str_xml = $xml->array_to_xml( $root );
				SaveChart( $root['settings']['name'],$root['settings']['name'], $root['settings']['title'], $root['settings']['status'], $str_xml, false );
			}
		}
		elseif ($root['tables'][0] != $rt || $root['table_type']!=$ttype)
		{
			if($_POST['web']=="webreports")
			{
				unset($root['totals']);
				unset($root['group_fields']);
				unset($root['sort_fields']);
				unset($root['table_relations']);
				unset($root['where_condition']);
				comlete_report_session_default_values(true);
				save_sql("webreports");
			}
			elseif ( $_POST['web'] == "webcharts" )
			{
				unset($root['table_relations']);
				unset($root['group_by']);				
				unset($root['parameters']);
				unset($root['appearance']);
				unset($root['type']);
				comlete_chart_session_default_values(true);
				save_sql("webcharts");
			}
		}
	}
	else
	{
		if($_POST['web']=="webreports")
			save_sql("webreports");
		if($_POST['web']=="webcharts")
			save_sql("webcharts");
	}
    echo "OK";
}
elseif ( isset( $_POST['str_xml'] ) && isset( $_POST['web'] ) && isset( $_POST['save'] ) )
{
	$arr = my_json_decode(postvalue('str_xml'));

	if(count($arr["parameters"])<2 && $_POST['web']=='webcharts' && $_POST['name']=='parameters')
	{
		echo "You must select at least one series";
		return;
	}
	
	$saveas=false;
	if(isset($_POST['saveas']))
		$saveas=true;

	foreach ($arr as $key => $val) 
	{
		$root[$key] = $val;
	}	
	if(is_wr_project()) 
		include("include/" . GetTableURL($root['tables'][0]) . "_variables.php");
    if ( $_POST['web'] == "webreports" )
	{
        $root['owner'] = @$_SESSION["UserID"];
        $root['table_name'] = $root['tables'][0];
        $root['short_table_name'] = GetTableURL( $root['tables'][0] );
        if(!is_wr_project() && array_key_exists("table_relations", $arr))
		{
    	    update_report_group_fields();
			update_report_totals();
			update_report_sort_fields();
		}
        if(array_key_exists("group_fields", $arr) || array_key_exists("sort_fields", $arr))
		{
			update_report_sort_fields();
		}
		if($_POST['save']==1)
			$_SESSION['webreports']['tmp_active'] = "";
		save_sql("webreports");

        $str_xml = $xml->array_to_xml( $root );
        SaveReport( $save_name, $root['settings']['name'], $root['settings']['title'], $root['settings']['status'], $str_xml, $saveas );
    }
	elseif ( $_POST['web'] == "webcharts" )
	{
        if(!is_wr_project() && (array_key_exists("table_relations", $arr) || array_key_exists("group_by_condition", $arr)))
        {
			update_chart_group_by_condition();
			update_chart_parameters();
		}
		$root['settings']['owner'] = @$_SESSION["UserID"];
        $root['settings']['table_name'] = $root['tables'][0];
        $root['settings']['short_table_name'] = GetTableURL( $root['tables'][0] );
		if($_POST['save']==1)
			$_SESSION['webcharts']['tmp_active'] = "";
		save_sql("webcharts");

        $str_xml = $xml->array_to_xml( $root );
        SaveChart( $save_name, $root['settings']['name'], $root['settings']['title'], $root['settings']['status'], $str_xml, $saveas );
    }

    echo "OK";
}
elseif ( isset( $_POST['del'] ))
{
	    if (count(GetUserGroups()) > 1)
	    {
        	$arr_reports = array();
	        if ( $_POST['web'] == "webreports" ){
        	    $arr_reports = GetReportsList();
	            $s="report";
        	    $g=$root['settings']['name'];
	        }
        	else {
	            $arr_reports = GetChartsList();
        	    $s="chart";
	            $g=$root['settings']['name'];
        	}

	        foreach ( $arr_reports as $rpt ) {
        	    if (( $rpt["owner"] != @$_SESSION["UserID"] || $rpt["owner"] == "") && $rpt["view"]==0 && $g==$rpt["name"])
	            {
        	        echo "<p>You don't have permissions to delete this ".$s."</p>";
                	exit;
	            }
        	}
	    }
    if ( $_POST['web'] == "webreports" ) {
        $opStatus = DeleteReport(postvalue('name'));
    } else {
        $opStatus = DeleteChart(postvalue('name'));
    }
    echo "OK";
}

function comlete_report_session_default_values($isedit="") {
	$root=&$_SESSION["webreports"];
	$table = $root['tables'][0];
    $arr_fields = WRGetNBFieldsList($table);
    $arr_fields_all = WRGetFieldsList($table);

 $gfield=$arr_fields[0];
	if(is_wr_db())
		$gfield=$table.".".$arr_fields[0];
	$root['group_fields'] = array(

        array(
			"name" => $gfield,
            "int_type" => "0",
            "ss" => "true",
            "group_order" => "1",
            "color1" => "FAFAD2",
            "color2" => "D4D4B2"
        ),
        array(
            "name" => "Summary",
            "sps" => "true",
            "sds" => "true",
            "sgs" => "true"
        )
    );

    $root['totals'] = array();

    foreach ($arr_fields_all as $fld) {
        $root['totals'][GoodFieldName($table.".".$fld)] = array(
            "name" => $fld,
            "table" => $table,
			"label" => Label($fld, $root['tables'][0]),
            "show" => "true",
            "min" => "false",
            "max" => "false",
            "sum" => "false",
            "avg" => "false",
            "search" => "",
            "view_format" => GetGenericViewFormat($table, $fld),
            "edit_format" => GetGenericEditFormat($table, $fld),
            "display_field" => GetLWDisplayField($fld),
            "linkfield" => GetLWLinkField($fld),
            "show_thumbnail" => ShowThumbnail($fld),
            "need_encode" => NeedEncode($fld),
            "thumbnail" => GetThumbnailPrefix($fld),
            "listformatobj_imgwidth" => GetImageWidth($fld),
            "listformatobj_imgheight" => GetImageHeight($fld),
            "hlprefix" => GetLinkPrefix($fld),
            "listformatobj_filename" => GetFilenameField($fld),
            "lookupobj_lookuptype" => GetLookupType($fld),
            "editformatobj_lookupobj_customdispaly" => GetLWDisplayField($fld),
            "editformatobj_lookupobj_table" => GetLookupTable($fld),
            "editformatobj_lookupobj_where" => GetLWWhere($fld)
        );
    }

    $root['sort_fields'] = array(
        array(
			"name" => $gfield,
            "desc" => "false"
        )
    );

	if(!$isedit)
	{
	    $root['miscellaneous'] = array(
        "type" => "stepped",
        "print_friendly" => "true",
        "lines_num" => "30"
		);
		
		$root['settings'] = array(
			"name" => GoodFieldName($root['tables'][0]).'_'.CheckLastID('report'),
			"title" => $root['tables'][0].' Report '.CheckLastID('report'),
			"status" => "private"
		);
		$_SESSION["webobject"]["name"]= GoodFieldName($root['tables'][0]).'_'.CheckLastID('report');
		$root['owner'] = $_SESSION["UserID"];
		$_SESSION['webreports']['tmp_active'] = "x";
	}
	$root['table_name'] = $root['tables'][0];
	$root['short_table_name'] = GetTableURL($root['tables'][0]);
}

function comlete_chart_session_default_values($isedit="") {
	$root=&$_SESSION["webcharts"];
	$table = $root['tables'][0];
    $arr_fields = WRGetNBFieldsList($table);
    $arr_fields_all = WRGetFieldsList($table);

    $root['chart_type'] = array(
        "type" => "2d_column"
    );
	
	$arr_data_series=array();
	$arr_label_series=array();
	get_chart_series_fields($arr_data_series,$arr_label_series);
	$datafield=array("field"=>$arr_fields[0],"label"=>WRChartLabel($arr_fields[0]));
	$labelfield=$datafield;
	if(count($arr_label_series))
	{
		$labelfield = $arr_label_series[0];
		$ttable="";
		$tfield="";
		WRSplitFieldName($labelfield["field"],$ttable,$tfield);
		$labelfield["field"]=$tfield;
	}
	if(count($arr_data_series))
	{
		$datafield = $arr_data_series[0];
		$ttable="";
		$tfield="";
		WRSplitFieldName($datafield["field"],$ttable,$tfield);
		$datafield["field"]=$tfield;
	}
    $root['parameters'] = array(
        array(
            "name"  => $datafield["field"],
			"ohlcOpen"  => $datafield["field"],
			"ohlcClose"  => $datafield["field"],
			"ohlcHigh"  => $datafield["field"],
			"ohlcLow"  => $datafield["field"],
			"table" => $table,
			"agr_func" => "",
            "label" => $datafield["label"]
        ),
        array(
            "name"  => $labelfield["field"],
			"table" => $table,
			"agr_func" => "",
            "label" => "undefined"
        )
    );
	
	$root['fields'] = array();

    foreach ($arr_fields_all as $fld) {
        $root['fields'][] = array(
            "name" => $fld,
			"label" => WRChartLabel($fld),
            "search" => ""
        );
    }
	

		$root['appearance']["series_color"] = "FF0000";
		$root['appearance']["color51"] = "";
		$root['appearance']["color52"] = "";
		$root['appearance']["color61"] = "";
		$root['appearance']["color62"] = "";
		$root['appearance']["color71"] = "";
		$root['appearance']["color72"] = "";
		$root['appearance']["color81"] = "";
		$root['appearance']["color82"] = "";
		$root['appearance']["color91"] = "";
		$root['appearance']["color92"] = "";
		$root['appearance']["color101"] = "";
		$root['appearance']["color102"] = "";
		$root['appearance']["color111"] = "";
		$root['appearance']["color112"] = "";
		$root['appearance']["color121"] = "";
		$root['appearance']["color122"] = "";
		$root['appearance']["color131"] = "";
		$root['appearance']["color132"] = "";
		$root['appearance']["color141"] = "";
		$root['appearance']["color142"] = "";
		$root['appearance']["slegend"] = "true";
		$root['appearance']["sgrid"] = "true";
		$root['appearance']["sname"] = "true";
		$root['appearance']["sval"] = "true";
		$root['appearance']["sanim"] = "true";
		$root['appearance']["scur"] = "false";
		$root['appearance']["sstacked"] = "false";
		$root['appearance']["saxes"] = "false";
		$root['appearance']["slog"] = "false";
		$root['appearance']["dec"] = "2";
		$root['appearance']["head"] = $root['tables'][0].' Chart '.CheckLastID('chart');
		$root['appearance']["foot"] = $root['tables'][0].' Chart '.CheckLastID('chart');
		$root['appearance']["aqua"] = "0";
		$root['appearance']["cview"] = "0";
		$root['appearance']["is3d"] = "false";
		$root['appearance']["isstacked"] = "false";
		$root['appearance']["cscroll"] = "true";
		$root['appearance']["autoupdate"] = "false";
		$root['appearance']["maxbarscroll"] = "10";
		$root['appearance']["update_interval"] = "5";
		$root['appearance']["accumulstyle"] = "0";
		$root['appearance']["accumulinvert"] = "false";
		$root['appearance']["linestyle"] = "0";
		$root['appearance']["gaugestyle"] = "0";
	
	if(!$isedit)
	{
		$root['settings'] = array(
			"name" => GoodFieldName($root['tables'][0]).'_'.CheckLastID('chart'),
			"title" => $root['tables'][0].' Chart '.CheckLastID('chart'),
			"status" => "private",
			"owner" => $_SESSION["UserID"],
			"table_name" => $root['tables'][0],
			"short_table_name" => GetTableURL($root['tables'][0])
		);
		$_SESSION["webobject"]["name"]= GoodFieldName($root['tables'][0]).'_'.CheckLastID('chart');
		$root['owner'] = $_SESSION["UserID"];
		$_SESSION['webcharts']['tmp_active'] = "x";
	}
	else
	{
		$root['settings'] = array(
			"name" => $_SESSION['webcharts']['settings']['name'],
			"title" => $_SESSION['webcharts']['settings']['title'],
			"status" => $_SESSION['webcharts']['settings']['status'],
			"owner" => $_SESSION['webcharts']['settings']['owner'],
			"table_name" => $root['tables'][0],
			"short_table_name" => GetTableURL($root['tables'][0])
		);
	}
		
	$root['table_name'] = $root['tables'][0];
	$root['short_table_name'] = GetTableURL($root['tables'][0]);
	
}

function save_sql($type) {
	$sql_query = "";
	$sql_where = "";
	$sql_order_by = "";
	$sql_order_by_preview="";
	$sql_group_by = "";
	$root=&$_SESSION[$type];
		
	switch ($type)
	{
		case "webreports" :
			if(is_wr_custom())
			{
				$arr=getCustomSQLbyName($root["tables"][0]);
				$sql_query=$arr[2];
				$sql_query_preview=$arr[2];
				$sql_where="";
				$sql_order_by="";
				$sql_group_by="";
				break;
			}
			$arr_fields_all = array();
			if ( !empty( $root['totals'] ) )
			{
				foreach ( $root['totals'] as $fld ) 
				{
					if ($fld["show"] == "true") 
					{
						$alias = " as " . AddFieldWrappers(GoodFieldName($fld["table"].".".$fld["name"]));
						$arr_fields_all[] = AddTableWrappers($fld["table"]).".".AddFieldWrappers($fld["name"]).$alias;
						if(!IsBinaryType(WRGetFieldType($fld["table"].".".$fld["name"])))
							$arr_fields_nb[]=AddTableWrappers($fld["table"]).".".AddFieldWrappers($fld["name"]).$alias;
					}
				}
			}
			else 
			{
				$table_name = $root['tables'][0];
				$arr_fields = WRGetFieldsList($table_name);
				for ($j=0; $j < count($arr_fields); $j++) 
				{
					$arr_fields_all[] = AddTableWrappers($table_name).".".AddFieldWrappers($arr_fields[$j]);
					if(!IsBinaryType(WRGetFieldType($table_name.".".$arr_fields[$j])))
						$arr_fields_nb[]=AddTableWrappers($table_name).".".AddFieldWrappers($arr_fields[$j]);
				}
			}
			$sql_query ="";
//			$sql_query .= " \nFROM " . AddTableWrappers($root['tables'][0]);	
			$sql_query .= " \n".make_from_clause($type);	
/*		
			if ( !empty( $root['table_relations'] ) ){
				$arr_relations = array_slice(explode("@END@", $root['table_relations']["relations"]), 0, -1);
				foreach ($arr_relations as $rel) {
					$arr_parts = explode("@SEP@", $rel);
					$sql_query .= $arr_parts[0];
				}
			}
*/			
			$sql_query_preview="SELECT\n".implode(", \n", $arr_fields_nb).$sql_query;
			$sql_query = "SELECT\n".implode(", \n", $arr_fields_all).$sql_query;
		
			if ( !empty( $root['where_condition'] ) ){
				$sql_where .= " \nWHERE ";
				foreach ( $root['where_condition'] as $arr ) {
					WRSplitFieldName($arr['field_opt'],$t,$f);
					$fld_name = AddTableWrappers($t).".".AddFieldWrappers($f);
					$sql_where .= "( " . $fld_name . $arr['filter_value'];
					$sql_where .= ( $arr['first_or_value'] == "" ) ? "" : " OR " . $fld_name . $arr['first_or_value'];
					$sql_where .= ( $arr['second_or_value'] == "" ) ? "" : " OR " . $fld_name . $arr['second_or_value'];
					$sql_where .= ( $arr['third_or_value'] == "" ) ? "" : " OR " . $fld_name . $arr['third_or_value'];
					$sql_where .= " ) AND ";
				}
				$sql_where = substr($sql_where,0,-5);
			}
			if(!empty($root['sort_fields']))
			{
				$sql_order_by .= " \nORDER BY ";
				foreach ( $root['sort_fields'] as $arr ) 
				{
					if(is_wr_project())
						$sql_order_by .= AddFieldWrappers($arr['name']);
					else
					{
						$table="";
						$field="";
						WRSplitFieldName($arr['name'],$table,$field);
						$sql_order_by .= AddTableWrappers($table).".".AddFieldWrappers($field);
					}
					$sql_order_by .= ($arr["desc"] == "true") ? " DESC, " : " ASC, ";
				}
				$sql_order_by = substr( $sql_order_by, 0, -2);
			}
			
			break;
		
		case "webcharts" :
			if(is_wr_custom())
			{
				$arr=getCustomSQLbyName($root["tables"][0]);
				$sql_query=$arr[2];
				$sql_query_preview=$arr[2];
				$sql_where="";
				$sql_order_by="";
				$sql_order_by_preview="";
				$sql_group_by="";
				break;
			}
		
			$table_name = $root['tables'][0];
			$arr_fields = WRGetFieldsList($table_name);
			for ($j=0; $j < count($arr_fields); $j++) {
				$arr_fields_all[] = AddTableWrappers($table_name).".".AddFieldWrappers($arr_fields[$j]);
			}			
			
			$sql_query = "SELECT\n";
			$sql_query_preview = "SELECT\n";
			//
			if ( !empty( $root['parameters'] ) )
			{
				if(is_groupby_chart())
				{
					foreach ( $root['parameters'] as $idx=>$arr ) 
					{
						if ( $arr["name"] == "" ) 
							continue;
						$fld=AddTableWrappers($arr["table"]).".".AddFieldWrappers($arr["name"]);
						if($arr["agr_func"]!="")
						{
							
							$fld = $arr["agr_func"]."(".$fld.")";
							if($idx<count($arr_fields)-1)
							{
	//	adding alias to Data series field
								$fld.=" AS ".AddFieldWrappers($arr["label"]);
							}
							else if($arr["agr_func"]!="GROUP BY")
							{
	//	adding alias to Label field
								$fld.=" AS ".AddFieldWrappers($arr["name"]);
							}
							
						}
						$sql_query .= $fld . ", \n";
					}
					for ($i=0; $i < count($root['group_by_condition'])-1; $i++)
					{
						$arr = $root['group_by_condition'][$i];
						if ( $arr["field_opt"] == "" || $arr["group_by_value"]==-1) 
							continue;
						$ttable="";
						$tfield="";
						WRSplitFieldName($arr["field_opt"],$ttable,$tfield);
						$fld=AddTableWrappers($ttable).".".AddFieldWrappers($tfield);
						if($arr["group_by_value"]!=-1 && $arr["group_by_value"]!="GROUP BY")
						{
							
							$fld = $arr["group_by_value"]."(".$fld.")";
							$fld.=" AS ".AddFieldWrappers($arr["field_opt"]);
						
						}
						$sql_query_preview .= $fld . ", \n";
					}
				}
				else
				{
					for ($j=0; $j < count($arr_fields); $j++) 
					{
						if(!IsBinaryType(WRGetFieldType($table_name.".".$arr_fields[$j])))
						{
							$sql_query .= AddTableWrappers($table_name).".".AddFieldWrappers($arr_fields[$j]).", \n";
							$sql_query_preview.= AddTableWrappers($table_name).".".AddFieldWrappers($arr_fields[$j]).", \n";
						}
					}
				}
				$sql_query = substr($sql_query,0,-3);				
				$sql_query_preview = substr($sql_query_preview,0,-3);				
			}

			$sql_query .= " \n".make_from_clause($type);	
			$sql_query_preview .= " \n".make_from_clause($type);	
	
			//
			$sql_where="";
			if ( !empty( $root['group_by_condition'][0] ) )
			{
				for ($i=0; $i < count($root['group_by_condition'])-1; $i++) {
					$arr = $root['group_by_condition'][$i];
					$fld = array(0=>"",1=>"");
					WRSplitFieldName($arr['field_opt'],$t,$f);
					$fld_name = AddTableWrappers($t).".".AddFieldWrappers($f);
					if ($arr['filter_value'] == "" ) {
						continue;
					}
					if(strlen($sql_where))
						$sql_where.=" AND ";
					$sql_where .= "(" . $fld_name . $arr['filter_value'];
					$sql_where .= ( $arr['first_or_value'] == "" ) ? "" : " OR " . $fld_name . ($arr['first_or_value']);
					$sql_where .= ( $arr['second_or_value'] == "" ) ? "" : " OR " . $fld_name . ($arr['second_or_value']);
					$sql_where .= ( $arr['third_or_value'] == "" ) ? "" : " OR " . $fld_name . ($arr['third_or_value']);
					$sql_where .= ")";
				}
				if(strlen($sql_where))
					$sql_where=" WHERE ".$sql_where;

				
				$group_by_clause = "";
				$having_clause = "";
				if(is_groupby_chart())
				{
					for ($i=0; $i < count($root['group_by_condition'])-1; $i++) {
						$arr = $root['group_by_condition'][$i];
						$table_name="";
						$field_name="";
						WRSplitFieldName($arr['field_opt'],$table_name,$field_name);
						$fld_name = AddTableWrappers($table_name).".".AddFieldWrappers($field_name);
						if ( $arr['group_by_value'] != "-1" ) 
						{
							if ( $arr['group_by_value'] == "GROUP BY" )
							{
								$group_by_clause .= $fld_name. ", ";
							}
							if ( !empty( $arr['having_value'] ) )
							{
								if($arr["group_by_value"]!="GROUP BY" && $arr["group_by_value"]!="-1")
									$fld_name = $arr["group_by_value"]."(".$fld_name.")";
								$having_clause .= $fld_name." ".$arr['having_value'] . " AND ";
							} 
						}
					}
					
					if ( $group_by_clause != "" ) 
					{
						$group_by_clause = "\nGROUP BY " . substr($group_by_clause, 0, -2);
					}
					if ( $having_clause != "" ) 
					{
						$having_clause = "\nHAVING " . substr($having_clause, 0, -5);
					}			
					$sql_group_by = $group_by_clause . $having_clause;
				}
				
//	calc order by clause
				$arr_order=array();
				for ($i=0; $i < count($root['group_by_condition'])-1; $i++) 
				{
					$arr = $root['group_by_condition'][$i];
					if($arr["sort_dir"]=="-1" || is_groupby_chart() && $arr['group_by_value']==-1)
						continue;
					WRSplitFieldName($arr['field_opt'],$table_name,$field_name);
					if(is_groupby_chart() && $arr['group_by_value']!=-1)
					{
						$fld_name = $arr["group_by_value"]."(".AddTableWrappers($table_name).".".AddFieldWrappers($field_name).")";
						$fld_name_preview = AddFieldWrappers($arr['field_opt']);
					}
					else
					{
						
						$fld_name_preview = AddTableWrappers($table_name).".".AddFieldWrappers($field_name);
						$fld_name = AddTableWrappers($table_name).".".AddFieldWrappers($field_name);
					}
					$arr_order[(int)($arr["sort_order"])]=array("field"=>$fld_name,"field_preview"=>$fld_name_preview,"dir"=>$arr["sort_dir"]);
				}
				
				if(count($arr_order))
				{
					$arr_sortorders = array_keys($arr_order);
					sort($arr_sortorders);
					foreach($arr_sortorders as $i)
					{
						if(strlen($sql_order_by_preview))
							$sql_order_by_preview.=", ";
						if(strlen($sql_order_by))
							$sql_order_by.=", ";
						$sql_order_by_preview.= $arr_order[$i]["field_preview"]." ".$arr_order[$i]["dir"];
						$sql_order_by.= $arr_order[$i]["field"]." ".$arr_order[$i]["dir"];
					}
					$sql_order_by_preview = "\nORDER BY ".$sql_order_by_preview;
					$sql_order_by = "\nORDER BY ".$sql_order_by;
				}
			}
			//$sql_query_preview = $sql_query;
			break;
		default : 
			break;		
	}
	$_SESSION[$type]['sql'] = $sql_query;
	$_SESSION[$type]['sql_preview'] = $sql_query_preview;
	$_SESSION[$type]['where'] = $sql_where;
	$_SESSION[$type]['order_by'] = $sql_order_by;
	$_SESSION[$type]['order_by_preview'] = $sql_order_by_preview;
	$_SESSION[$type]['group_by'] = $sql_group_by;
	$_SESSION["object_sql"]=$sql_query;
}

//	update chart fields (group by) when table is changed
function update_chart_group_by_condition()
{
	$root=&$_SESSION["webcharts"];
//	get tables list
	$arr_join_tables=getChartTablesList();

	$groupby_found=false;
	if(!count($root["group_by_condition"]))
	{
		$root["group_by_toggle"]=false;
		return;
	}
	$arr_unset = Array();

	foreach($root["group_by_condition"] as $idx=>$arr)
	{
		if($idx==="group_by_toggle")
			continue;
//	check if the field appears in the tables
		$table="";
		$field="";
		WRSplitFieldName($arr["field_opt"],$table,$field);
		$appear=false;
		foreach($arr_join_tables as $tbl)
		{
			if($tbl!=$table)
				continue;
			$fields=WRGetFieldsList($tbl);
			foreach($fields as $f)
			{
				if($field==$f)
				{
					$appear=true;
					break;
				}
			}
		}
		if(!$appear)
		{
//	remove field
			$arr_unset[]=$idx;
		} 
		elseif($arr["group_by_value"]=="GROUP BY")
			$groupby_found=true;
	}

	foreach($arr_unset as $idx=>$fld)
	{
		unset($root["group_by_condition"][$fld]);
	}
	
	
	
//	compact	group_by_condition array
	$keys = array_keys($root["group_by_condition"]);
	$group_by_condition = array();
	$i=0;
	foreach($keys as $k)
	{
		if(is_numeric($k))
			$group_by_condition[$i++]=$root["group_by_condition"][$k];
		else
			$group_by_condition[$k]=$root["group_by_condition"][$k];
	}
	$root["group_by_condition"] = $group_by_condition;
//	uncheck group by toggle	if needed
	if(!$groupby_found)
	{
		$root["group_by_condition"]["group_by_toggle"]="false";
		foreach($root["group_by_condition"] as $idx=>$arr)
		{
			if($idx==="group_by_toggle")
				continue;
			$root["group_by_condition"][$idx]["group_by_value"]="";
		}
	}
}

function update_chart_parameters()
{
	//	check if chart parameters are valid
	$root=&$_SESSION["webcharts"];
	$params_saved=0;
	$arr_join_tables=getChartTablesList();
	if(!count($root["parameters"]))
		return;
	foreach($root["parameters"] as $idx=>$arr)
	{
		$appear=false;
		if(is_groupby_chart())
		{
//	check if the parameter appear in the group_by_condition
			for($i=0;$i<count($root["group_by_condition"])-1;$i++)
			{
				if($root["group_by_condition"][$i]["field_opt"]!=$arr["table"].".".$arr["name"])
					continue;
				if($arr["agr_func"]==$root["group_by_condition"][$i]["group_by_value"] || !$arr["agr_func"] && $root["group_by_condition"][$i]["group_by_value"]=="GROUP BY")
				{
					$appear=true;
					break;
				}
			}
		}
		else
		{
			$root["parameters"][$idx]["agr_func"]="";
			//	check if the field appear in the list of tables
			foreach($arr_join_tables as $tbl)
			{
				if($tbl!=$arr["table"])
					continue;
				$fields=WRGetFieldsList($tbl);
				foreach($fields as $f)
				{
					if($f==$arr["name"])
					{
						$appear=true;
						break;
					}
				}
				if($appear)
					break;
			}
		}
		if($appear)
		{
			$params_saved++;
			continue;
		}
//		clean up parameter
		$root["parameters"][$idx]["name"]="";
		$root["parameters"][$idx]["table"]="";
		$root["parameters"][$idx]["agr_func"]="";
		$root["parameters"][$idx]["label"]="";
	}
	if(!$root["parameters"][0]["name"])
	{
//	add first default parameter
		set_default_chart_parameter(0,false,true);
		if(!$root["parameters"][0]["name"])
			set_default_chart_parameter(0,true,true);
		
	}
	if(!$root["parameters"][count($root["parameters"])-1]["name"])
	{
//	add first default parameter
		set_default_chart_parameter(count($root["parameters"])-1,true,false);
	}
}

function set_default_chart_parameter($idx,$labelMode,$addLabel)
{
	$root=&$_SESSION["webcharts"];
	$arr_join_tables=getChartTablesList();
	if(is_groupby_chart())
	{
		for($i=0;$i<count($root["group_by_condition"])-1;$i++)
		{
			if(!$root["group_by_condition"][$i]["group_by_value"])
				continue;
			$type=WRGetFieldType($root["group_by_condition"][$i]["field_opt"]);
			$grvalue=$root["group_by_condition"][$i]["group_by_value"];
			if(!$labelMode && (IsNumberType($type) || $grvalue!="GROUP BY") || $labelMode)
			{
				$table="";
				$field="";
				WRSplitFieldName($root["group_by_condition"][$i]["field_opt"],$table,$field);
				$root["parameters"][$idx]["name"]=$field;
				$root["parameters"][$idx]["table"]=$table;
				if($grvalue!="GROUP BY")
					$root["parameters"][$idx]["agr_func"]=$grvalue;
				if($addLabel)
					$root["parameters"][$idx]["label"]=$field;
				break;
			}
		}
	}
	else
	{
		foreach($arr_join_tables as $tbl)
		{
			if(!$labelMode)
				$fields=GetNumberFieldsList($tbl);
			else
				$fields=WRGetNBFieldsList($tbl);
			if(count($fields))
			{
				$root["parameters"][$idx]["name"]=$fields[0];
				$root["parameters"][$idx]["table"]=$tbl;
				$root["parameters"][$idx]["agr_func"]="";
				if($addLabel)
					$root["parameters"][$idx]["label"]=WRChartLabel($tbl.".".$fields[0]);
				break;
			}
		}
	}
}

function update_report_group_fields()
{
//	ensure all group fields are listed in the tables	
	$root=&$_SESSION["webreports"];
//	ensure all fields in reports are listed in the tables	
	$tables=getReportTablesList();
	$changed=false;
	$arr_unset = Array();
	foreach($root["group_fields"] as $idx=>$fld)
	{
		$table="";
		$field="";
		if($fld["name"] == "Summary")
			continue;

		if(is_wr_db())
			WRSplitFieldName($fld["name"],$table,$field);
		else
		{
			$field=$fld["name"];
			$table=$root['tables'][0];
		}
		if(array_search($table,$tables)!==false)
		{
			$fields=WRGetFieldsList($table);
			if(array_search($field,$fields)!==false)
				continue;
		}
//	remove $total if found
		$arr_unset[]=$idx;
		$changed=true;
	}
	
	
	foreach($arr_unset as $idx=>$fld)
	{
		unset($root["group_fields"][$fld]);
	}
	
	
	if(!$changed)
		return;
//	alter array indexes
	$j=0;
	$newarr=array();
	$keys=array_keys($root["group_fields"]);
	foreach($keys as $idx)
	{
		$newarr[$j]=$root["group_fields"][$idx];
		$j++;
	}
	$root["group_fields"] = $newarr;
}

function update_report_sort_fields()
{
//	ensure all group fields are listed in the tables	
	$root=&$_SESSION["webreports"];
	if(!$root["sort_fields"])
		return;
//	ensure all fields in reports are listed in the tables	
	$tables=getReportTablesList();
	$changed=false;
	$arr_unset = Array();
	foreach($root["sort_fields"] as $idx=>$fld)
	{
		$table="";
		$field="";

		if(is_wr_db())
			WRSplitFieldName($fld["name"],$table,$field);
		else
		{
			$field=$fld["name"];
			$table=$root['tables'][0];
		}
		if(array_search($table,$tables)!==false)
		{
			$fields=WRGetFieldsList($table);
			if(array_search($field,$fields)!==false)
				continue;
		}
//	remove $total if found
		$arr_unset[]=$idx;
	}
	foreach($arr_unset as $idx=>$fld)
	{
		unset($root["sort_fields"][$fld]);
	}
	
	
	
//	make new array

	$newarr=array();
//	add group fields	
	foreach($root["group_fields"] as $fld)
	{
		if($fld["name"]=="Summary")
			continue;
		$newarr[]=array("name"=>$fld["name"],"desc"=>"false");
	}
//	add the rest of fields
	$keys=array_keys($root["sort_fields"]);
	$j=count($newarr);
	foreach($keys as $idx)
	{
		$found=false;
		foreach($newarr as $nfld)
		{
			if($nfld["name"]==$root["sort_fields"][$idx]["name"])
			{
				$found=true;
				break;
			}
		}
		if($found)
			continue;
		$newarr[$j]=$root["sort_fields"][$idx];
		$j++;
	}
	$root["sort_fields"] = $newarr;
}


function update_report_totals()
{
	$root=&$_SESSION["webreports"];
//	ensure all fields in reports are listed in the tables	
	$tables=getReportTablesList();
	$arr_unset=array();
	foreach($root["totals"] as $idx=>$fld)
	{
		if(array_search($fld["table"],$tables)!==false)
		{
			$fields=WRGetFieldsList($fld["table"]);
			if(array_search($fld["name"],$fields)!==false)
				continue;
		}
//	remove $total if found
		$arr_unset[]=$idx;
	}
	foreach($arr_unset as $idx=>$fld)
	{
		unset($root["totals"][$fld]);
	}
	
//	ensure all fields appear in the totals
	$all_fields=array();
	foreach($tables as $t)
	{
		$fields=WRGetFieldsList($t);
		foreach($fields as $f)
		{
			if(is_wr_db())
				$all_fields[]=$t.".".$f;
			else
				$all_fields[]=$f;
		}
	}
//	ensure all series  fields appear in the totals
	foreach($all_fields as $f)
	{
		if(array_key_exists(GoodFieldName($f),$root["totals"]))
			continue;
		$table="";
		$fld="";
		if(is_wr_db())
			WRSplitFieldName($f,$table,$fld);
		else
		{
			$table=$tables[0];
			$fld=$f;
		}
        $root['totals'][GoodFieldName($f)] = array(
            "name" => $fld,
            "table" => $table,
			"label" => Label($fld, $table),
            "show" => "true",
            "min" => "false",
            "max" => "false",
            "sum" => "false",
            "avg" => "false",
            "search" => "",
            "view_format" => GetGenericViewFormat($table, $fld),
            "edit_format" => GetGenericEditFormat($table, $fld),
            "display_field" => GetLWDisplayField($fld),
            "linkfield" => GetLWLinkField($fld),
            "show_thumbnail" => ShowThumbnail($fld),
            "need_encode" => NeedEncode($fld),
            "thumbnail" => GetThumbnailPrefix($fld),
            "listformatobj_imgwidth" => GetImageWidth($fld),
            "listformatobj_imgheight" => GetImageHeight($fld),
            "hlprefix" => GetLinkPrefix($fld),
            "listformatobj_filename" => GetFilenameField($fld),
            "lookupobj_lookuptype" => GetLookupType($fld),
            "editformatobj_lookupobj_customdispaly" => GetLWDisplayField($fld),
            "editformatobj_lookupobj_table" => GetLookupTable($fld),
            "editformatobj_lookupobj_where" => GetLWWhere($fld)
        );
	}

}

function make_from_clause($type)
{
	$accessMode=(GetDatabaseType()==3);
	$root=&$_SESSION[$type];
	$ret=AddTableWrappers($root['tables'][0]);
	$fullouter="";
	$firstJoin=true;
	if(is_array($root["table_relations"]["relat"]))
	{
		foreach($root["table_relations"]["relat"] as $r)
		{
			if(trim($r["rel_type"])=="FULL OUTER JOIN")
			{
				$fullouter.="\n,".AddTableWrappers($r["right_table"]);
				continue;
			}
			if($accessMode && !$firstJoin)
			{
				$ret="(".$ret.")";
			}
			$firstJoin=false;
			$ret.="\n".$r["rel_type"]." ".AddTableWrappers($r["right_table"])." ON ";
			$joinon="";
			foreach($r["left_fields"] as $i=>$f)
			{
				if(strlen($joinon))
					$joinon.=" AND ";
				$joinon.=AddTableWrappers($r["left_table"]).".".AddFieldWrappers($r["left_fields"][$i]);
				$joinon.="=";
				$joinon.=AddTableWrappers($r["right_table"]).".".AddFieldWrappers($r["right_fields"][$i]);
			}
			$ret.=$joinon;
		}
	}
	return "FROM ".$ret.$fullouter;
}
?>