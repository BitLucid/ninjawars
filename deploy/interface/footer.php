<?php
// Displays the ending html stuff, and potentially the quickstats js refresh.
echo render_template('footer.tpl', array("quickstat"=>(isset($quickstat) ? $quickstat : null)));
?>
