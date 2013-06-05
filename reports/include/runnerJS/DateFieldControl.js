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
	
	dataPicker: null,
	
	timeBox: null,
	
	dateDelimiter: "/",
	
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
		
		this.dateFormat = typeof cfg.dateFormat != "undefined" ? cfg.dateFormat : Runner.pages.PageSettings.getGlobalData("locale").dateFormat;
		this.dateDelimiter = Runner.pages.PageSettings.getGlobalData("locale")["dateDelimiter"];
		
		this.showTime = Runner.pages.PageSettings.getFieldData(this.table, this.fieldName, "showTime", false);
		
		// add hidden field for date format on serverside
		this.dateFormatHiddId = "type"+(cfg.ctrlInd || "")+"_"+this.goodFieldName+"_"+this.id;
		this.dateFormatHiddElem = $("#"+this.dateFormatHiddId);	
		
		this.initDataPicker();		
	},	
	
	initDataPicker: function(){
		if(!this.useDatePicker){
			this.initDataPicker = Runner.emptyFn;
			return false;			
		}
		
		this.imgCal = $('#imgCal_'+this.valContId);
		this.datePickerHiddId = "tsvalue"+(this.ctrlInd || "")+"_"+this.goodFieldName+"_"+this.id;
		this.datePickerHiddElem = $("#"+this.datePickerHiddId);
		
		// for closure
 		var dateControl = this; 		
 	    // YUI init code   
        this.imgCal.bind("click", function(e){         	
			(function(e){
				Runner.Event.prototype.stopEvent(e);
				// Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners, until the first time the button is clicked.
	            if (!this.dataPicker) {
	            	// Hide Calendar if we click anywhere in the document other than the calendar
	            	YAHOO.util.Event.on(document, "click", function(e) {
	 					(function(e){	
				            var el = YAHOO.util.Event.getTarget(e);
				            var dialogEl = this.dataPicker.element;
				            if (el != this.dataPicker.dataPickerEl && !YAHOO.util.Dom.isAncestor(dialogEl, el) && el != this.imgCal.get(0) && !YAHOO.util.Dom.isAncestor(this.imgCal.get(0), el)) {
				                this.dataPicker.hide();
				            }	
	 					}).createDelegate(dateControl, [e])();					
			        });
	 
	                
	                function resetHandler() {
	                    // Reset the current calendar page to the select date, or 
	                    // to today if nothing is selected.
	                    var selDates = this.calendar.getSelectedDates();
	                    var resetDate;
	        
	                    if (selDates.length > 0) {
	                        resetDate = selDates[0];
	                        var dt = this.print_datetime(resetDate, this.dateFormat);
	                        var time = dt.split(" ")[1];
	                        //var time = resetDate.getHours()+":"+resetDate.getMinutes()+":"+resetDate.getSeconds();
	                        this.timeBox.val(time);
	                    } else {
	                        resetDate = this.calendar.today;
	                    }
	        			
	                    this.calendar.cfg.setProperty("pagedate", resetDate);
	                    this.calendar.render();
	                }
	        
	                function closeHandler() {
	                	 this.dataPicker.hide();                   
	                }
	 
	                this.dataPicker = new YAHOO.widget.Dialog("container"+this.id, {
	                    visible:false,
	                    context:["show", "tl", "bl"],
	                    buttons:[ 
	                    	{text:"Reset", handler: resetHandler.createDelegate(dateControl), isDefault:true}, 
	                    	{text:"Close", handler: closeHandler.createDelegate(dateControl)}
	                    ],
	                    draggable: true,
	                    close:true,
	                    constraintoviewport: true, 
	        			fixedcenter: true,
	        			modal: true
	                });
	                
	                this.dataPicker.setBody('<div id="cal'+this.id+'"></div>');
	                this.dataPicker.render(document.body);
	                // show time edit box
					if(this.showTime){
		                var timeSpan = $(document.createElement('DIV')).css("text-align", "center");
		                this.timeBox = $(document.createElement('INPUT')).
		                	attr('type', 'text').
		                		attr('size', '10').
		                			attr('id', 'timeBox'+this.id).
										attr('maxlength',8).
											css("margin-bottom", "8px").
												appendTo(timeSpan);
		                $(this.dataPicker.footer).prepend(timeSpan);
					}else{
						this.timeBox = $('#timeBox'+this.id);
					}
					
	                this.dataPicker.showEvent.subscribe(function() {
	                    if (Runner.isIE) {
	                        this.dataPicker.fireEvent("changeContent");
	                    }
	                }, this, this);
	            }
	            // Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
	            if (!this.calendar) {
	 
	                this.calendar = new YAHOO.widget.Calendar("cal"+this.id, {
	                    iframe:false,        
	                    hide_blank_weeks:true,
	                    LOCALE_WEEKDAYS: "short",
	                    START_WEEKDAY: 1
	                });
	                
	                this.calendar.render();
	 
	                this.calendar.selectEvent.subscribe(function() {
	                    if (this.calendar.getSelectedDates().length > 0) {	 						
	                        var selDate = this.calendar.getSelectedDates()[0];
	                        var selTime = this.getTime(); 
	                        if (selTime){
	                        	var timeArr = selTime.split(":");
	                        	selDate.setHours(timeArr[0]);
	                        	selDate.setMinutes(timeArr[1]);
	                        	selDate.setSeconds(timeArr[2]);
	                        }
	                        this.setValue(selDate, true);
	                    } else {
	                       this.setValue(false, true);
	                    }
	                    this.dataPicker.hide();
	                }, this, this);
	 
	                this.calendar.renderEvent.subscribe(function() {
	                    this.dataPicker.fireEvent("changeContent");
	                }, this, this);
	                
	                this.localizeCalendar();
	            }
	 
	            // Set the pagedate to show the selected date if it exists
                this.calendar.cfg.setProperty("pagedate", this.getValue());
                this.calendar.render();
	 
	            this.dataPicker.show();
	            
	            this.setTime(this.getValue());
	            
	    	}).call(dateControl, e);  
        });
	},

	localizeCalendar: function(){
		if (!this.calendar){
			return false;	
		}
		
		this.calendar.cfg.setProperty("DATE_FIELD_DELIMITER", this.dateDelimiter);

		if(this.dateFormat == -1){
			this.calendar.cfg.setProperty("MDY_DAY_POSITION", 1);
			this.calendar.cfg.setProperty("MDY_MONTH_POSITION", 2);
			this.calendar.cfg.setProperty("MDY_YEAR_POSITION", 3);
		}else if(this.dateFormat == 1){
			this.calendar.cfg.setProperty("MDY_DAY_POSITION", 1);
			this.calendar.cfg.setProperty("MDY_MONTH_POSITION", 2);
			this.calendar.cfg.setProperty("MDY_YEAR_POSITION", 3);
		}else if(this.dateFormat == 0){
			this.calendar.cfg.setProperty("MDY_DAY_POSITION", 2);
			this.calendar.cfg.setProperty("MDY_MONTH_POSITION", 1);
			this.calendar.cfg.setProperty("MDY_YEAR_POSITION", 3);
		}else{
			this.calendar.cfg.setProperty("MD_DAY_POSITION", 2);
			this.calendar.cfg.setProperty("MD_MONTH_POSITION", 1);
		}
		
		this.calendar.cfg.setProperty("MONTHS_SHORT", [Runner.lang.constants.TEXT_MONTH_JAN, Runner.lang.constants.TEXT_MONTH_FEB, Runner.lang.constants.TEXT_MONTH_MAR, Runner.lang.constants.TEXT_MONTH_APR, Runner.lang.constants.TEXT_MONTH_MAY, Runner.lang.constants.TEXT_MONTH_JUN, Runner.lang.constants.TEXT_MONTH_JUL,  Runner.lang.constants.TEXT_MONTH_AUG,  Runner.lang.constants.TEXT_MONTH_SEP,  Runner.lang.constants.TEXT_MONTH_OCT,  Runner.lang.constants.TEXT_MONTH_NOV,  Runner.lang.constants.TEXT_MONTH_DEC]);
		this.calendar.cfg.setProperty("MONTHS_LONG", [Runner.lang.constants.TEXT_MONTH_JAN, Runner.lang.constants.TEXT_MONTH_FEB, Runner.lang.constants.TEXT_MONTH_MAR, Runner.lang.constants.TEXT_MONTH_APR, Runner.lang.constants.TEXT_MONTH_MAY, Runner.lang.constants.TEXT_MONTH_JUN, Runner.lang.constants.TEXT_MONTH_JUL,  Runner.lang.constants.TEXT_MONTH_AUG,  Runner.lang.constants.TEXT_MONTH_SEP,  Runner.lang.constants.TEXT_MONTH_OCT,  Runner.lang.constants.TEXT_MONTH_NOV,  Runner.lang.constants.TEXT_MONTH_DEC]);
		
		this.calendar.cfg.setProperty("WEEKDAYS_1CHAR", [Runner.lang.constants.TEXT_DAY_SU, Runner.lang.constants.TEXT_DAY_MO, Runner.lang.constants.TEXT_DAY_TU, Runner.lang.constants.TEXT_DAY_WE, Runner.lang.constants.TEXT_DAY_TH, Runner.lang.constants.TEXT_DAY_FR, Runner.lang.constants.TEXT_DAY_SA]);
		this.calendar.cfg.setProperty("WEEKDAYS_SHORT", [Runner.lang.constants.TEXT_DAY_SU, Runner.lang.constants.TEXT_DAY_MO, Runner.lang.constants.TEXT_DAY_TU, Runner.lang.constants.TEXT_DAY_WE, Runner.lang.constants.TEXT_DAY_TH, Runner.lang.constants.TEXT_DAY_FR, Runner.lang.constants.TEXT_DAY_SA]);
		this.calendar.cfg.setProperty("WEEKDAYS_MEDIUM", [Runner.lang.constants.TEXT_DAY_SU, Runner.lang.constants.TEXT_DAY_MO, Runner.lang.constants.TEXT_DAY_TU, Runner.lang.constants.TEXT_DAY_WE, Runner.lang.constants.TEXT_DAY_TH, Runner.lang.constants.TEXT_DAY_FR, Runner.lang.constants.TEXT_DAY_SA]);
		this.calendar.cfg.setProperty("WEEKDAYS_LONG", [Runner.lang.constants.TEXT_DAY_SU, Runner.lang.constants.TEXT_DAY_MO, Runner.lang.constants.TEXT_DAY_TU, Runner.lang.constants.TEXT_DAY_WE, Runner.lang.constants.TEXT_DAY_TH, Runner.lang.constants.TEXT_DAY_FR, Runner.lang.constants.TEXT_DAY_SA]);
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
		Runner.controls.DateField.superclass.addValidation.call(this, type);
	},
	
	parseTime: function(dtObj) {
		if (typeof dtObj == "object"){
			return (
				(dtObj.getHours() < 10 ? '0' : '') + dtObj.getHours() + ":"
				+ (dtObj.getMinutes() < 10 ? '0' : '') + (dtObj.getMinutes()) + ":"
				+ (dtObj.getSeconds() < 10 ? '0' : '') + (dtObj.getSeconds())
			);	
		}else{
			return "";	
		}		
	},
	
	getTime: function(){
		if (this.showTime){
			return this.timeBox.val();	
		}else{
			return "";
		}
		
	},
	
	setTime: function(dtObj){
		if (this.showTime){
			if(dtObj)
				this.timeBox.val(this.parseTime(dtObj));
			else
				this.timeBox.val('00:00:00');
		}		
	},
	
	setValue: function(newDate, triggerEvent){
		if (typeof newDate == "string"){
			return parse_datetime(newDate, this.dateFormat);	
		}else{
			return newDate;
		}
	},
	/**
	 * format: -1 - native (d-m-y)
				1 - d/m/y
				0 - m/d/y
				2 - y/m/d
	 * @param {} value
	 * @param {} format
	 * @return {}
	 */
	print_datetime: function(value, format){		
		var date='';
			
		if(format==-1)
			date+=(value.getDate()<10?'0'+value.getDate():value.getDate())+'-'+(value.getMonth()<9?'0'+(value.getMonth()+1):value.getMonth()+1)+'-'+value.getFullYear();
		else if(format==1)
			date+=(value.getDate()<10?'0'+value.getDate():value.getDate())+this.dateDelimiter+(value.getMonth()<9?'0'+(value.getMonth()+1):value.getMonth()+1)+this.dateDelimiter+value.getFullYear();
		else if(format==0)
			date+=(value.getMonth()<9?'0'+(value.getMonth()+1):value.getMonth()+1)+this.dateDelimiter+(value.getDate()<10?'0'+value.getDate():value.getDate())+this.dateDelimiter+value.getFullYear();
		else
			date+=value.getFullYear()+this.dateDelimiter+(value.getMonth()<9?'0'+(value.getMonth()+1):value.getMonth()+1)+this.dateDelimiter+(value.getDate()<10?'0'+value.getDate():value.getDate());
		if(!this.showTime)
			return date;
			
		var time='';
		if(value.getHours()>0 || value.getMinutes()>0 || value.getSeconds()>0){
			time+=(value.getHours()<10?'0'+value.getHours():value.getHours());
			time+=':'+(value.getMinutes()<10?'0'+value.getMinutes():value.getMinutes())
		}
		if(value.getSeconds()>0)
			time+=':'+(value.getSeconds()<10?'0'+value.getSeconds():value.getSeconds());
		
		return date+' '+time;
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
		newDate = Runner.controls.DateTextField.superclass.setValue.call(this, newDate, triggerEvent);	
		// if we pass Date object, so we use it
		if (typeof newDate == 'object'&&newDate!=null){
			// call old date parse function, they will change in future
			var dt = this.print_datetime(newDate, this.dateFormat);
			//set value in edit textfield
			this.valueElem.val(dt);
			// if we need to set new date in hidden fields for datepicker
			if (this.useDatePicker){
				dt = this.print_datetime(newDate, -1);
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
	"blur": function(e){
		// call parent
		this.stopEvent(e);
		this.focusState = false;		
		if (!this.invalid()){
			return;
		}		
		var vRes = this.validate();
		// set values to hidden fields
		if (vRes.result && this.useDatePicker  && this.getValue()){
			this.setValue(this.getValue());
		}
	},
	/**
	 * Sets disable attr true
	 * Should be overriden for sophisticated controls
	 * @method
	 */
	setDisabled: function(){
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
	setEnabled: function(){
		if (this.valueElem.get(0)){
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
		this.hiddElemId = this.valContId;
		this.hiddValueElem = $("#"+this.hiddElemId);
		// add input type attr
		this.inputType = "3dd";
		// if allready have constants, than fill combos
		if (Runner.lang.constants.TEXT_MONTH_JAN){
			this.addYearOptions(cfg.yearVal);
			this.addMonthOptions(cfg.monthVal);
			this.addDayOptions(cfg.dayVal);
		}
		
		var dateObj = parse_datetime(this.hiddValueElem.val(), 2);
		this.setValue(dateObj);
	},	
	
	destructor: function(){
		// call parent
		Runner.controls.DateDropDown.superclass.destructor.call(this);
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
		monthNames[1] = Runner.lang.constants.TEXT_MONTH_JAN;
		monthNames[2] = Runner.lang.constants.TEXT_MONTH_FEB;
		monthNames[3] = Runner.lang.constants.TEXT_MONTH_MAR;
		monthNames[4] = Runner.lang.constants.TEXT_MONTH_APR;
		monthNames[5] = Runner.lang.constants.TEXT_MONTH_MAY;
		monthNames[6] = Runner.lang.constants.TEXT_MONTH_JUN;
		monthNames[7] = Runner.lang.constants.TEXT_MONTH_JUL;
		monthNames[8] = Runner.lang.constants.TEXT_MONTH_AUG;
		monthNames[9] = Runner.lang.constants.TEXT_MONTH_SEP;
		monthNames[10] = Runner.lang.constants.TEXT_MONTH_OCT;
		monthNames[11] = Runner.lang.constants.TEXT_MONTH_NOV;
		monthNames[12] = Runner.lang.constants.TEXT_MONTH_DEC;
		
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
	setValue: function(newDate, triggerEvent){	
		newDate = Runner.controls.DateTextField.superclass.setValue.call(this, newDate, triggerEvent);	
		// if we pass Date object, so we use it
		if(typeof newDate == 'object' && newDate!=null){
			this.hiddValueElem.get(0).value =  newDate.getFullYear() + '-' + (newDate.getMonth()+1) + '-' + newDate.getDate();		
			
			this.valueElem["day"].get(0).selectedIndex = newDate.getDate();
			
			this.valueElem["month"].get(0).selectedIndex = newDate.getMonth()+1;
			
			for(var i=0; i<this.valueElem["year"].get(0).options.length;i++){
				if(this.valueElem["year"].get(0).options[i].value==newDate.getFullYear()){
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
	setDisabled: function(){		
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
	setEnabled: function(){
		this.valueElem["day"][0].disabled = false;
		this.valueElem["month"][0].disabled = false;
		this.valueElem["year"][0].disabled = false;
		if(this.imgCal!=null){	
			this.imgCal.css('visibility','visible');
		}
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

