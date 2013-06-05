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