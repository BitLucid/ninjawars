<?php
// Development debugging tool functions for localhost environments,
// will be present but disabled on live.


// See also lib/settings/lib_debug.php for server debugging.

/**
 * Hack to dump out the local variables in a very visible format.
 * @param array $locals get_defined_vars() passed to this function.
**/
function var_dump_locals($defined=array()) {
	if (DEBUG) {
		echo "<table cellspacing='0' cellpadding='0' dir='ltr' class='xdebug-local-vars'
		style='background-color:lavender;clear:both;border:thin solid grey;width:800px;padding:0;margin:0;'>
			<tbody>
			<tr style='background-color:orange;color:Black'>
			<th>Local Var Name </th>
			<th>Value </th>
			</tr>";
		if (count($defined)==0) {
			echo "<tr><td colspan='2'>None sent in.</td><tr>";
		} else {
			foreach ($defined as $name => $contents) {
				echo "<tr class='table-row-2-column' style='border:thin solid grey;padding:1px;'>
					<td style='background-color:#e9b96e;color:black;font-weight:bold;border:thin solid grey;'>
					&#36$name =
					</td>
				 	<td style='border:thin solid grey;'>";
				var_dump($contents);
				echo "</td>
					</tr>";
			}
		}
		echo "<tr style='clear:both;'>
			<th colspan='2' style='background-color:brown;color:black;'>End of local var dump</th>
			</tr>
			</tbody>
			</table>";
	}
}

function debug($val) {
    if (DEBUG) {
        echo "<pre class='debug' style='font-size:12pt'>";
        var_dump($val);
        echo "</pre>";
    }
}
?>
