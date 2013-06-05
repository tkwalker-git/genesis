<?php
class SearchClause
{
	/**
	 * Array with all session data
	 *
	 * @var array
	 */
	var $_where = array();
	
	/**
	 * Name of current table, for which instance of class was created
	 *
	 * @var string
	 */
	var $tName = "";
	/**
	 * Array of fields for basic search
	 *
	 * @var array
	 */
	var $searchFieldsArr = array();
	/**
	 * Type of search
	 *
	 * @var string
	 */
	//var $srchType = 'advanced';
	var $srchType = 'integrated';
	
	/**
	 * Session vars pref
	 *
	 * @var string
	 */
	var $sessionPrefix = "";
	/**
	 * Indicator, if used search it will be true
	 *
	 * @var bool
	 */
	var $bIsUsedSrch = false;
	
	var $panelSearchFields = array();
	
	function SearchClause($tName="", $searchFieldsArr=array(), $sessionPrefix='')
	{
		global $strTableName;
		
		$this->tName = ($tName ? $tName : $strTableName);
		$this->sessionPrefix = ($sessionPrefix ? $sessionPrefix : $this->tName);
		$this->searchFieldsArr = $searchFieldsArr;
		
					
		$this->panelSearchFields = GetTableData($this->tName,".panelSearchFields",array());	
	}
	/**
	 * Build where for advanced search. 
	 * Need for compability with old projects
	 *
	 * @protected
	 * @return string
	 */
	function buildAdvancedWhere() 
	{
		$sWhere="";
		if(isset($this->_where[$this->sessionPrefix."_asearchfor"]))
		{			
			foreach($this->_where[$this->sessionPrefix."_asearchfor"] as $f => $sfor)
			{
				$strSearchFor = trim($sfor);
				$strSearchFor2 = "";
				$type=@$this->_where[$this->sessionPrefix."_asearchfortype"][$f];
				if(array_key_exists($f,@$this->_where[$this->sessionPrefix."_asearchfor2"]))
					$strSearchFor2=trim(@$this->_where[$this->sessionPrefix."_asearchfor2"][$f]);
				if($strSearchFor!="" || true)
				{
					if (!$sWhere) 
					{
						if($this->_where[$this->sessionPrefix."_asearchtype"]=="and")
							$sWhere="1=1";
						else
							$sWhere="1=0";
					}
					$strSearchOption=trim($this->_where[$this->sessionPrefix."_asearchopt"][$f]);
					if($where=StrWhereAdv($f, $strSearchFor, $strSearchOption, $strSearchFor2,$type))
					{
						if($this->_where[$this->sessionPrefix."_asearchnot"][$f])
							$where="not (".$where.")";
						if($this->_where[$this->sessionPrefix."_asearchtype"]=="and")
		   					$sWhere .= " and ".$where;
						else
		   					$sWhere .= " or ".$where;
					}
				}
			}
		}
			
		return $sWhere;
	}
	/**
	 * Build where for simple search. 
	 * Need for compability with old projects
	 * 
	 * @protected
	 * @return string
	 */
	function buildSimpleWhere() 
	{
		$sWhere = '';
		
		
		
		$strSearchFor = trim($this->_where[$this->sessionPrefix."_searchfor"]);
		$strSearchOption = trim($this->_where[$this->sessionPrefix."_searchoption"]);
		if(@$this->_where[$this->sessionPrefix."_searchfield"]) 
		{
			$strSearchField = $this->_where[$this->sessionPrefix."_searchfield"];
			if($where = StrWhereExpression($strSearchField, $strSearchFor, $strSearchOption, ""))
				$sWhere = whereAdd($sWhere, $where);
			else
				$sWhere = whereAdd($sWhere, "1=0");
		} else {
			$strWhere = "1=0";
			for($i = 0; $i < count($this->searchFieldsArr); $i ++) {
				if($where = StrWhereExpression($this->searchFieldsArr[$i], $strSearchFor, $strSearchOption, ""))
					$strWhere.= " or ".$where;
			}
			$sWhere = whereAdd($sWhere, $strWhere);
		}
		
		
		
		return $sWhere;
	}
	/**
	 * Build where for united search
	 * Params are common for advanced search and search panel on list
	 * Use in new projects
	 * 
	 * @protected
	 * @return string
	 *
	 */
	function buildItegratedWhere($fieldsArr) 
	{		
		
		if (!count($fieldsArr))
			return '';
		
		// get global options		
		$simpleSrch = $this->_where[$this->sessionPrefix."_simpleSrch"];
		if (trim($simpleSrch) === '%')
		{
			$simpleSrch = '['.$simpleSrch.']';
		}
		$srchType = $this->_where[$this->sessionPrefix."_srchType"];	
		$srchFields = &$this->_where[$this->sessionPrefix."_srchFields"];		
		
		$sWhere = '';
		// build where for any field contains search
		if (strlen($simpleSrch) || $this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"] == "Empty")
		{			
			if (strlen($this->_where[$this->sessionPrefix."simpleSrchFieldsComboOpt"]))
			{
				$where = StrWhereExpression($this->_where[$this->sessionPrefix."simpleSrchFieldsComboOpt"], $simpleSrch, $this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"], "");
				if($where && $this->_where[$this->sessionPrefix."simpleSrchTypeComboNot"])
				{
					$where ="not (".$where.")";
				}
				$sWhere = $where;
			}
			else 
			{
				$sWhere = "1=0";
				for($i = 0; $i < count($this->searchFieldsArr); $i++) {
					if (in_array($this->searchFieldsArr[$i], $fieldsArr))
					{
						$where = StrWhereExpression($this->searchFieldsArr[$i], $simpleSrch, $this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"], "");
						// add not 
						if($where && $this->_where[$this->sessionPrefix."simpleSrchTypeComboNot"])
						{
							$where ="not (".$where.")";
						}
						if($where)
						{
							$sWhere.= " or ".$where;
						}
					}
				}	
			}
		}
		
		$resWhere = whereAdd('', $sWhere);				
		// if there are fields for build advanced where
		$sWhere = '';
		if (count($srchFields)){
			// prepare vars
			$sWhere = $srchType=="and" ? "(1=1" : "(1=0";				
			$prevSrchFieldName = '';		
			
			// build where		
			foreach ($srchFields as $srchF)
			{	
				
				if (in_array($srchF['fName'], $fieldsArr))
				{
					$where=StrWhereAdv($srchF['fName'], $srchF['value1'], $srchF['opt'], $srchF['value2'], $srchF['eType']);							
					if($where)
					{
						// add not 
						if($srchF['not'])
						{
							$where="not (".$where.")";
						}
						// and|or depends on search type
						if($srchType=="and")
						{
							// add ( if we add new clause block for same field name
		   					$sWhere .= ($prevSrchFieldName != $srchF['fName'] ? ") and (" : " and ").$where;
						}
		   				else
		   				{
		   					$sWhere .= " or ".$where;
		   				}
					}			
					$prevSrchFieldName = $srchF['fName'];
				}
			}
			// add ) to final field block clause
			$sWhere .= ')';
		}
		$resWhere = whereAdd($resWhere, $sWhere);
		
		
		return $resWhere;
	}
	/**
	 * Public, return where clause
	 *
	 * @public
	 * @return string
	 */	
	function getWhere($fieldsArr)
	{		
		
		$sWhere = '';
		
		switch ($this->srchType){
			case 'showall' : 
				$sWhere = '';
				break;
			case 'advanced' :
				$sWhere = $this->buildAdvancedWhere();
				break;
			case 'simple' :
				$sWhere = $this->buildSimpleWhere();
				break;
			case 'integrated' :
				$sWhere = $this->buildItegratedWhere($fieldsArr);	
				break;
			default:
				$sWhere = '';				
		}
				
									
		return $sWhere;
		
	}
	
	function getSuggestWhere($fName, $fType, $suggestAllContent, $searchVal) 
	{		
		$sWhere = '';
		
		$searchOpt = $suggestAllContent ? "Contains" : "Equal";
		
		$where = StrWhereAdv($fName, $searchVal, $searchOpt, '', $fType, true);
		
		return $where;
	}
	
	
	/**
	 * Parse form with advanced search REQUEST	 
	 * Need for compability with old projects
	 * 
	 * @protected
	 * @return string
	 */
	function parseAdvancedRequest() 
	{
		
		$additionalControlId = 1;	
		
		$this->_where[$this->sessionPrefix."_asearchnot"]=array();
		$this->_where[$this->sessionPrefix."_asearchopt"]=array();
		$this->_where[$this->sessionPrefix."_asearchfor"]=array();
		$this->_where[$this->sessionPrefix."_asearchfor2"]=array();
		$tosearch=0;
		$asearchfield = postvalue("asearchfield");
		$this->_where[$this->sessionPrefix."_asearchtype"] = postvalue("type");
		if(!$this->_where[$this->sessionPrefix."_asearchtype"])
			$this->_where[$this->sessionPrefix."_asearchtype"]="and";
		if(isset($asearchfield) && is_array($asearchfield))
		{
			foreach($asearchfield as $field)
			{
				$gfield=GoodFieldName($field);
				$asopt=postvalue("asearchopt_".$gfield);
				$value1=postvalue("value_".$gfield);
				$type=postvalue("type_".$gfield);
				$value2=postvalue("value1_".$gfield);
				$not=postvalue("not_".$gfield);
				if($value1 || $asopt=='Empty')
				{
					$tosearch=1;
					$this->_where[$this->sessionPrefix."_asearchopt"][$field]=$asopt;
					if(!is_array($value1))
						$this->_where[$this->sessionPrefix."_asearchfor"][$field]=$value1;
					else
						$this->_where[$this->sessionPrefix."_asearchfor"][$field]=($value1);
					$this->_where[$this->sessionPrefix."_asearchfortype"][$field]=$type;
					if($value2)
						$this->_where[$this->sessionPrefix."_asearchfor2"][$field]=$value2;
					$this->_where[$this->sessionPrefix."_asearchnot"][$field]=($not=="on");
				}
			}
		}
		if($tosearch)
			$this->_where[$this->sessionPrefix."_search"]=2;
		else
			$this->_where[$this->sessionPrefix."_search"]=0;
		$this->_where[$this->sessionPrefix."_pagenumber"]=1;
	}
	/**
	 * Parse form with simple search REQUEST	 
	 * Need for compability with old projects
	 * 
	 * @protected
	 * @return string
	 */
	function parseSimpleRequest() 
	{
		if(postvalue("SearchField") == "" || in_array(postvalue("SearchField"), $this->searchFieldsArr) === true) 
		{
			$this->_where[$this->sessionPrefix."_searchfield"]= postvalue("SearchField");
			$this->_where[$this->sessionPrefix."_searchoption"]= postvalue("SearchOption");
			$this->_where[$this->sessionPrefix."_searchfor"]= postvalue("SearchFor");
			if(postvalue("SearchFor") != "" || postvalue("SearchOption") == 'Empty')
				$this->_where[$this->sessionPrefix."_search"]= 1;
			else
				$this->_where[$this->sessionPrefix."_search"]= 0;
			$this->_where[$this->sessionPrefix."_pagenumber"]= 1;
		} else {
			$this->_where[$this->sessionPrefix."_search"]= 0;
		}
	}
	/**
	 * Parse form with union search REQUEST
	 * Params are common for advanced search and search panel on list
	 * Use in new projects
	 * 
	 * @protected
	 * @return string
	 */
	function parseItegratedRequest() 
	{		
		// parse global options
		$this->_where[$this->sessionPrefix."_simpleSrch"] = trim(postvalue("ctlSearchFor"));	
		$this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"] = trim(postvalue("simpleSrchTypeComboOpt"));
		if (!strlen($this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"]))
		{
			$this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"] = "Contains";
		}
		$this->_where[$this->sessionPrefix."simpleSrchTypeComboNot"] = trim(postvalue("simpleSrchTypeComboNot")) != '';
		$this->_where[$this->sessionPrefix."simpleSrchFieldsComboOpt"] = trim(postvalue("simpleSrchFieldsComboOpt"));
		
			
		$srchType = postvalue("criteria");
		if(!$srchType)
			$srchType="and";
		$this->_where[$this->sessionPrefix."_srchType"] = $srchType;	
		// prepare vars		
		$this->_where[$this->sessionPrefix."_srchFields"] = array();		 
		$j=1;
		
		// scan all srch fields		
		while ($fName = postvalue('field'.$j)) 
		{	
			// check if field in request exist in searchFieldsArray, for prevent SQL injection
			if (in_array($fName, $this->searchFieldsArr))
			{	
				$srchF = array();
				$srchF['fName'] = trim($fName);
				$srchF['eType'] = trim(postvalue('type'.$j));
				$srchF['value1'] = trim(postvalue('value'.$j.'1'));
				$srchF['opt'] = (postvalue('option'.$j) ? postvalue('option'.$j) : 'Contains');
				$srchF['value2'] = trim(postvalue('value'.$j.'2'));	
				$srchF['not'] = postvalue('not'.$j) == 'on';
				$this->_where[$this->sessionPrefix."_srchFields"][] = $srchF;
				
			}
			$j++;
		}	
		
		// process srch panel attrs, better then use coockies. 
		$this->_where[$this->sessionPrefix."_srchOptShowStatus"]= postvalue('srchOptShowStatus')==='1';// || count($this->_where[$this->sessionPrefix."_srchFields"])>0;
		$this->_where[$this->sessionPrefix."_ctrlTypeComboStatus"]= postvalue('ctrlTypeComboStatus')==='1';
		$this->_where[$this->sessionPrefix."srchWinShowStatus"]= postvalue('srchWinShowStatus')==='1';
		
	}
	
	/**
	 * Parse REQUEST
	 * 
	 * @public
	 * @return string
	 */
	function parseRequest()
	{			
		//set session if show all records
		if(@$_REQUEST["a"] == "showall"){
			$this->_where[$this->sessionPrefix."_search"]= 0;	
			$this->srchType = 'showAll';	
			$this->bIsUsedSrch = false;	
			$this->clearSearch();
			$_SESSION[$this->sessionPrefix."_pagenumber"] = 1;
		}//set session if simple search	
		else if(@$_REQUEST["a"] == "search") {
			$this->srchType = 'simple';
			$this->bIsUsedSrch = true;
			$this->parseSimpleRequest();			
		} //set session if advanced search
		else if(@$_REQUEST["a"] == "advsearch") {
			$this->srchType = 'advanced';
			$this->bIsUsedSrch = true;
			$this->parseAdvancedRequest();
		}
		else if(@$_REQUEST["a"] == "integrated"){
			$this->srchType = 'integrated';
			$this->bIsUsedSrch = true;
			$this->parseItegratedRequest();
			$_SESSION[$this->sessionPrefix."_pagenumber"] = 1;
		}
	}
	/**
	 * Clears search params
	 *
	 */
	function clearSearch()
	{
		$this->_where[$this->sessionPrefix."_simpleSrch"] = '';		
		$this->_where[$this->sessionPrefix."_srchType"] = "and";
		$this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"] = "Contains";
		$this->_where[$this->sessionPrefix."simpleSrchTypeComboNot"] = false;
		$this->_where[$this->sessionPrefix."simpleSrchFieldsComboOpt"] = '';
		// prepare vars		
		$this->_where[$this->sessionPrefix."_srchFields"] = array();
		// process srch panel attrs, better then use coockies. 
		$this->_where[$this->sessionPrefix."_srchOptShowStatus"]= false;
		$this->_where[$this->sessionPrefix."_ctrlTypeComboStatus"]= false;
		$this->_where[$this->sessionPrefix."srchWinShowStatus"]= false;
		
	}
	
	function applyWhere(&$sql, $simpleFieldsArr, $aggFieldsArr)
	{
		if (!count($simpleFieldsArr) && !count($aggFieldsArr)){
			return $sql;
		}
		

		$searchWhereClause = $this->getWhere($simpleFieldsArr);
		$searchHavingClause = $this->getWhere($aggFieldsArr);
		
		if($searchWhereClause)
		{
			if($sql[2])
			{
				$sql[2] = '('.$sql[2].') AND ';
			}
			
			$sql[2] .= '('.$searchWhereClause.') ';
		}
		
		if($searchHavingClause)
		{
			if($sql[4])
			{
				$sql[4] = '('.$sql[4].') AND ';
			}
			
			$sql[4] .= '('.$searchHavingClause.') ';
		}
		
		return $sql;
	}
	/**
	 * deprecated
	 *
	 * @return unknown
	 */
	function getTable()
	{
		return $this->_where;
	}
	
	
	function getSearchCtrlParams($fName)
	{
		$resArr = array();
		if ($this->_where[$this->sessionPrefix."_srchFields"])
			foreach ($this->_where[$this->sessionPrefix."_srchFields"] as $srchField){
				if (strtolower($srchField['fName']) == strtolower($fName)){
					$resArr[] = $srchField;
				}
			}
		return $resArr;
	}
	
	function getUsedCtrlsCount() {
		if ($this->_where[$this->sessionPrefix."_srchFields"]){
			return count($this->_where[$this->sessionPrefix."_srchFields"]);
		}else{
			return 0;
		}
	}
	/**
	 * Global search params: use and|or, srchType panel|adv and simple search value
	 *
	 * @return array
	 */	
	function getSearchGlobalParams() {
		return array('simpleSrch'=>$this->_where[$this->sessionPrefix."_simpleSrch"], 
					 'srchTypeRadio'=>$this->_where[$this->sessionPrefix."_srchType"],
					 'srchType'=>$this->srchType,
					 'simpleSrchTypeComboOpt' => $this->_where[$this->sessionPrefix."simpleSrchTypeComboOpt"],
					 'simpleSrchTypeComboNot' => $this->_where[$this->sessionPrefix."simpleSrchTypeComboNot"],
					 'simpleSrchFieldsComboOpt' => $this->_where[$this->sessionPrefix."simpleSrchFieldsComboOpt"]
		);
	}
	/**
	 * Search panel status indicators array. Open|closed etc
	 *
	 * @return array
	 */
	function getSrchPanelAttrs(){		
		return array('srchOptShowStatus' => ($this->_where[$this->sessionPrefix."_srchOptShowStatus"] || count($this->panelSearchFields)),
					 'ctrlTypeComboStatus' => $this->_where[$this->sessionPrefix."_ctrlTypeComboStatus"],
					 'srchWinShowStatus' => $this->_where[$this->sessionPrefix."srchWinShowStatus"]
		);
	}
	/**
	 * Returns indicator is search was init
	 *
	 * @return unknown
	 */
	function isUsedSrch() {
		return $this->bIsUsedSrch;
	}
	
	
}
?>