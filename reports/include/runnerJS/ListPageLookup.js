/**
 * List page with search lookup control class
 * @requires Runner.controls.TextFieldLookup
 */
Runner.controls.ListPageLookup = Runner.extend(Runner.controls.TextFieldLookup, {
	/**
	 * id of a tag, which opens search div
	 * @type {String}
	 */
	selectLinkId: "",
	/**
	 * jQuery object of a tag, which opens search div
	 * @type {object}
	 */
	selectLinkElem: null,
	
	lookupVals: null,
	
	selectField: '',
	
	isLookupOpen: false,
	
	
	
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */
	constructor: function(cfg){
		//call parent
		Runner.controls.ListPageLookup.superclass.constructor.call(this, cfg);
		// init events handling
		this.init();	
		// a select tag id
		this.selectLinkId = "open_lookup_"+this.goodFieldName+"_"+this.id;
		// a select tag jQuery element
		this.selectLinkElem = $("#"+this.selectLinkId);
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}
		var control = this, eventParams = {
			tName: this.lookupTable, 
			pageType: Runner.pages.constants.PAGE_LIST, 
			pageId: this.pageId, 
			lookupCtrl: this,
			destroyOnClose: true,
			modal: true,
			baseParams: {
				parId: this.id, 
				field: this.fieldName, 
				category: "", 
				table: this.table, 
				firstTime: 1,
				mode: "lookup"
			}
		};
			
		
		this.selectLinkElem.bind("click", eventParams, function(e){
			Runner.Event.prototype.stopEvent(e);
			if (control.parentCtrl){
				e.data.baseParams.category = control.parentCtrl.getValue();
			}
			control.pageId = Runner.pages.PageManager.openPage(e.data);
		});
		
		
	},
	
	
	/**
	 * Overrides parent function for element control
	 * Sets disable attr true
	 * Sets hidden css style true for image selectLinkElem
	 * @method
	 */
	setDisabled: function(){
		var res = Runner.controls.ListPageLookup.superclass.setDisabled.call(this);
		if (res){
			if (this.selectLinkElem){
				this.selectLinkElem.css('visibility','hidden');
			}			
		}
		return res;		
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr false
	 * Sets visible css style true for image selectLinkElem
	 * @method
	 */
	setEnabled: function(){
		var res = Runner.controls.ListPageLookup.superclass.setEnabled.call(this);
		if (res){
			if (this.selectLinkElem){
				this.selectLinkElem.css('visibility','visible');
			}
		}
		return res;	
	},
	
	setLookupVal: function(valInd){		
		this.setValue(this.lookupVals[valInd].dispVal, this.lookupVals[valInd].linkVal, true);
	},
	
	addLookupVal: function(linkVal, dispVal){
		return (this.lookupVals.push({'linkVal': linkVal, 'dispVal':dispVal}) - 1);
	},
	/**
	 * Use for init link
	 * helper closure function, for sending correct ind link
	 * @param {} link
	 * @param {} ind
	 * @param {} windId
	 */
	initLink: function(link, ind){
		var ctrl = this;		
		link.bind('click', function(e){
			ctrl.stopEvent(e);
			ctrl.setLookupVal(ind);			
			Runner.pages.PageManager.unregister(ctrl.lookupTable, ctrl.pageId);
			ctrl.setFocus();
		});
	},
	/**
	 * Initialize links
	 * @param {} winId
	 */
	initLinks: function(winId){	
		var links = $("a[@type='lookupSelect"+winId+"']");
		
		for(var i=0;i<links.length;i++){
			// use helper func, to prevent sending links last index in closure
			this.initLink($(links[i]), i);
		}
	}
	
});