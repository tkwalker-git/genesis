/**
 * Search form with user interface. 
 * 
 */
Runner.search.SearchFormWithUI = Runner.extend(Runner.search.SearchForm, {	  
    /**
    * Options panel show status indicator
    * @type Boolean
    */
    srchOptShowStatus: false,
    /**
    * Search win show status indicator
    * @type Boolean
    */
    srchWinShowStatus: false,
    /**
    * Show status indicator of div, which contains add filter buttons
    * @type Boolean
    */
    ctrlChooseMenuStatus: false,
    /**
    * Show status indicator of search type combos
    * @type Boolean
    */
    ctrlTypeComboStatus: false,
    /**
    * jQuery obj of search options div
    * @type {obj}
    */
    srchOptDiv: null,
    /**
    * jQuery object of img-button options panel expander
    * @type {obj}
    */
    srchOptExpander: null,
    /**
    * jQuery object of img-button search win expander
    * @type {obj}
    */
    srchWinExpander: null,
    /**
     * jQuery object with div, that contains all search elements
     * @type {obj}
     */
    srchBlock: null,
    
    /**
     * show all button jQuery obj
     * @type {obj} 
     */
    showAll: null,
    
    showAllButtStatus: false,
    /**
     * jQuery object with div, that contains all search controls
     * @type {obj}
     */
    srchCtrlsBlock: null,
    /**
    * Show status indicator of search block
    * @type Boolean
    */
    srchBlockStatus: true,
    
    /**
     * jQuery obj of top div with radio conditions
     * @type {obj}
     */
    topCritCont: null,
    
    /**
    * jQuery object of div with add-filter buttons
    * @type {obj}
    */
    ctrlChooseMenuDiv: null,
    /**
    * jQuery object of div with basic search controls
    * @type {obj}
    */
    srchPanelHeader: null,
    /**
     * jQuery object of div where panel should be placed. Used to toggle window and panel mode
     * @type {obj}
     */
    panelContainer: null,
    /**
     * jQuery object of div with b tags at bottom of the panel. Used on some layouts
     * for example on Madrid
     * @type {obj} 
     */
    bottomPanelRound: null,
    /**
     * jQuery obj of bottom search button
     * @type {obj} 
     */
    bottomSearchButt: null,    
    /**
    * Img src attr for hide opt
    * @type String
    */
    hideOptSrc: "images/search/hideOptions.gif",
    /**
    * Img src attr for show opt
    * @type String
    */
    showOptSrc: "images/search/showOptions.gif",
    /**
	 * Search panel icon switcher text
	 * @type 
	 */
    showOptText: "Show search options",
    /**
	 * Search panel icon switcher text
	 * @type 
	 */
    hideOptText: "Hide search options",
    /**
	 * Search type combos switcher text
	 * @type 
	 */
	showComboText: 'Show options',
	/**
	 * Search type combos switcher text
	 * @type 
	 */
	hideComboText: 'Hide options',
    /**
    * Array of search type combos
    * @type {array}
    */
    searchTypeCombosArr: null,
    /**
    * Array of search type combos
    * appear in window mode
    * @type {array}
    */
    searchTypeCombosWinArr: null,
    /**
     * Array of divs, that used as containers for one search control with its combos, delete buttons etc.
     * @type {array}
     */
    srchFilterRowArr: null,
    /**
     * Array of trs, that used as containers for one search control with its combos, delete buttons etc.
     * Trs appear in window mode
     * @type {array}
     */
    srchFilterRowWinArr: null,
    /**
     * Array of field names
     * @type array
     */
    fNamesArr: null,
    /**
    * ctrls map. Used for indicate which index conected with which search ctrl
    * @type obj
    */    
    ctrlsShowMap: null,
    /**
     * jQuery obj of link-switcher. Toggles search type combos
     * @type obj
     */
    showHideSearchComboButton: null,
    /**
     * Iframe object used for control choose menu coverage in IE6
     * @type {object}
     */
    iframe: null,
    /**
     * Hider object, hide selects in fly div mode
     * @type 
     */
    hider: null,

    winDiv: null,
    /**
     * True if records_block div margin-left was change, to prevent grid coverage
     * @type Boolean
     */
    recBlockMargChange: false,
	
	moveBlockPadding: 'padding-left',
    
    moveGridDiv: null,
	/**
    * Constructor
    * @param {obj} cfg
    */
    constructor: function(cfg) {
    	// recreate objects
        this.searchTypeCombosArr = [];
        this.searchTypeCombosWinArr = [];
        this.fNamesArr = [];
        this.srchFilterRowArr = [];
        this.srchFilterRowWinArr = [];
        //call parent
        Runner.search.SearchFormWithUI.superclass.constructor.call(this, cfg);
        // -------------------stuf used only when in panel mode------------------
        // private jQuery obj
        this.srchOptDiv = $("#searchOptions" + this.id);
        this.srchOptExpander = $("#showOptPanel" + this.id);
        this.srchWinExpander = $("#showSrchWin" + this.id);        
        //this.ctrlChooseMenuDiv = $("#controlChooseMenu" + this.id);

        // div container with all search stuff
        var srchBlockId = 'search_block'+this.id;
        this.srchBlock = $("#"+srchBlockId);
        // div object with all controls        
        var srchCtrlsBlockId = 'controlsBlock_'+this.id;
        this.srchCtrlsBlock = $("#"+srchCtrlsBlockId);
        // table object with all controls fpr window       
        var srchCtrlsBlockWinId = 'controlsBlock_'+this.id+'_win';
        this.srchCtrlsBlockWin = $("#"+srchCtrlsBlockWinId);
        // add object with basic search controls
        var srchPanelHeaderId = 'searchPanelHeader'+this.id;
        this.srchPanelHeader = $("#"+srchPanelHeaderId);
        
        var showHideSearchComboButtonId = 'showHideSearchType'+this.id;
        this.showHideSearchComboButton = $('#'+showHideSearchComboButtonId); 
        // container where panel placed
        var panelContainerId = 'searchPanelContainer'+this.id;
        this.panelContainer = $('#'+panelContainerId);
        // for some layouts bottom panel round should handled by this class
        var bottomPanelRoundId = 'searchPanelBottomRound'+this.id;
        this.bottomPanelRound = $('#'+bottomPanelRoundId);
        
        var showAllId = "showall"+this.id;
        this.showAll = $('#'+showAllId);
        this.showAllButtStatus = this.usedSrch;
        //this.ctrlChooseMenuDiv.appendTo(document.body);
        // check amsterdam margin change
        this.moveGridDiv = $("div[@moveforsearch='move"+this.id+"']");
		if(Runner.isIE){
			if($(document.html).attr('dir').toLowerCase() == 'rtl'){
				this.moveBlockPadding = 'padding-right';
			}
		}else{
			if($("html[@dir=RTL]").length){
				this.moveBlockPadding = 'padding-right';
			}
		}
		var mrgLeft = this.moveGridDiv.css(this.moveBlockPadding);        
		if ($('#mainmenu_block').length==0 && $('#menu_block'+this.id).length == 0){
    		this.recBlockMargChange = true;
    	}
    	
        this.addDelegatedEvents();
    },
    
    /**
     * Binds hover events for table and div. 
     * Use parent containers as delegates
     * Call it in constructor
     */
    addDelegatedEvents: function(){
    	// for event handlers closures
    	var controller = this;
    	// filter div row mouseover event
    	this.srchCtrlsBlock.bind('mouseover', function(e){
    		// get event element
   			var target = Runner.Event.prototype.getTarget(e);
			// traverse to filter parent   			   				
			while (target && target.id != controller.srchCtrlsBlock.attr('id')) {
				if(target.id && target.id.indexOf('filter_') != -1) {
					// show del image
					$(target).find('img[@class=searchPanelButton]').css('visibility', 'visible');	
					$(target).removeClass('blockBorder').addClass('blockBorderHovered');
					break;
				} else {
					target = target.parentNode;
				}
			}
    	});
    	
    	// filter div row mouseout event
    	this.srchCtrlsBlock.bind('mouseout', function(e){
    		// get event element
   			var target = Runner.Event.prototype.getTarget(e);
			// traverse to filter parent   			   				
			while (target && target.id != controller.srchCtrlsBlock.attr('id')) {
				if(target.id && target.id.indexOf('filter_') != -1) {
					// hide del image
					$(target).find('img[@class=searchPanelButton]').css('visibility', 'hidden');	
					$(target).removeClass('blockBorderHovered').addClass('blockBorder');
					break;
				} else {
					target = target.parentNode;
				}
			}
    	});
    	
    	
    	// border hover events
    	this.srchCtrlsBlockWin.bind('mouseover', function(e){
    		// get event element
   			var target = Runner.Event.prototype.getTarget(e);
			// traverse to filter parent   			   				
			while (target.id != controller.srchCtrlsBlockWin.attr('id')) {				
				if(target.nodeName == "TD") {
					// get row with all tds
					var tr = $(target).parent();
					// show del image
					tr.find('img[@class=searchPanelButton]').css('visibility', 'visible');	
					// all cells
					var tds = tr.children();
					// make sure that we choosed td with controls, and not with loading box
					if ($(tds[0]).hasClass('srchWinCell')){
						// if second ctrldoesn't exist or is hidden, make right border for last-1 child
		    			var lastVisible = tds.length-1;    		
		    			//console.log($(tds[tds.length-1]).children(), 'child');
		    			if ($(tds[tds.length-1]).children().length === 0 || $(tds[tds.length-1]).find('*:first-child').css('display') == 'none'){
		    				lastVisible--;
		    			}    
		    			
		    			// set style for left element
		    			$(tds[0]).removeClass('cellBorderCenter').removeClass('cellBorderLeft').addClass('cellBorderCenterHovered').addClass('cellBorderLeftHovered');
		    			// set styles for center elements
		    			for(var i=0;i<lastVisible;i++){
		    				// try to remove also right style, because it may come when second ctrl was invisible
		    				$(tds[i]).removeClass('cellBorderCenter').removeClass('cellBorderRightHovered').addClass('cellBorderCenterHovered');
		    			}    
		    			//set style for last elem
		    			$(tds[lastVisible]).removeClass('cellBorderCenter').removeClass('cellBorderRight').addClass('cellBorderCenterHovered').addClass('cellBorderRightHovered');
					}
					break;
				} else {
					target = target.parentNode;
				}
			}			
    	});
    	
    	this.srchCtrlsBlockWin.bind('mouseout', function(e){
    		// get event element
   			var target = Runner.Event.prototype.getTarget(e);
			// traverse to filter parent   			   				
			while (target.id != controller.srchCtrlsBlockWin.attr('id')) {				
				if(target.nodeName == "TD") {
					// get row with all tds
					var tr = $(target).parent();
					// show del image
					tr.find('img[@class=searchPanelButton]').css('visibility', 'hidden');	
					// all cells
					var tds = tr.children();
					// make sure that we choosed td with controls, and not with loading box
					if ($(tds[0]).hasClass('srchWinCell')){
						// if second ctrldoesn't exist or is hidden, make right border for last-1 child
		    			var lastVisible = tds.length-1;
		    			if ($(tds[tds.length-1]).children().length === 0 || $(tds[tds.length-1]).find('*:first-child').css('display') == 'none'){
		    				lastVisible--;
		    			}
		    			// set style for left element
		    			$(tds[0]).removeClass('cellBorderCenterHovered').removeClass('cellBorderLeftHovered').addClass('cellBorderCenter').addClass('cellBorderLeft');
		    			// set styles for center elements
		    			for(var i=0;i<lastVisible;i++){
		    				$(tds[i]).removeClass('cellBorderCenterHovered').addClass('cellBorderCenter');
		    			}
		    			//set style for last elem
		    			$(tds[lastVisible]).removeClass('cellBorderCenterHovered').removeClass('cellBorderRightHovered').addClass('cellBorderCenter').addClass('cellBorderRight');
					}
					break;
				} else {
					target = target.parentNode;
				}
			}	
    	});  
    },
    /**
     * Return search type combo container id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getComboContId: function(fName, ind, isWin){    	
    	return "searchType_" + ind + "_" + Runner.goodFieldName(fName) + (isWin ? '_win' : '');
    }, 
    
    getComboId: function(fName, id){
    	return "srchOpt_" + id + "_" + Runner.goodFieldName(fName);
    },
    /**
     * Return filter div id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getFilterDivId: function(fName, ind, isWin){    	
    	return "filter_" + ind + "_" + Runner.goodFieldName(fName) + (isWin ? '_win' : '');
    },
   
    /**
    * Create flyDiv with search controls
    * If used as onlick handler pass event object, for get click coords
    * @param {event} e
    */
    showSearchWin: function(e) { 

    	this.hideCtrlChooseMenu();
    	
    	// get click coors
        var x = 50, y = 50;
        if (Runner.isIE && e) {
            y = e.y;
            x = e.x;
        } else if (e) {
            y = e.clientY;
            x = e.clientX;
        }
    	
    	// page renders itself into YUI panel with auto resize and DD
		this.winDiv = document.createElement("DIV");

		var width = "500px",
			height;
		if (e && e.w && e.h){
        	height = e.h+"px";
        	width = e.w+"px";
        }
		var winCfg = {
	        draggable: true,
	        width: width,
	        autofillheight: "body",
	        constraintoviewport: true,
	        xy:[x, y]/*,
	        context: ["showbtn", "tl", "bl"]*/
	    };
	    if (height){
	    	winCfg.height = height;
	    }
	    this.win = new YAHOO.widget.Panel(this.winDiv, winCfg);
		
		this.win.setHeader('<span style="color: black;">Search for:&nbsp;</span>');
	    this.win.render(document.body);		
	 	this.win.bringToTop();
	 	
	 	this.win.subscribe('hide', function(eventName, args, newScope){
	 		this.hideSearchWin();
        }, this, true);
        
        this.win.subscribe('drag', function(eventName, args, newScope){
	 		this.bringToTop();
        });
        
	 
        var resize = new YAHOO.util.Resize(this.winDiv, {
            handles: ["br"],
                autoRatio: false,
                minWidth: 300,
                minHeight: 100,
                status: false 
            });
 
            resize.on("startResize", function(args) { 
    		    if (this.cfg.getProperty("constraintoviewport")){
                    var D = YAHOO.util.Dom;
 
                    var clientRegion = D.getClientRegion();
                    var elRegion = D.getRegion(this.element);
 
                    resize.set("maxWidth", clientRegion.right - elRegion.left - YAHOO.widget.Overlay.VIEWPORT_OFFSET);
                	resize.set("maxHeight", clientRegion.bottom - elRegion.top - YAHOO.widget.Overlay.VIEWPORT_OFFSET);
            	}else{
	                resize.set("maxWidth", null);
	                resize.set("maxHeight", null);
	        	}
 
            }, this.win, true);
		 
			resize.on("resize", function(args) {
	            var panelHeight = args.height;
	            this.cfg.setProperty("height", panelHeight + "px");
	            var panelWidth = args.width;
	            this.cfg.setProperty("width", panelWidth + "px");
        }, this.win, true);
    	
    	
        	        
        // change appereance
        /*$(divContainer).css('padding-top', '10px');	        
        $(divContainer).css('background-color', this.panelContainer.css('background-color'));*/
        
        
        
        // add to fly div
        /*var bodyContDiv = document.createElement("DIV");
        this.srchPanelHeader.appendTo(bodyContDiv);
        this.srchOptDiv.appendTo(bodyContDiv); 
        this.win.setBody(bodyContDiv);*/        
        // without this, body div won't to init.
        this.win.setBody("&nbsp;"); 
        this.win.appendToBody(this.srchPanelHeader.get(0)); 
        this.win.appendToBody(this.srchOptDiv.get(0)); 
        // hide div for panel mode
        this.srchCtrlsBlock.hide();
        // move all content to table from divs
        this.moveCtrlsToTable();
        // show table for window mode
        this.srchCtrlsBlockWin.show();	   
        	 
        
        this.showSearchOptions();
        // set show indicator
        this.srchWinShowStatus = true;
		
		if (this.recBlockMargChange){
			this.moveGridDiv.css(this.moveBlockPadding, 0);
		}
         
		this.initWinDelButtons();
    },
    /**
     * Move controls when switch to window mode.
     * On each table and div row this method call moveCtrlsToTableRow,
     * which move html and DOM from divs to tds
     */
    moveCtrlsToTable: function(){
    	// loop through div rows
    	for(var i=0; i<this.srchFilterRowArr.length;i++){
    		var divRowId = this.srchFilterRowArr[i].attr('id');
    		var tableRowId = divRowId+'_win';
    		var tableRow = $('#'+tableRowId);
    		if (this.srchFilterRowArr[i].css('display') != 'none'){
    			tableRow.show();   
    		}else{
    			tableRow.css('display', 'none');
    		}
    		 		
    		// move div row content to table row symetrically
    		this.moveCtrlsToTableRow(this.srchFilterRowArr[i], tableRow);
    	}
    },
    /**
     * Used to move html and DOM of each div row to table row
     */
    moveCtrlsToTableRow: function(divRow, tableRow){
    	var divCells = divRow.children();
    	var tds = tableRow.children();
    	// loop through div cells
    	for(var i=0; i<divCells.length;i++){    		
    		// move all content of div cell to td
    		var divCellChildren = $(divCells[i]).children();
    		// clear from script tag, to prevent executing it twice
    		divCellChildren.find('script').remove();
    		// move with DOM objects, not only HTML
    		divCellChildren.appendTo($(tds[i]));
    		
    		if ($(divCells[i]).css('display') == 'none'){
    			$(tds[i]).hide();
    		}else{
    			$(tds[i]).show();
    		}
    	}	  
    	// move field name in this way, because it conatins only text
    	$(tds[1]).html($(divCells[1]).html());
    },
    /**
     * Move controls when switch to panel mode, from window.
     * On each table and div row this method call moveCtrlsToDivRow,
     * which move html and DOM from tds to divs
     */
    moveCtrlsToDiv: function(){
    	// loop through div rows
    	for(var i=0; i<this.srchFilterRowWinArr.length;i++){
    		var tableRowId = this.srchFilterRowWinArr[i].attr('id');
    		var divRowId = tableRowId.substr(0, tableRowId.lastIndexOf('_'));//divRowId+'_win';
    		var divRow = $('#'+divRowId);
    		if (this.srchFilterRowWinArr[i].css('display') != 'none'){
    			divRow.show();   
    		}else{
    			divRow.css('display', 'none');
    		}    		
    		// move div row content to table row symetrically
    		this.moveCtrlsToDivRow(this.srchFilterRowWinArr[i], divRow);
    	}
    },
    /**
     * Used to move html and DOM of each table row to div row
     */
    moveCtrlsToDivRow: function(tableRow, divRow){
    	var divCells = divRow.children();
    	var tds = tableRow.children();
    	// loop through div cells
    	for(var i=0; i<tds.length;i++){    		
    		// move all content of div cell to td
    		var tableCellChildren = $(tds[i]).children();
    		// clear from script tag, to prevent executing it twice
    		tableCellChildren.find('script').remove();
    		// move with DOM objects, not only HTML
    		tableCellChildren.appendTo($(divCells[i]));
    		if ($(tds[i]).css('display') == 'none'){
    			$(divCells[i]).hide();
    		}else{
    			$(divCells[i]).show();
    		}
    	}	  
    	// move field name in this way, because it conatins only text
    	$(divCells[1]).html($(tds[1]).html());
    },
    
    hideShowAll: function(){
    	this.showAll.hide();
    	this.showAllButtStatus = false;
    },
    
    showShowAll: function(){
    	this.showAll.parent().show();
    	
    	this.showShowAll = function(){
    		this.showAll.show();
    		this.showAllButtStatus = true;
    	};
    	
    	this.showShowAll();
    },
    
    toggleShowAll: function(){
    	this.showAllButtStatus ? this.hideShowAll() : this.showShowAll();        
    },
    
    /**
    * Removes fly div, and place controls to search panel
    */
    hideSearchWin: function(id) {
    	this.hideCtrlChooseMenu();
    	// move opt div to search panel        
        this.srchOptDiv.prependTo(this.panelContainer);
		// to correct amsterdam layout with no menu
		this.corGridDiv();
        // hide table
    	this.srchCtrlsBlockWin.hide();
    	// move controls
    	this.moveCtrlsToDiv();
    	// show panel mode div
    	this.srchCtrlsBlock.show();        
        this.srchPanelHeader.prependTo($("#searchform"+this.id));
        var win = this.win
        setTimeout(function(){
			win.destroy();
		}, 5);
        // set status indicator
        this.srchWinShowStatus = false;
    },
    
     

    /**
    * Search win switcher
    * opens and closes search win
    */
    toggleSearchWin: function(e) {
        this.srchWinShowStatus ? this.hideSearchWin() : this.showSearchWin(e);
        this.hideCtrlChooseMenu();
    },
   /**
    * Showes search options div and changes image expander 
    */
    showSearchBlock: function() {
    	// show div
        this.srchBlock.show();
        this.srchBlockStatus = true;
    },
    /**
    * Closes search options div and changes image expander 
    */
    hideSearchBlock: function() {
    	// hide div
        this.srchBlock.hide();
        this.srchBlockStatus = false;
    },
    /**
    * Search options switcher
    * opens and closes options in search panel
    */
    toggleSearchBlock: function() {
        // can open panel, only if win is hidden
        (this.srchBlockStatus && !this.srchWinShowStatus) ? this.hideSearchBlock() : this.showSearchBlock();
    },
	/**
    * Correct indentation of grid block for search if hasn't menu on list page
    */
    corGridDiv: function(){
		var mrgLeft = this.moveGridDiv.css(this.moveBlockPadding);
    	if (mrgLeft == 'auto' ||  mrgLeft == '0px'){
    		this.recBlockMargChange = true;
			this.moveGridDiv.css(this.moveBlockPadding, 203);
    	}
	},
    /**
    * Showes search options div and changes image expander 
    */
    showSearchOptions: function() {
    	// to correct amsterdam layout with no menu
		this.corGridDiv();
        // show div
    	this.srchOptDiv.show();	
    	// show bottom round if exist
        this.bottomPanelRound.css('display',  '');
        // change image
        
        this.srchOptExpander.css("background-image", 'url("'+this.hideOptSrc+'")');
        this.srchOptExpander.attr('alt', this.hideOptText);
        this.srchOptExpander.attr('title', this.hideOptText);
        this.srchOptShowStatus = true;
    },
    /**
    * Closes search options div and changes image expander 
    */
    hideSearchOptions: function() {
    	// to correct amsterdam layout with no menu 
    	if (this.recBlockMargChange){
    		this.moveGridDiv.css(this.moveBlockPadding, 0);
    	}
    	// hide div
    	this.srchOptDiv.hide();
        // hide bottom round if exist
        this.bottomPanelRound.css('display',  'none');
        // change image
        this.srchOptExpander.css("background-image", 'url("'+this.showOptSrc+'")');
        this.srchOptExpander.attr('alt', this.showOptText);
        this.srchOptExpander.attr('title', this.showOptText);
        this.srchOptShowStatus = false;
    },
    /**
    * Search options switcher
    * opens and closes options in search panel
    */
    toggleSearchOptions: function() {
        // can open panel, only if win is hidden
        (this.srchOptShowStatus && !this.srchWinShowStatus) ? this.hideSearchOptions() : this.showSearchOptions();
        this.hideCtrlChooseMenu();
    },

    /**
    * Showes search options div and changes image expander 
    */
    showCtrlChooseMenu: function() { 
    	if (!this.ctrlChooseMenuDiv){
	    	this.ctrlChooseMenuDiv = $("#controlChooseMenu" + this.id);
	    	this.ctrlChooseMenuDiv.appendTo(document.body);
    	}
    	// create closure
    	var controller = this;
    	// add events   
		var hideHandler = function(){
			controller.showCtrlChooseMenu();
		}
		var hideTask = new Runner.util.DelayedTask(hideHandler);
		controller.ctrlChooseMenuDiv.bind('mouseover', function(e){
			showTask.cancel();
            hideTask.delay(50, hideHandler, null, [e]);
        });    		
    	
		var showHandler = function(){
			controller.hideCtrlChooseMenu();
		}
		var showTask = new Runner.util.DelayedTask(showHandler);
		controller.ctrlChooseMenuDiv.bind('mouseout', function(e){
			hideTask.cancel();
            showTask.delay(50, showHandler, null, [e]);
        });    		
    	
    	// lazy init function
    	if (Runner.isIE6){    		
    		this.iframe = new Runner.util.IEHelper.iframe(/*this.ctrlChooseMenuDiv[0]*/);
    		this.hider = new Runner.util.IEHelper.selectsHider(this.ctrlChooseMenuDiv[0]);
    	}
    	// redefine
    	this.showCtrlChooseMenu = function(){
			// set menu position, relative to Add criteria link
    		var posObj = findPos($("#showHideControlChooseMenu"+this.id)[0]);
			// calc coordinates
    		var divT = posObj[1]+posObj[3], divL = posObj[0];
	    	// add only in win mode, strange positioning in fly div
	    	this.ctrlChooseMenuDiv.css('top', divT).css('left', divL);
	    	// show it
	        this.ctrlChooseMenuDiv.show();
	         // set div width, after div is visible, for correct offsetWidth data
	        this.ctrlChooseMenuDiv[0].offsetWidth < 80 ? this.ctrlChooseMenuDiv.css('width', '65px') : '';
	        // add iframe in panel mode
	        if (Runner.isIE6 && !this.srchWinShowStatus){
	       		// create iframe for IE6
		        this.iframe.reset({
					l: divL,
					t: divT,
					h: this.ctrlChooseMenuDiv[0].offsetHeight,
					w: this.ctrlChooseMenuDiv[0].offsetWidth
				});      				
			// in window mode hide combos	
	        }else if(Runner.isIE6 && this.srchWinShowStatus){
	        	this.hider.showSels();
	        	this.hider.getSelects();
	        	this.hider.hideSels();
	        }
	        // set max z-index
	        Runner.getZindex(this.ctrlChooseMenuDiv);
	        this.ctrlChooseMenuStatus = true;
    	}
    	// call function, after lazy-init
    	this.showCtrlChooseMenu();
    },
    /**
    * Closes search options div and changes image expander 
    */
    hideCtrlChooseMenu: function() {
    	if (!this.ctrlChooseMenuDiv){
	    	this.ctrlChooseMenuDiv = $("#controlChooseMenu" + this.id);
	    	this.ctrlChooseMenuDiv.appendTo(document.body);
    	}
        this.ctrlChooseMenuDiv.hide();
        this.ctrlChooseMenuStatus = false;
        if (Runner.isIE6 && !this.srchWinShowStatus && this.iframe){
        	this.iframe.hide();
        }else if(Runner.isIE6 && this.srchWinShowStatus && this.iframe){
        	this.hider.showSels();
        }
    },

    /**
    * Search options switcher
    * opens and closes options in search panel
    */
    toggleCtrlChooseMenu: function() {
        this.ctrlChooseMenuStatus ? this.hideCtrlChooseMenu() : this.showCtrlChooseMenu();        
    },
    
	/**
    * Search type combos show handler
    */
    showCtrlTypeCombo: function() {
        for (var i = 0; i < this.searchTypeCombosArr.length; i++) {        	
	    	this.searchTypeCombosArr[i].show();	
	    	this.searchTypeCombosArr[i].find('select').show();
        }
        for (var i = 0; i < this.searchTypeCombosWinArr.length; i++) {        	
	    	this.searchTypeCombosWinArr[i].show();	
	    	this.searchTypeCombosWinArr[i].find('select').show();
        }
        this.showHideSearchComboButton.html(this.hideComboText);
        this.showHideSearchComboButton.attr('title', this.hideComboText);
        this.ctrlTypeComboStatus = true;
        
    },
    /**
    * Search type combos hide handler
    */
    hideCtrlTypeCombo: function() {
        for (var i = 0; i < this.searchTypeCombosArr.length; i++) {        	
            this.searchTypeCombosArr[i].hide();
            this.searchTypeCombosArr[i].find('select').hide();
        }
        for (var i = 0; i < this.searchTypeCombosWinArr.length; i++) {        	
            this.searchTypeCombosWinArr[i].hide();
            this.searchTypeCombosWinArr[i].find('select').hide();
        }
        this.showHideSearchComboButton.html(this.showComboText);
        this.showHideSearchComboButton.attr('title', this.showComboText);
        this.ctrlTypeComboStatus = false;
    },    
    /**
    * Search type combos show\hide switcher
    */
    toggleCtrlTypeCombo: function() {
        this.ctrlTypeComboStatus ? this.hideCtrlTypeCombo() : this.showCtrlTypeCombo();
    },
    /**
     * Criterias show|hide controller
     * @param {int} ctrlsCount
     */
    toggleCrit: function(ctrlsCount){
    	// lazy init, get conditions containers
        var topCritContId = 'srchCritTop'+this.id;
        this.topCritCont = $('#'+topCritContId);
        var bottomSearchButtId = 'bottomSearchButt'+this.id;
        this.bottomSearchButt = $('#'+bottomSearchButtId); 
        // redefine after first call
        this.toggleCrit = function(ctrlsCount){
        	ctrlsCount > 1 ? this.topCritCont.show() : this.topCritCont.hide();
    		ctrlsCount > 0 ? this.bottomSearchButt.show() : this.bottomSearchButt.hide();
        }
        // for first call
		this.toggleCrit(ctrlsCount);
    }
});
