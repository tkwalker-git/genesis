(function(){
	
	var createDelayed = function(hn, obj, scope){
        return function(){
            var argsArr = Array.prototype.slice.call(arguments, 0);
            setTimeout(function(){
                hn.apply(scope, argsArr);
            }, obj.delay || 10);
        };
    };
    
    var createSingle = function(hn, e, fn, scope){
        return function(){
            e.removeListener(fn, scope);
            return hn.apply(scope, arguments);
        };
    };    
    
    var createBuffered = function(hn, obj, scope){
        var task = new Runner.util.DelayedTask();
        return function(){
            task.delay(obj.buffer, hn, scope, Array.prototype.slice.call(arguments, 0));
        };
    };

    Runner.util.Event = function(obj, name){
        this.name = name;
        this.obj = obj;
        this.listeners = [];
    };

    Runner.util.Event.prototype = {
    	
    	createListener: function(fn, scope, obj){
            obj = obj || {};
            scope = scope || this.obj;
            var ls = {
            	fn: fn, 
            	scope: scope, 
            	options: obj
            };
            var hn = fn;
            if(obj.delay){
                hn = createDelayed(hn, obj, scope);
            }
            if(obj.single){
                hn = createSingle(hn, this, fn, scope);
            }
            if(obj.buffer){
                hn = createBuffered(hn, obj, scope);
            }
            ls.fireFn = hn;
            return ls;
        },

        getListenerIndex: function(fn, scope){
            scope = scope || this.obj;
            var length = this.listeners.length,
            	ls;
            for(var i = 0; i < length; i++){
                ls = this.listeners[i];
                if(ls.fn == fn && ls.scope == scope){
                    return i;
                }
            }
            return -1;
        },        
        
    	fire: function(){
            var scope,
            	ls,
            	length = this.listeners.length;
            	
            if(length > 0){
                this.firing = true;
                var argsArr = Array.prototype.slice.call(arguments, 0);
                for(var i = 0; i < length; i++){
                    ls = this.listeners[i];
                    if(ls.fireFn.apply(ls.scope || this.obj || window, arguments) === false){
                        this.firing = false;
                        return false;
                    }
                }
                this.firing = false;
            }
            return true;
        },
        
        on: function(fn, scope, options){
            scope = scope || this.obj;
            if(!this.isListening(fn, scope)){
                ls = this.createListener(fn, scope, options);
                if(!this.firing){
                    this.listeners.push(ls);
                }else{ 
                    this.listeners = this.listeners.slice(0);
                    this.listeners.push(ls);
                }
            }
        },       

        isListening: function(fn, scope){
            return this.getListenerIndex(fn, scope) != -1;
        },

        removeListener: function(fn, scope){
            var index;
            if((index = this.getListenerIndex(fn, scope)) != -1){
                if(!this.firing){
                    this.listeners.splice(index, 1);
                }else{
                    this.listeners = this.listeners.slice(0);
                    this.listeners.splice(index, 1);
                }
                return true;
            }
            return false;
        },

        clearListeners: function(){
            this.listeners = [];
        }        
    };
})();


/**
 * @class Runner.util.Observable
 * Observer-subscriber class
 */
Runner.util.Observable = Runner.extend(Runner.emptyFn, {
	
	filterOptRe: /^(?:scope|delay|buffer|single)$/,
	
	addEvents: function(obj){
        if(!this.events){
            this.events = {};
        }
        if(typeof obj == 'string'){
            for(var i = 0, a = arguments, v; v = a[i]; i++){
                if(!this.events[a[i]]){
                    this.events[a[i]] = true;
                }
            }
        }else{
            Runner.apply(this.events, obj);
        }
    },
	
    fireEvent: function(){
        if(this.eventsSuspended !== true){
            var ce = this.events[arguments[0].toLowerCase()];
            if(typeof ce == "object"){
                return ce.fire.apply(ce, Array.prototype.slice.call(arguments, 1));
            }
        }
        return true;
    },

    on: function(evName, fn, scope, obj){
        if(typeof evName == "object"){
            obj = evName;
            for(var event in obj){
                if(this.filterOptRe.test(event)){
                    continue;
                }
                if(typeof obj[event] == "function"){
                    this.on(event, obj[event], obj.scope,  obj);
                }else{
                    this.on(event, obj[event].fn, obj[event].scope, obj[event]);
                }
            }
            return;
        }
        obj = (!obj || typeof obj == "boolean") ? {} : obj;
        evName = evName.toLowerCase();
        var ce = this.events[evName] || true;
        if(typeof ce == "boolean"){
            ce = new Runner.util.Event(this, evName);
            this.events[evName] = ce;
        }
        ce.on(fn, scope, obj);
    },

    un: function(evName, fn, scope){
        var ce = this.events[evName.toLowerCase()];
        if(typeof ce == "object"){
            ce.removeListener(fn, scope);
        }
    },

    purgeListeners: function(){
        for(var event in this.events){
            if(typeof this.events[event] == "object"){
                 this.events[event].clearListeners();
            }
        }
    },    
   
    suspendEvents: function(){
        this.eventsSuspended = true;
    },

    resumeEvents: function(){
        this.eventsSuspended = false;
    }
});

