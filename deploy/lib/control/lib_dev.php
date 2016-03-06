<?php
// Development debugging tool functions for localhost environments,
// will be present but disabled on live.


// See also lib/settings/lib_debug.php for server debugging.

function debug($val) {
    if (DEBUG) {
    	$vals = func_get_args();
    	foreach($vals as $val){
		    echo "<pre class='debug' style='font-size:12pt;background-color:white;color:black;position:relative;z-index:10'>";
		    var_dump($val);
		    echo "</pre>";
        }
    }
}

// Custom error function.
function nw_error($message, $level=E_USER_NOTICE) {
	$backtrace = debug_backtrace();
	$caller = next($backtrace);
	$next_caller = next($backtrace);
	trigger_error("<div  class='debug' style='font-size:12pt;background-color:white;color:black;position:relative;z-index:10'>".$message.' in <strong>'.$caller['function'].'</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."called within: ".$next_caller['function']."\n<br /> and finally from the error handler in lib_dev: </div>", $level);
}

