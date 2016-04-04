<?php
/**
 * Replaces occurances of http://whatever with links (in blank tab).
 */
function smarty_modifier_replace_urls($p_string) {
    // Images get added by the css after the fact.
    $host = "([a-z\d][-a-z\d]*[a-z\d]\.)+[a-z][-a-z\d]*[a-z]";
    $port = "(:\d{1,})?";
    $path = "(\/[^?<>\#\"\s]+)?";
    $query = "(\?[^<>\#\"\s]+)?";

    return preg_replace("#((ht|f)tps?:\/\/{$host}{$port}{$path}{$query})#i", "<a target='_blank' class='extLink' rel='nofollow' href='$1'>$1</a>", $p_string);
}
