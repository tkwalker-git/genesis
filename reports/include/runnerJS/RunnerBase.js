/**
 * Runner main object. 
 * 
 * JS files include order of inheritance, for example:
 * 1. Runner.js (main functionality)
 * 2. Validate.js (validations utilities)
 * 3. ControlManager.js (global object, for controls manage)
 * 4. Control.js (base abstract class for all controls)
 * 5. All controls in any order.
 */
var Runner = {version: '1.0'};
/**
 * Copies all the properties of config to obj.
 * @param {Object} obj The receiver of the properties
 * @param {Object} config The source of the properties
 * @param {Object} defaults object literal that will be applied first
 * @return {Object} returns obj
 * @member Runner apply
 */
Runner.apply = function(obj, cfg, defaults){
	// third argument passed copy it first
    if(defaults){        
        Runner.apply(obj, defaults);
    }
    // copy config and override defaults if they cross
    if(obj && cfg && typeof cfg == 'object'){
        for(var prop in cfg){
            obj[prop] = cfg[prop];
        }
    }
    return obj;
};
/**
 * Reusable empty function
 */
Runner.emptyFn = function(){};
/**
 * Main RunnerJS functionality
 */
(function(){
	
	var idCounter = 0;
	var zIndexMax = window.zindex_max;
    var userAgent = navigator.userAgent.toLowerCase();

    var isOpera = userAgent.indexOf("opera") > -1; 
    var isIE = (!isOpera && userAgent.indexOf("msie") > -1); 
	var isIE7 = (!isOpera && userAgent.indexOf("msie 7") > -1); 
	var isIE8 = (!isOpera && userAgent.indexOf("msie 8") > -1); 
	var isChrome = userAgent.indexOf("chrome") > -1; 
	var isSafari = (!isChrome && (/webkit|khtml/).test(userAgent)); 
	var isSafari3 = (isSafari && userAgent.indexOf('webkit/5') != -1); 
	var isGecko = (!isSafari && !isChrome && userAgent.indexOf("gecko") > -1); 
    var isGecko3 = (isGecko && userAgent.indexOf("rv:1.9") > -1);
        
    // copy properties to main object
    Runner.apply(Runner, {
		/**
         * Implements inheritance, on class-based model. 
         * Inherites one class from another and optionally overrides properties with third argument - object literal.
         * Function support three or two arguments call. With two arguments pass superclass as first, and literal with properties to override as second.
         * In three arguments call pass subclass, parent and object literal to copy properties in subclass
         * Example of usage

	    Runner.controls.TextArea = Runner.extend(Runner.controls.Control,{
			constructor: function(cfg){		
				this.addEvent(["change", "keyup"]);		
				// call parent
				Runner.controls.TextArea.superclass.constructor.call(this, cfg);
				// change input type, because textarea don't have type attr
				this.inputType = "textarea";		
			},
			getForSubmit: function(){
				return [this.valueElem.clone().val(this.getValue())]
			}
		});

         * @param {Function} subclass The class which inheriting the functionality
         * @param {Function} superclass The class for extend
         * @param {Object} overrides (optional) A literal object with properties which are copied into the subclass's prototype
         * @return {Function} The subclass constructor.
         * @method extend
         */
		extend: function(){
		    // inline overrides function
		    var overrideFunc = function(obj){
		        for(var prop in obj){
		            this[prop] = obj[prop];
		        }
		    };
		    // constructor of simple Object class
		    var baseObjConstr = Object.prototype.constructor;
			// create closure function
		    return function(subBase, supPar, overrides){
		    	// if function called with 2 paramters, superclass and object literal
		        if(typeof supPar == 'object'){
		        	// change vars, because called with 2 params
		            overrides = supPar;		            
		            supPar = subBase;
		            // if contructor won't overriden, call parent with all passed args
		            subBase = (overrides.constructor != baseObjConstr) ? overrides.constructor : function(){supPar.apply(this, arguments);};
		        }
		        // create temp function and vars with prototypes
		        var F = function(){}, subBaseProt, supParProt = supPar.prototype;
		        // change temp functiion prototype
		        F.prototype = supParProt;
		        // create new incstance of prototype, this will solve problem of one prototype chain
		        subBaseProt = subBase.prototype = new F();
		        // take care of inheritance, reset constructor
		        subBaseProt.constructor=subBase;
		        // make link to parent contructor
		        subBase.superclass=supParProt;
		        // reset parent constructor, don't know for what
		        if(supParProt.constructor == baseObjConstr){
		            supParProt.constructor=supPar;
		        }
		        // add override function
		        subBase.override = function(obj){
		            Runner.override(subBase, obj);
		        };
		        // add override to prototype
		        subBaseProt.override = overrideFunc;
		        // copy properties
		        Runner.override(subBase, overrides);
		        // add extend function
		        subBase.extend = function(obj){Runner.extend(subBase, obj);};
		        // return new class (constructor function)
		        return subBase;
	    	};
        }(),
		/**
         * Copies and replaces properties of one object with another
         * @param {Object} baseclass
         * @param {Object} object literal
         * @method override
         */
		override: function(origClass, overrides){
		    if(overrides){
		        var origProt = origClass.prototype;
		        // copy all properties to prototype
		        for(var method in overrides){
		            origProt[method] = overrides[method];
		        }
		        
		        if(Runner.isIE && overrides.toString != origClass.toString){
		            origProt.toString = overrides.toString;
		        }
		    }
		},
		
		getControl: function(rowId, fName){
			return Runner.controls.ControlManager.getAt(false, rowId, fName);
		},
		/**
		 * Loads javascript from file
		 * fileName {string} name of file to be loaded
		 */
		loadJS: function(src, onload, scope){
			scope = scope || this;
			var js = document.createElement('script');
			js.setAttribute('type', 'text/javascript');
			js.setAttribute('src', src);
			if(onload && Runner.isIE){
				js.onreadystatechange = function(){			
					if (js.readyState == 'complete' || js.readyState == 'loaded'){
						onload.call(scope);
					}
				};
			}else if(onload){
				js.onload = function(){
					onload.call(scope);
				}
			}
			document.getElementsByTagName('HEAD')[0].appendChild(js);
		},	
		/**
		 * Decodes after encoded str_replace(array("&","<",">"),array("&amp;","&lt;","&gt;"),$jscode);
		 * @param {string}
		 * @return {string}
		 */
		htmlDecode: function(txt){
			txt = txt.replace(/&gt;/ig,"\>");
			txt = txt.replace(/&lt;/ig,"\<");
			txt = txt.replace(/&amp;/ig,"&");	
			return txt;
		},
		/**
		 * Creates namesapce
		 * @method
		 */
		namespace: function(name){
			var params = name.split('.'), current = Runner;
			for(var i=1;i<params.length;i++){
				if (!current[params[i]]){
					current[params[i]] = {};
				}
				current = current[params[i]];
			}
			
			return current;
		},
		/**
		 * Generates unique id
		 */
		genId: function(pref){
			pref = pref || "runner";
            return pref + (++idCounter);
		},
		
		getZindex: function(elObj){
			// old global var. In future better to delete it and use only private zIndexMax;
			++zindex_max;
			zIndexMax = zindex_max;
			if (elObj){
				//if (Runner.isIE6){
					//elObj[0].style.zIndex=zindex_max;
				//}else{
					elObj.css("z-index", zIndexMax);	
				//}				
			}
			return zIndexMax;			
		},
		/**
		 * Replace all except numbers and strings into _
		 */
		goodFieldName: function(fName){	
			return fName.replace(/\W/g, '_');
		},
		
		getSearchController: function(id){
			return window['searchController'+id];
		},
		/**
         * True if browser is Opera.
         * @type Boolean
         */
        isOpera : isOpera,
        /**
         * True if browser is Mozilla
         * @type Boolean
         */
        isGecko : isGecko,
        /**
         * True if browser is Firefox 2++
         * @type Boolean
         */
        isGecko2 : isGecko && !isGecko3,
        /**
         * True if browser is Firefox 3++
         * @type Boolean
         */
        isGecko3 : isGecko3,        
        /**
         * True if browser is Safari.
         * @type Boolean
         */
        isSafari : isSafari,
        /**
         * True if browser is Safari 3++
         * @type Boolean
         */
        isSafari3 : isSafari3,
        /**
         * True if browser is Safari 2++
         * @type Boolean
         */
        isSafari2 : isSafari && !isSafari3,
        /**
         * True if browser is Internet Explorer.
         * @type Boolean
         */
        isIE : isIE,
        /**
         * True if browser is Internet Explorer 6++
         * @type Boolean
         */
        isIE6 : isIE && !isIE7 && !isIE8,
        /**
         * True if browser is Internet Explorer 7++
         * @type Boolean
         */
        isIE7 : isIE7,
        /**
         * True if browser is Internet Explorer 8++
         * @type Boolean
         */
        isIE8 : isIE8,
        /**
         * True if browser is Chrome.
         * @type Boolean
         */
        isChrome : isChrome
        
	});
	
    Runner.ns = Runner.namespace;
})();


/**
 * Controls objects package
 * @type {object}
 */
Runner.namespace('Runner.controls');
/**
 * Search objects package
 * @type {object} 
 */
Runner.namespace('Runner.search');


/**
 * produces a string in which '<', '>', and '&' are replaced with their HTML entity equivalents. 
 * This is essential for placing arbitrary strings into HTML texts. So, "if (a < b && b > c) {".entityify()
 * produces
 * "if (a &lt; b &amp;&amp; b &gt; c) {"
 * @return {string}
 */
String.prototype.entityify = function () {
    return this.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace('"', '&quot;');
};
/**
 * produces a quoted string. 
 * This method returns a string that is like the original string except 
 * that all quote and backslash characters are preceded with backslash.
 * @return {string}
 */
String.prototype.quote = function () {
	return this.replace("\\","\\\\").replace("'","\\'");
};
/**
 * does variable substitution on the string. 
 * It scans through the string looking for expressions enclosed in { } braces. 
 * If an expression is found, use it as a key on the object, and if the key has a string value or number value, 
 * it is substituted for the bracket expression and it repeats. This is useful for automatically fixing URLs. So
 * param = {domain: 'lala.com', media: 'http://zhuzhu.com/'};
 * url = "{media}logo.gif".xTempl(param);
 * produces a url containing "http://zhuzhu.com/logo.gif".
 * @param {object} o
 * @return {string}
 */
String.prototype.xTempl = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        }
    );
};
/**
 * method removes whitespace characters from the beginning and end of the string.
 * @return {string}
 */
String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
}; 

String.prototype.slashdecode = function(){
	var out = '';
	var pos = 0;
	for ( var i = 0; i < this.length - 1; i++ )
	{
		var c = this.charAt(i);
		if( c == '\\' )
		{
			out += this.substr(pos,i-pos);
			pos = i + 2;
			var c1 = this.charAt(i+1);
			i++;
			if ( c1 == '\\' ) {
				out += "\\";
			} else if ( c1 == 'r' ) {
				out += "\r";
			} else if ( c1 == 'n') {
				out += "\n";
			} else {
				i--;
				pos-=2;
			}
		}
	}
	if ( pos < this.length )
		out += this.substr(pos);
	
	return out;
};

/**
 * Checks if value exist in array
 * @param {mixed} value val to search
 * @param {bool} caseSensitive
 * @return {Boolean}
 */
Array.prototype.isInArray = function(value, caseSensitive){	
	for (var i=0; i < this.length; i++) {
		if (caseSensitive) {
			if(this[i] == value) { 
				return true; 
			}
		}else{			
			if(this[i].toString().toLowerCase() == value.toString().toLowerCase()) { 
				return true; 
			}
		}
	}
	return false;
};
/**
 * Counts elements in array
 * @param {mixed} value val to search
 * @param {bool} caseSensitive
 * @return {int}
 */
Array.prototype.countElems = function(value, caseSensitive){	
	var count = 0;
	for (var i=0; i < this.length; i++) {
		if (caseSensitive) {
			if(this[i] == value) { 
				count++;
			}
		}else{			
			if(this[i].toString().toLowerCase() == value.toString().toLowerCase()) { 
				count++; 
			}
		}
	}
	return count;
};

/**
 * Return index of element in array. If element doesn't exist in array, returns -1
 * @param {mixed} value value to search
 * @param {} callBack link to function that may used to comparison, 
 * accepts arguemnts:
 * 		value {mixed} value to search
 * 		elem {mixed} current array element in loop
 * 
 * @param {bool} caseSensitive doesn't work with callBack function
 * @return {int} index of element if found, or -1 if element doesn't exist in array
 */
Array.prototype.getIndexOfElem = function(value, callBack, caseSensitive){
	for (var i=0; i < this.length; i++) {
		if (callBack){
			if(callBack(value, this[i])){
				return i;
			}
		}else if (caseSensitive) {
			if (this[i] == value) {
				return i; 
			}
		}else{			
			if (this[i].toString().toLowerCase() == value.toString().toLowerCase()) {
				return i; 
			}
		}
	}
	return -1;
};
// register new namespace
Runner.namespace('Runner.util.Event');
/**
 * Cross browser solution for retrieve event target element 
 * @param {object} e
 * @return {object}
 */
Runner.util.Event.getTarget = function(e){
	return e.target || e.srcElement;
}


/**
 * @class Runner.util.DelayedTask
 * method for performing setTimeout where a new timeout cancels the old timeout. 
 * @param {Function} fn (optional) The default function to timeout
 * @param {Object} scope (optional) The default scope of that timeout
 * @param {Array} args (optional) The default Array of arguments
 */
Runner.util.DelayedTask = function(fn, scope, args){
    var id = null, delay, time;

    var call = function(){
        var now = new Date().getTime();
        //console.log(time, now, delay, now-time, 'calc to call');
        if(now - time >= delay){
            clearInterval(id);
            id = null;
            fn.apply(scope, args || []);
        }
    };
    /**
     * Cancels any pending timeout and queues a new one
     * @param {Number} delay The milliseconds to delay
     * @param {Function} newFn (optional) Overrides function passed to constructor
     * @param {Object} newScope (optional) Overrides scope passed to constructor
     * @param {Array} newArgs (optional) Overrides args passed to constructor
     */
    this.delay = function(newDelay, newFn, newScope, newArgs){
        if(id && delay != newDelay){
            this.cancel();
        }
        delay = newDelay;
        time = new Date().getTime();
        fn = newFn || fn;
        scope = newScope || scope;
        args = newArgs || args;
        if(!id){
            id = setInterval(call, delay);
        }
    };

    /**
     * Cancel the last queued timeout
     */
    this.cancel = function(){
        if(id){
            clearInterval(id);
            id = null;
        }
    };
};


/**
 * Page script manager. Should not be created directly, used by global script loader object
 * @class
 */
Runner.util.PageLoader = Runner.extend(Runner.emptyFn, {
	/**
	 * Function property, for adding js code from PHP
	 * @type {function}
	 */
	postLoadStep: Runner.emptyFn,
	/**
	 * Array of functions that should be called before JS from PHP will be executed
	 * @type {array}
	 */
	beforePool: null,
	/**
	 * Array of functions that should be called after JS from PHP will be executed
	 * @type {array}
	 */
	afterPool: null,
	/**
	 * Indicator
	 * @type Boolean
	 */
	isBeforeCalled: false,
	/**
	 * Indicator
	 * @type Boolean
	 */
	isAfterCalled: false,
	/**
	 * Indicator
	 * @type Boolean
	 */
	isPostLoadStepCalled: false,
	/**
	 * Same to page id for which current instance was created
	 * @type {int}
	 */
	id: -1,
	/**
	 * Extend constructor
	 * @param {object} cfg
	 */
	constructor: function(cfg){	
		Runner.util.PageLoader.superclass.constructor.call(this, cfg);
		this.beforePool = [];
		this.afterPool = [];
		// copy properties from cfg to controller obj
        Runner.apply(this, cfg);
	},
	
	addBe4PostLoad: function(func){
		if (!func){
			return false;
		}
		if (this.isBeforeCalled){
			func();
		}else{
			return this.beforePool.push(func);	
		}		
	},
	
	addPostLoadStep: function(func){
		if (!func){
			return false;
		}
		this.isPostLoadStepCalled = false;
		this.postLoadStep = func;
	},
	
	add2PostLoad: function(func){		
		if (!func){
			return false;
		}
		if (this.isAfterCalled){
			func();
		}else{
			return this.afterPool.push(func);	
		}	
	},
	
	
	/**
	 * Call functions from beforeLoadStep pool
	 * @private
	 * @method
	 * @return {int} number of function were called 
	 */
	callBeforePool: function(){
		for(var i=0;i<this.beforePool.length;i++){
			this.beforePool[i]();
		}
		this.isBeforeCalled = true;
		return this.beforePool.length;
	},
	
	callPostLoadStep: function(){
		this.postLoadStep();
		this.isPostLoadStepCalled = true;
	},
	/**
	 * Call functions from postLoadStep pool
	 * @private
	 * @method
	 * @return {int} number of function were called 
	 */
	callAfterPool: function(){
		for(var i=0;i<this.afterPool.length;i++){
			this.afterPool[i]();
		}
		this.isAfterCalled = true;
		return this.afterPool.length;
	},
	/**
	 * Call js code in correct order
	 * @method
	 * @public
	 */
	callJS: function(){
		if (!this.isBeforeCalled){
			this.callBeforePool();
		}
		if (!this.isPostLoadStepCalled){
			this.callPostLoadStep();
		}
		if (!this.isAfterCalled){
			this.callAfterPool();
		}
	}
	
	
});


/**
 * Global object for loading scripts and css files
 * @object
 */
Runner.util.ScriptLoader = function(){
	return {
		/**
		 * Array of CSS files for loading
		 * @type 
		 */
		cssFiles: [],
		/**
		 * Array of file names for load
		 * @type {array}
		 */
		jsFiles: [],
		/**
		 * Hash object with Runner.util.PageLoader instances as properties, for adding js code from PHP
		 * hash index is pageId
		 * @type {object}
		 */
		pageLoaders: {},
		/**
		 * Add js file to load queue
		 * @param {array} files
		 * @param any param except first will be added for requirements array
		 */
		addJS: function(files){
			var isAdded = false;
			// loop through all files to add
			for (var i=0;i<files.length;i++){
				// check if such file was added before
				for (var j=0;j<this.jsFiles.length;j++){
					if (this.jsFiles[j].name == files[i]){
						isAdded = true;
						break;
					}					
				}
				// add only new files
				if(!isAdded){
					// add files to array of file names
					this.jsFiles.push({
						name: files[i],
						isLoaded: false,
						//	add requirements, all passed arguments, except first
						requirements: Array.prototype.slice.call(arguments, 1)
					});
				}
				// reinit var
				isAdded = false;
			}
			
		},
		
		/**
		 * Method for load CSS files
		 * @param files {array}
		 */
		loadCSS: function (files){
			for (var i=0;i<files.length; i++){			
				// check if file exist in array of CSS files, try to get it's index
				var idx = this.cssFiles.getIndexOfElem(files[i], function(val, arrElem){
					if (val == arrElem.name){
						return true;
					}
				});
				// if file already added and it was loaded, than return true
				if (idx!=-1 && this.cssFiles[idx].isLoaded){
					return true;
				}			
				this.cssFiles.push({
					name: files[i],
					isLoaded: true
				})
				// load file			
				var head = $(document).find('head')[0];
				var css = document.createElement('link');
				css.setAttribute('rel', 'stylesheet');
				css.setAttribute('type', 'text/css');
				css.setAttribute('href', files[i]+".css");
				head.appendChild(css);
			}
		},
		
		load: function(pageId){
			// load all js files
			for(var i=0;i<this.jsFiles.length;i++){
				this.loadJS(i, pageId);		
			}
			
			if (typeof pageId != "undefined"){
				this.callPageJS(pageId);
			}else{
				// if no files for loading, or they are loaded than call js code
				this.callAllPagesJS();
			}
			
		},
		/**
		 * Load file from queue
		 * @param {int} idx file index
		 * @return {bool} true if success
		 * @method
		 * @private
		 */
		loadJS: function(idx, pageId){
			// return if no file obj for this file
			if (!this.jsFiles[idx]){
				return false;
			}
			// if loaded, load dependent files
			if(this.jsFiles[idx].isLoaded){
				this.postLoad(idx, pageId);
				return true;
			}
			// check requirements
			if (!this.checkReq(this.jsFiles[idx])){
				return false;
			}
			// file loading started already
			if(this.jsFiles[idx].isStarted){
				return false;
			}
			// load file
			this.jsFiles[idx].isStarted = true;
			var js = document.createElement('script');
			js.setAttribute('type', 'text/javascript');
			js.setAttribute('src', this.jsFiles[idx].name+".js");
			var sl = this;
			//	add onload event handler
			if(Runner.isIE){
				js.onreadystatechange = function(){			
					if (js.readyState == 'complete' || js.readyState == 'loaded'){
						sl.postLoad(idx, pageId);
					}
				};
			}else{
				js.onload = function() {	
					sl.postLoad(idx, pageId);
				};
			}
			document.getElementsByTagName('HEAD')[0].appendChild(js);
			return true;
		},
		
			
		/**
		 * Checks is required files are loaded
		 * @param {object} fileObj
		 * @return {Boolean}
		 */
		checkReq: function(fileObj){	
			// loop through all files
			for(var i=0;i<fileObj.requirements.length;i++){
				// loop through all req
				for(var j=0;j<this.jsFiles.length;j++){
					// if req cotains loaded file, than try to load it
					if (fileObj.requirements[i] == this.jsFiles[j].name && !this.jsFiles[j].isLoaded){ 
						return false;
					}
				}
			}
			

			return true;
			
		},
		/**
		 * After event handler. Called after file loaded.
		 * @method
		 */
		postLoad: function(idx, pageId){
			this.jsFiles[idx].isLoaded = true;			
			
			this.loadDependent(idx);
			
			if (typeof pageId != "undefined"){
				this.callPageJS(pageId);
			}else{
				// if no files for loading, or they are loaded than call js code
				this.callAllPagesJS();
			}
		},
		/**
		 * Call load for files, which are dependent to file with index = idx
		 * @param {int} idx
		 */
		loadDependent: function(idx){
			// loop through all files
			for(var i=0;i<this.jsFiles.length;i++){
				// loop through all req
				for(var j=0;j<this.jsFiles[i].requirements.length;j++){
					// if req cotains loaded file, than try to load it
					if (i != idx && this.jsFiles[i].requirements[j] == this.jsFiles[idx].name){ 
						this.loadJS(i);
					}
				}
			}
		},
		/**
		 * Call js code added for page with id = pageId
		 * @param {int} pageId
		 * @method
		 * @private
		 */
		callPageJS: function(pageId){
			for(var i=0;i<this.jsFiles.length;i++){
				if (!this.jsFiles[i].isLoaded){
					return;
				}
			}
			
			var loader = this.checkPageLoader(pageId);
			loader.callJS();
		},
		/**
		 * Call all js for all pages. Usually called after all files are loaded
		 */
		callAllPagesJS: function(){
			for(var i=0;i<this.jsFiles.length;i++){
				if (!this.jsFiles[i].isLoaded){
					return;
				}
			}
			
			for(var pageId in this.pageLoaders){
				this.callPageJS(pageId)
			}
			
		},
		/**
		 * Adds function before postLoad
		 * @param {function} func
		 * @param {int} pageId
		 * @method
		 * @public
		 */
		addBe4PostLoad: function(func, pageId){
			var loader = this.checkPageLoader(pageId);
			loader.addBe4PostLoad(func);	
		},
		/**
		 * Adds function to postLoad
		 * @param {function} func
		 * @param {int} pageId
		 */
		add2PostLoad: function(func, pageId){
			var loader = this.checkPageLoader(pageId);
			loader.add2PostLoad(func);
		},
		/**
		 * Adds function with js code, that generated from server
		 * @param {function} func
		 * @param {int} pageId
		 */
		addPostLoadStep: function(func, pageId){
			var loader = this.checkPageLoader(pageId);
			loader.addPostLoadStep(func);			
		},
		/**
		 * Check instance of pageLoader, create new if need it
		 * @param {int} pageId
		 * @return {object}
		 */
		checkPageLoader: function(pageId){
			if (!this.pageLoaders[pageId]){
				this.pageLoaders[pageId] = new Runner.util.PageLoader({
					id: pageId
				});
			}
			
			return this.pageLoaders[pageId];
		}
	
	}

}(); 
// create these classes only for IE6
//if (Runner.isIE6){
	/**
	 * IE utils objects package
	 * @type {object}
	 */
	Runner.namespace('Runner.util.IEHelper');
	/**
	 * iframe class. Used to cover select tags in IE6
	 * for correct work, el wich need to be covered should be child of document body 
	 * and have position absolute and method getPos should work with findPos, not with getAbsolutePosition
	 * 
	 * 
	 * @cfg {int} x
	 * @cfg {int} y
	 * @cfg {int} w
	 * @cfg {int} h
	 * @cfg {int} id options, if not passed, 
	 * @return {obj} iframe obj
	 */
	Runner.util.IEHelper.iframe = function(cfg){
		// init params		
		var cfg = cfg || {}, id;
		// if passed cfg object literal
		if (cfg.constructor === Object){
			cfg.w = cfg.w || 0, cfg.h = cfg.h || 0, cfg.t = cfg.t || 0, cfg.l = cfg.l || 0, id = cfg.id || Runner.genId();
		// deal with DOM element
		}else{
			var el = cfg;
			id = Runner.genId();			
		}
			
		// create iframe jQuery obj
		var iframe = $('#'+id);		
		if (!iframe.length){
			$(document).find('body').append('<iframe src="javascript:false;" id="'+id+'" frameborder="1" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no" style="background:white;position:absolute;display:block;opacity:0;filter:alpha(opacity=0);"></iframe>');
			//$(document).find('body').append('<iframe id="'+id+'" frameborder="1" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no" style="border: 1px solid red;background:white;position:absolute;display:block;"></iframe>');			
			iframe = $('#'+id);
		}	
		// obj with methods
		var iframeObj = {
			/**
			 * Move iframe to coordinates
			 * @param {int} l
			 * @param {int} t
			 * @return {obj} iframe obj
			 */
			move: function(t, l){
				if (t !== undefined && l !== undefined){
					iframe.css('top', t+'px').css('left', l+'px');
				}
				return this;
			},
			/**
			 * Completely removes iframe from DOM
			 */
			destroy: function(){
				iframe.remove();
				return this;
			},
			/**
			 * Move iframe to coordinates
			 * @param {int} w
			 * @param {int} h
			 * @return {obj} iframe obj
			 */
			resize: function(w, h){
				if (w !== undefined && h !== undefined){
					iframe.css('height', h).css('width', w);
				}
				return this;
			},
			/**
			 * Hide iframe, for next use
			 */
			hide: function(){
				iframe.hide();
				return this;
			},
			/**
			 * Show iframe, with old coors
			 */
			show: function(){
				iframe.show();
				return this;
			},
			/**
			 * Move iframe to coordinates
			 * @cfg {int} l
			 * @cfg {int} t
			 * @cfg {int} w
			 * @cfg {int} h
			 * @return {obj} iframe obj
			 */
			reset: function(coors){	
				// use old coors, if not passed new
				coors = coors || this.getPos() || cfg;
				// set postion and size and show iframe
				this.move(coors.t, coors.l).resize(coors.w, coors.h).show();
				// add z-index for iframe
				Runner.getZindex(iframe);
				return this;
			},
			/**
			 * Calculates postion of iframe, when DOM element passed to constructor
			 * @method
			 * @return {obj} literal with coordinates
			 */
			getPos: function(){	
				// lazy init func
				if (!el){
					this.getPos = function(){return false;}
				}else{
					this.getPos = function(){
						var posObj = getAbsolutePosition(el), coors = {};
						coors.w = el.offsetWidth, coors.h = el.offsetHeight, coors.t = posObj.t, coors.l = posObj.l;
						return coors;
					}
				}
				this.getPos();
			}
		}
		// return object
		return iframeObj.reset();
	}	
	/**
	 * Another way to solve IE select element coverage.
	 * 
	 * @param {DOM} el
	 * @return {object}
	 */
	Runner.util.IEHelper.selectsHider = function(el){
		// init private vars
		var selToHide = [], elem = el;
		
		return {
			/**
			 * Method checks intersection of two element by there center coordinates and dimensions
			 */
			checkIntersection: function(selCoors, elCoors){
				if ((Math.abs((elCoors.x-selCoors.x))<=(elCoors.w+selCoors.w)/2) 
					&& (Math.abs((elCoors.y-selCoors.y))<=(elCoors.h+selCoors.h)/2)){
						return true; 
				}
			},
			/**
			 * Calcs center of element
			 * @param {object} coors literal with coordinates
			 */
			getCenter: function(coors){
				coors.x = coors.l+coors.w/2;
				coors.y = coors.t+coors.h/2;
				return coors;
			},
			/**
			 * Hide select that were found in last getSelects call
			 */
			hideSels: function(){
				for(var i=0;i<selToHide.length;i++){
					$(selToHide[i]).hide();
				}
				return this;
			},
			/**
			 * Show hidden selects that were found in last getSelects call
			 */
			showSels: function(){				
				for(var i=0;i<selToHide.length;i++){
					$(selToHide[i]).show();
				}
				return this;
			},
			/**
			 * Check all select for intersection
			 */
			getSelects: function(elPos){
				// init vars
				var elCoors = elPos || {}, selToCheck = $('select');
				// clear old array with selects
				selToHide = [];
				// if element coords not passed
				if (!elPos){
					var pos = findPos(el);
					// add 10px for better coverage 
					elCoors.l = pos[0];
					elCoors.t = pos[1];
					elCoors.w = el.offsetWidth;
					elCoors.h = el.offsetHeight;
				}
				// get center of element
				elCoors = this.getCenter(elCoors);
				// check each select
				var coors = {};
				for(var i=0; i<selToCheck.length;i++){
					// get select position, coordinates and dimension
					pos = findPos(selToCheck[i]);
					coors.l = pos[0];
					coors.t = pos[1];
					coors.w = selToCheck[i].offsetWidth;
					coors.h = selToCheck[i].offsetHeight;
					coors = this.getCenter(coors);
					// check intersection
					if (this.checkIntersection(coors, elCoors)){
						selToHide.push(selToCheck[i]);
					}
				}
				//console.log(selToHide);
				// return array to hide
				return selToHide;
			}			
		}	
	}
//} 