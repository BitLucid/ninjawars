<?php
/**
 * Return first arg if non-empty; otherwise, second arg
 * @desc Replacement function for ternary if.
 * @return whichever value is first available.
**/
function either($a, $b) {
	return ($a != "") ? $a : $b;
}

?>
