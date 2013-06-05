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
						validationRes = Runner.lang.constants.TEXT_INLINE_FIELD_REQUIRED;
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
			return Runner.lang.constants.TEXT_INLINE_FIELD_REQUIRED;
		}
		else
			return true;
			
	},
	
	"IsNumeric": function(sVal)
	{
		sVal = sVal.replace(/,/g,"");
		if(isNaN(sVal)) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_NUMBER;
		else
			return true;
	},
//	
	"IsPassword": function(sVal)
	{
		var regexp1 = /^password$/;
		var regexp2 = /.{4,}/;
		if(sVal.match(regexp1))
			return Runner.lang.constants.TEXT_INLINE_FIELD_PASSWORD1;
		else if(!sVal.match(regexp2)) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_PASSWORD2;		
		else
			return	true;	
	},

	"IsEmail": function(sVal)
	{
		var regexp = /^[A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_EMAIL;
		else
			return true;
	}, 
//	
	"IsMoney": function(sVal)
	{
		var regexp = /^(\d*)\.?(\d*)$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_CURRENCY;
		else
			return true;	
	},  
//	
	"IsZipCode": function(sVal)
	{
		var regexp = /^\d{5}([\-]\d{4})?$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_ZIPCODE;
		else
			return true;	
	},
//	
	"IsPhoneNumber": function(sVal)
	{
		var regexp = /^\(\d{3}\)\s?\d{3}\-\d{4}$/;		
		var stripped = sVal.replace(/[\(\)\.\-\ ]/g, '');    
		if(sVal.match(/.+/) && (isNaN(parseInt(stripped)) || stripped.length != 10) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_PHONE;
		else
			return true;
	},
//	
	"IsState": function(sVal)
	{
		if(sVal.match(/.+/) && !arrStates.inArray(sVal,false) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_STATE;
		else
			return true;
	}, 
//	
	"IsSSN": function(sVal)
	{
		// 123-45-6789 or 123 45 6789
		var regexp = /^\d{3}(-|\s)\d{2}(-|\s)\d{4}$/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_SSN;
		else
			return true;
	},
//	
	"IsCC": function(sVal)
	{
		//Visa, Master Card, American Express
		var regexp = /^((4\d{3})|(5[1-5]\d{2}))(-?|\040?)(\d{4}(-?|\040?)){3}|^(3[4,7]\d{2})(-?|\040?)\d{6}(-?|\040?)\d{5}/;
		if(sVal.match(/.+/) && !sVal.match(regexp) ) 
			return Runner.lang.constants.TEXT_INLINE_FIELD_CC;
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
			return Runner.lang.constants.TEXT_INLINE_FIELD_TIME;
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
			return Runner.lang.constants.TEXT_INLINE_FIELD_DATE;	
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