function simple_var_dump(obj) {
		
	try { 
		console.log('simple_var_dump: ', obj);
		alert("Firebug dump successful.");
	} catch(e) { 
		alert("You don't have Firebug enabled!");
		dump = ''; 
		if(typeof obj == "object") {
			dump += "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
		} else {
		    dump += "Type: "+typeof(obj)+"\nValue: "+obj;
		}
		alert(dump);
	}
	
	/*Simple array:
	var myVar = { 
	    key1 : 'value1', 
	    key2 : 'value2', 
	    key3 : ['a', 'b', 'c'] 
	}; */ 
}//end function var_dump


/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}



