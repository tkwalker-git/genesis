/**
 * Common base class for rte fields
 */
Runner.controls.RTEField = Runner.extend(Runner.controls.Control, {
	
	iframeElemId: "",
	
	iframeElem: null,
	
	constructor: function(cfg){
		// may be need to turn off event initialization before iframe loaded
		cfg.stopEventInit=true;
		Runner.controls.RTEField.superclass.constructor.call(this, cfg);
		this.inputType = "RTE";
		this.tName = cfg.table;
		this.iframeElemId = this.valContId;
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}	
	},
	/**
	 * Indicates used datepicker with control or not
	 * @type {bool} cfg
	 */
	useRTE: false,
	/**
	 * Override addValidation
	 * @param {string} type
	 */
	addValidation: function(type)
	{
		// date field can be validated only as isRequired
		if (type!="isRequired")
			return false;
		// call parent
		Runner.controls.RTEField.superclass.addValidation.call(this, type);
	},
	
	getForSubmit: function()
	{
		var clElem = $('<input type="hidden" name="'+this.iframeElemId+'">').clone();
		$(clElem).val(this.getValue());
		return [clElem];
	},
	setDisabled: function()
	{
		if (this.iframeElem){
			var val = this.getValue();
			this.iframeElem.css('display','none');
			this.spanContElem.prepend('<div id="disabledRTE'+this.fieldName+'_'+this.id+'">'+val+'</div>')
			return true;
		}else{
			return false;
		}
	},
	setEnabled: function()
	{
		if (this.iframeElem){
			$("#disabledRTE"+this.fieldName+'_'+this.id).remove();
			this.iframeElem.css('display','block');
			return true;
		}else{
			return false;
		}
	}
});


Runner.controls.RTEInnova = Runner.extend(Runner.controls.RTEField, 
{
	constructor: function(cfg)
	{
		Runner.controls.RTEInnova.superclass.constructor.call(this, cfg);
		this.useRTE = cfg.useRTE ? cfg.useRTE : false;
		this.iframeElem = $('#'+this.iframeElemId);
		if($.browser.safari && Runner.pages.PageSettings.getTableData(this.tName, "pageMode", 0) == 2){
			src = this.iframeElem.attr('src');
			this.iframeElem.attr('src',"");
			this.iframeElem.attr('src', src);
		}	
		if(this.useRTE == "INNOVA")
			this.innerIframeId = 'idContentoEdit'+this.goodFieldName+'_'+this.id;
	},
	
	getValue: function()
	{	
		var val;
		if(this.iframeElem)
		{	
			if(this.useRTE=='INNOVA')
				val = this.iframeElem.contents().find('#'+this.innerIframeId).contents().find('body').html();
			else
				val = this.iframeElem.contents().find('#'+this.iframeElemId).contents().find('body').html();
				
			if(val)
				val = val.trim();
				
			if(val == '<br>')
				val = '';
				
			return val;
		}
		else 
			return false;
	},
		
	setValue: function(val)
	{
		if(this.useRTE=='INNOVA')
			this.iframeElem.contents().find('#'+this.innerIframeId).contents().find('body').html(val);
		else
			this.iframeElem.contents().find('#'+this.iframeElemId).contents().find('body').html(val);
	}
	
});

Runner.controls.RTECK = Runner.extend(Runner.controls.RTEField, {
	
	constructor: function(cfg){	
		Runner.controls.RTECK.superclass.constructor.call(this, cfg);
	},
	
	getEditor: function(){
		if (!window.CKEDITOR){
			return false;			
		}
		if (typeof window.CKEDITOR.instances[this.valContId] == 'undefined'){
			return false;	
		}
		return window.CKEDITOR.instances[this.valContId];
	},
	
	destructor: function(){
		var editor = this.getEditor();
		if (editor!==false){
			CKEDITOR.remove(editor);
		}		
	},
	
	getValue: function(){
		var editor = this.getEditor();
		if (editor===false){
			return false;	
		}
		return editor.getData();
	},
	
	setValue: function(val){
		var editor = this.getEditor();
		if (editor===false){
			return false;	
		}
		editor.setData(val);
		
		return true;	
	}
});


