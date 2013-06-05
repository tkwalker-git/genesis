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