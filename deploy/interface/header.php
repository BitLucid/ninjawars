<?php


$error = init(); // Creates some starting objects&vars, $sql, puts player info into global namespace (sadly), updates activity, starts buffering.

$header = render_header_when_not_section($page_title);

$error_to_display = render_error($error);

// **************** OUTPUT SECTION *******************************//

if ($error_to_display) {
    echo $header;
    echo $error_display;
    echo render_footer($quickstat); // Display the bottom of the error page, refresh the quickstat view if necessary.
    die();
} else {
    echo $header;
}

?>
