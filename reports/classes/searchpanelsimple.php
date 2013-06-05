<?php
/**
 * Search panel builder for LIST_SIMPLE mode
 *
 */
class SearchPanelSimple extends SearchPanel {

	var $srchPanelAttrs = array();
	
	var $isDisplaySearchPanel = true;
	
	function SearchPanelSimple(&$params) {
		parent::SearchPanel($params);
		
		$this->isDisplaySearchPanel = GetTableData($this->tName, ".showSearchPanel", false);
	}
	
	function buildSearchPanel($xtVarName) 
	{
		
		parent::buildSearchPanel();		
		
		$this->addPanelFiles();
		
		if ($this->isDisplaySearchPanel)
		{
			// create search panel
			$searchPanel = array();
			$searchPanel["method"] = "DisplaySearchPanel";		
			$searchPanel["object"] = &$this;
			
			$this->srchPanelAttrs = $this->searchClauseObj->getSrchPanelAttrs();
			
			$params = array();
			$searchPanel["params"] = $params;
			$this->pageObj->xt->assignbyref($xtVarName, $searchPanel);
		}		
	}
	
	
	function addPanelFiles()
	{
		
		if ($this->isUseAjaxSuggest)
			$this->pageObj->AddJSFile("include/ajaxsuggest");
			
		$this->pageObj->AddJSFile("include/ui");
		$this->pageObj->AddJSFile("include/ui.core", "ui");
		$this->pageObj->AddJSFile("include/ui.resizable", "include/ui.core");
		$this->pageObj->AddJSFile("include/onthefly");
		
		
		if (GetTableData($this->tName, ".isUseTimeForSearch", true))
		{		
			$this->pageObj->AddJSFile("include/jquery.utils","include/ui");
			$this->pageObj->AddJSFile("include/ui.dropslide","include/jquery.utils");
			$this->pageObj->AddJSFile("include/ui.timepickr","include/ui.dropslide");
			$this->pageObj->AddCSSFile("include/ui.dropslide");
		}
		
		
		if (GetTableData($this->tName, ".isUseCalendarForSearch", true))
			$this->pageObj->AddJSFile("include/calendar");
	}
	
	function searchAssign() {
		
		parent::searchAssign();
		
		$searchGlobalParams = $this->searchClauseObj->getSearchGlobalParams();	
		$searchPanelAttrs = $this->searchClauseObj->getSrchPanelAttrs();
		// show hide window	
		$this->pageObj->xt->assign("showHideSearchWin_attrs", 'align=ABSMIDDLE class="searchPanelButton" title="Floating window" alt="Floating window"  onclick="e=event; searchController'.$this->id.'.toggleSearchWin(e);"');
		$searchOpt_mess = ($searchPanelAttrs['srchOptShowStatus'] ? "Hide search options" : "Show search options");
		//$this->pageObj->xt->assign("showHideSearchPanel_attrs", 'align=ABSMIDDLE class="searchPanelButton" src="images/search/'.($searchPanelAttrs['srchOptShowStatus'] ? 'hideOptions' : 'showOptions').'.gif" title="'.$searchOpt_mess.'" alt="'.$searchOpt_mess.'"  onclick="searchController'.$this->id.'.toggleSearchOptions();"');
		$this->pageObj->xt->assign("showHideSearchPanel_attrs", 'align=ABSMIDDLE class="searchPanelButton" title="'.$searchOpt_mess.'" alt="'.$searchOpt_mess.'"  onclick="searchController'.$this->id.'.toggleSearchOptions();"');
		
		if($this->isUseAjaxSuggest)
			$searchforAttrs = "autocomplete=off ".$this->searchControlBuilder->nonCtrlSearchSuggestJS();
		else
		{
			$searchforAttrs = $this->searchControlBuilder->createNoSuggestJs();
		}
				
				
		$skruglAttrs = 'style="';
		$skruglAttrs .= $searchPanelAttrs['srchOptShowStatus'] ? '"' : 'display: none;"'; 
		$this->pageObj->xt->assignbyref("searchPanelBottomRound_attrs", $skruglAttrs); 
				
		
		if(!$this->searchClauseObj->isUsedSrch())
		{
			$searchforAttrs .= 'style="color: #C0C0C0;"';
			$searchforAttrs .= 'onfocus="if (searchController'.$this->id.'.smplUsed != true){ this.value = \'\'; searchController'.$this->id.'.smplUsed = true; $(this).css(\'color\', \'\'); this.onfocus = function(){};}"';
		}
		$searchforAttrs.= " name=\"ctlSearchFor".$this->id."\" id=\"ctlSearchFor".$this->id."\"";
		
		$valSrchFor = $this->searchClauseObj->isUsedSrch() ? $searchGlobalParams["simpleSrch"] : "search";
		$searchforAttrs.= " value=\"".htmlspecialchars($valSrchFor)."\"";
		$this->pageObj->xt->assignbyref("searchfor_attrs", $searchforAttrs);
		
		$this->pageObj->xt->assign('searchPanelTopButtons', $this->isDisplaySearchPanel);
				
		if (GetTableData($this->tName, ".showSimpleSearchOptions", false))
		{
			$simpleSearchTypeCombo = '<SELECT id="simpleSrchTypeCombo'.$this->id.'" NAME="simpleSrchTypeCombo'.$this->id.'" SIZE=1 >';
			$simpleSearchTypeCombo .= $this->searchControlBuilder->getSimpleSearchTypeCombo($searchGlobalParams["simpleSrchTypeComboOpt"], $searchGlobalParams["simpleSrchTypeComboNot"]) ;
			$simpleSearchTypeCombo .= "</SELECT>";		 
			
			$this->pageObj->xt->assign('simpleSearchTypeCombo', $simpleSearchTypeCombo);
						
			$simpleSearchFieldCombo = '<SELECT id="simpleSrchFieldsCombo'.$this->id.'" NAME="simpleSrchFieldsCombo'.$this->id.'" SIZE=1 >';
			$simpleSearchFieldCombo .= $this->searchControlBuilder->simpleSearchFieldCombo($this->allSearchFields, $searchGlobalParams["simpleSrchFieldsComboOpt"]) ;
			$simpleSearchFieldCombo .= "</SELECT>";
			
			$this->pageObj->xt->assign('simpleSearchFieldCombo', $simpleSearchFieldCombo);	
		}		
	}
	
	/**
	 * Search panel on list template handler
	 *
	 * @param array $params
	 */
	function DisplaySearchPanel(&$params)
	{		
		global $gLoadSearchControls;
		
			
		$dispNoneStyle = 'style="display: none;"';
		$xt = new Xtempl();
		
		$xt->assign('searchPanel', $this->isDisplaySearchPanel);
		
		$xt->assign('id', $this->id);			
		// search panel radio button assign
		$searchRadio = $this->searchControlBuilder->getSearchRadio();
		$xt->assign_section("all_checkbox_label", $searchRadio['all_checkbox_label'][0], $searchRadio['all_checkbox_label'][1]);
		$xt->assign_section("any_checkbox_label", $searchRadio['any_checkbox_label'][0], $searchRadio['any_checkbox_label'][1]);
		$xt->assignbyref("all_checkbox",$searchRadio['all_checkbox']);
		$xt->assignbyref("any_checkbox",$searchRadio['any_checkbox']);
		
			
		$xt->assign("searchbutton_attrs", "onClick=\"javascript: searchController".$this->id.".submitSearch(); ".($this->pageObj->listAjax ? $this->pageObj->addRunLoading() : "")."\"");
		
		
		
		
		
		$showHideOpt_mess = $this->srchPanelAttrs['ctrlTypeComboStatus'] ? "Hide options" : "Show options";
		// show criteries div
		$xt->assign("showHideCtrls_attrs", 'onclick="searchController'.$this->id.'.toggleCtrlChooseMenu();"');
		// show search type opt
		$xt->assign("showHideCtrlsOpt_attrs", ' onclick="searchController'.$this->id.'.toggleCtrlTypeCombo();"');
		// show hide search type opt message
		$xt->assign("showHideOpt_mess", $showHideOpt_mess);
		
		$xt->assign("srchOpt_attrs", 'style="display: none;"');	
		
		// render panel open if it was opened, may be better to show open if there are any stuff in it		
		/*if(!$this->srchPanelAttrs['srchOptShowStatus'])
		{
			$xt->assign("srchOpt_attrs", 'style="display: none;"');	
		}*/
		
		if($this->searchClauseObj->getUsedCtrlsCount()>0)
		{
			$xt->assign("srchCritTopCont_attrs", '');
		}
		else
		{
			$xt->assign("srchCritTopCont_attrs", 'style="display: none;"');
		}
		
		if($this->searchClauseObj->getUsedCtrlsCount()>1)
		{
			$xt->assign("srchCritBottomCont_attrs", '');
		}
		else
		{
			$xt->assign("srchCritBottomCont_attrs", 'style="display: none;"');
		}
		
		if($this->searchClauseObj->getUsedCtrlsCount()>0)
		{
			$xt->assign("bottomSearchButt_attrs", '');
		}
		else
		{
			$xt->assign("bottomSearchButt_attrs", 'style="display: none;"');
		}
		
				
		// string with JS for register block in searchController
		$regBlocksJS = '';
		// code for preload dependent
		$preloadDependentJS = '';	
		// search suggest js code
		$searchSuggestJS = '';
		// array for assign
		$srchCtrlBlocksArr = array();
		
		$recId = $this->pageObj->genId();
		
		// build search controls for each field, first we need to build used controls, because cached must have last index	
		for($j=0;$j<count($this->allSearchFields);$j++)
		{
			$srchFields = $this->searchClauseObj->getSearchCtrlParams($this->allSearchFields[$j]);
			$ctrlInd = 0;
			
			
			$isFieldNeedSecCtrl = $this->searchControlBuilder->isNeedSecondCtrl($this->allSearchFields[$j]);
			// add field that should be always shown on panel
			if (!count($srchFields) && in_array($this->allSearchFields[$j], $this->panelSearchFields))
			{
				$srchFields[] = array('opt'=>'', 'not'=>'', 'value1'=>'', 'value2'=>'');
			}
			
			// build used ctrls
			for($i=0; $i<count($srchFields); $i++)
			{		
				// build used ctrl
				$srchCtrlBlocksArr[] = $this->searchControlBuilder->buildSearchCtrlBlockArr($recId, $this->allSearchFields[$j], $ctrlInd, $srchFields[$i]['opt'], $srchFields[$i]['not'], false, $srchFields[$i]['value1'], $srchFields[$i]['value2']);
				// build used ctrls rows for window table
				$srchCtrlBlocksWinArr[] = $this->searchControlBuilder->buildSearchCtrlWinBlockArr($recId, $this->allSearchFields[$j]);
				// add suggest
				if ($this->isUseAjaxSuggest)
				{
					$searchSuggestJS .= $this->searchControlBuilder->createSearchSuggestJS($this->allSearchFields[$j], $recId);
				}
								

				if ($isFieldNeedSecCtrl) {			
					$ctrlsMap = "[".$ctrlInd.", ".($ctrlInd+1)."]";
					$ctrlInd+=2;
				}else{				
					$ctrlsMap = "[".$ctrlInd."]";
					$ctrlInd++;
				}
				$regBlocksJS .= "searchController".$this->id.".addRegCtrlsBlock('".jsreplace($this->allSearchFields[$j])."', ".$recId.", ".$ctrlsMap.");";
				// get content for preload and create JS code				
				$preloadDependentJS .= $this->searchControlBuilder->createPreloadJS($this->allSearchFields[$j], $srchFields[$i]['value1'], $recId);
				// increment ID
				$recId = $this->pageObj->genId();
				// make 0 for cached ctrls and build cache ctrls
				$ctrlInd = 0;
			}
			
			// add filter button
			$xt->assign("addSearchControl_".GoodFieldName($this->allSearchFields[$j])."_attrs", $this->searchControlBuilder->addSearchCtrlJSEvent($this->allSearchFields[$j]));
			// use this criteria, for create cached ctrls. Because, it can slow page with big amout of ctrls
			if (count($this->allSearchFields) < $gLoadSearchControls)
			{
				// add cached ctrl													
				$srchCtrlBlocksArr[] = $this->searchControlBuilder->buildSearchCtrlBlockArr($recId, $this->allSearchFields[$j], $ctrlInd, '', false, true, '', '');
				// add cached ctrl rows for window table
				$srchCtrlBlocksWinArr[] = $this->searchControlBuilder->buildSearchCtrlWinBlockArr($recId, $this->allSearchFields[$j]);
				
				
				if ($this->isUseAjaxSuggest)
				{
					$searchSuggestJS .= $this->searchControlBuilder->createSearchSuggestJS($this->allSearchFields[$j], $recId);
				}
				
				if ($isFieldNeedSecCtrl) {
					$ctrlsMap = "[".$ctrlInd.", ".($ctrlInd+1)."]";	
					$ctrlInd+=2;
				}else{
					$ctrlsMap = "[".$ctrlInd."]";			
					$ctrlInd++;
				}
				$regBlocksJS .= "window.searchController".$this->id.".addRegCtrlsBlock('".jsreplace($this->allSearchFields[$j])."', ".$recId.", ".$ctrlsMap.");";
				$recId = $this->pageObj->genId();	
			}	
		}
		// assign blocks with ctrls
		$xt->assign_loopsection('searchCtrlBlock', $srchCtrlBlocksArr);	
		$xt->assign_loopsection('searchCtrlBlock_win', $srchCtrlBlocksWinArr);	
		
		AddScript2Postload($searchSuggestJS, $this->pageObj->id);
		
		AddScript2Postload($regBlocksJS, $this->pageObj->id);		
		AddScript2Postload($preloadDependentJS, $this->pageObj->id);
		
		AddScript2Postload("window.searchController".$this->id.".remindPanelState();", $this->pageObj->id);
		// display templ
		$xt->display($this->pageObj->shortTableName."_search_panel.htm");
	}	
}

?>