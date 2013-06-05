// register new namespace
Runner.namespace('Runner.util');

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
					continue;
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
		
		load: function(hn, scope){
			if (typeof hn == 'function'){			
				this.afterLoadHn = {
					hn: hn,
					scope: scope
				}
			}
			// load all js files
			for(var i=0;i<this.jsFiles.length;i++){
				this.loadJS(i);		
			}			
		},
		/**
		 * Load file from queue
		 * @param {int} idx file index
		 * @return {bool} true if success
		 * @method
		 * @private
		 */
		loadJS: function(idx){
			// return if no file obj for this file
			if (!this.jsFiles[idx]){
				return false;
			}
			// if loaded, load dependent files
			if(this.jsFiles[idx].isLoaded){
				this.postLoad(idx);
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
						sl.postLoad(idx);
					}
				};
			}else{
				js.onload = function() {	
					sl.postLoad(idx);
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
		postLoad: function(idx){
			this.jsFiles[idx].isLoaded = true;			
			this.loadDependent(idx);
		},
		/**
		 * Call load for files, which are dependent to file with index = idx
		 * @param {int} idx
		 */
		loadDependent: function(idx){
			// loop through all files
			var loadedAll = true;
			for(var i=0;i<this.jsFiles.length;i++){
				// loop through all req
				for(var j=0;j<this.jsFiles[i].requirements.length;j++){
					// if req cotains loaded file, than try to load it
					if (i != idx && this.jsFiles[i].requirements[j] == this.jsFiles[idx].name){ 
						this.loadJS(i);
					}
				}
				if (!this.jsFiles[i].isLoaded){
					loadedAll = false;
				}
			}
						
			if (Runner.pages && loadedAll){
				Runner.pages.PageManager.initPages();
				
			}
			if (this.afterLoadHn && loadedAll){
				this.afterLoadHn.hn.call(this.afterLoadHn.scope || window);
				this.afterLoadHn = null;
			}
			
		}
	}
}();