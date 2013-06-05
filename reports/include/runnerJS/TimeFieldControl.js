/**
 * Class for time fields with textField value editor, and timepicker optional
 */
Runner.controls.TimeField = Runner.extend(Runner.controls.Control, {
	/**
	 * Id of type elem. Need for submit, which used on serverside
	 * @type {string}
	 */
	typeHiddId: "",
	/**
	 * jQuery object of type elem format hidden element, which used on serverside
	 * @type {Object} 
	 */
	typeHiddElem: null,	
	/**
	 * Overrides parent constructor
	 * @param {Object} cfg
	 * @param {bool} cfg.useDatePicker
	 */
	constructor: function(cfg){
		// call parent
		Runner.controls.TimeField.superclass.constructor.call(this, cfg);	
		// add hidden field for date format on serverside
		this.typeHiddId = "type_"+this.goodFieldName+"_"+this.id;
		this.typeHiddElem = $("#"+this.typeHiddId);
		this.imgTime = $("#trigger-test-"+this.valContId);
		// hide timepicker for IE, because it doesn't work properly
		if (this.imgTime.length && !Runner.isIE){
			this.imgTime.css('visibility','visible');
		}
		// initialize control disabled
		if (cfg.disabled===true || cfg.disabled==="true"){
			this.setDisabled();
		}
		this.addEvent(["change"]);
		this.init();
		this.initTimePicker();
	},
	
	initTimePicker: function()	{
		var ctrl = this;
		var initializer = function(e){
			ctrl.imgTime.unbind("click");
			$(function(){
				var settings = Runner.pages.PageSettings.getFieldData(ctrl.table,ctrl.fieldName, 'timePick', {});
				
				var params = {
					handle: "#"+ctrl.imgTime.attr("id"),
					tName: ctrl.table,
					fName: ctrl.fieldName,
					rowId: ctrl.id,
					dropslide: {
						hideoutDelay: 2000, 
						trigger: 'click'
					},
					open: ctrl.open, 
					convention: settings['convention'],
					seconds: settings['showSec'],			
					rangeMin: settings['rangeMin'],
					apm: settings['apm'],
					
					select: function(e, dropslide){
						if(!dropslide){
							return;
						}
						var t = dropslide.getSelection();
						if (!settings['showSec']){
							t[2] = 0;
							tvar = t[2];
						}else{
							tvar = t[3];
						}
						var newVal = print_time(t[0], 
												t[1], 
												t[2], 
												settings['locale'], 
												(settings['convention'] == 12 ? (settings['apm']['am'] == tvar ? true : false) : false),
												settings['convention'],
												settings['apm']['am'],
												settings['apm']['pm']);
						ctrl.setValue(newVal, true);
						e.stopPropagation();
					}				
				};
				// settings
				params["range"+settings['convention']+"h"] = settings['range'];
				ctrl.valueElem.timepickr(params);
				ctrl.imgTime.click();
			});			
		}
		this.imgTime.bind("click", initializer);		
	},
	/**
	 * Override addValidation
	 * @param {string} type
	 */	
	addValidation: function(type){
		// date field can be validated only as isRequired
		if (type!="isRequired"){
			return false;
		}
		// call parent
		Runner.controls.TimeField.superclass.addValidation.call(this, type);
	},
	/**
	 * Clone html for iframe submit
	 * @method
	 * @return {array}
	 */
	getForSubmit: function(){
		return [this.valueElem.clone(), this.typeHiddElem.clone()];
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr true
	 * Sets hidden css style true for image "time"
	 * @method
	 */
	setDisabled: function()
	{
		if (this.valueElem.get(0) && this.imgTime)
		{
			this.valueElem.get(0).disabled = true;
			this.imgTime.css('visibility','hidden');
			return true;
		}else{
			return false;
		}			
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr false
	 * Sets visible css style true for image "time"
	 * @method
	 */
	setEnabled: function()
	{
		if (this.valueElem.get(0))
		{
			this.valueElem.get(0).disabled = false;
			if (!Runner.isIE){
				this.imgTime.css('visibility','visible');	
			}			
			return true;
		}else{
			return false;
		}
	},
	"change":function(e)
	{
		return this.validate().result;
	}
});