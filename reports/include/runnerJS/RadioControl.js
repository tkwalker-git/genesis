/**
 * Radio control class
 * @requires Runner.controls.Control
 */
Runner.controls.RadioControl = Runner.extend(Runner.controls.Control, {
	/**
	 * Radio DOM elem id, starts from + _i 
	 * where i index of element, starts from 0
	 * @type {string} 
	 */
	radioElemsId: "",
	/**
	 * Radio jQuery obj
	 * @type {Object} 
	 */
	radioElemsArr: [],
	/**
	 * checkbox name attr 
	 * @type String
	 */
	radioElemsNameAttr: "",
	/**
	 * jQuery object which contains all radios
	 * @type {object}
	 */
	radioElem: null,
	/**
	 * Count of radio buttons
	 * @type {int}
	 */
	radioElemsCount: 0,
	/**
	 * Override parent contructor
	 * @constructor
	 * @param {Object} cfg
	 */
	constructor: function (cfg){
		this.radioElemsArr = new Array();
		cfg.stopEventInit = true;
		//call parent
		Runner.controls.RadioControl.superclass.constructor.call(this, cfg);	
		// id starts from
		this.radioElemsId = "radio_"+this.goodFieldName+"_"+this.id+"_";
		// radio elems name attr
		this.radioElemsNameAttr = "radio_"+this.goodFieldName+"_"+this.id; 
		// add radio DOM jQuery elem		
		this.radioElem = $('input[@name='+this.radioElemsNameAttr+']');
		// count of elems get from jQuery obj
		this.radioElemsCount = this.radioElem.length;
		// array of radios		
		for(var i=0;i<this.radioElemsCount;i++){
			this.radioElemsArr.push($("#"+this.radioElemsId+i));
			//elems for event are radios
			this.elemsForEvent.push(this.radioElemsArr[i].get(0));
		}		
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}
		// add events
		this.addEvent(["click"]);
		// init events
		this.init();
	},
	/**
	 * Set value to the control
	 * @param {string} val
	 * @param {bool} triggerEvent
	 * @return {bool}
	 */
	setValue: function(val, triggerEvent){
		var choosen = false;
		// loop for all radio elements
		for(var i=0;i<this.radioElemsCount;i++){
			if(this.radioElemsArr[i].val() == val){
				// set checked radio element
				this.radioElemsArr[i].get(0).checked = true;
				//set value in hidden eleme
				this.valueElem.val(val);
				choosen = true;
			}else{
				this.radioElemsArr[i].get(0).checked = false;
			}				
		}
		if(triggerEvent===true){
			this.fireEvent("change");
		}	
		return choosen;
	},
	/**
	 * Sets disable radio control
	 * @method
	 */
	setDisabled: function(){
		for(var i=0;i<this.radioElemsCount;i++){
			this.radioElemsArr[i].get(0).disabled = true;		
		}			
		return true;
	},	
	/**
	 * Sets disaqble attr false
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	setEnabled: function(){
		for(var i=0;i<this.radioElemsCount;i++){
			this.radioElemsArr[i].get(0).disabled = false;		
		}			
		return true;
	},	
	/**
	 * Clear blur event handler
	 */
	"blur": Runner.emptyFn,

	/*"change": function(e){
		// stop event
		this.stopEvent(e);				
		console.log(e, "change fired. This event object should used to change data in hidden element");
		//this.setValue(e.selected);
		// validate and return validation result
		return this.validate();		
	},*/
	
	"click": function(e){
		if (e.target.value != this.getValue()){	
			// set new val to hidden elem
			this.setValue(e.target.value, false);
			// validate and return validation result
			return this.validate().result;
		}else{
			return true;
		}
	}
	
});