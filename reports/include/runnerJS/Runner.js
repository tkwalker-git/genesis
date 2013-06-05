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
var Runner = {version: '2.0'};
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
	var zIndexMax = 0;
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
    var isSecure = (window.location.href.toLowerCase().indexOf("https") === 0);
        
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
			return ++idCounter;
		},
		
		setIdCounter: function(num){
			idCounter += ++num;
		},
		
		getZindex: function(elObj){			
			if (elObj){
				elObj.css("z-index", zIndexMax);				
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
		
		isArray: function(toCheck){
			return toCheck && typeof toCheck.splice == 'function' && typeof toCheck.length == 'number';
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
        isChrome : isChrome,        
        isSecure : isSecure
        
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


Runner.apply(Function.prototype, {
    createCallback : function(){
        var args = arguments;
        var func = this;
        return function() {
            return func.apply(window, args);
        };
    },

    createDelegate : function(obj, args, appendArgs){
        var func = this;
        return function() {
            var callArgs = args || arguments;
            if(appendArgs === true){
                callArgs = Array.prototype.slice.call(arguments, 0);
                callArgs = callArgs.concat(args);
            }else if(typeof appendArgs == "number"){
                callArgs = Array.prototype.slice.call(arguments, 0); 
                var applyArgs = [appendArgs, 0].concat(args); 
                Array.prototype.splice.apply(callArgs, applyArgs); 
            }
            return func.apply(obj || window, callArgs);
        };
    }
});