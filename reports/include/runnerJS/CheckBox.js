
/**
 * Multiple select control class
 * @requires Runner.controls.LookupWizard
 */
Runner.controls.CheckBoxLookup = Runner.extend(Runner.controls.LookupWizard, {
	/**
	 * type hidd element id
	 * @type String
	 */
	typeElemId: "",
	/**
	 * type hidd element jQuery obj
	 * @type {object}
	 */
	typeElem: null,
	/**
	 * Number of checkboxes
	 * @type Number
	 */
	checkBoxCount: 0,
	/**
	 * Array of checkbox jQuery elements
	 * @type {array}
	 */
	checkBoxesArr: null,
	/**
	 * String from which checkbox name attr starts, for getting
	 * @type String
	 */
	checkBoxNameAttr: "",
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */
	constructor: function(cfg){
		// add checkboxes elements
		this.checkBoxesArr = [];
		//call parent
		Runner.controls.CheckBoxLookup.superclass.constructor.call(this, cfg);
		// add input type
		this.inputType = "checkbox";
		// type hidd element id
		this.typeElemId = "type_"+this.goodFieldName+"_"+this.id;
		// type hidd element jQuery obj
		this.typeElem = $("#"+this.typeElemId);
		// span where situated data of checkbox
		this.dataCheckBoxId = "data_"+this.valContId;
		// add checkboxes elements, if checkbox used in simple way
		if (($("#"+this.valContId)).length){
			var checkBox=$("#"+this.valContId);
			// arr of jQuery checkboxes
			this.checkBoxesArr.push(checkBox);
			//elems for event are checkboxes
			this.elemsForEvent.push(this.checkBoxesArr[0].get(0));
		// if checkbox used as lookup
		}else{		
			var checkBox, i=0;
			while(($("#"+this.valContId+"_"+i)).length){
				checkBox=$("#"+this.valContId+"_"+i);
				// arr of jQuery checkboxes
				this.checkBoxesArr.push(checkBox);
				//elems for event are checkboxes
				this.elemsForEvent.push(this.checkBoxesArr[i].get(0));
				i++;
			}
		}
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}
		// WHICH EVENTS NEED TO ADD ?
		this.addEvent(["click"]);
		// init events
		this.init();
		//set defaultValue
		this.defaultValue = this.getValue();
		// get jQuery array of checkboxes as value element
		this.checkBoxNameAttr = 'value_'+this.goodFieldName+'_'+this.id;
		this.valueElem = $('input[@name^='+this.checkBoxNameAttr+']');
	},
	/**
	 * Sets array of values to checkboxes
	 * @method
	 * @param {array} valsArr
	 * @return {Boolean} true if success otherwise false
	 */	
	setValue: function(valsArr, triggerEvent){
		var checkCount = 0;
		//loop for all checkboxes
		for(var i=0;i<this.checkBoxesArr.length;i++){
			// set unchecked
			this.checkBoxesArr[i].get(0).checked = false;
			// loop for all vals
			for(var j=0; j<valsArr.length;j++){
				// if check box val same as val in arr to check
				if (this.checkBoxesArr[i].val() == valsArr[j]){
					this.checkBoxesArr[i].get(0).checked = true;
					checkCount++;
					break;
				}// eo if
			}// eo for
		}// eo for
		
		if(triggerEvent===true){
			//console.log(triggerEvent, 'triggerEvent on click cb called')
			this.fireEvent("click");
		}
		// check number of checked boxes
		if (checkCount == valsArr.length && checkCount<=this.checkBoxesArr.length && valsArr.length<=this.checkBoxesArr.length){
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Returns array of checked values
	 * @return {array}
	 */
	getValue: function(){
		var checkedArr = this.getCheckedBoxes(), valsArr = [];		
		// get value from each checkbox
		for(var i=0;i<checkedArr.length;i++){
			valsArr.push(checkedArr[i].val());
		}		
		return valsArr;
	},
	
	getStringValue: function(){
		return this.getValue().join(",");
	},
	/**
	 * Checks if control value is empty. Used for isRequired validation
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		// if length of values arr == 0
		if (this.getValue().length == 0){
			return true;
		}else{
			return false;
		}
	},	
	/**
	 * Sets disable attr true
	 * @method
	 */
	setDisabled: function(){
		for(var i=0;i<this.checkBoxesArr.length;i++){
			this.checkBoxesArr[i].get(0).disabled = true;
		}			
		return true;
	},
	
	/**
	 * Sets disaqble attr false
	 * @method
	 */
	setEnabled: function(){
		for(var i=0;i<this.checkBoxesArr.length;i++){
			this.checkBoxesArr[i].get(0).disabled = false;
		}			
		return true;
	},
	
	setDisabledShowCheckedBoxes: function()
	{
		for(var i=0;i<this.checkBoxesArr.length;i++)
		{
			if(this.checkBoxesArr[i].get(0).checked)
				this.checkBoxesArr[i].get(0).disabled = true;
			else{
					var dataId = $('#'+this.dataCheckBoxId+'_'+i);
					this.checkBoxesArr[i].css("display", "none");
					dataId.css("display", "none");
					dataId.next().css("display", "none");
				}	
		}			
		return true;
	},
	
	/**
	 * Returns array of cheked checkBoxes
	 * @return {array}
	 */
	getCheckedBoxes: function(){
		var chekedArr = [];
		// get value from each checkbox
		for(var i=0;i<this.checkBoxesArr.length;i++){			
			if (this.checkBoxesArr[i].get(0).checked){
				chekedArr.push(this.checkBoxesArr[i]);
			}
		}
		
		return chekedArr;
	},
	/**
	 * Returns array of jQuery object for inline submit
	 * @return {array}
	 */
	getForSubmit: function(){
		var checkedArr = this.getCheckedBoxes(), cloneArr = [];		
		// get clone of each checkbox
		for(var i=0;i<checkedArr.length;i++){
			var realCb = checkedArr[i];
			
			var cbClone = document.createElement('input');			
			$(cbClone).attr('type', 'hidden');
			$(cbClone).attr('id', realCb.attr('id'));
			$(cbClone).attr('name', realCb.attr('name'));
			$(cbClone).val(realCb.val());

			cloneArr.push(cbClone);	
		}		
		cloneArr.push(this.typeElem);
		return cloneArr;		
	},
	// =============== NEW CODE FROM DD ====================
	/**
	 * Deletes all checkBoxes from ctrl
	 * @method
	 */
	clearCheckBoxes: function(){
		this.spanContElem.find('div').children().remove().append('<br>');
		this.checkBoxesArr = [];
	},
	/**
	 * Adds option to select
	 * may be need to add options to specified index?
	 * @param {string} text
	 * @param {string} val
	 */
	addCheckBox: function(text, val){		
		var newCheckBoxId = this.valContId+"_"+this.checkBoxesArr.length;
		// create new checkbox input
		var checkBox = document.createElement("INPUT");
		$(checkBox).attr("type", "checkbox").attr("id", newCheckBoxId).attr("name", this.valContId+'[]').val(val);
		
		var label = document.createElement("B");
		$(label).attr("id", "data_"+newCheckBoxId).html(text);
		
		this.spanContElem.find('div').append(checkBox).append("&nbsp;").append(label).append("<br/>");
		
		this.checkBoxesArr.push($("#"+newCheckBoxId));
	},
	/**
	 * Add options from array.
	 * Array must have such structure:
	 * array[0] = value, array[1] = text,
	 * array[2] = value, array[3] = text,
	 * 2*i - indexes of values; 2*i+1 - indexes of text. I starts from 0   
	 * @param {array} optionsArr
	 */
	addCheckBoxArr: function(optionsArr){			
		for(var i=0; i < optionsArr.length - 1; i=i+2){ 
			this.addCheckBox(optionsArr[i+1], optionsArr[i]);
		}
	},	
	
	
	/**
	 * First loading, without ajax. Should be called directly
	 * @param {string} txt unparsed values for options
	 * @param {string} selectValue unparsed values of selected options
	 */
	preload: function(vals, selectValue){
		// clear all old options
		this.clearCheckBoxes();		
		// load options
		this.addCheckBoxArr(vals);
		// if only one values except please select, so choose it
		if (this.checkBoxesArr.length==1){
			this.setValue([this.checkBoxesArr[0].val()], false);	
		}	
		// don't need to use ajax reload call
		this.setValue([selectValue], false);		
	},	
	/**
	 * Reloading dropdown. Called by change event handler
	 * @param {string} value of master ctrl
	 */
	reload: function(masterCtrlValue){	
		//console.log(masterCtrlValue, 'masterCtrlValue');
		var ctrl = this, fName = this.fieldName, tName = this.table, rowId = this.id;
		
		//ajax params
		var ajaxParams = {
			// ctrl fieldName
			field: myEncode(fName),
			// value of master ctrl. Only first val from arr, because multiDrop cannot be master
			value: myEncode(masterCtrlValue),
			// tag name of ctrl
			type: this.valueElem[0].tagName,
			// random value for prevent caching
		    rndVal: (new Date().getTime())
		};		
		// get content		
		$.get(this.shortTableName+"_autocomplete.php", ajaxParams, function(respObj, textStatus){
			respObj = JSON.parse(respObj);
			var data = respObj.data;	
			// clear all options
			ctrl.clearCheckBoxes();			
			// parse string with new options
			var data = ctrl.parseContentToValues(txt);
			//console.log(data, 'data');
			// if bad data from server, or timeout ends..
			if(data===false){
				return false;
			}
			// load options
			ctrl.addOptionsArr(data);			
			// if only one values except please select, so choose it
			if (ctrl.checkBoxesArr.length==1){
				ctrl.setValue([ctrl.checkBoxesArr[0].val()], false);	
			}			
			// fire change event, for reload dependent ctrls
			ctrl.fireEvent("change");
			// after reload clear invalid massages			
			ctrl.clearInvalid();
		});
	},
	/**
	 * Drop custom function for blur event
	 * @param {Object} e
	 */
	"blur": Runner.emptyFn,
	
	"click": this["change"]
	
});
