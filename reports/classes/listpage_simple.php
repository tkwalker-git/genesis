<?php
/**
 * Class for list page with mode simple
 *
 */
class ListPage_Simple extends ListPage 
{
	/**
	 * Constructor, set initial params
	 *
	 * @param array $params
	 */	
	function ListPage_Simple(&$params) 
	{
		// call parent constructor
		parent::ListPage ($params);	
	}
	/**
	 * Add common assign for simple mode on list page
	 */	
	function commonAssign() 
	{
		parent::commonAssign();
		
		//search permissions
		$searchPermis = $this->permis[$this->tName]['search'];
		//export permissions
		$exportPermis = $this->permis[$this->tName]['export'];
		
		// adds style displat none to hiderecord controls, edit selected, delete selected, export selected and print selected if found 0 recs
		$this->xt->assign("details_block", $searchPermis && $this->rowsFound );
		$this->xt->assign("recordspp_block", $searchPermis && $this->rowsFound );
		if($this->listAjax)
			$this->xt->assign("recordspp_attrs", "onchange=\"".$this->getLocation($this->shortTableName."_list.php?mode=ajax&pagesize='+this.options[this.selectedIndex].value+'")."\"" );
		else
			$this->xt->assign("recordspp_attrs", "onchange=\"javascript: document.location='" . $this->shortTableName . "_list.php?pagesize='+this.options[this.selectedIndex].value;\"" );
		$this->xt->assign("pages_block", $searchPermis && $this->rowsFound );
		$this->xt->assign("shiftstyle_block", true);
		$this->xt->assign("left_block", true);
		$this->xt->assign("toplinks_block", true);
		
		//export selected link and attr
		$this->xt->assign("exportselected_link", $exportPermis);
		$this->xt->assign("exportselectedlink_span", $this->buttonShowHideStyle());
		$this->xt->assign("exportselectedlink_attrs", $this->getPrintExportLinkAttrs('export'));
		
		// print links and attrs
		$this->xt->assign("print_link", $exportPermis);
		$this->xt->assign("printlink_attrs", 
						  "href='".$this->shortTableName."_print.php' 
						   onclick=\"window.open('".$this->shortTableName."_print.php','wPrint');return false;\"");
		
		//print selected link and attr
		$this->xt->assign("printselected_link", $exportPermis);
		$this->xt->assign("printselectedlink_attrs", $this->getPrintExportLinkAttrs('print'));
		$this->xt->assign("printselectedlink_span", $this->buttonShowHideStyle());
		
		//print all link and attr
		$this->xt->assign("printall_link", $exportPermis);
		$this->xt->assign("printalllink_attrs", 
						  "href='".$this->shortTableName."_print.php?all=1' 
						   onclick=\"window.open('".$this->shortTableName."_print.php?all=1','wPrint');return false;\"");
		
		//export link and attr
		$this->xt->assign("export_link", $exportPermis);
		$this->xt->assign("exportlink_attrs", 
						  "href='".$this->shortTableName."_export.php' 
						   onclick=\"window.open('".$this->shortTableName."_export.php','wExport');return false;\"");
		
		//add link and attr
		$this->xt->assign("add_link", $this->permis[$this->tName]['add']);
		$this->xt->assign("addlink_attrs", "href='".$this->shortTableName."#_add.php' onClick=\"window.location.href='".$this->shortTableName."_add.php'\"");
		
		//select all link and attr
		$this->selectAllLinkAttrs();	
		
		//edit selected link and attr	
		$this->editSelectedLinkAttrs();		
		
		//save all link, attr, span	
		$this->saveAllLinkAttrs();
		
		//cansel all link, attr, span	
		$this->cancelAllLinkAttrs();
		
		$this->addAssignForGrid();
			
		if($this->listAjax)
			$this->xt->assign_section ("grid_block", ($this->isDispGrid() ? $this->getAdminFormHTML() : ""), "</form>");
		elseif($this->isDispGrid())
			$this->xt->assign_section ("grid_block", $this->getAdminFormHTML(), "</form>");
		
		$this->xt->assign('menu_block', $this->isCreateMenu());
		$this->xt->assign("languages_block",true);
	}
	/**
	 * Simple assign for grid block
	 */
	function addAssignForGrid()
	{
		parent::addAssignForGrid();
		
		//edit permissions
		$editPermis = $this->permis[$this->tName]['edit'];
		//add permissions
		$addPermis = $this->permis[$this->tName]['add'];
		//search permissions
		$searchPermis = $this->permis[$this->tName]['search'];
		
		//checkbox column				
		$this->checkboxColumnAttrs();
		
		//edit column
		$this->xt->assign("edit_column", $editPermis);
		$this->xt->assign("edit_headercolumn", $editPermis);
		$this->xt->assign("edit_footercolumn", $editPermis);
		
		//inline edit column	
		$this->xt->assign("inlineedit_column", $editPermis);
		$this->xt->assign("inlineedit_headercolumn", $editPermis);
		$this->xt->assign("inlineedit_footercolumn", $editPermis);
		
		//copy link
		$this->xt->assign("copy_column", $addPermis);
				
		//view column	
		$this->xt->assign("view_column", $searchPermis);
		
		//for list icons instead of list links
		$this->assignListIconsColumn($editPermis, $addPermis, $searchPermis);
		
		for($i = 0; $i < count($this->allDetailsTablesArr); $i ++) 
		{
			$permis =($this->permis[$this->allDetailsTablesArr[$i]['dDataSourceTable']]['add'] || $this->permis[$this->allDetailsTablesArr[$i]['dDataSourceTable']]['search']);
			if($permis)
			{
				$this->xt->assign(GoodFieldName($this->tName)."_dtable_column", $permis);
				break;
			}
		}
			
		//delete link and attr
		$this->deleteSelectedLink();
	}	
	
	/**
	 * Get Admin form with hidden fields
	 *
	 */
	function getAdminFormHTML()
	{
		return '<form method="POST" action="'.$this->shortTableName.'_list.php" name="frmAdmin'.$this->id.'" id="frmAdmin'.$this->id.'" '.$this->getFormTargetHTML().'>
				<input type="hidden" id="a'.$this->id.'" name="a" value="delete">'.$this->getFormInputsHTML().($this->is508 == true ? '<a name="skipdata"></a>' : '');
	}
	
	/**
	 * Add common html code for simple mode on list page
	 */	
	function addCommonHtml() 
	{
		$this->body ["begin"] .= "<script type=\"text/javascript\" src=\"include/jquery.js\"></script>";
		$this->body ["begin"] .= "<script type=\"text/javascript\" src=\"include/jsfunctions.js\"></script>\r\n";
		if ($this->debugJSMode === true)
		{
			$this->body ["begin"] .= "<script type=\"text/javascript\" src=\"include/runnerJS/Runner.js\"></script>";
			$this->body ["begin"] .= "<script type=\"text/javascript\" src=\"include/runnerJS/Util.js\"></script>";
		
		}
		else
		{
			$this->body ["begin"] .= "<script type=\"text/javascript\" src=\"include/runnerJS/RunnerBase.js\"></script>";
		}
					
		if ($this->isDisplayLoading)
			$this->body["begin"] .= "<script type=\"text/javascript\">runLoading(".$this->id.",document.body,0);</script>"; 
		
		$this->AddJSFile("include/customlabels");
		
		//add parent common html code
		parent::addCommonHtml();
		
		if($this->permis[$this->tName]['search'])
			$this->body["begin"].= $this->getSeachFormHTML();
		
		// assign body end
		$this->body['end'] = array();
		$this->body['end']["method"] = "assignBodyEnd";		
		$this->body['end']["object"] = &$this;	
		if($this->isDisplayLoading) 
			$this->getStopLoading();
	}
	
	/**
	 * Get search form target html for mode ajax on list page
	 *
	 * @return string
	 */
	function getFormTargetHTML()
	{
		$target = '';
		if($this->listAjax)
			$target = 'target="flyframe'.$this->id.'"';
		return $target;
	}
	/**
	 * Get search form hidden inputs html  for mode ajax on list page
	 *
	 * @return string
	 */
	function getFormInputsHTML() 
	{
		$html = '';
		if($this->listAjax)
			$html = '<input type="Hidden" name="mode" value="ajax">';
		return $html;
	}
	/**
	 * Add run loading js code for mode ajax on list page
	 *
	 * @return string
	 */
	function addRunLoading()
	{
		$run = '';
		if($this->listAjax)
			$run = "runLoading(".$this->id.",getParentTableObj(".$this->id."),".LIST_AJAX.");";
		return $run;
	}
	
	/**
	 * Add common javascript files and code
	 */
	function addCommonJs() 
	{
		parent::addCommonJs();
		
		if($this->listAjax)
		{
			$this->AddJSFile("include/ajaxreboottable");
			//If use ajax reboot for main table on list page
			$this->AddJsCode("\nwindow.reBootTable".$this->id." = new ajaxRebootTable(
							{'pageId':".$this->id.",
							 'mode':'list_ajax'
							});
							reBootTable".$this->id.".createAjaxIframe();");
			$this->body['begin'] .= $this->addLoadedContentDiv(1);				
		}	
	}
	
	/**
	 * Add javascript code for grid in simple mode on list page
	 */
	function addJsForGrid()
	{
		parent::addJsForGrid();
		
		if(!$this->isResizeColumns && $this->isUseInlineAdd && $this->permis[$this->tName]['add'] && !$this->numRowsFromSQLFromSQL) 
			$this->addJSCode ("$('[@name=maintable]').hide();" );	
		
		$this->addJSCode("\nwindow.MaxWindowPage=".$this->maxPages.";");
		$this->AddJSCode("\ns508pagination(".$this->id.");");
		
		if($this->is508)
		{
			$this->AddJSCode("\ns508jumpto(".$this->id.");");
			if($this->isUseInlineEdit && $this->permis[$this->tName]['edit'])
			{
				$this->AddJSCode("\ns508inlineEdit(".$this->id.");");
			}
		}
	}
	
	function buildSearchPanel($xtVarName) 
	{
		$params = array();
		$params['pageObj'] = &$this;
		$params['globSearchFields'] = $this->globSearchFields;
		$params['panelSearchFields'] = $this->panelSearchFields;
		$this->searchPanel = new SearchPanelSimple($params);
		$this->searchPanel->buildSearchPanel($xtVarName);
	}	
}


?>