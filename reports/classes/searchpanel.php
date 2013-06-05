<?php
/**
 * Base class for all search control builders
 *
 */
class SearchPanel {
	/**
	 * strTableName of searchPanel's table
	 *
	 * @var string
	 */		
	var $tName = '';
	var $dispNoneStyle = 'style="display: none;"';
	/**
	 * Object of page for output. Used for call xt methods for current page
	 *
	 * @var object
	 */
	var $pageObj = null;
	/**
	 * Object of searchClause class.
	 *
	 * @var object
	 */
	var $searchClauseObj = null;
	/**
	 * Object of PanelSearchControl class.
	 *
	 * @var object
	 */
	var $searchControlBuilder = null;
	/**
	 * Panel id
	 *
	 * @var int
	 */
	var $id = 1;	
	/**
	 * Array of panel state parametres, such as open|close menu etc.
	 *
	 * @var array
	 */
	var $panelState = array();
	/**
	 * Fields that used for advSearcH and search on panel
	 *
	 * @var array
	 */
	var $globSearchFields = array();
	/**
	 * Fields that used for search on panel, and should be open on every page load
	 *
	 * @var array
	 */
	var $panelSearchFields = array();
	/**
	 * Array of key's fields
	 *
	 * @var array
	 */
	
	var $allSearchFields = array();
	/**
	 * Indicator use suggest or not
	 *
	 * @var bool
	 */
	var $isUseAjaxSuggest = false;
	/**
	 * Permissions for search
	 *
	 * @var bool
	 */
	var $searchPerm = false;
	/**
	 * Constructor, accepts array of parametres, which will be copied to object properties by link
	 *
	 * @param array $params
	 * @return SearchPanel
	 */
	function SearchPanel(&$params)
	{
		// copy properties to object
		RunnerApply($this, $params);
		
		$this->searchClauseObj = &$this->pageObj->searchClauseObj;	
		
		$this->id = $this->pageObj->id;
		$this->tName = $this->pageObj->tName;
		$this->panelState = $this->searchClauseObj->getSrchPanelAttrs();	
		$this->isUseAjaxSuggest = GetTableData($this->tName, ".isUseAjaxSuggest", true);
		
		
		
		$this->searchControlBuilder = new PanelSearchControl($this->id, $this->tName, $this->searchClauseObj, $this->pageObj);	
		
				
		// get search permissions if not passed to constructor
		if (!isset($params['searchPerm'])){
			$this->searchPerm = $this->getSearchPerm();
		}
		// get search fields if not passed to contructor
		if (!isset($params['panelSearchFields']))
		{			
			$this->panelSearchFields = GetTableData($this->tName,".panelSearchFields",array());	
		}
		if (!isset($params['globSearchFields']))
		{			
			$this->globSearchFields = GetTableData($this->tName,".globSearchFields",array());	
		}
		
		$this->allSearchFields = GetTableData($this->tName, '.allSearchFields', array());
	}	
	
	function getSearchPerm($tName = "")
	{
		global $isGroupSecurity;
		
		$tName = $tName ? $tName : $this->tName;
				
		if (!$isGroupSecurity)
		{
			return true;
		}
		else
		{
			
			$strPerm = GetUserPermissions($tName);
			return (strpos($strPerm, "S") !== false);
		}		
	}
	
	/**
	 * Main method, call to build search panel
	 *
	 */
	function buildSearchPanel() 
	{
		$fNamesJsArr = $this->searchControlBuilder->fNamesJSArr($this->allSearchFields);
		$srchPanelAttrs = $this->searchClauseObj->getSrchPanelAttrs();
		if($this->pageObj->mode!=LIST_AJAX)
		{
			$this->pageObj->addJsCode("
				window.searchController".$this->id." = new Runner.search.SearchController({
					id: ".$this->id.",
					tName: '".jsreplace($this->tName)."',
					fNamesArr: [".$fNamesJsArr."],
					shortTName: '".$this->pageObj->shortTableName."',
					usedSrch: ".($this->searchClauseObj->isUsedSrch() ? 'true' : 'false').",
					panelSearchFields: [".$this->searchControlBuilder->fNamesJSArr($this->panelSearchFields)."]
				});
			");
		}
		$this->searchAssign();
	}
	
	
		
	function searchAssign() 
	{
		
		$this->pageObj->xt->assign('searchform', true);
		$this->pageObj->xt->assign("asearch_link", $this->searchPerm);
		$this->pageObj->xt->assign("asearchlink_attrs", "href=\"".$this->pageObj->shortTableName."_search.php\" onclick=\"window.location.href='".$this->pageObj->shortTableName."_search.php';return false;\"");

		if(isEnableSection508() && $this->searchPerm)
		{
			$searchPerm=array();
			$searchPerm["begin"]="<a name=\"skipsearch\"></a>";
		}
		else
			$searchPerm=$this->searchPerm;
		
		$this->pageObj->xt->assign("search_records_block", $searchPerm);
		
		
		$this->pageObj->xt->assign("searchform_text", true);
		$this->pageObj->xt->assign("searchform_search", true);
		
		$this->pageObj->xt->assign("searchform_showall", $this->searchClauseObj->isUsedSrch());	
		
		$srchButtTitle = "Search"; 
	
		$this->pageObj->xt->assign("searchbutton_attrs", "onClick=\"javascript: ".($this->pageObj->listAjax ? $this->pageObj->addRunLoading() : "")." searchController".$this->id.".submitSearch(); \" title=\"".$srchButtTitle.'"');
		$this->pageObj->xt->assign("showallbutton_attrs", "onClick=\"".($this->pageObj->listAjax ? $this->pageObj->addRunLoading() : "")." searchController".$this->id.".showAllSubmit()\"");
		
		
	}
	
	
	
	
	
	
	
	
}

?>