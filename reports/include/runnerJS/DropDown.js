
/**
 * Select control class. 
 * @requires Runner.controls.LookupWizard
 */
Runner.controls.DropDownLookup = Runner.extend(Runner.controls.LookupWizard, {
	/**
	 * Number of values to select.
	 * @type {Number}
	 */
	multiSel: 1,
	/**
	 * DropDown DOM options array
	 * @type {array}
	 */
	optionsDOM: null,
	/**
	 * Override parent contructor 
	 * @param {object} cfg
	 * @param {int} cfg.multiSelect number of values to select. Must be >= 1
	 */
	constructor: function(cfg){
		// add multiSelect property
		this.multiSel = cfg.multiSel ? cfg.multiSel : 1;	
		// value element id
		this.valContId = "value"+(cfg.ctrlInd || "")+"_"+cfg.goodFieldName+"_"+cfg.id;
		// value elem
		this.valueElem = $("#"+this.valContId);	
		// add options array property
		this.optionsDOM = this.valueElem.get(0).options;
		// call parent
		Runner.controls.DropDownLookup.superclass.constructor.call(this, cfg);			
		// set input type
		this.inputType = "select";
		//set defaultValue
		this.defaultValue = this.getValue(true);
		//event elem 
		this.elemsForEvent = [this.valueElem.get(0)];		
		// init events handling
		this.init();			
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}
		
	},
	/**
	 * Sets value to DropDown. Tries to set all values from array if multiselect control
	 * @param {array} val
	 * @return {bool} true if success otherwise false
	 */
	setValue: function(vals, triggerEvent){
		// number of choosen options
		var choosen = 0;
		for(var i=0; i<this.valueElem.get(0).options.length;i++){
			for(var j=0;j<vals.length;j++){
				if(this.valueElem.get(0).options[i].value==vals[j]){
					this.optionsDOM[i].selected=true;
					choosen++;
					if(this.multiSel==1){
						this.valueElem.get(0).selectedIndex=i;
					}else{						
						break;
					}										
				}else{
					this.optionsDOM[i].selected=false;
				}// eo if
			}// eo for	
			if (choosen>0 && this.multiSel==1){
				break;
			}
		}// eo for
		
		if(triggerEvent===true){
			//console.log(triggerEvent, 'triggerEvent on change in DD called')
			this.fireEvent("change");
		}
		
		// if selected all than success
		if (choosen == vals.length && choosen <= this.multiSel){
			return true;
		}else{
			return false;	
		}		
	},
	/**
	 * Returns values from dropDown. 
	 * @method
	 * @return {array}
	 */
	getValue: function(returnArray){
		var selVals = [];
		// loop for all options
		if(this.optionsDOM.length){
			for (var i=0; i<this.optionsDOM.length;i++){
				if (this.optionsDOM[i].selected)
					selVals.push(this.optionsDOM[i].value)
			}
		}else{
			for(var i=0; i<this.valueElem.get(0).options.length;i++){
				if(this.valueElem.get(0).options[i].selected){
						selVals.push(this.valueElem.get(0).options[i].value);
				}
			}
		}
		if(returnArray===true)
			return selVals;
		if(selVals.length>1)
			return selVals;
		else if(selVals.length==1)
			return selVals[0];
		else
			return "";
	},
	/**
	 * Checks if control value is empty. Used for isRequired validation
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		var selVals = this.getValue();
		if(typeof selVals!='object')
			return selVals==="";
		return false;
	},
	/**
	 * Deletes all options from ctrl
	 * @method
	 */
	clearOptions: function(){
		var select = this.valueElem.get(0);
		for(var i=this.optionsDOM.length-1; i>-1;i--){
			select.remove(i);
		}		
	},
	/**
	 * Adds option to select
	 * may be need to add options to specified index?
	 * @param {string} text
	 * @param {string} val
	 */
	addOption: function(text, val){
		this.valueElem.append(new Option(text, val));
	},
	/**
	 * Add options from array.
	 * Array must have such structure:
	 * array[0] = value, array[1] = text,
	 * array[2] = value, array[3] = text,
	 * 2*i - indexes of values; 2*i+1 - indexes of text. I starts from 0   
	 * @param {array} optionsArr
	 */
	addOptionsArr: function(optionsArr){		
		for(var i=0; i < optionsArr.length - 1; i=i+2){ 
			this.addOption(optionsArr[i+1], optionsArr[i]);
		}
	},	
	/**
	 * First loading, without ajax. Should be called directly
	 * @param {string} txt unparsed values for options
	 * @param {string} selectValue unparsed values of selected options
	 */
	preload: function(vals, selectValue){
		// clear all old options
		this.clearOptions();	
		// add empty option for non multiple select
		if (this.multiSel==1){
			// add empty option for non multiselect
			this.addOption(Runner.lang.constants.TEXT_PLEASE_SELECT, "");				
		}
		// load options
		this.addOptionsArr(vals);
		// if only one values except please select, so choose it
		if (this.optionsDOM.length==2){
			this.setValue([this.optionsDOM[1].value], false);	
		}else if(this.optionsDOM.length>0){
			this.setValue([this.optionsDOM[0].value], false);	
		}		
		if (this.multiSel == 1){			
			selectValue = [selectValue];
		}
		// don't need to use ajax reload call
		this.setValue(selectValue, false);		
	},	
	/**
	 * Reloading dropdown. Called by change event handler
	 * @param {string} value of master ctrl
	 */
	reload: function(masterCtrlValue){	
		var fName = this.fieldName, tName = this.table, rowId = this.id;
		// can't reload if no parent ctrl - for safety use
		if (masterCtrlValue && !this.parentCtrl){
			return false;
		}	
		//ajax params
		var ajaxParams = {
			// ctrl fieldName
			field: myEncode(fName),
			// value of master ctrl. Only first val from arr, because multiDrop cannot be master
			value: myEncode((masterCtrlValue !== undefined ? masterCtrlValue : '')),
			// is exist parent, indicator
			isExistParent: (this.parentCtrl ? 1 : 0),
			// tag name of ctrl
			type: this.valueElem[0].tagName,
			// page mode add, edit, etc..
			mode: this.mode,
			// random value for prevent caching
		    rndVal: (new Date().getTime())
		};				
		// for handler closure
		var ctrl = this;	
		// get content		
		$.get(this.shortTableName+"_autocomplete.php", ajaxParams, function(respObj, textStatus){	
			respObj = JSON.parse(respObj);
			var data = respObj.data;	
			// clear all options
			ctrl.clearOptions();	
			// add empty option for non multiple select if it doesn't comes from server data
			if (ctrl.multiSel==1){
				// add empty option for non multiselect
				ctrl.addOption(Runner.lang.constants.TEXT_PLEASE_SELECT, "");				
			}
			// load options
			ctrl.addOptionsArr(data);
			
			// if only one values except please select, so choose it
			if (ctrl.optionsDOM.length==2 && ctrl.multiSel==1){
				ctrl.setValue([ctrl.optionsDOM[1].value], false);	
			}else if(ctrl.optionsDOM.length==1 && ctrl.multiSel>1){
				ctrl.setValue([ctrl.optionsDOM[0].value], false);	
			}			
			// fire change event, for reload dependent ctrls
			ctrl.fireEvent("change");	
			// after reload clear invalid massages			
			ctrl.clearInvalid();
		});
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr true
	 * Sets hidden css style true for link add New
	 * @method
	 */
	setDisabled: function(){
		if (this.valueElem){
			this.valueElem.get(0).disabled = true;			
			if (this.addNew){
				this.addNew.css('visibility','hidden');					
			}			
			return true;
		}
		return false;			
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr false
	 * Sets visible css style true for link add New
	 * @method
	 */
	setEnabled: function(){
		if (this.valueElem){
			this.valueElem.get(0).disabled = false;
			if (this.addNew){
				this.addNew.css('visibility','visible');					
			}	
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Clone html for iframe submit.
	 * jQuery clone method won't clone object with new selected values
	 * that's why we need to set values in clone object separetely
	 * @return {array}
	 */
	getForSubmit: function(){
		var clone = this.valueElem.clone(), selVals = this.getValue(true);
		var cloneOpt = clone.get(0).options;
		for(var i=0;i<cloneOpt.length;i++){
			for(var j=0;j<selVals.length;j++){
				if(cloneOpt[i].value==selVals[j]){
					if(this.multiSel==1)
						clone.get(0).selectedIndex = i;
					cloneOpt[i].selected = true;
					break;
				}
				else
				{
					cloneOpt[i].selected = false;
				}// eo if
			}// eo for	
		}// eo for
		
		return [clone];
	},

	/**
	 * Drop custom function for blur event
	 * @param {Object} e
	 */
	"blur": Runner.emptyFn	
});