<?php

require_once(DB_ROOT .'lib_db_tests.php'); // DB included in base.
require_once(CHAR_ROOT.'lib_char_tests.php'); // Player included in base.
require_once(COMBAT_ROOT . "lib_combat_tests.php");
require_once(LIB_ROOT . "lib_tests.php");

test_player_obj();
test_PlayerDAO();
// *** Run the appropriate combat function tests:
test_attack_legal_object();

test_input();

test_filter_methods(); // (Currently empty)

test_filters();





echo 'Test runs finished.';

?>
