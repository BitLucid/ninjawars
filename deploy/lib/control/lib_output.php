<?php
// See also: the lib_input functions for filter methods.

function get_indefinite_article($p_noun) {
	return str_replace(' '.$p_noun, '', shell_exec('perl '.LIB_PERL.'lingua-a.pl "'.escapeshellcmd($p_noun).'"'));
}
// Need to cover out to html, and out to database in here somewhere, I think.



// For filtering user text/messages for output.
function out($dirty, $filter_callback='toHtml', $echo=false, $links=true){
    if ($filter_callback=='toHtml') {
        $res = htmlentities($dirty);
    } else {
        $res = $dirty;
        if($filter_callback && function_exists($filter_callback)){
            $res = $filter_callback($dirty);
        }
    }

    if ($links){ // Render http:// sections as links.
        $res = replace_urls($res);
    }

	$res = markdown($res);

    if (!$echo) {
        return $res;
    }

    echo $res;
}

function markdownCallback($p_matches)
{
	return '<a href="'.$p_matches[1].'">'.$p_matches[2].'</a>';	// *** was going to htmlentities here, then realized we do so in out(). Be aware of this ***
}

function markdown($p_input)
{
	$pattern = "/\[href:([^\[\]]+)\|([^\[\]]+)\]/";
	return preg_replace_callback($pattern, "markdownCallback", $p_input);
}

function message_url($url, $text){
    // Might should exclude external urls here, and potentially handle url encoding.
    return "[href:".$url."|".$text."]";
}

// Change this to default to toHtml.

// Replaces occurances of http://whatever with links (in blank tab).
function replace_urls($string){
	// Images get added by the css after the fact.
    $host = "([a-z\d][-a-z\d]*[a-z\d]\.)+[a-z][-a-z\d]*[a-z]";
    $port = "(:\d{1,})?";
    $path = "(\/[^?<>\#\"\s]+)?";
    $query = "(\?[^<>\#\"\s]+)?";
    return preg_replace("#((ht|f)tps?:\/\/{$host}{$port}{$path}{$query})#i", "<a target='_blank' class='extLink' rel='nofollow' href='$1'>$1</a>", $string);
}


?>
