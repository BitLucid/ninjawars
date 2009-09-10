<?php
/*
 * Deals with getting and filtering user input (get, post, cookie).
 *
 * @package interface
 * @subpackage input
 */

if(DEBUG){
	require_once(dirname(__FILE__)."/lib_tests.php");
	test_input();
	test_filter_methods();
	//test_filters();
}

// $filter_method is literally the filter object method to use.
function in($var_name, $default_val=null, $filter_method=null, $specific_source=null, &$error=null, $further_args=NULL){
	if($specific_source){
		$IN = $specific_source; // Can be used to filter custom arrays, and in tests.
	} else {
		$IN = $_REQUEST;
	}

	// Potential further_args: char_limit

	$filter = new Filter();
	if($filter_method == null) { $filter_method = 'toText'; } // default filter

	if (is_string($var_name)){
		// Assume single var to begin with, filter by default, all locations,
		$result = input_assign($var_name, $IN, $default_val, $filter, $filter_method);
	} elseif (is_array($var_name)){
		// Then rewrite for multiple variable names in an array.
		$result = array();
		foreach($var_name AS $loop_input_name){
			$result[$loop_input_name] = input_assign($loop_input_name, &$IN, &$default_val, &$filter, &$filter_method);
		}
		// The array should be passed with index names and then the list function
		// could be used to extract them.
	} else {
		throw new Exception('Input (in) function argument must be either string or array.');
	}
	assert(isset($filter));
	assert(isset($filter_method));

	return $result;
	// list() or extract() can then be used to copy the array values to the local scope.
}

// Interior function to repeatably assign the defaults.
function input_assign($var_name, &$IN, &$default_val, &$filter, &$filter_method){
	$result = NULL;
	if (!isset($IN[$var_name])){
		$result = $default_val; // Usually null default.
	} else {
		if ($filter_method == 'no filter' || $filter_method=='none'){
			$result = $IN[$var_name];
		} else {
			// Filter the result.
			$result = $filter->$filter_method($IN[$var_name]);
		}
	}
	return $result;
}

?>
