/**
 * Class for text fields control
 */
Runner.controls.TextField = Runner.extend(Runner.controls.Control, {
	constructor: function(cfg){
		this.addEvent(["change", "keyup"]);		
		Runner.controls.TextField.superclass.constructor.call(this, cfg);		
	}	
});


