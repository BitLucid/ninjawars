<?php
/*
 * Deals with getting and filtering user input (just from request).
 *
 * @package interface
 * @subpackage input
 */

function in($var_name, $default_val=null, $filter_method='toText'){
    $result = null;
    $req = isset($_REQUEST[$var_name])? $_REQUEST[$var_name] : null;
    $filter = new Filter();
    // Check for the appropriate method, only filter if something came in to start with.
    if($filter_method && method_exists($filter, $filter_method) && $req){
        return $filter->$filter_method($req);
    } else {
        return $default_val;
    }
}

?>
