// create namespace
Runner.namespace('Runner.form');
Runner.form.BasicForm = function(cfg){
	
	this.fieldControls = [];
	this.fields = [];
	this.addElems = [];
	this.ajaxForm = {};
	this.baseParams = {};
	Runner.apply(this, cfg);
   
    this.addEvents('beforeSubmit', 'successSubmit', 'submitFailed', 'validationFailed');
    	
	
	if (this.beforeSubmit){
		this.on({'beforeSubmit': this.beforeSubmit});
	}
	if (this.successSubmit){
		this.on({'successSubmit': this.successSubmit});
	}
	if (this.submitFailed){
		this.on({'submitFailed': this.submitFailed});
	}
	if (this.validationFailed){
		this.on({'validationFailed': this.validationFailed});
	}
       
    Runner.form.BasicForm.superclass.constructor.call(this. cfg);
    
    if (cfg.initImmediately){
    	this.initForm();
    }
};




Runner.form.BasicForm = Runner.extend(Runner.form.BasicForm, Runner.util.Observable, {
	
	fields: null,
	
	fieldControls: null,
	
	addElems: null,
	
	isFileUpload: false,
	
	standardSubmit: false,
	
	formEl: null,
	
	ioEl: null,
	
	ioElId: '',
	
	submitUrl: '',
	
	method: 'GET',
	
	id: -1,
	
	baseParams: null,
	
	tName: '',
	
	shortTName: '',	
	
	target: '',
	
	ajaxForm: null,
	
	autoValidation: true,
	
	addRndVal: true,
	
	initControls: function(){
		
	},
	
	destructor: function(leaveControls){
		if (leaveControls === true){
			for(var i=0;i<this.fieldControls.length;i++){
				this.fieldControls[i].unregister();
			}	
		}
				
		if (this.ioEl){
			$(this.ioEl).remove();
		}
		if (this.formEl){
			$(this.formEl).remove();
		}
	},
	
	submit: function(){
		
		if (!this.validate()){
			return false;
		};
		
		var beforeSubmitRes = this.fireEvent('beforeSubmit', this);
		if (beforeSubmitRes === false){
			return false;
		}
		if (this.isFileUpload || this.standardSubmit){
			this.initForm();
			this.addFormSubmit();			
			//this.fireEvent('beforeSubmit', this);
			this.formEl.submit();
		}else{
			this.addFormSubmit();
			//this.fireEvent('beforeSubmit', this);
			// for closure
			var formObj = this;			
			$.ajax({
				url: this.submitUrl,
				type: this.method,
				data: this.ajaxForm,
				success: function(data, textStatus, XMLHttpRequest) {
					var respObj = JSON.parse(data);
					formObj.fireEvent("successSubmit", respObj, formObj, formObj.fieldControls);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					formObj.fireEvent("successFailed", respObj, formObj, formObj.fieldControls);
				}
			});
		}
		return true;
	},
	
	initForm: function(){
		if (this.isFormReady){
			//this.clearForm();
			return;
		}
		if (this.isFileUpload && !this.standardSubmit){
			this.createIFrame();
			this.createForm();			
		}else if(this.standardSubmit){
			this.createForm();
		}
		this.isFormReady = true;
	},
	
	clearForm: function(){
		if (this.formEl){
			$(this.formEl).children().remove();
			return true;	
		}
		this.ajaxForm = {};
	},
	
	addFormSubmit: function(){
		if (this.addRndVal){
			this.baseParams["rndVal"] = Math.random();
		}
		if (this.formEl){
			var arrClns;
			for(var i=0; i<this.fieldControls.length; i++){
				arrClns = this.fieldControls[i].getForSubmit();
				for (var j = 0; j < arrClns.length; j++){ 
					$(arrClns[j]).appendTo(this.formEl);
				}
			}
			for(var param in this.baseParams){
				this.addToForm(param, this.baseParams[param]);
			}
			for(var i=0; i<this.addElems.length; i++){
				$(this.addElems[i]).appendTo(this.formEl);
			}			
		}else{
			this.ajaxForm = Runner.apply(this.ajaxForm, this.baseParams);
			for(var i=0; i<this.fieldControls.length; i++){
				this.ajaxForm[this.fieldControls[i].fieldName] = this.fieldControls[i].getStringValue(); 
			}
		}
		return true;
	},
	
	addToForm: function(id, val){
		if (typeof val == 'undefined' || typeof id == 'undefined' || val === null || id === null){
			return false;
		}
		
		if (this.isFileUpload || this.standardSubmit){
	    	if (!this.formEl){
	    		this.initForm();
	    	}	    	
	    	var formElem = document.createElement('INPUT');
	    	$(formElem).attr('type', 'hidden').attr('name', id).attr('id', id).val(val.toString()).appendTo(this.formEl);
		}else{
			this.ajaxForm[id] = val;
		}
    },
	
    addElemToForm: function(el){
    	if (!el){
    		return false;
    	}
    	if ($(el).attr("id") === ""){
			return false;
		}
    	if (this.isFileUpload || this.standardSubmit){
    		if (!this.formEl){
    			return false;
    		}
    		$(el).appendTo(this.formEl);
    	}else{
			this.ajaxForm[$(el).attr("id")] = $(el).val();
		}
    },
    
	validate: function(){
		if (!this.autoValidation){
			return true;
		}
		var vRes;
		for(var i=0; i<this.fieldControls.length; i++){
			vRes = this.fieldControls[i].validate();			
			if (!vRes.result){
				this.fireEvent("validationFailed", this, this.fieldControls);
				this.fieldControls[i].setFocus();
				return false;
			}			
		}
		return true;
	},
	
	createIFrame: function(){
		if (this.ioEl){
			return false;
		}
		var frameId = 'uploadFrame_'+this.id;
		
		if(Runner.isIE){
			this.ioEl = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
			if(Runner.isSecure){
				this.ioEl.src = 'javascript:false';
			}
		}
		else{
			this.ioEl = document.createElement('iframe');
			this.ioEl.id = frameId;
			this.ioEl.name = frameId;
		}

		this.ioEl.style.position = 'absolute';
		this.ioEl.style.top = '-1000px';
		this.ioEl.style.left = '-1000px';

		document.body.appendChild(this.ioEl);
		// for closure
		var basicForm = this;
		
		this.ioEl.onload = function(e){
			var iframeNode = $("#"+frameId)[0], ioDoc;
			if (iframeNode.contentDocument){
				ioDoc = iframeNode.contentDocument;
			}else if(iframeNode.contentWindow){
				ioDoc = iframeNode.contentWindow.document;
			}else{
				ioDoc = iframeNode.document;
			}
			if (ioDoc.body.innerHTML!=''){
				console.log($(ioDoc.body.innerHTML).text(), 'ioDoc.body.innerHTML', this);
				var responseObj = JSON.parse($(ioDoc.body.innerHTML).text());
				basicForm.fireEvent('successSubmit', responseObj, basicForm, basicForm.fieldControls);
			}else{
				basicForm.fireEvent('submitFailed', {}, basicForm, basicForm.fieldControls);
			}
		};		
		
		this.ioElId = frameId;
		
		return this.ioEl;
	},
	
	createForm: function(){
		if (this.formEl){
			return false;
		}
		this.formEl = document.createElement('FORM');		
		
		this.formEl.action = this.submitUrl;
		this.formEl.method = this.method;
		
		if (this.target){
			this.formEl.target = this.target;
		}
		
		$(this.formEl).css('display', 'none');
		if (this.isFileUpload){
			this.formEl.enctype = "multipart/form-data";
		}
		
		if (this.ioEl){
			$(this.formEl).attr('target', this.ioElId);
		}
		
		document.body.appendChild(this.formEl);
		
		/*$(this.formEl).bind('submit', {basicForm: this}, function(e){
			e.data.basicForm.fireEvent('afterSubmit');
		});*/
		
		return this.formEl;
	}	
});


