<?php
// See also: the filter object for useful preg_replace usages.

function get_indefinite_article($p_noun) {
	return str_replace(' '.$p_noun, '', shell_exec('perl '.LIB_PERL.'lingua-a.pl "'.escapeshellcmd($p_noun).'"'));
}
// Need to cover out to html, and out to database in here somewhere, I think.



// For filtering user text/messages for output.
function out($dirty, $filter_method='toHtml', $echo=false, $links=true){
    if($filter_method=='toHtml'){
        $res = htmlentities($dirty);
    } else {
    	$filter = new Filter();
    	$res = $filter->$filter_method($dirty);
    }
    if($links){ // Render http:// sections as links.
        $res = replace_urls($res);
    }
    if(!$echo){
        return $res;
    }
    // else
    echo $res;
}

// Change this to default to toHtml.

function sql($dirty){
	// wrapper function for filtering to sql, to encode or not to encode.
    return pg_escape_string($dirty);
}

// Replaces occurances of http://whatever with links (in blank tab).
function replace_urls($string, $image=true){
    $image = ($image? " <img class='extLink' alt='' src='".IMAGE_ROOT."externalLinkGraphic.gif'/>" : '');
    $host = "([a-z\d][-a-z\d]*[a-z\d]\.)+[a-z][-a-z\d]*[a-z]";
    $port = "(:\d{1,})?";
    $path = "(\/[^?<>\#\"\s]+)?";
    $query = "(\?[^<>\#\"\s]+)?";
    return preg_replace("#((ht|f)tps?:\/\/{$host}{$port}{$path}{$query})#i", "<a target='_blank' href='$1'>$1</a>$image", $string);
}


?>
