<?php
/**
 * Class for list page with mode ajax
 *
 */
class ListPage_Ajax extends ListPage_Simple 
{
	/**
	 * Constructor, set initial params
	 *
	 * @param array $params
	 */	
	function ListPage_Ajax(&$params) 
	{
		// call parent constructor
		parent::ListPage ($params);	
	}
	/**
	 * Add common assign for ajax mode on list page
	 */	
	function commonAssign() 
	{
		$this->xt->assign("id", $this->id);
		//search permissions
		$searchPermis = $this->permis[$this->tName]['search'];
		$this->xt->assign("details_block", $searchPermis && $this->rowsFound );
		$this->xt->assign("pages_block", $searchPermis && $this->rowsFound );
		$this->xt->assignbyref("body", $this->body);
		//$this->xt->assign("left_block", true);		
		
		$this->addAssignForGrid();
						
		if ($this->isDispGrid())
			$this->xt->assign_section("grid_block", '', '');
	}
	
	/**
	 * Add common html code for ajax mode on list page
	 */	
	function addCommonHtml() 
	{
		return true;
	}
	
	/**
	 * Add common javascript code for ajax mode on list page
	 */	
	function addCommonJs()
	{
		$this->addJsForGrid();
	}
	
	/**
      * Add javascript pagination code for current mode
      *
	  */
	function addJSPagination()
	{
		$this->addJSCode("\nwindow.GotoPage".$this->id." = function (nPageNumber){".$this->getLocation($this->shortTableName."_list.php?mode=ajax&goto='+nPageNumber+'",false)."};");
	}
	
	/**
      * Final build page
      *
	  */
	function prepareForBuildPage() 
	{	
		//orderlinkattrs for fields
		$this->orderLinksAttr();
		
		//Sorting fields
		$this->buildOrderParams();
		
		// delete record
		$this->deleteRecords();
		
		// build sql query
		$this->buildSQL();
		
		// build pagination block
		$this->buildPagination();
		
		// seek page must be executed after build pagination
		$this->seekPageInRecSet($this->querySQL);
		
		$this->setGoogleMapsParams($this->listFields);
		
		// fill grid data
		$this->fillGridData();
		
		// add common js code
		$this->addCommonJs();
		
		// add common html code
		$this->addCommonHtml();
		
		// Set common assign
		$this->commonAssign();
	}
	
	/**
      * Show page method
      *
      */
	function showPage()
	{
		$this->BeforeShowList();
		$jscode = $this->PrepareJs();		
		
		echo "<textarea id=data>decli";
		echo htmlspecialchars($jscode);
		echo "</textarea>";
		
		echo "<textarea id=\"html\">";	
		$this->xt->load_template($this->templatefile);
		ob_start();
		$this->xt->display_loaded("details_block");
		$this->xt->display_loaded("pages_block");
		$this->xt->display_loaded("grid_block");
		$this->xt->display_loaded("pagination_block");
		$this->xt->display_loaded("message_block");
		$contents = ob_get_contents();
		ob_end_clean();
		echo htmlspecialchars($contents);
		echo "</textarea>";
	}

}
?>