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