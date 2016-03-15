<?php
/**
 * Make all warnings and notices and stuff be made visible.
 *
 * @package lib
 * @subpackage settings
 */
if (DEBUG && DEBUG_ALL_ERRORS) {
	error_reporting(E_ALL);	// *** Completely everything ***
}
