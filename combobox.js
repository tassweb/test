/* **** ARRAY EXTENSION FOR NON-SUPPORTING BROWSERS **** */
if(typeof Array.prototype.push=='undefined') {
	Array.prototype.push = function () {
		var i=0,
			b=this.length,
			a=arguments;
		for(i;i<a.length;i++) {
			this[b+i]=a[i];
		}
		return this.length
	}
}
/* **** STRING EXTENSION FOR PUNCTUATION **** */
if (typeof(String.fromCharCode) == 'undefined') {
	String.fromCharCode = function () {
		if (arguments.length = 0) {
			return "";
		}
		var charCodeChars = new Array(32),
			returnString = "",
			i;
		charCodeChars[9] = '\t';
		charCodeChars[13] = '\n';
		charCodeChars.push(' ','!','"','#','$','%','',"'",'(',')','*','+',',','-','.','/','0','1','2','3','4','5','6','7','8','9',':',';','<','=','>','?','@');
		charCodeChars.push('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','[','\\',']','^','_','`');
		charCodeChars.push('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','{','|','}','~');
		for (i=0;arguments.length>i;i++) {
			returnString += charCodeChars[arguments[i]];
		}
		return returnString;
	}
}
String.fromKeyCode = function (keyCode,evtType) {
	if (!evtType || !evtType.length) {
		evtType = "keyDown";
	} else if (evtType.toLowerCase() == "keypress") {
		return String.fromCharCode(keyCode);
	}
	var keyDownChars = new Array(16);
		keyDownChars[8] = '[Bksp]';
		keyDownChars[9] = '[Tab]';
		keyDownChars[12] = '[N5+shift]';
		keyDownChars[13] = '[Enter]';
		keyDownChars.push('[Shift]','[Ctrl]','[Alt]','[Pause]','[CapsLock]');
		for (i=11;i;--i) {
			keyDownChars.push('undefined');
		}
		keyDownChars[27] = '[Esc]';
		keyDownChars.push(' ','[PgUp]','[PgDn]','[End]','[Home]','[Left]','[Up]','[Right]','[Down]');
		for (i=7;i;--i) {
			keyDownChars.push('undefined');
		}
		keyDownChars[45] = '[Ins]';
		keyDownChars[46] = '[Del]';
		keyDownChars.push(['0',')'],['1','!'],['2','@'],['3','#'],['4','$'],['5','%'],['6','^'],['7','&'],['8','*'],['9','(']);
		for (i=7;i;--i) {
			keyDownChars.push('undefined');
		}
		keyDownChars.push('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','[WinKey]');
		for (i=4;i;--i) {
			keyDownChars.push('undefined');
		}
		keyDownChars.push('0','1','2','3','4','5','6','7','8','9','*','+','undefined','-','.','/','[F1]','[F2]','[F3]','[F4]','[F5]','[F6]','[F7]','[F8]','[F9]','[F10]','[F11]','[F12]');
		for (i=62;i;--i) {
			keyDownChars.push('undefined');
		}
		keyDownChars[144] = '[NumLock]';
		keyDownChars[145] = '[ScrollLock]';
		keyDownChars.push([';',':'],['=','+'],[',','<'],['-','_'],['.','>'],['/','?'],['`','~']);
		for (i=26;i;--i) {
			keyDownChars.push('undefined')
		}
		keyDownChars.push(['[','{'],['\\','|'],[']','}'],["'",'"']);
	return keyDownChars[keyCode];
}

/* **** COMBOBOX CODE **** */
/**************************************************
Original Version (1.0):
Glenn G. Vergara
http://www21.brinkster.com/gver/
glenngv AT yahoo DOT com
Makati City, Philippines

Object-Based Version:
Eric C. Davis
http://www.10mar2001.com/
eric AT 10mar2001 DOT com
Atlanta, GA, US

(Keep the above intact if you want to use it! Thanks.)

Current Version: 2.5b
Last Update: 1 December 2003

********
Change Log:
New in version 2.5b:
	- Reversed selectItem() loop to prevent default IE6/Win rapid-change behaviour (skipped to second on match of first)
	- Outfitted for DOM2-style event handling; uses detection and falls back to DOM0 events
	- Resets immediately on ALT+TAB to prevent IE6/Win's loss of the reset timer.

New in version 2.4:
	- Added accepting of non-existent option
	- Added punctuation as acceptable input
	- Added setValueByValue() convenience method

New in version 2.2:
	- Many properties made private
	- Getters and setters for nearly all properties

New in version 2.0:
	- Object-oriented properties and methods using prototype
	- Constructor can accept a select element object or a select element object's ID string
	- Invocation reduced to single line of script: varName = new TypeAheadCombo('selectElementID');

New in version 1.4:
	- Allowable character set ranges use dynamic evaluation
	- Display of typed characters in status bar can be disabled

New in version 1.2:
	- Replaced major if/elseif/.../else statement with switch/case
	- Correction of characters typed on the numpad, reassigning to actual character values
********

********
API:
Constructor:
	new TypeAheadCombo(someSelectElement) // as an object or object reference
	new TypeAheadCombo('someSelectElementID') // as a string
	new TypeAheadCombobox('someSelectElementID', true) // to allow an undefined value

Privileged Methods: (these interact with private properties and act as helper functions)
	getTyped()
		- returns the string typed by the user since the last timeout
	setTyped(str)
		- argument "str" - string which will replace the value in the type buffer
	type(str)
		- argument "str" - string which will be appended to the type buffer
	resetTyped()
		- clears what has been typed from the buffer
	getIndex()
		- returns the location of the option currently selected
	setIndex(val)
		- stores the location of the option being selected
	getPrev()
		- returns the location of the option previously selected
	setPrev(val)
		- stores the location of the option previously selected
	setResetTime(val)
		- sets the timeout interval for the reset timers
	getResetTime()
		- returns the timeout interval for the reset timers
	setResetTimer()
		- sets the timeout for the reset of the typed buffer
	clearResetTimer()
		- clears the timeout of the reset of the typed buffer
	validChar(charCode)
		- validates that the charCode passed is acceptable to the typed buffer
	setDisplayStatus(bool)
		- set whether to display the typed buffer in the status bar
	getDisplayStatus()
		- returns the current setting for status bar display of the typed buffer

Public Methods:
	detectKey()
		- detects the keyCode, parses whether it is acceptable, and adds it to the typed buffer if so
	selectItem()
		- finds the first option that matches the typed buffer and selects it
	reset()
		- clears the typed buffer and the status display
	updateIndex()
		- handles the onclick and onblur events
	elementFocus()
		- handles the onfocus event
	elementKeydown()
		- handles the onkeydown event
********

***************************************************/
function TypeAheadCombo (anElement,acceptNewValue) {
	// DEGRADE UNSUPPORTED
	if (document.layers) {
		return;
	}
	// VALIDATION
	if (!anElement) {
		return false;
	}
	if (typeof anElement == "string") { // try for the ID
		anElement = document.getElementById ? document.getElementById(anElement) : document.all ? document.all[anElement] : anElement;
	}
	if (typeof anElement == "string") { // the grab failed: typeof null yields "object"
		return false;
	}
	// ASSOCIATION
	this.element = anElement;
	this.id = this.element.id + 'Combo';
	this.element.combo = this;
	// ELEMENT EVENT HANDLERS
	if (this.element.addEventListener) {
		// first try DOM2 methods
		this.element.addEventListener("keydown", this.elementKeydown, false);
		this.element.addEventListener("focus", this.elementFocus, false);
		this.element.addEventListener("click", this.updateIndex, false);
		this.element.addEventListener("blur", this.updateIndex, false);
	} else {
		// now try DOM0 methods
		this.element.onkeydown = this.elementKeydown;
		this.element.onfocus = this.elementFocus;
		this.element.onclick = this.updateIndex;
		this.element.onblur = this.updateIndex;
	}
	this.element.reset = this.reset;
	// PRIVATE PROPERTIES
	var self = this,	// corrects privatization bug
		typed = "",
		index = prev = 0,
		displayStatus = true,
		selector, resetter, nullStarter, acceptNew,
		resetTime = 1600,
		numberRangeStart = 48,
		numberRangeEnd = 57,
		charRangeStart = 65,
		charRangeEnd = 90,
		punctRangeStart = 146,
		punctRangeEnd = 223;
	if (this.element.options[0].text.length == 0 && (this.element.options[0].value.length == 0 || this.element.options[0].value == 0)) {
		nullStarter = true;
	} else {
		nullStarter = false;
	}
	if (typeof acceptNewValue != 'undefined' && acceptNewValue) {
		acceptNew = true;
		resetTime = 2400;
	} else {
		acceptNew = false;
	}
	// PRIVATE METHODS
	var getResetTime = function () {
		return resetTime;
	}
	var charInRanges = function (charCode) {
		if ((charCode >= numberRangeStart && charCode <= numberRangeEnd) || (charCode >= charRangeStart && charCode <= charRangeEnd) || (charCode >= punctRangeStart && charCode <= punctRangeEnd)) {
			return true;
		} else {
			return false;
		}
	}
	// PRIVILEDGED METHODS
	this.hasNullStarter = function () {
		return nullStarter;
	}
	this.getAcceptsNew = function () {
		return acceptNew;
	}
	this.getTyped = function () {
		return typed;
	}
	this.setTyped = function (str) {
		typed = str;
		return true;
	}
	this.resetTyped = function () {
		typed = "";
		return true;
	}
	this.type = function (str) {
		typed += str;
		return true;
	}
	this.getIndex = function () {
		return index;
	}
	this.setIndex = function (val) {
		if (!isNaN(val)) {
			index = val;
		}
	}
	this.getPrev = function () {
		return (prev ? prev : 0);
	}
	this.setPrev = function (val) {
		if (!isNaN(val)) {
			prev = val;
		}
	}
	this.setResetTime = function (val) {
		if (!isNaN(val)) {
			resetTime = val;
		}
	}
	this.setResetTimer = function () {
		resetter = setTimeout("document.forms['"+this.element.form.name+"'].elements['"+this.element.name+"'].reset();", getResetTime());
	}
	this.clearResetTimer = function () {
		clearTimeout(resetter);
	}
	this.delayedSelect = function () {
		selector = setTimeout("document.forms['"+this.element.form.name+"'].elements['"+this.element.name+"'].combo.selectItem();", 10);
	}
	this.cancelDelay = function () {
		clearTimeout(selector);
	}
	this.validChar = function (evt, charCode) {
		if ((evt.ctrlKey) || (evt.altKey)) {
			return false;
		} else if ((evt.shiftKey) && charInRanges(charCode)) {
			return true;
		} else if (evt.shiftKey) {
			return false;
		} else {
			return charInRanges(charCode);
		}
	}
	this.setDisplayStatus = function (bool) {
		if (bool == true || bool == false) {
			displayStatus = bool;
		}
	}
	this.getDisplayStatus = function () {
		return displayStatus;
	}
	this.cancel = function (evt) {
		if (evt) {
			evt.preventDefault();
		} else {
			window.event.returnValue = false;
		}
		return false;
	}
}

/*
PUBLIC METHODS
*/

TypeAheadCombo.prototype.detectKey = function (evt){
	this.clearResetTimer();
	this.cancelDelay();
	var combo_letter = "";
	var combo_code = (evt) ? evt.keyCode : window.event ? window.event.keyCode : evt.which;
	var event = (evt) ? evt : window.event;
	if (combo_code <= 105 && combo_code >= 96) { // make up for numPad typing
		combo_code = combo_code - 48;
	}
	switch (combo_code) {
		case 27:	//ESC key
			this.reset();
			this.setIndex(this.getPrev());
			// Put a little delay to override NS6/Mozilla's built-in behavior of ESC inside select element
			setTimeout("document.forms['"+this.element.form.name+"'].elements['"+this.element.name+"'].selectedIndex = document.forms['"+this.element.form.name+"'].elements['"+this.element.name+"'].index",0);
			return false;
			break;
		case 13:	//ENTER key
		case 9:		//TAB key
			this.reset();
			if (this.element.onchange) {
				// set timer to prevent stack overflow in IE.
				setTimeout("document.forms['" + this.element.form.name + "'].elements['" + this.element.name + "'].onchange()", 1);
			}
			return true;
			break;
		case 8:		//BACKSPACE key
			this.setTyped(this.getTyped().substring(0,this.getTyped().length-1));
			if (this.getAcceptsNew() && this.getIndex() == 0) {
				this.makeNewValue();
			}
			if (this.getTyped() == "") {
				this.reset();
				this.setIndex(this.getPrev());
				this.element.selectedIndex = this.getIndex();
				if (evt) {
					evt.preventDefault();
				} else {
					window.event.returnValue = false;
				}
				return false;
			} else {
				this.setResetTimer();
			}
			break;
		case 33:	//PAGEUP key
		case 34:	//PAGEDOWN key
		case 35:	//END key
		case 36:	//HOME key
		case 38:	//UP arrow
		case 40:	//DOWN arrow
			this.reset();
			return true;
			break;
		case 37:	//LEFT arrow	(translates to %)
		case 39:	//RIGHT arrow	(translates to ')
			this.reset();
			return false;
			break;
		case 32:	//SPACE key	(not in accepted ranges)
			combo_letter = " ";
			this.setResetTimer();
			break;
		default:
			if (this.validChar(event, combo_code)) {
				combo_letter = String.fromKeyCode(combo_code);
				if (combo_letter.length > 1) {
					if (event.shiftKey) {
						combo_letter = combo_letter[1];
					} else {
						combo_letter = combo_letter[0];
					}
				}
				this.setResetTimer();
			} else {
				return true;
			}
			break;
	}
	this.type(combo_letter);
	if (this.getDisplayStatus()) {
		window.status = this.getTyped();
	}
	if (document.all) {
		return this.selectItem();
	} else {
		return this.delayedSelect();
	}
}

TypeAheadCombo.prototype.selectItem = function (){
	var i = this.element.options.length,
		match = false;
	do {
		if (this.element.options[--i].text.toUpperCase().indexOf(this.getTyped().toUpperCase()) == 0){
			this.element.selectedIndex = i;
			this.setIndex(i);	//remember selected index
			match = true;
		}
	} while (i > 0);
	if (match) {
		return false; // always return false;
	}
	if (this.getAcceptsNew()) {
		this.makeNewValue();
	} else {
		this.element.selectedIndex = this.getIndex();	//re-select previously selected option even if there's no match
	}
	return false;  //always return false
}

TypeAheadCombo.prototype.makeNewValue = function () {
	this.removeNewValue();
	var tmpText = this.getTyped(),tmpStart = tmpEnd = "",tmpArr,i;
	if (this.hasNullStarter()) {
		newOption = this.element.options[0];
	} else if (tmpText.length > 0) {
		newOption = document.createElement("option");
		this.element.insertBefore(newOption, this.element.firstChild);
		this.newOption = newOption;
	} else {
		this.newOption = null;
		return;
	}
	tmpArr = tmpText.split(" ");
	i = tmpArr.length;
	if (tmpText.indexOf(" ") >= 0) {
		do {
			tmpStart = tmpArr[--i].substring(0,1);
			tmpEnd = tmpArr[i].substring(1,tmpArr[i].length);
			tmpArr[i] = tmpStart.toUpperCase() + tmpEnd.toLowerCase();
		} while (i);
		tmpText = tmpArr.join(" ");
	} else {
		tmpStart = tmpText.substring(0,1);
		tmpEnd = tmpText.substring(1,tmpText.length);
		tmpText = tmpStart.toUpperCase() + tmpEnd.toLowerCase();
	}
	newOption.value = tmpText;
	newOption.text = tmpText;
	this.element.selectedIndex = 0;
	this.setIndex(0);
}

TypeAheadCombo.prototype.removeNewValue = function () {
	if (this.hasNullStarter()) {
		this.element.options[0].text = '';
		this.element.options[0].value = '';
	} else if (this.newOption) {
		this.element.remove(this.newOption);
	}
}

TypeAheadCombo.prototype.setValueByValue = function (aValue) {
	var i = this.element.options.length;
	do {
		if (this.element.options[--i].value == aValue) {
			this.element.selectedIndex = i;
			break;
		}
	} while (i);
}

TypeAheadCombo.prototype.reset = function () {
	theCombo = this;
	if (this.combo) {
		theCombo = this.combo;
	}
	theCombo.element.selectedIndex = theCombo.getIndex();
	theCombo.resetTyped();
	if (theCombo.getDisplayStatus()) {
		window.status = window.defaultStatus ? window.defaultStatus : '';
	}
}

TypeAheadCombo.prototype.updateIndex = function (evt){
	var theCombo, theEl;
	if (evt && window.addEventListener) {
		// ready for handler with DOM2 event properties
		var e = new DOM2Event(evt, window.event, this);
		theEl = e.target;
	} else {
		theEl = this;
	}
	theCombo = theEl.combo;
	theCombo.setIndex(theEl.selectedIndex);
	theCombo.setPrev(theCombo.getIndex());
}

TypeAheadCombo.prototype.elementFocus = function (evt) {
	var theCombo;
	if (evt && window.addEventListener) {
		// ready for handler with DOM2 event properties
		var e = new DOM2Event(evt, window.event, this);
		theCombo = e.target.combo;
	} else {
		theCombo = this.combo;
	}
	theCombo.setIndex(theCombo.element.selectedIndex);
}

TypeAheadCombo.prototype.elementKeydown = function (evt) {
	var theCombo;
	if (evt && window.addEventListener) {
		// ready for handler with DOM2 event properties
		if (DOM2Event) {
			var e = new DOM2Event(evt, window.event, this);
		}
		theCombo = e.target.combo;
	} else {
		theCombo = this.combo;
	}
	if (!theCombo.detectKey(e)) {
		return theCombo.cancel(e);
	}
}