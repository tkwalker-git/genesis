/**
 * Abstract base class for LookupWizard fields, should not created directly.
 * Contains common functionality for dependent lookup wizard controls
 * @class 
 * @requires Runner.controls.Control
 */
Runner.controls.LookupWizard = Runner.extend(Runner.controls.Control, {
	/**
	 * Lookup wizard indicator
	 * @type Boolean
	 */
	isLookupWizard: true,
	/**
	 * Array dropDownControls which are dependent to this ctrl
	 * @type 
	 */
	dependentCtrls: null,
	/**
	 * Parent ctrl object. Used to get values in lookupSuggest
	 * @type {object}
	 */
	parentCtrl: null,
	/**
	 * Name of parent field
	 * @type String
	 */
	parentFieldName: '',
	
	lookupTable: "",
	
	addNew: "",
		
	pageId: -1,
	
	dispField: "",
	
	linkField: "",
	
	preloadData: null,
	
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */
	constructor: function(cfg){
		// stop event init
		cfg.stopEventInit=true;		
		// recreate object
		this.dependentCtrls = [];
		//call parent
		Runner.controls.LookupWizard.superclass.constructor.call(this, cfg);
		//link for add new record or not
		this.addNew = $("#addnew_"+this.valContId);	
		// add change event for reload dependences
		this.addEvent(["change"]);
				
		var control = this, eventParams = {
			tName: this.lookupTable, 
			pageType: Runner.pages.constants.PAGE_ADD, 
			pageId: this.pageId,
			fName: this.fieldName,
			category: this.parentFieldName,
			lookupCtrl: this,
			modal: true,
			baseParams: {
				parId: this.id, 
				field: this.fieldName, 
				category: "", 
				table: this.table, 
				editType: Runner.pages.constants.ADD_ONTHEFLY
			},
			afterSave: {
		        fn: function(respObj, formObj, fieldControls, page){
		        	if (respObj.success && control.inputType == 'select'){
		        		control.addOption(respObj.vals[control.dispField], respObj.vals[control.linkField]);	
		        	}else{
		        		console.log('error when try add rec');
		        		return false;
		        	}
				},
		        scope: this
		    }
		};
		this.addNew.bind("click", eventParams, function(e){
			Runner.Event.prototype.stopEvent(e);
			if (control.parentCtrl){
				e.data.baseParams.category = control.parentCtrl.getValue();
			}
			control.pageId = Runner.pages.PageManager.openPage(e.data);
		});
	},
	/**
	 * Method that called just before ControlManager deleted link on this object
	 */
	destructor: function(){
		// call parent
		Runner.controls.LookupWizard.superclass.destructor.call(this);
		// may be need to clear each array element
		delete this.dependentCtrls;
	},
	/**
	 * Add dependent controls to array of controls
	 * @method 
	 * @param {array} ctrlDD array of control objects
	 */
	addDependentCtrls: function(ctrlsArr){
		for(var i=0;i<ctrlsArr.length;i++){
			this.dependentCtrls.push(ctrlsArr[i]);
		}
	},
	/**
	 * Clear links from children to there's parent ctrl	 * 
	 * @param {bool} triggerReload pass true to call reload function on children
	 */
	clearChildrenLinks: function(triggerReload){
		// reload all children
		for(var i=0;i<this.dependentCtrls.length;i++){
			// if children exists
			if(this.dependentCtrls[i]){
				this.dependentCtrls[i].clearParent(triggerReload);
			}			
		}
	},
	/**
	 * Deletes link to parent ctrl, and optionaly reloads this
	 * @param {bool} triggerReload pass true to call reload method
	 */
	clearParent: function(triggerReload){
		this.parentCtrl = null;
		if (triggerReload===true){
			this.reload();
		}
	},
	/**
	 * Set parent ctrl property
	 * @param {object} ctrl
	 */
	setParentCtrl: function(ctrl){
		this.parentCtrl = ctrl;
	},
	/**
	 * Call reload method of each dependent DD
	 * @method
	 */
	reloadDependeces: function(){
		// value of parent ctrl
		var masterCtrlVal = this.getValue();
		// if parent ctrl returns array value, we need to pass only first element of array
		masterCtrlVal = typeof(masterCtrlVal) == 'object' ? masterCtrlVal[0] : masterCtrlVal;
		// reload all children
		for(var i=0;i<this.dependentCtrls.length;i++){
			// if children exists
			if(this.dependentCtrls[i]){
				this.dependentCtrls[i].reload(masterCtrlVal);
			}			
		}
	},
	
	clearInvalidOnDependences: function(){
		for(var i=0;i<this.dependentCtrls.length;i++){
			// if children exists
			if(this.dependentCtrls[i]){
				this.dependentCtrls[i].clearInvalid();
			}			
		}
	},
	

	/**
	 * Override simple dropDown event,
	 * add reloading for dependent dropDowns
	 * @event
	 * @param {event} e
	 */
	"change": function(e){	
		// clear invalid state in dependent controls in anyway
		this.clearInvalidOnDependences();
		//call parent
		var vRes = this.validate();
		// call reload if value pass validation
		if (vRes.result){
			this.reloadDependeces();
			return true;
		}else{
			return false;
		}
	}
});