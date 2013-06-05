/**
 * TextArea control class
 */
Runner.controls.TextArea = Runner.extend(Runner.controls.Control,{
	/**
	 * Override constructor
	 * @param {Object} cfg
	 */
	constructor: function(cfg){		
		this.addEvent(["change", "keyup"]);		
		// call parent
		Runner.controls.TextArea.superclass.constructor.call(this, cfg);
		// change input type, because textarea don't have type attr
		this.inputType = "textarea";		
	},
	/**
	 * Clone html for iframe submit
	 * @return {array}
	 */
	getForSubmit: function(){
		//return [$(document.createElement("INPUT")).attr('type', 'hidden').attr('id', this.valueElem.attr('id')).attr('name', this.valueElem.attr('name')).val(this.getValue())];
		return [this.valueElem.clone().val(this.getValue())]
	}
});



