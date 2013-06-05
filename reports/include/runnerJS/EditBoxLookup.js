
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
	"keyup": {
        fn: function(e){		
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
				// do request for suggest div data
				this.lookupAjax();
			}		
		},
        options: {
        	buffer: 200
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
	showDiv: function(divsArr){
		this.destroyDiv();
		// create div with html
		this.lookupDiv = $(document.createElement("DIV"));
		this.lookupDiv.
			attr("id", this.lookupDivId).
				addClass("search_suggest").
					css("visibility", "visible").
						appendTo(document.body);
							
		for(var i=0; i<divsArr.length; i++){
			this.lookupDiv.append(divsArr[i]);
		}
		// create iframe for IE6
		if (Runner.isIE6){
			$(document.body).append('<iframe src="javascript:false;" id="'+this.lookupIframeId+'" frameborder="1" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no" style="background:white;position:absolute;display:block;opacity:0;filter:alpha(opacity=0);"></iframe>');	
			this.lookupIframe = $("#"+this.lookupIframeId);
		}		
		// set div coors
		this.setDivPos();	
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
		//console.log(coors, this.getDispElem().position());
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
		$.get("lookupsuggest.php", ajaxParams, function(respObj, textStatus){	
			respObj = JSON.parse(respObj);
			// if data is empty than add red frame
			if (!respObj.success || !respObj.data.length){
				// make error if no hidden value
				this.isLookupError = true;
				if (ctrl.focusState){
					ctrl.addCSS("highlight");
				}
				return false;
			}
			// prepare vars
			var data = respObj.data, hiddVal = ctrl.getValue(), dispVal = ctrl.getDisplayValue(), valArr = [ctrl.getValue(),ctrl.getDisplayValue()];
			// if values correct, in recieved data exist looup hidden value
			if (hiddVal!="" && data.isInArray(hiddVal)){
				// remove error and highlight
				this.isLookupError = false;
				ctrl.removeCSS("highlight");
				// change new value pair and fire event
				$.each(data, function(i, n){
					if((n.toLowerCase()==valArr[1]) && (data[i-1]!=valArr[0])) {
						// setValue with firing change event which will call reloadDependences
						ctrl.setValue(valArr[1], data[i-1], true);
					}
				});
			}	
			ctrl.suggestValues = [];
			ctrl.lookupValues = [];
			// get suggest and lookup values, concatinate suggest div inner html
			var divsArr = [];				
			for(var i=0, j=0; i < data.length-1; i=i+2,j++) {
				(function(i, j){
					// div html, value and event handlers
					var suggestDiv = $(document.createElement("DIV")).attr("id", "suggestDiv"+i).css("cursor", "pointer").addClass("suggest_link").html(data[i+1]).
						bind("mouseover", function(){
							ctrl.suggestOver(this);
						}).bind("mouseout", function(){
							ctrl.suggestOut(this);
						}).bind("click", function(){
							ctrl.isLookupError = false;
							ctrl.removeCSS('highlight');
							ctrl.setValue(ctrl.suggestValues[j], data[i], true);
							ctrl.destroyDiv();
						});		
				
					divsArr.push(suggestDiv);	
				})(i, j);
				// change data in arrays
				ctrl.suggestValues[j] = data[i+1];
				ctrl.lookupValues[j] = data[i];
			}
			// show div
			ctrl.showDiv(divsArr);
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
			this.focusState = false;		
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
