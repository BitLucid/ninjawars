<?php

$error = init(isset($buffer) ? $buffer : true); // Creates some starting objects&vars, puts player info into global namespace (sadly), updates activity, starts buffering.

$header = render_header($page_title);

$error_to_display = render_error($error);

// **************** OUTPUT SECTION *******************************//

if ($error_to_display) {
    echo $header;
    echo $error_to_display;
    echo render_footer($quickstat); // Display the bottom of the error page, refresh the quickstat view if necessary.
    die(); // Do not display any further on the page.
} else {
    echo $header;
}
?>
