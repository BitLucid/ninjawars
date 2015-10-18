<?php
/**
 * Assert lib file to suppress assert warnings on live servers.
 * 
 * @package lib
 * @subpackage settings
**/

if (!DEBUG) {
	assert_options(ASSERT_ACTIVE, 0); // *** Sets assert off on live.
} else {
	assert_options(ASSERT_ACTIVE, 1);
}
