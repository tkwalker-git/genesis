// create namespace
Runner.namespace('Runner.util.inlineEditing');

/**
 * Base abstract class for InlineAdd and InlineEdit
 * provides base functionality and event handling
 */
Runner.util.inlineEditing.InlineEditor = Runner.extend(Runner.util.Observable, {
	
	addArea: null,
	
	tName: "",
	
	shortTName: "",
	
	id: -1,
	
	rows: null,
	
	ajaxRequestUrl: null,
	
	lookupTable: "",
	
	categoryValue: "",
	
	lookupField: "",
	
	fNames: null,
	
	pageType: "",
	
	submitUrl: "",
	
	isUseInlineEdit: true,
	
	isUseIcons: true,
	
	isUseEdit: true,
	
	isUseCopy: true,
	
	isUseView: true,
	
	isUseResize: false,
		
	showEditInPopup: false,
	
	showViewInPopup: false,
	
	saveAllButt: null,
	
	cancelAllButt: null,
	
	massRecButtEditMode: false,
	
	isUseFileUpload: true,
	
	hideRevertButt: false,
	
	hideSaveButt: false,
	
	baseParams: null,
	
	constructor: function(cfg){
		this.rows = [];
		this.fNames = [];
		this.baseParams = {};
		
		Runner.apply(this, cfg);
		Runner.util.inlineEditing.InlineEditor.superclass.constructor.call(this, cfg);	
		
		var permis = Runner.pages.PageSettings.getTableData(this.tName, "permissions", {});
		
		if (typeof cfg.isUseInlineEdit == "undefined"){
			this.isUseInlineEdit = Runner.pages.PageSettings.getTableData(this.tName, "isInlineEdit", false) && permis['edit'];
		}
		if (typeof cfg.isUseEdit == "undefined"){
			this.isUseEdit = permis['edit'];
		}
		if (typeof cfg.isUseCopy == "undefined"){
			this.isUseCopy = Runner.pages.PageSettings.getTableData(this.tName, "copy", false) && permis['add'];
		}
		if (typeof cfg.isUseView == "undefined"){
			this.isUseView = Runner.pages.PageSettings.getTableData(this.tName, "view", false) && permis['search'];
		}
		if (typeof cfg.isUseResize == "undefined"){
			this.isUseResize = Runner.pages.PageSettings.getTableData(this.tName, "isUseResize", false);
		}	
		
		this.addArea = $("#addarea"+this.id);
				
		this.addEvents(
			'rowsEdited',
			'createControls',
			'beforeSubmit',
			'afterSubmit',
			'beforeProcessNewRow',
			"revertRow",
			"validationFailed"
		);
		
		this.on('rowsEdited', function(){				
			this.toggleMassRecButt();
			this.calcTotals();
		}, this);				
		
		this.editAllButt = $("#edit_selected"+this.id);
		
		
		this.isUseIcons = Runner.pages.PageSettings.getTableData(this.tName, "listIcons");	
		
		this.rowPref = this.isUseResize ? "yui-rec" : "gridRow_";
	},
	
	init: function(){
		this.initButtons();		
	},
	
	reInit: function(id, gridRows){
		this.rows = gridRows;
		this.id = id;
		this.addArea = $("#addarea"+id);
	},
	
	initButtons: function(){
		this.saveAllButt = $("#saveall_edited"+this.id);
		this.cancelAllButt = $("#revertall_edited"+this.id);
		
		var inlineObj = this;
		this.saveAllButt.bind("click", function(e){
			inlineObj.saveAll();
			if (e.stopImmediatePropagation){
				e.stopImmediatePropagation();
			}			
		});
		
		this.cancelAllButt.bind("click", function(e){
			inlineObj.cancelAll();
			if (e.stopImmediatePropagation){
				e.stopImmediatePropagation();
			}
		});
	},
	
	parseForTotals: function(val, format){
		if (format=='Number'){
			return parseFloat(val);
		}else if(format=='Time'){
			var timeArr = val.split(":");
			if(timeArr.length!=3){
				return [0, 0, 0];
			}else{
				timeArr[0] = parseInt(timeArr[0],10);
				timeArr[1] = parseInt(timeArr[1],10);
				timeArr[2] = parseInt(timeArr[2],10);
				return timeArr;
			}				
		}else{
			return val.toString().trim();
		} 
	},
	
	calcTotalField: function(fName, format){
		var fVal, sec = 0, min = 0, hor = 0, totalVal = 0;
		for(var i=0;i<this.rows.length;i++){
			if (typeof this.rows[i].data[fName] == "undefined"){
				this.getValuesFromSpan(this.rows[i]);
			}
			fVal = this.parseForTotals(this.rows[i].data[fName], format);	
			if (format == "Time"){
				sec = sec+fVal[2];
				if(nsec>59){	
					sec=nsec-60;
					min+=1;
				}
				min = min+fVal[1];  
				if(nmin>59){				
					min = min-60;
					hor+=1;	
				}
				hor+=fVal[0];
			}else if(format == "Number"){
				totalVal+=fVal;
			}
		}
		if(format=='Time'){
			if(hor>23){
				var day = Math.round(hor/24);
				hor = hor-day*24;
			}
			totalVal = (day>0 ? day+'d ' : '')+(hor==0 ? '00' : hor)+':'+(min>9 ? min : (min==0 ? '00' :'0'+min))+':'+(sec>9 ? sec : (sec==0 ? '00' : '0'+sec));
		} 
		$("#total"+this.id+"_"+fName).html(totalVal);
	},
	
	calcCountField: function(fName){
		var fVal, totalVal = 0;
		for(var i=0;i<this.rows.length;i++){
			if (typeof this.rows[i].data[fName] == "undefined"){
				this.getValuesFromSpan(this.rows[i]);
			}
			fVal = this.rows[i].data[fName].toString().trim();	
			if (fVal){
				totalVal++;
			}
		}		 
		$("#total"+this.id+"_"+fName).html(totalVal);
	},
	
	calcAverageField: function(fName){
		var fVal, sec = 0, min = 0, hor = 0, totalVal = 0;
		for(var i=0;i<this.rows.length;i++){
			if (typeof this.rows[i].data[fName] == "undefined"){
				this.getValuesFromSpan(this.rows[i]);
			}
			fVal = this.parseForTotals(this.rows[i].data[fName], format);	
			if (format == "Time"){
				sec += fVal[2];
				min += fVal[1];
				hor += fVal[0];
			}else if(format == "Number"){
				totalVal+=fVal;
			}
		}
		if(format=='Time'){
			hor = Math.round(hor/this.rows.length);
			min = Math.round(hor/this.rows.length);
			sec = Math.round(hor/this.rows.length);
			if(hor>23){
				var day = Math.round(hor/24);
				hor = hor-day*24;
			}
			totalVal = (day>0 ? day+'d ' : '')+(hor==0 ? '00' : hor)+':'+(min>9 ? min : (min==0 ? '00' :'0'+min))+':'+(sec>9 ? sec : (sec==0 ? '00' : '0'+sec));
		}else{
			totalVal = Math.round(totalVal/this.rows.length);
		}
		$("#total"+this.id+"_"+fName).html(totalVal);
	},
	
	calcTotals: function(){
		for(var i=0; i<this.totalFields.length; i++){
			if (this.totalFields[i].type == "TOTAL"){
				this.calcTotalField(this.totalFields[i].fName, this.totalFields[i].format);
			}else if(this.totalFields[i].type == "COUNT"){
				this.calcCountField(this.totalFields[i].fName, this.totalFields[i].format);
			}else if(this.totalFields[i].type == "AVERAGE"){
				this.calcAverageField(this.totalFields[i].fName, this.totalFields[i].format);
			}
		};
	},
	
	initForm: function(row){
		if (row.basicForm){
			row.basicForm.fieldControls = Runner.controls.ControlManager.getAt(this.tName, row.id);
			row.basicForm.id = row.id;
			row.basicForm.fields = this.fNames;
		}else{		
			var inlineObj = this;

			row.basicForm = new Runner.form.BasicForm({
				fields: this.fNames,	
				fieldControls: Runner.controls.ControlManager.getAt(this.tName, row.id),			
				isFileUpload: this.isUseFileUpload,			
				standardSubmit: false,			
				submitUrl: this.submitUrl,			
				method: 'POST',
				id: row.id,
				baseParams: Runner.apply(this.baseParams, {a: 'added', editType: 'inline', id: row.id}),
			    successSubmit: {
			        fn: function(respObj, formObj, fieldControls){
						if (!respObj.success){
							//inlineObj.fireEvent("submitFailed", respObj, inlineObj, formObj, fieldControls);											
							inlineObj.makeError(respObj.message, row);	
						}else{
							inlineObj.afterSubmit(row, respObj);
						}						
						console.log(row, inlineObj, arguments, 'successSubmit args');
					},
			        scope: this
			    },
				submitFailed: {
			        fn: function(respObj, inlineObj, formObj, fieldControls){
			        	if (respObj.success === false){
			        		inlineObj.makeError(respObj.message, row);			        			
			        	}else{
			        		inlineObj.makeError("request doesn't complete", row);
			        	}
			        	inlineObj.fireEvent("submitFailed", {}, inlineObj, formObj, fieldControls);
					},
			        scope: this
			    },
				beforeSubmit: {
			        fn: function(formObj){
			        	inlineObj.fireEvent("beforeSubmit", row, inlineObj, formObj);
						console.log(row, inlineObj, arguments, 'beforeSubmit args');
					},
			        scope: this
			    },			    
			    validationFailed: {
			    	fn: function(formObj, fieldControls){
			    		inlineObj.fireEvent("validationFailed", formObj, fieldControls);
			    	},
			    	scope: this
			    }
			});
		}
	},
	
	cancelAll: function(){
		for(var i=0; i<this.rows.length; i++){
			if (!this.rows[i].submitted){
				if (this.rows[i].isAdd){
					Runner.util.inlineEditing.InlineAdd.prototype.cancelButtonHn.call(this, this.rows[i]);	
					i--;
				}else{
					Runner.util.inlineEditing.InlineEdit.prototype.cancelButtonHn.call(this, this.rows[i]);
				}
			}
		}
		this.toggleMassRecButt();
	},
	
	
	submit: function(row){
		//this.fireEvent("beforeSubmit", row);
		this.initForm(row);
		row.basicForm.submit();				
	},
	
	changeRowButtons: function(row){
		var pageId = this.id;
		var tempId = row.id;		
		
		if (!row.row.next().hasClass("shade")){
			row.row.addClass("shade");
		}
		
		$("*",row.row).each(function(j) {
			if(this.id == "editLink_add"+pageId){
				this.id = "editLink" + tempId;
				$(this).hide();			
			}else if(this.id == "copyLink_add" + pageId){
				this.id = "copyLink" + tempId;
				$(this).hide();
			}else if(this.id == "check_add" + pageId){
				this.id = "check"+pageId+"_" + tempId;
				$(this).hide();
			}else if(this.id == "viewLink_add" + pageId){
				this.id = "viewLink" + tempId;
				$(this).hide();
			}else if(this.id.substr(0,7)=="master_" && this.id.substr(this.id.length-4-pageId.toString().length)=="_add"+pageId){
				this.id = this.id.substr(0,this.id.length-4-pageId.toString().length)+tempId;
				$(this).hide();
			}else if(this.id.substr(this.id.length-8-pageId.toString().length)=="_preview"+pageId){
				this.id = this.id.substr(0,this.id.length-pageId.toString().length)+tempId;
				cntDet = $(this).find('span[id^=cntDet]');
				if($(cntDet).length){
					cntDetId = $(cntDet).attr('id');
					cntDetId+= tempId;
					$(cntDet).attr('id',cntDetId);
				}
				$(this).hide();
			}else if(this.id.substr(0,4+pageId.toString().length)=="add"+pageId+"_"){
				this.id = "edit"+tempId+'_'+this.id.substr(4+pageId.toString().length);
			}
		});
		
		
	},
	
	getControls: function(row, hideRevertButt, hideSaveButt){
		// for closure
		var inlineObject = this;
		
		var reqParams = {
			rndval: Math.random(),
			editType: "inline",
			id: row.id,
            isNeedSettings: !Runner.pages.PageSettings.checkSettings(this.tName),
            table: this.lookupTable,
            field: this.lookupField,
            category: this.categoryValue
		};
		if (row.keys){
			var i = 0;
			for(var key in row.keys){
				i++;
				reqParams['editid'+i] = escape(row.keys[key]);
			}
		}
		
		$.getJSON(this.ajaxRequestUrl, reqParams, function(ctrlsJSON){	
			if(ctrlsJSON.success || ctrlsJSON.success!==false)
				inlineObject.makeRowEditable(row, ctrlsJSON);			
			else
				inlineObject.makeError(ctrlsJSON.message, row);
			inlineObject.getEditBlock(row, hideRevertButt, hideSaveButt);			
		});	
	},	
	
	makeRowEditable: function(row, ctrlsJSON){
		for(var i=0; i<this.fNames.length; i++){			
			$('#edit'+row.id+'_'+Runner.goodFieldName(this.fNames[i])).html(ctrlsJSON['html'][this.fNames[i]]);			
		}		
		var ctrlsMap = ctrlsJSON.controlsMap[this.tName][this.pageType][row.id].controls,
			ctrlsArr = [];
		for(var i=0; i<ctrlsMap.length; i++){
			if (!this.fNames.isInArray(ctrlsMap[i].fieldName)){
				continue;
			}
			ctrlsMap[i].table = this.tName;
			ctrlsArr.push(Runner.controls.ControlFabric(ctrlsMap[i]));
		}		
		for(var i=0; i<ctrlsArr.length; i++){
			if (!ctrlsArr[i].isLookupWizard){
				continue;
			}
			if (ctrlsArr[i].parentFieldName && ctrlsArr[i].skipDependencies !== true){
				var parentCtrl = Runner.controls.ControlManager.getAt(this.tName, row.id, ctrlsArr[i].parentFieldName);
				ctrlsArr[i].setParentCtrl(parentCtrl); 
				if (parentCtrl && parentCtrl.isLookupWizard){
					parentCtrl.addDependentCtrls([ctrlsArr[i]]);	
				}				
			}			
		}
		this.fireEvent('createControls', row, ctrlsArr);
	},
	
	getSaveButt: function(row, hideSaveButt){
		
		var link = document.createElement('A');
		$(link).attr('title', Runner.lang.constants.TEXT_SAVE).addClass('saveEditing').attr('href', '').attr('id', 'save'+this.id+'_'+row.id);
		if (hideSaveButt === true){
			$(link).css('display', 'none');
		}
		var imgButt = document.createElement('IMG');
		$(imgButt).attr('src', "images/ok.gif").attr('border', '0').appendTo(link);
		
		$(imgButt).bind("click", {inlineObj: this, row: row}, function(e){
			Runner.Event.prototype.stopEvent(e);
			var row = e.data.row, 
				inlineObj = e.data.inlineObj;
				
			inlineObj.submit(row);
			inlineObj.toggleMassRecButt();
		});
		
		return $(link);
	},
	
	getCancelButt: function(row, hideRevertButt){
		var link = document.createElement('A');
		$(link).attr('title', Runner.lang.constants.TEXT_CANCEL).addClass('revertEditing').attr('href', '').attr('id', 'revert'+this.id+'_'+row.id);
		if (hideRevertButt === true){
			$(link).css('display', 'none');
		}
		var imgButt = document.createElement('IMG');
		$(imgButt).attr('src', "images/cancel.gif").attr('border', '0').appendTo(link).
			bind("click", {inlineObj: this, row: row}, function(e){
				Runner.Event.prototype.stopEvent(e);
				var inlineObj = e.data.inlineObj;
				inlineObj.cancelButtonHn(e.data.row);				
				inlineObj.toggleMassRecButt();
			}
		);
		
		return $(link);
	},		
	
	revertRow: function(row){
		// clear controls		
		Runner.controls.ControlManager.unregister(this.tName, row.id);
		this.fireEvent('revertRow', row);
	},
	
	removeRowData: function(row, ind){
		if (row.basicForm && row.basicForm.destructor){
			row.basicForm.destructor();
		}
		row.row.remove();
		ind = ind || this.getRowInd(row);
		return this.rows.splice(ind, 1);
	},
	
	getRowInd: function(row){
		for(var i=0; i<this.rows.length; i++){
			if (this.rows[i].id === row.id){
				return i;
			}
		}			
		return -1;
	},
	
	getRowById: function(rowId){
		for(var i=0; i<this.rows.length; i++){
			if (this.rows[i].id === rowId){
				return this.rows[i]; 
			}
		}			
		return false;
	},
	
	makeError: function(msg, row){
		if (!row.errorDiv){
			var span = $('#edit'+row.id+'_'+Runner.goodFieldName(this.fNames[0]));		
			span.append("<div class=error><a href=# id=\"error_" + row.id + "\" style=\"white-space:nowrap;\">"+Runner.lang.constants.TEXT_INLINE_ERROR+" >></a></div>");	
			row.errorDiv = span.find("div");
			if (!this.errCont){
				$(document.body).append("<div class=\"inline_error error\"></div>");
				this.errCont = $("div.inline_error").hide();
			}
			var errorCont = this.errCont;
			$("#error_"+row.id).bind("mouseover", function(e){
				errorCont.bind("mouseover", function(e){
					this.show();
				});
				var coors = findPos(this);
				coors[0] += coors[2];
				coors[1] += coors[3];
				
				errorCont.css("top",coors[1] + "px").show().css("left",coors[0] + "px").css("z-index",100);
			});
			$("#error_"+row.id).bind("mouseout", function(e){
				errorCont.hide();
			});
		}
		errorCont.html(msg);
	},
	
	
	
	afterSubmit: function(row, newData){
		this.fireEvent("beforeProcessNewRow", row, newData.vals, newData.fields, newData.keys);
		// add new data from server to row object
		row.data = Runner.apply({}, newData.vals);
		row.keys = newData.keys;			
		row.row.attr('id', this.rowPref+row.id);
		// delete controls
		Runner.controls.ControlManager.unregister(this.tName, row.id);		
		// proccess checkbox
		if (row.checkBox.length && !newData.noKeys){
			var checkBoxVal = "";
			for(var key in newData.keys){
				checkBoxVal += newData.keys[key];				
			}
			row.checkBox.val(checkBoxVal).show();
			row.checkBox[0].checked = false;
		}
		// change row to simple grid row with no editBoxes		
		this.setValuesIntoSpans(row);
		if (newData.noKeys !== true){
			this.getInlineButtBlock(row);	
		}else{
			this.clearInlineButtBlock(row);			
		}
			
		// set submitted attr
		row.submitted = true;
		row.isAdd = false;
		this.fireEvent('afterSubmit', newData.vals, newData.fields, newData.keys, row.id, newData);
		if (row.basicForm){
			row.basicForm.destructor();
			row.basicForm = null;	
		}
		
		if (this.rows.length == 1){
			$("span[@name=notfound_message"+this.id+"]").remove();
		}
		
		// fire rowsEdited event if all rows submited
		var allVals = [],
			allKeys = [],
			allRowIds = [];
		for(var i=0; i<this.rows.length; i++){
			allVals.push(this.rows[i].data);
			allKeys.push(this.rows[i].keys);
			allRowIds.push(this.rows[i].id);
			if (this.rows[i].submitted === false){
				return;		
			}
		}		
		this.fireEvent('rowsEdited', allVals, newData.fields, allKeys, allRowIds);
	},
	
	
	setValuesIntoSpans: function(row){
		this.fireEvent("beforeSetVals", row, this.fNames, row.data);
		for(var i=0; i<this.fNames.length; i++){
			if (typeof row.data[this.fNames[i]] == 'undefined'){
				row.data[this.fNames[i]] = "";
			}
			$('#edit'+row.id+'_'+Runner.goodFieldName(this.fNames[i])).html(row.data[this.fNames[i]]);
			if ($(row.data[this.fNames[i]]).attr('src')){
				$('#edit'+row.id+'_'+Runner.goodFieldName(this.fNames[i])).find('img').attr('src', ($(row.data[this.fNames[i]]).attr('src') + "&rndVal=" + Math.random()));				
			}			
		}
		return row.data;
	},
	
	getValuesFromSpan: function(row){
		row.data = {};
		for(var i=0; i<this.fNames.length; i++){
			row.data[this.fNames[i]] = $('#edit'+row.id+'_'+Runner.goodFieldName(this.fNames[i])).html();
		}
		return row.data;
	},
	
	getCopyButt: function(row){
		if (!this.isUseCopy){
			this.getCopyButt = function(row){
				return "";
			}			
			return "";
		}
		
		if (this.isUseIcons){			
			this.getCopyButt = function(row){
				/*var copyUrl = this.shortTName+'_'+Runner.pages.constants.PAGE_ADD+'.php?', 
					i=0;
				for(var key in row.keys){
					i++;
					copyUrl += "copyid"+i+'='+row.keys[key];
				}*/
				var copyUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_ADD, row.keys, 'copyid');
				var link = document.createElement('A');
				$(link).attr('title', Runner.lang.constants.TEXT_COPY).addClass('tablelinks').attr('href', copyUrl).attr('id', 'copyLink'+row.id);
				
				var imgButt = document.createElement('IMG');
				$(imgButt).attr('src', "images/icon_copy_new.gif").attr('border', '0').attr('align', 'middle').attr('alt', Runner.lang.constants.TEXT_COPY).addClass('listIcon').appendTo(link);
				
				if (Runner.pages.PageSettings.getTableData(this.tName, "showAddInPopup", false)){
					
					var editObj = this;
					$(link).bind("click", function(e){
						var eventParams = {
							tName: editObj.tName, 
							pageType: Runner.pages.constants.PAGE_ADD, 
							pageId: -1,
							destroyOnClose: true,
							parentPage: editObj,
							modal: true, 
							keys: row.keys,
							keyPref: "copyid",
							baseParams: {
								parId: editObj.id,
								table: editObj.tName,
								editType: Runner.pages.constants.ADD_POPUP
							},				
							afterSave: {
						        fn: function(respObj, formObj, fieldControls, page){
						        	if (respObj.success){
						        		this.addRowToGrid(respObj);	
						        	}else{
										$('#message_block'+page.id+' div.message').html(respObj.message);
						        		return false;
						        	}
								},
						        scope: editObj
						    }
						};
						Runner.Event.prototype.stopEvent(e);
						Runner.pages.PageManager.openPage(eventParams);
					});
				}
				return $(link);
			}
		}else{
			this.getCopyButt = function(row){
				/*var copyUrl = this.shortTName+'_'+Runner.pages.constants.PAGE_VIEW+'.php?', 
					i=0;
				for(var key in row.keys){
					i++;
					copyUrl += "copyid"+i+'='+row.keys[key];
				}*/
				var copyUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_ADD, row.keys, 'copyid');
				var link = document.createElement('A');		
				
				$(link).html(Runner.lang.constants.TEXT_COPY).attr('title', Runner.lang.constants.TEXT_COPY).addClass('tablelinks').attr('href', copyUrl).attr('id', 'copyLink'+row.id);
				
				if (Runner.pages.PageSettings.getTableData(this.tName, "showAddInPopup", false)){
						
					var editObj = this;
					$(link).bind("click", eventParams, function(e){
						var eventParams = {
							tName: editObj.tName, 
							pageType: Runner.pages.constants.PAGE_ADD, 
							pageId: -1,
							destroyOnClose: true,
							parentPage: editObj,
							modal: true, 
							keys: row.keys,
							keyPref: "copyid",
							baseParams: {
								parId: editObj.id,
								table: editObj.tName,
								editType: Runner.pages.constants.ADD_POPUP
							},				
							afterSave: {
						        fn: function(respObj, formObj, fieldControls, page){
						        	if (respObj.success){
						        		this.addRowToGrid(respObj);	
						        	}else{
										$('#message_block'+page.id+' div.message').html(respObj.message);
						        		return false;
						        	}
								},
						        scope: editObj
						    }
						};
						Runner.Event.prototype.stopEvent(e);
						Runner.pages.PageManager.openPage(eventParams);
					});
				}
				
				return $(link);
			}
		}
		return this.getCopyButt(row);
	},
	
	getViewButt: function(row){
		if (!this.isUseView){
			this.getViewButt = function(row){
				return "";	
			};			
			return "";
		};
		
		if (this.isUseIcons){			
			this.getViewButt = function(row){
				/*var viewUrl = this.shortTName+'_'+Runner.pages.constants.PAGE_VIEW+'.php?', 
					i=0;
				for(var key in row.keys){
					i++;
					viewUrl += "editid"+i+'='+row.keys[key];
				}*/
				var viewUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_VIEW, row.keys, 'editid');
				var link = document.createElement('A');
				$(link).attr('title', Runner.lang.constants.TEXT_VIEW).addClass('tablelinks').attr('href', viewUrl).attr('id', 'viewLink'+row.id);
				
				var imgButt = document.createElement('IMG');
				$(imgButt).attr('src', "images/icon_view_new.gif").attr('border', '0').attr('align', 'middle').attr('alt', 'Edit').addClass('listIcon').appendTo(link);
				
				if (this.showViewInPopup){
					var inlineObj = this;
					$(link).bind("click", function(e){
						Runner.Event.prototype.stopEvent(e);
						var eventParams = {
							tName: inlineObj.tName,  
							pageType: Runner.pages.constants.PAGE_VIEW, 
							pageId: -1,
							destroyOnClose: true,
							keys: row.keys,
							modal: true,
							baseParams: {
								parId: inlineObj.id,
								table: escape(inlineObj.tName)
							}
						},
						i = 0;
						
						for(var key in row.keys){
							i++;
							eventParams.baseParams["editid"+i] = row.keys[key];
						}
						
						Runner.pages.PageManager.openPage(eventParams);
					});
				}
				
				return $(link);
			};
		}else{
			this.getViewButt = function(row){
				var viewUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_VIEW, row.keys, 'editid');
				var link = document.createElement('A');		
				
				$(link).html(Runner.lang.constants.TEXT_VIEW).attr('title', Runner.lang.constants.TEXT_VIEW).addClass('tablelinks').attr('href', viewUrl).attr('id', 'viewLink'+row.id);
				
				if (this.showViewInPopup){
					var inlineObj = this;
					$(link).bind("click", function(e){
						Runner.Event.prototype.stopEvent(e);
						var eventParams = {
							tName: inlineObj.tName, 
							pageType: Runner.pages.constants.PAGE_VIEW, 
							pageId: -1,
							destroyOnClose: true,
							keys: row.keys,
							modal: true,
							baseParams: {
								parId: inlineObj.id,
								table: escape(inlineObj.tName)
							}
						},
						i = 0;
						
						for(var key in row.keys){
							i++;
							eventParams.baseParams["editid"+i] = row.keys[key];
						}						
						
						Runner.pages.PageManager.openPage(eventParams);
					});
				}
				
				return $(link);
			};
		};
		return this.getViewButt(row);
	},
	
	getEditButt: function(row){
		
		if (!this.isUseEdit){
			this.getEditButt = function(row){
				return "";	
			}			
			return "";
		}
		
		if (this.isUseIcons){
			this.getEditButt = function(row){
				var editUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_EDIT, row.keys, 'editid');			
				var link = document.createElement('A');
				$(link).attr('title', Runner.lang.constants.TEXT_EDIT).addClass('tablelinks').attr('href', editUrl).attr('id', 'editLink'+row.id);
				
				var imgButt = document.createElement('IMG');
				$(imgButt).attr('src', "images/icon_edit_new.gif").attr('border', '0').attr('align', 'middle').addClass("listIcon").appendTo(link);
				
				if (this.showEditInPopup){
					var inlineObj = this;
					$(link).bind("click", function(e){
						Runner.Event.prototype.stopEvent(e);
						var eventParams = {
							tName: inlineObj.tName,  
							pageType: Runner.pages.constants.PAGE_EDIT, 
							pageId: -1,
							destroyOnClose: true,
							keys: row.keys,
							modal: true,
							baseParams: {
								parId: inlineObj.id,
								table: escape(inlineObj.tName),
								editType: Runner.pages.constants.EDIT_POPUP
							},				
							afterSave: {
						        fn: function(respObj, formObj, fieldControls, page){
						        	if (respObj.success){
						        		this.afterSubmit(row, respObj);
						        	}else{
										$('#message_block'+page.id+' div.message').html(respObj.message);
										$('div.bd').animate({scrollTop:0});
						        		return false;
						        	}
								},
						        scope: inlineObj
						    }
							
						},
						i = 0;
						
						for(var key in row.keys){
							i++;
							eventParams.baseParams["editid"+i] = row.keys[key];
						}							
						
						Runner.pages.PageManager.openPage(eventParams);
					});
				}
				
				return $(link);
			}
		}else{
			this.getEditButt = function(row){
				/*var editUrl = this.shortTName+'_'+Runner.pages.constants.PAGE_EDIT+'.php?',
					i=0;
				for(var key in row.keys){
					i++
					editUrl += "editid"+i+'='+row.keys[key];
				}*/
				var editUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_EDIT, row.keys, 'editid');
				var link = document.createElement('A');		
				
				$(link).html(Runner.lang.constants.TEXT_EDIT).attr('title', Runner.lang.constants.TEXT_EDIT).addClass('tablelinks').attr('href', editUrl).attr('id', 'editLink'+row.id);
				
				if (this.showEditInPopup){
					var inlineObj = this;
					$(link).bind("click", function(e){
						Runner.Event.prototype.stopEvent(e);
						var eventParams = {
							tName: inlineObj.tName,  
							pageType: Runner.pages.constants.PAGE_EDIT, 
							pageId: -1,
							destroyOnClose: true,
							keys: row.keys,
							modal: true,
							baseParams: {
								parId: inlineObj.id,
								table: escape(inlineObj.tName),
								editType: Runner.pages.constants.EDIT_POPUP
							},				
							afterSave: {
						        fn: function(respObj, formObj, fieldControls, page){
						        	this.afterSubmit(row, respObj);
								},
						        scope: inlineObj
						    }
						},
						i = 0;
						
						for(var key in row.keys){
							i++;
							eventParams.baseParams["editid"+i] = row.keys[key];
						}						
						
						Runner.pages.PageManager.openPage(eventParams);
					});
				}
				
				return $(link);
			}
		}
		return this.getEditButt(row);
	},
	
	getInlineEditButt: function(row){
		if (!this.isUseInlineEdit){
			this.getInlineEditButt = function(row){
				return "";	
			}			
			return "";
		};
		
		if (this.isUseIcons){
			this.getInlineEditButt = function(row){
				var link = document.createElement('A');
		
				$(link).attr('title', Runner.lang.constants.TEXT_INLINE_EDIT).addClass('tablelinks').attr('href', '').attr('id', 'ieditLink'+this.id+'_'+row.id);
				
				var imgButt = document.createElement('IMG');
				
				$(imgButt).attr('src', "images/icon_inline_edit_new.gif").attr('border', '0').attr('align', 'middle').addClass("listIcon").appendTo(link);
				
				$(imgButt).bind("click", {inlineObj: this, row: row}, function(e){
					Runner.Event.prototype.stopEvent(e);
					var row = e.data.row, inlineObj = e.data.inlineObj;
					if (inlineObj.inlineEditObj){
						inlineObj.inlineEditObj.inlineEdit(row);
					}else{
						inlineObj.inlineEdit(row);
					}
				});
				
				return $(link);
			};
		}else{
			this.getInlineEditButt = function(row){
				/*var editUrl = this.shortTName+'_'+Runner.pages.constants.PAGE_EDIT+'.php?',
					i=0;
				for(var key in row.keys){
					i++;
					editUrl += "editid"+i+'='+row.keys[key];
				}*/
				var editUrl = Runner.pages.getUrl(this.tName, Runner.pages.constants.PAGE_EDIT, row.keys, 'editid');
				var link = document.createElement('A');		
				
				$(link).html(Runner.lang.constants.TEXT_INLINE_EDIT).attr('title', Runner.lang.constants.TEXT_INLINE_EDIT).addClass('tablelinks').attr('href', editUrl).attr('id', 'ieditLink'+this.id+'_'+row.id).
					bind("click", {inlineObj: this, row: row}, function(e){
						Runner.Event.prototype.stopEvent(e);
						var row = e.data.row, inlineObj = e.data.inlineObj;
						if (inlineObj.inlineEditObj){
							inlineObj.inlineEditObj.inlineEdit(row);
						}else{
							inlineObj.inlineEdit(row);
						}
					}
				);
				
				return $(link);
			};
		}				
		return this.getInlineEditButt(row);
	},
	
	getEditBlock: function(row, hideRevertButt, hideSaveButt){
		if (row.row.find("td[@iEditCont=all]").length){
			this.getEditBlock = function(row, hideRevertButt, hideSaveButt){
				var buttSpan = document.createElement('SPAN');
				$(buttSpan).attr('id', 'ieditLink'+this.id+'_'+row.id).append(this.getSaveButt(row, hideSaveButt)).append('&nbsp;&nbsp;').append(this.getCancelButt(row, hideRevertButt));
				row.row.find("td[@iEditCont=all]").empty().append(buttSpan);
				return $(buttSpan);
			};
		}else if(row.row.find("td[@iEditCont=iEdit]").length){
			// create td's for each link
			this.getEditBlock = function(row, hideRevertButt, hideSaveButt){
				row.row.find("td[@iEditCont=iEdit]").empty().
					append(this.getSaveButt(row, hideSaveButt)).
						append('&nbsp;&nbsp;').
							append(this.getCancelButt(row, hideRevertButt));
			};
		}else{
			// inline add in lookup wizard, add to td with id
			this.getEditBlock = function(row, hideRevertButt, hideSaveButt){
				var spans = row.row.find("span"),
					added = false;
				// find span with &nbsp;
				for(var i=0; i<spans.length;i++){
					if ($(spans[i]).html() == "&nbsp;"){
						$(spans[i]).empty().
							append(this.getSaveButt(row, hideSaveButt)).
								append('&nbsp;&nbsp;').
									append(this.getCancelButt(row, hideRevertButt));
						added = true;			
						break;
					}
				}
				if (!added){
					row.row.find("td:first").find('span:first').
						prepend(this.getCancelButt(row, hideRevertButt)).						
							prepend('&nbsp;&nbsp;').
								prepend(this.getSaveButt(row, hideSaveButt));
				}
				
			};
		}
		return this.getEditBlock(row, hideRevertButt, hideSaveButt);
	},
	
	getInlineButtBlock: function(row){
		if (row.row.find("td[@iEditCont=all]").length){
			this.getInlineButtBlock = function(row){
				row.row.find("td[@iEditCont=all]").empty()
					.append(this.getEditButt(row)).append('&nbsp;')					
						.append(this.getInlineEditButt(row)).append('&nbsp;')
							.append(this.getCopyButt(row)).append('&nbsp;')
								.append(this.getViewButt(row));
			}
		}else if(row.row.find("td[@iEditCont=iEdit]")){
			// create td's for each link
			this.getInlineButtBlock = function(row){
								
				row.row.find("td[@iEditCont=edit]").empty().append(this.getEditButt(row));
				row.row.find("td[@iEditCont=iEdit]").empty().append(this.getInlineEditButt(row));
				row.row.find("td[@iEditCont=view]").empty().append(this.getCopyButt(row));
				row.row.find("td[@iEditCont=copy]").empty().append(this.getViewButt(row));
			}
		}else{
			this.getInlineButtBlock = function(row){
				row.row.find("td:first")
					.append(this.getEditButt(row)).append('&nbsp;')					
						.append(this.getInlineEditButt(row)).append('&nbsp;')
							.append(this.getCopyButt(row)).append('&nbsp;')
								.append(this.getViewButt(row));
			}
		}
		this.getInlineButtBlock(row);
	},
	
	clearInlineButtBlock: function(row){
		if (row.row.find("td[@iEditCont=all]").length){
			this.clearInlineButtBlock = function(row){
				row.row.find("td[@iEditCont=all]").empty();
			}
		}else if(row.row.find("td[@iEditCont=iEdit]")){
			// create td's for each link
			this.clearInlineButtBlock = function(row){
								
				row.row.find("td[@iEditCont=edit]").empty();
				row.row.find("td[@iEditCont=iEdit]").empty();
				row.row.find("td[@iEditCont=view]").empty();
				row.row.find("td[@iEditCont=copy]").empty();
			}
		}else{
			this.clearInlineButtBlock = Runner.emptyFn;
			
		}
		this.clearInlineButtBlock(row);
	},
		
	toggleMassRecButt: function(){
		if (!this.isRowsEditing()){
			this.saveAllButt.hide().parent().hide();
			this.cancelAllButt.hide().parent().hide();
			this.massRecButtEditMode = false;
		}else{
			this.saveAllButt.show().parent().show();
			this.cancelAllButt.show().parent().show();
			this.massRecButtEditMode = true;
		}
	},
	
	isRowsEditing: function(){
		for(var i=0; i<this.rows.length; i++){
			if (!this.rows[i].submitted){
				return true;
			}
		}
		return false;
	},
	
	validate: function(){
		var ctrls, 
			vRes = true;
		for(var i=0; i<this.rows.length; i++){
			ctrls = Runner.controls.ControlManager.getAt(this.tName, this.rows[i].id);
			for(var j=0; j<ctrls.length; j++){
				if (!ctrls[j].validate().result){
					vRes = false;
				}
			}
		}
		return vRes;
	},
	
	addRowToGrid: function(data){		
		var newAddRow = this.prepareRow(data.vals, true);
		this.changeRowButtons(newAddRow);
		this.getEditBlock(newAddRow);
		this.afterSubmit(newAddRow, data);
	},
	
	prepareRow: function(vals, submitted){	
		// make sure that table is shown
		if (this.rows.length === 0){
			$("#grid_block"+this.id).find('table').show();
			$("#gridHeaderTr"+this.id, "#grid_block"+this.id).show();
			this.editAllButt.parent().show();
			this.saveAllButt.parent().show();
			this.cancelAllButt.parent().show();
			$("#print_selected"+this.id).parent().show();
			$("#export_selected"+this.id).parent().show();
			$("#delete_selected"+this.id).parent().show();
		}
		
		this.prepareRow = function(vals, submitted){
			var newAddRow = {
				row: this.addArea.clone(true), 
				id: Runner.genId(), 
				//rowInd: this.rows.length, 
				data: vals, 
				submitted: false || submitted, 
				isAdd: true
			};
			
			newAddRow.checkBox = newAddRow.row.find("td[@iEditCont=checkBox]").find("input[@type=checkbox]");		
			this.rows.push(newAddRow);
			
			newAddRow.row.attr("id", "addArea"+newAddRow.id);
			newAddRow.row.insertAfter(this.addArea);		
			newAddRow.row.show();	
			
			setHoverForTR(newAddRow.row,this.id, Runner.pages.PageSettings.getTableData(this.tName, "isUseHighlite", false), this.isUseIcons, Runner.pages.PageSettings.getTableData(this.tName, "isUseResize", false));			
			
			return newAddRow;
		};
		
		return this.prepareRow(vals, submitted);		
	}	
});





Runner.util.inlineEditing.InlineAdd = Runner.extend(Runner.util.inlineEditing.InlineEditor, {	
	
	
	
	constructor: function(cfg){
		this.pageType = 'add';
		Runner.util.inlineEditing.InlineAdd.superclass.constructor.call(this, cfg);		
		this.pageType = Runner.pages.constants.PAGE_ADD;
		this.ajaxRequestUrl = this.shortTName + "_" + Runner.pages.constants.PAGE_ADD + ".php";	
		
		this.submitUrl = this.shortTName + '_' + Runner.pages.constants.PAGE_ADD + ".php";
		
		this.addEvents("beforeSetVals");
	},
	
	init: function(){
		Runner.util.inlineEditing.InlineAdd.superclass.init.call(this);	
		this.initAddButton();
	},
	
	initAddButton: function(){
		$("#inlineAdd"+this.id).bind("click", {inlineAddObj: this}, function(e){
			e.data.inlineAddObj.inlineAdd();
		});
	},	
	
	cancelButtonHn: function(row){
		this.removeRowData(row);
	},
	
	revertRow: function(row){
		Runner.util.inlineEditing.InlineAdd.superclass.revertRow.call(this, row);		
		// clear row data from memory
		this.removeRowData(row);
	},
	
	saveAll: function(){
		for(var i=0; i<this.rows.length; i++){
			if (!this.rows[i].submitted && this.rows[i].isAdd){
				this.submit(this.rows[i]);
			}
		}		
	},
		
	inlineAdd: function(hideRevertButt, hideSaveButt){
		if (typeof hideRevertButt == "undefined"){
			hideRevertButt = this.hideRevertButt;
		}
		if (typeof hideSaveButt == "undefined"){
			hideSaveButt = this.hideSaveButt;
		}
		var newAddRow = this.prepareRow({}, false);
		this.toggleMassRecButt();
		this.changeRowButtons(newAddRow);
		this.getControls(newAddRow, hideRevertButt, hideSaveButt);
	}
});






Runner.util.inlineEditing.InlineEdit = Runner.extend(Runner.util.inlineEditing.InlineEditor, {	
	
	constructor: function(cfg){
		Runner.util.inlineEditing.InlineEdit.superclass.constructor.call(this, cfg);	
		this.ajaxRequestUrl = this.shortTName + "_" + Runner.pages.constants.PAGE_EDIT + ".php";
		this.pageType = Runner.pages.constants.PAGE_EDIT;
		this.submitUrl = this.shortTName + '_' + Runner.pages.constants.PAGE_EDIT + ".php"; 		
		this.addEvents("beforeEditRow");
	},
	
	init: function(){		
		Runner.util.inlineEditing.InlineEdit.superclass.init.call(this);		
		this.initRows();
		this.initInlineRowEditors();
	},
	
	initRows: function(){
		for(var i=0; i<this.rows.length; i++){
			this.rows[i].submitted = true;
			this.rows[i].row = $("#"+this.rowPref+this.rows[i].id);
			this.rows[i].data = {};		
			this.rows[i].checkBox = this.rows[i].row.find("td[@iEditCont=checkBox]").find("input[@type=checkbox]")
		}
	},
	
	initInlineRowEditors: function(){
		for(var i=0; i<this.rows.length; i++){
			this.getInlineButtBlock(this.rows[i]);
		}
	},	
	
	initButtons: function(){
		Runner.util.inlineEditing.InlineEdit.superclass.initButtons.call(this);
		this.initEditAll();		
	},
	
	initEditAll: function(id){
		if(typeof id == "undefined"){
			id = this.id;
		}
		
		var inlineObj = this;
		this.editAllButt.unbind("click").bind("click", function(e){
			var selBoxes = $('input[@type=checkbox][@checked][@id^=check'+id+'_]'),							
				row, 
				checkBox;
			for(var i=0; i<selBoxes.length; i++){
				for(var j=0; j<inlineObj.rows.length; j++){
					checkBox = inlineObj.rows[j].row.find('input[@type=checkbox]');
					if ($(checkBox[0]).attr('id') == $(selBoxes[i]).attr('id')){
						row = inlineObj.rows[j];
						inlineObj.inlineEdit(row);
					}
				}
			}
			inlineObj.toggleMassRecButt();
		});
	},
	
	reInit: function(id, gridRows){
		Runner.util.inlineEditing.InlineEdit.superclass.reInit.call(this, id, gridRows);
		this.initEditAll(id);		
		this.initRows();	
		this.cancelAll();
	},
	
	inlineEdit: function(row, hideRevertButt, hideSaveButt){
		if (typeof hideRevertButt == "undefined"){
			hideRevertButt = this.hideRevertButt;
		}
		if (typeof hideSaveButt == "undefined"){
			hideSaveButt = this.hideSaveButt;
		}
		this.fireEvent("beforeEditRow", row);
		row.submitted = false;
		this.toggleMassRecButt();
		this.getValuesFromSpan(row);
		this.changeRowButtons(row);	
		this.getControls(row, hideRevertButt, hideSaveButt);		
	},
	
	changeRowButtons: function(row){
		Runner.util.inlineEditing.InlineEdit.superclass.changeRowButtons.call(this, row);
		row.row.find("td[@iEditCont=checkBox]").find("input[@type=checkbox]").hide();
	},
		
	revertRow: function(row){	
		row.submitted = true;
		row.errorDiv = false;
		if (row.checkBox.length){
			row.checkBox[0].checked = false;
		}
		// clear controls
		Runner.util.inlineEditing.InlineEdit.superclass.revertRow.call(this, row);
		// change row buttons
		this.getInlineButtBlock(row);
		// set row data into spans
		this.setValuesIntoSpans(row);
		row.checkBox.show();
	},	
	
	
	initForm: function(row){
		Runner.util.inlineEditing.InlineEdit.superclass.initForm.call(this, row);
		// change base params
		row.basicForm.baseParams = {a: 'edited', editType: 'inline', id: row.id};
		// add keys
		var i=0;
		for(var key in row.keys){
			i++;
			row.basicForm.baseParams["editid"+i] = row.keys[key];
		}
	},
	
	cancelButtonHn: function(row){	
		this.revertRow(row);		
	},
	
	toggleMassRecButt: function(){
		Runner.util.inlineEditing.InlineEdit.superclass.toggleMassRecButt.call(this);
		if (this.massRecButtEditMode){
			this.editAllButt.hide().parent().hide();
		}else{
			this.editAllButt.show().parent().show();
		}
	},
	
	saveAll: function(){
		for(var i=0; i<this.rows.length; i++){
			if (!this.rows[i].submitted && !this.rows[i].isAdd){
				this.submit(this.rows[i]);
			}
		}		
	}
	
});
