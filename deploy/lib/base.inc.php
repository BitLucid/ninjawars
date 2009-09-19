<?php
/**
 * The starting include file for all of NW.
 *
 * @package lib
 * @subpackage base
**/

// Cut down on the global includes, use specific includes instead.

require_once(substr(__FILE__, 0, (strpos(__FILE__, 'lib/')))."resources.php");
// *** Included first from the index, so has to be on the same level as index.
// Standalone utilities
require_once(LIB_ROOT."func/either.php");
require_once(LIB_ROOT."func/redirect.php");
require_once(DB_ROOT."util.php");
require_once(OBJ_ROOT."Start.php");
require_once(OBJ_ROOT."Filter.php");
require_once(OBJ_ROOT."Sanitize.php");
require_once(OBJ_ROOT."Nmail.class.php");
require_once(OBJ_ROOT."Cookie.class.php");
require_once(OBJ_ROOT."Session.class.php");

// *** Include all common function includes here.
require_once(LIB_ROOT."common/lib_input.php");
require_once(LIB_ROOT."common/lib_output.php");
require_once(LIB_ROOT."templates/lib_templates.php");

// Development includes, for live also.
require_once(LIB_ROOT."common/lib_dev.php");

// Includes that actually actively modify settings.
require_once(LIB_ROOT."settings/lib_assert.php");
require_once(LIB_ROOT."settings/lib_error_reporting.php");
require_once(LIB_ROOT."settings/lib_formulas.php");

// Include all the commands, which eventually should be broken up.
require_once(LIB_ROOT."common/commands.php");
require_once(LIB_ROOT."common/lib_message.php");
require_once(LIB_ROOT."common/lib_events.php");
require_once(LIB_ROOT."common/lib_mail.php");
require_once(LIB_ROOT."common/lib_auth.php"); // Authentication and activity.


// Game objects
require_once(DB_ROOT . "PlayerVO.class.php");
require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(COMBAT_ROOT . "AttackLegal.class.php");
require_once(CHAR_ROOT . "Player.class.php");
require_once(LIB_ROOT."common/lib_attack.php");

// Include the functions abstracted out of the header and footer
require_once(SERVER_ROOT."interface/lib_header.php");
require_once(SERVER_ROOT."interface/lib_footer.php");

?>
