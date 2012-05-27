<?php
// Core may be autoprepended in ninjawars

$error_level = error_reporting();
error_reporting(0); // Don't report any errors in the test library code.
require_once(LIB_ROOT.'third-party/simpletest/autorun.php'); // Include the testing library.
error_reporting($error_level); // Return to normal error level.

require_once(LIB_ROOT.'lib_auth.php');

/* Account behavior

When an account is created, it is initially unconfirmed, but "operational" (only used to delete unwanted accounts, spammers, etc)
Login attempts when unconfirmed ask for confirmation.
After confirmation, login is allowed.
A ninja may start out as non-active, which means that they won't show up in the list until they log in.
If they are inactive for long enough, they'll be deactivated, and won't show up in the list until they log in again, at which point
their status will be toggled to "active".

A player will never have to confirm themselves more than once.
Login is all it takes to turn a ninja from "inactive" to "active", and it should happen transparently upon login.
Login should also update "last logged in" data, but that's probably better in a different test suite.

*/


/* Tests:
// create test account & player, they should start off unconfirmed, operational.
// Try to login with a newly created player, the account should not be able to be logged in (for most emails)
// confirm an account, test that the database data is toggled
// login with confirmed account, login should proceed correctly
// create a new account, confirm it, and then set it inactive.  Then perform a login.  test that after login, the ninja is toggled to active.

*/

class TestAccountConfirmation extends UnitTestCase {
	
	function testSomething(){
		$this->t(true);
	}
}

?>
