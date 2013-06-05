/**
 * Base abstract class for all file controls. Should not be created directly.
 * @requires Runner.controls.Control
 */
Runner.controls.FileControl = Runner.extend(Runner.controls.Control, {
	/**
	 * Radio DOM elem id
	 * @type {string} 
	 */
	radioElemsName: "",
	/**
	 * Radio jQuery obj
	 * @type {Object} 
	 */
	radioElems: null,
	
	/**
	 * Override parent contructor
	 * @constructor
	 * @param {Object} cfg
	 */
	constructor: function (cfg){
		this.radioElems = {};
		cfg.stopEventInit = true;
		//call parent
		Runner.controls.FileControl.superclass.constructor.call(this, cfg);		
		// add radio DOM elem ID		
		this.radioElemsName = "type_"+this.goodFieldName+"_"+this.id;
		// for ASP version in inline add mode, there are no radios, but one input type hidden
		if ($('#'+this.radioElemsName).length){
			this.getChekedRadio = function(){
				return false;
			}
		}
		// add radio DOM elem ID,
		this.getRadioControls();
		// add events
		this.events = ["change"];
		// clear blur event
		delete this["blur"];
		//event elem
		this.elemsForEvent = [this.valueElem.get(0)];
		// init events
		this.init();
	},
	/**
	 * Clear blur event handler
	 */
	"blur": Runner.emptyFn,
	/**
	 * Add change event base handler
	 * @param {Object} e
	 */
	"change": function(e){
		// stop event
		this.stopEvent(e);
		// set radio button to update
		this.changeRadio("updateRadio");
		// validate and return validation result
		return this.validate();		
	},
	/**
	 * Radio buttons switcher. Call when need change radio
	 * @param {string} radioToCheck Name of radio button.
	 */
	changeRadio: function(radioToCheck){
		for(var radio in this.radioElems){			
			// if exists radio button
			if (radio == radioToCheck && this.radioElems[radio]!=false){
				this.radioElems[radio].elem.get(0).checked = true;
				this.radioElems[radio].cheked = true;
			// if not exists return false	
			}else if(radio == radioToCheck && this.radioElems[radio]==false){
				return false;
			// switch other radios	
			}else if(this.radioElems[radio]!=false){
				this.radioElems[radio].elem.get(0).checked = false;
				this.radioElems[radio].cheked = false;
			}
		}		
		// in success
		return true;
	},
	/**
	 * Get object which contains radio elems
	 * @method
	 * @return {bool}
	 */
	getRadioControls: function(){		
		var keepRadio = $('#'+this.radioElemsName+'_keep');
		var ctrl = this;
		keepRadio.bind('click', function(e){		
			ctrl.changeRadio('keepRadio');
		});
		var deleteRadio = $('#'+this.radioElemsName+'_delete');
		deleteRadio.bind('click', function(e){		
			ctrl.changeRadio('deleteRadio');
		});
		var updateRadio = $('#'+this.radioElemsName+'_update');
		updateRadio.bind('click', function(e){		
			ctrl.changeRadio('updateRadio');
		});
		// create radioElems obj
		this.radioElems["keepRadio"] = keepRadio.length ? {elem: keepRadio, cheked: true} : false;		
		this.radioElems["deleteRadio"] = deleteRadio.length ? {elem: deleteRadio, cheked: false} : false;
		this.radioElems["updateRadio"] = updateRadio.length ? {elem: updateRadio, cheked: false} : false;
		return true;
	},
	/**
	 * Return name of cheked radio
	 * @return {string}
	 */
	getChekedRadio: function(){
		for(var radio in this.radioElems){		
			if (this.radioElems[radio]!=false && this.radioElems[radio].cheked === true){
				return radio;
			}
		}
		return false;
	},
	
	validate: function(valArr){
		if (this.mode == 1 || this.mode == 7){
			return {result: true, messageArr: []};
		}
		return Runner.controls.FileControl.superclass.validate.call(this, valArr);	
	},
	
	/**
	 * Returns array of jQuery object for inline submit
	 * @return {array}
	 */
	getForSubmit: function(){		
		// array of fileValue, and cheked radio
		var radio = this.getChekedRadio();
		var cloneArr = [];
		// make real clone of radio, to prevent troubles in IE
		if (radio){
			var radioClone = document.createElement('input');			
			$(radioClone).attr('type', 'hidden');
			$(radioClone).attr('id', this.radioElems[radio].elem.attr('id'));
			$(radioClone).attr('name', this.radioElems[radio].elem.attr('name'));
			$(radioClone).val(this.radioElems[radio].elem.val());			
			cloneArr.push($(radioClone));
		// for ASP version in inline add mode, there are no radios, but one input type hidden
		}else if($('#'+this.radioElemsName).length){
			cloneArr.push($('#'+this.radioElemsName));			
		}
		// add real file elem
		var realFile = this.valueElem;
		var clone = this.valueElem.clone(true);
		clone.insertAfter(realFile); 
		cloneArr.push(realFile);		
		return cloneArr;
	}
});

/**
 * Class for image field controls.
 * @requires Runner.controls.FileControl
 */
Runner.controls.ImageField = Runner.extend(Runner.controls.FileControl, {
	
	imgElem: null,
	
	
	/**
	 * Override parent contructor
	 * @constructor
	 * @param {Object} cfg
	 */
	constructor: function(cfg){
		//call parent
		Runner.controls.ImageField.superclass.constructor.call(this, cfg);	
		this.imgElemId = "image_"+this.goodFieldName+"_"+this.id;	
		this.imgElem = $("#"+this.imgElemId);	
	},
	
	setValue: function(val, triggerEvent){
		if ($(val).attr('src')){
			this.imgElem.attr('src', ($(val).attr('src') + "&rndVal=" + Math.random()));
		}else{
			Runner.controls.ImageField.superclass.setValue.call(this, val, triggerEvent);	
		}
	}
	
	
});

/**
 * Class for file field controls. For images use Runner.controls.ImageField
 * @requires Runner.controls.FileControl
 */
Runner.controls.FileField = Runner.extend(Runner.controls.FileControl, {	
	/**
	 * Indicates if need to add timeStamp to fileName
	 * @type {bool} 
	 */
	addTimeStamp: false,
	/**
	 * ID of filename elem
	 * @type {string}
	 */
	fileNameElemId: "",
	/**
	 * Filename textfield jQuery object
	 * @param {Object} 
	 */
	fileNameElem: null,
	/**
	 * ID of hidden fileName DOM elem
	 * @type String
	 */
	fileHiddElemId: "",
	/**
	 * jQuery object of hidden fileName DOM elem
	 * @type {object} 
	 */
	fileHiddElem: null,	
	/**
	 * Override parent contructor
	 * @constructor
	 * @param {Object} cfg
	 * @param {bool} cfg.addTimeStamp
	 */
	constructor: function (cfg){
		cfg.stopEventInit = true;
		//call parent
		Runner.controls.FileField.superclass.constructor.call(this, cfg);		
		// add fileName DOM elem	
		this.fileNameElemId = "filename_"+this.goodFieldName+"_"+this.id;	
		this.fileNameElem = $("#"+this.fileNameElemId).length ? $("#"+this.fileNameElemId) : null; 
		// add fileName hidden DOM elem
		this.fileHiddElemId = "filenameHidden_"+this.goodFieldName+"_"+this.id;	
		this.fileHiddElem = $("#"+this.fileHiddElemId).length ? $("#"+this.fileHiddElemId) : null;
		//timeStamp to fileName indicator
		this.addTimeStamp = cfg.addTimeStamp ? cfg.addTimeStamp : false;
		// add radio buttons style switchers
		for (radio in this.radioElems){		
			// if exists radio	
			if (this.radioElems[radio]){
				// create closure event handler
				var objScope = this;
				// add handler
				this.radioElems[radio].elem.bind('click', function(e){					
					// get name of radio object
					var radioTypeStartFrom = this.id.lastIndexOf('_');
					var radioTypeName = this.id.substring(radioTypeStartFrom+1)+'Radio';
					// change styles
					objScope.changeControlsStyles(radioTypeName);
				});//)[0].onclick = onRadioClickHandler//.call(this, this)
			}
		}			
	},
	
	/**
	 * Override addValidation
	 * @method
	 * @param {string} type
	 */
	addValidation: function(type){
		// date field can be validated only as isRequired
		if (type!="isRequired"){
			return false;
		}
		// call parent
		Runner.controls.FileField.superclass.addValidation.call(this, type);
	},
	/**
	 * Cuts name of file from path
	 * @param {string} path
	 * @return {string}
	 */
	getFileNameFromPath: function(path){
		var wpos=path.lastIndexOf('\\'); 
		var upos=path.lastIndexOf('/'); 
		var pos=wpos; 
		if(upos>wpos)
			pos=upos; 
		return path.substr(pos+1);
	},
	/**
	 * Override setValue function, for files need to change radio control status
	 * @method
	 * @param {file} val
	 */
	setValue: function(val, triggerEvent){		
		var valWithStamp = "", 
			fileName = "";
		// if need to get filename without path
		if (this.fileNameElem != null || this.addTimeStamp){
			fileName = this.getFileNameFromPath(this.valueElem.val());
		}
		// add timestamp if needed
		if (this.addTimeStamp){			
			var valWithStamp = addTimestamp(fileName);
		}
		// if name element exists, set new value		
		if (this.fileNameElem != null){
			this.fileNameElem.val(valWithStamp || fileName);
		}
		
		if(triggerEvent===true){
			this.fireEvent("change");
		}
	},	
	/**
	 * Change file value event handler. 
	 * Changes radio to update, validates, and change fileName if file pass validation
	 * @method
	 * @param {Object} e
	 */
	"change": function(e){
		this.stopEvent(e);		
		this.changeRadio("updateRadio");
		var vRes = this.validate();		
		if (vRes.result){			
			var vl = this.getValue();
			this.setValue(vl, false);
		}
		return vRes.result;
	},
	/**
	 * Override radio buttons switcher, add call change styles method
	 * @param {string} radioToCheck
	 */
	changeRadio: function(radioToCheck){
		// change styles
		this.changeControlsStyles(radioToCheck);
		// call parent
		Runner.controls.FileField.superclass.changeRadio.call(this, radioToCheck);		
	},
	/**
	 * Change styles and set disabled filename field
	 * @param {Object} radioToCheck
	 */
	changeControlsStyles: function(radioToCheck){
		// if such radio button defined
		if (!this.radioElems[radioToCheck]){
			return false;
		}
		// if there is filename that need to be changed
		if (this.fileNameElem == null) {
			return false;
		}		
		// if choosed delete
		if (radioToCheck == "deleteRadio"){
			this.fileNameElem.css('backgroundColor','gainsboro');
			this.fileNameElem[0].disabled=true;
			return true;
		// if choosed update or keep
		}else if(radioToCheck == "updateRadio" || radioToCheck == "keepRadio"){
			this.fileNameElem.css('backgroundColor','white');
			this.fileNameElem[0].disabled=false;
			return true;
		// in other way return false
		}else{
			return false;
		}
	},
	/**
	 * Checks if control value is empty. Used for isRequired validation
	 * For files has specific criterias
	 * @override
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		if (this.fileHiddElem && this.fileHiddElem.val()!=""){
			return this.radioElems["deleteRadio"].cheked === false;
		}else{
			return (this.getValue().toString() == "" && (this.radioElems["updateRadio"] === false || this.radioElems["keepRadio"].cheked === true));
		}
	},
	/**
	 * Get fileName from fileName type text elem.
	 * @return {string}
	 */
	getFileName: function(){
		if (this.fileHiddElem){
			return this.fileHiddElem.val();
		}else{
			return false;
		}
	},
	/**
	 * Set fileName to fileName type text elem.
	 * @param {string} fileName
	 * @return {Boolean}
	 */
	setFileName: function(fileName){
		if (this.fileHiddElem){
			this.fileHiddElem.val(fileName);
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Returns array of jQuery object for inline submit
	 * @return {array}
	 */
	getForSubmit: function(){
		var cloneArr = Runner.controls.ImageField.superclass.getForSubmit.call(this);	
		if (this.fileNameElem){
			cloneArr.push(this.fileNameElem.clone());
		}
		if (this.fileHiddElem){
			cloneArr.push(this.fileHiddElem.clone());
		}
		return cloneArr;
	}
	
	
});