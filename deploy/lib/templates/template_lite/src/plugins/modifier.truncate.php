<?php
/**
 * template_lite truncate modifier plugin
 *
 * Type:     modifier
 * Name:     truncate
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and 
 *           appending the $etc string.
 * Credit:   Taken from the original Smarty
 *           http://smarty.php.net
 */
function tpl_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false)
{
	if ($length == 0)
	{
		return '';
	}

	if (strlen($string) > $length)
	{
		$length -= strlen($etc);
		if (!$break_words)
		{
			$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
		}
		return substr($string, 0, $length).$etc;
	}
	else
	{
		return $string;
	}
}
?>