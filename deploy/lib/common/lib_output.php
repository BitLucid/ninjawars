<?php
function get_indefinite_article($p_noun)
{
	return str_replace(' '.$p_noun, '', shell_exec('perl '.LIB_PERL.'lingua-a.pl "'.escapeshellcmd($p_noun).'"'));
}
// Need to cover out to html, and out to database in here somewhere, I think.
?>
