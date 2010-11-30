<?php
if (PROFILE) {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$totaltime = ($mtime - $__starttime);
	$totalmemory = memory_get_peak_usage(true);
	$unit=array('b','kb','mb','gb','tb','pb');
	$totalmemory = @round($totalmemory/pow(1024,($i=floor(log($totalmemory,1024)))),2).' '.$unit[$i];
	error_log('PROFILE - Script: '.$_SERVER["SCRIPT_NAME"]." - Time: $totaltime - Mem: $totalmemory");
}
?>
