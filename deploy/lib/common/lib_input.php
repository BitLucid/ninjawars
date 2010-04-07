<?php
/*
 * Deals with getting and filtering user input (just from request).
 *
 * @package interface
 * @subpackage input
 */

function in($var_name, $default_val=null, $filter_method='toText'){
    $result = isset($_REQUEST[$var_name])? $_REQUEST[$var_name] : $default_val;
    $filter = new Filter();
    // Check for the appropriate method.
    if($filter_method && method_exists($filter, $filter_method)){
        $result = $filter->$filter_method($result);
    }
    return $result;
}

?>
