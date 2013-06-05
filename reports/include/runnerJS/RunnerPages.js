Runner.namespace('Runner.pages');

Runner.pages.constants = {
	PAGE_LIST: "list",
	PAGE_ADD: "add",
	PAGE_EDIT: "edit",
	PAGE_VIEW: "view",
	PAGE_MENU: "menu",
	PAGE_REGISTER: "register",
	PAGE_SEARCH: "search",
	PAGE_REPORT: "report",
	PAGE_CHART: "chart",
	PAGE_PRINT: "print",
	PAGE_EXPORT: "export",
	PAGE_IMPORT: "import",
	PAGE_ADMIN_MEMBERS: "admin_members",
	PAGE_ADMIN_RIGHTS: "admin_rights",
	PAGE_ADMIN_GROUPS: "admin_groups",
	
	LIST_SIMPLE: 0,
	LIST_LOOKUP: 1,
	LIST_DETAILS: 3,
	LIST_AJAX: 4,
	RIGHTS_PAGE:  5,
	MEMBERS_PAGE:  6,
	
	ADD_SIMPLE: 0,
	ADD_INLINE: 1,
	ADD_ONTHEFLY: 2,
	ADD_MASTER: 3,
	ADD_POPUP: 4,
	
	EDIT_SIMPLE: 0,
	EDIT_INLINE: 1,
	EDIT_ONTHEFLY: 2,
	EDIT_POPUP: 3
};
/** 
 * Global control manager. Alows to add, delete and manage controls
 * Collection of controls for the specific table.
 * Should not be created directly, only one instance per page. 
 * Use its instance to get access to any control
 * @singleton
 */
Runner.pages.PageManager = function(){
	/**
	 * Table managers collection
	 * @type {object} private
	 */
	var tables = {}, beforeUnloadPool = [];	
	
	if (window.onunload){
		beforeUnloadPool.push(window.onBeforeUnload, window, []);
	}
	window.onunload = function(){
		window.Runner.pages.PageManager.callUnload();
	}
	//console.log(tables, 'tables in PageManager');
	
	return {
		/**
		 * Control to register
		 * @param {#link} control
		 */
		register: function(pageController){
			// return false if not control
			if (!pageController){
				return false;
			}
			// get table name
			var pageTable = pageController.tName;		
			// if table not exists, create new one
			if (!tables[pageTable]){
				tables[pageTable] = {};	
			}
			
			var pageId = pageController.pageId;		
			
			tables[pageTable][pageId] = pageController;
			
			return true;
		},
		/**
		 * Returns control or array of controls by following params
		 * @param {string} tName
		 * @param {string} rowId Pass false or null to get all controls of the table
		 * @param {string} fName Pass false or null to get all controls of the row
		 * @param {int} controlIndex Pass false or null to get first control of the field
		 * @return {object} return control, array of controls or false
		 */
		getAt: function(tName, pageId){
			// if table not exists
			if (!tables[tName]) {
				return false;
			}	
			if (!tables[tName][pageId]){
				return false;
			}
			
			return tables[tName][pageId];
		},
		/**
		 * Unregister control, row or table
		 * @param {string} tName
		 * @param {string} rowId Pass false or null to clear all controls of the table
		 * @param {string} fName Pass false or null to clear all controls of the row
		 * @param {int} controlIndex Pass false or null to clear first control of the field
		 * @return {bool} true if success, otherwise false
		 */
		unregister: function(tName, pageId){	
			// if no table name passed, return false
			if (!tables[tName]) {
				return false;
			}	
			// recursively destroy pageObjects
			if (!pageId){
				for(var id in tables[tName]){
					this.unregister(tName, id);
				}
				delete tables[tName];
			}else{
				if (tables[tName][pageId].destructor){					
					tables[tName][pageId].destructor();
				}
				Runner.pages.PageControlsMap.removeMap(tName, tables[tName][pageId].pageType, pageId);
				delete tables[tName][pageId];
			}
			
			return true;
		},
		
		initPages: function(){
			var controlsMap = Runner.pages.PageControlsMap.getMap(),
				cfg;
			
			for(var tName in controlsMap){
				for(var pageType in controlsMap[tName]){
					for(var pageId in controlsMap[tName][pageType]){
						cfg = {
							tName: tName, 
							pageType: pageType, 
							pageId: pageId, 
							controlsMap: controlsMap[tName][pageType][pageId], 
							pageMode: Runner.pages.PageSettings.getTableData(tName, "pageMode", 0)
						};
						this.initPage(cfg);
					}
				}
			}			
			// init only once
			this.initPages = Runner.emptyFn;
			stopLoading(1,0);
		},
		
		initPage: function(cfg){
			var page = Runner.pages.PageFabric(cfg),
				events = Runner.pages.PageSettings.getTableData(cfg.tName, "events", {}),
				event;
			
			for(var pType in events){
				if (pType != cfg.pageType){
					continue;
				}
				for(var evName in events[pType]){
					event = events[pType][evName];
					page.on(evName, event.hn, page);
				}				
			}
			// call init method		
			if (!page.fly){
				page.init();
			}else{
				page.initFlyPage();
			}
			
		},
		/**
		 * For dynamic page opening
		 */
		openPage: function(pageParams){
			if (this.getAt(pageParams.tName, pageParams.pageId)){				
				var page = this.getAt(pageParams.tName, pageParams.pageId);
				page.show();
				return pageParams.pageId;
			}
			
			pageParams.pageId = Runner.genId();		
			
			var reqParams = {
				rndval: Math.random(),		
				id: pageParams.pageId,
				onFly: 1,
	            isNeedSettings: !Runner.pages.PageSettings.checkSettings(pageParams.tName)
			};
			// add base params to request						
			Runner.apply(reqParams, pageParams.baseParams);					
			// for closure		
			var pageManager = this, ajaxRequestUrl = Runner.pages.getUrl(pageParams.tName, pageParams.pageType, pageParams.keys, pageParams.keyPref);			
			// make request
			$.getJSON(ajaxRequestUrl, reqParams, function(ctrlsJSON){
				if (ctrlsJSON.settings){
					// add settings
					Runner.pages.PageSettings.addSettings(pageParams.tName, ctrlsJSON.settings);
				}
				// add map
				Runner.pages.PageControlsMap.addMap(pageParams.tName, pageParams.pageType, pageParams.pageId, ctrlsJSON.controlsMap);
				// callback analyze request data and call initPage of this class with cfg as param
				var cfg = {
					tName: pageParams.tName, 
					pageType: pageParams.pageType, 
					pageId: pageParams.pageId,
					controlsMap: ctrlsJSON.controlsMap[pageParams.tName][pageParams.pageType][pageParams.pageId], 
					headerCont: ctrlsJSON.headerCont || "&nbsp;",
					bodyCont: ctrlsJSON.html || "&nbsp;",
					fotterCont: ctrlsJSON.fotterCont || "&nbsp;",
					fly: true,
					submitUrl: ajaxRequestUrl
				};
				Runner.apply(cfg, pageParams);
				pageManager.initPage(cfg);		
				// add id counter
				Runner.setIdCounter(ctrlsJSON.idStartFrom);			
			});			
			
			return reqParams.id
		},
		
		addUnloadHn: function(hn, scope, args){
			if (typeof hn != 'function'){
				return false;
			}
			beforeUnloadPool.push({fn: hn, scope: scope || window, args: args || []});
			return true;
		},
		
		callUnload: function(){
			var scope, args;
			for(var i=0;i<beforeUnloadPool.length;i++){
				scope = beforeUnloadPool[i].scope;
				args = beforeUnloadPool[i].args;
				beforeUnloadPool[i].fn.apply(scope, args);
			}
			for(var tName in tables){
				this.unregister(tName);
			}
		}
	}
}();




// create namespace
Runner.namespace('Runner.pages');

Runner.pages.PageControlsMap = function(){
	
	window.controlsMap = JSON.parse(window.controlsMap) || {};
	
	console.log(controlsMap, 'controlsMap');
	
	return {
		
		addMap: function(tName, pageType, pageId, map){
			if (!tName || !pageType || typeof pageId == 'undefined'){
				return false;
			}
			if (!controlsMap[tName]){
				controlsMap[tName] = {};
			}
			if (!controlsMap[tName][pageType]){
				controlsMap[tName][pageType] = {};
			}
			controlsMap[tName][pageType][pageId] = map[tName][pageType][pageId];
		},
		
		getMap: function(tName, pageType, pageId){
			if (!tName){
				return controlsMap;
			}
			if (!pageType){
				return controlsMap[tName];
			}
			if (typeof pageId == 'undefined'){
				return controlsMap[tName][pageType];
			}
			return controlsMap[tName][pageType][pageId];
		},
		
		removeMap: function(tName, pageType, pageId){
			if (!tName || !pageType || typeof pageId == "undefined"){
				return false;
			}
			if (!controlsMap[tName]){
				return false;
			}
			if (!controlsMap[tName][pageType]){
				return false;
			}
			delete controlsMap[tName][pageType][pageId];
			return true;
		}
	
	
	};
	
}();


/**
 * Page settings store
 * @param {string} tName name of table
 * @param {string} pageType pageType of settings (list, add, edit etc.)
 */
Runner.pages.PageSettings = function(){
	
	// private
	window.settings = JSON.parse(window.settings) || {tables:{}};
	
	console.log(settings, 'settings')
	
	if (settings.idStartFrom){
		Runner.setIdCounter(settings.idStartFrom);
	}
	
	// public
	this.getSettings = function(tName, fName){
		if (!tName){
			return settings;
		}
		if (!fName){
			return settings.tableSettings[tName];
		}
		return settings.tableSettings[tName]['fieldSettings'][fName];
	};
	
};

/**
 * @singletone
 */
Runner.pages.PageSettings = Runner.extend(Runner.pages.PageSettings, {

	/**
	 * Checks setting if they are already exists
	 * @param {} tName
	 * @param {} pageType
	 */
	checkSettings: function(tName, fName){
		var settings = this.getSettings(tName, fName);
		if (settings){
			return true;
		}else{
			return false;
		}
	},
	
	/**
	 * Load settings from server, make ajax request
	 * @param {} tName
	 * @param {} pageType
	 */
	loadSettings: function(tName, pageType){
		
		
	},
		
	
	addPageEvent: function(tName, pType, evName, evHn){
		if (!settings.tableSettings[tName]["events"]){
			settings.tableSettings[tName]["events"] = {};
		}
		if (!settings.tableSettings[tName]["events"][pType]){
			settings.tableSettings[tName]["events"][pType] = {};
		}
		settings.tableSettings[tName]["events"][pType][evName] = {hn: evHn};
	},
	
	/**
	 * Add settings from data
	 * @param {object} cfg
	 */
	addSettings: function(tName, addSettings, forceRewrite){
		if (this.checkSettings(tName) && forceRewrite !== true){
			return false;
		}
		
		var settings = this.getSettings();		
		settings.tableSettings[tName] = addSettings.tableSettings[tName];
		// add short table names
		Runner.apply(settings.shortTNames, addSettings.shortTNames);
		/*if (addSettings.shortTNames){
			for(var table in addSettings.shortTNames){
				if (!settings.shortTNames[table]){
					settings.shortTNames[table] = addSettings.shortTNames[table];
				}
			}
		}
		*/
		return true;		
	},
	
	getViewType: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["viewFormat"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["viewFormat"];
		}else{
			return  settings.fieldDefSettings.ViewFormat || Runner.controls.constants.FORMAT_NONE;	
		}		
	},
	
	getEditFormat: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["editFormat"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["editFormat"];
		}else{
			return  settings.fieldDefSettings.EditFormat || Runner.controls.constants.EDIT_FORMAT_NONE;	
		}	 
	},
	
	getShortTName: function(tName){
		return settings.shortTNames[tName] || settings.tableSettings[tName].shortTName || "";
	},
	
	getValidations: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["validation"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["validation"];
		}else{
			return  settings.fieldDefSettings.validation || {validationArr: [], regExp: null};	
		}	 
	},
	
	getDisabledStatus: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["isDisabled"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["isDisabled"];
		}else{
			return  settings.fieldDefSettings.isDisabled || false;	
		}		
	},
	
	getHiddenStatus: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["isHidden"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["isHidden"];
		}else{
			return  settings.fieldDefSettings.isHidden || false;	
		}
	},
	
	getCategoryField: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["categoryField"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["categoryField"];
		}else{
			return  settings.fieldDefSettings.categoryField || false;	
		}
	},
	
	getLookupTable: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["lookupTable"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["lookupTable"];
		}else{
			return  "";	
		}
	},
	
	getLookupSize: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["selectSize"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["selectSize"];
		}else{
			return  settings.fieldDefSettings.selectSize || 0;	
		}
	},
	
	getLCT: function(tName, fName){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName]["lcType"] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName]["lcType"];
		}else{
			return  settings.fieldDefSettings.lcType || Runner.controls.constants.LCT_DROPDOWN;
		}
	},
	
	/*getLookupPreload: function(tName, fName){
		if (this.getEditFormat(tName, fName) == Runner.controls.constants.EDIT_FORMAT_LOOKUP_WIZARD){
			return settings.tableSettings[tName]["fieldSettings"][fName].preload || settings.fieldDefSettings.preload || {vals: [], fVal: false};
		}
		return false;
	},*/
	
	getTableData: function(tName, key, defVal){
		return settings.tableSettings[tName][key] || settings.tableDefSettings[key] || defVal;
	},
	
	getFieldData: function(tName, fName, key, defVal){
		if (typeof settings.tableSettings[tName]["fieldSettings"][fName] != 'undefined' && typeof settings.tableSettings[tName]["fieldSettings"][fName][key] != 'undefined'){
			return settings.tableSettings[tName]["fieldSettings"][fName][key];
		}else{
			return  defVal || settings.fieldDefSettings[key];
		}
	},
	
	getGlobalData: function(key, defVal){
		return settings[key] || defVal;
	}
});

Runner.pages.PageSettings = new Runner.pages.PageSettings();
		

Runner.pages.PageFabric = function(baseCfg){
	var cfg = {};
	Runner.apply(cfg, baseCfg);
	
	//console.log(cfg, 'new cfg in page fabric');	
	
	switch (cfg.pageType){
		case Runner.pages.constants.PAGE_LIST:
			if (cfg.fly){				
				return new Runner.pages.ListPageFly(cfg);
			}else if(cfg.pageMode == Runner.pages.constants.LIST_AJAX){
				return new Runner.pages.ListPageAjax(cfg);	
			}else if(cfg.dp){	
				return new Runner.pages.ListPageDP(cfg);
			}else if(cfg.pageMode == Runner.pages.constants.RIGHTS_PAGE){
				return new Runner.pages.RightsPage(cfg);						
			}else if(cfg.pageMode == Runner.pages.constants.MEMBERS_PAGE){
				return new Runner.pages.MembersPage(cfg);	
			}else{
				return new Runner.pages.ListPage(cfg);	
			}						
			break;
		case Runner.pages.constants.PAGE_ADD:
			if (cfg.fly){
				return new Runner.pages.AddPageFly(cfg);
			}else{
				return new Runner.pages.AddPage(cfg);	
			}	
			break;
		case Runner.pages.constants.PAGE_EDIT:
			return new Runner.pages.EditPage(cfg);
			break;
		case Runner.pages.constants.PAGE_VIEW:
			return new Runner.pages.ViewPage(cfg);
			break;
		case Runner.pages.constants.PAGE_SEARCH:
			return new Runner.pages.SearchPage(cfg);
			break;	
		case Runner.pages.constants.PAGE_REPORT:
			return new Runner.pages.ReportPage(cfg);
			break;
		case Runner.pages.constants.PAGE_CHART:
			return new Runner.pages.ChartPage(cfg);
			break;
		case Runner.pages.constants.PAGE_PRINT:
			return new Runner.pages.PrintPage(cfg);
			break;
		case Runner.pages.constants.PAGE_REGISTER:
			return new Runner.pages.RegisterPage(cfg);
			break;	
		case Runner.pages.constants.PAGE_EXPORT:
			return new Runner.pages.ExportPage(cfg);
			break;
		case Runner.pages.constants.PAGE_IMPORT:
			return new Runner.pages.ImportPage(cfg);
			break;
		default:
			cfg.createAsDefault = true;
			return new Runner.pages.RunnerPage(cfg);
			break;
	}
	
};

Runner.pages.getUrl = function(tName, pageType, keys, keyPref){
	var url = Runner.pages.PageSettings.getShortTName(tName)+"_"+pageType+".php",
		i=0,
		key;
	keyPref = keyPref || "editid";	
	for(key in keys){
		url += "?";
		break;
	}
	for(key in keys){
		i++;
		url += keyPref+i+"="+escape(keys[key]);
	}
	return url;
};

/**
 * Base abstract class for all pages
 * @abstract
 */
Runner.pages.RunnerPage = Runner.extend(Runner.util.Observable, {	
	
	tName: '',
	
	pageType: '',
	
	parentPage: null,
	
	modal: false,
	
	pageId: -1,
	
	controlsMap: null,
	
	fly: false,
	
	headerCont: "",
	
	bodyCont: "",
	
	fotterCont: "",
	
	win: null,
	
	winDiv: null,
	
	createAsDefault: false,
	
	destroyOnClose: false,
	
	constructor: function(cfg){
		
		Runner.apply(this, cfg);		
		
		Runner.pages.PageManager.register(this);
				
		this.addEvents('beforeInit', 'afterInit', 'filesLoaded');
	    
	    this.id = this.pageId;	    
	},
	
	destructor: function(){
		// unregister page Object, clean memory
		if (this.win){
			this.win.destroy();
		}
		this.purgeListeners();
		Runner.controls.ControlManager.unregister(this.tName, this.pageId);
	},
	
	init: function(){
		
		this.fireEvent('beforeInit', this);
		
		if(Runner.pages.PageSettings.getTableData(this.tName, "isUseIbox", false) && this.pageType!=Runner.pages.constants.PAGE_ADD && this.pageType!=Runner.pages.constants.PAGE_EDIT && this.pageType!=Runner.pages.constants.PAGE_REGISTER){
			init_ibox();
		}
		
		if (Runner.isSafari){
			$('span.buttonborder').removeClass('buttonborder');
		}
		$('body').addClass("yui-skin-sam");
		window.MaxWindowPage = 3;
		s508pagination(this.id);
		
		//this.initPage();		
		this.initEvents();		
		this.initControls();		
		this.initLookups();			
		
		if (this.createAsDefault){
			this.fireEvent('afterInit', this, this.id);
		}
		setHoverForTR(false,this.id,Runner.pages.PageSettings.getTableData(this.tName, "isUseHighlite", false),Runner.pages.PageSettings.getTableData(this.tName, "listIcons", false),Runner.pages.PageSettings.getTableData(this.tName, "isUseResize", false));
	},
	
	initFlyPage: function(){
		this.initFly();
		this.loadFiles();
	},
	
	loadFiles: function(){
		if (!this.fly){
			return false;
		}
		
		
		if (Runner.pages.PageSettings.getTableData(this.tName, "isUseIbox", false)){
			Runner.util.ScriptLoader.addJS(["include/ibox"]);
			Runner.util.ScriptLoader.loadCSS(["include/ibox"]);
		}
		var fSett = Runner.pages.PageSettings.getTableData(this.tName, "fieldSettings", {});
		for(var fName in fSett){
			
			if (fSett[fName].timePick){
				Runner.util.ScriptLoader.addJS(["include/ui"]);
				Runner.util.ScriptLoader.addJS(["include/jquery.utils"], "include/ui");
				Runner.util.ScriptLoader.addJS(["include/ui.dropslide"], "include/jquery.utils");
				Runner.util.ScriptLoader.addJS(["include/ui.timepickr"], "include/ui.dropslide");
				Runner.util.ScriptLoader.loadCSS(["include/ui.dropslide"]);
			}
			if (fSett[fName].editFormat == Runner.controls.constants.EDIT_DATE_DD_DP || fSett[fName].editFormat == Runner.controls.constants.EDIT_DATE_SIMPLE_DP){			
				Runner.util.ScriptLoader.addJS(["include/yui/calendar"], "include/yui/container");
				Runner.util.ScriptLoader.loadCSS(["include/yui/calendar"]);
				Runner.util.ScriptLoader.loadCSS(["include/yui/calendar-skin"]);
				Runner.util.ScriptLoader.loadCSS(["include/yui/container"]);
				Runner.util.ScriptLoader.loadCSS(["include/yui/container-skin"]);
				Runner.util.ScriptLoader.loadCSS(["include/yui/resize"]);
			}
		}
		
		Runner.util.ScriptLoader.load(this.init, this);
	},
	
	initFly: function(){
		if (!this.fly){
			return false;
		}
		// page renders itself into YUI panel with auto resize and DD
		this.winDiv = document.createElement("DIV");

	    this.win = new YAHOO.widget.Panel(this.winDiv, {
	        draggable: true,
	        height: "400px",
	        width: "500px",	        
	        autofillheight: "body",
	        constraintoviewport: true,
	        modal: this.modal,
	        fixedcenter: true/*,
	        context: ["showbtn", "tl", "bl"]*/
	    });
		this.win.setBody(this.bodyCont);
		this.win.setHeader(this.headerCont);
		this.win.setFooter(this.fotterCont);
	    this.win.render(document.body);		
	 	this.win.bringToTop();
	    
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
		
		if (this.destroyOnClose){
			// for closure
			var tName = this.tName,
				pageId = this.pageId;
				
			this.win.subscribe('hide', function(){
				setTimeout(function(){
					Runner.pages.PageManager.unregister(tName, pageId);
				}, 5);			
			});	
		}
	},
	
	show: function(){
		if (!this.win){
			return this.initFly();
		}
		this.win.show();
		return true;
	},
	
	close: function(){
		if (!this.win){
			return false
		}
		this.win.hide();
		return true;
	
	},
		
	initEvents: function(){
		
	},
	
	
	addSettings: function(settings){
		Runner.pages.PageSettings.addSettings(settings, this.tName, this.pageType);
	},
	
	/**
	 * Controls Class fabric with lazy initialization
	 * may be should be a function in Runner.controls.FileControl and just used here
	 */
	initControls: function(){		
		for(var i=0; i<this.controlsMap.controls.length; i++){
			this.controlsMap.controls[i].table = this.tName;
			var ctrl = Runner.controls.ControlFabric(this.controlsMap.controls[i]);			
		}
	},
	
	initLookups: function(){
		
		var pageCtrls = Runner.controls.ControlManager.getAt(this.tName, this.pageId);
		// init dependeces and preload
		for(var i=0; i<pageCtrls.length; i++){
			if (!pageCtrls[i].isLookupWizard){
				continue;
			}
			if (pageCtrls[i].parentFieldName && pageCtrls[i].skipDependencies !== true){
				var parentCtrl = Runner.controls.ControlManager.getAt(this.tName, this.pageId, pageCtrls[i].parentFieldName);
				pageCtrls[i].setParentCtrl(parentCtrl); 
				if (parentCtrl && parentCtrl.isLookupWizard){
					parentCtrl.addDependentCtrls([pageCtrls[i]]);	
				}				
			}
		}
	},
	
	
	
	initCustomButtons: function(){
		for(var mapInd in this.controlsMap.buttons){
			Runner.util.ScriptLoader.addJS(['include/json']);
			if (Runner.debugMode === true){
				Runner.util.ScriptLoader.addJS(['include/runnerJS/pages/RunnerEvent']);
				Runner.util.ScriptLoader.addJS(['include/runnerJS/pages/button'],'include/runnerJS/pages/RunnerEvent');	
			}else{
				Runner.util.ScriptLoader.addJS(['include/runnerJS/pages/RunnerControls']);
				Runner.util.ScriptLoader.addJS(['include/runnerJS/pages/button'],'include/runnerJS/pages/RunnerControls');	
			}
			break;
		}
		
		var hnFileNames = [];
		
		for(var mapInd in this.controlsMap.buttons){
			hnFileNames.push('include/runnerJS/handlers/'+this.controlsMap.buttons[mapInd]);				
		}
		
		Runner.util.ScriptLoader.addJS(hnFileNames, 'include/runnerJS/pages/button');	
		
		Runner.util.ScriptLoader.load(this.pageId);
	}
	
});
/**
 * Search page class
 */

Runner.pages.SearchPage = Runner.extend(Runner.pages.RunnerPage, {
	
	constructor: function(cfg){
		Runner.pages.SearchPage.superclass.constructor.call(this, cfg);	
	},
	
	init: function(){
		Runner.pages.SearchPage.superclass.init.call(this);		
		this.initButtons();
		this.initSearch();
		this.fireEvent('afterInit', this, this.id);
	},
	
	initButtons: function(){
		var pageObj = this;
		$("#searchButton"+this.pageId).bind("click", function(e){
			pageObj.searchController.submitSearch();
		});
		
		$("#resetButton"+this.pageId).bind("click", function(e){
			pageObj.searchController.resetCtrls();
		});
		
		$("#backButton"+this.pageId).bind("click", function(e){
			pageObj.searchController.returnSubmit();
		});	
	},
	
	initSearch: function(){
		this.searchController = new Runner.search.SearchForm({
			id: this.pageId,
			tName: this.tName,
			fNamesArr: this.controlsMap.search.allSearchFields,
			shortTName: this.shortTName,
			usedSrch: this.controlsMap.search.isUsedSearch,
			searchType: 'advanced',
			panelSearchFields: this.controlsMap.search.panelSearchFields,
			pageType: this.controlsMap.search.submitPageType,
			baseParams: this.controlsMap.search.baseParams || {}
		});		
		
		this.searchController.init(this.controlsMap.search.searchBlocks);
	}
});
Runner.pages.ViewPage = Runner.extend(Runner.pages.RunnerPage, {
	
	keys: null,
	
	prevKeys: null,
	
	nextKeys: null,
	
	pageType: Runner.pages.constants.PAGE_VIEW,
	/**
	 * Element, container to which search panel rendered
	 * @type 
	 */
	searchPanelEl: null,
	
	constructor: function(cfg){
		Runner.pages.ViewPage.superclass.constructor.call(this, cfg);
		
		this.keys = cfg.keys || Runner.pages.PageSettings.getTableData(this.tName, 'keys', {});
		this.prevKeys = Runner.pages.PageSettings.getTableData(this.tName, 'prevKeys', {});
		this.nextKeys = Runner.pages.PageSettings.getTableData(this.tName, 'nextKeys', {});
		window.recKeysObj = this.keys;
	},
	
	init: function(){
		Runner.pages.ViewPage.superclass.init.call(this);		
		this.initMap();
		this.initButtons();
		this.initDetails();		
		this.fireEvent('afterInit', this, this.id);
	},
	
	initButtons: function(){
		var pageObj = this;

		$("#nextButton"+this.id).bind("click", function(e){
			UnlockRecord(pageObj.shortTName+'_'+pageObj.pageType+'.php', pageObj.nextKeys,'',function(){
				window.location.href = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, pageObj.nextKeys);
			});
		});	
		
		$("#prevButton"+this.id).bind("click", function(e){
			UnlockRecord(pageObj.shortTName+'_'+pageObj.pageType+'.php', pageObj.prevKeys,'',function(){
				window.location.href = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, pageObj.prevKeys);
			});
		});	
		
		$("#backButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);	
			UnlockRecord(pageObj.shortTName+'_'+pageObj.pageType+'.php', pageObj.keys, '', function(){
				window.location.href = Runner.pages.getUrl(pageObj.tName, Runner.pages.constants.PAGE_LIST, {})+"?a=return";
			});	
		});
	},
	
	initMap: function(){
		if (!this.controlsMap.gMaps || !this.controlsMap.gMaps.isUseGoogleMap){
			return false;
		}
		
		this.mapManager = new Runner.controls.MapManager(this.controlsMap.gMaps);
		this.mapManager.init();
	},
	
	initDetails: function(){
		if (Runner.pages.PageSettings.getTableData(this.tName, "isShowDetails", false)){
			window.dpObj = new dpInlineOnAddEdit({
				'mTableName':this.tName,
				'mPageType':Runner.pages.constants.PAGE_VIEW
			});
			window.dpObj.init();
		}
	}
});
/**
 * Base abstract class for all pages with editing content, add, edit etc.
 * ????? ??????????? ???
 */

Runner.pages.EditorPage = Runner.extend(Runner.pages.RunnerPage, {
	
	baseParams: null,
	
	tabs: null,
	
	constructor: function(cfg){
		
		Runner.pages.EditorPage.superclass.constructor.call(this, cfg);
		
		this.baseParams = cfg.baseParams || {};
		this.baseParams['id'] = this.id;
		this.baseParams["editType"] = this.baseParams["editType"] || this.editType;
		this.submitUrl = Runner.pages.PageSettings.getShortTName(this.tName)+"_"+this.pageType+".php";
		
		this.addEvents("beforeSave", "afterSave");
		if (this.fly){
			
			this.baseParams['onFly'] = 1;
		
			this.on('beforeSave', function(form, fieldControls, page){
				form.standardSubmit = false;
				return true;
			});
		}
	},
	
	init: function(){
		Runner.pages.EditorPage.superclass.init.call(this);	
		if (this.beforeSave){
			this.on({'beforeSave': this.beforeSave});
		}
		if (this.afterSave){
			this.on({'afterSave': this.afterSave});
		}
		
		this.initButtons();
		this.initTabs();
		
		Runner.controls.ControlManager.getAt(this.tName, this.id)[0].setFocus();		
	},
	
	initButtons: function(){		
		$("#saveButton"+this.id).bind("click", {page: this}, this.saveHn);			
	},
	
	saveHn: function(e){
		Runner.Event.prototype.stopEvent(e);
		
		var page = e.data.page;
		
		var form = new Runner.form.BasicForm({
			submitUrl: e.data.page.submitUrl,	
			standardSubmit: true,
			isFileUpload: true,
			method: 'POST',
			id: e.data.page.pageId,
			baseParams: e.data.page.baseParams,
			fieldControls: Runner.controls.ControlManager.getAt(e.data.page.tName, e.data.page.pageId),
			successSubmit: {
		        fn: function(respObj, formObj, fieldControls){
					var evRes = page.fireEvent("afterSave", respObj, formObj, fieldControls, page);
					if (evRes !== false){
						page.close();
					}		
					formObj.destructor();
				}
		    },
			submitFailed: {
		        fn: function(formObj, fieldControls){
		        	page.fireEvent("afterSave", {success: false}, formObj, fieldControls, page);
				}
		    },
			beforeSubmit: {
		        fn: function(formObj){
		        	return page.fireEvent("beforeSave", formObj, formObj.fieldControls, page);
				}
		    }
		});
		
		form.submit();	
	},
	
	initTabs: function(){

		this.tabs = {};
		
		for(var i=0; i<this.controlsMap.tabs.length; i++){
			this.tabs[this.controlsMap.tabs[i]] = new YAHOO.widget.TabView(this.controlsMap.tabs[i]); 
		}
		// init sections
		for(var i=0; i<this.controlsMap.sections.length; i++){
			(function(secId){
				$("#"+secId+"Butt").bind("click", function(e){
					if ($(this).attr("src") == 'include/img/minus.gif'){
						$("#"+secId).hide();
						$(this).attr("src", 'include/img/plus.gif');
					}else{
						$("#"+secId).show();
						$(this).attr("src", 'include/img/minus.gif');
					}
					
				});
			})(this.controlsMap.sections[i]);			 
		}
	}
	
});
Runner.pages.AddPageFly = Runner.extend(Runner.pages.EditorPage, {
	
	pageType: Runner.pages.constants.PAGE_ADD,
	
	fName: "",
	
	parentFieldName: "", 
	
	editType: Runner.pages.constants.ADD_ONTHEFLY,
	
	constructor: function(cfg){
		Runner.pages.AddPageFly.superclass.constructor.call(this, cfg);		
		
		this.baseParams["a"] = "added";
	},
	
	init: function(){
		Runner.pages.AddPageFly.superclass.init.call(this);		
		this.fireEvent('afterInit', this, this.id);
	},
	
		
	initButtons: function(){
		Runner.pages.AddPageFly.superclass.initButtons.call(this);
		$("#cancelButton"+this.id).bind("click", {page: this}, function(e){
			Runner.Event.prototype.stopEvent(e);	
			e.data.page.close();
		});
	},
	
	saveHn: function(e){
		Runner.pages.AddPageFly.superclass.saveHn.call(this, e);		
	}
	
});
Runner.pages.AddPage = Runner.extend(Runner.pages.EditorPage, {
	
	pageType: Runner.pages.constants.PAGE_ADD,
	
	baseParams: null,
	
	constructor: function(cfg){
		Runner.pages.AddPage.superclass.constructor.call(this, cfg);
		// fields for add to form add to this.baseParams
		this.baseParams['a'] = "added";
	},
	
	init: function(){
		Runner.pages.AddPage.superclass.init.call(this);	
		this.initDetails();
		this.fireEvent('afterInit', this, this.id);
	},
	
	initDetails: function(){
		if (Runner.pages.PageSettings.getTableData(this.tName, "isShowDetails", false)){		
			window.dpObj = new dpInlineOnAddEdit({
				'mTableName':this.tName,
				'mShortTableName':Runner.pages.PageSettings.getShortTName(this.tName), 
				'mPageType':Runner.pages.constants.PAGE_ADD,
				'mMessage':'', 
				'mId':this.pageId,
				'ext':Runner.pages.PageSettings.getGlobalData('ext',""),
				'dMessages':'', 
				'dCaptions':[]
			});
			window.dpObj.init();
			this.baseParams['editType'] = 'addmaster';		
		}
	},
	
	initButtons: function(){		
		Runner.pages.AddPage.superclass.initButtons.call(this);	
		$("#backButton"+this.id).bind("click", {page: this}, function(e){
			Runner.Event.prototype.stopEvent(e);	
			window.location.href = Runner.pages.PageSettings.getShortTName(e.data.page.tName) + '_' + Runner.pages.constants.PAGE_LIST + '.php?a=return';	
		});
	},
	
	saveHn: function(e){
		Runner.Event.prototype.stopEvent(e);						
		var isShowDetails = Runner.pages.PageSettings.getTableData(e.data.page.tName, "isShowDetails", false);
		
		if(!window.dpObj || !window.dpObj.isSbmMaster){
			var form = new Runner.form.BasicForm({
				submitUrl: e.data.page.submitUrl,	
				standardSubmit: !isShowDetails,
				isFileUpload: true,
				method: 'POST',
				id: e.data.page.pageId,
				baseParams: e.data.page.baseParams,
				fieldControls: Runner.controls.ControlManager.getAt(e.data.page.tName, e.data.page.pageId),
			});
	
			if(isShowDetails){
				form.on('beforeSubmit', function(basicForm){
					var valRes = basicForm.validate();
					if (!valRes){
						return false;
					}
					// validate details and save, test param 
					var ids = window.dpObj.dpParFromM['ids'],
						vRes = true;
					
					for(var i=0;i<ids.length;i++)
					{
						if (!window['dpInline'+ids[i]].inlineAddChangeContent)
							continue;
						if (!window['dpInline'+ids[i]].inlineAdd.validate())
							vRes = false;
					}
					return vRes;
					
				}, e.data.page);
				
				form.on('successSubmit', function(responseObj, basicForm, fieldControls){
					basicForm.destructor();
					if(responseObj.success){
						window.dpObj.isSbmMaster = true;
						window.dpObj.Opts.mSavedValues = {};
						for(var i=0;i<responseObj.fields.length;i++)
							window.dpObj.Opts.mSavedValues[responseObj.fields[i]] = responseObj.vals[responseObj.fields[i]];
											
						//hide captha
						if(responseObj.hideCaptha)
							window.dpObj.hideCaptcha();
						
						//save detail' records	
						window.dpObj.mKeys = responseObj.mKeys;
						window.dpObj.saveAllDetail(e);
					}else{					
						if(responseObj.captha===false)
							window.dpObj.showHideInvalidCaptcha('show');
						else{
							window.dpObj.Opts.mMessage = responseObj.error;
							window.dpObj.showMessage();
							window.dpObj.showHideInvalidCaptcha('hide');
						}
					}					
				}, e.data.page);
			}			
			form.submit();
		// master saved save details
		}else if(isShowDetails){

			window.dpObj.saveAllDetail(e);
		}
	}
	
});

Runner.pages.EditPage = Runner.extend(Runner.pages.EditorPage, {
	
	keys: null,
	
	prevKeys: null,
	
	nextKeys: null,
	
	pageType: Runner.pages.constants.PAGE_EDIT,
	
	details: null,
	
	constructor: function(cfg){
		Runner.pages.EditPage.superclass.constructor.call(this, cfg);
		
		this.keys = cfg.keys || Runner.pages.PageSettings.getTableData(this.tName, 'keys', {});
		this.prevKeys = Runner.pages.PageSettings.getTableData(this.tName, 'prevKeys', {});
		this.nextKeys = Runner.pages.PageSettings.getTableData(this.tName, 'nextKeys', {});
		this.baseParams['a'] = "edited";
		
		var i=0;
		for(var key in this.keys){
			i++;
			this.baseParams["editid"+i] = this.keys[key];	
		}
		
	},
	
	init: function(){
		Runner.pages.EditPage.superclass.init.call(this);
		this.initCtrlEvents();
		this.initDetails();
		this.fireEvent('afterInit', this, this.id);
	},
	
	
	initButtons: function(){
		Runner.pages.EditPage.superclass.initButtons.call(this);
		
		var pageObj = this;
		
		$("#resetButton"+this.id).bind("click", function(e){
			$('#next'+pageObj.id).attr('style','').attr('disabled','');
			$('#prevButton'+pageObj.id).attr('style','').attr('disabled','');
			Runner.controls.ControlManager.resetControlsForTable(pageObj.tName);	
		}).bind("mouseover", function(e){
			this.focus();
		});
		
		
		$("#nextButton"+this.id).bind("click", function(e){
			UnlockRecord(pageObj.shortTName+'_'+pageObj.pageType+'.php', pageObj.keys, '', function(){
				window.location.href = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, pageObj.nextKeys);
			});
		});	
		
		$("#prevButton"+this.id).bind("click", function(e){
			UnlockRecord(pageObj.shortTName+'_'+pageObj.pageType+'.php', pageObj.prevKeys, '', function(){
				window.location.href = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, pageObj.prevKeys);
			});
		});	
		
		$("#backButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);	
			UnlockRecord(pageObj.shortTName+'_'+pageObj.pageType+'.php', pageObj.nextKeys, '', function(){
				window.location.href = Runner.pages.PageSettings.getShortTName(pageObj.tName) + '_' + Runner.pages.constants.PAGE_LIST + '.php?a=return';
			});	
		});
	},
	
	initCtrlEvents: function(){
		var ctrls = Runner.controls.ControlManager.getAt(this.tName, this.pageId),
			cntrlType, 
			eventName = 'change', 
			singleFire = false, 
			delay = 0;
		
		for (var i = 0; i < ctrls.length; i++){
			
			cntrlType = ctrls[i].getControlType(), eventName = 'change', singleFire = false, delay = 0;
			
			if(cntrlType=='checkbox' || cntrlType=='radio'){
				eventName = 'click';
			}else if(cntrlType=='text' || cntrlType=='password' || cntrlType=='textarea'){
				eventName = 'keyup';
				delay = 60;
				ctrls[i].on('change', this.prevNextButtonHandler, {single: true, timeout: 0, scope: this});
			}else if(cntrlType=='RTE'){
				eventName = 'blur';
				delay = 5000;
			}
			ctrls[i].on(eventName, this.prevNextButtonHandler, {single: singleFire, timeout: delay, scope: this});
		}
	},
	
	prevNextButtonHandler: function(e){
		// for click event on checkbox, do not use stopEvent, that cause that checkbox won't be checked
		//this.stopEvent(e);
		//	skip arrows, tab keys
		if(e && (e.type=='keyup' || e.type=='keypress' || e.type=='keydown')){
			if(e.keyCode>=33 /*page up*/ && e.keyCode<=40 /* down arrow */ || e.keyCode==9 /*tab*/)
				return true;
		}
		var prev = $('#prevButton'+this.id),
			next = $('#nextButton'+this.id);
		
		prev.css('background','#dcdcdc url(\"images/sortprev.gif\") center no-repeat').
			css('color','#dcdcdc').
				css('cursor','default').
					attr('disabled','disabled');
		
		next.css('background','#dcdcdc url("images/sortnext.gif") center no-repeat').
			css('color','#dcdcdc').
				css('cursor','default').
					attr('disabled','disabled');
		
		return true;
	},
	/**
	 * Init dp object for add, edit or view pages
	 * 
	 */
	initDetails: function(){
		//console.log('initDetails on master edit page - ',this.tName);
		if (Runner.pages.PageSettings.getTableData(this.tName, "isShowDetails", false))	{
			window.dpObj = new dpInlineOnAddEdit({
				'mPageType':Runner.pages.constants.PAGE_EDIT,
				'mTableName':this.tName,
				'mId':this.id,
				'dMessages':'',
				'dCaptions':[]
			});		
			window.dpObj.init();
		}
	},
	
	saveHn: function(e){
		var pageObj = e.data.page;
		if(Runner.pages.PageSettings.getTableData(pageObj.tName, "isShowDetails", false)){	
			// validate master
			var masterCtrls = Runner.controls.ControlManager.getAt(e.data.page.tName, e.data.page.pageId),
				vRes,
				validationPassed = true;
			for(var i=0; i<masterCtrls.length;i++){
				vRes = masterCtrls[i].validate();
				if (!vRes.result){
					validationPassed = false;
				}
			}
			if (validationPassed){
				window.dpObj.saveAllDetail(e);
			}
		}else{	
			Runner.pages.EditPage.superclass.saveHn.call(this, e);
		}
	}		
});

/**
 * Base abstract class for all pages with showing content, list, view etc.
 */
Runner.pages.DataPageWithSearch = Runner.extend(Runner.pages.RunnerPage, {
	/**
	 * Element, container to which grid is rendered
	 * @type 
	 */
	gridEl: null,
	
	searchController: null,
	
	shortTName: "",
	/**
	 * Element, container to which search panel rendered
	 * @type 
	 */
	searchPanelEl: null,
	
	
	constructor: function(cfg){
		Runner.pages.DataPageWithSearch.superclass.constructor.call(this, cfg);
		this.shortTName = Runner.pages.PageSettings.getShortTName(this.tName);
		this.gridEl = $(getParentTableObj(this.id)).
			bind("click", this.gridClickHn.createDelegate(this, [], true));
	},
	
	
	init: function(){
		Runner.pages.DataPageWithSearch.superclass.init.call(this);		
		
		this.initPagination();
		this.initSearch();		
	},
	
	gridClickHn: function(e){
		this.largeTextOpenerDelegate(e);
	},
	
	largeTextOpenerDelegate: function(e){			
		var target = Runner.Event.prototype.getTarget(e);
		if(target.nodeName != "A" || !$(target).attr("query")) {
			return false;
		}
		Runner.Event.prototype.stopEvent(e);
		
		var query = $(target).attr("query");				
		var winId = Runner.genId();
		
		var fullTextWin = new YAHOO.widget.Panel("fullText"+winId, {
	        draggable: true,
	        height: "400px",
	        width: "500px",	        
	        //autofillheight: "body",
	        //constraintoviewport: true, 
	        fixedcenter: true
	    });		    
	    
	    $.get(query, {id: this.id, rndVal: Math.random()}, function(respObj){
	    	respObj = JSON.parse(respObj);
	    	if (respObj.success){
				fullTextWin.setBody(respObj.textCont);
	    	}else{
	    		fullTextWin.setBody(respObj.error || "Server error");
	    	}
	    	fullTextWin.render(document.body);
	    	fullTextWin.bringToTop();
	 		
	    	
		    fullTextWin.subscribe('drag', function(eventName, args, newScope){
		 		this.bringToTop();
	        });
		 
		 
	        var resize = new YAHOO.util.Resize("fullText"+winId, {
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
 
            }, fullTextWin, true);
		 
			resize.on("resize", function(args) {
				console.log(args, 'args in resize');
	            var panelHeight = args.height;
	            this.cfg.setProperty("height", panelHeight + "px");
	            var panelWidth = args.width;
	            this.cfg.setProperty("width", panelWidth + "px");
       		}, fullTextWin, true);		
		});	
		
		fullTextWin.setHeader("&nbsp;");
		fullTextWin.setFooter("&nbsp;");		 		
		
	},
	
	initSearch: function(){		
		this.searchController = new Runner.search.SearchController({
			id: this.pageId,
			tName: this.tName,
			fNamesArr: this.controlsMap.search.allSearchFields,
			shortTName: this.shortTName,
			usedSrch: this.controlsMap.search.usedSrch,
			panelSearchFields: this.controlsMap.search.panelSearchFields,
			useSuggest: Runner.pages.PageSettings.getTableData(this.tName, "ajaxSuggest", true),
			pageType: this.pageType
		});		
		
		this.searchController.init(this.controlsMap.search.searchBlocks);
	},
	
	initPagination: function(){
		// add delegated events to pagination links
		$("table[@name=paginationTable"+this.pageId+"]").bind("click", {pageObj: this}, function(e){
			Runner.Event.prototype.stopEvent(e);	
			var target = Runner.Event.prototype.getTarget(e);
			if(target.nodeName != "A") {
				return false;
			}
			var pageNum = $(target).attr("pageNum"),
				pageObj = e.data.pageObj;
			var url = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, {})+"?goto="+pageNum;
			window.location.href = url;
		});
	}
	
});
Runner.pages.ListPageFly = Runner.extend(Runner.pages.DataPageWithSearch, {
	
	lookupCtrl: null,
	
	lookupBaseParams: null,
	
	constructor: function(cfg){
		
		Runner.pages.ListPageFly.superclass.constructor.call(this, cfg);
		this.listFields = Runner.pages.PageSettings.getTableData(this.tName, 'listFields', []);
		
		this.on("afterInit", function(pageObj){
			
			this.lookupCtrl.lookupVals = this.controlsMap.lookupVals;
			
			this.lookupCtrl.initLinks(this.pageId);
			
			this.lookupCtrl.lookupSelectField = this.controlsMap.lookupSelectField;
			this.lookupCtrl.dispFieldAlias = this.controlsMap.dispFieldAlias;
			this.lookupCtrl.linkField = this.controlsMap.linkField;
			this.lookupCtrl.dispField = this.controlsMap.dispField;
			
		}, this);
		
		this.lookupBaseParams = {
			parId: this.lookupCtrl.pageId, 
			table: this.lookupCtrl.table, 
			field: this.lookupCtrl.fieldName, 
			mode: "lookup",
			category: this.lookupCtrl.parentFieldName,
			control: "control", 
			editMode: 'inline',
			id: this.pageId
		};
		
	},
	
	
	destructor: function(){
		Runner.pages.ListPageFly.superclass.destructor.call(this);
		this.win = null;
		this.winDiv = null;
		this.lookupCtrl.pageId = -1;
	},
	
	init: function(){
		Runner.pages.ListPageFly.superclass.init.call(this);	
				
		this.initInline();
		
		this.initButtons();
		this.initSorting(this.pageId);
		this.fireEvent('afterInit', this, this.id);		
	},
	
	initSearch: function(){
		//Runner.pages.ListPageFly.superclass.initSearch.call(this);
		this.searchController = new Runner.search.SearchController({
			id: this.pageId,
			tName: this.tName,
			fNamesArr: this.controlsMap.search.allSearchFields,
			shortTName: this.shortTName,
			usedSrch: this.controlsMap.search.usedSrch,
			panelSearchFields: this.controlsMap.search.panelSearchFields,
			ajaxSubmit: true,
			useSuggest: false,
			pageType: this.pageType
		});		
		
		
		
		this.searchController.init(this.controlsMap.search.searchBlocks);
		
		this.searchController.srchForm.baseParams = this.lookupBaseParams;
				
		this.searchController.srchForm.on("beforeSubmit", function(form){
			
			/*if (!this.searchController.loadingMask){
				this.searchController.loadingMask = new YAHOO.widget.Panel("wait"+this.id, {			
					width: "240px", 
	                fixedcenter: true, 
	                close: false, 
	                draggable: false, 
	                modal: true,
	                visible: false
	            });
	    		
	            this.searchController.loadingMask.setHeader("Loading, please wait...");
	            this.searchController.loadingMask.setBody("<img src=\"http://l.yimg.com/a/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
	            this.searchController.loadingMask.render(this.winDiv);
			}
            this.searchController.loadingMask.show();*/
			
			form.baseParams["id"] = Runner.genId();
			runLoading(this.id, this.win.body, 1);
			this.lookupBaseParams.id = form.baseParams["id"];
		}, this);
		

		this.searchController.on('afterSearch', function(respObj, srchController, srchForm){
			//this.searchController.loadingMask.hide();
			stopLoading(this.id, 1);
			
			this.pageReloadHn(respObj);
			if (this.searchController.usedSrch){
				this.searchController.showShowAll();
			}else{
				this.searchController.hideShowAll();
			}			
		}, this);
		
		this.searchController.srchForm.on('submitFailed', function(){	
			stopLoading(this.id, 1);
			console.log(this, arguments, 'submitFailed search args');
		}, this);
			
	},
	
	initInline: function(){
		
		var permis = Runner.pages.PageSettings.getTableData(this.tName, "permissions", {});
		
		if (Runner.pages.PageSettings.getTableData(this.tName, "isInlineAdd", true) && permis['add']){
			
			this.inlineAdd = new Runner.util.inlineEditing.InlineAdd({
				tName: this.tName,	
				shortTName: this.shortTName,	
				id: this.pageId,
				fNames: this.listFields,
				rows: this.controlsMap.gridRows,
				inlineEditObj: this.inlineEdit,
				totalFields: Runner.pages.PageSettings.getTableData(this.tName, "totalFIelds", []),				
				lookupTable: this.tName,
				lookupField: this.lookupCtrl.fieldName,
				categoryValue: this.lookupCtrl.getValue()
			});
			
			this.inlineAdd.init();
			
			this.inlineAdd.on("beforeSetVals", function(row, fields, data){
				if (data[this.lookupCtrl.dispFieldAlias]){
					data[this.lookupCtrl.dispFieldAlias] = '<a type="lookupSelect'+this.searchController.srchForm.baseParams.id+'">'+data[this.lookupCtrl.dispFieldAlias]+'</a>';
				}else if(data[this.lookupCtrl.dispField]){
					data[this.lookupCtrl.dispField] = '<a type="lookupSelect'+this.searchController.srchForm.baseParams.id+'">'+data[this.lookupCtrl.dispField]+'</a>';
				}
			}, this);
			
			this.inlineAdd.on("afterSubmit", function(vals, fields, keys){
				var newInd = this.lookupCtrl.addLookupVal(vals[this.lookupCtrl.linkField], vals[this.lookupCtrl.dispFieldAlias] || vals[this.lookupCtrl.dispField]);
				var links = $('a[@type="lookupSelect' + this.searchController.srchForm.baseParams.id + '"]');
				var link = $(links[0]);				
				
				this.lookupCtrl.initLink(link, newInd);
				
			}, this);
		}
	},
	
	initSorting: function(pageId){
		for(var i=0; i<this.listFields.length; i++){
			$("#order_"+Runner.goodFieldName(this.listFields[i])+"_"+pageId).bind("click", {pageObj: this}, function(e){				
				Runner.Event.prototype.stopEvent(e);
				runLoading(this.id, getParentTableObj(this.id), 1);
				var pageObj = e.data.pageObj;
				
				$.ajax({
					url: this.href, 
					type: "GET",
					success: function(respObj){
						pageObj.pageReloadHn.call(pageObj, respObj)
					},
					dataType: "json",
					data: pageObj.lookupBaseParams
				});
			});			
		}
	},
	
	initButtons: function(){
		
	},
	
	initPagination: function(pageId){
		if (typeof pageId == 'undefined'){
			pageId = this.id;
		}
		$("table[@name=paginationTable"+pageId+"]").bind("click", {pageObj: this}, function(e){
			Runner.Event.prototype.stopEvent(e);	
			runLoading(this.id, getParentTableObj(this.id), 1);
			var target = Runner.Event.prototype.getTarget(e);
			if(target.nodeName != "A") {
				return false;
			}
			var pageNum = $(target).attr("pageNum");
				pageObj = e.data.pageObj;
						
			var url = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, {})+"?goto="+pageNum;
			pageObj.lookupBaseParams.id = Runner.genId();
			// ajax page reload	
			$.ajax({
				url: url, 
				type: "GET",
				success: function(respObj){
					pageObj.pageReloadHn.call(pageObj, respObj)
				},
				dataType: "json",
				data: pageObj.lookupBaseParams
			});
		});
	},
	
	pageReloadHn: function(respObj){
		stopLoading(this.id, 1);
		if (respObj.success){
			Runner.setIdCounter(respObj.idStartFrom);
			//cut grid cont
			var gridCont = $("#grid_block"+this.lookupBaseParams.id, respObj.html).html();
			$("#grid_block"+this.pageId).html(gridCont);
			// cut pagination
			var paginationTable = $(respObj.html).find("#pagination_block"+this.lookupBaseParams.id).find('table');			
			var pagTablesName = $("#pagination_block"+this.pageId).find('table').attr("name");
			var pagTables = $("table[@name="+pagTablesName+"]").replaceWith(paginationTable);
			
			this.initPagination(this.lookupBaseParams.id);
			this.initSorting(this.lookupBaseParams.id);
			// set new vals
			this.lookupCtrl.lookupVals = respObj.controlsMap[this.tName][this.pageType][this.lookupBaseParams.id].lookupVals;			
			this.lookupCtrl.initLinks(this.lookupBaseParams.id);		
			
			this.controlsMap.gridRows = respObj["controlsMap"][this.tName][this.pageType][this.lookupBaseParams.id].gridRows;
			
			if (this.inlineAdd){
				this.inlineAdd.reInit(this.lookupBaseParams.id, this.controlsMap.gridRows);
			}
			
			this.searchController.usedSrch = respObj.controlsMap[this.tName][this.pageType][this.lookupBaseParams.id].search.usedSrch;
			
		}else{
			this.win.setBody("REQUEST FAILED");
		}	
	}
});		
Runner.pages.ListPage = Runner.extend(Runner.pages.DataPageWithSearch, {
	
	inlineEdit: null,
	
	pageType: Runner.pages.constants.PAGE_LIST,
	
	mapManager: null,
	
	constructor: function(cfg){
		
		Runner.pages.ListPage.superclass.constructor.call(this, cfg);
		this.listFields = Runner.pages.PageSettings.getTableData(this.tName, 'listFields', []);
		
	},
	
	init: function(){
		Runner.pages.ListPage.superclass.init.call(this);
		this.initResize();
		this.initInline();		
		this.initButtons();
		this.initSorting();
		this.initMaps();
		this.initDetails();		
		this.fireEvent('afterInit', this, this.id);		
	},
	
	
	initMaps: function(){
		if (!this.controlsMap.gMaps || !this.controlsMap.gMaps.isUseGoogleMap){
			return false;
		}
		
		this.mapManager = new Runner.controls.MapManager(this.controlsMap.gMaps);
		this.mapManager.init();
		
		if (this.inlineEdit){	
		
			this.inlineEdit.on('afterSubmit', function(vals, fields, keys, recId){
				for(var i=0; i<fields.length; i++){
					if (this.isFieldIsMap(fields[i])){
						var mapDiv = this.getMapDiv("FIELD_MAP", recId, fields[i]);
						var span = $("#edit"+recId+"_"+fields[i]);
						$(mapDiv).appendTo(span);						
						this.initMap(mapDiv.id);
					}else if(this.isFieldCenterLink(fields[i])){
						var mapCenterLink = this.getCenterLink(recId, vals[fields[i]], this.isFieldCenterLink(fields[i]));
						var span = $("#edit"+recId+"_"+fields[i]);
						$(mapCenterLink).appendTo(span);
						this.initCenterLink($(mapCenterLink));
					}
				}
				this.updateAfterEdit(recId, vals);	
			}, this.mapManager);
		}
		
		if (this.inlineAdd){	
		
			this.inlineAdd.on('afterSubmit', function(vals, fields, keys, recId){
				var j=0,
					viewQuery = "";
				for(key in keys){
					j++;
					viewQuery += "editid"+i+"="+keys[key];
				}
				var recordVals = {viewKey: viewQuery, recId: recId};
				Runner.apply(recordVals, vals);						
				this.updateAfterAdd(recId, vals);
				
				for(var i=0; i<fields.length; i++){
					if (this.isFieldIsMap(fields[i])){
						var mapDiv = this.getMapDiv("FIELD_MAP", recId, fields[i]);
						var span = $("#edit"+recId+"_"+fields[i]);
						$(mapDiv).appendTo(span);						
						this.initMap(mapDiv.id);						
						this.refreshMaps(recId, viewQuery);
					}else if(this.isFieldCenterLink(fields[i])){
						var mapCenterLink = this.getCenterLink(recId, vals[fields[i]], this.isFieldCenterLink(fields[i]));
						var span = $("#edit"+recId+"_"+fields[i]);
						$(mapCenterLink).appendTo(span);
						this.initCenterLink($(mapCenterLink));
					}
				}				
			}, this.mapManager);
		}
	},
	
	initInline: function(){	
		var permis = Runner.pages.PageSettings.getTableData(this.tName, "permissions", {});
		if (Runner.pages.PageSettings.getTableData(this.tName, "isInlineEdit", false) && permis['edit']){
			
			this.inlineEdit = new Runner.util.inlineEditing.InlineEdit({
				tName: this.tName,	
				shortTName: this.shortTName,	
				id: this.pageId,
				fNames: this.listFields,
				rows: this.controlsMap.gridRows,
				totalFields: Runner.pages.PageSettings.getTableData(this.tName, "totalFields", []),				
				showEditInPopup: Runner.pages.PageSettings.getTableData(this.tName, "showEditInPopup", false),
				showViewInPopup: Runner.pages.PageSettings.getTableData(this.tName, "showViewInPopup", false)			
			});
			
			this.inlineEdit.init();		
		}
		
		if (Runner.pages.PageSettings.getTableData(this.tName, "isInlineAdd", false) && permis['add']){

			this.inlineAdd = new Runner.util.inlineEditing.InlineAdd({
				tName: this.tName,	
				shortTName: this.shortTName,	
				id: this.pageId,
				fNames: this.listFields,
				rows: this.controlsMap.gridRows,
				inlineEditObj: this.inlineEdit,
				totalFields: Runner.pages.PageSettings.getTableData(this.tName, "totalFields", []),				
				showEditInPopup: Runner.pages.PageSettings.getTableData(this.tName, "showEditInPopup", false),
				showViewInPopup: Runner.pages.PageSettings.getTableData(this.tName, "showViewInPopup", false)
			});
			
			this.inlineAdd.init();	
			
			if (Runner.pages.PageSettings.getTableData(this.tName, "isUseDP", false)){
				this.inlineAdd.on("afterSubmit", function(vals, fields, keys, id, response){
					if (response.noKeys !== true){
						window['dpInline'+this.pageId].linksAfterInlineAdd(id,response.detKeys);
					}					
				}, this);
			}
			
		}
		
	},
	
	/**
	 * Init details for master list page
	 * Call only for list page and only once
	 */
	initDetails: function(){
		//console.log('initDetails on master list page - ',this.tName);
		// init links and other stuff
		if (Runner.pages.PageSettings.getTableData(this.tName, "isUseDP", false)){
			window["dpInline"+this.pageId] = new detailsPreviewInline({
				'pageId':this.pageId,
				'mSTable':Runner.pages.PageSettings.getShortTName(this.tName),
				'mTable':this.tName,
				'mode':'list_details',
				'ext':Runner.pages.PageSettings.getGlobalData('ext',"") 
			});
		}
	},
	
	initResize: function(id){
		if (typeof id == "undefined"){
			id = Runner.pages.PageSettings.getTableData(this.tName, "recIdStart", 0);
		}
		if(Runner.pages.PageSettings.getTableData(this.tName, "isUseResize", false)){
			var permis = Runner.pages.PageSettings.getTableData(this.tName, "permissions", {});
			prepareForCreateTable({
				'id':this.pageId,
				'recId': id,
				'tName':Runner.pages.PageSettings.getShortTName(this.tName),
				'firstTime':(Runner.pages.PageSettings.getTableData(this.tName, "pageMode", false)!=Runner.pages.constants.LIST_AJAX ? 1 : 0),
				'useInlineAdd':(Runner.pages.PageSettings.getTableData(this.tName, "isInlineAdd", false) ? 1 : 0),
				'permisAdd':(permis.add ? 1 : 0),
				'numRows':(Runner.pages.PageSettings.getTableData(this.tName, "numRows", 0) ? 1 : 0)
			});
		}	
	},	
	
	initDeleteButton: function(id){

		var submitUrl = this.shortTName+"_"+this.pageType+".php";
		
		if (typeof id == "undefined"){
			id = this.id;
		}
		
		$("#delete_selected"+this.id).unbind("click").bind("click", function(e){
			
			var selBoxes = $('input[@type=checkbox][@checked][@id^=check'+id+'_]');				
			
			if(selBoxes.length == 0 || !confirm('Do you really want to delete these records?')){
				return false;
			}							
			
			var form = new Runner.form.BasicForm({															
				standardSubmit: true,			
				submitUrl: submitUrl,			
				method: 'POST',
				id: this.id,
				baseParams: {"a": 'delete'},
				addElems: [selBoxes]
			});
			
			form.submit();
			form.destructor();
		});
	},
	
	initExportLink: function(id){
		var submitUrl = this.shortTName+"_"+Runner.pages.constants.PAGE_EXPORT+".php";
		
		if (typeof id == "undefined"){
			id = this.id;
		}
		
		$("#export_selected"+this.id).unbind("click").bind("click", function(e){
			var selBoxes = $('input[@type=checkbox][@checked][@id^=check'+id+'_]');
			
			if(selBoxes.length == 0){
				return false;
			}				
			
			var form = new Runner.form.BasicForm({															
				standardSubmit: true,			
				submitUrl: submitUrl,
				target: '_blank',
				method: 'POST',
				id: this.id,
				addElems: [selBoxes.clone()],
				baseParams: {a: "export"}
			});
			
			form.submit();
			form.destructor();
		});
	},
	
	initPrintLink: function(id){
		var submitUrl = this.shortTName+"_"+Runner.pages.constants.PAGE_PRINT+".php";
		
		if (typeof id == "undefined"){
			id = this.id;
		}
		$("#print_selected"+this.id).unbind("click").bind("click", function(e){
			var selBoxes = $('input[@type=checkbox][@checked][@id^=check'+id+'_]');				
			
			if(selBoxes.length == 0){
				return false;
			}				
			
			var form = new Runner.form.BasicForm({															
				standardSubmit: true,			
				submitUrl: submitUrl,
				target: '_blank',
				method: 'POST',
				id: this.id,
				addElems: [selBoxes.clone()],
				baseParams: {a: "print"}
			});
			
			form.submit();
			form.destructor();
		});		
	},
	
	initRecordBlock: function(){
		var submitUrl = Runner.pages.getUrl(this.tName, this.pageType, {});
		
		$("#recordspp"+this.id).bind("change", function(e){
			document.location = submitUrl+'?pagesize='+this.options[this.selectedIndex].value;
		});
	},
	
	initAddButton: function(){
		if (Runner.pages.PageSettings.getTableData(this.tName, "showAddInPopup", false)){
			
			var page = this;
			$("#addButton"+this.id).bind("click", function(e){
				var eventParams = {
					tName: page.tName, 
					pageType: Runner.pages.constants.PAGE_ADD, 
					pageId: -1,
					destroyOnClose: true,
					parentPage: page,
					modal: true, 
					baseParams: {
						parId: page.id,
						table: page.tName,
						editType: Runner.pages.constants.ADD_POPUP					
					},				
					afterSave: {
				        fn: function(respObj, formObj, fieldControls, page){
				        	if (respObj.success){
				        		this.inlineAdd.addRowToGrid(respObj);	
				        	}else{
								$('#message_block'+page.id+' div.message').html(respObj.message);
								$('div.bd').animate({scrollTop:0});
				        		return false;
				        	}
						},
				        scope: page
				    }
				};
				Runner.Event.prototype.stopEvent(e);
				Runner.pages.PageManager.openPage(eventParams);
			});
		}else{
			var page = this;
			$("#addButton"+this.id).bind("click", function(e){
				document.location = Runner.pages.getUrl(page.tName, Runner.pages.constants.PAGE_ADD, {});
			});	
		}			
	},
	
	initButtons: function(){
		this.initDeleteButton();
		this.initRecordBlock();
		this.initExportLink();
		this.initPrintLink();	
		this.initAddButton();
	},
	
	initSorting: function(){
		for(var i=0; i<this.listFields.length; i++){
			$("#order_"+Runner.goodFieldName(this.listFields[i])+"_"+this.id).
				bind("mouseout", delspan).
					bind("mousemove", movespan).
						bind("mouseover", addspan).
							bind("mousedown", function(e){
								sort(e, this.href, false, 1);
							});
		}
	}
	
	
	
});

Runner.pages.ListPageAjax = Runner.extend(Runner.pages.ListPage, {
	
	ajaxBaseParams: null,
	
	constructor: function(cfg){
		
		Runner.pages.ListPageAjax.superclass.constructor.call(this, cfg);
		this.listFields = Runner.pages.PageSettings.getTableData(this.tName, 'listFields', []);
				
		
		this.ajaxBaseParams = {
			mode: "ajax",
			id: this.pageId
		};
		
	},
	
	initSearch: function(){		
		
		Runner.pages.ListPageAjax.superclass.initSearch.call(this);
		
		this.searchController.ajaxSubmit = true;
		this.searchController.srchForm.standardSubmit = false; 
		this.searchController.srchForm.baseParams = this.ajaxBaseParams;
				
		this.searchController.srchForm.on("beforeSubmit", function(form){
			
			/*if (!this.searchController.loadingMask){
				this.searchController.loadingMask = new YAHOO.widget.Panel("wait"+this.id, {			
					width: "240px", 
	                fixedcenter: true, 
	                close: false, 
	                draggable: false, 
	                modal: true,
	                visible: false
	            });
	    		
	            this.searchController.loadingMask.setHeader("Loading, please wait...");
	            this.searchController.loadingMask.setBody("<img src=\"http://l.yimg.com/a/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
	            this.searchController.loadingMask.render(this.winDiv);
			}
            this.searchController.loadingMask.show();*/
			
			form.baseParams["id"] = Runner.genId();
			runLoading(this.id, getParentTableObj(this.id), 4);
			this.ajaxBaseParams.id = form.baseParams["id"];
		}, this);
		

		this.searchController.on('afterSearch', function(respObj, srchController, srchForm){
			//this.searchController.loadingMask.hide();
			stopLoading(this.id, 4);
			this.pageReloadHn(respObj);
			if (this.searchController.usedSrch){
				this.searchController.showShowAll();
			}else{
				this.searchController.hideShowAll();
			}			
		}, this);
		
		this.searchController.srchForm.on('submitFailed', function(){	
			//this.searchController.loadingMask.hide();
			stopLoading(this.id, 4);
			console.log(this, arguments, 'submitFailed search args')
		}, this);
			
	},
		
	initSorting: function(pageId){
		if (typeof pageId == 'undefined'){
			pageId = this.id;
		}
		var pageObj = this;
		for(var i=0; i<this.listFields.length; i++){
			$("#order_"+Runner.goodFieldName(this.listFields[i])+"_"+pageId).
				bind("mouseout", delspan).
					bind("mousemove", movespan).
						bind("mouseover", addspan).
							bind("mousedown", function(e){
								Runner.Event.prototype.stopEvent(e);
								var href = sort(e, this.href, true, 1) || this.href;								
								runLoading(pageObj.id, getParentTableObj(pageObj.id), 4);
																
								$.ajax({
									url: href, 
									type: "GET",
									success: function(respObj){
										pageObj.pageReloadHn.call(pageObj, respObj)
									},
									dataType: "json",
									data: pageObj.ajaxBaseParams
								});
							}).bind("click", function(e){
								Runner.Event.prototype.stopEvent(e);
							});
		}
	},
			
	initPagination: function(pageId){
		if (typeof pageId == 'undefined'){
			pageId = this.id;
		}
		$("table[@name=paginationTable"+pageId+"]").bind("click", {pageObj: this}, function(e){
			Runner.Event.prototype.stopEvent(e);
			runLoading(this.id, getParentTableObj(this.id), 4);
			var target = Runner.Event.prototype.getTarget(e);
			if(target.nodeName != "A") {
				return false;
			}
			var pageNum = $(target).attr("pageNum");
				pageObj = e.data.pageObj;
						
			var url = Runner.pages.getUrl(pageObj.tName, pageObj.pageType, {})+"?goto="+pageNum;
			pageObj.ajaxBaseParams.id = Runner.genId();
			// ajax page reload	
			$.ajax({
				url: url, 
				type: "GET",
				success: function(respObj){
					pageObj.pageReloadHn.call(pageObj, respObj)
				},
				dataType: "json",
				data: pageObj.ajaxBaseParams
			});
		});
	},
	
	pageReloadHn: function(respObj){
		stopLoading(this.id, 1);
		if (respObj.success){
			Runner.setIdCounter(respObj.idStartFrom);
			//cut grid cont			
			$("#grid_block"+this.pageId).html(respObj.html.grid);
			// cut pagination
			var pagTablesName = $("#pagination_block"+this.pageId).find('table').attr("name");
			var pagTables = $("table[@name="+pagTablesName+"]").replaceWith($('table', respObj.html.pagination));
			
			$("#message_block"+this.pageId).html(respObj.html.message);			
			$("#details_block"+this.pageId).html(respObj.html.details);
			$("#pages_block"+this.pageId).html(respObj.html.pages);
			
			this.controlsMap.gridRows = respObj["controlsMap"][this.tName][this.pageType][this.ajaxBaseParams.id].gridRows;
			
			if (this.controlsMap.gridRows.length){
				var rowIdStartFrom = this.controlsMap.gridRows[0].id;
			}
			this.initResize(rowIdStartFrom);
			
			
			if (this.inlineAdd){				
				this.inlineAdd.reInit(this.ajaxBaseParams.id, this.controlsMap.gridRows);
			}
			if (this.inlineEdit){			
				this.inlineEdit.reInit(this.ajaxBaseParams.id, this.controlsMap.gridRows);
			}	
			
			this.initPagination(this.ajaxBaseParams.id);
			this.initSorting(this.ajaxBaseParams.id);
			this.initDeleteButton(this.ajaxBaseParams.id);
			this.initExportLink(this.ajaxBaseParams.id);
			this.initPrintLink(this.ajaxBaseParams.id);	
			
			this.searchController.usedSrch = respObj.controlsMap[this.tName][this.pageType][this.ajaxBaseParams.id].search.usedSrch;
			if (this.searchController.usedSrch){
				this.searchController.showShowAll();	
			}			
		}else{
			$("#message_block"+this.pageId).html("Submit failed!");	
		}	
	}
});
Runner.pages.ReportPage = Runner.extend(Runner.pages.DataPageWithSearch, {
	
	pageType: Runner.pages.constants.PAGE_REPORT,
	
	init: function(){
		Runner.pages.ReportPage.superclass.init.call(this);
		this.fireEvent('afterInit', this, this.id);
	},
	
	constructor: function(cfg){
		Runner.pages.ReportPage.superclass.constructor.call(this, cfg);
		this.writePagination(Runner.pages.PageSettings.getTableData(this.tName, "pageNumber", 0), Runner.pages.PageSettings.getTableData(this.tName, "maxPages", 0));
	},
	
	getPaginationLink: function(pageNum,linkText,cls){
		return '<a href="#" pageNum="'+pageNum+'" '+(cls ? 'class="pag_n"' : '')+' style="TEXT-DECORATION: none;">' + linkText + '</a>';
	},
	
	writePagination: function(mypage, maxpages){
		var paginationHTML = "";
		if(maxpages > 1 && mypage <= maxpages){
			paginationHTML += '<table rows="1" cols="1" align="center" width="auto" border="0" name="paginationTable'+this.id+'">'; 
			paginationHTML += '<tr valign="center"><td align="center">';
	 		
			var counterstart = mypage - 9; 
			if (mypage%10) 
				counterstart = mypage - (mypage%10) + 1; 
	 
			var counterend = counterstart + 9; 
			if (counterend > maxpages) 
				counterend = maxpages; 
	 
			if (counterstart != 1)
				paginationHTML += this.getPaginationLink(1,Runner.lang.constants.TEXT_FIRST,false)+'&nbsp;:&nbsp;'+
								  this.getPaginationLink(counterstart - 1,Runner.lang.constants.TEXT_PREVIOUS,false)+'&nbsp;';
	 
			paginationHTML += '<b>[</b>';
			
			var pad = '';
			var counter	= counterstart;
			for(;counter<=counterend;counter++)
			{
				if (counter != mypage) 
					paginationHTML += '&nbsp;'+this.getPaginationLink(counter,pad + counter,true);
				else 
					paginationHTML += '&nbsp;<b>' + pad + counter + '</b>';		
			}
			paginationHTML += '&nbsp;<b>]</b>';
			
			if (counterend != maxpages) 
				paginationHTML += '&nbsp;' + this.getPaginationLink(counterend + 1,Runner.lang.constants.TEXT_NEXT,false)+'&nbsp;:&nbsp;'+
								  this.getPaginationLink(maxpages,Runner.lang.constants.TEXT_LAST,false);
							
			paginationHTML += '</td></tr></table>';
			$('div.reportPagination').html(paginationHTML);
		}
	}

});
Runner.pages.ChartPage = Runner.extend(Runner.pages.DataPageWithSearch, {
	
	pageType: Runner.pages.constants.PAGE_CHART,
	
	init: function(){
		Runner.pages.ChartPage.superclass.init.call(this);
		this.fireEvent('afterInit', this, this.id);
	},
	
	constructor: function(cfg){
		Runner.pages.ChartPage.superclass.constructor.call(this, cfg);
	}
});

Runner.pages.MembersPage = Runner.extend(Runner.pages.ListPage, {
		
	pageType: Runner.pages.constants.PAGE_ADMIN_MEMBERS,
	
	constructor: function(cfg){		
		Runner.pages.MembersPage.superclass.constructor.call(this, cfg);	
		this.submitUrl = "admin_members_list.php";	
	},
	
	init: function(){
		Runner.pages.MembersPage.superclass.init.call(this);	
		this.initButtons();
		this.fireEvent('afterInit', this, this.id);
	},
	
	initButtons: function(){
		var pageObj = this;
		$("#saveButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);						
					
			var form = new Runner.form.BasicForm({															
				isFileUpload: false,			
				submitUrl: pageObj.submitUrl,	
				standardSubmit: true,
				method: 'POST',
				baseParams: {a: "save"},
				id: pageObj.pageId,
				addElems: [$("input", "#grid_block"+pageObj.pageId).clone()]
			});
			
			form.submit();
			
		});	
		
		// form just for reset
		$("#records_block"+pageObj.pageId).wrap('<form id="resetForm' + pageObj.pageId + '"/>');	
	}
});
Runner.pages.RightsPage = Runner.extend(Runner.pages.ListPage, {
		
	pageType: Runner.pages.constants.PAGE_ADMIN_RIGHTS,
	
	constructor: function(cfg){		
		Runner.pages.RightsPage.superclass.constructor.call(this, cfg);
	},
	
	init: function(){
		this.initOldCode();
		this.fireEvent('afterInit', this, this.id);
	},
		
	initOldCode: function(){
		window.TEXT_AA_ADD_NEW_GROUP = "Add new group";
		window.TEXT_AA_RENAMEGROUP = "Rename group";
		window.renameidx = -1;
		window.tables = Runner.pages.PageSettings.getTableData(this.tName, "rightsTables", false);
		window.groups = Runner.pages.PageSettings.getTableData(this.tName, "rightsGroups", false);
		window.fillboxes = function (group)
		{
			var add_uncheck=false;
			var edt_uncheck=false;
			var del_uncheck=false;
			var lst_uncheck=false;
			var exp_uncheck=false;
			var imp_uncheck=false;
			var adm_uncheck=false;
			for(g=0;g<groups.length;g++)
			{
				for(t=0;t<tables.length;t++)
				{
					var disp='none';
					if(groups[g]==group)
					{
						var tbl_uncheck=false;
						disp='';
						if((!tbl_uncheck || !add_uncheck) && !document.getElementById('cbadd_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							add_uncheck=true;
						}
						if((!tbl_uncheck || !edt_uncheck) && !document.getElementById('cbedt_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							edt_uncheck=true;
						}
						if((!tbl_uncheck || !del_uncheck) && !document.getElementById('cbdel_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							del_uncheck=true;
						}
						if((!tbl_uncheck || !lst_uncheck) && !document.getElementById('cblst_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							lst_uncheck=true;
						}
						if((!tbl_uncheck || !exp_uncheck) && !document.getElementById('cbexp_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							exp_uncheck=true;
						}
						if((!tbl_uncheck || !imp_uncheck) && !document.getElementById('cbimp_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							imp_uncheck=true;
						}
						if((!tbl_uncheck || !adm_uncheck) && !document.getElementById('cbadm_'+tables[t]+'_'+groups[g]).checked)
						{
							tbl_uncheck=true;
							adm_uncheck=true;
						}
						document.getElementById(tables[t]).checked=!tbl_uncheck;
					}
					document.getElementById('cbadd_'+tables[t]+'_'+groups[g]).style.display=disp;
					document.getElementById('cbedt_'+tables[t]+'_'+groups[g]).style.display=disp;
					document.getElementById('cbdel_'+tables[t]+'_'+groups[g]).style.display=disp;
					document.getElementById('cblst_'+tables[t]+'_'+groups[g]).style.display=disp;
					document.getElementById('cbexp_'+tables[t]+'_'+groups[g]).style.display=disp;
					document.getElementById('cbimp_'+tables[t]+'_'+groups[g]).style.display=disp;
					document.getElementById('cbadm_'+tables[t]+'_'+groups[g]).style.display=disp;
				}
			}
			document.getElementById('add').checked=!add_uncheck;
			document.getElementById('edt').checked=!edt_uncheck;
			document.getElementById('del').checked=!del_uncheck;
			document.getElementById('lst').checked=!lst_uncheck;
			document.getElementById('exp').checked=!exp_uncheck;
			document.getElementById('imp').checked=!imp_uncheck;
			document.getElementById('adm').checked=!adm_uncheck;
			if(group<0)
			{
				document.getElementById('delgroup').disabled=true;
				document.getElementById('delgroup').className='button_dis button';
				document.getElementById('rengroup').disabled=true;
				document.getElementById('rengroup').className='button_dis button';
			}
			else
			{
				document.getElementById('delgroup').disabled=false;
				document.getElementById('delgroup').className='button';
				document.getElementById('rengroup').disabled=false;
				document.getElementById('rengroup').className='button';
				var gr=document.getElementById('group'); 
				renameidx=gr.selectedIndex;
				$('#groupname').val(gr.options[gr.selectedIndex].text);
			}
		}
		var gsel=document.getElementById('group');
		if(gsel.selectedIndex<0)
			gsel.selectedIndex=0;
		fillboxes(gsel.options[gsel.selectedIndex].value);
		window.deletegroup = function ()
		{
			var gr=document.getElementById('group'); 
			var disp = gr.options[gr.selectedIndex].text;
			var idx=gr.selectedIndex;
			var id = gr.options[idx].value;
			if(!confirm("Do you really want to delete group" +disp+'?')) 
				return;
			$.get('ug_group.php',
				{	
					rndval: Math.random(),
					id: id,
					a: 'del'
				},
				function(ret){	
					if(ret!='ok')
					{
					  alert('Error deleting record!');
					  return;
					}
					for(i=0;i<groups.length;i++)
						if(groups[i]==id)
							groups.splice(i,1);
					gr.selectedIndex--;
					gr.onchange();
					$('input[@type=checkbox][@id$=_'+id+']').each(function(){this.parentNode.removeChild(this);});
					gr.remove(idx);
					gr.size=gr.size-1;
				}
			);
		}
		window.makename = function (group)
		{
		  var n=1;
		  var i;
		  tgroup=group;
		  var gr=document.getElementById('group'); 
		  while(1)
		  {
			  for(i=0;i<gr.options.length;i++)
			  {
				if(tgroup==gr.options[i].text)
		  		  break;
			  }	
			  if(i==groups.length)
			  {
			    return tgroup;
			}
			tgroup=group+n;
			n++;
		  }
		}
		//Save name group
		window.save = function (name)
		{
		 if(renameidx==-1)
		 {
		  $.get('ug_group.php',
		  {	
		   rndval: Math.random(),
		   name: name,
		   a: 'add'
		  },
		  function(ret)
		  {	
		   if(ret.substring(0,2)!='ok')
		   {
		    alert('Error adding group!');
		    return;
		   }
		   var id=ret.substring(2);
		   var gr=document.getElementById('group'); 
		   $('#addarea').hide();
		//	append checkboxes
		   $('input[@type=checkbox][@id$=_-1]').each( 
		   function() 
		   {
			var cbid = $(this)[0].id;
			$(this.parentNode).append('<input type=checkbox  id="'+cbid.substring(0,cbid.length-2)+id+'" name="'+cbid.substring(0,cbid.length-2)+id+'">');
		   });
		   groups[groups.length]=id;
		   gr.options[gr.options.length]=new Option(name,id);
		   gr.selectedIndex=gr.options.length-1;
		   gr.onchange();
		   gr.size=gr.size+1;
		   $('#groupname').val('');
		  		});
				}
				else
				{
					var idx=renameidx;
					renameidx=-1;
					var gr=document.getElementById('group'); 
				$.get('ug_group.php',
				{	
					rndval: Math.random(),
					id: gr.options[idx].value,
					name: name,
					a: 'rename'
				},
				function(ret){	
					if(ret.substring(0,2)!='ok')
					{
					  alert('Error renaming group!');
					  return;
					}
					$('#addarea').hide();
					gr.options[idx].text=name;
					$('#groupname').val('');
				});
				}
		};
	}
});
Runner.pages.ExportPage = Runner.extend(Runner.pages.RunnerPage, {
		
	pageType: Runner.pages.constants.PAGE_IMPORT,
	
	constructor: function(cfg){		
		Runner.pages.ExportPage.superclass.constructor.call(this, cfg);	
		this.submitUrl = Runner.pages.getUrl(this.tName, this.pageType, {});	
	},
	
	init: function(){
		Runner.pages.ExportPage.superclass.init.call(this);	
		this.initButtons();
	},
	
	initButtons: function(){
		var pageObj = this;
		$("#saveButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);	

			pageObj.form = new Runner.form.BasicForm({
				submitUrl: pageObj.submitUrl,	
				standardSubmit: true,
				method: 'POST',
				id: pageObj.pageId,
				addElems: [$('input[@type=radio][@name=type]').clone(), $('input[@type=radio][@name=records]').clone()]
			});
			
			pageObj.form.submit();
			pageObj.form.destructor();
			pageObj.form = null;
		});	
	}
});
Runner.pages.ImportPage = Runner.extend(Runner.pages.RunnerPage, {
		
	pageType: Runner.pages.constants.PAGE_IMPORT,
	
	constructor: function(cfg){		
		Runner.pages.ImportPage.superclass.constructor.call(this, cfg);	
		this.submitUrl = Runner.pages.getUrl(this.tName, this.pageType, {});	
	},
	
	init: function(){
		Runner.pages.ImportPage.superclass.init.call(this);	
		this.initButtons();		
		this.fireEvent('afterInit', this, this.id);
	},
	
	initButtons: function(){
		var pageObj = this;
		$("#saveButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);

			var path = $("#file_ImportFileName"+pageObj.id).val();
			if (!path){
				return false;
			}
			var wpos = path.lastIndexOf('\\'); 
			var upos = path.lastIndexOf('/');  				
			var pos = wpos; 
			if(upos>wpos){
				pos=upos;				
			}
			baseParams = {
				a: "added", 
				id: pageObj.pageId
			};
			baseParams["value_ImportFileName"+pageObj.id] = path.substr(pos+1);
			baseParams["type_ImportFileName"+pageObj.id] = 'upload2';
			
			var form = new Runner.form.BasicForm({	
				submitUrl: pageObj.submitUrl,	
				standardSubmit: true,
				isFileUpload: true,
				method: 'POST',
				baseParams: baseParams,
				id: pageObj.pageId,
				addElems: [$("#file_ImportFileName"+pageObj.id)]
			});
			
			form.submit();
			
		});	
		
		$("#backButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);	
			window.location.href = Runner.pages.getUrl(pageObj.tName, Runner.pages.constants.PAGE_LIST, {})+"?a=return";	
		});
		
		$("a[@linkType=debugOpener]").bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);	    
	        // show info table
	        $("#importDebugInfoTable"+this.recId).toggle();   
		});
	}
});
Runner.pages.RegisterPage = Runner.extend(Runner.pages.RunnerPage, {
	
	submitUrl: "",
	
	passFieldName: 'password',
	
	constructor: function(cfg){		
		Runner.pages.RegisterPage.superclass.constructor.call(this, cfg);	
		this.submitUrl = "register.php";	
		this.passFieldName = Runner.pages.PageSettings.getTableData(this.tName, "passFieldName", 'password'); 
	},
	
	init: function(){
		Runner.pages.RegisterPage.superclass.init.call(this);
		this.initButtons();
		this.initControlEvents();
		Runner.controls.ControlManager.getAt(this.tName, this.id)[0].setFocus();
		this.fireEvent('afterInit', this, this.id);
	},
	
	initButtons: function(){
		var pageObj = this;
		$("#saveButton"+this.id).bind("click", function(e){
			Runner.Event.prototype.stopEvent(e);
			
			var arrcntrl = Runner.controls.ControlManager.getAt(pageObj.tName);
				
			for (var i = 0; i < arrcntrl.length; i++){
				if (arrcntrl[i].invalid() === true || !arrcntrl[i].validate().result){
					return false;
				}
			}		
			
			var form = new Runner.form.BasicForm({															
				isFileUpload: true,			
				submitUrl: pageObj.submitUrl,	
				standardSubmit: true,
				method: 'POST',
				baseParams: {'btnSubmit': "Register"},
				id: pageObj.pageId,
				fieldControls: Runner.controls.ControlManager.getAt(pageObj.tName)
			});
						
			form.submit();
		});			
	},
	
	initControlEvents: function(){
		
		var passctrl = Runner.controls.ControlManager.getAt(this.tName, this.id, this.passFieldName),
			confctrl = Runner.controls.ControlManager.getAt(this.tName, this.id, 'confirm');
		
		passctrl.on('blur', function(e, confctrl){
			if(confctrl.getValue()!=this.getValue() && confctrl.getValue()!=""){
				confctrl.markInvalid([Runner.lang.constants.PASSWORDS_DONT_MATCH]);		
			}else{
				confctrl.clearInvalid();
				this.clearInvalid();
			}
		}, {args: [confctrl]});
		
		confctrl.on('blur', function(e, passctrl){
			if(passctrl.getValue()!=this.getValue()){
				this.markInvalid([Runner.lang.constants.PASSWORDS_DONT_MATCH]);		
			}else{
				passctrl.clearInvalid();
				this.clearInvalid();
			}
		}, {args: [passctrl]});
		
		
		var ctrls = Runner.controls.ControlManager.getAt(this.tName, this.id);
		
		for(var i=0;i<ctrls.length;i++){
			
			if (ctrls[i].fieldName == 'confirm' || ctrls[i].fieldName == this.passFieldName){
				continue;
			}					
			ctrls[i].on('blur', function(e, fName){
				var ctrl = this, params = {
					id: this.id,
					rndval: Math.random(),
					field: this.fieldName,
					val: this.getValue(),
					table: this.table
				};
				$.get('registersuggest.php', params, function(resp){				
					if(resp){
						ctrl.markInvalid([resp]);
					}
				});
			});
		}
	}
	
});

/// <reference path="Runner.js" />

/**
 * Search form controller. Need for submit form in advanced and panel mode
 */
Runner.search.SearchForm = Runner.extend(Runner.util.Observable, {
	/**
     * jQuery obj of simple search edit box
     * @type {obj}
     */
    smplSrchBox: null,
    
    simpleSrchTypeCombo: null,
    
    simpleSrchFieldsCombo: null,
    
	/**
     * Indicator. True when simple search edit box get focus 
     * @type Boolean
     */
    usedSrch: false,
	/**
    * Id of page, used when page loades dynamicly
    * @type {int}
    */
    id: -1,  
	/**
     * Name of table for which instance of class was created
     * @type string
     */
    tName: "",
	/**
	 * Type of search: panel on list, or advanced search page
	 * @type String
	 */
	searchType: "panel",  
	/**
     * jQuery obj of top radio with conditions
     * @type {obj}
     */
    conditionRadioTop: null,    
	/**
     * jQuery obj
     * @type 
     */
    srchForm: null,
	/**
    * ctrls map. Used for indicate which index conected with which search ctrl
    * @type obj
    */    
    ctrlsShowMap: null,
    
    ajaxSubmit: false,
    
    baseParams: null,
    
    optCombosArr: null,
    
	/**
     * Override parent contructor
     * Add interaction with server
     * @param {obj} cfg
     */
    constructor: function(cfg){  
    	this.ctrlsShowMap = {};
    	this.baseParams = {};
    	this.optCombosArr = [];
    	// copy properties from cfg to controller obj
        Runner.apply(this, cfg);
    	//call parent
    	Runner.search.SearchForm.superclass.constructor.call(this, cfg);        
        // radio with contion choose or|and
        this.conditionRadioTop = $('input:radio[name=srchType]');
        
         // edit box any field contains search
        this.smplSrchBox = $('#ctlSearchFor'+this.id);
        
        this.simpleSrchTypeCombo = $('#simpleSrchTypeCombo'+this.id);
        this.simpleSrchFieldsCombo = $('#simpleSrchFieldsCombo'+this.id);
        
        this.addEvents('beforeSearch', 'afterSearch');
    },
    
    init: function(ctrlsBlocks){
    	this.initControlBlocks(ctrlsBlocks); 
    	this.initForm();
    	this.initSuggest();
    	this.initButtons();
    },
    
    initForm: function(){
    	var method = "GET";
    	if (this.ajaxSubmit){
    		var method = "POST";
    	}
    	
    	if (this.pageType == Runner.pages.constants.PAGE_LIST || this.pageType == Runner.pages.constants.PAGE_REPORT || this.pageType == Runner.pages.constants.PAGE_CHART || this.pageType == Runner.pages.constants.PAGE_PRINT){
    		var submitUrl = Runner.pages.getUrl(this.tName, this.pageType, {});
    	}else{
    		var submitUrl = this.pageType+".php";
    	}
    	
    	// get form object       
        this.srchForm = new Runner.form.BasicForm({
			standardSubmit: !this.ajaxSubmit,
			initImmediately: true,
			submitUrl: submitUrl,			
			method: method,
			id: this.id,
			addRndVal: false,
			baseParams: this.baseParams || {}
		});
		
		this.srchForm.on('successSubmit', function(respObj){
			this.fireEvent('afterSearch', respObj, this, this.srchForm);
		}, this);
    	
    },
    
    initCombo: function(recId, fName, map){
    	$("#"+this.getComboId(fName, recId)).bind("change", {tName: this.tName, recId: recId, fName: fName, map: map}, function(e){
    			if (typeof e.data.map[1] == "undefined"){
    				return false;
    			}
				var ctrl = Runner.controls.ControlManager.getAt(e.data.tName, e.data.recId, e.data.fName, e.data.map[1]);
				if (!ctrl){
					return false;
				}
				if (this.value=='Between' || this.value=='NOT Between'){
					ctrl.show();
				}else{
					ctrl.hide();
				};	
			}
		);
		this.optCombosArr.push($("#"+this.getComboId(fName, recId)));
		$("#"+this.getComboId(fName, recId)).get(0).defVal = $("#"+this.getComboId(fName, recId)).val();
    },
    
    initButtons: function(){
    	var searchController = this;
    	
    	$("#searchButtTop"+this.id).bind("click", function(e){
    		Runner.Event.prototype.stopEvent(e);
    		searchController.submitSearch();
    		// add run loading for ajax reboot
    	});
    	
    	$("#showAll"+this.id).bind("click", function(e){
    		Runner.Event.prototype.stopEvent(e);
    		searchController.showAllSubmit();
    	}); 
    },
    
    initControlBlocks: function(ctrlsBlocks){		
		for(var i=0; i<ctrlsBlocks.length; i++){
			this.addRegCtrlsBlock(ctrlsBlocks[i].fName, ctrlsBlocks[i].recId, ctrlsBlocks[i].ctrlsMap);
		}	
    },
    
    initSuggest: function(){
    	if (!this.useSuggest){
    		return false;
    	}
    	var ctrls, i, searchController = this;
    	
    	for(var fName in this.ctrlsShowMap){
    		for(var recId in this.ctrlsShowMap[fName]){
    			ctrls = Runner.controls.ControlManager.getAt(this.tName, recId);
		    	for(i=0; i<ctrls.length; i++){
		    		ctrls[i].on('keyup', function(e, argsArr){
						var srchTypeComboId = searchController.getComboId(searchController.tName, searchController.id);
						var srchTypeCombo = $('#'+srchTypeComboId);				
						var suggestUrl = 'searchsuggest.php?table='+searchController.shortTName;
						return searchSuggest_new(e, this, srchTypeCombo, 'advanced', suggestUrl);
					}, {buffer: 200});
					ctrls[i].on('keydown', function(e, argsArr){
						return listenEvent(e, this.valueElem.get(0), searchController);
					});
		    	}
    		}
    	}
    },
    
   
    /**
     * Create and submit form 
     */
    submitSearch: function(){    
    	this.fireEvent('beforeSearch', this, this.srchForm);
    	this.srchForm.clearForm();
    	this.srchForm.addToForm("a", 'integrated');
    	
    	// add fields thats appear only on list panel mode
    	this.srchForm.addToForm('ctlSearchFor', this.smplSrchBox.val());
    	
    	
    	
    	// for simple search with combos
    	this.srchForm.addToForm('simpleSrchFieldsComboOpt', this.simpleSrchFieldsCombo.val() || "");
    	
    	var simpleSrchTypeComboVal = this.simpleSrchTypeCombo.val();
    	if (simpleSrchTypeComboVal && simpleSrchTypeComboVal.indexOf('NOT') == 0){
			simpleSrchTypeComboVal = simpleSrchTypeComboVal.substr(4);
			this.srchForm.addToForm('simpleSrchTypeComboNot', 'on');
		}else{
			this.srchForm.addToForm('simpleSrchTypeComboNot', '');
		}
    	this.srchForm.addToForm('simpleSrchTypeComboOpt', simpleSrchTypeComboVal || "");   	
    	
    	
    	// add radio values
    	for (var i=0;i<this.conditionRadioTop.length;i++){
    		if(this.conditionRadioTop[i].checked == true){
    			this.srchForm.addToForm('criteria', this.conditionRadioTop[i].value);
    			break;
    		}
    	}
    	    	
    	// for interator, field counter
		var j=1, notVal=''; 
		// add search params for each field
    	for(var fName in this.ctrlsShowMap){    		
    		// loop through all ctrls, except cached and deleted
    		for(var ind in this.ctrlsShowMap[fName]){    			
    			// get ctrls map for field name
    			var fMap = this.ctrlsShowMap[fName][ind];
    			// add ctrls vals    			
    			var ctrl1 = Runner.controls.ControlManager.getAt(this.tName, ind, fName, fMap[0]);
    			// add empty vals, if we search empty or not empty vals
    			var srchCombo = $('#'+this.getComboId(fName, ind)); 
    			var comboVal = srchCombo.val();
    			// add only non empty vals
    			if (ctrl1.isEmpty() && comboVal.indexOf('Empty') == -1){
    				continue;
    			}
    			// add first value and type
    			this.srchForm.addToForm('type'+j, ctrl1.ctrlType);    	
    			var ctrl1Val = ctrl1.getStringValue();    			
    			this.srchForm.addToForm('value'+j+'1', ctrl1Val);
    			// add fName to form
    			this.srchForm.addToForm('field'+j, fName);
    			// add option to form
    			
    			
    			if (srchCombo.val().indexOf('NOT') == 0){
    				comboVal = comboVal.substr(4);
    			}
    			this.srchForm.addToForm('option'+j, comboVal);
    			// add not checkBox to form
    			var srchCheckBox = $('#'+this.getCheckBoxId(fName, ind));
    			notVal = '';
    			// if there is any checkbox, then use its value, else parse value from combo
    			if (srchCheckBox.length){
    				notVal = srchCheckBox[0].checked ? 'on' : '';
    			}else{
    				notVal = srchCombo.val().indexOf('NOT') == 0 ? 'on' : '';
    			}
    			this.srchForm.addToForm('not'+j, notVal); 
    			// if search type between and exists second ctrl
    			if (srchCombo.val().toLowerCase().indexOf('between') !== -1 && fMap[1]){
    				var ctrl2 = Runner.controls.ControlManager.getAt(this.tName, ind, fName, fMap[1]);
    				var ctrl2Val = ctrl2.getStringValue();	    			
    				this.srchForm.addToForm('value'+j+'2', ctrl2Val);
    			}    			
    			j++;
    		}    		
    	}   
    	
    	this.usedSrch = true;
    	// submit
    	this.srchForm.submit();  
    },
    /**
     * Register ctrl in show map
     * @param {string} fName
     * @param {string} ind
     * @param {string} ctrlIndArr
     */
    addToShowMap: function(fName, ind, ctrlIndArr){
    	// create field names and indexes if they not created
    	!this.ctrlsShowMap[fName] ? this.ctrlsShowMap[fName] = {} : '';
    	!this.ctrlsShowMap[fName][ind] ? this.ctrlsShowMap[fName][ind] = {} : '';
    	// add ctrls indexes array
    	this.ctrlsShowMap[fName][ind] = ctrlIndArr;    	
    },
    /**
     * Adds block to map, regs its components and ands HTML
     * @param {} fName
     * @param {} ind
     * @param {} ctrlIndArr
     * @param {} blockHTML
     */
    addRegCtrlsBlock: function(fName, ind, ctrlIndArr){
    	// add to map
    	ctrlIndArr ? this.addToShowMap(fName, ind, ctrlIndArr) : '';
    	this.initCombo(ind, fName, ctrlIndArr);
    },
    /**
     * Return search type combo id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getComboId: function(fName, ind){
    	return "srchOpt_" + ind + "_" + Runner.goodFieldName(fName);
    },
    /**
     * Return search checkbox id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getCheckBoxId: function(fName, ind){
    	return "not_" + ind + "_" + fName;
    },
    showAllSubmit: function(){
    	this.srchForm.clearForm();
    	this.srchForm.addToForm("a", 'showall');
    	this.usedSrch = false;
    	this.smplUsed = true;
    	// submit
    	this.srchForm.submit();
    },
    /**
     * Submit for for return on list page
     */
    returnSubmit: function(){
    	this.srchForm.clearForm();
    	this.srchForm.addToForm('a', 'return');
    	// submit
    	this.srchForm.submit();
    },
    /**
     * Resets form ctrls, for panel should be overriden
     * @return {Boolean}
     */
    resetCtrls: function(){
    	Runner.controls.ControlManager.resetControlsForTable(this.tName);
    	var val;
        for(var i=0; i<this.optCombosArr.length;i++){
        	val = this.optCombosArr[i].get(0).defVal;
        	this.optCombosArr[i].val(val);
        	this.optCombosArr[i].change();
        }
		return false;
		
		
    }    
});
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

// create namespace
Runner.namespace('Runner.form');
Runner.form.BasicForm = function(cfg){
	
	this.fieldControls = [];
	this.fields = [];
	this.addElems = [];
	this.ajaxForm = {};
	this.baseParams = {};
	Runner.apply(this, cfg);
   
    this.addEvents('beforeSubmit', 'successSubmit', 'submitFailed', 'validationFailed');
    	
	
	if (this.beforeSubmit){
		this.on({'beforeSubmit': this.beforeSubmit});
	}
	if (this.successSubmit){
		this.on({'successSubmit': this.successSubmit});
	}
	if (this.submitFailed){
		this.on({'submitFailed': this.submitFailed});
	}
	if (this.validationFailed){
		this.on({'validationFailed': this.validationFailed});
	}
       
    Runner.form.BasicForm.superclass.constructor.call(this. cfg);
    
    if (cfg.initImmediately){
    	this.initForm();
    }
};




Runner.form.BasicForm = Runner.extend(Runner.form.BasicForm, Runner.util.Observable, {
	
	fields: null,
	
	fieldControls: null,
	
	addElems: null,
	
	isFileUpload: false,
	
	standardSubmit: false,
	
	formEl: null,
	
	ioEl: null,
	
	ioElId: '',
	
	submitUrl: '',
	
	method: 'GET',
	
	id: -1,
	
	baseParams: null,
	
	tName: '',
	
	shortTName: '',	
	
	target: '',
	
	ajaxForm: null,
	
	autoValidation: true,
	
	addRndVal: true,
	
	initControls: function(){
		
	},
	
	destructor: function(leaveControls){
		if (leaveControls === true){
			for(var i=0;i<this.fieldControls.length;i++){
				this.fieldControls[i].unregister();
			}	
		}
				
		if (this.ioEl){
			$(this.ioEl).remove();
		}
		if (this.formEl){
			$(this.formEl).remove();
		}
	},
	
	submit: function(){
		
		if (!this.validate()){
			return false;
		};
		
		var beforeSubmitRes = this.fireEvent('beforeSubmit', this);
		if (beforeSubmitRes === false){
			return false;
		}
		if (this.isFileUpload || this.standardSubmit){
			this.initForm();
			this.addFormSubmit();			
			//this.fireEvent('beforeSubmit', this);
			this.formEl.submit();
		}else{
			this.addFormSubmit();
			//this.fireEvent('beforeSubmit', this);
			// for closure
			var formObj = this;			
			$.ajax({
				url: this.submitUrl,
				type: this.method,
				data: this.ajaxForm,
				success: function(data, textStatus, XMLHttpRequest) {
					var respObj = JSON.parse(data);
					formObj.fireEvent("successSubmit", respObj, formObj, formObj.fieldControls);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					formObj.fireEvent("successFailed", respObj, formObj, formObj.fieldControls);
				}
			});
		}
		return true;
	},
	
	initForm: function(){
		if (this.isFormReady){
			//this.clearForm();
			return;
		}
		if (this.isFileUpload && !this.standardSubmit){
			this.createIFrame();
			this.createForm();			
		}else if(this.standardSubmit){
			this.createForm();
		}
		this.isFormReady = true;
	},
	
	clearForm: function(){
		if (this.formEl){
			$(this.formEl).children().remove();
			return true;	
		}
		this.ajaxForm = {};
	},
	
	addFormSubmit: function(){
		if (this.addRndVal){
			this.baseParams["rndVal"] = Math.random();
		}
		if (this.formEl){
			var arrClns;
			for(var i=0; i<this.fieldControls.length; i++){
				arrClns = this.fieldControls[i].getForSubmit();
				for (var j = 0; j < arrClns.length; j++){ 
					$(arrClns[j]).appendTo(this.formEl);
				}
			}
			for(var param in this.baseParams){
				this.addToForm(param, this.baseParams[param]);
			}
			for(var i=0; i<this.addElems.length; i++){
				$(this.addElems[i]).appendTo(this.formEl);
			}			
		}else{
			this.ajaxForm = Runner.apply(this.ajaxForm, this.baseParams);
			for(var i=0; i<this.fieldControls.length; i++){
				this.ajaxForm[this.fieldControls[i].fieldName] = this.fieldControls[i].getStringValue(); 
			}
		}
		return true;
	},
	
	addToForm: function(id, val){
		if (typeof val == 'undefined' || typeof id == 'undefined' || val === null || id === null){
			return false;
		}
		
		if (this.isFileUpload || this.standardSubmit){
	    	if (!this.formEl){
	    		this.initForm();
	    	}	    	
	    	var formElem = document.createElement('INPUT');
	    	$(formElem).attr('type', 'hidden').attr('name', id).attr('id', id).val(val.toString()).appendTo(this.formEl);
		}else{
			this.ajaxForm[id] = val;
		}
    },
	
    addElemToForm: function(el){
    	if (!el){
    		return false;
    	}
    	if ($(el).attr("id") === ""){
			return false;
		}
    	if (this.isFileUpload || this.standardSubmit){
    		if (!this.formEl){
    			return false;
    		}
    		$(el).appendTo(this.formEl);
    	}else{
			this.ajaxForm[$(el).attr("id")] = $(el).val();
		}
    },
    
	validate: function(){
		if (!this.autoValidation){
			return true;
		}
		var vRes;
		for(var i=0; i<this.fieldControls.length; i++){
			vRes = this.fieldControls[i].validate();			
			if (!vRes.result){
				this.fireEvent("validationFailed", this, this.fieldControls);
				this.fieldControls[i].setFocus();
				return false;
			}			
		}
		return true;
	},
	
	createIFrame: function(){
		if (this.ioEl){
			return false;
		}
		var frameId = 'uploadFrame_'+this.id;
		
		if(Runner.isIE){
			this.ioEl = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
			if(Runner.isSecure){
				this.ioEl.src = 'javascript:false';
			}
		}
		else{
			this.ioEl = document.createElement('iframe');
			this.ioEl.id = frameId;
			this.ioEl.name = frameId;
		}

		this.ioEl.style.position = 'absolute';
		this.ioEl.style.top = '-1000px';
		this.ioEl.style.left = '-1000px';

		document.body.appendChild(this.ioEl);
		// for closure
		var basicForm = this;
		
		this.ioEl.onload = function(e){
			var iframeNode = $("#"+frameId)[0], ioDoc;
			if (iframeNode.contentDocument){
				ioDoc = iframeNode.contentDocument;
			}else if(iframeNode.contentWindow){
				ioDoc = iframeNode.contentWindow.document;
			}else{
				ioDoc = iframeNode.document;
			}
			if (ioDoc.body.innerHTML!=''){
				console.log($(ioDoc.body.innerHTML).text(), 'ioDoc.body.innerHTML', this);
				var responseObj = JSON.parse($(ioDoc.body.innerHTML).text());
				basicForm.fireEvent('successSubmit', responseObj, basicForm, basicForm.fieldControls);
			}else{
				basicForm.fireEvent('submitFailed', {}, basicForm, basicForm.fieldControls);
			}
		};		
		
		this.ioElId = frameId;
		
		return this.ioEl;
	},
	
	createForm: function(){
		if (this.formEl){
			return false;
		}
		this.formEl = document.createElement('FORM');		
		
		this.formEl.action = this.submitUrl;
		this.formEl.method = this.method;
		
		if (this.target){
			this.formEl.target = this.target;
		}
		
		$(this.formEl).css('display', 'none');
		if (this.isFileUpload){
			this.formEl.enctype = "multipart/form-data";
		}
		
		if (this.ioEl){
			$(this.formEl).attr('target', this.ioElId);
		}
		
		document.body.appendChild(this.formEl);
		
		/*$(this.formEl).bind('submit', {basicForm: this}, function(e){
			e.data.basicForm.fireEvent('afterSubmit');
		});*/
		
		return this.formEl;
	}	
});


	