<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'www/')))."lib/base.inc.php"); // *** Absolute path include of everything.

// Displays the ending html stuff, and potentially the quickstats js refresh.
echo render_footer($quickstat);

?>
