<?php 

function DisplayMap($params) 
{
	global $pageObject;
		
	$pageObject->googleMapCfg['mapsData'][$params['id']]['addressField'] = $params['addressField'] ? $params['addressField'] : "";
	$pageObject->googleMapCfg['mapsData'][$params['id']]['latField'] = $params['latField'] ? $params['latField'] : '';
	$pageObject->googleMapCfg['mapsData'][$params['id']]['lngField'] = $params['lngField'] ? $params['lngField'] : '';
	$pageObject->googleMapCfg['mapsData'][$params['id']]['width'] = $params['width'] ? $params['width'] : 0;
	$pageObject->googleMapCfg['mapsData'][$params['id']]['height'] = $params['height'] ? $params['height'] : 0;
	$pageObject->googleMapCfg['mapsData'][$params['id']]['type'] = 'BIG_MAP';
	$pageObject->googleMapCfg['mapsData'][$params['id']]['showCenterLink'] = $params['showCenterLink'] ? $params['showCenterLink'] : 0;
	$pageObject->googleMapCfg['mapsData'][$params['id']]['descField'] = $params['descField'] ? $params['descField'] : $pageObject->googleMapCfg['mapsData'][$params['id']]['addressField'];
	if (isset($params['zoom'])){
		$pageObject->googleMapCfg['mapsData'][$params['id']]['zoom'] = $params['zoom'];
	}
	
	//$pageObject->googleMapCfg['bigMapDefZoom'] = $pageObject->googleMapCfg['mapsData'][$params['id']]['zoom'];
	
	if ($pageObject->googleMapCfg['mapsData'][$params['id']]['showCenterLink'])
	{
		$pageObject->googleMapCfg['mapsData'][$params['id']]['centerLinkText'] = $params['centerLinkText'] ? $params['centerLinkText'] : '';
	}
	$pageObject->googleMapCfg['mainMapIds'][] = $params['id'];
	
	if (isset($params['APIkey']))
	{
		$pageObject->googleMapCfg['APIcode'] = $params['APIkey'];	
	}
		
}

function DisplayCAPTCHA() 
{
	
	global $pageObject, $captchaId;
		
	$pageObject->xt->assign_event($captchaId, $pageObject, 'createCaptcha', array());
}


function checkTableName($shortTName, $type=false)
{
	if (!$shortTName)
	{
		return false;
	}
	if ("webreport_users" == $shortTName && ($type===false || ($type!==false && $type == 0)))
	{
		return true;	
	}
	return false;
}

////////////////////////////////////////////////////////////////////////////////
// table and field info functions
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns array of master tables , which are master for current detail table
 * @param string $tName - it's data source detail table name
 * @return array if success otherwise false
 */
function GetMasterTablesArr($tName) 
{
	global $masterTablesData;
	return $masterTablesData[$tName];
}

/**
 * Returns array of detail tables , which are detail for current master table
 * @param string $tName - it's data source master table name
 * @return array if success otherwise false
 */
function GetDetailTablesArr($tName) 
{
	global $detailsTablesData;
	return $detailsTablesData[$tName];
}

/**
 * Returns array of detail keys for passed masterTable
 *
 * @param string $mTableName - it's master table name, 
 * @param string $tName - it's current (detail) table
 * @return array if success otherwise false
 */
function GetDetailKeysByMasterTable($mTableName = "", $tName = "")
{
	global $masterTablesData;
	if(!$mTableName)
		return false;
	if(!$tName)
		$tName = $strTableName;
	foreach($masterTablesData[$tName] as $mTableDataArr)
	{
		if ($mTableDataArr['mDataSourceTable'] == $mTableName)
			return $mTableDataArr['detailKeys'];
	}
	return false;
}

/**
 * Returns array of master keys for passed detailTable
 *
 * @param string $dTableName - it's detail data sourse table name, 
 * @param string $tName - current(master) table name
 * @return array if success otherwise false
 */
function GetMasterKeysByDetailTable($dTableName, $tName = "")
{
	global $detailsTablesData;
	if(!$dTableName)
		return false;
	if(!$tName)
		$tName = $strTableName;
	foreach ($detailsTablesData[$tName] as $dTableDataArr)
	{
		if ($dTableDataArr['dDataSourceTable'] == $dTableName)
			return $dTableDataArr['masterKeys'];
	}
	return false;
}

/**
 * Returns array of detail keys for passed detailTable
 *
 * @param string $dTableName - It's detail data sourse table name
 * @param string $tName - current(master) table name
 * @return array if success otherwise false
 */
function GetDetailKeysByDetailTable($dTableName, $tName)
{
	global $detailsTablesData;
		
	foreach ($detailsTablesData[$tName] as $dTableDataArr)
	{
		if ($dTableDataArr['dDataSourceTable'] == $dTableName)
			return $dTableDataArr['detailKeys'];
	}
	
	return false;
}
/**
 * Returns details preview Type 
 *
 * @param string $dTableName - it's detail data sourse table name, 
 * @param string $tName - current(master) table name
 * @return array if success otherwise false
 */
function GetDPType($dTableName, $tName) 
{
	global $detailsTablesData;
	if(!$dTableName)
		return false;
	if(!$tName)
		$tName = $strTableName;
	foreach ($detailsTablesData[$tName] as $dTableDataArr)
	{
		if ($dTableDataArr['dDataSourceTable'] == $dTableName)
			return $dTableDataArr['previewOnList'];
	}
	return false;
}



function GetFieldByIndex($index, $table="")
{
	global $strTableName,$tables_data;
	if(!$table) 
		$table = $strTableName;
	if(!array_key_exists($table,$tables_data))
		return null;
	foreach($tables_data[$table] as $key=>$value)
	{
		if(!is_array($value) || !array_key_exists("Index",$value))
			continue;
		if($value["Index"]==$index and GetFieldIndex($key))
			return $key;
	}
	return null;
}

// return field label
function Label($field,$table="")
{
	return GetFieldData($table,$field,"Label",$field);
}


// return filename field if any
function GetFilenameField($field,$table="")
{
	return GetFieldData($table,$field,"Filename","");
}

//	return hyperlink prefix
function GetLinkPrefix($field,$table="")
{
	return GetFieldData($table,$field,"LinkPrefix","");
}

//	return database field type
//	using ADO DataTypeEnum constants
//	the full list available at:
//	http://msdn.microsoft.com/library/default.asp?url=/library/en-us/ado270/htm/mdcstdatatypeenum.asp
function GetFieldType($field,$table="")
{
	return GetFieldData($table,$field,"FieldType","");
}

function IsAutoincField($field,$table="")
{
	return GetFieldData($table,$field,"AutoInc",false);
}

function IsUseiBox($field,$table="")
{
	return GetFieldData($table,$field,"UseiBox",false);
}

//	return Edit format
function GetEditFormat($field,$table="")
{
	return GetFieldData($table,$field,"EditFormat","");
}

//	return View format
function ViewFormat($field,$table="")
{
	return GetFieldData($table,$field,"ViewFormat","");
}

//	show time in datepicker or not
function DateEditShowTime($field,$table="")
{
	return GetFieldData($table,$field,"ShowTime",false);
}

//	use FastType Lookup wizard or not
function FastType($field,$table="")
{
	return GetFieldData($table,$field,"FastType",false);
}

function LookupControlType($field,$table="")
{
	return GetFieldData($table,$field,"LCType",LCT_DROPDOWN);
}


//	is Lookup wizard dependent or not
function UseCategory($field,$table="")
{
	return GetFieldData($table,$field,"UseCategory",false);
}

//	is Lookup wizard with multiple selection
function Multiselect($field,$table="")
{
	return GetFieldData($table,$field,"Multiselect",false);
}

// Lookup wizard select size
function SelectSize($field,$table="")
{
	return GetFieldData($table,$field,"SelectSize",1);
}


function ShowThumbnail($field,$table="")
{
	return GetFieldData($table,$field,"ShowThumbnail",false);
}

function GetImageWidth($field,$table="")
{
	return GetFieldData($table,$field,"ImageWidth",0);
}

function GetImageHeight($field,$table="")
{
	return GetFieldData($table,$field,"ImageHeight",0);
}

//	return Lookup Wizard Where expression
function GetLWWhere($field,$table="")
{
	global $strTableName;
	if(!$table) 
		$table = $strTableName;
	return "";
}

function GetLookupType($field,$table="")
{
	return GetFieldData($table,$field,"LookupType",0);
}

function GetLookupTable($field,$table="")
{
	return GetFieldData($table,$field,"LookupTable","");
}

function GetLWLinkField($field,$table="", $addWrap = true)
{
	if ($addWrap)
	{
		return AddFieldWrappers(GetFieldData($table,$field,"LinkField","")); 
	}
	else 
	{
		return GetFieldData($table,$field,"LinkField","");	
	}		
}

function GetLWLinkFieldType($field,$table="")
{
	return GetFieldData($table,$field,"LinkFieldType",0);
}

function GetLWDisplayField($field,$table="", $addWrap = true)
{
	if ($addWrap && !GetFieldData($table, $field, 'CustomDisplay', false))
	{
		return AddFieldWrappers(GetFieldData($table,$field,"DisplayField","")); 
	}
	else 
	{
		return GetFieldData($table,$field,"DisplayField","");	
	}	
}

function NeedEncode($field,$table="")
{
	return GetFieldData($table,$field,"NeedEncode",false);
}

function AppearOnListPage($field,$table="")
{
	return GetFieldData($table,$field,"ListPage",false);
}

function GetTablesList($pdfMode=false)
{
	$arr=array();
	$strPerm = GetUserPermissions("webreport_users");
	if(strpos($strPerm, "P")!==false || ($pdfMode && strpos($strPerm, "S")!==false))
	{
		$arr[]="webreport_users";
	}
	return $arr;
}


function GetFieldsList($table="")
{
	global $strTableName,$tables_data;
	if(!$table)
		$table = $strTableName;
	if(!array_key_exists($table,$tables_data))
		return array();
	$t = array_keys($tables_data[$table]);
	$arr=array();
	foreach($t as $f)
		if(substr($f,0,1)!=".")
			$arr[]=$f;
	return $arr;
}

function GetBinaryFieldsIndices($table="")
{
	$fields = GetFieldsList($table);
	$out=array();
	foreach($fields as $idx=>$f)
	{
		if(IsBinaryType(GetFieldType($f,$table)))
			$out[]=$idx+1;
	}
	return $out;
}
function GetNBFieldsList($table="")
{
	$t = GetFieldsList($table);
	$arr=array();
	foreach($t as $f)
		if(!IsBinaryType(GetFieldType($f,$table)))
			$arr[]=$f;
	return $arr;
}

//	Category Control field for dependent dropdowns
function CategoryControl($field,$table="")
{
	return GetFieldData($table,$field,"CategoryControl","");
}

//	create Thumbnail or not
function GetCreateThumbnail($field,$table="")
{
	return GetFieldData($table,$field,"CreateThumbnail",false);
}

//	return Thumbnail prefix
function GetThumbnailPrefix($field,$table="")
{
	return GetFieldData($table,$field,"ThumbnailPrefix","");
}

//	resize on upload
function ResizeOnUpload($field,$table="")
{
	return GetFieldData($table,$field,"ResizeImage",false);
}

//	get size to reduce image after upload
function GetNewImageSize($field,$table="")
{
	return GetFieldData($table,$field,"NewSize",0);
}

//	return field name
function GetFieldByGoodFieldName($field,$table="")
{
	global $strTableName,$tables_data;
	if(!$table)
		$table=$strTableName;
	if(!array_key_exists($table,$tables_data))
		return "";

	foreach($tables_data[$table] as $key=>$value)
	{
		if(count($value)>1)
		{
			if($value["GoodName"]==$field)
				return $key;
		}
	}
	return "";
}

//	return the full database field original name
function GetFullFieldName($field,$table="")
{
	$fname=AddTableWrappers(GetOriginalTableName($table)).".".AddFieldWrappers($field);
	return GetFieldData($table,$field,"FullName",$fname);
}

//     return height of text area
function GetNRows($field,$table="")
{
	return GetFieldData($table,$field,"nRows",$field);
}

//     return width of text area
function GetNCols($field,$table="")
{
	return GetFieldData($table,$field,"nCols",$field);
}

//	return original table name
function GetOriginalTableName($table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	return GetTableData($table,".OriginalTable",$table);
}

//	return list of key fields
function GetTableKeys($table="")
{
	return GetTableData($table,".Keys",array());
}


//	return number of chars to show before More... link
function GetNumberOfChars($table="")
{
	return GetTableData($table,".NumberOfChars",0);
}

//	return table short name
function GetTableURL($table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	if("webreport_users"==$table) 
		return "webreport_users";
}

//	return strTableName by short table name
function GetTableByShort($shortTName="")
{	
	if(!$shortTName)
		return false;
	if("webreport_users"==$shortTName) 
		return "webreport_users";
}

//	return table Owner ID field
function GetTableOwnerID($table="")
{
	return GetTableData($table,".OwnerID",0);
}

//	is field marked as required
function IsRequired($field,$table="")
{
	return GetFieldData($table,$field,"IsRequired",false);
}

//	use Rich Text Editor or not
function UseRTE($field,$table="")
{
	return GetFieldData($table,$field,"UseRTE",false);
}

//	add timestamp to filename when uploading files or not
function UseTimestamp($field,$table="")
{
	return GetFieldData($table,$field,"UseTimestamp",false);
}

function GetUploadFolder($field, $table="")
{
	$path = GetFieldData($table,$field,"UploadFolder","");
	if(strlen($path) && substr($path,strlen($path)-1) != "/")
		$path.="/";
	return $path;
}

function GetFieldIndex($field, $table="")
{
	return GetFieldData($table,$field,"Index",0);
}

function GetListOfFieldsByExprType($needaggregate,$table="")
{
	global $strTableName,$tables_data;
	if(!strlen($table))
		$table = $strTableName;
	if(!array_key_exists($table,$tables_data))
		return array();
	$query = &$tables_data[$table][".sqlquery"];
	$fields=GetFieldsList($table);
	$out=array();
	foreach($fields as $idx=>$f)
	{
		$aggr = $query->IsAggrFuncField($idx);
		if($needaggregate && $aggr || !$needaggregate && !$aggr)
			$out[]=$f;
	}
	return $out;
}

//	return Date field edit type
function DateEditType($field,$table="")
{
	return GetFieldData($table,$field,"DateEditType",0);
}

// returns text edit parameters
function GetEditParams($field, $table="")
{
	return GetFieldData($table,$field,"EditParams","");
}

// returns Chart type
function GetChartType($shorttable)
{
	return "";
}

////////////////////////////////////////////////////////////////////////////////
// data output functions
////////////////////////////////////////////////////////////////////////////////

//	format field value for output
function GetData($data, $field, $format)
{
	return GetDataInt($data[$field], $data, $field, $format);
}


//	GetData Internal
function GetDataInt($value, $data, $field, $format)
{
	global $strTableName;
	if($format == FORMAT_CUSTOM && $data)
	{
		return CustomExpression($value,$data,$field,"");
	}

	$ret="";
// long binary data?
	if(IsBinaryType(GetFieldType($field)))
	{
		$ret="LONG BINARY DATA - CANNOT BE DISPLAYED";
	} 
	else
		$ret = $value;
	if($ret===false)
		$ret="";
	
	if($format == FORMAT_DATE_SHORT) 
		$ret = format_shortdate(db2time($value));
	else if($format == FORMAT_DATE_LONG) 
		$ret = format_longdate(db2time($value));
	else if($format == FORMAT_DATE_TIME) 
		$ret = str_format_datetime(db2time($value));
	else if($format == FORMAT_TIME) 
	{
		if(IsDateFieldType(GetFieldType($field)))
			$ret = str_format_time(db2time($value));
		else
		{
			$numbers=parsenumbers($value);
			if(!count($numbers))
				return "";
			while(count($numbers)<3)
				$numbers[]=0;
			$ret = str_format_time(array(0,0,0,$numbers[0],$numbers[1],$numbers[2]));
		}
	}
	else if($format == FORMAT_NUMBER) 
		$ret = str_format_number($value);
	else if($format == FORMAT_CURRENCY) 
		$ret = str_format_currency($value);
	else if($format == FORMAT_CHECKBOX) 
	{
		$ret="<img src=\"images/check_";
		if($value && $value!=0)
			$ret.="yes";
		else
			$ret.="no";
		$ret.=".gif\" border=0";
		if(isEnableSection508())
			$ret.= " alt=\" \"";
		$ret.= ">";
	}
	else if($format == FORMAT_PERCENT) 
	{
		if($value!="")
			$ret = ($value*100)."%";
	}
	else if($format == FORMAT_PHONE_NUMBER)
	{
		if(strlen($ret)==7)
			$ret=substr($ret,0,3)."-".substr($ret,3);
		else if(strlen($ret)==10)
			$ret="(".substr($ret,0,3).") ".substr($ret,3,3)."-".substr($ret,6);
	}
	else if($format == FORMAT_FILE_IMAGE)
	{
		if(!CheckImageExtension($ret))
			return "";
			
		$thumbnailed=false;
		$thumbprefix="";
		if($thumbnailed)
		{
		 	// show thumbnail
			$thumbname=$thumbprefix.$ret;
			if(substr(GetLinkPrefix($field),0,7)!="http://" && !myfile_exists(GetUploadFolder($field).$thumbname))
				$thumbname=$ret;
			$ret="<a target=_blank href=\"".htmlspecialchars(AddLinkPrefix($field,$ret))."\">";
			$ret.="<img";
			if(isEnableSection508())
				$ret.= " alt=\"".htmlspecialchars($data[$field])."\"";
			$ret.=" border=0";
			$ret.=" src=\"".htmlspecialchars(AddLinkPrefix($field,$thumbname))."\"></a>";
		}
		else
			if(isEnableSection508())
				$ret='<img alt=\"".htmlspecialchars($data[$field])."\" src="'.AddLinkPrefix($field,$ret).'" border=0>';
			else
				$ret='<img src="'.htmlspecialchars(AddLinkPrefix($field,$ret)).'" border=0>';
	}
	else if($format == FORMAT_HYPERLINK)
	{
		if($data)
			$ret=GetHyperlink($ret,$field,$data);
	}
	else if($format==FORMAT_EMAILHYPERLINK)
	{
		$link=$ret;
		$title=$ret;
		if(substr($ret,0,7)=="mailto:")
			$title=substr($ret,8);
		else
			$link="mailto:".$link;
		$ret='<a href="'.$link.'">'.$title.'</a>';
	}
	else if($format==FORMAT_FILE)
	{
		$iquery="table=".GetTableURL($strTableName)."&field=".rawurlencode($field);
		$arrKeys = GetTableKeys($strTableName);
		$keylink="";
		for ( $j=0; $j < count($arrKeys); $j++ ) 
		{
			$keylink.="&key" . ($j+1) . "=".rawurlencode($data[$arrKeys[$j]]);
		}
		$iquery.=$keylink;
		return 	'<a href="download.php?'.$iquery.'">'.htmlspecialchars($ret).'</a>';
	}
	else if(GetEditFormat($field)==EDIT_FORMAT_CHECKBOX && $format==FORMAT_NONE)
	{
		if($ret && $ret!=0)
			$ret="Yes";
		else
			$ret="No";
	}
	return $ret;
}


function ProcessLargeText($strValue,$iquery="",$table="", $mode=MODE_LIST)
{
	global $strTableName;

	$cNumberOfChars = GetNumberOfChars($table);
	//??
	if(substr($strValue,0,8)=="<a href=")
		return $strValue;
	//??
	if(substr($strValue,0,23)=="<img src=\"images/check_")
		return $strValue;
	
	/*if($cNumberOfChars>0 && strlen($strValue)>$cNumberOfChars && (strlen($strValue)<200 || !strlen($iquery)) && $mode==MODE_LIST)
	{
		$ret = substr($strValue,0,$cNumberOfChars );
		$ret=htmlspecialchars($ret);
		$ret.=" <a href=\"#\" onClick=\"javascript: pwin = window.open('',null,'height=300,width=400,status=yes,resizable=yes,toolbar=no,menubar=no,location=no,left=150,top=200,scrollbars=yes'); ";
		$ind = 1;
		$ret.="pwin.document.write('" . htmlspecialchars(jsreplace(nl2br(substr($strValue,0, 801)))) ."');";
//		$ret.="pwin.document.write('" . db_addslashes(str_replace("\r\n","<br>",htmlspecialchars(substr($strValue,0, 801)))) ."');";
		$ret.="pwin.document.write('<br><hr size=1 noshade><a href=# onClick=\\'window.close();return false;\\'>"."Close window"."</a>');";
		$ret.="return false;\">"."More"." ...</a>";
	}
	else*/ if($cNumberOfChars>0 && strlen($strValue)>$cNumberOfChars && $mode==MODE_LIST)
	{
		$table = GetTableURL($table);
		$ret = substr($strValue,0,$cNumberOfChars );
		$ret=htmlspecialchars($ret);
		$ret.=" <a href='javascript:void(0);' onClick=\"return DisplayPage(event,'fulltext.php','','','table=".GetTableURL($strTableName)."&".$iquery."','','');\">"."More"." ...</a>";
	}
	else if($cNumberOfChars>0 && strlen($strValue)>$cNumberOfChars && $mode==MODE_PRINT)
	{
		$ret = substr($strValue,0,$cNumberOfChars );
		$ret=htmlspecialchars($ret);
		if(strlen($strValue)>$cNumberOfChars)
			$ret.=" ...";
	}
	else
		$ret= htmlspecialchars($strValue);

/*
//	highlight search results
	if ($mode==MODE_LIST && $_SESSION[$strTableName."_search"]==1)
	{
		$ind = 0;
		$searchopt=$_SESSION[$strTableName."_searchoption"];
		$searchfor=$_SESSION[$strTableName."_searchfor"];
//		highlight Contains search
		if($searchopt=="Contains")
		{
			while ( ($ind = my_stripos($ret, $searchfor, $ind)) !== false )
			{
				$ret = substr($ret, 0, $ind) . "<span class=highlight>". substr($ret, $ind, strlen($searchfor)) ."</span>" . substr($ret, $ind + strlen($searchfor));
				$ind+= strlen("<span class=highlight>") + strlen($searchfor) + strlen("</span>");
			}
		}
//		highlight Starts with search
		elseif($searchopt=="Starts with ...")
		{
			if( !strncasecmp($ret, $searchfor,strlen($searchfor)) )
				$ret = "<span class=highlight>". substr($ret, 0, strlen($searchfor)) ."</span>" . substr($ret, strlen($searchfor));
		}
		elseif($searchopt=="Equals")
		{
			if( !strcasecmp($ret, $searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="More than ...")
		{
			if( strtoupper($ret)>strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="Less than ...")
		{
			if( strtoupper($ret)<strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="Equal or more than ...")
		{
			if( strtoupper($ret)>=strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="Equal or less than ...")
		{
			if( strtoupper($ret)<=strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
	}
*/
	return nl2br($ret);
}

//	construct hyperlink
function GetHyperlink($str, $field,$data,$table="")
{
	global $strTableName;
	if(!strlen($table))
		$table=$strTableName;
	if(!strlen($str))
		return "";
	$ret=$str;
	$title=$ret;
	$link=$ret;
	if(substr($ret,strlen($ret)-1)=='#')
	{
		$i=strpos($ret,'#');
		$title=substr($ret,0,$i);
		$link=substr($ret,$i+1,strlen($ret)-$i-2);
		if(!$title)
			$title=$link;
	}
	$target="";
	
	if(strpos($link,"://")===false && substr($link,0,7)!="mailto:")
		$link=$prefix.$link;
	$ret='<a href="'.$link.'"'.$target.'>'.$title.'</a>';
	return $ret;
}

//	add prefix to the URL
function AddLinkPrefix($field,$link,$table="")
{
	if(strpos($link,"://")===false && substr($link,0,7)!="mailto:")
		return GetLinkPrefix($field,$table).$link;
	return $link;
}

function GetTotalsForTime($value,$arr)
{
	$nsec=0;
	$nmin=0;
	if($value!='')
	{
		$time=parsenumbers($value);
		if(!empty($time))
		{
			if(count($time)==3 && is_numeric($time[2]))
			{
				$nsec=$arr[2]+$time[2];
				if($nsec>59)
				{	
					$arr[2]=$nsec-60;
					$time[1]+=1;
				}
				else $arr[2]+=$time[2];
			}
			if(count($time)>1 && is_numeric($time[1]))
			{
				$nmin=$arr[1]+$time[1];  
				if($nmin>59)
				{
					$arr[1]=$nmin-60;
					$time[0]+=1;	
				}
				else $arr[1]+=$time[1];
			}
			if(is_numeric($time[0]))
				$arr[0]+=$time[0];
		}
	}
	return $arr;
}


//	return Totals string
function GetTotals($field,$value, $stype, $iNumberOfRows,$sFormat)
{
	$days=0;
	if($stype=="AVERAGE")
	{
		if($iNumberOfRows)
		{	
			if($sFormat == FORMAT_TIME)
			{
				if($value!='')
				{
					$pr=parsenumbers($value);
					if(!empty($pr) && count($pr)==3)
					{
						$avhor=round($pr[0]/$iNumberOfRows,0);
						if($avhor>23)
						{
							$days=floor($avhor/24);
							$avhor=$avhor-$days*24;
						}
						$avmin=round($pr[1]/$iNumberOfRows,0);
						$avsec=round($pr[2]/$iNumberOfRows,0);
						$value=($days!=0 ? $days.'d ' : '').$avhor.':'.($avmin>9 ? $avmin : ($avmin==0 ? '00' : '0'.$avmin)).':'.($avsec>9 ? $avsec : ($avsec==0 ? '00' : '0'.$avsec));
					}
				}
			}
			else $value=round($value/$iNumberOfRows,2);	
		}
		else
			return "";
	}
	if($stype=="TOTAL")
	{
		if($sFormat == FORMAT_TIME)
		{
			if($value!='')
			{
				$pr=parsenumbers($value);
				if(!empty($pr)&& count($pr)==3)
				{
					if($pr[0]>23)
					{	
						$days=floor($pr[0]/24);
						$pr[0]=$pr[0]-$days*24;
					}
					$value=($days!=0 ? $days.'d ' :'').($pr[0]==0 ? '00' : $pr[0]).':'.($pr[1]==0 ? '00' : ($pr[1]>9 ? $pr[1] : '0'.$pr[1])).':'.($pr[2]==0 ? '00' : ($pr[2]>9 ? $pr[2] : '0'.$pr[2]));
				}
			}
		}
	}
	$sValue="";
	$data=array($field=>$value);
	if($sFormat == FORMAT_CURRENCY)
	 	$sValue = str_format_currency($value);
	else if($sFormat == FORMAT_PERCENT)
		$sValue = str_format_number($value*100)."%"; 
	else if($sFormat == FORMAT_NUMBER)
 		$sValue = str_format_number($value);
	else if($sFormat == FORMAT_CUSTOM && $stype!="COUNT")
 		$sValue = GetData($data,$field,$sFormat);
	else 
 		$sValue = $value;

	if($stype=="COUNT") 
		return $value;
	if($stype=="TOTAL") 
		return $sValue;
	if($stype=="AVERAGE") 
		return $sValue;
	return "";
}


//	display Lookup Wizard value in List/View mode
function DisplayLookupWizard($field, $value, $data, $keylink, $mode)
{
	global $conn, $strTableName;
	if(!strlen($value))
		return "";
	$LookupSQL="SELECT ";
	$LookupSQL.=GetLWDisplayField($field);
	$LookupSQL.=" FROM ".AddTableWrappers(GetLookupTable($field))." WHERE ";
	$where="";
	$lookupvalue=$value;
	$iquery = "field=".htmlspecialchars(rawurlencode($field)).$keylink; 
	if(Multiselect($field))
	{
		$arr = splitvalues($value);
		$numeric=true;
		$type = GetLWLinkFieldType($field);
		if(!$type)
		{
			foreach($arr as $val)
				if(strlen($val) && !is_numeric($val))
				{
					$numeric=false;
					break;
				}
		}
		else
			$numeric = !NeedQuotes($type);
		$in="";
		foreach($arr as $val)
		{
			if($numeric && !strlen($val))
				continue;
			if(strlen($in))
				$in.=",";
			if($numeric)
				$in.=($val+0);
			else
				$in.="'".db_addslashes($val)."'";
		}
		if(strlen($in))
		{
			$LookupSQL.= GetLWLinkField($field)." in (".$in.")";
			$where = GetLWWhere($field);
			if(strlen($where))
				$LookupSQL.=" and (".$where.")";
			LogInfo($LookupSQL);
			$rsLookup = db_query($LookupSQL,$conn);
			$found=false;
			$out="";
			while($lookuprow=db_fetch_numarray($rsLookup))
			{
				$lookupvalue=$lookuprow[0];
				if($found)
					$out.=",";
				$found = true;
				$out.=GetDataInt($lookupvalue,$data,$field, ViewFormat($field));
			}
			if($found)
			{
				if(NeedEncode($field) && $mode!=MODE_EXPORT)
					return ProcessLargeText($out,$iquery,"",$mode);
				else
					return $out;
			}
		}
	}
	else
	{
		$strdata = make_db_value($field,$value);
		$LookupSQL.=GetLWLinkField($field)." = " . $strdata;
		$where = GetLWWhere($field);
		if(strlen($where))
			$LookupSQL.=" and (".$where.")";
		LogInfo($LookupSQL);
		$rsLookup = db_query($LookupSQL,$conn);
		if($lookuprow=db_fetch_numarray($rsLookup))
			$lookupvalue=$lookuprow[0];
	}
	if(NeedEncode($field) && $mode!=MODE_EXPORT)
		$value=ProcessLargeText(GetDataInt($lookupvalue,$data,$field, ViewFormat($field)),$iquery,"",$mode);
	else
		$value=GetDataInt($lookupvalue,$data,$field, ViewFormat($field));
	return $value;
}

function DisplayNoImage()
{
	$path = getabspath("images/no_image.gif");
	header("Content-Type: image/gif");
	printfile($path);
}

function DisplayFile()
{
	$path = getabspath("images/file.gif");
	header("Content-Type: image/gif");
	printfile($path);
}

////////////////////////////////////////////////////////////////////////////////
// miscellaneous functions
////////////////////////////////////////////////////////////////////////////////



//	analog of strrpos function
function my_strrpos($haystack, $needle) {
   $index = strpos(strrev($haystack), strrev($needle));
   if($index === false) {
       return false;
   }
   $index = strlen($haystack) - strlen($needle) - $index;
   return $index;
}

//	utf-8 analog of strlen function
function strlen_utf8($str)
{
	$len=0;
	$i=0;
	$olen=strlen($str);
	while($i<$olen)
	{
		$c=ord(substr($str,$i,1));
		if($c<128)
			$i++;
		else if($i<$olen-1 && $c>=192 && $c<=223)
			$i+=2;
		else if($i<$olen-2 && $c>=224 && $c<=239)
			$i+=3;
		else if($i<$olen-3 && $c>=240)
			$i+=4;
		else
			break;
		$len++;
	}
	return $len;
}

//	utf-8 analog of substr function
function substr_utf8($str,$index,$strlen)
{
	if($strlen<=0)
		return "";
	$len=0;
	$i=0;
	$olen=strlen($str);
	$oindex=-1;
	while($i<$olen)
	{
		if($len==$index)
			$oindex=$i;
		
		$c=ord(substr($str,$i,1));
		if($c<128)
			$i++;
		else if($i<$olen-1 && $c>=192 && $c<=223)
			$i+=2;
		else if($i<$olen-2 && $c>=224 && $c<=239)
			$i+=3;
		else if($i<$olen-3 && $c>=240)
			$i+=4;
		else
			break;
		$len++;
		if($oindex>=0 && $len==$index+$strlen)
			return substr($str,$oindex,$i-$oindex);
	}
	if($oindex>0)
		return substr($str,$oindex,$olen-$oindex);
	return "";
}


//	prepare string for JavaScript. Replace ' with \' and linebreaks with \r\n
function jsreplace($str)
{
	$ret= str_replace(array("\\","'","\r","\n"),array("\\\\","\\'","\\r","\\n"),$str);
	return my_str_ireplace("</script>","</scr'+'ipt>",$ret);
}


function LogInfo($SQL)
{
//	global $dSQL,$dDebug;
//	$dSQL=$SQL;
//	if($dDebug)
//	{
//		echo $dSQL;
//		echo "<br>";
//	}
}


//	check if file extension is image extension
function CheckImageExtension($filename)
{
	if(strlen($filename)<4)
		return false;
	$ext=strtoupper(substr($filename,strlen($filename)-4));
	if($ext==".GIF" || $ext==".JPG" || $ext=="JPEG" || $ext==".PNG" || $ext==".BMP")
		return $ext;
	return false;
} 























































































function RTESafe($strText)
{
//	returns safe code for preloading in the RTE
	$tmpString="";
	
	$tmpString = trim($strText);
	if(!$tmpString) return "";
	
//	convert all types of single quotes
	$tmpString = str_replace( chr(145), chr(39),$tmpString);
	$tmpString = str_replace( chr(146), chr(39),$tmpString);
	$tmpString = str_replace("'", "&#39;",$tmpString);
	
//	convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34),$tmpString);
	$tmpString = str_replace(chr(148), chr(34),$tmpString);
	
//	replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ",$tmpString);
	$tmpString = str_replace(chr(13), " ",$tmpString);
	
	return $tmpString;
}



function html_special_decode($str)
{
	$ret=$str;
	$ret=str_replace("&gt;",">",$ret);
	$ret=str_replace("&lt;","<",$ret);
	$ret=str_replace("&quot;","\"",$ret);
	$ret=str_replace("&#039;","'",$ret);
	$ret=str_replace("&#39;","'",$ret);
	$ret=str_replace("&amp;","&",$ret);
	return $ret;
}

////////////////////////////////////////////////////////////////////////////////
// database and SQL related functions
////////////////////////////////////////////////////////////////////////////////

function CalcSearchParameters()
{
	global $strTableName, $strSQL;
	$sWhere="";
	if(@$_SESSION[$strTableName."_search"]==2)
//	 advanced search
	{
		foreach(@$_SESSION[$strTableName."_asearchfor"] as $f => $sfor)
		{
			$strSearchFor=trim($sfor);
			$strSearchFor2="";
			$type=@$_SESSION[$strTableName."_asearchfortype"][$f];
			if(array_key_exists($f,@$_SESSION[$strTableName."_asearchfor2"]))
				$strSearchFor2=trim(@$_SESSION[$strTableName."_asearchfor2"][$f]);
			if($strSearchFor!="" || true)
			{
				if (!$sWhere) 
				{
					if($_SESSION[$strTableName."_asearchtype"]=="and")
						$sWhere="1=1";
					else
						$sWhere="1=0";
				}
				$strSearchOption=trim($_SESSION[$strTableName."_asearchopt"][$f]);
				if($where=StrWhereAdv($f, $strSearchFor, $strSearchOption, $strSearchFor2,$type))
				{
					if($_SESSION[$strTableName."_asearchnot"][$f])
						$where="not (".$where.")";
					if($_SESSION[$strTableName."_asearchtype"]=="and")
	   					$sWhere .= " and ".$where;
					else
	   					$sWhere .= " or ".$where;
				}
			}
		}
	}
	return $sWhere;
}

//	add WHERE condition to gstrSQL
function gSQLWhere($where,$having="")
{
	global $gsqlFrom,$gsqlWhereExpr, $gQuery;
	
	$sqlHead = $gQuery->HeadToSql();
	$sqlGroupBy = $gQuery->GroupByToSql();
	$oHaving = $gQuery->Having();
	$sqlHaving = $oHaving->toSql($gQuery);
	
	return gSQLWhere_having($sqlHead,$gsqlFrom,$gsqlWhereExpr,$sqlGroupBy, $sqlHaving, $where, $having);
}

function gSQLWhere_having($sqlHead,$sqlFrom,$sqlWhere,$sqlGroupBy,$sqlHaving,$where="", $having="")
{
	$strWhere=whereAdd($sqlWhere,$where);
	if(strlen($strWhere))
		$strWhere=" where ".$strWhere." ";
	$strHaving = havingAdd($sqlHaving, $having);
	if (strlen($strHaving))
		$strHaving =" having ".$strHaving." ";
	return $sqlHead." ".$sqlFrom.' '.$strWhere.' '.$sqlGroupBy.' '.$strHaving;
}

//	add clause to HAVING expression
function havingAdd($having,$clause)
{
	if(!strlen($clause))
		return $having;
	if(!strlen($having))
		return $clause;
	return "(".$having.") and (".$clause.")";
}

//	add clause to WHERE expression
function whereAdd($where,$clause)
{
	if(!strlen($clause))
		return $where;
	if(!strlen($where))
		return $clause;
	return "(".$where.") and (".$clause.")";
}

//	add WHERE clause to SQL string
function AddWhere($sql,$where)
{
	if(!strlen($where))
		return $sql;
	$sql=str_replace(array("\r\n","\n","\t")," ",$sql);
	$tsql = strtolower($sql);
	$n = my_strrpos($tsql," where ");
	$n1 = my_strrpos($tsql," group by ");
	$n2 = my_strrpos($tsql," order by ");
	if($n1===false)
		$n1=strlen($tsql);
	if($n2===false)
		$n2=strlen($tsql);
	if ($n1>$n2)
		$n1=$n2;
	if($n===false)
		return substr($sql,0,$n1)." where ".$where.substr($sql,$n1);
	else
		return substr($sql,0,$n+strlen(" where "))."(".substr($sql,$n+strlen(" where "),$n1-$n-strlen(" where ")).") and (".$where.")".substr($sql,$n1);
}

//	construct WHERE clause with key values
function KeyWhere(&$keys, $table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	$strWhere="";
	
	$keyFields = GetTableKeys($table);
	foreach($keyFields as $kf)
	{
		if(strlen($strWhere))
			$strWhere.=" and ";
		$value=make_db_value($kf,$keys[$kf]);
			$valueisnull = ($value==="null");
		if($valueisnull)
			$strWhere.=GetFullFieldName($kf,$table)." is null";
		else
			$strWhere.=GetFullFieldName($kf,$table)."=".make_db_value($kf,$keys[$kf]);
	}
	return $strWhere;
}

//	consctruct SQL WHERE clause for simple search
function StrWhereExpression($strField, $SearchFor, $strSearchOption, $SearchFor2)
{
	global $strTableName;
	$type=GetFieldType($strField);
	
	$ismssql=false;
	
	$isdb2=false;

	$isMysql = false;

	$btexttype=IsTextType($type);

	if($strSearchOption=='Empty')
	{
		if(IsCharType($type) && (!$ismssql || !$btexttype))
			return "(".GetFullFieldName($strField)." is null or ".GetFullFieldName($strField)."='')";			
		elseif ($ismssql && $btexttype)	
			return "(".GetFullFieldName($strField)." is null or ".GetFullFieldName($strField)." LIKE '')";
		else
			return GetFullFieldName($strField)." is null";
	}
	$strQuote="";
	if(NeedQuotes($type))
		$strQuote = "'";
//	return none if trying to compare numeric field and string value
	$sSearchFor=$SearchFor;
	$sSearchFor2=$SearchFor2;
	if(IsBinaryType($type))
		return "";
	

	
	if(IsDateFieldType($type) && $strSearchOption!="Contains" && $strSearchOption!="Starts with" )
	{
		$time=localdatetime2db($SearchFor);
		if($time=="null")
			return "";
		$sSearchFor=db_datequotes($time);
		if($strSearchOption=="Between")
		{
			$time=localdatetime2db($SearchFor2);
			if($time=="null")
				$sSearchFor2="";
			else
				$sSearchFor2=db_datequotes($time);
		}
	}
	
	if(!$strQuote && !is_numeric($sSearchFor) && !is_numeric($sSearchFor))
		return "";
	else if(!$strQuote && $strSearchOption!="Contains" && $strSearchOption!="Starts with")
	{
		$sSearchFor = 0+$sSearchFor;
		$sSearchFor2 = 0+$sSearchFor2;
	}
	else if(!IsDateFieldType($type) && $strSearchOption!="Contains" && $strSearchOption!="Starts with")
	{
		if($btexttype)
		{
			$sSearchFor=$strQuote.db_addslashes($sSearchFor).$strQuote;
			if($strSearchOption=="Between" && $sSearchFor2)
				$sSearchFor2=$strQuote.db_addslashes($sSearchFor2).$strQuote;
		}
		else
		{
			$sSearchFor=isEnableUpper($strQuote.db_addslashes($sSearchFor).$strQuote);
			if($strSearchOption=="Between" && $sSearchFor2)
				$sSearchFor2=isEnableUpper($strQuote.db_addslashes($sSearchFor2).$strQuote);
		}
	}
	else if(!IsDateFieldType($type) || $strSearchOption=="Contains" || $strSearchOption=="Starts with" )
		$sSearchFor=db_addslashes($sSearchFor);
		

	if(IsCharType($type) && !$btexttype)
		$strField=isEnableUpper(GetFullFieldName($strField));
	elseif ($ismssql && !$btexttype && ($strSearchOption=="Contains" || $strSearchOption=="Starts with"))
		$strField="convert(varchar(50),".GetFullFieldName($strField).")";
	elseif ($isdb2 && !$btexttype && ($strSearchOption=="Contains" || $strSearchOption=="Starts with"))
		$strField="char(".GetFullFieldName($strField).")";
	else 
		$strField=GetFullFieldName($strField);
	$ret="";
		$like="like";
	
	if ($isMysql)
	{	
		$sSearchForMysql = str_replace('\\\\', '\\\\\\\\', $sSearchFor); 
	}
	if($strSearchOption=="Contains")
	{		
		if ($isMysql)
		{
			$sSearchFor = $sSearchForMysql;
		}
		
		if(IsCharType($type) && !$btexttype)
			return $strField." ".$like." ".isEnableUpper("'%".$sSearchFor."%'");
		else
			return $strField." ".$like." '%".$sSearchFor."%'";
	}
	else if($strSearchOption=="Equals") return $strField."=".$sSearchFor;
	else if($strSearchOption=="Starts with")
	{
		if ($isMysql)
		{
			$sSearchFor = $sSearchForMysql;
		}
		if(IsCharType($type) && !$btexttype)
			return $strField." ".$like." ".isEnableUpper("'".$sSearchFor."%'");
		else
			return $strField." ".$like." '".$sSearchFor."%'";
	}
	else if($strSearchOption=="More than") return $strField.">".$sSearchFor;
	else if($strSearchOption=="Less than") return $strField."<".$sSearchFor;
	else if($strSearchOption=="Between")
	{		
		$ret=$strField.">=".$sSearchFor;
		if($sSearchFor2) $ret.=" and ".$strField."<=".$sSearchFor2;
			return $ret;
	}
	return "";
}

//	construct SQL WHERE clause for Advanced search
function StrWhereAdv($strField, $SearchFor, $strSearchOption, $SearchFor2, $etype, $isSuggest=false)
{
	global $strTableName;
	$type=GetFieldType($strField);
	$isOracle = false;	

	$ismssql=false;

	$isdb2=false;
	
	$btexttype=IsTextType($type);

	$isMysql = false;

	if(IsBinaryType($type))
		return "";
	if($strSearchOption=='Empty')
	{		
		if(IsCharType($type) && (!$ismssql || !$btexttype) && !$isOracle)
		{
			return "(".GetFullFieldName($strField)." is null or ".GetFullFieldName($strField)."='')";
		}			
		elseif ($ismssql && $btexttype)
		{	
			return "(".GetFullFieldName($strField)." is null or ".GetFullFieldName($strField)." LIKE '')";
		}
		else
		{
			return GetFullFieldName($strField)." is null";
		}
	}
		$like="like";
	
	
	if(GetEditFormat($strField)==EDIT_FORMAT_LOOKUP_WIZARD)
	{
		
		if(Multiselect($strField))
			$SearchFor=splitvalues($SearchFor);
		else
			$SearchFor=array($SearchFor);
		$ret="";
		foreach($SearchFor as $value)
		{
			if(!($value=="null" || $value=="Null" || $value==""))
			{
				if(strlen($ret))
					$ret.=" or ";
				if($strSearchOption=="Equals")
				{
					$value=make_db_value($strField,$value);
					if(!($value=="null" || $value=="Null"))
						$ret.=GetFullFieldName($strField).'='.$value;
				}
				elseif($isSuggest)
				{
					$ret.=" ".GetFullFieldName($strField)." ".$like." '%".$value."%'";	
				}
				else
				{
					if(strpos($value,",")!==false || strpos($value,'"')!==false)
						$value = '"'.str_replace('"','""',$value).'"';
					$value=db_addslashes($value);
					if ($isMysql)
					{	
						$value = str_replace('\\\\', '\\\\\\\\', $value); 
					}
					$ret.=GetFullFieldName($strField)." = '".$value."'";
					$ret.=" or ".GetFullFieldName($strField)." ".$like." '%,".$value.",%'";
					$ret.=" or ".GetFullFieldName($strField)." ".$like." '%,".$value."'";
					$ret.=" or ".GetFullFieldName($strField)." ".$like." '".$value.",%'";
				}
			}
		}
		if(strlen($ret))
			$ret="(".$ret.")";
		return $ret;
	}
	if(GetEditFormat($strField)==EDIT_FORMAT_CHECKBOX)
	{
		if($SearchFor=="none")
			return "";			
			
		if(NeedQuotes($type))
		{
							$isOracle = false;	
			
			if($SearchFor=="on")
			{			
				$whereStr = "(".GetFullFieldName($strField)."<>'0' ";
				if (!$isOracle)
				{
					$whereStr .= " and ".GetFullFieldName($strField)."<>'' ";
				} 
				$whereStr .= " and ".GetFullFieldName($strField)." is not null)";
				return $whereStr;				
			}
			elseif($SearchFor=="off")
			{
				$whereStr = "(".GetFullFieldName($strField)."='0' ";
				if (!$isOracle)
				{
					$whereStr .= " or ".GetFullFieldName($strField)."='' "; 
				}
				$whereStr .= " or ".GetFullFieldName($strField)." is null)";
			}
		}
		else
		{
			if($SearchFor=="on")
			{
				return "(".GetFullFieldName($strField)."<>0 and ".GetFullFieldName($strField)." is not null)";
			}
			elseif($SearchFor=="off")
			{
				return "(".GetFullFieldName($strField)."=0 or ".GetFullFieldName($strField)." is null)";
			}
		}
	}
	$value1=make_db_value($strField,$SearchFor,$etype);
	
	$value2=false;
	$cleanvalue2=false;
	if($strSearchOption=="Between")
	{
		$cleanvalue2=prepare_for_db($strField,$SearchFor2,$etype);
		$value2=make_db_value($strField,$SearchFor2,$etype);
	}
		
	if($strSearchOption!="Contains" && $strSearchOption!="Starts with" && ($value1==="null" || $value2==="null" ))
		return "";

	if(IsCharType($type) && !$btexttype)
	{
		$value1=isEnableUpper($value1);
		$value2=isEnableUpper($value2);
		$gstrField=isEnableUpper(GetFullFieldName($strField));
	}
	elseif ($ismssql && !$btexttype && ($strSearchOption=="Contains" || $strSearchOption=="Starts with"))
		$gstrField="convert(varchar,".GetFullFieldName($strField).")";
	elseif ($isdb2 && !$btexttype && ($strSearchOption=="Contains" || $strSearchOption=="Starts with"))
		$gstrField="char(".GetFullFieldName($strField).")";
	else 
		$gstrField=GetFullFieldName($strField);
	$ret="";
	
		
	
	if($strSearchOption=="Contains")
	{
		$SearchFor = db_addslashes($SearchFor);
		
		if ($isMysql)
		{
			$SearchFor = str_replace('\\\\', '\\\\\\\\', $SearchFor);
		}	
		if(IsCharType($type) && !$btexttype)
			return $gstrField." ".$like." ".isEnableUpper("'%".$SearchFor."%'");
		else
			return $gstrField." ".$like." '%".$SearchFor."%'";
	}
	else if($strSearchOption=="Equals") return $gstrField."=".$value1;
	else if($strSearchOption=="Starts with")
	{
		$SearchFor = db_addslashes($SearchFor);
		
		if ($isMysql)
		{
			$SearchFor = str_replace('\\\\', '\\\\\\\\', $SearchFor);
		}	
		if(IsCharType($type) && !$btexttype)
			return $gstrField." ".$like." ".isEnableUpper("'".$SearchFor."%'");
		else
			return $gstrField." ".$like." '".$SearchFor."%'";
	}
	else if($strSearchOption=="More than") return $gstrField.">".$value1;
	else if($strSearchOption=="Less than") return $gstrField."<".$value1;
	else if($strSearchOption=="Equal or more than") return $gstrField.">=".$value1;
	else if($strSearchOption=="Equal or less than") return $gstrField."<=".$value1;
	else if($strSearchOption=="Between")
	{
		$ret=$gstrField.">=".$value1." and ";		
		if (IsDateFieldType($type))
		{
			$timeArr = db2time($cleanvalue2);
			// for dates without time, add one day
			if ($timeArr[3]==0 && $timeArr[4]==0 && $timeArr[5]==0)
			{
				$timeArr = adddays($timeArr, 1);
				$value2 = $timeArr[0]."-".$timeArr[1]."-".$timeArr[2];
				$value2 = make_db_value($strField, $value2, $etype);
				$ret .= $gstrField."<".$value2;
			}
			else
			{
				$ret.=$gstrField."<=".$value2;
			}
		}
		else 
		{
			$ret.=$gstrField."<=".$value2;
		}
		return $ret;
	}
	return "";
}

//	get count of rows from the query
function gSQLRowCount($where,$groupby=false)
{
	global $gsqlFrom,$gsqlWhereExpr;
	global $gQuery;
	
	$sqlHead = $gQuery->HeadToSql();
	$sqlGroupBy = $gQuery->GroupByToSql();
	
	$oHaving = $gQuery->Having();	
	$sqlHaving = $oHaving->toSql($gQuery);
	
	return gSQLRowCount_int($sqlHead,$gsqlFrom,$gsqlWhereExpr,$sqlGroupBy, $sqlHaving,$where,$groupby);
}

function gSQLRowCount_int($sqlHead,$sqlFrom,$sqlWhere,$sqlGroupBy, $sqlHaving,$where,$groupby=false)
{
	global $conn;
	global $bSubqueriesSupported;
	
	$strWhere=whereAdd($sqlWhere,$where);
	if(strlen($strWhere))
		$strWhere=" where ".$strWhere." ";
	
	if($groupby)
	{
			$countstr = "select count(*) from (".gSQLWhere_having($sqlHead,$sqlFrom,$sqlWhere,$sqlGroupBy, $sqlHaving,$where).") a";
	}
	else
	{
		$countstr = "select count(*) ".$sqlFrom.$strWhere.$sqlGroupBy.$sqlHaving;
	}
	
	$countrs = db_query($countstr, $conn);
	$countdata = db_fetch_numarray($countrs);
	return $countdata[0];
}

//	get count of rows from the query
function GetRowCount($strSQL)
{
	global $conn;
	$strSQL=str_replace(array("\r\n","\n","\t")," ",$strSQL);
	$tstr = strtoupper($strSQL);
	$ind1 = strpos($tstr,"SELECT ");
	$ind2 = my_strrpos($tstr," FROM ");
	$ind3 = my_strrpos($tstr," GROUP BY ");
	if($ind3===false)
	{
		$ind3 = strpos($tstr," ORDER BY ");
		if($ind3===false)
			$ind3=strlen($strSQL);
	}
	$countstr=substr($strSQL,0,$ind1+6)." count(*) ".substr($strSQL,$ind2+1,$ind3-$ind2);
	$countrs = db_query($countstr,$conn);
	$countdata = db_fetch_numarray($countrs);
	return $countdata[0];
}

//	add MSSQL Server TOP clause
function AddTop($strSQL, $n)
{
	$tstr = strtoupper($strSQL);
	$ind1 = strpos($tstr,"SELECT");
	return substr($strSQL,0,$ind1+6)." top ".$n." ".substr($strSQL,$ind1+6);
}
//	add DB2 Server TOP clause
function AddTopDB2($strSQL, $n)
{
	
	return $strSQL." fetch first ".$n." rows only";
}
function AddTopIfx($strSQL,$n)
{
	return substr($strSQL,0,7)."limit ".$n." ".substr($strSQL,7);
}
//	add Oracle ROWNUMBER checking
function AddRowNumber($strSQL, $n)
{
	return "select * from (".$strSQL.") where rownum<".($n+1);
}

// test database type if values need to be quoted
function NeedQuotesNumeric($type)
{
    if($type == 203 || $type == 8 || $type == 129 || $type == 130 || 
		$type == 7 || $type == 133 || $type == 134 || $type == 135 ||
		$type == 201 || $type == 205 || $type == 200 || $type == 202 || $type==72 || $type==13)
		return true;
	else
		return false;
}

//	using ADO DataTypeEnum constants
//	the full list available at:
//	http://msdn.microsoft.com/library/default.asp?url=/library/en-us/ado270/htm/mdcstdatatypeenum.asp

function IsNumberType($type)
{
	if($type==20 || $type==6 || $type==14 || $type==5 || $type==10 
	|| $type==3 || $type==131 || $type==4 || $type==2 || $type==16
	|| $type==21 || $type==19 || $type==18 || $type==17 || $type==139
	|| $type==11)
		return true;
	return false;
}

function IsFloatType($type)
{
	if($type==14 || $type==5 || $type==131 || $type==6)
		return true;
	return false;
}


function NeedQuotes($type)
{
	return !IsNumberType($type);
}

function IsBinaryType($type)
{
	if($type==128 || $type==205 || $type==204)
		return true;
	return false;
}

function IsDateFieldType($type)
{
	if($type==7 || $type==133 || $type==135)
		return true;
	return false;
}

function IsTimeType($type)
{
	if($type==134)
		return true;
	return false;
}

function IsCharType($type)	
{
	if(IsTextType($type) || $type==8 || $type==129 || $type==200 || $type==202 || $type==130)
		return true;
	return false;
}

function IsTextType($type)
{
	if($type==201 || $type==203)
		return true;
	return false;
}

////////////////////////////////////////////////////////////////////////////////
// security functions
////////////////////////////////////////////////////////////////////////////////


//	return user permissions on the table
//	A - Add
//	D - Delete
//	E - Edit
//	S - List/View/Search
//	P - Print/Export


function IsAdmin()
{
	return false;
}

function GetUserPermissionsStatic($table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;

	$sUserGroup=@$_SESSION["GroupID"];
	if($table=="webreport_users" && $sUserGroup=="<Guest>")
	{
				return "";
	}
	if($table=="webreport_users" && $sUserGroup=="admin")
	{
				return "AEDSPI";
	}
//	default permissions	
	if($table=="webreport_users")
	{
		return "AEDSPI";
	}
}

function GetUserPermissions($table="")
{
	return GetUserPermissionsStatic($table);
}


//	check whether field is viewable
function CheckFieldPermissions($field, $table="")
{
	return GetFieldData($table,$field,"FieldPermissions",false);
}

// 
function CheckSecurity($strValue, $strAction)
{
global $cAdvSecurityMethod, $strTableName;
	if($_SESSION["AccessLevel"]==ACCESS_LEVEL_ADMIN)
		return true;

	$strPerm = GetUserPermissions();
	if(@$_SESSION["AccessLevel"]!=ACCESS_LEVEL_ADMINGROUP && strpos($strPerm, "M")===false)
	{
	}
	//	 check user group permissions
	if($strAction=="Add" && !(strpos($strPerm, "A")===false) ||
	   $strAction=="Edit" && !(strpos($strPerm, "E")===false) ||
	   $strAction=="Delete" && !(strpos($strPerm, "D")===false) ||
	   $strAction=="Search" && !(strpos($strPerm, "S")===false) ||
	   $strAction=="Import" && !(strpos($strPerm, "I")===false) ||
	   $strAction=="Export" && !(strpos($strPerm, "P")===false) )
		return true;
	else
		return false;
	return true;
}


//	add security WHERE clause to SELECT SQL command
function SecuritySQL($strAction, $table="")
{
	global $cAdvSecurityMethod,$strTableName;
	
	if (!strlen($table))	
		$table = $strTableName;
		
   	$ownerid=@$_SESSION["_".$table."_OwnerID"];
	$ret="";
	if(@$_SESSION["AccessLevel"]==ACCESS_LEVEL_ADMIN)
		return "";
		
	$ret="";
	$strPerm = GetUserPermissions($table);

	if(@$_SESSION["AccessLevel"]!=ACCESS_LEVEL_ADMINGROUP)
	{
	}
	
	if($strAction=="Edit" && !(strpos($strPerm, "E")===false) ||
	   $strAction=="Delete" && !(strpos($strPerm, "D")===false) ||
	   $strAction=="Search" && !(strpos($strPerm, "S")===false) ||
	   $strAction=="Export" && !(strpos($strPerm, "P")===false) )
		return $ret;
	else
		return "1=0";
	return "";
}

////////////////////////////////////////////////////////////////////////////////
// editing functions
////////////////////////////////////////////////////////////////////////////////

function make_db_value($field,$value,$controltype="",$postfilename="",$table="")
{	
	$ret=prepare_for_db($field,$value,$controltype,$postfilename,$table);
	
	if($ret===false)
		return $ret;
	return add_db_quotes($field,$ret,$table);
}

function add_db_quotes($field,$value,$table="")
{
	global $strTableName;
	$type = GetFieldType($field, $table);
	if(IsBinaryType($type))
		return db_addslashesbinary($value);
	if(($value==="" || $value===FALSE) && !IsCharType($type))
		return "null";
	if(NeedQuotes($type))
	{
		if(!IsDateFieldType($type))
			$value="'".db_addslashes($value)."'";
		else
			$value=db_datequotes($value);
	}
	else
	{
		$strvalue = (string)$value;
		$strvalue = str_replace(",",".",$strvalue);
		if(is_numeric($strvalue))
			$value=$strvalue;
		else
			$value=0;
	}
	return $value;
}

function prepare_for_db($field,$value,$controltype="",$postfilename="",$table="")
{
	global $strTableName;
	$filename="";
	$type=GetFieldType($field,$table);
	if(!$controltype || $controltype=="multiselect")
	{
		if(is_array($value))
			$value=combinevalues($value);
		if(($value==="" || $value===FALSE) && !IsCharType($type))
			return "";
		return $value;
	}	
	else if($controltype=="time")
	{
		if(!strlen($value))
			return "";
		$time=localtime2db($value);
		if(IsDateFieldType(GetFieldType($field,$table)))
		{
			$time="2000-01-01 ".$time;
		}
		return $time;
	}
	else if(substr($controltype,0,4)=="date")
	{
		$dformat=substr($controltype,4);
		if($dformat==EDIT_DATE_SIMPLE || $dformat==EDIT_DATE_SIMPLE_DP)
		{
			$time=localdatetime2db($value);
			if($time=="null")
				return "";
			return $time;
		}
		else if($dformat==EDIT_DATE_DD || $dformat==EDIT_DATE_DD_DP)
		{
			$a=explode("-",$value);
			if(count($a)<3)
				return "";
			else
			{
				$y=$a[0];
				$m=$a[1];
				$d=$a[2];
			}
			if($y<100)
			{
				if($y<70)
					$y+=2000;
				else
					$y+=1900;
			}
			return mysprintf("%04d-%02d-%02d",array($y,$m,$d));
		}
		else
			return "";
	}
	else if(substr($controltype,0,8)=="checkbox")
	{
		if($value=="on")
			$ret=1;
		else if($value=="none")
			return "";
		else 
			$ret=0;
		return $ret;
	}
	else
		return false;
}

//	delete uploaded files when deleting the record
function DeleteUploadedFiles($where,$table="")
{
	global $conn,$gstrSQL;
	$sql = gSQLWhere($where);
	$rs = db_query($sql,$conn);
	if(!($data=db_fetch_array($rs)))
		return;
	foreach($data as $field=>$value)
	{
		if(strlen($value) && GetEditFormat($field)==EDIT_FORMAT_FILE)
		{
			if(myfile_exists(GetUploadFolder($field).$value))
				myunlink(GetUploadFolder($field).$value);
			if(GetCreateThumbnail($field) && myfile_exists(GetUploadFolder($field).GetThumbnailPrefix($field).$value))
				myunlink(GetUploadFolder($field).GetThumbnailPrefix($field).$value);
		}
	}
}

//	combine checked values from multi-select list box
function combinevalues($arr)
{
	$ret="";
	foreach($arr as $val)
	{
		if(strlen($ret))
			$ret.=",";
		if(strpos($val,",")===false && strpos($val,'"')===false)
			$ret.=$val;
		else
		{
			$val=str_replace('"','""',$val);
			$ret.='"'.$val.'"';
		}
	}
	return $ret;
}

//	split values for multi-select list box
function splitvalues($str)
{
	$arr=array();
	$start=0;
	$i=0;
	$inquot=false;
	while($i<=strlen($str))
	{
		if($i<strlen($str) && substr($str,$i,1)=='"')
			$inquot=!$inquot;
		else if($i==strlen($str) || !$inquot && substr($str,$i,1)==',')
		{
			$val=substr($str,$start,$i-$start);
			$start=$i+1;
			if(strlen($val) && substr($val,0,1)=='"')
			{
				$val=substr($val,1,strlen($val)-2);
				$val=str_replace('""','"',$val);
			}
			$arr[]=$val;
		}
		$i++;
	}
	return $arr;
}


////////////////////////////////////////////////////////////////////////////////
// edit controls creation functions
////////////////////////////////////////////////////////////////////////////////

//	returns HTML code that represents required Date edit control
function GetDateEdit($field, $value, $type, $fieldNum=0,$search=MODE_EDIT,$record_id="",$jsControlObjectParams, &$pageObj)
{	
	global $cYearRadius, $locale_info, $jscode, $strTableName;
	$is508=isEnableSection508();
	$strLabel=Label($field);
	$cfieldname=GoodFieldName($field);
	$cfield="value_".GoodFieldName($field).'_'.$record_id;
	if($fieldNum)
		$cfield="value".$fieldNum."_".GoodFieldName($field).'_'.$record_id;
	$tvalue=$value;
	
	
	$time=db2time($tvalue);
	if(!count($time))
		$time=array(0,0,0,0,0,0);
	$dp=0;
	switch($type)
	{
		case EDIT_DATE_SIMPLE_DP:
			$ovalue=$value;
			if($locale_info["LOCALE_IDATE"]==1)
			{
				$fmt="dd".$locale_info["LOCALE_SDATE"]."MM".$locale_info["LOCALE_SDATE"]."yyyy";
				$sundayfirst="false";
			}
			else if($locale_info["LOCALE_IDATE"]==0)
			{
				$fmt="MM".$locale_info["LOCALE_SDATE"]."dd".$locale_info["LOCALE_SDATE"]."yyyy";
				$sundayfirst="true";
			}
			else
			{
				$fmt="yyyy".$locale_info["LOCALE_SDATE"]."MM".$locale_info["LOCALE_SDATE"]."dd";
				$sundayfirst="false";
			}
			
			if($time[5])
				$fmt.=" HH:mm:ss";
			else if($time[3] || $time[4])
				$fmt.=" HH:mm";
			
			if($time[0])
				$ovalue=format_datetime_custom($time,$fmt);
			$ovalue1=$time[2]."-".$time[1]."-".$time[0];
			$showtime="false";
			if(DateEditShowTime($field))
			{
				$showtime="true";
				$ovalue1.=" ".$time[3].":".$time[4].":".$time[5];
			}
			// need to create date control object to use it with datePicker
			$ret='<input id="'.$cfield.'" type="Text" name="'.$cfield.'" size="20" value="'.$ovalue.'">';
			$ret.='<input id="ts'.$cfield.'" type="Hidden" name="ts'.$cfield.'" value="'.$ovalue1.'">&nbsp;&nbsp;';
			$ret.='&nbsp;<a href="#" id="imgCal_'.$cfield.'" onclick="var cntrl = window.Runner.controls.ControlManager.getAt(\''.htmlspecialchars(jsreplace($strTableName)).'\', \''.$record_id.'\', \''.htmlspecialchars(jsreplace($field)).'\', '.$fieldNum.'); var v=show_calendar(cntrl, \'\',\'\', $(\'input[@name=ts'.$cfield.']\').get(0).value,'.$showtime.','.$sundayfirst.'); return false;">'.
				'<img src="images/cal.gif" width=16 height=16 border=0 alt="'."Click Here to Pick up the date".'"></a>';			
			echo $ret;
			$str = substr($jsControlObjectParams, 0, -1);
			$jsControlObjectParams = $str.', useDatePicker: true, dateFormat: '.$locale_info["LOCALE_IDATE"].' '.($showtime ? ', showTime: true' : '').'}';	
			jscodeToAdd("DateTextField",$jsControlObjectParams,$record_id, $pageObj);
			return;
		case EDIT_DATE_DD_DP:
			$dp=1;
		case EDIT_DATE_DD:
			$retday='<select id="day'.$cfield.'" class=selects '.(($search == MODE_INLINE_EDIT || $search==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="day'.$cfield.'" ></select>';
			$retmonth='<select id="month'.$cfield.'" class=selects '.(($search == MODE_INLINE_EDIT || $search==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="month'.$cfield.'" ></select>';
			$retyear='<select id="year'.$cfield.'" class=selects '.(($search == MODE_INLINE_EDIT || $search==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="year'.$cfield.'" ></select>';
			$sundayfirst="false";
			if($locale_info["LOCALE_ILONGDATE"]==1)
				$ret=$retday."&nbsp;".$retmonth."&nbsp;".$retyear;
			else if($locale_info["LOCALE_ILONGDATE"]==0)
			{
				$ret=$retmonth."&nbsp;".$retday."&nbsp;".$retyear;
				$sundayfirst="true";
			}
			else
				$ret=$retyear."&nbsp;".$retmonth."&nbsp;".$retday;
				
			if($time[0] && $time[1] && $time[2])
				$ret.="<input id=\"".$cfield."\" type=hidden name=\"".$cfield."\" value=\"".$time[0]."-".$time[1]."-".$time[2]."\">";
			else
				$ret.="<input id=\"".$cfield."\" type=hidden name=\"".$cfield."\" value=\"\">";
			
			$str = substr($jsControlObjectParams, 0, -1);
			// calendar handling for three DD
			if($dp)
			{
				$ret.='&nbsp;<a href="#" id="imgCal_'.$cfield.'" onclick="var cntrl = window.Runner.controls.ControlManager.getAt(\''.htmlspecialchars(jsreplace($strTableName)).'\', \''.$record_id.'\', \''.htmlspecialchars(jsreplace($field)).'\', '.$fieldNum.'); var v=show_calendar(cntrl, \'\',\'\', $(\'input[@name=ts'.$cfield.']\').get(0).value,false,'.$sundayfirst.'); return false;">'.
				'<img src="images/cal.gif" width=16 height=16 border=0 alt="Click Here to Pick up the date"></a>'.
				'<input id="ts'.$cfield.'" type=hidden name="ts'.$cfield.'" value="'.$time[2].'-'.$time[1].'-'.$time[0].'">';
				
				$str = $str.', useDatePicker: true';			
			}
			$jsControlObjectParams = $str.', dateFormat: '.$locale_info["LOCALE_IDATE"].', yearVal: '.$time[0].', monthVal: '.$time[1].', dayVal: '.$time[2].'}';
			
			$pageid=$record_id;
			if(isset($pageObj->id))
			{
				$pageid=$pageObj->id;
			}
			$code = "var Cntrl = Runner.controls.ControlManager.getAt('".jsreplace($strTableName)."',".$record_id.",'".jsreplace($field)."', ".$fieldNum.");
					if(Cntrl)
					{
						Cntrl.addYearOptions(".$time[0].");
						Cntrl.addMonthOptions(".$time[1].");
						Cntrl.addDayOptions(".$time[2].");
						Cntrl.defaultValue = Cntrl.getValue();
					}
			";
			AddScript2Postload($code, $record_id);
			echo $ret;
			jscodeToAdd("DateDropDown",$jsControlObjectParams,$record_id, $pageObj);			
			return;
	//	case EDIT_DATE_SIMPLE:
		default:
			$ovalue=$value;
			if($time[0])
			{
				if($time[3] || $time[4] || $time[5])
					$ovalue=str_format_datetime($time);
				else
					$ovalue=format_shortdate($time);
			}
			echo '<input id="'.$cfield.'" type=text name="'.$cfield.'" size="20" value="'.htmlspecialchars($ovalue).'">';
			$str = substr($jsControlObjectParams, 0, -1);
			$jsControlObjectParams = $str.', dateFormat: '.$locale_info["LOCALE_IDATE"].'}';
			jscodeToAdd("DateTextField",$jsControlObjectParams,$record_id, $pageObj);
			return;;
	}
}

//	create javascript array with values for dependent dropdowns
function BuildSecondDropdownArray( $arrName, $strSQL)
{
	global $conn;

	echo $arrName . "=new Array();\r\n";
	$i=0;
	$rs = db_query($strSQL,$conn);
	while($row=db_fetch_numarray($rs))
	{
		echo $arrName."[".($i*3)."]='".jsreplace($row[0]). "';\r\n";
		echo $arrName."[".($i*3 + 1)."]='".jsreplace($row[1]). "';\r\n";
		echo $arrName."[".($i*3 + 2)."]='".jsreplace($row[2]). "';\r\n";
		$i++;
	}
}

//	create Lookup wizard control
function BuildSelectControl($field, $value, $fieldNum=0, $mode, $id="", $jsControlObjectParams, $additionalCtrlParams, &$pageObj)
{
	global $conn,$strTableName;
	
//	read control settings
	$table=$strTableName;
	$strLabel=Label($field);
	$is508=isEnableSection508();
	$alt="";
	if(($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $is508)
		$alt=' alt="'.htmlspecialchars($strLabel).'" ';
	$cfield="value_".GoodFieldName($field)."_".$id;
	$clookupfield="display_value_".GoodFieldName($field)."_".$id;
	$openlookup = "open_lookup_".GoodFieldName($field)."_".$id;
	$ctype="type_".GoodFieldName($field)."_".$id;
	if($fieldNum)
	{
		$cfield="value".$fieldNum."_".GoodFieldName($field)."_".$id;
		$ctype="type".$fieldNum."_".GoodFieldName($field)."_".$id;
	}
	$addnewitem=false;
	$advancedadd=false;
	$strCategoryControl=CategoryControl($field,$table);
	$categoryFieldId = GoodFieldName(CategoryControl($field, $table));
	$bUseCategory = UseCategory($field,$table);
	$dependentLookups = GetFieldData($table,$field,"DependentLookups",array());
	
	
	$lookupType=GetLookupType($field,$table);
	$LCType=LookupControlType($field,$table);
	$horizontalLookup = GetFieldData($table,$field,"HorizontalLookup",false);
	$inputStyle = ($additionalCtrlParams['style'] ? 'style="'.$additionalCtrlParams['style'].'"' : '');
	$lookupTable=GetLookupTable($field,$table);
	$strLookupWhere = LookupWhere($field,$table);

	$lookupSize=SelectSize($field,$table);
	if($LCType==LCT_CBLIST)
		$lookupSize = 2; // simply > 1 for CBLIST
	
	$add_page = GetTableURL($lookupTable)."_add.php";
	$list_page=GetTableURL($lookupTable)."_list.php";

	$strPerm = GetUserPermissions($lookupTable);
//	alter "add on the fly" settings	
	if(strpos($strPerm,"A")!==false)
	{
		$addnewitem = GetFieldData($table,$field,"AllowToAdd",false);
		$advancedadd = !GetFieldData($table,$field,"SimpleAdd",false);
		if(!$advancedadd)
			$addnewitem=false;
	}
//	alter lookuptype settings
	if($LCType==LCT_LIST && strpos($strPerm,"S")===false)
	{
		$LCType=LCT_DROPDOWN;
	}
	if($LCType==LCT_LIST)
		$addnewitem=false;
	if($mode==MODE_SEARCH)
		$addnewitem=false;
//	prepare multi-select attributes
	$multiple="";
	$postfix="";
	if($lookupSize>1)
	{
		$avalue=splitvalues($value);
		$multiple=" multiple";
		$postfix="[]";
	}
	else 
		$avalue=array((string)$value);
		
//	prepare JS code

	$className="DropDownLookup";
	if($LCType==LCT_AJAX)
		$className="EditBoxLookup";
	elseif($LCType==LCT_LIST)
		$className="ListPageLookup";
	elseif($LCType==LCT_CBLIST)
		$className="CheckBoxLookup";

			
	if(UseCategory($field))
	{
		$str = substr($jsControlObjectParams, 0, -1);
		$jsControlObjectParams = $str.", parentFieldName: '".GoodFieldName(CategoryControl($field))."'}";
	}
	if($lookupSize>1)
	{
		$str = substr($jsControlObjectParams, 0, -1);
		$jsControlObjectParams = $str.", multiSel: ".$lookupSize."}";
	}
	
	jscodeToAdd($className,$jsControlObjectParams,$id, $pageObj);

//	process dependent and parent dropdowns 
	
	$code="";
	if(!@$additionalCtrlParams['skipDependencies'] && ($bUseCategory || count($dependentLookups)))
	{
		$code = "var Cntrl = Runner.controls.ControlManager.getAt('".jsreplace($table)."',".$id.",'".jsreplace($field)."'); ";
//	process dependent
		$depCode="";
		foreach($dependentLookups as $dl)
		{
			if(!strlen($depCode))
				$depCode="Cntrl.addDependentCtrls([Runner.controls.ControlManager.getAt('".jsreplace($table)."',".$id.",'".jsreplace($dl)."')";
			else
				$depCode.=",Runner.controls.ControlManager.getAt('".jsreplace($table)."',".$id.",'".jsreplace($dl)."')";
		}
		if(strlen($depCode))
			$depCode.="]);";
		$code.=$depCode;
//	process parent control
		if($bUseCategory)
		{
			$code .= "Cntrl.setParentCtrl(Runner.controls.ControlManager.getAt('".jsreplace($table)."',".$id.",'".jsreplace($strCategoryControl)."')); ";
		}
	}
//	add the code to the page
	if (strlen($code))
	{
		$pageid=$id;
		if(isset($pageObj->id))
			$pageid=$pageObj->id;
		AddScript2Postload($code, $pageid);	
	}

//	build the control

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	list of values
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($lookupType==LT_LISTOFVALUES)
	{
//	read lookup values
		$arr=GetFieldData($table,$field,"LookupValues",array());
//	print Type control to allow selecting nothing
		if($lookupSize>1)
			echo "<input id=\"".$ctype."\" type=hidden name=\"".$ctype."\" value=\"multiselect\">";
//	dropdown control
		if($LCType==LCT_DROPDOWN)
		{
			$alt="";
			echo '<select id="'.$cfield.'" size = "'.$lookupSize.'" '.$alt.'name="'.$cfield.$postfix.'" '.$multiple.'>';
			if($lookupSize<2 )
				echo '<option value="">'."Please select".'</option>';
			else if($mode==MODE_SEARCH)
				echo '<option value=""> </option>';
				
			foreach($arr as $opt)
			{
				$res=array_search((string)$opt,$avalue);
				if(!($res===NULL || $res===FALSE))
		      			echo '<option value="'.htmlspecialchars($opt).'" selected>'.htmlspecialchars($opt).'</option>';
				else
		      			echo '<option value="'.htmlspecialchars($opt).'">'.htmlspecialchars($opt).'</option>';
			}
			echo "</select>";
		}
		elseif($LCType==LCT_CBLIST)
		{
			echo '<div align=\'left\'>';
			$spacer = '<br/>';
			if($horizontalLookup)
				$spacer = '&nbsp;';
			$i=0;
			foreach($arr as $opt)
			{
				echo '<input id="'.$cfield.'_'.$i.'" type="checkbox" '.$alt.' name="'.$cfield.$postfix.'" value="'.htmlspecialchars($opt).'"';
				$res=array_search((string)$opt,$avalue);
				if(!($res===NULL || $res===FALSE))
					echo ' checked="checked" ';
				echo '/>';
				echo '&nbsp;<b id="data_'.$cfield.'_'.$i.'">'.htmlspecialchars($opt).'</b>'.$spacer;
				$i++;
			}
			echo '</div>';
		}
		return;
	}

// build table-based lookup

////////////////////////////////////////////////////////////////////////////////////////////
//	table-based ajax-lookup control
////////////////////////////////////////////////////////////////////////////////////////////
	if($LCType==LCT_AJAX || $LCType==LCT_LIST)
	{
////////////////////////////////////////////////////////////////////////////////////////////
//	dependent ajax-lookup control
////////////////////////////////////////////////////////////////////////////////////////////
		if(UseCategory($field))
		{
// ajax	dependent dropdown
			// get parent value
			$celementvalue = "var parVal = ''; var parCtrl = Runner.controls.ControlManager.getAt('".jsreplace($strTableName)."', ".$id.", '".jsreplace($field)."', 0).parentCtrl; if (parCtrl){ parVal = parCtrl.getStringValue();};";
			if($LCType==LCT_AJAX)
			{
				echo '<input type="text" categoryId="'.$categoryFieldId.'" autocomplete="off" id="'.$clookupfield.'" name="'.$clookupfield.'" '.$inputStyle.'>';
			}
			elseif($LCType==LCT_LIST)
			{	
				echo '<input type="text" categoryId="'.$categoryFieldId.'" autocomplete="off" id="'.$clookupfield.'" name="'.$clookupfield.'"  readonly '.$inputStyle.'>';				
				echo "&nbsp;<a href=# id=".$openlookup." onclick=\"".$celementvalue." return Runner.controls.ControlManager.getAt('".jsreplace($strTableName)."', ".$id.", '".jsreplace($field)."').showPage(event, '".$list_page."', '".$cfield."', parVal);\">"."Select"."</a>";				
			}
			echo '<input type="hidden" id="'.$cfield.'" name="'.$cfield.'">';
//	add new item link
			if($addnewitem)
			{
				echo "&nbsp;<a href=# id='addnew_".$cfield."' onclick=\"".$celementvalue." return DisplayPage(event,'".$add_page."',".$id.",'".$cfield."','".jsreplace($field)."','".jsreplace($strTableName)."', parVal);\">"."Add new"."</a>";
			}
			return;
		}
////////////////////////////////////////////////////////////////////////////////////////////
//	regular ajax-lookup control
////////////////////////////////////////////////////////////////////////////////////////////

//	get the initial value
		$lookup_value = "";
		$lookupSQL = buildLookupSQL($field,$table,"",$value,false,true,false,true);
		$rs_lookup=db_query($lookupSQL,$conn);	
			
		if ( $data = db_fetch_numarray($rs_lookup) ) 
			$lookup_value = $data[1];
		elseif(strlen($strLookupWhere))
		{
		// try w/o WHERE expression
			$lookupSQL = buildLookupSQL($field,$table,"",$value,false,true,false,true);
			$rs_lookup=db_query($lookupSQL,$conn);			
			if($data = db_fetch_numarray($rs_lookup))
				$lookup_value = $data[1];
		}
//	build the control
		if($LCType==LCT_AJAX)
		{
			echo '<input type="text" '.$inputStyle.' autocomplete="off" '.(($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'id="'.$clookupfield.'" name="'.$clookupfield.'" value="'.htmlspecialchars($lookup_value).'">';
		}
		elseif($LCType==LCT_LIST)
		{
			echo '<input type="text" autocomplete="off" '.$inputStyle.' id="'.$clookupfield.'" '.(($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$clookupfield.'" value="'.htmlspecialchars($lookup_value).'" 	readonly >';			
			echo "&nbsp;<a href=# id=".$openlookup." onclick=\"return Runner.controls.ControlManager.getAt('".jsreplace($strTableName)."', ".$id.", '".jsreplace($field)."').showPage(event, '".$list_page."', '".$cfield."', '');\">"."Select"."</a>";			
		}
		echo '<input type="hidden" id="'.$cfield.'" name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
//	add new item
		if($addnewitem)
		{
			echo "&nbsp;<a href=# id='addnew_".$cfield."' onclick=\"return DisplayPage(event,'".$add_page."',".$id.",'".$cfield."','".jsreplace($field)."','".jsreplace($strTableName)."','');\">"."Add new"."</a>";
		}
		return;
	}
////////////////////////////////////////////////////////////////////////////////////////////
//	classic lookup - start
////////////////////////////////////////////////////////////////////////////////////////////
	
	$lookupSQL = buildLookupSQL($field,$table,"","",false,false,false);
	$rs=db_query($lookupSQL,$conn);

////////////////////////////////////////////////////////////////////////////////////////////
//	dependent classic lookup
////////////////////////////////////////////////////////////////////////////////////////////
	if($bUseCategory)
	{
		//	print Type control to allow selecting nothing
		if($lookupSize>1)
			echo "<input id=\"".$ctype."\" type=hidden name=\"".$ctype."\" value=\"multiselect\">";
		echo '<select size = "'.$lookupSize.'" id="'.$cfield.'" name="'.$cfield.$postfix.'"'.$multiple.'>';
		echo '<option value="">'."Please select".'</option>';
		echo "</select>";
		if($addnewitem)
		{
			$celement="document.getElementById('value_".GoodFieldName($strCategoryControl)."_".$id."')";
			$celementvalue = '$('.$celement.").val()";
			echo "&nbsp;<a href=# id='addnew_".$cfield."' onclick=\"return DisplayPage(event,'".$add_page."',".$id.",'".htmlspecialchars(jsreplace($cfield))."','".jsreplace($field)."','".jsreplace($strTableName)."',".$celementvalue.");\">"."Add new"."</a>";
		}
		return;
	}
////////////////////////////////////////////////////////////////////////////////////////////
//	simple classic lookup
////////////////////////////////////////////////////////////////////////////////////////////
//	print control header
	if($lookupSize>1)
		echo "<input id=\"".$ctype."\" type=hidden name=\"".$ctype."\" value=\"multiselect\">";
	if($LCType!=LCT_CBLIST)
	{
		echo '<select size = "'.$lookupSize.'" id="'.$cfield.'" '.(($mode == MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.$postfix.'"'.$multiple.'>';
		if($lookupSize<2)
			echo '<option value="">'."Please select".'</option>';
		else if($mode==MODE_SEARCH)
			echo '<option value=""> </option>';
	}
	else
	{
		echo '<div align=\'left\'>';
		$spacer = '<br/>';
		if($horizontalLookup)
			$spacer = '&nbsp;';
	}
//	print lookup data
   	$found=false;
	$i = 0;
	while($data=db_fetch_numarray($rs))
	{
		$res=array_search((string)$data[0],$avalue);
		$checked="";
		if(!($res===NULL || $res===FALSE))
		{
			$found=true;
			if($LCType==LCT_CBLIST)
				$checked=" checked=\"checked\"";
			else
				$checked=" selected";
		}
		if($LCType==LCT_CBLIST)
		{
			echo '<input id="'.$cfield.'_'.$i.'" type="checkbox" '.$alt.' name="'.$cfield.$postfix.'" value="'.htmlspecialchars($data[0]).'"'.$checked.'/>';
			echo '&nbsp;<b id="data_'.$cfield.'_'.$i.'">'.htmlspecialchars($data[1]).'</b>'.$spacer;
		}
		else
			echo '<option value="'.htmlspecialchars($data[0]).'"'.$checked.'>'.htmlspecialchars($data[1]).'</option>';
		$i++;
	}

//	try the same query w/o WHERE clause if current value not found
	if(!$found && strlen($value) && $mode==MODE_EDIT && strlen($strLookupWhere))
	{
		$lookupSQL = buildLookupSQL($field,$table,"",$value,false,true,false,false,true);
		$rs=db_query($lookupSQL,$conn);
		if($data=db_fetch_numarray($rs))
		{
			if($LCType==LCT_CBLIST)
			{
				echo '<input id="'.$cfield.'_'.$i.'" type="checkbox" '.$alt.' name="'.$cfield.$postfix.'" value="'.htmlspecialchars($data[0]).'" checked="checked"/>';
				echo '&nbsp;<b id="data_'.$cfield.'_'.$i.'">'.htmlspecialchars($data[1]).'</b>'.$spacer;
			}
			else
				echo '<option value="'.htmlspecialchars($data[0]).'" selected>'.htmlspecialchars($data[1]).'</option>';
		}
	}
//	print footer
	if($LCType!=LCT_CBLIST)
	{
		echo "</select>";
	}
	else
		echo '</div>';
//	add new item
	if($addnewitem)
		echo "&nbsp;<a href=# id='addnew_".$cfield."' onclick=\"return DisplayPage(event,'".$add_page."',".$id.",'".$cfield."','".htmlspecialchars(jsreplace($field))."','".htmlspecialchars(jsreplace($strTableName))."','');\">"."Add new"."</a>";

}

function BuildRadioControl($field, $value,$fieldNum=0,$id="", $mode)
{
	global $conn,$LookupSQL,$strTableName;
	$is508=isEnableSection508();
	$strLabel=Label($field);
	$cfieldname=GoodFieldName($field)."_".$id;
	$cfield="value_".GoodFieldName($field)."_".$id;
	//$cfieldid="value_".GoodFieldName($field);
	$ctype="type_".GoodFieldName($field)."_".$id;
	
	if($fieldNum)
	{
		$cfield="value".$fieldNum."_".GoodFieldName($field)."_".$id;
		$ctype="type".$fieldNum."_".GoodFieldName($field)."_".$id;
	}
	$LookupSQL ="";
	$spacer = '<br/>';

	if($LookupSQL)
	{
	    LogInfo($LookupSQL);
		$rs=db_query($LookupSQL,$conn);
		echo '<input id="'.$cfield.'" type=hidden name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
		$i=0;
	    while($data=db_fetch_numarray($rs))
		{
			$checked="";
			if($data[0]==$value)
				$checked=" checked";
			echo "<input type=\"Radio\" id=\"radio_".$cfieldname."_".$i."\" ".(($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $is508==true ? "alt=\"".$strLabel."\" " : "")."name=\"radio_".$cfieldname."\" ".$checked." value=\"".htmlspecialchars($data[0])."\">".htmlspecialchars($data[1]).$spacer;
			$i++;
		}
	}
	else
	{
		echo '<input id="'.$cfield.'" type=hidden name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
		$i=0;
		foreach($arr as $opt)
		{
			$checked="";
			if($opt==$value)
				$checked=" checked";
			echo "<input  type=\"Radio\" id=\"radio_".$cfieldname."_".$i."\" ".(($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $is508==true ? "alt=\"".$strLabel."\" " : "")."name=\"radio_".$cfieldname."\" ".$checked." value=\"".htmlspecialchars($opt)."\">".htmlspecialchars($opt).$spacer;
			$i++;
		}
	}
	return;

}
//For add jscode to control
function jscodeToAdd($ControlObjectName,$jsControlObjectParams,$id, &$pageObj)
{
	global $strTableName;
	
	if ($ControlObjectName)	
	{				
		$strCodeToAdd = "var new".$ControlObjectName.$id." = new Runner.controls.".$ControlObjectName."(".$jsControlObjectParams."); ";
		if(isset($pageObj->id))
			$id=$pageObj->id;
		AddScriptBe4Postload($strCodeToAdd, $id);
	}	
}		
//for js control object params
function ControlObjectParams($validate,$field,$id,$fieldNum=0,$additionalCtrlParams)
{
	global $strTableName;
	$jsControlObjectParams = "{
		fieldName: '".jsreplace($field)."', 
		goodFieldName: '".GoodFieldName($field)."',
		shortTableName: '".jsreplace(GetTableData($strTableName, ".shortTableName", ''))."',
		id: '".$id."',
		ctrlInd: ".$fieldNum.",";
	
	// custom user validation regExp
	if (isset($validate['RegExp']))
	{
		$jsControlObjectParams .= "
			regExp:{
				regex: '".$validate["RegExp"][0]."',
				message: '".$validate["RegExp"][1]."',
				messagetype: '".$validate["RegExp"][2]."'
			},";
	}
	// predefined validations and custom user functions
	if (isset($validate['basicValidate']))
	{
		// make js validation arr for constructor cfg
		$jsControlObjectParams .= "validationArr: [";
		if(count($validate['basicValidate']))
		{
			for($i=0;$i<count($validate['basicValidate']);$i++)
				$jsControlObjectParams .= "'".$validate['basicValidate'][$i]."',";		
			$jsControlObjectParams = substr($jsControlObjectParams, 0, -1);
		}
		$jsControlObjectParams .= "],";
	}
	
	// add additional params to object cfg as string
	foreach($additionalCtrlParams as $paramName=>$paramVal)
	{
		if (is_string($paramVal))
			$jsControlObjectParams .= $paramName.": '".$paramVal."',";
		else if(is_bool($paramVal))
			$jsControlObjectParams .= $paramName.": ".($paramVal ? 'true' : 'false').",";
		else
			$jsControlObjectParams .= $paramName.": ".$paramVal.",";
	}
	$jsControlObjectParams .= "table: '".jsreplace($strTableName)."'}";
	return $jsControlObjectParams;
}
 
function BuildEditControl($field , $value, $format, $edit, $fieldNum=0, $id="",$validate, $additionalCtrlParams, &$pageObj)
{
	global $rs,$data,$strTableName,$filenamelist,$keys,$locale_info,$jscode;
	
	$additionalCtrlParams['mode'] = $edit;
	$jsControlObjectParams = ControlObjectParams($validate,$field,$id,$fieldNum,$additionalCtrlParams);
	
	$inputStyle = 'style="';
	$inputStyle .= ($additionalCtrlParams['style'] ? $additionalCtrlParams['style'] : '');
	//$inputStyle .= ($additionalCtrlParams['hidden'] ? 'display: none;' : '');
	$inputStyle .= '"';
	
	// for files, if we need to use time stamp
	if (UseTimestamp($field))
	{
		$str = substr($jsControlObjectParams, 0, -1);
		$jsControlObjectParams = $str.", addTimeStamp: 'true'}";
	}
	
	$cfieldname=GoodFieldName($field)."_".$id;
	$cfield="value_".GoodFieldName($field)."_".$id;
	$ctype="type_".GoodFieldName($field)."_".$id;
	$is508=isEnableSection508();

	$strLabel=Label($field);
	if($fieldNum)

	{
		$cfield="value".$fieldNum."_".GoodFieldName($field)."_".$id;
		$ctype="type".$fieldNum."_".GoodFieldName($field)."_".$id;
	}
	$type=GetFieldType($field);
	$arr="";
	$iquery="field=".rawurlencode($field);
	$keylink="";

	$arrKeys = GetTableKeys($strTableName);
	for ( $j=0; $j < count($arrKeys); $j++ ) 
	{
		$keylink.="&key" . ($j+1) . "=".rawurlencode($data[$arrKeys[$j]]);
	}
	$iquery.=$keylink;

	$isHidden = (isset($additionalCtrlParams['hidden']) && $additionalCtrlParams['hidden']);
	echo '<span id="edit'.$id.'_'.GoodFieldName($field).'_'.$fieldNum.'" style="'.($isHidden ? 'display:none;' : '').(($format==EDIT_FORMAT_DATE || $format==EDIT_FORMAT_TEXT_FIELD) && $edit != MODE_SEARCH ? 'white-space: nowrap;' : '').'">';
	if($format==EDIT_FORMAT_FILE && $edit==MODE_SEARCH)
		$format="";
	if($format==EDIT_FORMAT_TEXT_FIELD)
	{
		if(IsDateFieldType($type))
		{
			echo '<input id="'.$ctype.'" type="hidden" name="'.$ctype.'" value="date'.EDIT_DATE_SIMPLE.'">'.GetDateEdit($field,$value,0,$fieldNum,$edit,$id,$jsControlObjectParams, $pageObj);
			$str = substr($jsControlObjectParams, 0, -1);
			$jsControlObjectParams = $str.", ctrlType: 'date".EDIT_DATE_SIMPLE."'}";			
		}
		else
	    {
			if($edit==MODE_SEARCH)
				echo '<input id="'.$cfield.'" '.$inputStyle.' type="text" autocomplete="off" '. (($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '') . 'name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($value).'">';				
			else
				echo '<input id="'.$cfield.'" '.$inputStyle.' type="text" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($value).'">';
			
		}
		jscodeToAdd("TextField",$jsControlObjectParams,$id, $pageObj);
	}
	else if($format==EDIT_FORMAT_TIME)
	{
		echo '<input id="'.$ctype.'" '.$inputStyle.' type="hidden" name="'.$ctype.'" value="time">';
		$str = substr($jsControlObjectParams, 0, -1);
		$jsControlObjectParams = $str.", ctrlType: 'time'}";
		jscodeToAdd("TimeField",$jsControlObjectParams,$id, $pageObj);
		
		$timeAttrs = GetFieldData($strTableName,$field,"FormatTimeAttrs",array());	
		if(count($timeAttrs))
		{
			if($timeAttrs["useTimePicker"])
			{
				$convention = $timeAttrs["hours"];
				$locale_convention = $locale_info["LOCALE_ITIME"] ? 24 : 12;
				
				if($convention == $locale_convention)
				{
						$am = $locale_info["LOCALE_S1159"];
						$pm = $locale_info["LOCALE_S2359"];
						$locale = $locale_info["LOCALE_STIMEFORMAT"];
				}
				else
				{
						if($convention == 24)
						{
								$am = '';
								$pm = '';
								$locale = "H:mm:ss";
						}
						else
						{
								$am = 'am';
								$pm = 'pm';
								$locale = "h:mm:ss tt";
						}
				}
			}
			else
				$locale = $locale_info["LOCALE_STIMEFORMAT"];
		
			$val = "";
			$dbtime = array();
			if(IsDateFieldType($type))
			{
				$dbtime = db2time($value);
				if(count($dbtime))
					$val = format_datetime_custom($dbtime, $locale);
			}
			else 
			{
				$arr = parsenumbers($value);
				if(count($arr))
				{
					while(count($arr)<3)
						$arr[] = 0;
					$dbtime = array(0, 0, 0, $arr[0], $arr[1], $arr[2]);
					$val = format_datetime_custom($dbtime, $locale);
				}
			}
						
			if($timeAttrs["useTimePicker"])
			{
				$ret = '$(function(){';
				$ret.= '$(\'#'.$cfield.'\').timepickr({';
				$ret.= 'handle: \'#trigger-test-'.$cfield.'\',';
				$ret.= 'convention: ' . $convention . ',';
				$ret.= 'seconds: '.$timeAttrs["showSeconds"].',';
				$ret.= "tName: '".jsreplace($strTableName)."',";
				$ret.= "rowId: ".$id.",";
				$ret.= "fName: '".GoodFieldName($field)."',";				
				$range = array();
				if($convention==24)
				{
					for($h = 0; $h < $convention; $h ++)
							$range []= '\''.$h.'\'';
				}
				else
				{
					for($h = 1; $h <= $convention; $h ++)
							$range []= '\''.$h.'\'';
				}
				$ret.= 'range'.$convention.'h: ['.join(',', $range).'],';
				$minutes = array();
				for($m = 0; $m < 60; $m += $timeAttrs["minutes"])
						$minutes []= '\''.$m.'\'';
				$ret.= 'rangeMin: ['.join(',', $minutes).'],';
				$ret.= 'apm: [\''.$am.'\',\''.$pm.'\'],';
				if(count($dbtime) > 0)
					$ret.= 'hover: ['.$dbtime[3].','.$dbtime[4].','.$dbtime[5].'],';
				$ret.= 'open: '.($val ? 'true' : 'false').',';
				$ret.= 'select: function(e, dropslide){';
				$ret.= 'if(!dropslide) return;';
				$ret.= 'var t = dropslide.getSelection();';				
				$ret.= 'var newval = print_time(t[0], t[1], ';				
				
				if($timeAttrs["showSeconds"])
					$ret.='t[2]';
				else
					$ret.='0';
				
				if($timeAttrs["showSeconds"])
					$tvar1='t[3]';
				else
					$tvar1='t[2]';
				
				$ret.=', ' .
				'\''.$locale . '\', ' .
				($convention == '12' ? '"' . $am . '"==' . $tvar1 . '?true:false'	: 'false') .
				',' . $convention . ',' .
				'\'' . $am . '\',' .
				'\'' . $pm . '\'' .
				');';
				$ret.= "var ctrl = Runner.controls.ControlManager.getAt('".jsreplace($strTableName)."', ".$id." ,'".jsreplace($field)."');";				
				$ret .= "ctrl.setValue(newval,true);";
				$ret.= 'e.stopPropagation();';
				$ret.= '},';
				$ret.= "dropslide: {hideoutDelay: 2000, trigger: 'click'}";
				$ret.= '});';
				$ret.= '});';
				
				$pageid=$id;
				if(isset($pageObj->id))
					$pageid=$pageObj->id;
				AddScript2Postload($ret, $pageid);
				echo '<input type="text" '.$inputStyle.' name="'.$cfield.'" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'id="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($val).'">';
				echo '&nbsp;';
				echo '<img src="include/img/clock.gif" alt="Time" border="0" style="margin:4px 0 0 6px; visibility: hidden;" id="trigger-test-'.$cfield.'" />';
			}	
			else
				echo '<input id="'.$cfield.'" '.$inputStyle.' type="text" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($val).'">';
		}
	}
	else if($format==EDIT_FORMAT_TEXT_AREA)
	{
		$nWidth = GetNCols($field);
		$nHeight = GetNRows($field);
		if(UseRTE($field))
		{
			$value = RTESafe($value);
						$str = substr($jsControlObjectParams, 0, -1);
			$jsControlObjectParams = $str.", useRTE: 'RTE'}";
			// creating src url
			$browser="";
			if(@$_REQUEST["browser"]=="ie")
				$browser="&browser=ie";
			$iframeSrcParam = GetTableURL($strTableName)."_rte.php?".($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD ? "id=".$id ."&": '').$iquery.$browser;
			// add JS code
			jscodeToAdd("RTEInnova",$jsControlObjectParams,$id, $pageObj);
			echo "<iframe frameborder=\"0\" vspace=\"0\" hspace=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" id=\"".$cfield."\" ".(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? "alt=\"".$strLabel."\" " : "")."name=\"".$cfield."\" title=\"Basic rich text editor\" style='width: " . ($nWidth+1) . "px;height: " . ($nHeight+100) . "px;'";
			echo " src=\"rte.php?table=".GetTableURL($strTableName)."&".($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD ? "id=".$id ."&": '').$iquery.$browser."&".($edit==MODE_ADD || $edit==MODE_INLINE_ADD ? "action=add" : '')."\">";  
			echo "</iframe>";
								}
		else{
				jscodeToAdd("TextArea",$jsControlObjectParams,$id, $pageObj);
				echo '<textarea id="'.$cfield.'" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.'" style="width: ' . $nWidth . 'px;height: ' . $nHeight . 'px;">'.htmlspecialchars($value).'</textarea>';
			}
	}
	else if($format==EDIT_FORMAT_PASSWORD)
	{
		echo '<input '.$inputStyle.' id="'.$cfield.'" type="Password" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($value).'">';
		jscodeToAdd("TextField",$jsControlObjectParams,$id, $pageObj);
	}
	else if($format==EDIT_FORMAT_DATE)
	{
		echo '<input id="'.$ctype.'" type="hidden" name="'.$ctype.'" value="date'.DateEditType($field).'">'.GetDateEdit($field,$value,DateEditType($field),$fieldNum,$edit,$id,$jsControlObjectParams, $pageObj);
		$str = substr($jsControlObjectParams, 0, -1);
		$jsControlObjectParams = $str.", ctrlType: 'date".DateEditType($field)."'}";	
	}
	else if($format==EDIT_FORMAT_RADIO)
	{
		jscodeToAdd("RadioControl",$jsControlObjectParams,$id, $pageObj);
		BuildRadioControl($field,$value,$fieldNum,$id,$edit);
	}
	else if($format==EDIT_FORMAT_CHECKBOX)
	{
		if($edit==MODE_ADD || $edit==MODE_INLINE_ADD || $edit==MODE_EDIT || $edit==MODE_INLINE_EDIT) 
		{
			$checked="";
			if($value && $value!=0)
				$checked=" checked";
			echo '<input id="'.$ctype.'" type="hidden" name="'.$ctype.'" value="checkbox">';
			$str = substr($jsControlObjectParams, 0, -1);
			$jsControlObjectParams = $str.", ctrlType: 'checkbox'}";			
			echo '<input id="'.$cfield.'" type="Checkbox" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.'" '.$checked.'>';
			jscodeToAdd("CheckBoxLookup",$jsControlObjectParams,$id, $pageObj);
		}
		else
		{
			echo '<input id="'.$ctype.'" type="hidden" name="'.$ctype.'" value="checkbox">';
			$str = substr($jsControlObjectParams, 0, -1);
			$jsControlObjectParams = $str.", ctrlType: 'checkbox'}";
			echo '<select id="'.$cfield.'" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').'name="'.$cfield.'">';
			$val=array("","on","off");
			$show=array("","True","False");
			foreach($val as $i=>$v)
			{
				$sel="";
				if($value===$v)
					$sel=" selected";
				echo '<option value="'.$v.'"'.$sel.'>'.$show[$i].'</option>';
			}
			echo "</select>";
			jscodeToAdd("DropDownLookup",$jsControlObjectParams,$id, $pageObj);
		}
	}
	else if($format==EDIT_FORMAT_DATABASE_IMAGE || $format==EDIT_FORMAT_DATABASE_FILE)
	{
		
		if ($format==EDIT_FORMAT_DATABASE_IMAGE){
			jscodeToAdd("ImageField",$jsControlObjectParams,$id, $pageObj);	
		}else {
			jscodeToAdd("FileField",$jsControlObjectParams,$id, $pageObj);
		}
		$disp="";
		$strfilename="";
		//$onchangefile="";
		if($edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
			$value=db_stripslashesbinary($value);
			$itype=SupposeImageType($value);
			$thumbnailed=false;
			$thumbfield="";

			if($itype)
			{
				if($thumbnailed)
				{
					$disp="<a ";
					
					if(IsUseiBox($field, $strTableName))
						$disp.= " rel='ibox'";
					else
						$disp.= " target=_blank";
						
					$disp.=" href=\"imager.php?table=".GetTableURL($strTableName)."&".$iquery."\">";
					$disp.= "<img name=\"".$cfield."\" border=0";
					if(isEnableSection508())
						$disp.= " alt=\"Image from DB\"";
					$disp.=" src=\"imager.php?table=".GetTableURL($strTableName)."&field=".rawurlencode($thumbfield)."&alt=".rawurlencode($field).$keylink."\">";
					$disp.= "</a>";
				}
				else
				{
					$disp='<img name="'.$cfield.'"';
					if(isEnableSection508())
						$disp.= ' alt="Image from DB"';
					$disp.=' border=0 src="imager.php?table='.GetTableURL($strTableName).'&'.$iquery.'">';
				}	
				
			}
			else
			{
				if(strlen($value))
				{
					$disp='<img name="'.$cfield.'" border=0 ';
					if(isEnableSection508())
						$disp.= ' alt="file"';
					$disp.=' src="images/file.gif">';
				}
				else
				{
					$disp='<img name="'.$cfield.'" border=0';
					if(isEnableSection508())
						$disp.= ' alt=" "';
					$disp.=' src="images/no_image.gif">';
				}
			}
//	filename
			if($format==EDIT_FORMAT_DATABASE_FILE && !$itype && strlen($value))
			{
				if(!($filename=@$data[GetFilenameField($field)]))
					$filename="file.bin";
				$disp='<a href="getfile.php?table='.GetTableURL($strTableName).'&filename='.htmlspecialchars($filename).'&'.$iquery.'".>'.$disp.'</a>';
			}
//	filename edit
			if($format==EDIT_FORMAT_DATABASE_FILE && GetFilenameField($field))
			{
				if(!($filename=@$data[GetFilenameField($field)]))
					$filename="";
				if($edit==MODE_INLINE_EDIT)
				{
					$strfilename='<br><label for="filename_'.$cfieldname.'">'."Filename".'</label>&nbsp;&nbsp;<input type="text" '.$inputStyle.' id="filename_'.$cfieldname.'" name="filename_'.$cfieldname.'" size="20" maxlength="50" value="'.htmlspecialchars($filename).'">';					
				}
				else
				{
					$strfilename='<br><label for="filename_'.$cfieldname.'">'."Filename".'</label>&nbsp;&nbsp;<input type="text" '.$inputStyle.' id="filename_'.$cfieldname.'" name="filename_'.$cfieldname.'" size="20" maxlength="50" value="'.htmlspecialchars($filename).'">';					
				}
			}
			$strtype='<br><input id="'.$ctype.'_keep" type="Radio" name="'.$ctype.'" value="file0" checked>'."Keep";
			if(strlen($value) && !IsRequired($field))
			{
				$strtype.='<input id="'.$ctype.'_delete" type="Radio" name="'.$ctype.'" value="file1">'."Delete";
			}
			$strtype.='<input id="'.$ctype.'_update" type="Radio" name="'.$ctype.'" value="file2">'."Update";
		}
		else
		{
//	if Add mode
			$strtype='<input id="'.$ctype.'" type="hidden" name="'.$ctype.'" value="file2">';
			if($format==EDIT_FORMAT_DATABASE_FILE && GetFilenameField($field))
			{
				$strfilename='<br><label for="filename_'.$cfieldname.'">'."Filename".'</label>&nbsp;&nbsp;<input type="text" '.$inputStyle.' id="filename_'.$cfieldname.'" name="filename_'.$cfieldname.'" size="20" maxlength="50">';			
			}
		}
		
		if($edit==MODE_INLINE_EDIT && $format==EDIT_FORMAT_DATABASE_FILE)
			$disp="";
		echo $disp.$strtype;
		if ($edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
			echo '<br>';
		}
		echo '<input type="File" '.$inputStyle.' id="'.$cfield.'" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').' name="'.$cfield.'" >'.$strfilename;
	}
	else if($format==EDIT_FORMAT_LOOKUP_WIZARD)
			BuildSelectControl($field, $value, $fieldNum, $edit,$id, $jsControlObjectParams, $additionalCtrlParams, $pageObj);
	else if($format==EDIT_FORMAT_HIDDEN)
			echo '<input id="'.$cfield.'" type="Hidden" name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
	else if($format==EDIT_FORMAT_READONLY)
	{
		echo '<input id="'.$cfield.'" type="Hidden" name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
		jscodeToAdd("TextField",$jsControlObjectParams,$id, $pageObj);	
	}
	else if($format==EDIT_FORMAT_FILE)
	{
		jscodeToAdd("FileField",$jsControlObjectParams,$id, $pageObj);
		$disp="";
		$strfilename="";
		$function="";
		if($edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
//	show current file
			if(ViewFormat($field)==FORMAT_FILE || ViewFormat($field)==FORMAT_FILE_IMAGE)
			{
				$disp=GetData($data,$field,ViewFormat($field))."<br>";
			}
			$filename=$value;			
//	filename edit
			$filename_size=30;
			if(UseTimestamp($field))
				$filename_size=50;
			$strfilename='<input type=hidden name="filenameHidden_'.$cfieldname.'" value="'.htmlspecialchars($filename).'"><br>'."Filename".'&nbsp;&nbsp;<input type="text" style="background-color:gainsboro" disabled id="filename_'.$cfieldname.'" name="filename_'.$cfieldname.'" size="'.$filename_size.'" maxlength="100" value="'.htmlspecialchars($filename).'">';
			if ( $edit==MODE_INLINE_EDIT ) {
				$strtype='<br><input id="'.$ctype.'_keep" type="Radio" name="'.$ctype.'" value="upload0" checked onclick="/*$(\'[@id='.$cfield.']\').css(\'backgroundColor\',\'gainsboro\');$(\'[@id='.$cfield.']\')[0].disabled=true;*/">'."Keep";
			} else {
				$strtype='<br><input id="'.$ctype.'_keep" type="Radio" name="'.$ctype.'" value="upload0" checked onclick="/*controlfilename'.$cfieldname.'(false)*/">'."Keep";
			}


			if(strlen($value) && !IsRequired($field))
			{
				$strtype.='<input id="'.$ctype.'_delete" type="Radio" name="'.$ctype.'" value="upload1">'."Delete";
			}
			$strtype.='<input id="'.$ctype.'_update" type="Radio" name="'.$ctype.'" value="upload2">'."Update";
		}
		else
		{
//	if Adding record		
			$filename_size=30;
			if(UseTimestamp($field))
				$filename_size=50;
			$strtype='<input id="'.$ctype.'" type="hidden" name="'.$ctype.'" value="upload2">';
			$strfilename='<br>'."Filename".'&nbsp;&nbsp;<input type="text" id="filename_'.$cfieldname.'" name="filename_'.$cfieldname.'" size="'.$filename_size.'" maxlength="100">';			
		}
		echo $disp.$strtype.$function;
		
		if ($edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
			echo '<br>';
		}
		
		echo '<input type="File" id="'.$cfield.'" '.(($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD) && $is508==true ? 'alt="'.$strLabel.'" ' : '').' name="'.$cfield.'" >'.$strfilename;
	}
	if(count($validate['basicValidate']) && array_search('IsRequired', $validate['basicValidate'])!==false)
		echo'&nbsp;<font color="red">*</font></span>';
	else
		echo '</span>';
}
function my_stripos($str,$needle, $offest)
{
    if (strlen($needle)==0 || strlen($str)==0)
		return false;
	return strpos(strtolower($str),strtolower($needle), $offest);
} 

function my_str_ireplace($search, $replace,$str)
{
    $pos=my_stripos($str,$search,0);
	if($pos===false)
		return $str;
	return substr($str,0,$pos).$replace.substr($str,$pos+strlen($search));
} 


function in_assoc_array($name, $arr)
{
foreach ($arr as $key => $value) 
	if ($key==$name)
		return true;

return false;
}

function buildLookupSQL($field,$table,$parentVal,$childVal="",$doCategoryFilter=true,$doValueFilter=false,$addCategoryField=false,$doWhereFilter=true, $oneRecordMode=false)
{
	global $strTableName;
	if(!strlen($table))
		$table=$strTableName;

//	read settings
	$nLookupType = GetFieldData($table,$field,"LookupType",LT_LISTOFVALUES);
	if($nLookupType!=LT_LOOKUPTABLE)
		return "";
	$bUnique=GetFieldData($table,$field,"LookupUnique",false);
	$strLookupWhere = LookupWhere($field,$table);
	$strOrderBy = GetFieldData($table,$field,"LookupOrderBy","");
	$bDesc = GetFieldData($table,$field,"LookupDesc",false);
	$strCategoryFilter = GetFieldData($table,$field,"CategoryFilter","");
	
	if($doCategoryFilter)
		$parentVal = make_db_value(CategoryControl($field,$table),$parentVal);
		
	if($doValueFilter)
		$childVal = make_db_value($field,$childVal);
		
//	build SQL string	
	$LookupSQL = "SELECT ";
		if($oneRecordMode)
		$LookupSQL .= "top 1 ";
	if($bUnique)
		$LookupSQL .= "DISTINCT ";
	$LookupSQL .= GetLWLinkField($field,$table);
	$LookupSQL .= ",".GetLWDisplayField($field,$table);
	if($addCategoryField && strlen($strCategoryFilter))
		$LookupSQL .= ",".AddFieldWrappers($strCategoryFilter);
	
	$LookupSQL .= " FROM ".AddTableWrappers(GetLookupTable($field,$table));

//	build Where clause

	$categoryWhere="";
	$childWhere="";
	if(UseCategory($field,$table) && $doCategoryFilter)
	{
		$condition = "=".$parentVal;
		if($childVal === "null")
			$condition = " is null";
		$categoryWhere=AddFieldWrappers($strCategoryFilter).$condition;
	}
	if($doValueFilter)
	{
		$condition = "=".$childVal;
		if($childVal === "null")
			$condition = " is null";
		$childWhere = AddFieldWrappers(GetLWLinkField($field,$table)).$condition;
	}
	$strWhere="";
	if($doWhereFilter && strlen($strLookupWhere))
		$strWhere="(".$strLookupWhere.")";
		
	if(strlen($categoryWhere))
	{
		if(strlen($strWhere))
			$strWhere.=" AND ";
		$strWhere.=$categoryWhere;
	}
	if(strlen($childWhere))
	{
		if(strlen($strWhere))
			$strWhere.=" AND ";
		$strWhere.=$childWhere;
	}
	if(strlen($strWhere))
		$LookupSQL.=" WHERE ".$strWhere;
//	order by clause
	if(strlen($strOrderBy))
	{
		$LookupSQL.= " ORDER BY ".AddTableWrappers(GetLookupTable($field,$table)).".".AddFieldWrappers($strOrderBy);
		if($bDesc)
			$LookupSQL.=" DESC";
	}
				return $LookupSQL;
}

function loadSelectContent($childFieldName, $parentVal, $doCategoryFilter=true, $childVal="", $initialLoad = true)
{
	global $conn,$LookupSQL,$strTableName;

	$table=$strTableName;
	
	$Lookup = "";
	$response = array();
	$output = "";
	
	

	$LookupSQL = buildLookupSQL($childFieldName,$table,$parentVal,$childVal,$doCategoryFilter,FastType($childFieldName,$table) && $initialLoad);

	$rs=db_query($LookupSQL,$conn);

	if(!FastType($childFieldName,$table))
	{
		while ($data = db_fetch_numarray($rs)) 
		{
			$response[] = $data[0];
			$response[] = $data[1];
		}
	}
	else
	{
		$data=db_fetch_numarray($rs);
//	one record only
		if($data && (strlen($childVal) || !db_fetch_numarray($rs)))
		{
			$response[] = $data[0];
			$response[] = $data[1];
		}
	}
	return $response;
}

function xmlencode($str)
{

	$str = str_replace("&","&amp;",$str);
	$str = str_replace("<","&lt;",$str);
	$str = str_replace(">","&gt;",$str);

	$out="";
	$len=strlen($str);
	$ind=0;
	for($i=0;$i<$len;$i++)
	{
		if(ord(substr($str,$i,1))>=128)
		{
			if($ind<$i)
				$out.=substr($str,$ind,$i-$ind);
			$out.="&#".ord(substr($str,$i,1)).";";
			$ind=$i+1;
		}
	}
	if($ind<$len)
		$out.=substr($str,$ind);
	return str_replace("'","&apos;",$out);

}

function print_inline_array(&$arr,$printkey=false)
{
	if(!$printkey)
	{
		foreach ( $arr as $key=>$val )
			echo str_replace(array("&","<","\\","\r","\n"),array("&amp;","&lt;","\\\\","\\r","\\n"),str_replace(array("\\","\r","\n"),array("\\\\","\\r","\\n"),$val))."\\n";
	}
	else
	{
		foreach( $arr as $key=>$val )
			echo str_replace(array("&","<","\\","\r","\n"),array("&amp;","&lt;","\\\\","\\r","\\n"),str_replace(array("\\","\r","\n"),array("\\\\","\\r","\\n"),$key))."\\n";
	}
		
}


function GetChartXML($chartname)
{
	$strTableName = GetTableByShort($chartname);	
	return GetTableData($strTableName, '.chartXml', '');
}

//For add script code to function postloadstep, that performed after loading all files
function AddScript2Postload($code,$id)
{
	echo "<s"."cript  language='javascript'>
			Runner.util.ScriptLoader.add2PostLoad(function(){
				".$code."
			}, ".$id.");
		 </script>";
}

function AddScriptBe4Postload($code,$id)
{
	echo "<s"."cript  language='javascript'>
			Runner.util.ScriptLoader.addBe4PostLoad(function(){
				".$code."
			}, ".$id.");
		 </script>";
}

function AddCSSFile($file)
{
	global $includes_css;
	$includes_css[]=$file;
}

function AddJSFile($file,$req1="",$req2="",$req3="")
{
	global $includes_js,$includes_jsreq;
	$includes_js[]=$file;
	if($req1!="")
		$includes_jsreq[$file]=array($req1);
	if($req2!="")
		$includes_jsreq[$file][]=$req2;
	if($req3!="")
		$includes_jsreq[$file][]=$req3;
}

function LoadJS_CSS($id)
{
	global $includes_js,$includes_jsreq,$includes_css;
	$includes_js=array_unique($includes_js);
	$includes_css=array_unique($includes_css);
	$sl = "sl".$id;
	$out="var ".$sl." = new ScriptLoader('".$id."');\r\n";
	foreach($includes_css as $file)
		$out.=$sl.".loadCSS('".$file."');";
	foreach($includes_js as $file)
	{
		$out .= $sl.".addJS('".$file."'";
		if(array_key_exists($file,$includes_jsreq))
		{
			foreach($includes_jsreq[$file] as $req)
				$out.=",'".$req."'";
		}
		$out.=");\r\n";
	}
	$out.=$sl.".load();";
	return $out;
}


function GetSiteUrl()
{
	$url = "http://".$_SERVER["SERVER_NAME"];
	if($_SERVER["SERVER_PORT"]!=80)
	{
		if ($_SERVER["SERVER_PORT"]==443)
		   $url = "https://".$_SERVER["SERVER_NAME"];
		else
		   $url.=":".$_SERVER["SERVER_PORT"];
	}
	return $url;
}


function GetAuditObject($table="")
{
	return NULL;
	
	if (GetTableData($table, '.audit', false) || !$table)
	{	
		require_once(getabspath("include/audit.php"));
			}
	else
	{
		return NULL;
	}
}
function GetLockingObject($table="")
{
	return NULL;

	if(!$table)
	{
		global $strTableName;
		$table = $strTableName;
	}
	
	if (GetTableData($table, '.locking', false))
	{	
		require_once(getabspath("include/locking.php"));
		return new oLocking();
	}
	else
	{
		return NULL;
	}
}

function isEnableSection508()
{
	return false;
}
function isEnableUpper($val)
{
	global $strTableName,$tables_data;
	if($tables_data[$strTableName][".NCSearch"])
		return db_upper($val);
	else
		return $val;
}
/**
 * Returns validation type which defined in js validation object.
 * Use this function, because runner constants has another names of validation functions
 *
 * @param string $name
 * @return string
 */
function getJsValidatorName($name) 
{	
	switch ($name) 
	{
		case "Number":
			return "IsNumeric";
			break;
		case "Password":
			return "IsPassword";
			break;
		case "Email":
			return "IsEmail";
			break;
		case "Currency":
			return "IsMoney";
			break;
		case "US ZIP Code":
			return "IsZipCode";
			break;
		case "US Phone Number":
			return "IsPhoneNumber";
			break;
		case "US State":
			return "IsState";
			break;
		case "US SSN":
			return "IsSSN";
			break;
		case "Credit Card":
			return "IsCC";
			break;
		case "Time":
			return "IsTime";
			break;
		case "Regular expression":
			return "RegExp";
			break;						
		default:
			return $name;
			break;
	}
}

function GetInputElementId($field ,$id)
{
	$format=GetEditFormat($field);
	if($format==EDIT_FORMAT_DATE)
	{
		$type=DateEditType($field);
		if($type==EDIT_DATE_DD || $type==EDIT_DATE_DD_DP)
			return "dayvalue_".GoodFieldName($field)."_".$id;
		else
			return "value_".GoodFieldName($field)."_".$id;
	}
	else if($format==EDIT_FORMAT_RADIO)
		return "radio_".GoodFieldName($field)."_".$id."_0";
	else if($format==EDIT_FORMAT_LOOKUP_WIZARD)	
	{
		$lookuptype=LookupControlType($field);
		if($lookuptype==LCT_AJAX || $lookuptype==LCT_LIST)
			return "display_value_".GoodFieldName($field)."_".$id;
		else
			return "value_".GoodFieldName($field)."_".$id;
	}	
	else
		return "value_".GoodFieldName($field)."_".$id;		
}

function SetLangVars($links)
{
	global $xt;
	$xt->assign("lang_label",true);
	if(@$_REQUEST["language"])
		$_SESSION["language"]=@$_REQUEST["language"];

	$var=GoodFieldName(mlang_getcurrentlang())."_langattrs";
	$xt->assign($var,"selected");
	$is508=isEnableSection508();
	if($is508)
		$xt->assign_section("lang_label","<label for=\"lang\">","</label>");
	$xt->assign("langselector_attrs","name=lang ".($is508==true ? "id=\"lang\" " : "")."onchange=\"javascript: window.location='".$links.".php?language='+this.options[this.selectedIndex].value\"");
}

function GetTableCaption($table)
{
	global $tableCaptions;
	return @$tableCaptions[mlang_getcurrentlang()][$table];
}

function GetFieldByLabel($table="", $label) 
{
	global $field_labels, $strTableName;
	if (!$table)
	{
		$table = $strTableName;
	}
	
	if(!array_key_exists($table,$field_labels))
		return "";
	$currLang = mlang_getcurrentlang();
	if(!array_key_exists($currLang,$field_labels[$table]))
		return "";	
	$lables = $field_labels[$table][mlang_getcurrentlang()];
	foreach ($lables as $key=>$val)
	{
		if ($val == $label)
		{
			return $key;
		}
	}
	return '';
}

function GetFieldLabel($table,$field)
{
	global $field_labels;
	if(!array_key_exists($table,$field_labels))
		return "";
	return @$field_labels[$table][mlang_getcurrentlang()][$field];
}

function GetCustomLabel($custom)
{
	global $custom_labels;
	return @$custom_labels[mlang_getcurrentlang()][$custom];
}

function mlang_getcurrentlang()
{
	global $mlang_messages,$mlang_defaultlang;
	if(@$_SESSION["language"])
		return $_SESSION["language"];
	return $mlang_defaultlang;
}

function mlang_getlanglist()
{
	global $mlang_messages,$mlang_defaultlang;
	return array_keys($mlang_messages);
}

function displayDetailsOn($table,$page)
{
	global $detailsTablesData;
	if(!isset($detailsTablesData[$table]) && !is_array($detailsTablesData[$table]))
		return false;
	if($page == PAGE_EDIT)
		$key="previewOnEdit";
	elseif($page == PAGE_ADD)
		$key="previewOnAdd";
	elseif($page == PAGE_VIEW)
		$key="previewOnView";
	else
		$key="previewOnList";
	for($i=0;$i<count($detailsTablesData[$table]);$i++)
	{
		if($detailsTablesData[$table][$i][$key])
			return true;
	}
	return false;
}

function showDetailTable($params)
{
	global $strTableName;
	$oldTableName = $strTableName;
	$strTableName = $params["table"];
	// show page
	if($params["dpObject"]->isDispGrid())
		$params["dpObject"]->showPage();	
	$strTableName = $oldTableName;
}

//	update record on Edit page

function DoUpdateRecordSQL($table,&$evalues,&$blobfields,$strWhereClause, $pageid)
{
	global $error_happened,$conn,$inlineedit,$usermessage,$message;
	if(!count($evalues))
		return true;
	$strSQL = "update ".AddTableWrappers($table)." set ";
	$blobs = PrepareBlobs($evalues,$blobfields);
//	construct SQL string
	foreach($evalues as $ekey=>$value)
	{
		if(in_array($ekey,$blobfields))			
			$strValues=$value;
		else
			$strValues=add_db_quotes($ekey,$value);
		$strSQL.=GetFullFieldName($ekey)."=".$strValues.", ";
	}
	if(substr($strSQL,-2)==", ")
		$strSQL=substr($strSQL,0,strlen($strSQL)-2);
	if($strWhereClause === "")
	{
		$strWhereClause = " (1=1) ";
	}
	$strSQL.=" where ".$strWhereClause;
	if(SecuritySQL("Edit"))
		$strSQL .= " and (".SecuritySQL("Edit").")";

	if(!ExecuteUpdate($strSQL,$blobs,false))
		return false;

//	delete & move files
	ProcessFiles();
	if ( $inlineedit ) 
	{
		$status="UPDATED";
		$message=""."Record updated"."";
		$IsSaved = true;
	} 
	else 
		$message="<<< "."Record updated"." >>>";
	if($usermessage!="")
		$message = $usermessage;
	return true;
}

//	insert record on Add & Register pages

function DoInsertRecordSQL($table,&$avalues,&$blobfields, $pageid)
{
	global $error_happened,$conn,$files_delete,$files_move,$files_save,$inlineadd,$usermessage,$message,$failed_inline_add,$keys;
//	make SQL string
	$strSQL = "insert into ".AddTableWrappers($table)." ";
	$strFields="(";
	$strValues="(";
	$blobs = PrepareBlobs($avalues,$blobfields);
	foreach($avalues as $akey=>$value)
	{
		$strFields.=GetFullFieldName($akey).", ";
		if(in_array($akey,$blobfields))			
			$strValues.=$value.", ";
		else
			$strValues.=add_db_quotes($akey,$value).", ";
	}
	if(substr($strFields,-2)==", ")
		$strFields=substr($strFields,0,strlen($strFields)-2);
	if(substr($strValues,-2)==", ")
		$strValues=substr($strValues,0,strlen($strValues)-2);
	$strSQL.=$strFields.") values ".$strValues.")";
	
	if(!ExecuteUpdate($strSQL,$blobs,true))
		return false;
	
	if($error_happened)
		return false;
	ProcessFiles();
	if ( $inlineadd==ADD_INLINE ) 
	{
		$status="ADDED";
		$message=""."Record was added"."";
		$IsSaved = true;
	} 
	else
		$message="<<< "."Record was added"." >>>";
	if($usermessage!="")
		$message = $usermessage;
		
	$auditObj = GetAuditObject($table);
	
	if($inlineadd==ADD_SIMPLE || $inlineadd==ADD_INLINE || $inlineadd==ADD_ONTHEFLY || $inlineadd==ADD_MASTER || function_exists("AfterAdd") || $auditObj)
	{
		$failed_inline_add = false;
		$keyfields=GetTableKeys();
		foreach($keyfields as $k)
		{
			if(array_key_exists($k,$avalues))
				$keys[$k]=$avalues[$k];
			elseif(IsAutoincField($k))
			{
							$mystrSQL = "SELECT MAX(".AddFieldWrappers($k).") FROM ".AddTableWrappers($table);
				$myrs=db_query($mystrSQL,$conn);
				if($mydata = db_fetch_numarray($myrs));
					$keys[$k] = $mydata[0];
			}
			else
				$failed_inline_add = true;
		}
	}
	return true;
}

function event_exists($event)
{
	global $strTableName,$arrEventTables,$blockAlienEvents;
	if(!function_exists($event))
		return false;
	if(!$blockAlienEvents)
		return true;
	return (@$arrEventTables[$event]==$strTableName);
}

function add_nocache_headers()
{
	header("Cache-Control: no-cache, no-store, max-age=0, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
}
function IsStoredProcedure($strSQL)
{
	if(strlen($strSQL)>6)
	{
		$c=strtolower(substr($strSQL,6,1));
		if(strtolower(substr($strSQL,0,6))=="select" && ($c<'0' || $c>'9') && ($c<'a' || $c>'z') && $c!='_')
			return false;
		else
			return true;
	}
	else
		return true;
}
?>