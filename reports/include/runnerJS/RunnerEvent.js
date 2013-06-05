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
			if (typeof this[this.events[i]] == "function"){
				this.on(this.events[i], this[this.events[i]]);
			}else if (typeof this[this.events[i]] == "object"){
				this.on(this.events[i], this[this.events[i]].fn, this[this.events[i]].options, this[this.events[i]].scope);
			}
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
			if (e){
				obj.unbindHn(eventName, e.data.hn);
			}
        };
	},
	
	bindHn: function(eventName, callHandler){
		if (!callHandler || !eventName){
			return false;
		}
		var el;
		// adding listeners for all elems for event		
		for(var i=0;i<this.elemsForEvent.length;i++){				
			el = this.elemsForEvent[i];
			$(el).bind(eventName, {hn: callHandler, obj: this}, callHandler);
		}
		return true;
	},
	
	unbindHn: function(eventName, callHandler){
		if (!callHandler || !eventName){
			return false;
		}
		var el;
		// remove listeners for all elems for event	
		for (var j = 0; j < this.elemsForEvent.length; j++) {
			el = this.elemsForEvent[j];
			$(el).unbind(eventName, callHandler);	
		}
		return true;
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
		eventName = eventName.toLowerCase();
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
		var args = options.args ? options.args : [], 
			single = options.single ? options.single : false, 
			timeout = options.timeout ? options.timeout : 0, 
			buffer = options.buffer ? options.buffer : 0;	
		
		var callHandler = function(e){	
			// prevent call if event suspended 
			if (objScope.suspendedEvents.isInArray(eventName)){
				return;
			}			
			fn.apply(scope, [e].concat(args));			
		}
		// creating delayed handler, usefull for validations etc.
		if (timeout){
			callHandler = this.createDelayed(callHandler, timeout);
		}
		// function will clear itself after called, usefull when function need to be called once
		if(single){
			callHandler = this.createSingle(callHandler, eventName)
		}
		if(buffer){
			callHandler = this.createBuffered(callHandler, buffer);
		}
				
		this.listeners.push({
			name: eventName,
			handler: fn,
			callHandler: callHandler,
			options: options,
			scope: scope,
			index: this.listeners.length
		});
						
		this.bindHn(eventName, callHandler);
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
		var eventInd = this.isDefinedEvent(eventName);
		if (eventInd == -1){
			return false;
		}
		this.clearEvent(eventName);		
		// remove native event handler
		this.unbindHn(eventName, this[eventName]);
		// delete event handler from object
		delete this[eventName];		
		//kill event
		this.events.splice(eventInd, 1);
		// in success
		return true;
	},
	
	purgeListeners: function(){
		for(var i=0; i<this.events.length; i++){
			this.killEvent(this.events[i]);
		}
	},
	/**
	 * Clear custom event handling, sets only base class handler
	 * @method
	 * @param {string} eventName
	 * @return {bool} true if success otherwise false
	 */
	clearEvent: function(eventName){
		// search for listener object
		var listeners = this.getListeners(eventName);	
		
		for(var i=0; i<listeners.length; i++){
			// clear handlers
			this.unbindHn(eventName, listeners[i].callHandler);
			this.listeners.splice(i,1);
		}	
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
		var listeners = this.getListeners(eventName);
		
		for(var i=0; i<listeners.length; i++){
			listeners[i].callHandler.apply(this, [null].concat(Array.prototype.slice.call(arguments, 1)));
		}

		if (this[eventName]){
			this[eventName].apply(this, [null].concat(Array.prototype.slice.call(arguments, 1)));
		}
    },
	/**
	 * Checks if event defined
	 * @param {string} eventName
	 * @return {mixed} false if not found otherwise arrray index
	 */
	isDefinedEvent: function(eventName){	
		return this.events.getIndexOfElem(eventName);
	},
	
	getListeners: function(eventName){		
		var listeneresArr = [];
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i].name == eventName) {	
				listeneresArr.push(this.listeners[i]);
			}
		}
		return listeneresArr;
	}
});


