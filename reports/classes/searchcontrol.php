<?php
/**
 * Base class for all search control builders
 *
 */
class SearchControl {
			
	var $tName = '';
	var $globSrchParams = array();
	var $getSrchPanelAttrs = array();
	var $dispNoneStyle = 'style="display: none;"';
	var $pageObj = null;
	var $searchClauseObj = false;
	var $id = 1;
	
	
	function SearchControl($id, $tName='', &$searchClauseObj, &$pageObj)
	{
		$this->tName = $tName;
		
		$this->searchClauseObj = $searchClauseObj;		
		$this->getSrchPanelAttrs = $this->searchClauseObj->getSrchPanelAttrs();
		$this->globSrchParams = $this->searchClauseObj->getSearchGlobalParams();
		
		$this->id = $id;
		$this->pageObj = $pageObj;				
	}
	/**
	 * Checks if field need to add search suggest event
	 *
	 * @param string $fName
	 * @return bool
	 */
	function isAddSuggestEvent($fName)
	{
		if (!GetTableData($this->tName, '.isUseAjaxSuggest', true))
		{
			return false;
		}
		$fType = GetEditFormat($fName, $this->tName);
		//LookupControlType($fName, $this->tName)
		// add suggest only for text field edit format and not lookup.
		if ($fType == EDIT_FORMAT_TEXT_FIELD && !FastType($fName, $this->tName))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	function getCtrlParamsArr($fName, $recId, $fieldNum=0, $value, $renderHidden = false, $isCached=true) 
	{
		$fType = GetEditFormat($fName, $this->tName);	
			
		if ($fType == EDIT_FORMAT_TEXT_AREA
			|| $fType == EDIT_FORMAT_PASSWORD
			|| $fType == EDIT_FORMAT_HIDDEN
			|| $fType == EDIT_FORMAT_READONLY
			|| $fType == EDIT_FORMAT_FILE)
		{
			$format=EDIT_FORMAT_TEXT_FIELD;
		}
		else 
		{
			$format = $fType;
		}
		
		$control = array();
		$control["params"] = array();
		$control["func"]="xt_buildeditcontrol";
		$control["params"]["field"]=$fName;
		$control["params"]["mode"]="search";
		$control["params"]["id"]=$recId;
		$control["params"]["fieldNum"]=$fieldNum;		
		$control["params"]["format"]=$format;
		$control["params"]["pageObj"]=$this->pageObj;
		
		
				
		$additionalCtrlParams = array();
		$additionalCtrlParams['hidden'] = $renderHidden || $isCached;
				
		$control["params"]["additionalCtrlParams"]=$additionalCtrlParams;
		
		$control["params"]["value"]= $value;
		
		return $control;
	}
	
	function getSecCtrlParamsArr($fName, $recId, $fieldNum=0, $value, $renderHidden = false, $isCached=true) {
		
		$fType = GetEditFormat($fName, $this->tName);	
		
		if ($this->isNeedSecondCtrl($fName))
		{			 				
			return $this->getCtrlParamsArr($fName, $recId, ($fieldNum+1), $value, $renderHidden, $isCached);
		}
		else 
		{
			return false;
		}		
	}
	
	function isNeedSecondCtrl($fName)
	{
		
		$fType = GetEditFormat($fName, $this->tName);	
		
		if ($fType == EDIT_FORMAT_DATE || $fType == EDIT_FORMAT_TIME || $fType == EDIT_FORMAT_TEXT_FIELD || $fType == EDIT_FORMAT_TEXT_AREA
			 || $fType == EDIT_FORMAT_PASSWORD || $fType == EDIT_FORMAT_HIDDEN || $fType == EDIT_FORMAT_READONLY)
		{			 	
			return true;
		}
		else 
		{
			return false;
		}		
	}
	
	function getSimpleSearchTypeCombo($selOpt, $not) {
		$options="";
		$options.="<OPTION VALUE=\"Contains\" ".(($selOpt=="Contains" && !$not)?"selected":"").">"."Contains"."</option>";
		$options.="<OPTION VALUE=\"Equals\" ".(($selOpt=="Equals" && !$not)?"selected":"").">"."Equals"."</option>";
		$options.="<OPTION VALUE=\"Starts with\" ".(($selOpt=="Starts with" && !$not)?"selected":"").">"."Starts with"."</option>";
		$options.="<OPTION VALUE=\"More than\" ".(($selOpt=="More than" && !$not)?"selected":"").">"."More than"."</option>";
		$options.="<OPTION VALUE=\"Less than\" ".(($selOpt=="Less than" && !$not)?"selected":"").">"."Less than"."</option>";
		$options.="<OPTION VALUE=\"Empty\" ".(($selOpt=="Empty" && !$not)?"selected":"").">"."Empty"."</option>";
		return $options;
	}
	
	
	function getCtrlSearchTypeOptions($fName, $selOpt, $not) 
	{
		if (strlen($fName))
		{
			$fType = GetEditFormat($fName, $this->tName);	
		}
		else 
		{
			$fType = EDIT_FORMAT_TEXT_FIELD;
		}
			
		$options="";
		
		if ($fType == EDIT_FORMAT_DATE || $fType == EDIT_FORMAT_TIME)
		{
			$options.="<OPTION VALUE=\"Equals\" ".(($selOpt=="Equals" && !$not)?"selected":"").">"."Equals"."</option>";
			$options.="<OPTION VALUE=\"More than\" ".(($selOpt=="More than" && !$not)?"selected":"").">"."More than"."</option>";
			$options.="<OPTION VALUE=\"Less than\" ".(($selOpt=="Less than" && !$not)?"selected":"").">"."Less than"."</option>";
			$options.="<OPTION VALUE=\"Between\" ".(($selOpt=="Between" && !$not)?"selected":"").">"."Between"."</option>";
			$options.="<OPTION VALUE=\"Empty\" ".(($selOpt=="Empty" && !$not)?"selected":"").">"."Empty"."</option>";
		}
		elseif ($fType == EDIT_FORMAT_LOOKUP_WIZARD)
		{
			if (Multiselect($fName, $this->tName)){
				$options.="<OPTION VALUE=\"Contains\" ".(($selOpt=="Contains" && !$not)?"selected":"").">"."Contains"."</option>";	
			}else{
				$options.="<OPTION VALUE=\"Equals\" ".(($selOpt=="Equals" && !$not)?"selected":"").">"."Equals"."</option>";	
			}
		}
		elseif ($fType == EDIT_FORMAT_TEXT_FIELD || $fType == EDIT_FORMAT_TEXT_AREA || $fType == EDIT_FORMAT_PASSWORD 
					|| $fType == EDIT_FORMAT_HIDDEN || $fType == EDIT_FORMAT_READONLY)
		{
			$options.="<OPTION VALUE=\"Contains\" ".(($selOpt=="Contains" && !$not)?"selected":"").">"."Contains"."</option>";
			$options.="<OPTION VALUE=\"Equals\" ".(($selOpt=="Equals" && !$not)?"selected":"").">"."Equals"."</option>";
			$options.="<OPTION VALUE=\"Starts with\" ".(($selOpt=="Starts with" && !$not)?"selected":"").">"."Starts with"."</option>";
			$options.="<OPTION VALUE=\"More than\" ".(($selOpt=="More than" && !$not)?"selected":"").">"."More than"."</option>";
			$options.="<OPTION VALUE=\"Less than\" ".(($selOpt=="Less than" && !$not)?"selected":"").">"."Less than"."</option>";
			$options.="<OPTION VALUE=\"Between\" ".(($selOpt=="Between" && !$not)?"selected":"").">"."Between"."</option>";
			$options.="<OPTION VALUE=\"Empty\" ".(($selOpt=="Empty" && !$not)?"selected":"").">"."Empty"."</option>";
		}
		else
			$options.="<OPTION VALUE=\"Equals\" ".(($selOpt=="Equals" && !$not)?"selected":"").">"."Equals"."</option>";
		
		return $options;
	}
	
	function getCtrlSearchType($fName, $recId, $fieldNum=0, $selOpt, $not, $renderHidden=false) 
	{	
		// on change event handlers, that shows second ctrl
		$selectOnChange = "
			var ctrl = Runner.controls.ControlManager.getAt('".htmlspecialchars(jsreplace($this->tName))."', ".$recId.", '".htmlspecialchars(jsreplace($fName))."', ".($fieldNum+1).");
			if (!ctrl){
				return false;
			}
			var ctrlsSpan = ctrl.spanContElem;
			var parentCont = ctrlsSpan.parent();	
			
			if (this.value=='Between' || this.value=='NOT Between'){
				ctrl.show();
			}else{
				ctrl.hide();
			};
		";
		
		$searchtype = '<SELECT id="'.$this->getSearchOptionId($fName, $recId).'" NAME="'.$this->getSearchOptionId($fName, $recId).'" SIZE=1 onchange="'.$selectOnChange.'" '.($renderHidden || !$this->getSrchPanelAttrs['ctrlTypeComboStatus'] ? 'style="display: none;"' : '').'>';
		$searchtype .= $this->getCtrlSearchTypeOptions($fName, $selOpt, $not);
		$searchtype .= "</SELECT>";
		
		return $searchtype;		
	}
	
	function getSearchOptionId($fName, $recId) {
		return 'srchOpt_'.$recId.'_'.GoodFieldName($fName);
	}
	
	
	function getNotBox($fName, $recId, $not){	
		$notbox = 'id="not_'.$recId.'_'.GoodFieldName($fName).'"';
		if($not)
			$notbox .=" checked";
			
		return $notbox;
	}
	
	
	function  getDelButtonHtml($fName, $recId)
	{		
		$html = '<img style="visibility: hidden;" id = "'.$this->getDelButtonId($fName, $recId).'" class="searchPanelButton" src="images/search/closeRed.gif" alt="'."Delete control".'"  onclick="searchController'.$this->id.'.delCtrl(\''.htmlspecialchars(jsreplace($fName)).'\', '.$recId.');">';
		return $html;
	}
	
	function getDelButtonId($fName, $recId) {
		return 'delCtrlButt_'.$recId.'_'.GoodFieldName($fName);
	}
	
	function getSearchRadio()
	{	
		$resArr = array();
		// search panel radio button assign
		$resArr['all_checkbox_label'] = array(0=>'', 1=>'');
		$resArr['any_checkbox_label'] = array(0=>'', 1=>'');		
		
		if(isEnableSection508())
		{
			$resArr['all_checkbox_label'] = array(0=>"<label for=\"all_checkbox\">", 1=>"</label>");
			$resArr['any_checkbox_label'] = array(0=>"<label for=\"any_checkbox\">", 1=>"</label>");			
		}
		
		$id508l="id=\"all_checkbox\" ";
		$id508n="id=\"any_checkbox\" ";
		
		$resArr['all_checkbox']	= $id508l;
		$resArr['any_checkbox']	= $id508n;
		
		$resArr['all_checkbox'] .= "value=\"and\" ";
		$resArr['any_checkbox'] .= "value=\"or\" ";
		
		
		if(isset($this->globSrchParams['srchTypeRadio']) && $this->globSrchParams['srchTypeRadio']=="or")
		{
			$resArr['any_checkbox'] .=" checked";
		}
		else
		{
			$resArr['all_checkbox'] .=" checked";
		}
		
		
		return $resArr;
	}
	
	function addSearchCtrlJSEvent($fName)
	{
		return 'onclick="searchController'.$this->id.'.addFilter(\''.htmlspecialchars(jsreplace($fName)).'\'); searchController'.$this->id.'.hideCtrlChooseMenu();"';
	}
	
	function getFilterDivId($recId, $fName)
	{
		return 'filter_'.$recId.'_'.GoodFieldName($fName);
	}
	
	function getCtrlComboContId($recId, $fName)
	{
		return 'searchType_'.$recId.'_'.GoodFieldName($fName);
	}
	
	
	function buildSearchCtrlBlockArr($recId, $fName, $ctrlInd, $opt, $not, $isChached, $val1, $val2)
	{		
		$srchCtrlBlock = array();
		$srchCtrlBlock['searchcontrol'] = $this->getCtrlParamsArr($fName, $recId, $ctrlInd, $val1, false, $isChached);	
		// create second control, if need it
		$renderHidden = strtolower($opt)!='between' && strtolower($opt)!='not between';
		$srchCtrlBlock['searchcontrol1'] = $this->getSecCtrlParamsArr($fName, $recId, $ctrlInd, $val2, $renderHidden, $isChached);	
		
		
		$srchCtrlBlock['secCtrlCont_attrs'] = '';//(strtolower($opt)!='between' && strtolower($opt)!='not between' ? 'style="display: none;"' : '');		
		// del button
		$srchCtrlBlock['delCtrlButt'] = $this->getDelButtonHtml($fName, $recId);		
		// one control with options container attr
		$filterDivId = $this->getFilterDivId($recId, $fName);
		$srchCtrlBlock['filterDiv_attrs'] = ($isChached ? $this->dispNoneStyle : '').' id="'.$filterDivId.'" ';
		$srchCtrlBlock['fName'] = $fName;
		// combo with attrs
		$srchCtrlBlock['searchtype'] = $this->getCtrlSearchType($fName, $recId, $ctrlInd, $opt, $not);		
		$srchCtrlBlock['srchTypeCont_attrs'] = 'id="'.$this->getCtrlComboContId($recId, $fName).'"';
		$srchCtrlBlock['srchTypeCont_attrs'] .= ($this->getSrchPanelAttrs['ctrlTypeComboStatus'] ? '' : 'style="display: none;"');
		// checkbox attrs
		$srchCtrlBlock['notbox'] = $this->getNotBox($fName, $recId, $not);
		$srchCtrlBlock['fLabel'] = GetFieldLabel(GoodFieldName($this->tName),GoodFieldName($fName));
		return $srchCtrlBlock;
	}
	
	
	
	/**
	 * Return name of parent field
	 *
	 * @param string $fName
	 * @return string
	 */
	function getParentCtrlName($fName) {
		return CategoryControl($fName, $this->tName); 
	}
	/**
	 * Return parent value for dependent ctrl
	 * Used value of first parent field
	 *
	 * @param string $fName
	 * @return string
	 */
	function getParentVal($fName)
	{		
		$categoryFieldParams = $this->searchClauseObj->getSearchCtrlParams($this->getParentCtrlName($fName));
		if (count($categoryFieldParams))
			return $categoryFieldParams[0]['value1'];
		else
			return false;	
	}
	/**
	 * Return JS for preload dependent ctrl
	 *
	 * @param string $fName
	 * @param string $fval
	 * @param int $recId
	 * @return string
	 */
	function createPreloadJS($fName, $fval, $recId)
	{
		// if no parent in project settings
		if (!UseCategory($fName, $this->tName)){
			return '';
		}
		// if parent exist in settings
		$parentFieldName = CategoryControl($fName, $this->tName);
		$parentVal = $this->getParentVal($fName);
		$doFilter = true;
		// if no filter f parent doesn't exist or it's value is empty
		if ($parentVal===false || $parentVal===''){
			$doFilter = false;
		}
		
		$output = loadSelectContent($fName, $parentVal, $doFilter, $fval);
				
		$txt = ""; 
		foreach( $output as $value ) 
			$txt .= jsreplace($value)."\\n";
			
		return "var Cntrl = Runner.controls.ControlManager.getAt('".jsreplace($this->tName)."', ".$recId.", '".jsreplace($fName)."');	
					Cntrl.preload('".$txt."','".jsreplace($fval)."');";	
			
	}
	/**
	 * Create searchSuggest js if ctrl can use suggest
	 *
	 * @param string $fName
	 * @param int $recId
	 * @param int $ctrlInd
	 * @return string
	 */
	function createSearchSuggestJS($fName, $recId, $ctrlInd=0)
	{	
		if (!$this->isAddSuggestEvent($fName))
		{
			return '';
		}
		$js = "
			// get all search ctrls
			var ctrl = Runner.controls.ControlManager.getAt('".jsreplace($this->tName)."', ".$recId.", '".jsreplace($fName)."', ".$ctrlInd.");
			ctrl.on('keyup', function(e, argsArr){
				var srchTypeComboId = searchController".$this->id.".getComboId('".jsreplace($fName)."', ".$recId.");
				var srchTypeCombo = $('#'+srchTypeComboId);				
				var suggestUrl = 'searchsuggest.php?table=".GetTableData($this->tName, ".shortTableName", '')."';
				return searchSuggest_new(e, this, srchTypeCombo, 'advanced', suggestUrl);
			});
			
			ctrl.on('keydown', function(e, argsArr){
				return listenEvent(e, this.valueElem.get(0), searchController".$this->id.");
			});
		";
		if ($this->isNeedSecondCtrl($fName) && $ctrlInd === 0)
		{
			$js .= $this->createSearchSuggestJS($fName, $recId, 1);
		}
		return $js;
	}
	
	function fNamesJSArr($fNamesArr) 
	{
		$fNamesJsArr = "";
		for($j = 0; $j < count($fNamesArr); $j++) 
		{
			$fNamesJsArr.= "'".jsreplace($fNamesArr[$j])."',";
		}
		return substr($fNamesJsArr, 0, - 1);
	}
	
	function createNoSuggestJs() 
	{
		return "
			onkeydown=\"
				e=event; 
				if(!e){
					e = window.event;
				} 
				if(e.keyCode != 13){ 
					return true; 
				}
				e.cancel = true; 
				searchController".$this->id.".submitSearch(); 
				return false;
			\"
		";
	}
	
	function nonCtrlSearchSuggestJS() 
	{
		return "onkeydown=\"return listenEvent(event, this, searchController".$this->id.");\" onkeyup=\"searchSuggest(event,this,'ordinary','searchsuggest.php?table=".GetTableURL($this->tName)."', ".$this->id.");\"";
	}
	
}

?>