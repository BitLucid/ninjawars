<?php
require_once(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'ninjawars')+10).'deploy/resources.php');
// Core may be autoprepended in ninjawars
require_once(LIB_ROOT.'base.inc.php');
require_once(LIB_ROOT.'cleanup.inc.php'); // Profiling stuff

$error_level = error_reporting();
//error_reporting(0); // Don't report any errors in the test library code.
require_once(LIB_ROOT.'third-party/simpletest/autorun.php'); // Include the testing library.
error_reporting($error_level); // Return to normal error level.

require_once('lib_db_tests.php'); // DB included in base.
require_once('lib_char_tests.php'); // Player included in base.
require_once("lib_combat_tests.php");
require_once("lib_tests.php");

include('testaccountconfirmation.php');

test_player_obj();
test_PlayerDAO();
// *** Run the appropriate combat function tests:
test_attack_legal_object();

test_input();

test_filter_methods(); // (Currently empty)

test_filters();





echo 'Test runs finished.';

?>
