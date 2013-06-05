/**
 * @class Runner.Event
 * Abstract base class that provides event functionality. 
 * Example of usage:
Employee = function(name){
    this.name = name;
    this.addEvent(["blur", "change"]);
    this.init();
 }
Runner.extend(Employee, Runner.Event);

=================================================================

Predefined, javascript events:

abort	Loading of an image is interrupted
blur	An element loses focus
change	The user changes the content of a field
click	Mouse clicks an object	1	3
dblclick	Mouse double-clicks an object	1	4
error	An error occurs when loading a document or an image	1	4
focus	An element gets focus	1	3
keydown	A keyboard key is pressed	1	3
keypress	A keyboard key is pressed or held down	1	3
keyup	A keyboard key is released	1	3
load	A page or an image is finished loading	1	3
mousedown	A mouse button is pressed	1	4
mousemove	The mouse is moved	1	3
mouseout	The mouse is moved off an element	1	4
mouseover	The mouse is moved over an element	1	3
mouseup	A mouse button is released	1	4
reset	The reset button is clicked	1	4
resize	A window or frame is resized	1	4
select	Text is selected	1	3
submit	The submit button is clicked	1	3
unload The user exits the page
 */


Runner.Event = Runner.extend(Runner.emptyFn,{
	/**
	 * Array of predefined events
	 * @type {array}
	 */
	events: null,
	/**
	 * Array of predefined listeners
	 * @type {array}
	 */
	listeners: null,
	/**
	 * Array of elements, on which listeners should be added
	 * @type {array}
	 */
	elemsForEvent: null,
	/**
	 * Array of events that are suspended for this control
	 * @type {array}
	 */
	suspendedEvents: null,
	/**
	 * @constructor
	 */
	constructor: function(){
		// recreate objects, to prevent memory mix
		this.listeners = [];
		this.elemsForEvent = [];
		this.suspendedEvents = [];
	},
	/**
	 * Init method, should be called by class contructor, for event initialization
	 * @method
	 */	
	init: function(){
		if (this.events.length == 0){
			return false;
		}
		for(var i=0;i<this.events.length;i++){
			// pass event name and event standard handler
			this.on(this.events[i], this[this.events[i]]);
		}
		
		return true;
	},
	
	suspendEvent: function(eventArr){
		for(var i=0;i<eventArr.length;i++){
			if (!this.suspendedEvents.isInArray(eventArr[i])){
				this.suspendedEvents.push(eventArr[i]);
			}
			
		}
	},
	resumeEvent: function(eventArr){
		var eventInd = -1;
		for(var i=0;i<eventArr.length;i++){
			eventInd = this.suspendedEvents.getIndexOfElem(eventArr[i]);
			if (eventInd != -1){
				this.suspendedEvents.splice(eventInd, 1);
			}
		}
	},
	
	createDelayed: function(handler, timeout){
		return function(e){
            setTimeout(function(){
                handler(e);
            }, timeout || 10);
        };
	},
	
	createBuffered: function(handler, buffer){
		var task = new Runner.util.DelayedTask(handler);
        return function(e){
            task.delay(buffer, handler, null, [e]);
        };
	},
	
	createSingle: function(handler, eventName){
		var obj = this;
		return function(e){
			handler(e);
			obj.clearEvent(eventName);
        };
	},
	/**
	 * Add events to the object. Events names should be similar to predefined
	 * javascript DOM element event names.
	 * @method
	 * @param {string} eventName
	 * @param {link} fn
	 * @param {array} options.args Optional. Array of arguments, that should be passed to event handler
	 * @param {bool} options.single Optional. Pass true to fire event only once
	 * @param {int} options.timeout Optional. Pass number of miliseconds to create delayed handler
	 * @param {int} options.buffer Optional. Pass number of miliseconds to buffer. Usefull for keypress events and validations. Not fully work at now.
	 * @param {link} scope
	 */
	on: function(eventName, fn, options, scope){
		// if no DOM elems as event targets, then stop adding event
		if (!this.elemsForEvent.length || !fn){
			//console.log("no elems");
			return false;
		}
		//add event to event array, if needed
		this.addEvent([eventName]);
		// prepare event name, for DOM scpecifications
		var onEventName = "";
		if (eventName.indexOf("on", 0) == 0){
			onEventName = eventName;
			eventName = eventName.substring(2);
		}else{
			onEventName = "on"+eventName;			
		}		
		// predefine scope and func params for creating closure
		var scope = scope ? scope : this, objScope = this, options = options ? options : {};
		// predefine additional params
		var args = options.args ? options.args : [], single = options.single ? options.single : false, timeout = options.timeout ? options.timeout : 0, buffer = options.buffer ? options.buffer : 0;	
		
		var callHandler = function(e){	
			// prevent call if event suspended 
			if (objScope.suspendedEvents.isInArray(eventName)){
				return;
			}
			fn.call(scope, e, args);			
		}
		// creating delayed handler, usefull for validations etc.
		if (timeout){
			this.createDelayed(callHandler, timeout);
		}
		// function will clear itself after called, usefull when function need to be called once
		if(single){
			this.createSingle(callHandler, eventName)
		}
		if(buffer){
			this.createBuffered(callHandler, buffer);
		}
		// add to listeners array
		var listener = this.getListener(eventName);
		/* to better event handling need to collect fn handlers into array
		 * because only last event params are saved in listener object, better way to have object with arrays 
		 */
		if (!listener){
			this.listeners.push({
				name: eventName,
				handler: fn,
				callHandler: callHandler,
				options: options,
				scope: scope,
				index: this.listeners.length
			});
		}else{
			this.listeners[listener.index].handler = fn;
			this.listeners[listener.index].callHandler = callHandler;
			this.listeners[listener.index].options = options;
			this.listeners[listener.index].scope = scope;
		}
				
		// adding listeners for all elems for event		
		for(var i=0;i<this.elemsForEvent.length;i++){				
			var el = this.elemsForEvent[i];
			$(el).bind(eventName, callHandler);
		}
		return true;
    },
	/**
	 * Add events to object, make list of predefined events, before call init method
	 * @method
	 * @param {array} eventNameArr
	 */
	addEvent: function(eventNameArr){		
		if (!this.events){
			this.events = [];
		}		
		// lazy init func
		this.addEvent = function(eventNameArr){
			for(var i=0;i<eventNameArr.length;i++){
				// check if this event already added
				if (!this.events.isInArray(eventNameArr[i])){
					this.events.push(eventNameArr[i]);		
				}	
			}	
		}
		this.addEvent(eventNameArr);	
	},
	/**
	 * Kill event handling, sets empty fn as handler
	 * @method
	 * @param {string} eventName
	 * @return {bool} true if success otherwise false
	 */
	killEvent: function(eventName){
		// search event
		var eventInd = this.events.getIndexOfElem(eventName);
		if (eventInd === -1){
			return false;
		}		
		// search for listener object
		var listener = this.getListener(eventName);				
		if (!listener){
			return false;	
		}
		// clear handlers
		for (var j = 0; j < this.elemsForEvent.length; j++) {
			var el = this.elemsForEvent[j];	
			$(el).unbind(eventName, listener.callHandler);
		}	
		// do in this way to prevent memory leaks	
		this.listeners.splice(j,1);
		// delete event handler from object
		delete this[eventName];		
		//kill event
		this.events.splice(eventInd, 1);
		// in success
		return true;
	},
	/**
	 * Clear custom event handling, sets only base class handler
	 * @method
	 * @param {string} eventName
	 * @return {bool} true if success otherwise false
	 */
	clearEvent: function(eventName){
		// search event
		var eventInd = this.events.getIndexOfElem(eventName);
		if (eventInd === -1){
			return false;
		}
		// search for listener object
		var listener = this.getListener(eventName);				
		if (!listener){
			return false;
		}
		// clear handlers
		for (var j = 0; j < this.elemsForEvent.length; j++) {
			var el = this.elemsForEvent[j];
			$(el).unbind(eventName, listener.callHandler);	
		}	
		// do in this way to prevent memory leaks, clear custom handlers
		//sets only base class handler
		this.on(eventName);
		// in success
		return true;	
	},	
	
	
	stopEvent: function(e) {
        this.stopPropagation(e);
        this.preventDefault(e);
    },


    stopPropagation: function(e) {
        e = this.getEvent(e);
        if (e && e.stopPropagation){
            e.stopPropagation();
        }else if(e){
            e.cancelBubble = true;
        }
    },


    preventDefault: function(e) {
    	e = this.getEvent(e);
        if(e && e.preventDefault){
            e.preventDefault();
        }else if(e){
            e.returnValue = false;
        }
    },
    
	getEvent: function(e) {
        return e || window.event;
    },
	
    getTarget: function(e){
    	if (e){
			return e.target || e.srcElement;
    	}
	},
	
	
	/**
	 * Fires the specified event with the passed parameters (minus the event name).
     * @param {String} eventName
	 * @return {bool} True if hadler called, otherwise false.
	 */
	fireEvent : function(eventName){
		var eventIndex = this.isDefinedEvent(eventName);
		if (eventIndex === -1){
			return false;
		}
		
		var listener = this.getListener(eventName);
		if (listener === false){
			return false;
		}
		// better way to send normalized event object then null
		listener.callHandler.apply(this, [null].concat(Array.prototype.slice.call(arguments, 1)));
		// call base handler in such way, because we save only last handler, better to save all handlers, that we should do in next time
		if (this[eventName]){
			this[eventName].apply(this, [null].concat(Array.prototype.slice.call(arguments, 1)));
		}
		return true;
    
    },
	/**
	 * Checks if event defined
	 * @param {string} eventName
	 * @return {mixed} false if not found otherwise arrray index
	 */
	isDefinedEvent: function(eventName){	
		return this.events.getIndexOfElem(eventName);
	},
	
	getListener: function(eventName){		
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i].name == eventName) {					
				return this.listeners[i];
			}
		}
		return false;
	}
});



/**
 * Global validtion object, that used to cheked controls values
 * @type {object}
 */
validation = {	
	/**
	 * Status of validator function. 
	 * @type {object} 
	 */
	validatorConsts:{
		predefined: 1,
		user: 2,
		notFound: 3
	},
	/**
	 * Array of names of user validation functions
	 */
	userValidators: [],
	/**
	 * Array of names of predefined validators 
	 * @type {array}
	 */
	predefinedValidatorsArr: ['isrequired' ,'isnumeric' ,'ispassword' ,'isemail' ,'ismoney', 'iszipcode', 'isphonenumber', 'isstate', 'isssn', 'iscc','istime', 'isdate', 'regexp'],
	/**
	 * Main function that provides object validation
	 * @param {array} validArr
	 * @param {object} control
	 * @return {object}
	 */
	validate: function(validArr, control){
		// total result obj
		var validationRes = false, validatorStatus, result = {result: true, messageArr: []};		
		// loop for all validation on obj
		for(var i=0;i<validArr.length;i++){	
			// to prevent check for undefined values, that mistically appears in IE!
			if (!validArr[i]){
				continue;
			}
			// get status of validator
			validatorStatus = this.getValidatorStatus(validArr[i]); 
			// custom user validation
			if(validatorStatus == this.validatorConsts.user){
				validationRes = window[control.validationArr[i]](control.getValue());
			// validation method in object
			}else if(validatorStatus == this.validatorConsts.predefined){				
				// for IsRequired use isEmpty method
				if(validArr[i] == "IsRequired"){
					// if field not passed IsRequired validation, we need to add text
					if (control.isEmpty()){
						validationRes = TEXT_INLINE_FIELD_REQUIRED;
					}else{
						validationRes = true;
					}
				}else{
					// pass regExp object as second param, for regExp method
					validationRes = this[validArr[i]](control.getValue(), control.regExp);
				}
			}else{
				//alert('validation function not found');
				//console.log('validation function not found');
				validationRes = true;
			}
			// set to final result object
			result = this.setResult(validationRes, result);
		}
		// return result
		return result;
	},
	/**
	 * Check validator function status.
	 * @param {string} validatorName
	 * @return {string} property from validatorConsts object
	 */
	getValidatorStatus: function(validatorName){
		if(this.predefinedValidatorsArr.isInArray(validatorName)){
		//if(IsInArray(this.predefinedValidatorsArr, validatorName, false)){
			return this.validatorConsts.predefined;
		}else if(window[validatorName] && ((typeof(window[validatorName])=='function')||(Runner.isIE&&typeof(window[validatorName])=='object'))){
			return this.validatorConsts.user;
		}else{
			return this.validatorConsts.notFound;
		}
	},
	/**
	 * Set result to final result object
	 * @param {mixed} res result from any validation function true, or error text
	 * @param {object} obj final result object
	 * @return {object}
	 */
	setResult: function(res, obj){
		var len = obj.messageArr.length;		
		if(res!==true){
			// add message and set false to final result
			obj.result = false;
			// if res is array of messages, add each message to array
			if (typeof(res)=='object'){
				for(var i=0;i<res.length;i++){
					obj.messageArr.push(res[i]);
				}
			// add to message array if res is string
			}else{
				obj.messageArr.push(res);
			}				
		}
		return obj;
	},
	/**
	 * Handler loading custom validation function from file.
	 * @param {object} ctrl
	 */
	registerCustomValidation: function(ctrl){
		var validatorStatus;
		// loop for all validations
		for(var i=0;i<ctrl.validationArr.length;i++){		
			// to prevent check undefined vals
			if (!ctrl.validationArr[i]){
				continue;
			}
			// get validator status
			validatorStatus = this.getValidatorStatus(ctrl.validationArr[i]);			
			// if user vvalidator, and defined as function			
			if(validatorStatus == this.validatorConsts.user || validatorStatus == this.validatorConsts.notFound){			
				// check if was added
				var isAdded = false;
				for(var j=0;j<this.userValidators.length;j++){
					if(this.userValidators[j]==ctrl.validationArr[i]){
						isAdded=true;
						break;
					}
				}
				// add if not
				if(!isAdded){					
					// load js from file
					Runner.loadJS('include/validate/'+ctrl.validationArr[i]+'.js');
					// add to validation arr
					this.userValidators.push(ctrl.validationArr[i]);
				}
			}
		}
		
	},
	
	"IsRequired": function(sVal)
	{
		var regexp = /.+/;
		if(typeof(sVal)!='string')
			sVal = sVal.toString();
		if(!sVal.match(regexp) && !this.setRequired) 
		{
			this.setRequired = true;
			return TEXT_INLINE_FIELD_REQUIRED;
		}
		else
			return true;
			
	},
	
	"IsNumeric": function(sVal)
	{
		sVal = sVal.replace(/,/g,"");
		if(isNaN(sVal)) 
			return TEXT_INLINE_FIELD_NUMBER;
		else
			return true;
	},
//	
	"IsPassword": function(sVal)
	{
		var regexp1 = /^password$/;
		var regexp2 = /.{4,}/;
		if(sVal.match(regexp1))
			return TEXT_INLINE_FIELD_PASSWORD1;
		else if(!sVal.match(regexp2)) 
			return TEXT_INLINE_FIELD_PASSWORD2;		
		else
			return	true;	
	},

	"IsEmail": function(sVal)
	{
		var regexp = /^[A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return TEXT_INLINE_FIELD_EMAIL;
		else
			return true;
	}, 
//	
	"IsMoney": function(sVal)
	{
		var regexp = /^(\d*)\.?(\d*)$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return TEXT_INLINE_FIELD_CURRENCY;
		else
			return true;	
	},  
//	
	"IsZipCode": function(sVal)
	{
		var regexp = /^\d{5}([\-]\d{4})?$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return TEXT_INLINE_FIELD_ZIPCODE;
		else
			return true;	
	},
//	
	"IsPhoneNumber": function(sVal)
	{
		var regexp = /^\(\d{3}\)\s?\d{3}\-\d{4}$/;		
		var stripped = sVal.replace(/[\(\)\.\-\ ]/g, '');    
		if(sVal.match(/.+/) && (isNaN(parseInt(stripped)) || stripped.length != 10) ) 
			return TEXT_INLINE_FIELD_PHONE;
		else
			return true;
	},
//	
	"IsState": function(sVal)
	{
		if(sVal.match(/.+/) && !arrStates.inArray(sVal,false) ) 
			return TEXT_INLINE_FIELD_STATE;
		else
			return true;
	}, 
//	
	"IsSSN": function(sVal)
	{
		// 123-45-6789 or 123 45 6789
		var regexp = /^\d{3}(-|\s)\d{2}(-|\s)\d{4}$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return TEXT_INLINE_FIELD_SSN;
		else
			return true;
	},
//	
	"IsCC": function(sVal)
	{
		//Visa, Master Card, American Express
		var regexp = /^((4\d{3})|(5[1-5]\d{2}))(-?|\040?)(\d{4}(-?|\040?)){3}|^(3[4,7]\d{2})(-?|\040?)\d{6}(-?|\040?)\d{5}/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return TEXT_INLINE_FIELD_CC;
		else
			return true;
	},
//	
	"IsTime": function(sVal)
	{
		var regexp = /\d+/g;
		if(!sVal)
			return true;
		var arr = sVal.match(regexp);
		var bFlag = true;
		if(arr==null || arr.length > 3)  
			bFlag = false; 
		while(bFlag && arr.length < 3) 
			arr[arr.length] = 0; 
		if( bFlag && (arr[0]<0 || arr[0]>23 || arr[1]<0 || arr[1]>59 || arr[2]<0 || arr[2]>59) ) 
			bFlag = false; 
		if(!bFlag) 
			return TEXT_INLINE_FIELD_TIME;
		else
			return true;
	},
//
	"IsDate": function(sVal)
	{
		var fmt = "";
		switch (locale_dateformat){
			case 0 :
				fmt="MDY";
			break;
			case 1 : 
				fmt="DMY";
			break;	
			default:
				fmt="YMD";
			break;				
		};
		if(!this.isValidDate(sVal,fmt)){ 
			return TEXT_INLINE_FIELD_DATE;	
		}else{
			return true;
		}
	},
	
	"RegExp": function(sVal, regExpParamsObj){
		// create regExp obj		
		var re = new RegExp(regExpParamsObj.regex);
		// test against regExp
		if(sVal.length != 0 && (!re.test(sVal) || re.exec(sVal)[0] != sVal)){
			// return error text
			if(regExpParamsObj.messagetype == 'Text'){
				return regExpParamsObj.message;
			}else{
				return GetCustomLabel(regExpParamsObj.message);
			}
		}else{
			return true;
		}			
	},	
//		
	isValidDate: function(dateStr, format){
		if (format == null) 
			format = "MDY"; 
		format = format.toUpperCase();
		if (format.length != 3)  
			format = "MDY"; 
		if ((format.indexOf("M") == -1) || (format.indexOf("D") == -1) || (format.indexOf("Y") == -1) ) 
			format = "MDY"; 
		if (format.substring(0, 1) == "Y") 
		{ // If the year is first
			var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/;
			var reg2 = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/;
		} 
		// If the year is second
		else if (format.substring(1, 2) == "Y"){ 
			var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/;
			var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/;
		// The year must be third
		}else{ 
				var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/;
				var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
			}
		// If it doesn't conform to the right format (with either a 2 digit year or 4 digit year), fail
		if ((reg1.test(dateStr) == false) && (reg2.test(dateStr) == false)) 
			return false; 
		var parts = dateStr.split(RegExp.$1); // Split into 3 parts based on what the divider was
		// Check to see if the 3 parts end up making a valid date
		if (format.substring(0, 1) == "M") 
			var mm = parts[0];  
		else if (format.substring(1, 2) == "M") 
			var mm = parts[1];  
		else	
			var mm = parts[2]; 
		if (format.substring(0, 1) == "D") 
			var dd = parts[0];  
		else if (format.substring(1, 2) == "D") 
			var dd = parts[1]; 
		else	
			var dd = parts[2]; 
		if (format.substring(0, 1) == "Y") 
			var yy = parts[0];  
		else if (format.substring(1, 2) == "Y") 
			var yy = parts[1];  
		else 
			var yy = parts[2]; 
		if (parseFloat(yy) <= 50) 
			yy = (parseFloat(yy) + 2000).toString();
		if (parseFloat(yy) <= 99) 
			yy = (parseFloat(yy) + 1900).toString(); 
		var dt = new Date(parseFloat(yy), parseFloat(mm)-1, parseFloat(dd), 0, 0, 0, 0);
		if (parseFloat(dd) != dt.getDate()) 
			return false; 
		if (parseFloat(mm)-1 != dt.getMonth()) 
			return false; 
	   return true;
	}
} 
/**
 * Row control manager. Alows to add, delete and manage controls
 * Collection of control for the specific row
 */
Runner.controls.RowManager = Runner.extend(Runner.emptyFn, {
	/**
	 * Fields control collection 
	 * @param {object} fields
	 */
	fields: {},
	/**
	 * Id of row
	 * @type {int}
	 */
	rowId: -1,	
	/**
	 * Count of registred fields
	 * @param {int} fieldsCount
	 */
	fieldsCount: 0,
	/**
	 * Array of names of registered fields controls
	 * @type {array} control
	 */
	fieldNames: [],
	/**
	 * @constructor
	 * @param {int} rowId
	 */
	constructor: function(rowId){
		Runner.controls.RowManager.superclass.constructor.call(this, rowId);	
		this.fields = {};
		this.fieldNames = [];
		this.rowId = rowId;
	},
	
	/**
	 * Control to register
	 * @param {link} control
	 */
	register: function(control){	
		var controlName = control.fieldName;
		// if need to create new field
		if (!this.fields[controlName]) {			
			this.fields[controlName] = [];			
			this.fieldNames.push(controlName);
			this.fieldsCount++;			
		}
		// add control
		this.fields[controlName][control.ctrlInd] = control;
		/*if (control.secondCntrl){
			this.fields[controlName][1] = control;
		}else{
			this.fields[controlName][0] = control;
		}*/
		return true;		
	},
	/**
	 * Return control by following param
	 * @param {string} fName Pass false to get all controls of the row
	 * @param {int} controlIndex Pass false or null to get first control of the field
	 */
	getAt: function(fName, controlIndex){		
		// need to get all controls
		if (!fName){
			// array of row controls
			var rowControlsArr = [];
			// collect all controls from rowManager
			for(var i=0;i<this.fieldNames.length;i++){	
				// get all controls from field. Field may contain more then one
				for(var j=0;j< this.fields[this.fieldNames[i]].length;j++){
					// field control
					var fControl = this.getAt(this.fieldNames[i], j);
					// add to array
					rowControlsArr.push(fControl);
				}					
			}
			return rowControlsArr;
		}
		// if we need specific control
		if (!this.fields[fName]) {
			return false;
		}		
		return this.fields[fName][controlIndex];
	},
	/**
	 * Control which need to unregister
	 * @param {string} fName
	 */
	unregister: function(fName, controlIndex){
		// unreg all rows
		if (fName == null){
			for(var i=0;i<this.fieldsCount;i++){
				this.unregister(this.fieldNames[i], null);
				i--;
			}
			return true;
		// no such row
		}else if(!this.fields[fName]){
			return false;
		// unreg whole field
		}else if(controlIndex==null){
			for (var i=0;i<this.fields[fName].length; i++){
				this.unregister(fName, i);
			};			
			// delete fieldName from names arr
			for(var i=0;i<this.fieldsCount;i++){
				if (this.fieldNames[i]==fName){
					this.fieldNames.splice(i,1);						
					this.fieldsCount--;
				}
			}			
			delete this.fields[fName];
			return true;
		// unreg by params
		}else{
			// call object destructor
			if (this.fields[fName][controlIndex].destructor){
				this.fields[fName][controlIndex].destructor();
			}else if(this.fields[fName][controlIndex]["destructor"]){
				this.fields[fName][controlIndex]["destructor"]();
			}
			// remove from arr
			//this.fields[fName].splice(controlIndex, 1);
			delete this.fields[fName][controlIndex];
			return true;
		}
	},
	
	getMaxFieldIndex: function(fName){
		// if no field with such name
		if(!this.fields[fName]){
			return false;
		}
		
		return this.fields[fName].length;
	}
});
/** 
 * Table controls manager. Alows to add, delete and manage controls
 * Collection of control for the specific table.
 */
Runner.controls.TableManager = Runner.extend(Runner.emptyFn, {
	/**
	 * Row managers collection
	 * @param {object} rows
	 */
	rows: {},
	/**
	 * Name of table
	 * @type {String}
	 */
	tName: "",
	/**
	 * Count of registred rows
	 * @param {int} rowsCount
	 */
	rowsCount: 0,
	/**
	 * Ids of registered rows
	 * @type {array} control
	 */
	rowIds: [],
	/**
	 * Contructor
	 * @param {string} tName
	 */
	constructor: function(tName){
		this.tName = tName;
		this.rows = {};
		this.rowIds = [];
	},
	
	/**
	 * Control to register
	 * @param {#link} control
	 */
	register: function(control){		
		var controlId = control.id;
		// if need to create new row
		if (!this.rows[controlId]){
			this.rows[controlId] = new Runner.controls.RowManager(controlId);
			this.rowIds.push(controlId);
			this.rowsCount++;
		}
		// return register result
		return this.rows[controlId].register(control);
	},
	/**
	 * Return control by following params
	 * @param {string} rowId Pass false or null to get all controls of the table
	 * @param {string} fName Pass false or null to get all controls of the row
	 * @param {int} controlIndex Pass false or null to get first control of the field
	 */
	getAt: function(rowId, fName, controlIndex){		
		// if no rowId, then get all controls from table
		if (rowId==null){
			// array of controls for return
			var tableControlsArr = [];
			// collect all controls from rows managers
			for(var i=0;i<this.rowIds.length;i++){
				//get all controls of the row
				var rowControls = this.rows[this.rowIds[i]].getAt();
				// collect controls from row controls arr 
				for(var j=0;j<rowControls.length;j++){
					tableControlsArr.push(rowControls[j]);
				}	
			}
			return tableControlsArr;
		}
		// if row id defined, but no rows with such id
		if (!this.rows[rowId]) {
			return false;
		}
		// return result
		return this.rows[rowId].getAt(fName, controlIndex);	
	},
	/**
	 * Control which need to unregister
	 * @param {string} rowId
	 * @param {string} fName Pass false or null to clear all controls of the row
	 * @param {int} controlIndex Pass false or null to clear all control of the field
	 * @return {bool} true if success, otherwise false
	 */
	unregister: function(rowId, fName, controlIndex){		
		// unreg all rows
		if (rowId == null){
			for(var i=0;i<this.rowsCount;i++){
				this.rows[this.rowIds[i]].unregister(null, null);
			}
			return true;
		// no such row
		}else if(!this.rows[rowId]){
			return false;
		// unreg by params
		}else{
			var rowUnregStat = this.rows[rowId].unregister(fName, controlIndex);
			if (rowUnregStat && fName==null){
				// delete row id from ids arr
				for(var i=0;i<this.rowsCount;i++){
					if (this.rowIds[i]==rowId){
						this.rowIds.splice(i,1);						
						this.rowsCount--;
					}
				}
				// delete table object
				delete this.rows[rowId];
				return true;
			}else{
				return rowUnregStat;
			}
		}

	},
	
	getMaxFieldIndex: function(rowId, fName){
		// if no row with such id
		if(!this.rows[rowId]){
			return false;
		}
		
		return this.rows[rowId].getMaxFieldIndex(fName);
	}
});
/** 
 * Global control manager. Alows to add, delete and manage controls
 * Collection of controls for the specific table.
 * Should not be created directly, only one instance per page. 
 * Use its instance to get access to any control
 * @singleton
 */
Runner.controls.ControlManager = function(){
	/**
	 * Table managers collection
	 * @type {object} private
	 */
	var tables = {};	
	/**
	 * Count of registred tables
	 * @type {int} private
	 */
	var tablesCount = 0;
	/**
	 * Names of registred tables
	 * @type {array} private
	 */
	var tableNames = [];
	
	//console.log(tables, 'tables');
	
	return {
		/**
		 * Control to register
		 * @param {#link} control
		 */
		register: function(control){
			// return false if not control
			if (!control){
				return false;
			}
			// get table name
			var controlTable = control.table;		
			// if table not exists, create new one
			if (!tables[controlTable]){
				tables[controlTable] = new Runner.controls.TableManager(controlTable);	
				tableNames.push(controlTable);
				tablesCount++;		
			}
			//console.log(tables, 'tables before reg');
			// return register result
			return tables[controlTable].register(control);	
			
		},
		/**
		 * Returns control or array of controls by following params
		 * @param {string} tName
		 * @param {string} rowId Pass false or null to get all controls of the table
		 * @param {string} fName Pass false or null to get all controls of the row
		 * @param {int} controlIndex Pass false or null to get first control of the field
		 * @return {object} return control, array of controls or false
		 */
		getAt: function(tName, rowId, fName, controlIndex){
			
			// if no index passed we return control with 0 index
			controlIndex = controlIndex ? controlIndex : 0;
			
			if (tName === false){
				for(var i=0; i<tableNames.length;i++){
					var ctrl = tables[tableNames[i]].getAt(rowId, fName, controlIndex);
					if (ctrl !== false){
						return ctrl;
					}
				}
				return false;
			}
			
			// if table not exists
			if (!tables[tName]) {
				return false;
			}	
			
			// else return by params
			return tables[tName].getAt(rowId, fName, controlIndex);
		},
		/**
		 * Unregister control, row or table
		 * @param {string} tName
		 * @param {string} rowId Pass false or null to clear all controls of the table
		 * @param {string} fName Pass false or null to clear all controls of the row
		 * @param {int} controlIndex Pass false or null to clear first control of the field
		 * @return {bool} true if success, otherwise false
		 */
		unregister: function(tName, rowId, fName, controlIndex){	
			// if no table name passed, return false
			if (!tables[tName]) {
				return false;
			}			
			//controlIndex = controlIndex ? controlIndex : 0;			
			// recursively call unregister through table rows
			var tUnregStat = tables[tName].unregister(rowId, fName, controlIndex);
			// if delete whole table and recursive unreg call success
			if (tUnregStat && rowId==null){
				// delete table name from name arr
				for(var i=0;i<tablesCount;i++){
					if (tableNames[i]==tName){
						tableNames.splice(i,1);						
						tablesCount--;
					}
				}
				// delete table object
				delete tables[tName];
				return true;
			}else{
				return tUnregStat;
			}
		},
		
		getMaxFieldIndex: function(tName, rowId, fName){
			// if no table with such name
			if (!tables[tName]) {
				return false;
			}
			
			return tables[tName].getMaxFieldIndex(rowId, fName);
		},
		
		/**
		 * Resets all controls for specified table
		 * @method
		 * @param {string} tName
		 * @return {bool} true if success, otherwise false
		 */
		resetControlsForTable: function(tName){
			var cntrls = this.getAt(tName);
			if (!cntrls){
				return false;
			}
			for(var i=0;i<cntrls.length;i++){
				cntrls[i].reset();
			}
			return true;
		},
		
		/**
		 * Resets all controls for specified table
		 * @method
		 * @param {string} tName
		 * @return {bool} true if success, otherwise false
		 */
		clearControlsForTable: function(tName){
			var cntrls = this.getAt(tName);
			if (!cntrls){
				return false;
			}
			for(var i=0;i<cntrls.length;i++){
				cntrls[i].clear();
			}
			return true;
		}
	};
}(); 
/// <reference path="Runner.js" />
/**
 * Search form controller. Need for submit form in advanced and panel mode
 */
Runner.search.SearchForm = Runner.extend(Runner.emptyFn, {
	/**
     * Indicator. True when simple search edit box get focus 
     * @type Boolean
     */
    usedSrch: false,
	/**
    * Id of page, used when page loades dynamicly
    * @type {int}
    */
    id: -1,  
	/**
     * Name of table for which instance of class was created
     * @type string
     */
    tName: "",
	/**
	 * Type of search: panel on list, or advanced search page
	 * @type String
	 */
	searchType: "panel",  
	/**
     * jQuery obj of top radio with conditions
     * @type {obj}
     */
    conditionRadioTop: null,    
	/**
     * jQuery obj
     * @type 
     */
    srchForm: null,
	/**
    * ctrls map. Used for indicate which index conected with which search ctrl
    * @type obj
    */    
    ctrlsShowMap: null,
    
	/**
     * Override parent contructor
     * Add interaction with server
     * @param {obj} cfg
     */
    constructor: function(cfg){  
    	this.ctrlsShowMap = {};
    	// copy properties from cfg to controller obj
        Runner.apply(this, cfg);
    	//call parent
    	Runner.search.SearchForm.superclass.constructor.call(this, cfg);
        // get form object
        var srchFormId = 'frmSearch'+this.id;
        this.srchForm = $('#'+srchFormId);
        // radio with contion choose or|and
        this.conditionRadioTop = $('input:radio[name=srchType]');
    },
    /**
     * Add to hidden fields to search form
     * @param {string} val
     * @param {string} id
     * @param {string} type
     */
    addToForm: function(val, id, type){
    	if (typeof val == 'undefined'){
    		return false;
    	}
    	// lookup ctrls may return array value, for submit take only first
		val = (typeof val === 'object') ? val[0] : val;
    	type = (type ? type : 'hidden');
    	
    	var formElem = this.srchForm.find('input[name='+id+']');
    	if (formElem.length){
    		formElem.val(val.toString().entityify());
    	}else{
    		var elemHtml = '<input type="'+type+'" name="'+id+'" value="'+val.toString().entityify()+'" />';
    		this.srchForm.append(elemHtml);
    	}
    	
    	return true;
    },
    /**
     * Create and submit form 
     */
    submitSearch: function(){    	
    	// add common search params
    	this.addToForm('integrated', 'a');
    	this.addToForm(this.id, 'id');
    	    	
    	// add radio values
    	for (var i=0;i<this.conditionRadioTop.length;i++){
    		if(this.conditionRadioTop[i].checked == true){
    			this.addToForm(this.conditionRadioTop[i].value, 'criteria');
    			break;
    		}
    	}
    	    	
    	// for interator, field counter
		var j=1, notVal=''; 
		// add search params for each field
    	for(var fName in this.ctrlsShowMap){    		
    		// loop through all ctrls, except cached and deleted
    		for(var ind in this.ctrlsShowMap[fName]){    			
    			// get ctrls map for field name
    			var fMap = this.ctrlsShowMap[fName][ind];
    			// add ctrls vals    			
    			var ctrl1 = Runner.controls.ControlManager.getAt(this.tName, ind, fName, fMap[0]);
    			// add empty vals, if we search empty or not empty vals
    			var srchCombo = $('#'+this.getComboId(fName, ind)); 
    			var comboVal = srchCombo.val();
    			// add only non empty vals
    			if (ctrl1.isEmpty() && comboVal.indexOf('Empty') == -1){
    				continue;
    			}
    			// add first value and type
    			this.addToForm(ctrl1.ctrlType, 'type'+j);    	
    			var ctrl1Val = ctrl1.getStringValue();    			
    			this.addToForm(ctrl1Val, 'value'+j+'1');
    			// add fName to form
    			this.addToForm(fName, 'field'+j);
    			// add option to form
    			
    			
    			if (srchCombo.val().indexOf('NOT') == 0){
    				comboVal = comboVal.substr(4);
    			}
    			this.addToForm(comboVal, 'option'+j);
    			// add not checkBox to form
    			var srchCheckBox = $('#'+this.getCheckBoxId(fName, ind));
    			notVal = '';
    			// if there is any checkbox, then use its value, else parse value from combo
    			if (srchCheckBox.length){
    				notVal = srchCheckBox[0].checked ? 'on' : '';
    			}else{
    				notVal = srchCombo.val().indexOf('NOT') == 0 ? 'on' : '';
    			}
    			this.addToForm(notVal, 'not'+j); 
    			// if search type between and exists second ctrl
    			if (srchCombo.val().toLowerCase().indexOf('between') !== -1 && fMap[1]){
    				var ctrl2 = Runner.controls.ControlManager.getAt(this.tName, ind, fName, fMap[1]);
    				var ctrl2Val = ctrl2.getStringValue();	    			
    				this.addToForm(ctrl2Val, 'value'+j+'2');
    			}    			
    			j++;
    		}    		
    	} 
    	this.usedSrch = true;
    	// submit
    	this.srchForm.submit();
    },
    /**
     * Register ctrl in show map
     * @param {string} fName
     * @param {string} ind
     * @param {string} ctrlIndArr
     */
    addToShowMap: function(fName, ind, ctrlIndArr){
    	// create field names and indexes if they not created
    	!this.ctrlsShowMap[fName] ? this.ctrlsShowMap[fName] = {} : '';
    	!this.ctrlsShowMap[fName][ind] ? this.ctrlsShowMap[fName][ind] = {} : '';
    	// add ctrls indexes array
    	this.ctrlsShowMap[fName][ind] = ctrlIndArr;    	
    },
    /**
     * Adds block to map, regs its components and ands HTML
     * @param {} fName
     * @param {} ind
     * @param {} ctrlIndArr
     * @param {} blockHTML
     */
    addRegCtrlsBlock: function(fName, ind, ctrlIndArr){
    	// add to map
    	ctrlIndArr ? this.addToShowMap(fName, ind, ctrlIndArr) : '';
    },
    /**
     * Return search type combo id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getComboId: function(fName, ind){
    	return "srchOpt_" + ind + "_" + Runner.goodFieldName(fName);
    },
    /**
     * Return search checkbox id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getCheckBoxId: function(fName, ind){
    	return "not_" + ind + "_" + fName;
    },
    showAllSubmit: function(){
    	this.addToForm(this.id, 'id');
    	this.addToForm('showall', 'a');
    	this.usedSrch = false;
    	// submit
    	this.srchForm.submit();
    },
    /**
     * Submit for for return on list page
     */
    returnSubmit: function(){
    	this.addToForm(this.id, 'id');
    	this.addToForm('return', 'a');
    	// submit
    	this.srchForm.submit();
    },
    /**
     * Resets form ctrls, for panel should be overriden
     * @return {Boolean}
     */
    resetCtrls: function(){
    	Runner.controls.ControlManager.resetControlsForTable(this.tName);
		return false;
    }    
}); 
/**
 * Search form with user interface. 
 * 
 */
Runner.search.SearchFormWithUI = Runner.extend(Runner.search.SearchForm, {	  
    /**
    * Options panel show status indicator
    * @type Boolean
    */
    srchOptShowStatus: false,
    /**
    * Search win show status indicator
    * @type Boolean
    */
    srchWinShowStatus: false,
    /**
    * Show status indicator of div, which contains add filter buttons
    * @type Boolean
    */
    ctrlChooseMenuStatus: false,
    /**
    * Show status indicator of search type combos
    * @type Boolean
    */
    ctrlTypeComboStatus: false,
    /**
    * jQuery obj of search options div
    * @type {obj}
    */
    srchOptDiv: null,
    /**
    * jQuery object of img-button options panel expander
    * @type {obj}
    */
    srchOptExpander: null,
    /**
    * jQuery object of img-button search win expander
    * @type {obj}
    */
    srchWinExpander: null,
    /**
     * jQuery object with div, that contains all search elements
     * @type {obj}
     */
    srchBlock: null,
    /**
     * jQuery object with div, that contains all search controls
     * @type {obj}
     */
    srchCtrlsBlock: null,
    /**
    * Show status indicator of search block
    * @type Boolean
    */
    srchBlockStatus: true,
    
    /**
     * jQuery obj of top div with radio conditions
     * @type {obj}
     */
    topCritCont: null,
    
    /**
    * jQuery object of div with add-filter buttons
    * @type {obj}
    */
    ctrlChooseMenuDiv: null,
    /**
    * jQuery object of div with basic search controls
    * @type {obj}
    */
    srchPanelHeader: null,
    /**
     * jQuery object of div where panel should be placed. Used to toggle window and panel mode
     * @type {obj}
     */
    panelContainer: null,
    /**
     * jQuery object of div with b tags at bottom of the panel. Used on some layouts
     * for example on Madrid
     * @type {obj} 
     */
    bottomPanelRound: null,
    /**
     * jQuery obj of bottom search button
     * @type {obj} 
     */
    bottomSearchButt: null,    
    /**
    * Img src attr for hide opt
    * @type String
    */
    hideOptSrc: "images/search/hideOptions.gif",
    /**
    * Img src attr for show opt
    * @type String
    */
    showOptSrc: "images/search/showOptions.gif",
    /**
	 * Search panel icon switcher text
	 * @type 
	 */
    showOptText: "Show search options",
    /**
	 * Search panel icon switcher text
	 * @type 
	 */
    hideOptText: "Hide search options",
    /**
	 * Search type combos switcher text
	 * @type 
	 */
	showComboText: 'Show options',
	/**
	 * Search type combos switcher text
	 * @type 
	 */
	hideComboText: 'Hide options',
    /**
    * Array of search type combos
    * @type {array}
    */
    searchTypeCombosArr: null,
    /**
    * Array of search type combos
    * appear in window mode
    * @type {array}
    */
    searchTypeCombosWinArr: null,
    /**
     * Array of divs, that used as containers for one search control with its combos, delete buttons etc.
     * @type {array}
     */
    srchFilterRowArr: null,
    /**
     * Array of trs, that used as containers for one search control with its combos, delete buttons etc.
     * Trs appear in window mode
     * @type {array}
     */
    srchFilterRowWinArr: null,
    /**
     * Array of field names
     * @type array
     */
    fNamesArr: null,
    /**
    * ctrls map. Used for indicate which index conected with which search ctrl
    * @type obj
    */    
    ctrlsShowMap: null,
    /**
     * jQuery obj of link-switcher. Toggles search type combos
     * @type obj
     */
    showHideSearchComboButton: null,
    /**
     * Iframe object used for control choose menu coverage in IE6
     * @type {object}
     */
    iframe: null,
    /**
     * Hider object, hide selects in fly div mode
     * @type 
     */
    hider: null,
    /**
     * Fly div id, do not use controller id, to prevent fly div collisions
     * @type Number
     */
    flyDivId: 0,
    /**
     * True if records_block div margin-left was change, to prevent grid coverage
     * @type Boolean
     */
    recBlockMargChange: false,
	
	moveBlockPadding: 'padding-left',
    
    moveGridDiv: null,
	/**
    * Constructor
    * @param {obj} cfg
    */
    constructor: function(cfg) {
    	// recreate objects
        this.searchTypeCombosArr = [];
        this.searchTypeCombosWinArr = [];
        this.fNamesArr = [];
        this.srchFilterRowArr = [];
        this.srchFilterRowWinArr = [];
        //call parent
        Runner.search.SearchFormWithUI.superclass.constructor.call(this, cfg);
        // -------------------stuf used only when in panel mode------------------
        // private jQuery obj
        this.srchOptDiv = $("#searchOptions" + this.id);
        this.srchOptExpander = $("#showOptPanel" + this.id);
        this.srchWinExpander = $("#showSrchWin" + this.id);        
        //this.ctrlChooseMenuDiv = $("#controlChooseMenu" + this.id);

        // div container with all search stuff
        var srchBlockId = 'search_block'+this.id;
        this.srchBlock = $("#"+srchBlockId);
        // div object with all controls        
        var srchCtrlsBlockId = 'controlsBlock_'+this.id;
        this.srchCtrlsBlock = $("#"+srchCtrlsBlockId);
        // table object with all controls fpr window       
        var srchCtrlsBlockWinId = 'controlsBlock_'+this.id+'_win';
        this.srchCtrlsBlockWin = $("#"+srchCtrlsBlockWinId);
        // add object with basic search controls
        var srchPanelHeaderId = 'searchPanelHeader'+this.id;
        this.srchPanelHeader = $("#"+srchPanelHeaderId);
        
        var showHideSearchComboButtonId = 'showHideSearchType'+this.id;
        this.showHideSearchComboButton = $('#'+showHideSearchComboButtonId); 
        // container where panel placed
        var panelContainerId = 'searchPanelContainer'+this.id;
        this.panelContainer = $('#'+panelContainerId);
        // for some layouts bottom panel round should handled by this class
        var bottomPanelRoundId = 'searchPanelBottomRound'+this.id;
        this.bottomPanelRound = $('#'+bottomPanelRoundId);
        
        //this.ctrlChooseMenuDiv.appendTo(document.body);
        // check amsterdam margin change
        this.moveGridDiv = $("div[@moveforsearch='move"+this.id+"']");
		if($.browser.msie){
			if($(document.html).attr('dir').toLowerCase() == 'rtl')
				this.moveBlockPadding = 'padding-right';
		}else{
			if($("html[@dir=RTL]").length)
				this.moveBlockPadding = 'padding-right';
		}
		var mrgLeft = this.moveGridDiv.css(this.moveBlockPadding);        
		if (/*mrgLeft == '203px' && */$('#mainmenu_block').length==0 && $('#menu_block'+this.id).length == 0){
    		this.recBlockMargChange = true;
    	}
    	
        this.addDelegatedEvents(); 
        /*// open search panel for changing image
        if (this.srchOptShowStatus && !this.srchWinShowStatus){
        	this.showSearchOptions();
        }*/
    },
    
    /**
     * Binds hover events for table and div. 
     * Use parent containers as delegates
     * Call it in constructor
     */
    addDelegatedEvents: function(){
    	// for event handlers closures
    	var controller = this;
    	// filter div row mouseover event
    	this.srchCtrlsBlock.bind('mouseover', function(e){
    		// get event element
   			var target = Runner.util.Event.getTarget(e);
			// traverse to filter parent   			   				
			while (target && target.id != controller.srchCtrlsBlock.attr('id')) {
				if(target.id && target.id.indexOf('filter_') != -1) {
					// show del image
					$(target).find('img[@class=searchPanelButton]').css('visibility', 'visible');	
					$(target).removeClass('blockBorder').addClass('blockBorderHovered');
					break;
				} else {
					target = target.parentNode;
				}
			}
    	});
    	
    	// filter div row mouseout event
    	this.srchCtrlsBlock.bind('mouseout', function(e){
    		// get event element
   			var target = Runner.util.Event.getTarget(e);
			// traverse to filter parent   			   				
			while (target && target.id != controller.srchCtrlsBlock.attr('id')) {
				if(target.id && target.id.indexOf('filter_') != -1) {
					// hide del image
					$(target).find('img[@class=searchPanelButton]').css('visibility', 'hidden');	
					$(target).removeClass('blockBorderHovered').addClass('blockBorder');
					break;
				} else {
					target = target.parentNode;
				}
			}
    	});
    	
    	
    	// border hover events
    	this.srchCtrlsBlockWin.bind('mouseover', function(e){
    		// get event element
   			var target = Runner.util.Event.getTarget(e);
			// traverse to filter parent   			   				
			while (target.id != controller.srchCtrlsBlockWin.attr('id')) {				
				if(target.nodeName == "TD") {
					// get row with all tds
					var tr = $(target).parent();
					// show del image
					tr.find('img[@class=searchPanelButton]').css('visibility', 'visible');	
					// all cells
					var tds = tr.children();
					// make sure that we choosed td with controls, and not with loading box
					if ($(tds[0]).hasClass('srchWinCell')){
						// if second ctrldoesn't exist or is hidden, make right border for last-1 child
		    			var lastVisible = tds.length-1;    		
		    			//console.log($(tds[tds.length-1]).children(), 'child');
		    			if ($(tds[tds.length-1]).children().length === 0 || $(tds[tds.length-1]).find('*:first-child').css('display') == 'none'){
		    				lastVisible--;
		    			}    
		    			
		    			// set style for left element
		    			$(tds[0]).removeClass('cellBorderCenter').removeClass('cellBorderLeft').addClass('cellBorderCenterHovered').addClass('cellBorderLeftHovered');
		    			// set styles for center elements
		    			for(var i=0;i<lastVisible;i++){
		    				// try to remove also right style, because it may come when second ctrl was invisible
		    				$(tds[i]).removeClass('cellBorderCenter').removeClass('cellBorderRightHovered').addClass('cellBorderCenterHovered');
		    			}    
		    			//set style for last elem
		    			$(tds[lastVisible]).removeClass('cellBorderCenter').removeClass('cellBorderRight').addClass('cellBorderCenterHovered').addClass('cellBorderRightHovered');
					}
					break;
				} else {
					target = target.parentNode;
				}
			}			
    	});
    	
    	this.srchCtrlsBlockWin.bind('mouseout', function(e){
    		// get event element
   			var target = Runner.util.Event.getTarget(e);
			// traverse to filter parent   			   				
			while (target.id != controller.srchCtrlsBlockWin.attr('id')) {				
				if(target.nodeName == "TD") {
					// get row with all tds
					var tr = $(target).parent();
					// show del image
					tr.find('img[@class=searchPanelButton]').css('visibility', 'hidden');	
					// all cells
					var tds = tr.children();
					// make sure that we choosed td with controls, and not with loading box
					if ($(tds[0]).hasClass('srchWinCell')){
						// if second ctrldoesn't exist or is hidden, make right border for last-1 child
		    			var lastVisible = tds.length-1;
		    			if ($(tds[tds.length-1]).children().length === 0 || $(tds[tds.length-1]).find('*:first-child').css('display') == 'none'){
		    				lastVisible--;
		    			}
		    			// set style for left element
		    			$(tds[0]).removeClass('cellBorderCenterHovered').removeClass('cellBorderLeftHovered').addClass('cellBorderCenter').addClass('cellBorderLeft');
		    			// set styles for center elements
		    			for(var i=0;i<lastVisible;i++){
		    				$(tds[i]).removeClass('cellBorderCenterHovered').addClass('cellBorderCenter');
		    			}
		    			//set style for last elem
		    			$(tds[lastVisible]).removeClass('cellBorderCenterHovered').removeClass('cellBorderRightHovered').addClass('cellBorderCenter').addClass('cellBorderRight');
					}
					break;
				} else {
					target = target.parentNode;
				}
			}	
    	});  
    },
    /**
     * Return search type combo container id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getComboContId: function(fName, ind, isWin){    	
    	return "searchType_" + ind + "_" + Runner.goodFieldName(fName) + (isWin ? '_win' : '');
    }, 
    
    getComboId: function(fName, id){
    	return "srchOpt_" + id + "_" + Runner.goodFieldName(fName);
    },
    /**
     * Return filter div id
     * @param {string} fName
     * @param {int} ind
     * @return {string}
     */
    getFilterDivId: function(fName, ind, isWin){    	
    	return "filter_" + ind + "_" + Runner.goodFieldName(fName) + (isWin ? '_win' : '');
    },
    /**
     * Recalc window dimension after change content
     * @param {object} winObj
     * @return {Boolean} true if success
     */
	recalcWindowDim: function(winObj){
		// if no window, return false
		if (!this.srchWinShowStatus){
			return false;
		}
		// if window object not passed, get it
		winObj = (winObj ? winObj : $("#fly"+this.flyDivId));		
		// recalc
    	var x = winObj.css('left'), y = winObj.css('top');
    	var flyDivDimAndCoorsObj = getFlyDivSizeAndCoors(this.flyDivId, x, y);
   		setFlyDivDimAndCoors(flyDivDimAndCoorsObj, this.flyDivId);
        
   		return true;
	},
    /**
    * Create flyDiv with search controls
    * If used as onlick handler pass event object, for get click coords
    * @param {event} e
    */
    showSearchWin: function(e, id) { 
    	
    	// lazy-init vars, redeclare fly div title
        var headerObj = {
        	title: '<span style="color: black;">Search for:&nbsp;</span>',
        	buttons: [{src: 'images/search/windowPin.gif', handler: 'searchController'+this.id+'.hideSearchWin(); ', alt: 'Hide window', title: 'Hide window'}],
        	closeButton: false
        };
        
        var cfgObj = {
        	headerObj: headerObj,
        	border:{
        		color: $('#controlChooseMenu'+this.id).css('background-color')
        	}
        };
        // redeclare function, after lazy-init
        this.showSearchWin = function(e, id){
        	this.hideCtrlChooseMenu();
        	// get click coors
	        var x = 50, y = 50;
	        if (Runner.isIE && e) {
	            y = e.y;
	            x = e.x;
	        } else if (e) {
	            y = e.clientY;
	            x = e.clientX;
	        }
	        // handler text, will fire before window closed
	        var oncloseHandlerCode = '';
	        
	        this.flyDivId = id || ++window.flyid;
	        // create div
	        var divContainer = DisplayFlyDiv("", "", this.flyDivId, "", x, y, 'search', "", this.flyDivId, oncloseHandlerCode, cfgObj,0);	 
	        
	        
	        $(divContainer).css('padding-top', '10px');
	        // set div color, because in panel mode in IE6 it suddenly covers controls
	        //this.srchOptDiv.css('background-color', this.panelContainer.css('background-color'));
	        $(divContainer).css('background-color', this.panelContainer.css('background-color'));
	        // add to fly div
	        this.srchPanelHeader.appendTo(divContainer);
	        this.srchOptDiv.appendTo(divContainer);   
	        // hide div for panel mode
	        this.srchCtrlsBlock.hide();
	        // move all content to table from divs
	        this.moveCtrlsToTable();
	        // show table for window mode
	        this.srchCtrlsBlockWin.show();	        
	        // resize and set coors with new content
	        var flyDivDimAndCoorsObj = getFlyDivSizeAndCoors(this.flyDivId, x, y);
	        
	        if (e.w && e.h){
	        	flyDivDimAndCoorsObj.height = e.h;
	        	flyDivDimAndCoorsObj.width = e.w;
	        }
	        setFlyDivDimAndCoors(flyDivDimAndCoorsObj, this.flyDivId);	 
	        
	        this.showSearchOptions();
	        // set show indicator
	        this.srchWinShowStatus = true;
			
			if (this.recBlockMargChange){
				this.moveGridDiv.css(this.moveBlockPadding, 0);
			}
        }
    	// for first use
    	this.showSearchWin(e, id);        
    },
    /**
     * Move controls when switch to window mode.
     * On each table and div row this method call moveCtrlsToTableRow,
     * which move html and DOM from divs to tds
     */
    moveCtrlsToTable: function(){
    	// loop through div rows
    	for(var i=0; i<this.srchFilterRowArr.length;i++){
    		var divRowId = this.srchFilterRowArr[i].attr('id');
    		var tableRowId = divRowId+'_win';
    		var tableRow = $('#'+tableRowId);
    		if (this.srchFilterRowArr[i].css('display') != 'none'){
    			tableRow.show();   
    		}else{
    			tableRow.css('display', 'none');
    		}
    		 		
    		// move div row content to table row symetrically
    		this.moveCtrlsToTableRow(this.srchFilterRowArr[i], tableRow);
    	}
    },
    /**
     * Used to move html and DOM of each div row to table row
     */
    moveCtrlsToTableRow: function(divRow, tableRow){
    	var divCells = divRow.children();
    	var tds = tableRow.children();
    	// loop through div cells
    	for(var i=0; i<divCells.length;i++){    		
    		// move all content of div cell to td
    		var divCellChildren = $(divCells[i]).children();
    		// clear from script tag, to prevent executing it twice
    		divCellChildren.find('script').remove();
    		// move with DOM objects, not only HTML
    		divCellChildren.appendTo($(tds[i]));
    		
    		if ($(divCells[i]).css('display') == 'none'){
    			$(tds[i]).hide();
    		}else{
    			$(tds[i]).show();
    		}
    	}	  
    	// move field name in this way, because it conatins only text
    	$(tds[1]).html($(divCells[1]).html());
    },
    /**
     * Move controls when switch to panel mode, from window.
     * On each table and div row this method call moveCtrlsToDivRow,
     * which move html and DOM from tds to divs
     */
    moveCtrlsToDiv: function(){
    	// loop through div rows
    	for(var i=0; i<this.srchFilterRowWinArr.length;i++){
    		var tableRowId = this.srchFilterRowWinArr[i].attr('id');
    		var divRowId = tableRowId.substr(0, tableRowId.lastIndexOf('_'));//divRowId+'_win';
    		var divRow = $('#'+divRowId);
    		if (this.srchFilterRowWinArr[i].css('display') != 'none'){
    			divRow.show();   
    		}else{
    			divRow.css('display', 'none');
    		}    		
    		// move div row content to table row symetrically
    		this.moveCtrlsToDivRow(this.srchFilterRowWinArr[i], divRow);
    	}
    },
    /**
     * Used to move html and DOM of each table row to div row
     */
    moveCtrlsToDivRow: function(tableRow, divRow){
    	var divCells = divRow.children();
    	var tds = tableRow.children();
    	// loop through div cells
    	for(var i=0; i<tds.length;i++){    		
    		// move all content of div cell to td
    		var tableCellChildren = $(tds[i]).children();
    		// clear from script tag, to prevent executing it twice
    		tableCellChildren.find('script').remove();
    		// move with DOM objects, not only HTML
    		tableCellChildren.appendTo($(divCells[i]));
    		if ($(tds[i]).css('display') == 'none'){
    			$(divCells[i]).hide();
    		}else{
    			$(divCells[i]).show();
    		}
    	}	  
    	// move field name in this way, because it conatins only text
    	$(divCells[1]).html($(tds[1]).html());
    },
    
    /**
    * Removes fly div, and place controls to search panel
    */
    hideSearchWin: function(id) {
    	this.hideCtrlChooseMenu();
    	// remove color to prevent strange controls coverage in IE6
    	//this.srchOptDiv.css('background-color', '');
    	// move opt div to search panel        
        this.srchOptDiv.prependTo(this.panelContainer);
		// to correct amsterdam layout with no menu
		this.corGridDiv();
        // hide table
    	this.srchCtrlsBlockWin.hide();
    	// move controls
    	this.moveCtrlsToDiv();
    	// show panel mode div
    	this.srchCtrlsBlock.show();        
        this.srchPanelHeader.prependTo($("#searchform"+this.id));
        // remove fly win
        RemoveFlyDiv(id || this.flyDivId, true);
        // set status indicator
        this.srchWinShowStatus = false;
    },
    
     

    /**
    * Search win switcher
    * opens and closes search win
    */
    toggleSearchWin: function(e) {
        this.srchWinShowStatus ? this.hideSearchWin() : this.showSearchWin(e);
        this.hideCtrlChooseMenu();
    },
   /**
    * Showes search options div and changes image expander 
    */
    showSearchBlock: function() {
    	// show div
        this.srchBlock.show();
        this.srchBlockStatus = true;
    },
    /**
    * Closes search options div and changes image expander 
    */
    hideSearchBlock: function() {
    	// hide div
        this.srchBlock.hide();
        this.srchBlockStatus = false;
    },
    /**
    * Search options switcher
    * opens and closes options in search panel
    */
    toggleSearchBlock: function() {
        // can open panel, only if win is hidden
        (this.srchBlockStatus && !this.srchWinShowStatus) ? this.hideSearchBlock() : this.showSearchBlock();
    },
	/**
    * Correct indentation of grid block for search if hasn't menu on list page
    */
    corGridDiv: function(){
		var mrgLeft = this.moveGridDiv.css(this.moveBlockPadding);
    	if (mrgLeft == 'auto' ||  mrgLeft == '0px'){
    		this.recBlockMargChange = true;
			this.moveGridDiv.css(this.moveBlockPadding, 203);
    	}
	},
    /**
    * Showes search options div and changes image expander 
    */
    showSearchOptions: function() {
    	// to correct amsterdam layout with no menu
		this.corGridDiv();
        // show div
    	this.srchOptDiv.show();	
    	// show bottom round if exist
        this.bottomPanelRound.css('display',  '');
        // change image
        
        this.srchOptExpander.css("background-image", 'url("'+this.hideOptSrc+'")');
        this.srchOptExpander.attr('alt', this.hideOptText);
        this.srchOptExpander.attr('title', this.hideOptText);
        this.srchOptShowStatus = true;
    },
    /**
    * Closes search options div and changes image expander 
    */
    hideSearchOptions: function() {
    	// to correct amsterdam layout with no menu 
    	if (this.recBlockMargChange){
    		this.moveGridDiv.css(this.moveBlockPadding, 0);
    	}
    	// hide div
    	this.srchOptDiv.hide();
        // hide bottom round if exist
        this.bottomPanelRound.css('display',  'none');
        // change image
        this.srchOptExpander.css("background-image", 'url("'+this.showOptSrc+'")');
        this.srchOptExpander.attr('alt', this.showOptText);
        this.srchOptExpander.attr('title', this.showOptText);
        this.srchOptShowStatus = false;
    },
    /**
    * Search options switcher
    * opens and closes options in search panel
    */
    toggleSearchOptions: function() {
        // can open panel, only if win is hidden
        (this.srchOptShowStatus && !this.srchWinShowStatus) ? this.hideSearchOptions() : this.showSearchOptions();
        this.hideCtrlChooseMenu();
    },

    /**
    * Showes search options div and changes image expander 
    */
    showCtrlChooseMenu: function() { 
    	if (!this.ctrlChooseMenuDiv){
	    	this.ctrlChooseMenuDiv = $("#controlChooseMenu" + this.id);
	    	this.ctrlChooseMenuDiv.appendTo(document.body);
    	}
    	// create closure
    	var controller = this;
    	// add events   
		var hideHandler = function(){
			controller.showCtrlChooseMenu();
		}
		var hideTask = new Runner.util.DelayedTask(hideHandler);
		controller.ctrlChooseMenuDiv.bind('mouseover', function(e){
			showTask.cancel();
            hideTask.delay(50, hideHandler, null, [e]);
        });    		
    	
		var showHandler = function(){
			controller.hideCtrlChooseMenu();
		}
		var showTask = new Runner.util.DelayedTask(showHandler);
		controller.ctrlChooseMenuDiv.bind('mouseout', function(e){
			hideTask.cancel();
            showTask.delay(50, showHandler, null, [e]);
        });    		
    	
    	// lazy init function
    	if (Runner.isIE6){    		
    		this.iframe = new Runner.util.IEHelper.iframe(/*this.ctrlChooseMenuDiv[0]*/);
    		this.hider = new Runner.util.IEHelper.selectsHider(this.ctrlChooseMenuDiv[0]);
    	}
    	// redefine
    	this.showCtrlChooseMenu = function(){
			// set menu position, relative to Add criteria link
    		var posObj = findPos($("#showHideControlChooseMenu"+this.id)[0]);
			// calc coordinates
    		var divT = posObj[1]+posObj[3], divL = posObj[0];
	    	// add only in win mode, strange positioning in fly div
	    	this.ctrlChooseMenuDiv.css('top', divT).css('left', divL);
	    	// show it
	        this.ctrlChooseMenuDiv.show();
	         // set div width, after div is visible, for correct offsetWidth data
	        this.ctrlChooseMenuDiv[0].offsetWidth < 80 ? this.ctrlChooseMenuDiv.css('width', '65px') : '';
	        // add iframe in panel mode
	        if (Runner.isIE6 && !this.srchWinShowStatus){
	       		// create iframe for IE6
		        this.iframe.reset({
					l: divL,
					t: divT,
					h: this.ctrlChooseMenuDiv[0].offsetHeight,
					w: this.ctrlChooseMenuDiv[0].offsetWidth
				});      				
			// in window mode hide combos	
	        }else if(Runner.isIE6 && this.srchWinShowStatus){
	        	this.hider.showSels();
	        	this.hider.getSelects();
	        	this.hider.hideSels();
	        }
	        // set max z-index
	        Runner.getZindex(this.ctrlChooseMenuDiv);
	        this.ctrlChooseMenuStatus = true;
    	}
    	// call function, after lazy-init
    	this.showCtrlChooseMenu();
    },
    /**
    * Closes search options div and changes image expander 
    */
    hideCtrlChooseMenu: function() {
    	if (!this.ctrlChooseMenuDiv){
	    	this.ctrlChooseMenuDiv = $("#controlChooseMenu" + this.id);
	    	this.ctrlChooseMenuDiv.appendTo(document.body);
    	}
        this.ctrlChooseMenuDiv.hide();
        this.ctrlChooseMenuStatus = false;
        if (Runner.isIE6 && !this.srchWinShowStatus && this.iframe){
        	this.iframe.hide();
        }else if(Runner.isIE6 && this.srchWinShowStatus && this.iframe){
        	this.hider.showSels();
        }
    },

    /**
    * Search options switcher
    * opens and closes options in search panel
    */
    toggleCtrlChooseMenu: function() {
        this.ctrlChooseMenuStatus ? this.hideCtrlChooseMenu() : this.showCtrlChooseMenu();        
    },
    
	/**
    * Search type combos show handler
    */
    showCtrlTypeCombo: function() {
        for (var i = 0; i < this.searchTypeCombosArr.length; i++) {        	
	    	this.searchTypeCombosArr[i].show();	
	    	this.searchTypeCombosArr[i].find('select').show();
        }
        for (var i = 0; i < this.searchTypeCombosWinArr.length; i++) {        	
	    	this.searchTypeCombosWinArr[i].show();	
	    	this.searchTypeCombosWinArr[i].find('select').show();
        }
        this.showHideSearchComboButton.html(this.hideComboText);
        this.showHideSearchComboButton.attr('title', this.hideComboText);
        this.ctrlTypeComboStatus = true;
        
    },
    /**
    * Search type combos hide handler
    */
    hideCtrlTypeCombo: function() {
        for (var i = 0; i < this.searchTypeCombosArr.length; i++) {        	
            this.searchTypeCombosArr[i].hide();
            this.searchTypeCombosArr[i].find('select').hide();
        }
        for (var i = 0; i < this.searchTypeCombosWinArr.length; i++) {        	
            this.searchTypeCombosWinArr[i].hide();
            this.searchTypeCombosWinArr[i].find('select').hide();
        }
        this.showHideSearchComboButton.html(this.showComboText);
        this.showHideSearchComboButton.attr('title', this.showComboText);
        this.ctrlTypeComboStatus = false;
    },    
    /**
    * Search type combos show\hide switcher
    */
    toggleCtrlTypeCombo: function() {
        this.ctrlTypeComboStatus ? this.hideCtrlTypeCombo() : this.showCtrlTypeCombo();
    },
    /**
     * Criterias show|hide controller
     * @param {int} ctrlsCount
     */
    toggleCrit: function(ctrlsCount){
    	// lazy init, get conditions containers
        var topCritContId = 'srchCritTop'+this.id;
        this.topCritCont = $('#'+topCritContId);
        var bottomSearchButtId = 'bottomSearchButt'+this.id;
        this.bottomSearchButt = $('#'+bottomSearchButtId); 
        // redefine after first call
        this.toggleCrit = function(ctrlsCount){
        	ctrlsCount > 1 ? this.topCritCont.show() : this.topCritCont.hide();
    		ctrlsCount > 0 ? this.bottomSearchButt.show() : this.bottomSearchButt.hide();
        }
        // for first call
		this.toggleCrit(ctrlsCount);
    }
});
 
/**
 * search panel controller. Used for manage search on the list page
 * for multiple search classes use id param.
 * @class
 * @param {object} cfg
 */
Runner.search.SearchController = Runner.extend(Runner.search.SearchFormWithUI, {
   
    /**
     * jQuery obj of simple search edit box
     * @type {obj}
     */
    smplSrchBox: null,
    
    simpleSrchTypeCombo: null,
    
    simpleSrchFieldsCombo: null,
    
    panelStateExpires: '',
   /**
    * Ajax add filter cache url
    * @type String
    */
    ajaxSearchUrl: "",  
    /**
     * Reusable style display none
     * @type String
     */
    styleDispNoneText: 'display: none;',
    /**
     * Short table name, used for create urls
     */
    shortTName: "",
    /**
     * Override parent contructor
     * Add interaction with server
     * @param {obj} cfg
     */
    constructor: function(cfg){    	
    	//call parent
    	Runner.search.SearchController.superclass.constructor.call(this, cfg);	
    	// set search url, for ajax
        this.ajaxSearchUrl = this.shortTName + '_search.php';
        // edit box any field contains search
        this.smplSrchBox = $('#ctlSearchFor'+this.id);
        
        this.simpleSrchTypeCombo = $('#simpleSrchTypeCombo'+this.id);
        this.simpleSrchFieldsCombo = $('#simpleSrchFieldsCombo'+this.id);
        
        
        var oldOnBeforeUnload = window.onbeforeunload;
        var controller = this;        
        window.onbeforeunload = function(){
        	controller.rememberPanelState();
        	if (oldOnBeforeUnload){
        		oldOnBeforeUnload();        		
        	}        	
        }
        
    },
    
    /**
     * Get index of last added from cache control. 
     * @param {string} filterName
     * @return {int}
     */    
    getLastAddedInd: function(filterName){
    	// if no map for this field
    	if (!this.ctrlsShowMap[filterName]){
    		return false;
    	}
    	// get last added and not cached ctrls block index
    	var maxInd = 0, beforeMaxInd=false, i=0;
		for(var ind in this.ctrlsShowMap[filterName]){			
			// need to convert to int from string. May be because object property name is string, typeof return string
			ind = parseInt(ind);
			// get max index, it will give last cached
			if (maxInd < ind){
				beforeMaxInd = maxInd;
				maxInd = ind;
			}
			// at first time take maxInd, because 0 may not appear
			if (i===0){
				beforeMaxInd = maxInd;
			}
			i++;
		}
		return beforeMaxInd;
    },
    /**
     * returns last added filter, usefull when add new
     * 
     * @param {string} filterName field name
     * @return {obj} true if success otherwise false
     */
    getLastAdded: function(filterName){
    	var beforeMaxInd = this.getLastAddedInd(filterName);
    	if (!beforeMaxInd){
    		return false;
    	}
    	// get obj
    	var filterObj = $('#'+this.getFilterDivId(filterName, beforeMaxInd, this.srchWinShowStatus));    	
    	if (filterObj.length){
    		return filterObj;
    	}else{
    		return false;
    	}
    },
    
    /**
     * Adds ctrls block HTML to DOM
     * @param {string} fName
     * @param {string} ind
     * @param {object} blockHTML
     */
    addCtrlsHtml: function(fName, ind, blockHTML){
    	this.addPanelHtml(fName, ind, blockHTML);
    	this.addTableHtml(fName, ind);
    	// take div container, or tr
    	var rowCont = $('#'+this.getFilterDivId(fName, ind, this.srchWinShowStatus))
    	// put into cells block html
    	var cells = rowCont.children();
    	$(cells[0]).html(blockHTML.delButt);
    	$(cells[2]).html(blockHTML.comboHtml);
    	$(cells[3]).html(blockHTML.control1);
    	$(cells[4]).html(blockHTML.control2);

  		// execute additional js code
		eval(blockHTML.jsCode);	
    },
    
    addTableHtml: function(fName, ind){
    	// ctrl main container id
    	var newSrchCtrlContId = this.getFilterDivId(fName, ind, true);
    	// add ctrl main container
    	var filterRowHtml = this.createTableRow(newSrchCtrlContId, 'winRow', this.styleDispNoneText, '');
    	this.srchCtrlsBlockWin.append(filterRowHtml);
    	// main container obj
    	var newSrchCtrlCont = $("#"+newSrchCtrlContId);
    	// add del button
    	newSrchCtrlCont.append(this.createTableCell('', 'srchWinCell', '', ''));
    	// add div with field name
    	var fNameCellHtml = this.createTableCell('', 'srchWinCell', '', fName+':&nbsp;');
    	newSrchCtrlCont.append(fNameCellHtml);
    	// combo type container id
    	var comboHtml = this.createTableCell(this.getComboContId(fName, ind, true), 'srchWinCell', (this.ctrlTypeComboStatus ? '' : this.styleDispNoneText), '');    	
    	newSrchCtrlCont.append(comboHtml);
    	// add first control obj with html
    	newSrchCtrlCont.append(this.createTableCell('', 'srchWinCell', '', ''));
    	// add second ctrl obj if exists, or set empty container, for border puposes
    	newSrchCtrlCont.append(this.createTableCell('', 'srchWinCell', '', '')); 
    	
    	return newSrchCtrlCont;
    },
    
    addPanelHtml: function(fName, ind, blockHTML){
    	// ctrl main container id
    	var newSrchCtrlContId = this.getFilterDivId(fName, ind);
    	// add ctrl main container
    	var filterDivHtml = this.createDivCont(newSrchCtrlContId, 'srchPanelRow blockBorder', this.styleDispNoneText, '');
    	this.srchCtrlsBlock.append(filterDivHtml);
    	// main container obj
    	var newSrchCtrlCont = $("#"+newSrchCtrlContId);
    	// add del button
    	newSrchCtrlCont.append(this.createDivCont('', 'srchPanelCell', '', ''));
    	// add div with field name
    	var fNameDivHtml = this.createDivCont('', 'srchPanelCell', '', blockHTML.fLabel+':&nbsp;');
    	newSrchCtrlCont.append(fNameDivHtml);
    	// combo type container id
    	var comboHtml = this.createDivCont(this.getComboContId(fName, ind), 'srchPanelCell srchPanelCell2', (this.ctrlTypeComboStatus ? '' : this.styleDispNoneText), '');    	
    	newSrchCtrlCont.append(comboHtml);
    	// add first control obj with html
    	newSrchCtrlCont.append(this.createDivCont('', 'srchPanelCell srchPanelCell2', '', ''));
    	// add second ctrl obj if exists, or set empty container, for border puposes
    	newSrchCtrlCont.append(this.createDivCont('', 'srchPanelCell srchPanelCell2', '', '')); 
    	
    	return newSrchCtrlCont;
    },
    /**
     * Adds block to map, regs its components and ands HTML
     * @param {} fName
     * @param {} ind
     * @param {} ctrlIndArr
     * @param {} blockHTML
     */
    addRegCtrlsBlock: function(fName, ind, ctrlIndArr, blockHTML){
    	// call parent
    	Runner.search.SearchController.superclass.addRegCtrlsBlock.call(this, fName, ind, ctrlIndArr);
    	//add to DOM
    	blockHTML ? this.addCtrlsHtml(fName, ind, blockHTML) : "";    	
    	// set links for parent and child if lookup ctrl
    	var ctrl = Runner.controls.ControlManager.getAt(this.tName, ind, fName);
    	// if ctrl hidden it's used for cache, than, do not add link
    	if (!ctrl.hidden){
    		//this.setDependences(ctrl, true);	
    		this.setDependences(ctrl);
    	}    	
    	// reg combos
    	this.searchTypeCombosArr.push($("#"+this.getComboContId(fName, ind)));
    	// reg td combos
    	this.searchTypeCombosWinArr.push($("#"+this.getComboContId(fName, ind, true)));
    	// reg filter div block
    	this.srchFilterRowArr.push($("#"+this.getFilterDivId(fName, ind)));
    	// reg filter tr row
    	this.srchFilterRowWinArr.push($("#"+this.getFilterDivId(fName, ind, true)));
    	// call crit controller
  		this.toggleCrit(this.getVisibleBlocksCount());	
    },
   
    /**
     * Creates div container html
     * @param {string} id
     * @param {string} cssClass
     * @param {string} style
     * @param {string} innerHtml
     * @return {string}
     */
    createDivCont: function(id, cssClass, style, innerHtml){
    	return '<div class="'+cssClass+'" id="'+id+'" style="'+style+'">'+innerHtml+'</div>';
    },
    
    createTableRow: function(id, cssClass, style, innerHtml){
    	return '<tr class="'+cssClass+'" id="'+id+'" style="'+style+'">'+innerHtml+'</tr>';
    },
    
    createTableCell: function(id, cssClass, style, innerHtml){
    	return '<td class="'+cssClass+'" id="'+id+'" style="'+style+'">'+innerHtml+'</td>';
    },
    
    /**
     * Put block into right place depending on ctrl type. 
     * If parent field name passed, ctrl will be placed bellow parent
     * If no parent passed, ctrl will be placed above last added for this field
     * 
     * @param {string} filterName
     * @param {int} cachedInd
     * @param {string} parentFieldName
     */
    putCachedBlock: function(filterName, cachedInd, parentFieldName){
    	// get control from cache
        var cachedRow = $("#"+this.getFilterDivId(filterName, cachedInd, this.srchWinShowStatus));        
    	// move cached div to top, insert it after control choose menu
        var lastAdded = this.getLastAdded(filterName);
        // if use parent
        if (parentFieldName && this.getLastAdded(parentFieldName)){
        	cachedRow.insertAfter(this.getLastAdded(parentFieldName));
        }else if(lastAdded){
        	cachedRow.insertBefore(lastAdded);
        }else{
        	// if no parent, add to window
        	if (this.srchWinShowStatus){
        		this.srchCtrlsBlockWin.prepend(cachedRow);
        	// or to panel container
        	}else{
        		this.srchCtrlsBlock.prepend(cachedRow);
        	}
        	
        }
        // show row with controls
    	cachedRow.show();	
        // make window height bigger
        /*if (this.srchWinShowStatus){
        	this.recalcWindowDim();	
        }*/        
    },
    
    
    createLoadingBox: function(filterName){
    	var loadingTxt = '&nbsp;&nbsp;' + filterName + ':&nbsp;loading&nbsp;...&nbsp;';
    	// add div for panel mode
    	if (!this.srchWinShowStatus){
	    	var loadDiv = document.createElement('DIV');	    	
	    	$(loadDiv).addClass('blockBorderHovered').html(loadingTxt);
	    	return loadDiv;
	    // add tr for win mode
    	}else{
    		var loadTr = document.createElement('TR');
    		var loadTd = document.createElement('TD');
    		$(loadTd).attr('colspan', '4').addClass('cellBorderRightHovered').addClass('cellBorderLeftHovered').addClass('cellBorderCenterHovered').html(loadingTxt);    		
    		$(loadTr).addClass('winRow').append(loadTd);
	    	return loadTr;	
    	}   
    },
    
    putLoadingBox: function(loadBox, filterName){
    	// move cached div to top, insert it after control choose menu
        var lastAdded = this.getLastAdded(filterName);
        
        if(lastAdded){
        	$(loadBox).insertBefore(lastAdded);
        }else{
        	// if no parent, add to window
        	if (this.srchWinShowStatus){
        		this.srchCtrlsBlockWin.append($(loadBox));
        	// or to panel container
        	}else{
        		this.srchCtrlsBlock.append($(loadBox));
        	}
        	
        }
    },
    /**
     * Set dependent and parent links to ctrls. 
     * If passed triggerReload, will invoke event of parent ctrl, to reload dependent ctrls
     * 
     * @param {obj} ctrl dependent control
     * @param {string} parentFieldName field name of parent ctrl
     * @param {Boolean} triggerReload pass true to reload dependent ctrls
     * @return {Boolean} true if success otherwise false
     */
    setDependences: function(ctrl, triggerReload){
    	
    	if (!ctrl.isLookupWizard){
    		return false;
    	}
    	
		if(!ctrl.parentFieldName)
			return false;
		
    	if (!ctrl.parentFieldName || !this.ctrlsShowMap[ctrl.parentFieldName]){
    		ctrl.reload();
    		return false;
    	}
    	// get parent index
    	var parentInd = this.getLastAddedInd(ctrl.parentFieldName);
    	if (!this.ctrlsShowMap[ctrl.parentFieldName][parentInd]){
    		return false;
    	}
    	// get parent ctrl
		var parentCtrl = Runner.controls.ControlManager.getAt(this.tName, parentInd, ctrl.parentFieldName, this.ctrlsShowMap[ctrl.parentFieldName][parentInd][0]);
				
		// add link to child
		if (parentCtrl.showStatus){
			ctrl.setParentCtrl(parentCtrl);		
			// add to dependent array
			parentCtrl.addDependentCtrls([ctrl]);
			// reload all children
			if (triggerReload===true){
				parentCtrl.fireEvent('change');
			}		
		}else{
			ctrl.reload();
		}
		return true;		
    },
    
    getShownFilterNames: function(){
    	var fNamesArr = [];
    	
    	for(var fName in this.ctrlsShowMap){
    		var cachedInd = 0;
    		for(var ind in this.ctrlsShowMap[fName]){
				// need to convert to int from string. May be because object property name is string, typeof return string
				ind = parseInt(ind);
				
				if($("#"+this.getFilterDivId(fName, ind, this.srchWinShowStatus)).css('display') != 'none'){
					fNamesArr.push(fName);
				}
			} 
    	}
    	
    	return fNamesArr;
    },
    
    showCached: function(filterName){
    	// no cache
    	if (!this.ctrlsShowMap[filterName]){
    		return false;
    	}
    	// index of div, that cached and we need to show it
    	var cachedInd = 0;
		for(var ind in this.ctrlsShowMap[filterName]){
			// need to convert to int from string. May be because object property name is string, typeof return string
			ind = parseInt(ind);
			// get max index, it will give last cached
			cachedInd = cachedInd < ind ? ind : cachedInd;
		}      		
		
		// no cached ctrls, only already shown
		if($("#"+this.getFilterDivId(filterName, cachedInd, this.srchWinShowStatus)).css('display') != 'none'){
			return false;
		}
		
		// index of last cached ctrl for this field
    	var cachedCtrlIndArr = this.ctrlsShowMap[filterName][cachedInd];    
        //------------------------------------------------------------------------------------------
        // process controls
        var objIndForCM, parentFieldName, parentCtrl = null, parentInd = false, ctrl1;
    	// scan each object
		for(var i=0;i<cachedCtrlIndArr.length;i++){
        	// index of object that stored in CM
        	objIndForCM = cachedCtrlIndArr[i];
        	// get ctrl
        	var ctrlFromCache = Runner.controls.ControlManager.getAt(this.tName, cachedInd, filterName, objIndForCM);
        	// save link to first ctrl, at the end use it to set focus on it
        	if (i===0){
        		ctrl1 = ctrlFromCache;
        		// show ctrl
        		ctrl1.show();
        	}        	
        	// get parentFieldName for lookup ctrls and add dependeces to lookup ctrls
        	parentFieldName = ctrlFromCache.parentFieldName;
        	// set dependeces between child and parent if these links could be
    		this.setDependences(ctrlFromCache, true);
        	// clear javascript, to prevent it executing second time
        	ctrlFromCache.spanContElem.find('script').remove();
        }        
        //------------------------------------------------------------------------------------------
        // place ctrl depend on it's type: lookup or simple
        this.putCachedBlock(filterName, cachedInd, parentFieldName);        
        // show type combo, if it shown in others ctrl
        if (this.ctrlTypeComboStatus){
        	$("#"+this.getComboContId(filterName, cachedInd)).show();	
        }
        // set focus to added ctrl, turned off in window mode, because it cause bad visual effects in bottom control in window mode
        if (!this.srchWinShowStatus){
        	ctrl1.setFocus();
        }   
        return true;
    },
    /**
     * Adds filter to panel or window, and loads another one for cache
     * @param {string} filterName
     */
    addFilter: function(filterName) {
    	var isShown = this.showCached(filterName);  
    	if (!isShown){
    		var loadBox = this.createLoadingBox(filterName);
    		this.putLoadingBox(loadBox, filterName);
    	}else{
    		this.ctrlTypeComboStatus ? this.showCtrlTypeCombo() : this.hideCtrlTypeCombo();
    	}
        
        // ajax params
        var ajaxParams = {
            searchControllerId: this.id,
            rndval: Math.random(),
            mode: "inlineLoadCtrl",
            ctrlField: myEncode(filterName),
            id: flyid
        };
        flyid++;
        // create var for ajax handler closure
        var controller = this;
        // ajax query and callback func 
        $.getJSON(this.ajaxSearchUrl, ajaxParams, function(ctrlJSON, queryStatus){
        	// register new ctrl block        	
        	controller.addRegCtrlsBlock(filterName, ctrlJSON.divInd, (ctrlJSON.control2 ? [0, 1] : [0]), ctrlJSON);
        	if (!isShown){
        		controller.showCached(filterName);
        		$(loadBox).remove();
        		controller.toggleCrit(controller.getVisibleBlocksCount());	
        		// because ajax ctrl will shown with delay
        		controller.ctrlTypeComboStatus ? controller.showCtrlTypeCombo() : controller.hideCtrlTypeCombo();
        	}
        });
    },
    /**
     * Deletes controls, its objects add html from DOM
     * @param {string} fName
     * @param {int} ind
     */
    delCtrl: function(fName, ind){    	
    	var objIndForCM;

        // ureg ctrls, loop will delete also second ctrl, if it was created
		for(var i=0;i<this.ctrlsShowMap[fName][ind].length;i++){
        	// index of object that stored in CM
        	objIndForCM = this.ctrlsShowMap[fName][ind][i];
        	// for lookup ctrls, clear links from children and trigger reload them with all values
        	if (objIndForCM.isLookupWizard){
        		objIndForCM.clearChildrenLinks(true);
        	}
        	// delete each object
        	Runner.controls.ControlManager.unregister(this.tName, this.id, fName, objIndForCM);
        }        
        
        // remove element from dom
        this.removeComboById(this.getComboContId(fName, ind));
        this.removeFilterById(this.getFilterDivId(fName, ind));
        // set new window dimensions
        if (this.srchWinShowStatus){
        	this.recalcWindowDim();	
        }        
        // call crit controller
        this.toggleCrit(this.getVisibleBlocksCount());
        // remove from ctrl show map
        delete this.ctrlsShowMap[fName][ind];
    },
    /**
     * Deletes filter by id, removes from array and DOM element
     * @param {string} id
     */
    removeFilterById: function(id){
    	var isUseWinId = (id.lastIndexOf('_win') != -1);
    	id = (isUseWinId ? id.substr(0, id.lastIndexOf('_')) : id);
		// del from panel arr
    	var elemInd = this.srchFilterRowArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.srchFilterRowArr[elemInd].remove();       
    		this.srchFilterRowArr.splice(elemInd, 1);
    	}
    	
    	id += '_win';
    	// del from win arr
    	var elemInd = this.srchFilterRowWinArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.srchFilterRowWinArr[elemInd].remove();        
    		this.srchFilterRowWinArr.splice(elemInd, 1);
    	}
    	
    },
    /**
     * Deletes combo cont by id, removes from array and DOM element
     * @param {string} id
     */
    removeComboById: function(id){
    	var isUseWinId = (id.lastIndexOf('_win') != -1);
    	id = (isUseWinId ? id.substr(0, id.lastIndexOf('_')) : id);
		// del from panel arr
    	var elemInd = this.searchTypeCombosArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.searchTypeCombosArr.splice(elemInd, 1);
    	}
    	id += '_win';
    	// del from win arr
    	var elemInd = this.searchTypeCombosWinArr.getIndexOfElem(id, function(val, elem){
    		return elem.attr('id')==val;
    	});
    	if (elemInd !== -1){
    		this.searchTypeCombosWinArr.splice(elemInd, 1);
    	}
    },
    /**
     * Get number of visible ctrls blocks
     * @return {int}
     */
    getVisibleBlocksCount: function(){
    	var visCount = 0;
    	// use tr arr if window mode, or div arr if panel
    	var rowArr = (this.srchWinShowStatus ? this.srchFilterRowWinArr : this.srchFilterRowArr);
    	// loop through all filters to get which are visible
    	for(var i=0; i<rowArr.length; i++){    		
    		if (rowArr[i].css('display') != 'none'){
    			visCount++;
    		}
    	}
    	return visCount;
    },
     /**
     * Create and submit form 
     */
    submitSearch: function(){  
    	this.rememberPanelState();
    	
    	// clear any field contains search if it wasn't used
    	if (!this.usedSrch && !this.smplUsed){
    		this.smplSrchBox.val('');
    	}
    	// add fields thats appear only on list panel mode
    	this.addToForm(this.smplSrchBox.val(), 'ctlSearchFor');	
    	
    	// for simple search with combos
    	this.addToForm(this.simpleSrchFieldsCombo.val(), 'simpleSrchFieldsComboOpt');
    	
    	var simpleSrchTypeComboVal = this.simpleSrchTypeCombo.val();
    	if (simpleSrchTypeComboVal && simpleSrchTypeComboVal.indexOf('NOT') == 0){
			simpleSrchTypeComboVal = simpleSrchTypeComboVal.substr(4);
			this.addToForm('on', 'simpleSrchTypeComboNot');
		}else{
			this.addToForm('', 'simpleSrchTypeComboNot');
		}
    	this.addToForm(simpleSrchTypeComboVal, 'simpleSrchTypeComboOpt');
    	    	
    	Runner.search.SearchController.superclass.submitSearch.call(this);
    },
    /**
     * Resets form ctrls, for panel
     * @return {Boolean}
     */
    resetCtrls: function(){
    	var objIndForCM;
    	
    	for(var fName in this.ctrlsShowMap){
			for(var ind in this.ctrlsShowMap[fName]){
				for(var i=0;i<this.ctrlsShowMap[fName][ind].length;i++){
					// index of object that stored in CM
		        	objIndForCM = this.ctrlsShowMap[fName][ind][i];
		        	// delete each object
		        	var ctrl = Runner.controls.ControlManager.getAt(this.tName, this.id, fName, objIndForCM);
		        	ctrl.reset();
				}
			}
        }
		return false;
    },
    
    rememberPanelState: function(){
    	
    	var cutFrom = document.location['pathname'].lastIndexOf('/', 1);
		var cookieRoot = document.location['pathname'].substr(0,(cutFrom+1));
    	
		var panelStateObj = {srchPanelOpen: this.srchOptShowStatus, srchCtrlComboOpen: this.ctrlTypeComboStatus, srchWinOpen: this.srchWinShowStatus, openFilters: []};
		if (this.srchWinShowStatus){
			var win = $("#fly"+this.flyDivId);
			panelStateObj.winState = {x: win.css('left'), clientX: win.css('left'), clientY: win.css('top'), y: win.css('top'), h: win.css('height'), w: win.css('width')};			
		}
		
		if (!this.usedSrch){
			panelStateObj.openFilters = this.getShownFilterNames();
		}
		
		var panelStateString = JSON.stringify(panelStateObj);		
		set_cookie('panelState_'+this.shortTName+'_'+this.id, panelStateString, this.panelStateExpires, cookieRoot, '', '');
    },
    
    remindPanelState: function(){
    	var panelStateString = get_cookie('panelState_'+this.shortTName+'_'+this.id);
    	
    	if (!panelStateString){
    		if (this.panelSearchFields.length){
    			this.showSearchOptions();
    		}
    		return;
    	}
    	
    	var panelStateObj = JSON.parse(panelStateString);
    	
    	if (panelStateObj.srchWinOpen){
    		this.hideSearchOptions();
    		this.showSearchWin(panelStateObj.winState);
    	}else if(panelStateObj.srchPanelOpen){
    		this.showSearchOptions();
    	}
    	
    	if (panelStateObj.srchCtrlComboOpen){
    		this.showCtrlTypeCombo();
    	}else{
    		this.hideCtrlTypeCombo();
    	}
    	
    	if (!this.usedSrch){
    		// cut all quick search fields from array
    		for(var i=0;i<this.panelSearchFields.length;i++){
    			var elemIndex = panelStateObj.openFilters.getIndexOfElem(this.panelSearchFields[i]);
    			if (elemIndex != -1){
    				panelStateObj.openFilters.splice(elemIndex, 1);
    			}
	    	}
	    	// add fields
	    	for(var i=0;i<panelStateObj.openFilters.length;i++){	    		
	    		this.addFilter(panelStateObj.openFilters[i]);
	    	}
    	}
    },
    
    getFieldIds: function(fName){
    	var idsArr = [];
    	if (this.ctrlsShowMap[fName]){    		
    		for(var id in this.ctrlsShowMap[fName]){
    			idsArr.push(id);
    		}    		
    	}
		return idsArr;
    },
    getFieldControls: function(fName){
    	var ctrlsArr = [], ctrl = null, idsArr = this.getFieldIds(fName);
    	for(var i=0; i<idsArr.length; i++){
    		ctrl = Runner.controls.ControlManager.getAt(this.tName, idsArr[i], fName);
    		ctrlsArr.push(ctrl);
    	}
    	return ctrlsArr;
    },
    
    getSecondControl: function(fName, id){
    	return Runner.controls.ControlManager.getAt(this.tName, id, fName, 1);
    },
    
    getFieldOptions: function(fName){
    	var optsArr = [], opt = null, idsArr = this.getFieldIds(fName);
    	for(var i=0; i<idsArr.length; i++){
    		opt = $('#'+this.getComboId(fName, idsArr[i])).get(0);
    		optsArr.push(opt);
    	}
    	return optsArr;
    }
    
});
 
/**
 * Base abstract class for all controls, should not be created directly
 * @requires runner, ControlManager, validate, Event
 */
Runner.controls.Control = Runner.extend(Runner.Event, {
	/**
	 * Name of control
	 * @type string
	 */
 	fieldName: "",
 	/**
 	 * Name used for HTML tags, attrs
 	 * @type String
 	 */
 	goodFieldName: "",
 	/**
 	 * table name for urls request
 	 * @type String
 	 */
 	shortTableName: "",
	/**
	 * Control id
	 * @type string
	 */
	id: "",
	/**
	 * custom CSS classes
	 * @type string
	 */
	css: "",
	/**
	 * Custom css styles
	 * @type String
	 */
	style: "",
	/**
	 * Value DOM element id
	 * @type string
	 */
	valContId: "",
	/**
	 * Object, value DOM element
	 * @type {object}
	 */
	valueElem: null,	
	/**
	 * Span container element id
	 * @type {string}
	 */
	spanContId: "",
	/**
	 * Span jQuery object
	 * @type {object}
	 */
	spanContElem: null,
	/**
	 * Error container id
	 * @type {string}
	 */
	errContId: "",
	/**
	 * Error container, div
	 * @type {object}
	 */	
	errContainer: null,
	/**
	 * Array of validation types
	 * @type array of string
	 */
	validationArr: [],
	/**
	 * Value after initialization
	 */
	defaultValue: null,
	/**
	 * Is reset form happend or not
	 */
	isResetHappend: false,
	/**
	 * Source table
	 */
	table: "",
	/**
	 * Defined regExp with ,message, messageType, allowEmpty, regExp 
	 * @type {object}
	 */
	regExp: null,
	/**
	 * Type attr
	 * @type {string}
	 */
	inputType: "",
	/**
	 * Edit type of control, that used to process data on server
	 * Was created for search submit
	 * @type String
	 */
	ctrlType: "",
	/**
	 * Is editable elems shown
	 * @type {bool}
	 */
	showStatus: true,
	/**
	 * Number of control for the field. In advanced search page only 2 controls may appear for the field.
	 * But ControlManager can add any ammount of controls to the field 
	 * @type number
	 */
	ctrlInd: -1,
	/**
	 * Indicator, is focused element or not
	 * @type Boolean
	 */
	isSetFocus: false,
	/**
	 * Hidden property
	 * @type Boolean
	 */
	hidden: false,
	/**
	 * Mode of using control add|adit|search
	 * @type String
	 */
	mode: '',
	/**
	 * Indicator, true if control was marked as invalid.
	 * Usefull for password matching and validation etc.
	 * @type Boolean
	 */
	isInvalid: false,
	/**
	 * Class constructor
	 * @constructor
	 * @extends Runner.emptyFn
	 * @param {Mixed} cfg
	 * @param {string} cfg.fieldName
	 * @param {string} cfg.id
	 * @param {array} cfg.validationArr
	 * @param {object} cfg.regExp
	 */	
	constructor: function(cfg) {		
		this.validationArr = new Array();	
		// copy properties from cfg to controller obj
        Runner.apply(this, cfg);
		//call parent
		Runner.controls.Control.superclass.constructor.call(this, cfg);	
		// value element id
		this.valContId = "value"+(cfg.ctrlInd || "")+"_"+this.goodFieldName+"_"+this.id;
		// value elem
		this.valueElem = (this.valueElem == null) ? $("#"+this.valContId) : this.valueElem;	
		// span container id
		this.spanContId = "edit"+this.id+"_"+this.goodFieldName+"_"+cfg.ctrlInd;
		// add span elem
		this.spanContElem = $("#"+this.spanContId);
		// error DOM element id
		this.errContId = "errorCont"+cfg.ctrlInd+"_"+this.valContId;
		// initialize control disabled
		if (cfg.disabled===true || cfg.disabled==="true"){
			this.setDisabled();
		}
		// initialize control hidden
		if (cfg.hidden===true || cfg.hidden==="true"){
			this.hide();
		}
		// there we can also apply custom css classes		
		this.ccs ? valueElem.addclass(this.css) : '' ;
		// there we can also apply custom css styles
		this.addStyle(this.style);		
		// get default value
		this.defaultValue = this.getValue();
		// add input type attr, if it exist
		if (this.valueElem.attr && this.valueElem.attr("type")){
			this.inputType = this.valueElem.attr("type");
		}		
		// need for use focus indicator
		this.addEvent(["click"]);
		// if not passed stop event init param
		if (cfg.stopEventInit!==true) {			
			//event elem
			this.elemsForEvent = [this.valueElem.get(0)];
			//adding events
			this.addEvent(["blur"]);
			// init events
			this.init();
		}
		// register new control in manager
		Runner.controls.ControlManager.register(this);
		// register in validator for custom user validation functions loading
		validation.registerCustomValidation(this);
		//console.log(Runner.controls.ControlManager.getAt(this.table, this.id, this.fieldName), "from CM");		
	},
	/**
	 * Add styles to value element
	 * @param {string} styleToAdd
	 * @return {Boolean} true in success, otherwise false
	 */
	addStyle: function(styleToAdd){
		if (!styleToAdd){
			return false;
		}
		
		var stylesArr = styleToAdd.split(';');
		
		for(var i=0; i<stylesArr.length; i++){			
			var style = stylesArr[i].split(":");
			style[0] = style[0].toString().trim();
			if (!style[0]){
				continue;
			}
			style[1] = style[1].toString().trim();
			this.valueElem.css(style[0], style[1]);
		}
		
		
		/*// style that was on element
		var oldStyle = this.valueElem.attr('style');
		// new style, with added
		var newStyle = (oldStyle ? oldStyle + ' ' : '') + styleToAdd;
		// set new style
		this.valueElem.attr('style', newStyle);	*/
		return true;
	},
	/**
	 * Validates control against validation types, defined in validationArr
	 * @method validate
	 * @params valArr - array of validation for event blur only
	 * @return {object} if success true, otherwise false	 
	 */
	validate:function(valArr){
		var vRes = validation.validate(valArr || this.validationArr, this);		
		// change invalid status only if any validation were made, to prevent init error container
		if (valArr || this.validationArr.length){
			if (!vRes.result){
				this.markInvalid(vRes.messageArr);
			}else{
				this.clearInvalid();
			}
		}
		// return validation result
		return vRes;		
	},
	/**
	 * removes validation from control. 
	 * @param {string} vType
	 * @return {bool} If success true, false otherwise
	 */
	removeValidation: function(vType){
		if (typeof vType != "string"){
			this.regExp = null;
			vType = "RegExp";
		}
		
		for(var i=0;i<this.validationArr.length;i++){
			if (this.validationArr[i] == vType){
				this.validationArr.splice(i,1);
				return true;
			}
		}
		return false;
	},
	/**
	 * Adds validation to control
	 * @param {string} vType
	 */
	addValidation: function (vType){
		if (typeof vType != "string"){
			this.regExp = vType;
			vType = "RegExp";
		}		
		if (!this.isSetValidation(vType)){
			this.validationArr[this.validationArr.length] = vType;
		}
	},
	/**
	 * Checks if validation added
	 * private
	 * @param {string} vType
	 * @return {bool} If success true, false otherwise
	 */
	isSetValidation: function (vType){
		// checks if this vType defined
		if (!validation[vType]){
			return false;
		}
		for(var i=0;i<this.validationArr.length;i++){
			if (this.validationArr[i] == vType){				
				return true;
			}
		}
		return false;
	},
	/**
	 * Validates control value against vType validation
	 * @param {string} vType
	 * @return {mixed}
	 */
	validateAs: function(vType){
		return validation[vType](this.getValue());
	},
	/**
	 * Helper func for lazy init error container
	 * @private
	 */
	initErrorCont: function(){
		// create error container
		this.errContainer = document.createElement('div');		
		this.errContainer = $(this.errContainer);
		this.errContainer.attr('id', this.errContId);
		this.errContainer.addClass('errorText');
		this.errContainer.css('display', "none");			
		this.errContainer.appendTo(this.spanContElem);	
		this.initErrorCont = Runner.emptyFn;
	},
	
	/**
	 * Sets error messages after validation
	 * @param {array} messArr
	 */
	markInvalid: function(messArr){
		this.initErrorCont();
		this.markInvalid = function(messArr){
			var divInnerHtml = "";
			this.errContainer.show();
			for(var i=0;i<messArr.length;i++){
				divInnerHtml += messArr[i]+"</br>";
			}
			// add message to container
			this.errContainer.html(divInnerHtml);
			// set invalid indicator
			this.isInvalid = true;
		}
		this.markInvalid(messArr);
	},
	/**
	 * Clears invalid state
	 * @method
	 */
	clearInvalid: function(){	
		this.initErrorCont();
		this.clearInvalid = function(){
			this.errContainer.hide();
			this.errContainer.empty();
			// set invalid indicator
			this.isInvalid = false;
		}
		this.clearInvalid();
	},	
	/**
	 * Return invalid state of control
	 * @return {bool}
	 */
	invalid: function(){
		return this.isInvalid;
	},
	
	/**
	 * sets default value to control
	 * return true if success. otherwise false
	 * @method
	 */
	reset: function()
	{
		this.isResetHappend = true;
		this.setValue(this.defaultValue);
		this.clearInvalid();		
		this.isResetHappend = false;
		return true;
	},
	/**
	 * Sets empty value to control
	 * return true if success. otherwise false
	 * @method
	 * @return bool
	 */
	clear: function(){
		this.setValue('');
		this.clearInvalid();		
		return true;
	},
	
	/**
	 * Hide control - set display attr none
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	hide: function(){
		this.spanContElem.css("display", "none");
		this.showStatus = false;
	},
	
	/**
	 * Show control - set display attr block
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	show: function(){
		this.spanContElem.css("display", "");
		this.showStatus = true;
	},	
	/**
	 * Toggle show/hide status
	 */
	toggleHide: function(){
		if (this.showStatus){
			this.hide();
		}else{
			this.show();
		}
	},
	/**
	 * Get value from value element. 
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	getValue: function(){		
		if (this.valueElem.val){
			return this.valueElem.val();
		}else{
			return false;
		}
		//return this.valueElem.val();
	},
	
	/**
	 * Return value as string
	 * @return {string}
	 */
	getStringValue: function(){
		return this.getValue();
	},
	
	/**
	 * Sets value to value DOM elem
	 * Should be overriden for sophisticated controls
	 * @method
	 * @param {mixed} val
	 */
	setValue: function(val, triggerEvent){
		if (this.valueElem.val){			
			this.valueElem.val(val);
			// trigger event
			//if(triggerEvent===true){
			if(triggerEvent===true){
				//console.log(triggerEvent, 'triggerEvent on change cb called');
				this.fireEvent("change");
			}
		}else{
			return false;
		}
	},
	
	
	/**
	 * Sets disable attr true
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	setDisabled: function(){
		if (this.valueElem.get(0)){
			this.valueElem.get(0).disabled = true;
			return true;
		}else{
			return false;
		}			
	},
	
	/**
	 * Sets disaqble attr false
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	setEnabled: function(){
		if (this.valueElem.get(0)){
			this.valueElem.get(0).disabled = false;
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Returns input tag type attribute.
	 * @method
	 * @return {string}
	 */
	getControlType: function(){
		return this.inputType;
	},	
	/**
	 * Sets focus to the element
	 * @method
	 * @return {bool}
	 */
	setFocus: function(triggerEvent){
		
		var cType = this.getControlType();
		if (cType != "" && (cType == 'text' || cType == 'password' || cType == 'file' || cType=='textarea')){
			// can't set focus on disabled element. This may cause IE error
			if (this.valueElem.get(0).disabled == true || !this.showStatus || this.valueElem.css('display') == 'none' || this.valueElem.css('visibility') == 'hidden'){
				return false;
			}
			try{
			  this.valueElem.get(0).focus();
			}catch(err){
				// just for prevent error in IE :)
			}
			
			// trigger event
			if(triggerEvent===true){
				//console.log(triggerEvent, 'triggerEvent on focus ctrl called');
				this.fireEvent("focus");
			}
			this.isSetFocus = true;
			return true;
		}else{
			this.isSetFocus = false;
			return false;
		}
	},
	/**
	 * Checks if control value is empty. Used for isRequired validation
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		return this.getValue().toString()=="";
	},
	
	/**
	 * Custom function for onblur event
	 * @param {Object} e
	 */
	"blur": function(e){
		this.stopEvent(e);		
		this.isSetFocus = false;
		/*var len = this.validationArr.length;
		if(this.validationArr[len-1] == 'IsRequired'){
			var valArr = this.validationArr.slice(0,len-1)
		}else{ 
			var valArr = this.validationArr;
		}
		return this.validate(valArr);	*/	
		return this.validate();
	},
	/**
	 * Sets focus indicator true when click on elem
	 * @param {event} e
	 */
	"click": function(e){		
		this.isSetFocus = true;
	},
	/**
	 * Removes css class to value element
	 * @param {string} className
	 */
	removeCSS: function(className){
		this.valueElem.removeClass(className);
	},
	/**
	 * Adds css class to value element
	 * @param {string} className
	 */
	addCSS: function(className){
		this.valueElem.addClass(className);
	},
	/**
	 * Returns specified attribute from value element
	 * @param {string} attrName
	 */
	getAttr: function(attrName){
		return this.valueElem.attr(attrName);
	},
	/**
	 * Return element that used as display.
	 * Usefull for suggest div positioning
	 * @return {object}
	 */
	getDispElem: function(){
		return this.valueElem;
	},
	/**
	 * Clone html for iframe submit
	 * @return {array}
	 */
	getForSubmit: function(){
		return [this.valueElem.clone().val(this.valueElem.val())];
	}
	
});



 
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
		return [this.valueElem.clone().val(this.getValue())]
	}
});



 
/**
 * Class for text fields control
 */
Runner.controls.TextField = Runner.extend(Runner.controls.Control, {
	constructor: function(cfg){
		this.addEvent(["change", "keyup"]);		
		Runner.controls.TextField.superclass.constructor.call(this, cfg);		
	}	
});


 
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
		if(this.useRTE == "INNOVA")
			this.innerIframeId = 'idContentoEdit'+this.goodFieldName+'_'+this.id;
	},
	
	getValue: function()
	{	
		var val;
		if(this.iframeElem)
		{	
			if(this.useRTE=='RTE')
				val = this.iframeElem.contents().find('#'+this.iframeElemId).contents().find('body').html();
			else
				val = this.iframeElem.contents().find('#'+this.innerIframeId).contents().find('body').html();
			if(val)
				val=val.trim();
			if(val=='<br>')
				val='';
			return val;
		}
		else 
			return false;
	},
		
	setValue: function(val)
	{
		if(this.useRTE=='RTE')
			this.iframeElem.contents().find('#'+this.iframeElemId).contents().find('body').html(val);
		else
			this.iframeElem.contents().find('#'+this.innerIframeId).contents().find('body').html(val);
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
	/**
	 * Override parent contructor
	 * @constructor
	 * @param {Object} cfg
	 */
	constructor: function(cfg){
		//call parent
		Runner.controls.ImageField.superclass.constructor.call(this, cfg);	
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
		var valWithStamp = "", fileName = "";
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
		// call change handler if needed
		//if (triggerEvent===null || triggerEvent===true){
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
/**
 * Abstract base class for date fields, should not created directly
 */
Runner.controls.DateField = Runner.extend(Runner.controls.Control, {
	/**
	 * Id of hidden elem, which used by datepicker
	 * @type {string} 
	 */
	datePickerHiddId: "",
	/**
	 * Hidden elem, which used by datepicker
	 * ts element
	 * @type {element} 
	 */
	datePickerHiddElem: null,
	/**
	 * Image and link of datepicker
	 * link element
	 * @type {element} 
	 */
	imgCal: null,
	/**
	 * Indicates used datepicker with control or not
	 * @type {bool} cfg
	 */
	useDatePicker: false,
	/**
	 * Id of date format hidden element, which used on serverside
	 * @type {string}
	 */
	dateFormatHiddId: "",
	/**
	 * Indicates date format with control or not
	 * @type {bool} cfg
	 */
	dateFormat: "",
	/**
	 * Indicates show time with control or not
	 * @type {bool} cfg
	 */
	showTime: false,
	/**
	 * jQuery object of date format hidden element, which used on serverside
	 * @type {Object} 
	 */
	dateFormatHiddElem: null,
	/**
	 * Overrides parent constructor
	 * @param {Object} cfg
	 * @param {bool} cfg.useDatePicker
	 */
	constructor: function(cfg){
		// call parent
		Runner.controls.DateField.superclass.constructor.call(this, cfg);
		// add hidden field for datepicker usege
		this.useDatePicker = cfg.useDatePicker ? cfg.useDatePicker : false;
		
		this.dateFormat = typeof cfg.dateFormat != "undefined" ? cfg.dateFormat : window.locale_dateformat;
		
		this.showTime = cfg.showTime ? cfg.showTime : false;
		
		if (this.useDatePicker){
			this.datePickerHiddId = "tsvalue"+(cfg.ctrlInd || "")+"_"+this.goodFieldName+"_"+this.id;
			this.datePickerHiddElem = $("#"+this.datePickerHiddId);			
		}
		// add hidden field for date format on serverside
		this.dateFormatHiddId = "type"+(cfg.ctrlInd || "")+"_"+this.goodFieldName+"_"+this.id;
		this.dateFormatHiddElem = $("#"+this.dateFormatHiddId);
		if(this.useDatePicker){
			this.imgCal = $('#imgCal_'+this.valContId);
		}
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
		this.superclass.addValidation.call(this, type);
	}
	
	
	
});
/**
 * Class for date fields with textField value editor
 * If there is datePicker, instance of Runner.controls.DateTextField should be passed as target
 */
Runner.controls.DateTextField = Runner.extend(Runner.controls.DateField, {
			
	/**
	 * Overrides parent constructor
	 * @param {Object} cfg
	 */
	constructor: function(cfg){
		this.addEvent(["change", "keyup"]);		
		Runner.controls.DateTextField.superclass.constructor.call(this, cfg);	
		// initialize control disabled
		if (cfg.disabled===true || cfg.disabled==="true"){
			this.setDisabled();
		}
	},
	getValue: function(){
		var parsedTime = parse_datetime(this.valueElem.val(),this.dateFormat);		
		if (parsedTime == null){
			return "";
		}else{
			return parsedTime;
		}		
	},
	/**
	 * Set value, also change value in hidden field
	 * @method
	 * @param {Object} val
	 * @return {bool} if passed correct Date object, otherwise false
	 */
	setValue: function(newDate, triggerEvent)	{
		// if we pass Date object, so we use it
		if (typeof newDate == 'object'&&newDate!=null){
			// call old date parse function, they will change in future
			var dt = print_datetime(newDate,this.dateFormat,this.showTime);
			//set value in edit textfield
			this.valueElem.val(dt);
			// if we need to set new date in hidden fields for datepicker
			if (this.useDatePicker){
				dt = print_datetime(newDate,-1,this.showTime);
				this.datePickerHiddElem.val(dt);
			}
			this.validate();
			return true;
		}else{
			// set empty value = ""
			this.valueElem.val("");
			// if we need to set new date in hidden fields for datepicker
			if (this.useDatePicker){				
				this.datePickerHiddElem.val("");
			}
			this.validate();
			return false;
		}		
		if(triggerEvent===true){
			this.fireEvent("change");
		}	
	},
	/**
	 * Custom function for onblur event
	 * @param {Object} e
	 */
	"blur": function(e)
	{
		// call parent
		this.stopEvent(e);
		// set values to hidden fields
		var vRes = this.validate();
		if (vRes.result && this.useDatePicker  && this.getValue())
			this.setValue(this.getValue());
	},
	/**
	 * Sets disable attr true
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	setDisabled: function()
	{
		if (this.valueElem.get(0) && this.imgCal)
		{
			this.valueElem.get(0).disabled = true;
			this.imgCal.css('visibility','hidden');
			return true;
		}else{
			return false;
		}			
	},
	/**
	 * Sets disaqble attr false
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	setEnabled: function()
	{
		if (this.valueElem.get(0))
		{
			this.valueElem.get(0).disabled = false;
			if(this.imgCal!=null)
				this.imgCal.css('visibility','visible');
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Clone html for iframe submit
	 */
	getForSubmit: function(){
		return [this.valueElem.clone(), this.dateFormatHiddElem.clone()];
	},
	/**
	 * Return date value as string
	 * @return {string}
	 */
	getStringValue: function(){
		var dateObj = this.getValue();
		if (dateObj===""){
			return "";
		}else{
			return dateObj.getFullYear()+'-'+(dateObj.getMonth()+1)+'-'+dateObj.getDate()+' '+dateObj.getHours()+':'+dateObj.getMinutes()+':'+dateObj.getSeconds();
		}
	}	
});

/**
 * Class for date fields with three dropdowns value editor
 * If there is datePicker, instance of Runner.controls.DateDropDown should be passed as target
 */
Runner.controls.DateDropDown = Runner.extend(Runner.controls.DateField, {

	/**
	 * Hidden element for date value
	 * value for server submit
	 * @type {Object} type
	 */
	hiddValueElem: null,
	/**
	 * Hidden element id
	 * @type {string}
	 */
	hiddElemId: "",
	
	/**
	 * Overrides parent constructor
	 * @param {Mixed} cfg
	 */
	constructor: function(cfg){	
		cfg.stopEventInit=true;		
		// call parent
		Runner.controls.DateDropDown.superclass.constructor.call(this, cfg);
		//Overrides value elem. For handling 3 dropdowns
		this.valueElem = {
			"day": $("#day"+this.valContId),
			"month": $("#month"+this.valContId),
			"year": $("#year"+this.valContId)		
		};

		// initialize control disabled
		if (cfg.disabled===true || cfg.disabled==="true"){
			this.setDisabled();
		}
		// get default value
		this.defaultValue = this.getValue();
		// use onchange instead onblur for DD
		this.addEvent(["change"]);
		// onblur not usable
		this.killEvent("blur");
		//event elems 
		this.elemsForEvent = [this.valueElem["day"].get(0), this.valueElem["month"].get(0), this.valueElem["year"].get(0)];		
		// init events handling
		this.init();		
		// add hidden elems
		this.hiddElemId = this.valContId;//"value_"+this.goodFieldName+"_"+this.id;
		this.hiddValueElem = $("#"+this.hiddElemId);
		// add input type attr
		this.inputType = "3dd";
		// if allready have constants, than fill combos
		if (window.TEXT_MONTH_JAN){
			this.addYearOptions(cfg.yearVal);
			this.addMonthOptions(cfg.monthVal);
			this.addDayOptions(cfg.dayVal);
		}
	},	
	
	destructor: function(){
		this.valueElem['day'].remove();
		this.valueElem['month'].remove();
		this.valueElem['year'].remove();
	},
	/**
	 * Add year options
	 * @param {integer} year value
	 */
	addYearOptions: function(selectedYear)	{	
		this.valueElem["year"].html('');

		var dt = new Date();
		var currentYear = dt.getFullYear();		
		var startYear = currentYear-100;
		var endYear = currentYear+10;
		var opt = document.createElement('OPTION');
		$(opt).val('').html('');
		this.valueElem["year"].append(opt);
		
		for(var i = startYear; i<=endYear; i++){
			var opt = document.createElement('OPTION');
			$(opt).val(i).html(i);			
			if (i==selectedYear){
				opt.selected = true;
			}
			this.valueElem["year"].append(opt);
		};
		this.addYearOptions = Runner.emptyFn;
	},
	/**
	 * Add month options
	 * @param {integer} month value
	 */
	addMonthOptions: function(selectedMonth){
		this.valueElem["month"].html('');
		
		var opt = document.createElement('OPTION');
		$(opt).val('').html('');
		this.valueElem["month"].append(opt);
		
		var monthNames = [];		
		monthNames[1] = window.TEXT_MONTH_JAN;
		monthNames[2] = window.TEXT_MONTH_FEB;
		monthNames[3] = window.TEXT_MONTH_MAR;
		monthNames[4] = window.TEXT_MONTH_APR;
		monthNames[5] = window.TEXT_MONTH_MAY;
		monthNames[6] = window.TEXT_MONTH_JUN;
		monthNames[7] = window.TEXT_MONTH_JUL;
		monthNames[8] = window.TEXT_MONTH_AUG;
		monthNames[9] = window.TEXT_MONTH_SEP;
		monthNames[10] = window.TEXT_MONTH_OCT;
		monthNames[11] = window.TEXT_MONTH_NOV;
		monthNames[12] = window.TEXT_MONTH_DEC;
		
		for(var i=1; i<monthNames.length; i++){
			var opt = document.createElement('OPTION');
			$(opt).val(i).html(monthNames[i]);
			if (i==selectedMonth){
				opt.selected = true;
			}
			this.valueElem["month"].append(opt);
		}
		this.valueElem["month"].css('width','90px');	
		
		this.addMonthOptions = Runner.emptyFn;
	},
	/**
	 * Add day options
	 * @param {integer} day value
	 */
	addDayOptions: function(selectedDay){		
		this.valueElem["day"].html('');
		var opt = document.createElement('OPTION');
		$(opt).val('').html('');
		this.valueElem["day"].append(opt);
		
		for(var i=1; i<=31; i++){
			var opt = document.createElement('OPTION');
			$(opt).val(i).html(i);
			if (i==selectedDay){
				opt.selected = true;
			}
			this.valueElem["day"].append(opt);
		};
		this.addDayOptions = Runner.emptyFn;
	},
	
	/**
	 * Custom function for onchange event
	 * @param {Object} e
	 */
	"change": function(e){		
		this.stopEvent(e);
		// if any dd is empty, than we can't start validation
		for(var name in this.valueElem){
			if (this.valueElem[name].val() == '')
			{
				this.setValue();
				return true;
			}
		}		
		var vRes = this.validate();		
		if (vRes.result){
			this.setValue(this.getValue());
		}
		return vRes;
	},			
	"blur": Runner.emptyFn,	
	/**
	 * Gets values from dropdowns and returns it in YYYY-mm-dd-hh-ss format
	 */		
	getValue: function()
	{		
		// date pieces from dropdowns
		if (this.valueElem["day"] && this.valueElem["day"].val()){
			var dayVal = this.valueElem["day"].val();
		}else{
			return false;
		}
		if (this.valueElem["month"] && this.valueElem["month"].val()){
			var monthVal = this.valueElem["month"].val();
		}else{
			return false;
		}
		if (this.valueElem["year"] && this.valueElem["year"].val()){
			var yearVal = this.valueElem["year"].val();
		}else{
			return false;
		}
		
		var date = new Date(yearVal, monthVal-1, dayVal, 00, 00, 00);
		return date;
	},
	/**
	 * Sets value to dropdowns
	 * @param {Date} newDate
	 * @return {bool}Returns true if success, otherwise false
	 */
	setValue: function(newDate, triggerEvent)	
	{
		// if we pass Date object, so we use it
		if(typeof newDate == 'object' && newDate!=null)
		{
			this.hiddValueElem.get(0).value =  newDate.getFullYear() + '-' + (newDate.getMonth()+1) + '-' + newDate.getDate();		
			
			this.valueElem["day"].get(0).selectedIndex = newDate.getDate();
			
			this.valueElem["month"].get(0).selectedIndex = newDate.getMonth()+1;
			
			for(var i=0; i<this.valueElem["year"].get(0).options.length;i++)
			{
				if(this.valueElem["year"].get(0).options[i].value==newDate.getFullYear())
				{
					this.valueElem["year"].get(0).selectedIndex=i;
					break;
				}
			}
			if(this.useDatePicker)
				this.datePickerHiddElem.get(0).value = newDate.getDate() + '-' + (newDate.getMonth()+1) + '-' + newDate.getFullYear();
			return true;
		}else{
			this.hiddValueElem.val('');
			if(this.isResetHappend)
			{
				this.valueElem["day"].get(0).selectedIndex = 0;
				this.valueElem["month"].get(0).selectedIndex = 0;
				this.valueElem["year"].get(0).selectedIndex = 0;
			}
			// if we need to set new date in hidden fields for datepicker
			if (this.useDatePicker){				
				this.datePickerHiddElem.val("");
			}
			//this.validate();
			return false;
		}
		if(triggerEvent===true){
			this.fireEvent("change");
		}	
	},
	/**
	 * Overrides parent function for three element control
	 */
	setDisabled: function()
	{		
		if (!this.valueElem["day"] || !this.valueElem["month"] || !this.valueElem["year"] || !this.imgCal){
			return false;
		}
		
		this.valueElem["day"][0].disabled = true;
		this.valueElem["month"][0].disabled = true;
		this.valueElem["year"][0].disabled = true;		
		this.imgCal.css('visibility','hidden');
		return true;
	},
	/**
	 * Overrides parent function for three element control
	 */
	setEnabled: function()
	{
		this.valueElem["day"][0].disabled = false;
		this.valueElem["month"][0].disabled = false;
		this.valueElem["year"][0].disabled = false;
		if(this.imgCal!=null)	
			this.imgCal.css('visibility','visible');
		return true;
	},	
	/**
	 * Clone html for iframe submit
	 * @method
	 */
	getForSubmit: function(){
		return [this.hiddValueElem.clone(), this.dateFormatHiddElem.clone()];
	},	
	/**
	 * Sets focus to the element, override
	 * @method
	 * @param bool
	 * @return {bool}
	 */
	setFocus: function(triggerEvent){
		if (this.valueElem["day"].get(0).disabled != true){
			// set focus to first dropdown
			this.valueElem["day"].get(0).focus();
			if(triggerEvent===true){
				this.fireEvent("focus");
			}
			this.isSetFocus = true;
			return true;
		}else{
			this.isSetFocus = false;
			return false;
		}
	},
	/**
	 * Checks if control value is empty. 
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		if (this.valueElem["day"].val() == "" || this.valueElem["month"].val() == "" || this.valueElem["year"].val() == ""){
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Return date value as string
	 * @return {string}
	 */
	getStringValue: function(){
		return this.hiddValueElem.val();
	}
});


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
/**
 * Abstract base class for LookupWizard fields, should not created directly.
 * Contains common functionality for dependent lookup wizard controls
 * @class 
 * @requires Runner.controls.Control
 */
Runner.controls.LookupWizard = Runner.extend(Runner.controls.Control, {
	/**
	 * Lookup wizard indicator
	 * @type Boolean
	 */
	isLookupWizard: true,
	/**
	 * Array dropDownControls which are dependent to this ctrl
	 * @type 
	 */
	dependentCtrls: [],
	/**
	 * Parent ctrl object. Used to get values in lookupSuggest
	 * @type {object}
	 */
	parentCtrl: null,
	/**
	 * Name of parent field
	 * @type String
	 */
	parentFieldName: '',
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */
	constructor: function(cfg){
		// stop event init
		cfg.stopEventInit=true;		
		// recreate object
		this.dependentCtrls = new Array();
		//call parent
		Runner.controls.LookupWizard.superclass.constructor.call(this, cfg);
		//link for add new record or not
		this.addNew = $("#addnew_"+this.valContId);	
		// add change event for reload dependences
		this.addEvent(["change"]);
	},
	/**
	 * Method that called just before ControlManager deleted link on this object
	 */
	destructor: function(){
		// may be need to clear each array element
		delete this.dependentCtrls;
	},
	/**
	 * Add dependent controls to array of controls
	 * @method 
	 * @param {array} ctrlDD array of control objects
	 */
	addDependentCtrls: function(ctrlsArr){
		for(var i=0;i<ctrlsArr.length;i++){
			this.dependentCtrls.push(ctrlsArr[i]);
		}
	},
	/**
	 * Clear links from children to there's parent ctrl	 * 
	 * @param {bool} triggerReload pass true to call reload function on children
	 */
	clearChildrenLinks: function(triggerReload){
		// reload all children
		for(var i=0;i<this.dependentCtrls.length;i++){
			// if children exists
			if(this.dependentCtrls[i]){
				this.dependentCtrls[i].clearParent(triggerReload);
			}			
		}
	},
	/**
	 * Deletes link to parent ctrl, and optionaly reloads this
	 * @param {bool} triggerReload pass true to call reload method
	 */
	clearParent: function(triggerReload){
		this.parentCtrl = null;
		if (triggerReload===true){
			this.reload();
		}
	},
	/**
	 * Set parent ctrl property
	 * @param {object} ctrl
	 */
	setParentCtrl: function(ctrl){
		this.parentCtrl = ctrl;
	},
	/**
	 * Call reload method of each dependent DD
	 * @method
	 */
	reloadDependeces: function(){
		// value of parent ctrl
		var masterCtrlVal = this.getValue();
		// if parent ctrl returns array value, we need to pass only first element of array
		masterCtrlVal = typeof(masterCtrlVal) == 'object' ? masterCtrlVal[0] : masterCtrlVal;
		// reload all children
		for(var i=0;i<this.dependentCtrls.length;i++){
			// if children exists
			if(this.dependentCtrls[i]){
				this.dependentCtrls[i].reload(masterCtrlVal);
			}			
		}
	},
	
	clearInvalidOnDependences: function(){
		for(var i=0;i<this.dependentCtrls.length;i++){
			// if children exists
			if(this.dependentCtrls[i]){
				this.dependentCtrls[i].clearInvalid();
			}			
		}
	},
	
	/**
	 * Parse string to array. Used for parse preload and reload params
	 * @param {string} txt
	 * @return {array}
	 */
	parseContentToValues: function(txt){
		if (txt.length==0){
			return false;
		}		
		return txt.split('\n');
	},	
	/**
	 * Override simple dropDown event,
	 * add reloading for dependent dropDowns
	 * @event
	 * @param {event} e
	 */
	"change": function(e){	
		// clear invalid state in dependent controls in anyway
		this.clearInvalidOnDependences();
		//call parent
		var vRes = this.validate();
		// call reload if value pass validation
		if (vRes.result){
			this.reloadDependeces();
			return true;
		}else{
			return false;
		}
	}
});

/**
 * Select control class. 
 * @requires Runner.controls.LookupWizard
 */
Runner.controls.DropDownLookup = Runner.extend(Runner.controls.LookupWizard, {
	/**
	 * Number of values to select.
	 * @type {Number}
	 */
	multiSel: 1,
	/**
	 * DropDown DOM options array
	 * @type {array}
	 */
	optionsDOM: [],
	/**
	 * Override parent contructor 
	 * @param {object} cfg
	 * @param {int} cfg.multiSelect number of values to select. Must be >= 1
	 */
	constructor: function(cfg){
		// add multiSelect property
		this.multiSel = cfg.multiSel ? cfg.multiSel : 1;		
		// call parent
		Runner.controls.DropDownLookup.superclass.constructor.call(this, cfg);			
		// set input type
		this.inputType = "select";
		//set defaultValue
		this.defaultValue = this.getValue(true);
		//event elem 
		this.elemsForEvent = [this.valueElem.get(0)];		
		// init events handling
		this.init();	
		// add options array property
		this.optionsDOM = this.valueElem.get(0).options;
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}
		
	},
	/**
	 * Sets value to DropDown. Tries to set all values from array if multiselect control
	 * @param {array} val
	 * @return {bool} true if success otherwise false
	 */
	setValue: function(vals, triggerEvent)
	{
		// number of choosen options
		var choosen = 0;
		for(var i=0; i<this.valueElem.get(0).options.length;i++){
			for(var j=0;j<vals.length;j++){
				if(this.valueElem.get(0).options[i].value==vals[j]){
					this.optionsDOM[i].selected=true;
					choosen++;
					if(this.multiSel==1){
						this.valueElem.get(0).selectedIndex=i;
					}else{						
						break;
					}										
				}else{
					this.optionsDOM[i].selected=false;
				}// eo if
			}// eo for	
			if (choosen>0 && this.multiSel==1){
				break;
			}
		}// eo for
		
		if(triggerEvent===true){
			//console.log(triggerEvent, 'triggerEvent on change in DD called')
			this.fireEvent("change");
		}
		
		// if selected all than success
		if (choosen == vals.length && choosen <= this.multiSel){
			return true;
		}else{
			return false;	
		}		
	},
	/**
	 * Returns values from dropDown. 
	 * @method
	 * @return {array}
	 */
	getValue: function(returnArray)
	{
		var selVals = [];
		// loop for all options
		if(this.optionsDOM.length)
		{
			for (var i=0; i<this.optionsDOM.length;i++)
			{
				if (this.optionsDOM[i].selected)
					selVals.push(this.optionsDOM[i].value)
			}
		}
		else{
				for(var i=0; i<this.valueElem.get(0).options.length;i++)
				{
					if(this.valueElem.get(0).options[i].selected)
							selVals.push(this.valueElem.get(0).options[i].value);
							
				}
			}
		if(returnArray===true)
			return selVals;
		if(selVals.length>1)
			return selVals;
		else if(selVals.length==1)
			return selVals[0];
		else
			return "";
	},
	/**
	 * Checks if control value is empty. Used for isRequired validation
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		var selVals = this.getValue();
		if(typeof selVals!='object')
			return selVals==="";
		return false;
	},
	/**
	 * Deletes all options from ctrl
	 * @method
	 */
	clearOptions: function(){
		//this.valueElem.get(0).innerHTML = "";
		this.valueElem.empty();
	},
	/**
	 * Adds option to select
	 * may be need to add options to specified index?
	 * @param {string} text
	 * @param {string} val
	 */
	addOption: function(text, val){
		this.optionsDOM[this.optionsDOM.length]= new Option(unescape(text),unescape(val));
	},
	/**
	 * Add options from array.
	 * Array must have such structure:
	 * array[0] = value, array[1] = text,
	 * array[2] = value, array[3] = text,
	 * 2*i - indexes of values; 2*i+1 - indexes of text. I starts from 0   
	 * @param {array} optionsArr
	 */
	addOptionsArr: function(optionsArr){		
		for(var i=0; i < optionsArr.length - 1; i=i+2){ 
			this.addOption(optionsArr[i+1], optionsArr[i]);
		}
	},	
	/**
	 * First loading, without ajax. Should be called directly
	 * @param {string} txt unparsed values for options
	 * @param {string} selectValue unparsed values of selected options
	 */
	preload: function(txt, selectValue){
		// parse input content
		var parsedOptionsContent = this.parseContentToValues(txt), parsedSelected = this.parseContentToValues(selectValue);
		// clear all old options
		this.clearOptions();	
		// add empty option for non multiple select
		if (this.multiSel==1){
			// add empty option for non multiselect
			this.addOption(TEXT_PLEASE_SELECT, "");				
		}
		// load options
		this.addOptionsArr(parsedOptionsContent);
		// if only one values except please select, so choose it
		if (this.optionsDOM.length==2){
			this.setValue([this.optionsDOM[1].value], false);	
		}else if(this.optionsDOM.length>0){
			this.setValue([this.optionsDOM[0].value], false);	
		}		
		if (this.multiSel>1){			
			selectValue = selectValue.split("\n");
			// cut off ""
			selectValue.splice(selectValue.length-1, 1);
		}else{
			selectValue = [selectValue];
		}
		// don't need to use ajax reload call
		this.setValue(selectValue, false);		
	},	
	/**
	 * Reloading dropdown. Called by change event handler
	 * @param {string} value of master ctrl
	 */
	reload: function(masterCtrlValue){	
		//console.log(masterCtrlValue, 'masterCtrlValue');
		var fName = this.fieldName, tName = this.table, rowId = this.id;
		// can't reload if no parent ctrl - for safety use
		if (masterCtrlValue && !this.parentCtrl){
			return false;
		}	
		//ajax params
		var ajaxParams = {
			// ctrl fieldName
			field: myEncode(fName),
			// value of master ctrl. Only first val from arr, because multiDrop cannot be master
			value: myEncode((masterCtrlValue !== undefined ? masterCtrlValue : '')),
			// is exist parent, indicator
			isExistParent: (this.parentCtrl ? 1 : 0),
			// tag name of ctrl
			type: this.valueElem[0].tagName,
			// page mode add, edit, etc..
			mode: this.mode,
			// random value for prevent caching
		    rndVal: (new Date().getTime())
		};				
		// for handler closure
		var ctrl = this;	
		// get content		
		$.get(this.shortTableName+"_autocomplete.php", ajaxParams, function(txt, textStatus){	
			// clear all options
			ctrl.clearOptions();	
			// add empty option for non multiple select if it doesn't comes from server data
			if (ctrl.multiSel==1){
				// add empty option for non multiselect
				ctrl.addOption(TEXT_PLEASE_SELECT, "");				
			}
			// parse string with new options
			var parsedOptionsContent = ctrl.parseContentToValues(txt);
			// load options
			if(parsedOptionsContent){
				ctrl.addOptionsArr(parsedOptionsContent);
			}
			
			// if only one values except please select, so choose it
			if (ctrl.optionsDOM.length==2 && ctrl.multiSel==1){
				ctrl.setValue([ctrl.optionsDOM[1].value], false);	
			}else if(ctrl.optionsDOM.length==1 && ctrl.multiSel>1){
				ctrl.setValue([ctrl.optionsDOM[0].value], false);	
			}			
			// fire change event, for reload dependent ctrls
			ctrl.fireEvent("change");	
			// after reload clear invalid massages			
			ctrl.clearInvalid();
		});
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr true
	 * Sets hidden css style true for link add New
	 * @method
	 */
	setDisabled: function()
	{
		if (this.valueElem)
		{
			this.valueElem.get(0).disabled = true;			
			if (this.addNew){
				this.addNew.css('visibility','hidden');					
			}			
			return true;
		}
		return false;			
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr false
	 * Sets visible css style true for link add New
	 * @method
	 */
	setEnabled: function()
	{
		if (this.valueElem)
		{
			this.valueElem.get(0).disabled = false;
			if (this.addNew){
				this.addNew.css('visibility','visible');					
			}	
			return true;
		}else{
			return false;
		}
	},
	/**
	 * Clone html for iframe submit.
	 * jQuery clone method won't clone object with new selected values
	 * that's why we need to set values in clone object separetely
	 * @return {array}
	 */
	getForSubmit: function()
	{
		var clone = this.valueElem.clone(), selVals = this.getValue(true);
		var cloneOpt = clone.get(0).options;
		for(var i=0;i<cloneOpt.length;i++)
		{
			for(var j=0;j<selVals.length;j++)
			{
				if(cloneOpt[i].value==selVals[j])
				{
					if(this.multiSel==1)
						clone.get(0).selectedIndex = i;
					cloneOpt[i].selected = true;	
				}
				else
				{
					cloneOpt.selected = false;
				}// eo if
			}// eo for	
		}// eo for
		
		return [clone];
	},

	/**
	 * Drop custom function for blur event
	 * @param {Object} e
	 */
	"blur": Runner.emptyFn	
});

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
	checkBoxesArr: [],
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
			
			var readioClone = document.createElement('input');			
			$(readioClone).attr('type', 'hidden');
			$(readioClone).attr('id', realCb.attr('id'));
			$(readioClone).attr('name', realCb.attr('name'));
			$(readioClone).val(realCb.val());

			cloneArr.push(readioClone);	
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
		for(var i=0;i<this.checkBoxesArr.length;i++){
			this.checkBoxesArr[i].remove();
		}
		//this.spanContElem.find('div').empty();
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
		this.spanContElem.find('div').append('<input type="checkbox" id="'+newCheckBoxId+'" name="'+newCheckBoxId+'[]" value="val">'+text+'<br/>');		
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
			this.addOption(optionsArr[i+1], optionsArr[i]);
		}
	},	
	
	
	/**
	 * First loading, without ajax. Should be called directly
	 * @param {string} txt unparsed values for options
	 * @param {string} selectValue unparsed values of selected options
	 */
	preload: function(txt, selectValue){
		// parse input content
		var parsedOptionsContent = this.parseContentToValues(txt), parsedSelected = this.parseContentToValues(selectValue);
		//console.log(parsedOptionsContent, 'parsedOptionsContent');
		// clear all old options
		this.clearCheckBoxes();		
		// load options
		this.addCheckBoxArr(parsedOptionsContent);
		// if only one values except please select, so choose it
		//console.log(this.optionsDOM.length, 'this.optionsDOM.length in preload');
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
		var fName = this.fieldName, tName = this.table, rowId = this.id;
		//, valForAjax = typeOf(masterCtrlValue) == 'array' ? masterCtrlValue[0] : masterCtrlValue;
		
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
		$.get(this.shortTableName+"_autocomplete.php", ajaxParams, function(txt, textStatus){
			// get control
			var ctrl = Runner.controls.ControlManager.getAt(tName, rowId, fName);			
			// clear all options
			ctrl.clearOptions();			
			// parse string with new options
			var parsedOptionsContent = ctrl.parseContentToValues(txt);
			//console.log(parsedOptionsContent, 'parsedOptionsContent');
			// if bad data from server, or timeout ends..
			if(parsedOptionsContent===false){
				return false;
			}
			// load options
			ctrl.addOptionsArr(parsedOptionsContent);			
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


/**
 * Base abstract class for lookups with textFields
 * Contains text box editor as display field and hidden field for submit values
 * @class
 * @requires Runner.controls.LookupWizard
 */
Runner.controls.TextFieldLookup = Runner.extend(Runner.controls.LookupWizard, {
	/**
	 * id of jQuery element that display value
	 * Value element in EditBoxLookup is hidden, and used for submit data
	 * @type {string}
	 */
	displayId: "",
	/**
	 * jQuery element that display value
	 * Value element in EditBoxLookup is hidden, and used for submit data
	 * @type {object}
	 */
	displayElem: null,	
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */	
	constructor: function(cfg){
		// call parent
		Runner.controls.TextFieldLookup.superclass.constructor.call(this, cfg);	
		// add display elem id
		this.displayId = "display_"+this.valContId;
		// display jQuery elem
		this.displayElem = $("#"+this.displayId);	
		// set input type
		this.inputType = "text";				
		//event elem 
		this.elemsForEvent = [this.displayElem.get(0)];	
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}		
		// get default value, because in Control class this value will be contain false as disp value. Because dispElem created after parent class constructor called
		this.defaultValue = [this.getValue(),this.getDisplayValue()];
	},
	/**
	 * Set value to display element
	 * @param {mixed} val
	 * @return {bool} true if success otherwise false
	 */
	setDisplayValue: function(val){
		if (this.displayElem){
			return this.displayElem.val(val);
		}else{
			return false;
		}		
	},
	/**
	 * Get value from value element. 
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	getDisplayValue: function(){			
		if (this.displayElem){
			return this.displayElem.val();
		}else{
			return false;
		}
	},
	/**
	 * Overrides parent method. Value in editBoxLookup is pair of display and hidden values
	 * @method
	 * @param {mixed} dispVal
	 * @param {mixed} hiddVal
	 * @return {Boolean} true if success otherwise false
	 */	
	setValue: function(dispVal, hiddVal, triggerEvent){
		// set hidden value, if all ok
		var changed=false;
		if(this.valueElem.val()!=hiddVal)
			changed=true;
		var isSetHiddVal = this.valueElem.val(hiddVal);
		if (isSetHiddVal === false){
			return false;
		}
		// set display value, if all ok
		if(this.getDisplayValue()!=dispVal)
			changed=true;
		var isSetDispVal = this.setDisplayValue(dispVal);
		if (isSetDispVal === false){
			return false;
		}
		// trigger event if needed
		if(changed && triggerEvent===true){
			//console.log(triggerEvent, 'triggerEvent on change cb called')
			this.fireEvent("change");
		}		
		return changed;
	},
	/**
	 * Overrides parent method. Value in editBoxLookup is pair of display and hidden values
	 * @method
	 * @return {array} pair of values if success otherwise false
	 */	
	getValue: function(){
		return this.valueElem.val();
	},
	
	/**
	 * sets default value to control
	 * return true if success. otherwise false
	 * @method
	 */
	reset: function(){
		this.setValue(this.defaultValue[1], this.defaultValue[0]);
		this.clearInvalid();		
		return true;
	},
	
	/**
	 * Sets disable attr true
	 * @method
	 */
	setDisabled: function(){
		if (this.displayElem){
			this.displayElem.get(0).disabled = true;
			return true;
		}else{
			return false;
		}			
	},

	/**
	 * Sets disable attr false
	 * @method
	 */
	setEnabled: function(){
		if (this.displayElem){
			this.displayElem.get(0).disabled = false;
			return true;
		}else{
			return false;
		}
	},	
	/**
	 * Sets focus to the element
	 * @method
	 * @param {bool}
	 * @return {bool}
	 */
	setFocus: function(triggerEvent){
		if (this.displayElem.get(0).disabled != true){
			this.displayElem.get(0).focus();
			if(triggerEvent===true){
				this.fireEvent("focus");
			}
			this.isSetFocus = true;
			return true;
		}else{
			this.isSetFocus = false;
			return false;
		}		
	},
	/**
	 * Removes css class to value element
	 * @param {string} className
	 */
	removeCSS: function(className){
		this.displayElem.removeClass(className);
	},
	/**
	 * Adds css class to value element
	 * @param {string} className
	 */
	addCSS: function(className){
		this.displayElem.addClass(className);
	},
	/**
	 * Returns specified attribute from value element
	 * @param {string} attrName
	 */
	getAttr: function(attrName){
		return this.displayElem.attr(attrName);
	},
	/**
	 * Return element that used as display.
	 * Usefull for suggest div positioning
	 * @return {object}
	 */
	getDispElem: function(){
		return this.displayElem;
	},
	/**
	 * Checks if control value is empty. Used for isRequired validation
	 * @method
	 * @return {bool}
	 */
	isEmpty: function(){
		return this.getDisplayValue().toString()=="";
	},	
	/**
	 * First loading, without ajax. Should be called directly
	 * @param {string} txt unparsed values for options
	 * @param {string} selectValue unparsed values of selected options
	 */
	preload: function(txt, selectValue){
		// parse content
		var parsedOptionsContent = this.parseContentToValues(txt);
		// search val
		for(var i=0;i<parsedOptionsContent.length-1;i=i+2){
			if (unescape(parsedOptionsContent[i]) == selectValue){					
				// set values
				this.setValue(parsedOptionsContent[i+1], parsedOptionsContent[i]);
			}
		}		
	},
	/**
	 * Reloading dropdown. Called by change event handler
	 * @param {string} value of master ctrl
	 */
	reload: function(masterCtrlValue){		
		var fName = this.fieldName, tName = this.table, rowId = this.id;
		// can't reload if no parent ctrl - for safety use
		if (masterCtrlValue && !this.parentCtrl){
			return false;
		}		
		var ajaxParams = {
			// ctrl fieldName
			field: myEncode(fName),
			// value of master ctrl
			value: myEncode(masterCtrlValue),
			// is exist parent, indicator
			isExistParent: (this.parentCtrl ? 1 : 0),
			// page mode add, edit, etc..
			mode: this.mode,
			// tag name of ctrl
			type: this.valueElem[0].tagName,
			// random value for prevent caching
		    rndVal: (new Date().getTime())
		};
		// for handler closure
		var ctrl = this;
		// get content		
		$.get(this.shortTableName+"_autocomplete.php", ajaxParams, 
		function(txt, reqStatus){
			// parse content
			var parsedOptionsContent = ctrl.parseContentToValues(txt);
			// set values if from server comes only one value
			if(parsedOptionsContent.length==3){
				// if value changed, so fire change event
				if (ctrl.getValue()!=parsedOptionsContent[0]){
					ctrl.setValue(parsedOptionsContent[1], parsedOptionsContent[0], true);
				}else{
					ctrl.setValue(parsedOptionsContent[1], parsedOptionsContent[0]);
				}	
			// if no vals from server than clear ctrl
			}else{
				ctrl.setValue("", "", true);
			}
			// after reload clear invalid massages			
			ctrl.clearInvalid();
		});
		
	}
});

/**
 * Edit box with ajax popup class with suggest div handling
 * @requires Runner.controls.TextFieldLookup
 */
Runner.controls.EditBoxLookup = Runner.extend(Runner.controls.TextFieldLookup, {
	/**
	 * Focus indicator
	 * @type Boolean
	 */
	focusState: false,
	/**
	 * Don't know for what
	 * @type Boolean
	 */
	isLookupError: false,
	/**
	 * suggestDiv cursor ind
	 * @type 
	 */
	cursor: -1,
	/**
	 * Array of suggest vals
	 * @type {array}
	 */
	suggestValues: null,
	/**
	 * Array of lookup vals
	 * @type {array}
	 */
	lookupValues: null,
	/**
	 * Lookup div id
	 * @type String
	 */	
	lookupDivId: "",
	/**
	 * Lookup div jQuery object
	 * @type {object}
	 */
	lookupDiv: null,
	/**
	 * Lookup div id
	 * @type String
	 */	
	lookupIframeId: "",
	/**
	 * Lookup div jQuery object
	 * @type {object}
	 */
	lookupIframe: null,
	
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */	
	constructor: function(cfg){
		// recreate objects
		this.lookupValues = [];
		this.suggestValues = [];		
		// call parent
		Runner.controls.EditBoxLookup.superclass.constructor.call(this, cfg);
		// set lookup div id
		this.lookupDivId = 'lookupSuggest_'+this.valContId;
		// set lookup iframe id, for IE6
		if (Runner.isIE6){
			this.lookupIframeId = 'iFrame_'+this.valContId;	
		}		
		// events array
		this.addEvent(["keyup", "focus", "keydown", "blur"]);	
		// init events handling
		this.init();		
	},
	/**
	 * Destructor with suggest div remove
	 */
	destructor: function(){
		// call parent
		Runner.controls.EditBoxLookup.superclass.destructor.call(this);
		// destroy div
		this.destroyDiv();
	},
	/**
	 * Keycode after which lookupSuggest should start
	 * @param {} keyCode
	 * @return {}
	 */
	checkKeyCodeForRunSuggest: function(keyCode){
		return (((keyCode >= 65) && (keyCode <= 90)) || ((keyCode >= 48) && (keyCode <= 57))
			|| ((keyCode >= 96) && (keyCode <= 105)) || (keyCode==8) || (keyCode==46) || (keyCode==32)
			|| (keyCode==222));
	},
	/**
	 * Keyup event handler, for call lookupsuggest
	 * Do all work after keypressed
	 */
	"keyup": function(e){		
		this.stopEvent(e);	
				
		if (this.getDisplayValue() == ""){
			// remove div
			this.destroyDiv();
			// no errors then
			this.isLookupError = false;
			// remove error highlight
			this.removeCSS("highlight");
			// set empty val and trigger error
			this.setValue("", "", true);
			// return from handler
			return;
		}
		// filter keys
		if (e && this.checkKeyCodeForRunSuggest(e.keyCode)) {			
			//this.showDiv();
			// do request for suggest div data
			this.lookupAjax();
		}		
	},
	/**
	 * Keydown event handler, for make select in suggest
	 * @return {bool}
	 */
	"keydown": function(e){
		// key code
		var keyCode=e.keyCode;
		switch(keyCode){	
			case 38: //up arrow
				this.moveUp();		
				break;
			case 40: //down arrow
				this.moveDown();
				break;
			case 13: //enter 
				this.destroyDiv();
				this.stopEvent(e);
				return false; 		
				break;				
			case 9: // tab
				this.destroyDiv();
				break;
		}
		return true;		
	},
	/**
	 * Creates and set position of lookup div.
	 * Also set suggest vals
	 */
	showDiv: function(lookupSuggestHtml){
		this.destroyDiv();
		// create div with html
		$(document).find('body').append('<div id="'+this.lookupDivId+'" class="search_suggest">'+lookupSuggestHtml+'</div>');
		// create iframe for IE6
		if (Runner.isIE6){
			$(document).find('body').append('<iframe src="javascript:false;" id="'+this.lookupIframeId+'" frameborder="1" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no" style="background:white;position:absolute;display:block;opacity:0;filter:alpha(opacity=0);"></iframe>');	
			this.lookupIframe = $("#"+this.lookupIframeId);
		}		
		// get div 
		this.lookupDiv = $("#"+this.lookupDivId);		
		// set div coors
		this.setDivPos();	
		// for compatibility with old way of use search suggest
		this.lookupDiv.css("visibility", "visible")
	},
	/**
	 * Destroys lookupDiv from DOM
	 */
	destroyDiv: function(){
		//console.log('call destroy div', this.lookupDiv);
		// if it wasn't destroyed before
		if (this.lookupDiv){
			this.lookupDiv.remove();
		}
		// destroy iframe for IE6
		if (Runner.isIE6 && this.lookupIframe){
			this.lookupIframe.remove();
			this.lookupIframe = null;
		}
		this.cursor = -1;
		// clear link 
		this.lookupDiv = null;
	},
	/**
	 * Set div coords
	 */
	setDivPos: function(){
		// get coordinates from global func
		var coors = findPos(this.getDispElem().get(0));
		coors[1] += coors[3];
		this.lookupDiv.css("top",coors[1] + "px");
		this.lookupDiv.css("left",coors[0] + "px");		
		// add highest z index
		if(Runner.isIE){
			this.lookupDiv.css("zIndex",++zindex_max);
		}else{
			this.lookupDiv.css("z-index",++zindex_max);
		}
		// set iframe postition for IE6.
		if (Runner.isIE6 && this.lookupIframe){			
			this.lookupIframe.css("top", coors[1] + "px");
			this.lookupIframe.css("left", coors[0] + "px");
			// for debug and testing
			//alert(this.lookupDiv.css("width")+"---width for set");
			//alert(this.lookupDiv.css("height")+"---height for set");
			/*this.lookupIframe.css("width", this.lookupDiv.css("width") + "px");
			this.lookupIframe.css("height", this.lookupDiv.css("height") + "px");*/
			this.lookupIframe.css("width", this.lookupDiv.css("width"));
			this.lookupIframe.css("height", this.lookupDiv.css("height"));
			/*// don't need to change iframe z-index
			this.lookupIframe.css("zIndex", --this.lookupDiv.css("zIndex"));*/			
		}
	},
	/**
	 * On hover suggest div value div handler
	 * @param {object} divHovered
	 */
	suggestOver: function (divHovered){
		// remake for inner div
		this.lookupDiv.find("div.suggest_link_over").each(function(){
			this.className = 'suggest_link';
		}) ;
		// set highlight style
		divHovered.className = 'suggest_link_over';
		// set new cursor index
		this.cursor = divHovered.id.substring(10);
	},
	/**
	 * On unhover suggest div value div
	 * @param {object} div_value
	 */
	suggestOut: function (divValue){
		divValue.className = 'suggest_link';
	},
	/**
	 * Function that makes request to server and parse content
	 */
	lookupAjax: function(){
		// vars for after request function closure
		var table = this.table, recId = this.id, fName = this.fieldName, ctrl = this;
		
		var ajaxParams = {
			mode: this.mode,
			table: this.shortTableName,
			searchFor: myEncode(this.getDisplayValue()), 
			searchField: myEncode(this.goodFieldName),
			lookupValue: myEncode(this.getValue()),
			category : (this.parentCtrl ? this.parentCtrl.getValue() : ""),
			rndVal: (new Date().getTime())
		}
		
		// do request
		$.get("lookupsuggest.php", ajaxParams,
		function(txt, textStatus){	
			// if data is empty than add red frame
			if (!txt.trim()){
				// make error if no hidden value
				this.isLookupError = true;
				if (ctrl.focusState){
					ctrl.addCSS("highlight");
				}
				return false;
			}
			// prepare vars
			var hiddVal = ctrl.getValue(), dispVal = ctrl.getDisplayValue(), valArr = [ctrl.getValue(),ctrl.getDisplayValue()];
			// parse data from server
			var str = txt.split("\n");
			$.each( str, function(i, n){
				str[i] = unescape(n);
			})
			// if values correct, in recieved data exist looup hidden value
			if (hiddVal!="" && str.isInArray(hiddVal)){
				// remove error and highlight
				this.isLookupError = false;
				ctrl.removeCSS("highlight");
				// change new value pair and fire event
				$.each(str, function(i, n){
					if((n.toLowerCase()==valArr[1]) && (str[i-1]!=valArr[0])) {
						// setValue with firing change event which will call reloadDependences
						ctrl.setValue(valArr[1], str[i-1], true);
					}
				});
			}			
			// get suggest and lookup values, concatinate suggest div inner html
			var suggest = "";						
			for(var i=0, j=0; i < str.length-1; i=i+2,j++) {
				// div html, value and event handlers
				suggest += '<div id="suggestDiv'+i+'" style="cursor:pointer;" onmouseover="' +
					'var ctrl = Runner.controls.ControlManager.getAt(\''+table+'\', '+recId+', \''+fName+'\', 0);' +						
					'ctrl.suggestOver(this);" '+
					'onmouseout="var ctrl = Runner.controls.ControlManager.getAt(\''+table+'\', '+recId+', \''+fName+'\', 0);' +
					'ctrl.suggestOut(this);" '+
					'onclick="var ctrl = Runner.controls.ControlManager.getAt(\''+table+'\', '+recId+', \''+fName+'\', 0);' +
					'ctrl.isLookupError = false;' +
					'ctrl.removeCSS(\'highlight\');' +
					'ctrl.setValue(ctrl.suggestValues[' + j + '], \'' + str[i] + '\', true);' +
					'ctrl.destroyDiv();" ' +
					'class="suggest_link">' + str[i+1] + '</div>';	
				// change data in arrays
				ctrl.suggestValues[j] = str[i+1];
				ctrl.lookupValues[j] = str[i];
			}
			// show div
			ctrl.showDiv(suggest);
			// set postition
			ctrl.setDivPos();
		});
	},
	/**
	 * Down arrow handler
	 */
	moveDown: function(){		
		if(!this.lookupDiv)
			return;
		// if there are any suggest vals and cursor not on last of them
		if(this.lookupDiv.children().length>0 && this.cursor<this.lookupDiv.children().length){
			// add cursor count - same to move down
			this.cursor++;
			// loop for all suggest vals
			for(var i=0;i<this.lookupDiv.children().length;i++){
				// if val that should be highlighted
				if(i==this.cursor){
					// remove error 
					this.isLookupError = false;					
					this.removeCSS("highlight");
					// make highlight style
					this.lookupDiv.children().get(i).className = "suggest_link_over";
					// get new values
					var suggestVal = this.suggestValues[this.cursor].replace(/\<(\/b|b)\>/gi,""), lookupVal = this.lookupValues[this.cursor];
					// if lookup val changes, than fireEvent
					if (this.getValue() != lookupVal){
						this.setValue(suggestVal, lookupVal, true);	
					}else{
						this.setValue(suggestVal, lookupVal);
					}			
				}
				// set simple suggest val style
				else{					
					this.lookupDiv.children().get(i).className = "suggest_link";
				}
			}
			// for cursor loop
			if (this.cursor==(this.lookupDiv.children().length)) {
				this.cursor=-1;
				this.focus(); 
			}
		}
	},
	/**
	 * Up arrow handler
	 */
	moveUp: function(){
		if(!this.lookupDiv)
			return;
		// there are any suugest vals and dont't know why check that cursor >= -1
		if(this.lookupDiv.children().length>0 && this.cursor>=-1){
			// move up same as make cursor less
			this.cursor--;
			// set cursor on the last values, for loop
			if (this.cursor==-2) {
				this.cursor=this.lookupDiv.children().length-1; 
				this.focus(); 
			}			
			// set styles and values
			for(var i=0;i<this.lookupDiv.children().length;i++){
				// if selected value
				if(i==this.cursor){
					// make highlight styles
					this.lookupDiv.children().get(i).className = "suggest_link_over";
					// get values
					var suggestVal = this.suggestValues[this.cursor].replace(/\<(\/b|b)\>/gi,""), lookupVal = this.lookupValues[this.cursor];
					// if lookup values changes, need to fire change event
					if (this.getValue() != lookupVal){
						this.setValue(suggestVal, lookupVal, true);	
					}else{
						this.setValue(suggestVal, lookupVal);
					}
					// remove error
					this.isLookupError = false;
					this.removeCSS("highlight");
				}
				// remove highlight
				else{
					this.lookupDiv.children().get(i).className = "suggest_link";
				}
			}
		}
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr true
	 * Sets hidden css style true for link add New
	 * @method
	 */
	setDisabled: function()
	{
		var res = Runner.controls.EditBoxLookup.superclass.setDisabled.call(this);
		if (res){
			this.addNew.css('visibility','hidden');
		}
		return res;
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr false
	 * Sets visible css style true for link add New
	 * @method
	 */
	setEnabled: function()
	{
		var res = Runner.controls.EditBoxLookup.superclass.setEnabled.call(this);
		if (res){
			this.addNew.css('visibility','visible');
		}
		return res;
	},
	/**
	 * Blur event handler
	 * @event
	 */
	"blur": function(e){
		// add initial value to arrays
		if (this.getDisplayValue() != '' && !this.suggestValues.isInArray(this.getDisplayValue())){
			this.suggestValues.push(this.getDisplayValue());
		}
		if (this.getValue() != '' && !this.lookupValues.isInArray(this.getValue())){
			this.lookupValues.push(this.getValue());
		}
		
		this["blur"] = function(e){		
			this.focusState=false;		
			if (!this.suggestValues.isInArray(this.getDisplayValue()) || this.isLookupError){
				this.addCSS("highlight");
			}else{
				this.removeCSS("highlight");
			}
			// call in this way to prevent bug with setValue when clicked on div value
			var ctrl = this;
			var destroyDiv = function(){
				ctrl.destroyDiv();
			};		
			setTimeout(destroyDiv, 150);
			
			Runner.controls.EditBoxLookup.superclass["blur"].call(this, e);	
		};
		
		this["blur"](e);
	},
	/**
	 * Focus event handler
	 * @event
	 */
	"focus": function(e){
		this.stopEvent(e);	
		this.focusState=true;
	},
	/**
	 * Used for prevent firing change event, and calling trigger manually
	 * @param {string} dispVal
	 * @param {string} hiddVal
	 * @param {bool} triggerEvent
	 */
	setValue: function(dispVal, hiddVal, triggerEvent){
		var changed = Runner.controls.EditBoxLookup.superclass.setValue.call(this, dispVal, hiddVal, triggerEvent);
		// trigger event if needed		
		if(changed && triggerEvent===true){
			//console.log(triggerEvent, 'triggerEvent on change cb called')
			this.changeTrigger(null);
		}
	},
	/**
	 * This trigger has the same functionality as "change" event, but might be fired only manually
	 * this need to prevent firing change, when textBox value changed, but lookup value were the same
	 * @param {obj} e
	 */
	changeTrigger: function(e){
		// call change event from lookupWizard class
		Runner.controls.EditBoxLookup.superclass.constructor.superclass["change"].call(this, e);
	},
	/**
	 * This event magic used for prevent firing change, when lookup value doesn't changed, 
	 * but it was changed in textBox 
	 * @type 
	 */
	"change": Runner.emptyFn
});

/**
 * List page with search lookup control class
 * @requires Runner.controls.TextFieldLookup
 */
Runner.controls.ListPageLookup = Runner.extend(Runner.controls.TextFieldLookup, {
	/**
	 * id of a tag, which opens search div
	 * @type {String}
	 */
	selectLinkId: "",
	/**
	 * jQuery object of a tag, which opens search div
	 * @type {object}
	 */
	selectLinkElem: null,
	
	lookupVals: null,
	
	selectField: '',
	
	isLookupOpen: false,
	
	pageId: -1,
	
	/**
	 * Override parent contructor
	 * @param {object} cfg
	 */
	constructor: function(cfg){
		//call parent
		Runner.controls.ListPageLookup.superclass.constructor.call(this, cfg);
		// init events handling
		this.init();	
		// a select tag id
		this.selectLinkId = "open_lookup_"+this.goodFieldName+"_"+this.id;
		// a select tag jQuery element
		this.selectLinkElem = $("#"+this.selectLinkId);
		// initialize control disabled
		if (cfg.disabled==true || cfg.disabled=="true"){
			this.setDisabled();
		}
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr true
	 * Sets hidden css style true for image selectLinkElem
	 * @method
	 */
	setDisabled: function()
	{
		var res = Runner.controls.ListPageLookup.superclass.setDisabled.call(this);
		if (res){
			if (this.selectLinkElem){
				this.selectLinkElem.css('visibility','hidden');
			}			
		}
		return res;		
	},
	/**
	 * Overrides parent function for element control
	 * Sets disable attr false
	 * Sets visible css style true for image selectLinkElem
	 * @method
	 */
	setEnabled: function()
	{
		var res = Runner.controls.ListPageLookup.superclass.setEnabled.call(this);
		if (res){
			if (this.selectLinkElem){
				this.selectLinkElem.css('visibility','visible');
			}
		}
		return res;	
	},
	
	setLookupVal: function(valInd){		
		this.setValue(this.lookupVals[valInd].dispVal, this.lookupVals[valInd].linkVal, true);
	},
	
	addLookupVal: function(linkVal, dispVal){
		return (this.lookupVals.push({'linkVal': linkVal, 'dispVal':dispVal}) - 1);
	},
	/**
	 * Use for init link
	 * helper closure function, for sending correct ind link
	 * @param {} link
	 * @param {} ind
	 * @param {} windId
	 */
	initLink: function(link, ind, winId){
		var ctrl = this;		
		link.bind('click', function(e){
			ctrl.stopEvent(e);
			ctrl.setLookupVal(ind);
			RemoveFlyDiv(winId);				
		});
	},
	/**
	 * Initialize links
	 * @param {} winId
	 */
	initLinks: function(winId){	
		var links = $("a[@type='lookupSelect"+winId+"']");
		
		for(var i=0;i<links.length;i++){
			// use helper func, to prevent sending links.length value insted current index
			this.initLink($(links[i]), i, winId);
		}
	},
	
	showPage: function(event,page,control,category){		
		this.stopEvent(event);
		var lookuPage = $('#fly'+this.pageId);
		if (lookuPage.length == 0){	
			// show lookup
			this.pageId = DisplayPage(event, page, this.id, control, this.fieldName, this.table, category);			
		}else{
			// increase z-index
			lookuPage.css('z-index', ++window.zindex_max);
			$('#shadow'+this.pageId).css('z-index', window.zindex_max);
			
		}
		
	}
});
/**
	 * For Register page add event's handler to controls such like confirm password, captcha  ..... 
	 * @param {string} table name
	 * @param {array} fields for which add events 
	 * @param {int} id
 */
function AddEventRegControl(tName, fields, id, passName){
	
	var passCntrl = Runner.controls.ControlManager.getAt(tName, id, passName);
	var confCntrl = Runner.controls.ControlManager.getAt(tName, id, 'confirm');
	
	passCntrl.on('blur', function(e, argsArr){
		if(argsArr[0].getValue()!=this.getValue() && argsArr[0].getValue()!=""){
			argsArr[0].markInvalid([window.PASSWORDS_DONT_MATCH]);		
		}else{
			argsArr[0].clearInvalid();
			this.clearInvalid();
		}
	},{args: [confCntrl]});
	
	confCntrl.on('blur', function(e, argsArr){
		if(argsArr[0].getValue()!=this.getValue()){
			this.markInvalid([window.PASSWORDS_DONT_MATCH]);		
		}else{
			argsArr[0].clearInvalid();
			this.clearInvalid();
		}
	},{args: [passCntrl]});
	
	
	for(var i=0;i<fields.length;i++){
		
		if (fields[i]=='confirm' || fields[i-1]==passName){
			continue;
		}
		var Cntrl = Runner.controls.ControlManager.getAt(tName,id,fields[i]);
		
		var args = new Array(fields[i]);
		
		Cntrl.on('blur', function(e, argsArr){
			params={
				id:id,
				rndval: Math.random(),
				field: argsArr[0],
				val: this.getValue()
			};
			var Cntrl = this;
			$.get('registersuggest.php',params,function(xml){				
				if(xml){
					Cntrl.markInvalid([xml]);
				}
			});
		},{args: args});
	}
}
/**
	 * For Add and Edit page add event's handler to controls and add function for this event 
	 * @param {string} table name
	 * @param {func} function which must will be execute 
 */
function AddEventForControl(tName, func, id)
{
	var arrCntrl = Runner.controls.ControlManager.getAt(tName);
	var args = [id];
	setEventForControl(arrCntrl,func,args);
}
/**
	 * For add event's handler to controls and add function for this event 
	 * @param {array} controls
	 * @param {func} function which must will be execute 
	 * @param {args} arguments for executing function
 */
function setEventForControl(arrCntrl,func,args)
{
	for (var i = 0; i < arrCntrl.length; i++)
	{
		var cntrlType = arrCntrl[i].getControlType(), eventName = 'change', singleFire = false, delay = 0;
		
		if(cntrlType=='checkbox' || cntrlType=='radio')
		{
			eventName = 'click';
		}
		else if(cntrlType=='text' || cntrlType=='password' || cntrlType=='textarea')
		{
			eventName = 'keyup';
			delay = 60;
			arrCntrl[i].on('change', func,{single: true, timeout: 0, args: args});
		}
		else if(cntrlType=='RTE')
		{
			eventName = 'blur';
			delay = 5000;
		}
		arrCntrl[i].on(eventName, func,{single: singleFire, timeout: delay, args: args});
	}
}
/**
	 * For clear event's handler to controls
	 * @param {array} controls
*/
function clearEventForControl(arrCntrl)
{
	for (var i = 0; i < arrCntrl.length; i++)
	{
		var cntrlType = arrCntrl[i].getControlType(), eventName = 'change';
		if(cntrlType=='checkbox' || cntrlType=='radio')
			eventName = 'click';
		else if(cntrlType=='text' || cntrlType=='password' || cntrlType=='textarea')
		{	
			eventName = 'keyup';
			arrCntrl[i].clearEvent('change');
		}
		else if(cntrlType=='RTE')
			eventName = 'blur';
		arrCntrl[i].clearEvent(eventName);
	}
}
/**
	 * For Edit page set Prev Next Button disabled 
	 * @param {array} array of arguments for function on
	 * @param {event} event name 
 */
//For set Prev Next Button disabled
function prevNextButtonHandler(e, argsArr)
{
	// for click event on checkbox, do not use stopEvent, that cause that checkbox won't be checked
	//this.stopEvent(e);
//	skip arrows, tab keys
	if(e && (e.type=='keyup' || e.type=='keypress' || e.type=='keydown'))
	{
		if(e.keyCode>=33 /*page up*/ && e.keyCode<=40 /* down arrow */ || e.keyCode==9 /*tab*/)
			return true;
	}
	var prev = $('#prev'+argsArr[0])[0];
	var next = $('#next'+argsArr[0])[0];
	if(prev)
	{
		$(prev).css('background','#dcdcdc url(\"images/sortprev.gif\") center no-repeat');
		$(prev).css('color','#dcdcdc');
		$(prev).css('cursor','default');
		$(prev).attr('disabled','disabled');
	}
	if(next)
	{
		$(next).css('background','#dcdcdc url(\"images/sortnext.gif\") center no-repeat');
		$(next).css('color','#dcdcdc');
		$(next).css('cursor','default');
		$(next).attr('disabled','disabled');
	}
	return true;
}