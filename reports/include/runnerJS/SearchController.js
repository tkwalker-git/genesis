/**
 * search panel controller. Used for manage search on the list page
 * for multiple search classes use id param.
 * @class
 * @param {object} cfg
 */
Runner.search.SearchController = Runner.extend(Runner.search.SearchFormWithUI, {
   
    
    
    panelStateExpires: '',
   /**
    * Ajax add filter cache url
    * @type String
    */
    ajaxSearchUrl: "",  
    /**
     * Reusable style display none
     * @type String
     */
    styleDispNoneText: 'display: none;',
    /**
     * Short table name, used for create urls
     */
    shortTName: "",
    /**
     * Override parent contructor
     * Add interaction with server
     * @param {obj} cfg
     */
    constructor: function(cfg){    	
    	//call parent
    	Runner.search.SearchController.superclass.constructor.call(this, cfg);	
    	// set search url, for ajax
        this.ajaxSearchUrl = this.shortTName + '_search.php';
       
        
        Runner.pages.PageManager.addUnloadHn(this.rememberPanelState, this, []);
        
    },
    
    init: function(ctrlsBlocks){
    	Runner.search.SearchController.superclass.init.call(this, ctrlsBlocks);	
    	    	
    	this.remindPanelState();    
    	this.initFastSearch(); 
    	this.initAddLinks();
    	this.initDelButtons();
    },   
    
    initFastSearch: function(){
    	var controller = this;
    	
    	if (!this.usedSrch){
	    	$("#ctlSearchFor"+this.id).bind('focus', function(e){    		
	    		if (controller.smplUsed != true){ 
	    			this.value = ''; 
	    			controller.smplUsed = true; 
	    			$(this).css('color', '');
	    			// purge this listener
	    			$(this).unbind(e);
	    		}
	    	});
    	}else{
    		$("#ctlSearchFor"+this.id).focus();
    	}
    	
    	if (this.useSuggest){
	    	$("#ctlSearchFor"+this.id).bind('keyup', function(e){
	    		searchSuggest(e, this, 'ordinary', 'searchsuggest.php?table='+controller.shortTName, 1);
	    		
	    	});
	    	$("#ctlSearchFor"+this.id).bind('keydown', function(e){
	    		return listenEvent(e, this, controller);    		
	    	});
    	}
    },
    
    initAddLinks: function(){
    	var controller = this;
    	for(var i=0; i<this.fNamesArr.length; i++){
    		$("#addSearchControl_"+Runner.goodFieldName(this.fNamesArr[i])).bind("click", {fName: this.fNamesArr[i]}, function(e){
    			Runner.Event.prototype.stopEvent(e);
    			controller.addFilter(e.data.fName);
    			controller.hideCtrlChooseMenu();
    		});
    	};
    },
    
    initDelButtons: function(){
    	var srchController = this;
    	this.srchCtrlsBlock.bind('click', function(e){
    		Runner.Event.prototype.stopEvent(e);	
			var target = Runner.Event.prototype.getTarget(e);
			if(target && target.nodeName != "IMG" || !$(target).attr("fName")) {
				return false;
			}			
			var fName = $(target).attr("fName"),
				ctrlId = parseInt($(target).attr("ctrlId"));
			for(var i=0; i<srchController.fNamesArr.length;i++){
				if (fName == Runner.goodFieldName(srchController.fNamesArr[i])){
					fName = srchController.fNamesArr[i];
					break;
				}
			}
    		srchController.delCtrl(fName, ctrlId);
    	});
    },
    
    initWinDelButtons: function(){
    	var srchController = this;
    	
    	$(this.win.body).bind('click', function(e){
    		Runner.Event.prototype.stopEvent(e);	
			var target = Runner.Event.prototype.getTarget(e);
			if(target && target.nodeName != "IMG" || !$(target).attr("fName")) {
				return false;
			}			
			var fName = $(target).attr("fName"),
				ctrlId = parseInt($(target).attr("ctrlId"));
			for(var i=0; i<srchController.fNamesArr.length;i++){
				if (fName == Runner.goodFieldName(srchController.fNamesArr[i])){
					fName = srchController.fNamesArr[i];
					break;
				}
			}
    		srchController.delCtrl(fName, ctrlId);
    	});
    },
    
    initButtons: function(){
    	Runner.search.SearchController.superclass.initButtons.call(this);	
    	
    	var searchController = this;
    	
    	$("#showOptPanel"+this.id).bind("click", function(e){
    		searchController.toggleSearchOptions();    		
    	});
    	$("#showSrchWin"+this.id).bind("click", function(e){
    		searchController.toggleSearchWin();    		
    	});
    	$("#searchButton"+this.id).bind("click", function(e){
    		searchController.submitSearch();
    		// add run loading for ajax reboot
    	});
    	
    	
    	$("#showHideSearchType"+this.id).bind("click", function(e){
    		Runner.Event.prototype.stopEvent(e);
    		searchController.toggleCtrlTypeCombo();
    	});
    	$("#showHideControlChooseMenu"+this.id).bind("click", function(e){
    		Runner.Event.prototype.stopEvent(e);
    		searchController.toggleCtrlChooseMenu();
    	}); 
    	
    	
    },
    
    hideShowAll: function(){
    	Runner.search.SearchController.superclass.hideShowAll.call(this);
    	this.smplSrchBox.val("");
    },
    
    /**
     * Get index of last added from cache control. 
     * @param {string} filterName
     * @return {int}
     */    
    getLastAddedInd: function(filterName){
    	// if no map for this field
    	if (!this.ctrlsShowMap[filterName]){
    		return false;
    	}
    	// get last added and not cached ctrls block index
    	var maxInd = 0, beforeMaxInd=false, i=0;
		for(var ind in this.ctrlsShowMap[filterName]){			
			// need to convert to int from string. May be because object property name is string, typeof return string
			ind = parseInt(ind);
			// get max index, it will give last cached
			if (maxInd < ind){
				beforeMaxInd = maxInd;
				maxInd = ind;
			}
			// at first time take maxInd, because 0 may not appear
			if (i===0){
				beforeMaxInd = maxInd;
			}
			i++;
		}
		return beforeMaxInd;
    },
    /**
     * returns last added filter, usefull when add new
     * 
     * @param {string} filterName field name
     * @return {obj} true if success otherwise false
     */
    getLastAdded: function(filterName){
    	var beforeMaxInd = this.getLastAddedInd(filterName);
    	if (!beforeMaxInd){
    		return false;
    	}
    	// get obj
    	var filterObj = $('#'+this.getFilterDivId(filterName, beforeMaxInd, this.srchWinShowStatus));    	
    	if (filterObj.length){
    		return filterObj;
    	}else{
    		return false;
    	}
    },
    
    /**
     * Adds ctrls block HTML to DOM
     * @param {string} fName
     * @param {string} ind
     * @param {object} blockHTML
     */
    addCtrlsHtml: function(fName, ind, blockHTML){
    	this.addPanelHtml(fName, ind, blockHTML);
    	this.addTableHtml(fName, ind);
    	// take div container, or tr
    	var rowCont = $('#'+this.getFilterDivId(fName, ind, this.srchWinShowStatus))
    	// put into cells block html
    	var cells = rowCont.children();
    	$(cells[0]).html(blockHTML.delButt);
    	$(cells[2]).html(blockHTML.comboHtml);
    	$(cells[3]).html(blockHTML.control1);
    	$(cells[4]).html(blockHTML.control2);

  		// execute additional js code
		eval(blockHTML.jsCode);	
    },
    
    addTableHtml: function(fName, ind){
    	// ctrl main container id
    	var newSrchCtrlContId = this.getFilterDivId(fName, ind, true);
    	// add ctrl main container
    	var filterRowHtml = this.createTableRow(newSrchCtrlContId, 'winRow', this.styleDispNoneText, '');
    	this.srchCtrlsBlockWin.append(filterRowHtml);
    	// main container obj
    	var newSrchCtrlCont = $("#"+newSrchCtrlContId);
    	// add del button
    	newSrchCtrlCont.append(this.createTableCell('', 'srchWinCell', '', ''));
    	// add div with field name
    	var fNameCellHtml = this.createTableCell('', 'srchWinCell', '', fName+':&nbsp;');
    	newSrchCtrlCont.append(fNameCellHtml);
    	// combo type container id
    	var comboHtml = this.createTableCell(this.getComboContId(fName, ind, true), 'srchWinCell', (this.ctrlTypeComboStatus ? '' : this.styleDispNoneText), '');    	
    	newSrchCtrlCont.append(comboHtml);
    	// add first control obj with html
    	newSrchCtrlCont.append(this.createTableCell('', 'srchWinCell', '', ''));
    	// add second ctrl obj if exists, or set empty container, for border puposes
    	newSrchCtrlCont.append(this.createTableCell('', 'srchWinCell', '', '')); 
    	
    	return newSrchCtrlCont;
    },
    
    addPanelHtml: function(fName, ind, blockHTML){
    	// ctrl main container id
    	var newSrchCtrlContId = this.getFilterDivId(fName, ind);
    	// add ctrl main container
    	var filterDivHtml = this.createDivCont(newSrchCtrlContId, 'srchPanelRow blockBorder', this.styleDispNoneText, '');
    	this.srchCtrlsBlock.append(filterDivHtml);
    	// main container obj
    	var newSrchCtrlCont = $("#"+newSrchCtrlContId);
    	// add del button
    	newSrchCtrlCont.append(this.createDivCont('', 'srchPanelCell', '', ''));
    	// add div with field name
    	var fNameDivHtml = this.createDivCont('', 'srchPanelCell', '', blockHTML.fLabel+':&nbsp;');
    	newSrchCtrlCont.append(fNameDivHtml);
    	// combo type container id
    	var comboHtml = this.createDivCont(this.getComboContId(fName, ind), 'srchPanelCell srchPanelCell2', (this.ctrlTypeComboStatus ? '' : this.styleDispNoneText), '');    	
    	newSrchCtrlCont.append(comboHtml);
    	// add first control obj with html
    	newSrchCtrlCont.append(this.createDivCont('', 'srchPanelCell srchPanelCell2', '', ''));
    	// add second ctrl obj if exists, or set empty container, for border puposes
    	newSrchCtrlCont.append(this.createDivCont('', 'srchPanelCell srchPanelCell2', '', '')); 
    	
    	return newSrchCtrlCont;
    },
    /**
     * Adds block to map, regs its components and ands HTML
     * @param {} fName
     * @param {} ind
     * @param {} ctrlIndArr
     * @param {} blockHTML
     */
    addRegCtrlsBlock: function(fName, ind, ctrlIndArr, blockHTML){    	
    	//add to DOM
    	blockHTML ? this.addCtrlsHtml(fName, ind, blockHTML) : "";
    	// call parent
    	Runner.search.SearchController.superclass.addRegCtrlsBlock.call(this, fName, ind, ctrlIndArr);
    	// set links for parent and child if lookup ctrl
    	var ctrl = Runner.controls.ControlManager.getAt(this.tName, ind, fName);
    	// if ctrl hidden it's used for cache, than, do not add link
    	if (!ctrl.hidden){
    		//this.setDependences(ctrl, true);	
    		this.setDependences(ctrl);
    	}    	
    	// reg combos    	
    	this.searchTypeCombosArr.push($("#"+this.getComboContId(fName, ind)));
    	// reg td combos
    	this.searchTypeCombosWinArr.push($("#"+this.getComboContId(fName, ind, true)));
    	// reg filter div block
    	this.srchFilterRowArr.push($("#"+this.getFilterDivId(fName, ind)));
    	// reg filter tr row
    	this.srchFilterRowWinArr.push($("#"+this.getFilterDivId(fName, ind, true)));
    	// call crit controller
  		this.toggleCrit(this.getVisibleBlocksCount());	
    },
   
    /**
     * Creates div container html
     * @param {string} id
     * @param {string} cssClass
     * @param {string} style
     * @param {string} innerHtml
     * @return {string}
     */
    createDivCont: function(id, cssClass, style, innerHtml){
    	return '<div class="'+cssClass+'" id="'+id+'" style="'+style+'">'+innerHtml+'</div>';
    },
    
    createTableRow: function(id, cssClass, style, innerHtml){
    	return '<tr class="'+cssClass+'" id="'+id+'" style="'+style+'">'+innerHtml+'</tr>';
    },
    
    createTableCell: function(id, cssClass, style, innerHtml){
    	return '<td class="'+cssClass+'" id="'+id+'" style="'+style+'">'+innerHtml+'</td>';
    },
    
    /**
     * Put block into right place depending on ctrl type. 
     * If parent field name passed, ctrl will be placed bellow parent
     * If no parent passed, ctrl will be placed above last added for this field
     * 
     * @param {string} filterName
     * @param {int} cachedInd
     * @param {string} parentFieldName
     */
    putCachedBlock: function(filterName, cachedInd, parentFieldName){
    	// get control from cache
        var cachedRow = $("#"+this.getFilterDivId(filterName, cachedInd, this.srchWinShowStatus));        
    	// move cached div to top, insert it after control choose menu
        var lastAdded = this.getLastAdded(filterName);
        // if use parent
        if (parentFieldName && this.getLastAdded(parentFieldName)){
        	cachedRow.insertAfter(this.getLastAdded(parentFieldName));
        }else if(lastAdded){
        	cachedRow.insertBefore(lastAdded);
        }else{
        	// if no parent, add to window
        	if (this.srchWinShowStatus){
        		this.srchCtrlsBlockWin.prepend(cachedRow);
        	// or to panel container
        	}else{
        		this.srchCtrlsBlock.prepend(cachedRow);
        	}
        	
        }
        // show row with controls
    	cachedRow.show();	
        // make window height bigger
        /*if (this.srchWinShowStatus){
        	this.recalcWindowDim();	
        }*/        
    },
    
    
    createLoadingBox: function(filterName){
    	var loadingTxt = '&nbsp;&nbsp;' + filterName + ':&nbsp;loading&nbsp;...&nbsp;';
    	// add div for panel mode
    	if (!this.srchWinShowStatus){
	    	var loadDiv = document.createElement('DIV');	    	
	    	$(loadDiv).addClass('blockBorderHovered').html(loadingTxt);
	    	return loadDiv;
	    // add tr for win mode
    	}else{
    		var loadTr = document.createElement('TR');
    		var loadTd = document.createElement('TD');
    		$(loadTd).attr('colspan', '4').addClass('cellBorderRightHovered').addClass('cellBorderLeftHovered').addClass('cellBorderCenterHovered').html(loadingTxt);    		
    		$(loadTr).addClass('winRow').append(loadTd);
	    	return loadTr;	
    	}   
    },
    
    putLoadingBox: function(loadBox, filterName){
    	// move cached div to top, insert it after control choose menu
        var lastAdded = this.getLastAdded(filterName);
        
        if(lastAdded){
        	$(loadBox).insertBefore(lastAdded);
        }else{
        	// if no parent, add to window
        	if (this.srchWinShowStatus){
        		this.srchCtrlsBlockWin.append($(loadBox));
        	// or to panel container
        	}else{
        		this.srchCtrlsBlock.append($(loadBox));
        	}
        	
        }
    },
    /**
     * Set dependent and parent links to ctrls. 
     * If passed triggerReload, will invoke event of parent ctrl, to reload dependent ctrls
     * 
     * @param {obj} ctrl dependent control
     * @param {string} parentFieldName field name of parent ctrl
     * @param {Boolean} triggerReload pass true to reload dependent ctrls
     * @return {Boolean} true if success otherwise false
     */
    setDependences: function(ctrl, triggerReload){
    	
    	if (!ctrl.isLookupWizard){
    		return false;
    	}
    	
		if(!ctrl.parentFieldName)
			return false;
		
    	if (!ctrl.parentFieldName || !this.ctrlsShowMap[ctrl.parentFieldName]){
    		ctrl.reload();
    		return false;
    	}
    	// get parent index
    	var parentInd = this.getLastAddedInd(ctrl.parentFieldName);
    	if (!this.ctrlsShowMap[ctrl.parentFieldName][parentInd]){
    		return false;
    	}
    	// get parent ctrl
		var parentCtrl = Runner.controls.ControlManager.getAt(this.tName, parentInd, ctrl.parentFieldName, this.ctrlsShowMap[ctrl.parentFieldName][parentInd][0]);
				
		// add link to child
		if (parentCtrl.showStatus && parentCtrl.isLookupWizard){
			ctrl.setParentCtrl(parentCtrl);		
			// add to dependent array
			parentCtrl.addDependentCtrls([ctrl]);
			// reload all children
			if (triggerReload===true){
				parentCtrl.fireEvent('change');
			}else{
				var preloadData = Runner.pages.PageSettings.getFieldData(ctrl.table, ctrl.fieldName, "preload", {vals: [], fVal: false});
				ctrl.preload(preloadData.vals, preloadData.fVal);
			}
		}else{
			ctrl.reload();
		}
		return true;		
    },
    
    getShownFilterNames: function(){
    	var fNamesArr = [];
    	
    	for(var fName in this.ctrlsShowMap){
    		var cachedInd = 0;
    		for(var ind in this.ctrlsShowMap[fName]){
				// need to convert to int from string. May be because object property name is string, typeof return string
				ind = parseInt(ind);
				
				if($("#"+this.getFilterDivId(fName, ind, this.srchWinShowStatus)).css('display') != 'none'){
					fNamesArr.push(fName);
				}
			} 
    	}
    	
    	return fNamesArr;
    },
    
    showCached: function(filterName){
    	// no cache
    	if (!this.ctrlsShowMap[filterName]){
    		return false;
    	}
    	// index of div, that cached and we need to show it
    	var cachedInd = 0;
		for(var ind in this.ctrlsShowMap[filterName]){
			// need to convert to int from string. May be because object property name is string, typeof return string
			ind = parseInt(ind);
			// get max index, it will give last cached
			cachedInd = cachedInd < ind ? ind : cachedInd;
		}      		
		
		// no cached ctrls, only already shown
		if($("#"+this.getFilterDivId(filterName, cachedInd, this.srchWinShowStatus)).css('display') != 'none'){
			return false;
		}
		
		// index of last cached ctrl for this field
    	var cachedCtrlIndArr = this.ctrlsShowMap[filterName][cachedInd];    
        //------------------------------------------------------------------------------------------
        // process controls
        var objIndForCM, parentFieldName, parentCtrl = null, parentInd = false, ctrl1;
    	// scan each object
		for(var i=0;i<cachedCtrlIndArr.length;i++){
        	// index of object that stored in CM
        	objIndForCM = cachedCtrlIndArr[i];
        	// get ctrl
        	var ctrlFromCache = Runner.controls.ControlManager.getAt(this.tName, cachedInd, filterName, objIndForCM);
        	// save link to first ctrl, at the end use it to set focus on it
        	if (i===0){
        		ctrl1 = ctrlFromCache;
        		// show ctrl
        		ctrl1.show();
        	}        	
        	// get parentFieldName for lookup ctrls and add dependeces to lookup ctrls
        	parentFieldName = ctrlFromCache.parentFieldName;
        	// set dependeces between child and parent if these links could be
    		this.setDependences(ctrlFromCache, true);
        	// clear javascript, to prevent it executing second time
        	ctrlFromCache.spanContElem.find('script').remove();
        }        
        //------------------------------------------------------------------------------------------
        // place ctrl depend on it's type: lookup or simple
        this.putCachedBlock(filterName, cachedInd, parentFieldName);        
        // show type combo, if it shown in others ctrl
        if (this.ctrlTypeComboStatus){
        	$("#"+this.getComboContId(filterName, cachedInd)).show();	
        }
        // set focus to added ctrl, turned off in window mode, because it cause bad visual effects in bottom control in window mode
        if (!this.srchWinShowStatus){
        	ctrl1.setFocus();
        }   
        return true;
    },
    /**
     * Adds filter to panel or window, and loads another one for cache
     * @param {string} filterName
     */
    addFilter: function(filterName) {
    	var isShown = this.showCached(filterName);  
    	if (!isShown){
    		var loadBox = this.createLoadingBox(filterName);
    		this.putLoadingBox(loadBox, filterName);
    	}else{
    		this.ctrlTypeComboStatus ? this.showCtrlTypeCombo() : this.hideCtrlTypeCombo();
    	}
            	    	    	
        // ajax params
        var ajaxParams = {
            searchControllerId: this.id,
            rndval: Math.random(),
            mode: "inlineLoadCtrl",
            ctrlField: myEncode(filterName),
            id: Runner.genId(),
            isNeedSettings: !Runner.pages.PageSettings.checkSettings(this.tName, filterName)
        };
        
        // create var for ajax handler closure
        var controller = this;
        // ajax query and callback func 
        $.getJSON(this.ajaxSearchUrl, ajaxParams, function(ctrlJSON, queryStatus){
        	// register new ctrl block        	
        	controller.addRegCtrlsBlock(filterName, ctrlJSON.divInd, (ctrlJSON.control2 ? [0, 1] : [0]), ctrlJSON);
        	
        	if (!Runner.pages.PageSettings.checkSettings(controller.tName, filterName)){   
        		Runner.pages.PageSettings.addSettings(controller.tName, ctrlsJSON.settings);      		
        	}
        	
        	for(var i=0; i<ctrlJSON.ctrlMap.length; i++){
				console.log(ctrlJSON.ctrlMap[i], 'control map in search controller');				
				Runner.controls.ControlFabric(ctrlJSON.ctrlMap[i]);			
			}
        	
        	if (!isShown){
        		controller.showCached(filterName);
        		$(loadBox).remove();
        		controller.toggleCrit(controller.getVisibleBlocksCount());	
        		// because ajax ctrl will shown with delay
        		controller.ctrlTypeComboStatus ? controller.showCtrlTypeCombo() : controller.hideCtrlTypeCombo();
        	}
        });
    },
    /**
     * Deletes controls, its objects add html from DOM
     * @param {string} fName
     * @param {int} ind
     */
    delCtrl: function(fName, ctrlId){    	
    	var objIndForCM;

        // ureg ctrls, loop will delete also second ctrl, if it was created
		for(var i=0;i<this.ctrlsShowMap[fName][ctrlId].length;i++){
        	// index of object that stored in CM
        	objIndForCM = this.ctrlsShowMap[fName][ctrlId][i];
        	// for lookup ctrls, clear links from children and trigger reload them with all values
        	if (objIndForCM.isLookupWizard){
        		objIndForCM.clearChildrenLinks(true);
        	}
        	// delete each object
        	Runner.controls.ControlManager.unregister(this.tName, ctrlId, fName, objIndForCM);
        }        
        
        // remove element from dom
        this.removeComboById(this.getComboContId(fName, ctrlId));
        this.removeFilterById(this.getFilterDivId(fName, ctrlId));
        // set new window dimensions
        /*if (this.srchWinShowStatus){
        	this.recalcWindowDim();	
        }*/        
        // call crit controller
        this.toggleCrit(this.getVisibleBlocksCount());
        // remove from ctrl show map
        delete this.ctrlsShowMap[fName][ctrlId];
    },
    /**
     * Deletes filter by id, removes from array and DOM element
     * @param {string} id
     */
    removeFilterById: function(id){
    	var isUseWinId = (id.lastIndexOf('_win') != -1);
    	id = (isUseWinId ? id.substr(0, id.lastIndexOf('_')) : id);
		// del from panel arr
    	var elemInd = this.srchFilterRowArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.srchFilterRowArr[elemInd].remove();       
    		this.srchFilterRowArr.splice(elemInd, 1);
    	}
    	
    	id += '_win';
    	// del from win arr
    	var elemInd = this.srchFilterRowWinArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.srchFilterRowWinArr[elemInd].remove();        
    		this.srchFilterRowWinArr.splice(elemInd, 1);
    	}
    	
    },
    /**
     * Deletes combo cont by id, removes from array and DOM element
     * @param {string} id
     */
    removeComboById: function(id){
    	var isUseWinId = (id.lastIndexOf('_win') != -1);
    	id = (isUseWinId ? id.substr(0, id.lastIndexOf('_')) : id);
		// del from panel arr
    	var elemInd = this.searchTypeCombosArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.searchTypeCombosArr.splice(elemInd, 1);
    	}
    	id += '_win';
    	// del from win arr
    	var elemInd = this.searchTypeCombosWinArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.searchTypeCombosWinArr.splice(elemInd, 1);
    	}
    },
    /**
     * Get number of visible ctrls blocks
     * @return {int}
     */
    getVisibleBlocksCount: function(){
    	var visCount = 0;
    	// use tr arr if window mode, or div arr if panel
    	var rowArr = (this.srchWinShowStatus ? this.srchFilterRowWinArr : this.srchFilterRowArr);
    	// loop through all filters to get which are visible
    	for(var i=0; i<rowArr.length; i++){    		
    		if (rowArr[i].css('display') != 'none'){
    			visCount++;
    		}
    	}
    	return visCount;
    },
     /**
     * Create and submit form 
     */
    submitSearch: function(){  
    	this.rememberPanelState();
    	
    	// clear any field contains search if it wasn't used
    	if (!this.usedSrch && !this.smplUsed){
    		this.smplSrchBox.val('');
    	}
    	
    	Runner.search.SearchController.superclass.submitSearch.call(this);
    	
    	 	
    },
    /**
     * Resets form ctrls, for panel
     * @return {Boolean}
     */
    resetCtrls: function(){
    	var objIndForCM;
    	
    	for(var fName in this.ctrlsShowMap){
			for(var ind in this.ctrlsShowMap[fName]){
				for(var i=0;i<this.ctrlsShowMap[fName][ind].length;i++){
					// index of object that stored in CM
		        	objIndForCM = this.ctrlsShowMap[fName][ind][i];
		        	// delete each object
		        	var ctrl = Runner.controls.ControlManager.getAt(this.tName, this.id, fName, objIndForCM);
		        	ctrl.reset();
				}
			}
        }        
		return false;
    },
    
    rememberPanelState: function(){
    	
    	var cutFrom = document.location['pathname'].lastIndexOf('/', 1);
		var cookieRoot = document.location['pathname'].substr(0,(cutFrom+1));
    	
		var panelStateObj = {srchPanelOpen: this.srchOptShowStatus, srchCtrlComboOpen: this.ctrlTypeComboStatus, srchWinOpen: this.srchWinShowStatus, openFilters: []};
		if (this.srchWinShowStatus){			
			panelStateObj.winState = {
				x: this.win.cfg.getProperty("x"), 
				clientX: this.win.cfg.getProperty("x"), 
				clientY: this.win.cfg.getProperty("y"), 
				y: this.win.cfg.getProperty("y"), 
				h: parseInt(this.win.cfg.getProperty("height")), 
				w: parseInt(this.win.cfg.getProperty("width"))
			};			
		}
		
		if (!this.usedSrch){
			panelStateObj.openFilters = this.getShownFilterNames();
		}
		
		var panelStateString = JSON.stringify(panelStateObj);		
		set_cookie('panelState_'+this.shortTName+'_'+this.id, panelStateString, this.panelStateExpires, cookieRoot, '', '');
    },
    
    remindPanelState: function(){
    	var panelStateString = get_cookie('panelState_'+this.shortTName+'_'+this.id);
    	
    	if (!panelStateString){
    		if (this.panelSearchFields.length){
    			this.showSearchOptions();
    		}
    		return;
    	}
    	
    	var panelStateObj = JSON.parse(panelStateString);
    	
    	if (panelStateObj.srchWinOpen){
    		this.hideSearchOptions();
    		this.showSearchWin(panelStateObj.winState);
    	}else if(panelStateObj.srchPanelOpen){
    		this.showSearchOptions();
    	}
    	
    	if (panelStateObj.srchCtrlComboOpen){
    		this.showCtrlTypeCombo();
    	}else{
    		this.hideCtrlTypeCombo();
    	}
    	
    	if (!this.usedSrch){
    		// cut all quick search fields from array
    		for(var i=0;i<this.panelSearchFields.length;i++){
    			var elemIndex = panelStateObj.openFilters.getIndexOfElem(this.panelSearchFields[i]);
    			if (elemIndex != -1){
    				panelStateObj.openFilters.splice(elemIndex, 1);
    			}
	    	}
	    	// add fields
	    	for(var i=0;i<panelStateObj.openFilters.length;i++){	    		
	    		this.addFilter(panelStateObj.openFilters[i]);
	    	}
    	}
    },
    
    getFieldIds: function(fName){
    	var idsArr = [];
    	if (this.ctrlsShowMap[fName]){    		
    		for(var id in this.ctrlsShowMap[fName]){
    			idsArr.push(id);
    		}    		
    	}
		return idsArr;
    },
    getFieldControls: function(fName){
    	var ctrlsArr = [], ctrl = null, idsArr = this.getFieldIds(fName);
    	for(var i=0; i<idsArr.length; i++){
    		ctrl = Runner.controls.ControlManager.getAt(this.tName, idsArr[i], fName);
    		ctrlsArr.push(ctrl);
    	}
    	return ctrlsArr;
    },
    
    getSecondControl: function(fName, id){
    	return Runner.controls.ControlManager.getAt(this.tName, id, fName, 1);
    },
    
    getFieldOptions: function(fName){
    	var optsArr = [], opt = null, idsArr = this.getFieldIds(fName);
    	for(var i=0; i<idsArr.length; i++){
    		opt = $('#'+this.getComboId(fName, idsArr[i])).get(0);
    		optsArr.push(opt);
    	}
    	return optsArr;
    }
    
});
