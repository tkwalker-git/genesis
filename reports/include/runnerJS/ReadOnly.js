/**
 * Class for read only control
 */
Runner.controls.ReadOnly = Runner.extend(Runner.emptyFn, {
	
	value: "",
	
 	fieldName: "",
 	
 	goodFieldName: "",
 	
 	shortTableName: "",
 	
	id: "",
	
	table: "",
	
	ctrlType: "",
	
	ctrlInd: -1,
	
	mode: '',
	
	constructor: function(cfg) {
        Runner.apply(this, cfg);
		Runner.controls.ReadOnly.superclass.constructor.call(this, cfg);
		Runner.controls.ControlManager.register(this);	
	},
	
	getValue: function(){
		return this.value;
	},
	
	setValue: Runner.emptyFn,
	
	destructor: Runner.emptyFn,
	
	on: Runner.emptyFn,
	
	unregister: function(){
		Runner.controls.ControlManager.unregister(this.table, this.id, this.fieldName);
	},
	
	validate: function(){
		return {result: true};
	},
	
	getForSubmit: function(){
		return [];
	},
	
	getControlType: function(){
		return "readonly";
	},
	
	setFocus: function(){
		return false;
	}
});


