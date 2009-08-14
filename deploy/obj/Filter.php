<?php
/*
 * Allows the filtering of different types.
 * 
 * @package input
 * @subpackage filter
 */
require_once(substr(__FILE__,0,(strpos(__FILE__, 'www/')))."lib/base.inc.php");
require_once(OBJ_ROOT."Sanitize.php"); // *** Mainly a replacement for the Filter methods.


class Filter
{
	// TODO: Username and password validation functions are in lib_auth.
	
	function toNumeric($dirty)
	{
		$result = NULL;
		if(is_numeric($dirty)){
			$result = $dirty;
		}
		return $result;
	}
	
	function toID($dirty)
	{
		return filter_var($dirty, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1) ) );
	}
	
	function toInt($dirty, $max=NULL, $min=NULL)
	{
		$options = NULL;
		if ($max || $min){
			$options = array("options" => array("min_range"=>$min, "max_range"=>$max));
		}
		return filter_var($dirty, FILTER_VALIDATE_INT, $options);
	}
	
	function toWord($dirty)
	{ // Essentially allows numbers, words, and usernames, no spaces.
		return preg_replace("[^A-Za-z0-9_\-]", "", (string) $dirty);
	}
	
	// Can be used as the default solution for the in() function.
	function toDefault($dirty)
	{
	    return toText($dirty);
	}
	
	// This is the default non-user-message filtering.
	// Only needs to cover fewer eventualities: spaces, emails and usernames, digits,
	// and standard urls.
	function toText($dirty)
	{
	    // Allows words, digits, spaces, _, -, ., @, :, and slash for urls /
		return preg_replace("/[^\w\d\s_\-\.\@\:\/]/", "", (string) $dirty);
	}
	
	/** Extensive filtering for passwords going into sql, legacy passwords
	 * shouldn't be effected, validate_password should limit the possibilities further.
	**/
	function toPassword($dirty){
	    // should strip out all sql-problematic characters, ' # ` ; and "
	    $dirty = preg_replace("/[^\w\d_\+\-\.\&\s\!\?\,\=\*\(\)\:\@\/]/", "", (string) $dirty);
	    return $dirty;
	}
	
	/**
	 * Relatively permissive username filter used during login procedure.
	 * Must allow: ^[A-Za-z0-9_-]+ alphanumerics, digits, underscores, and dashes.
	**/
	function toUsername($dirty){
	    return preg_replace("/[^\w\d\s_\-]/", "", (string) $dirty);
	}
	
	/**
	 * Filters an array of ids each to the id filter
	**/
	function toIds($dirty){
	    if(is_array($dirty)){
	        foreach($dirty as $key => $dirty_val){
	            $dirty[$key] = $this->toID($dirty_val);
	        }
	    }
	    return $dirty;
	}
	
	function forSql($dirty){
	    return $this->sanitize_sql_string($dirty);
	}
	
	// sanitize a string for SQL input (simple slash out quotes and slashes)
    function sanitize_sql_string($string, $min='', $max='')
    {
      // Replace slashes, slash code double-quotes, and slash code apostrophes.
      $pattern[0] = '/(\\\\)/';
      $pattern[1] = "/\"/";
      $pattern[2] = "/'/";
      $replacement[0] = '\\\\\\';
      $replacement[1] = '\"';
      $replacement[2] = "\\'";
      $len = strlen($string);
      if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
        return FALSE;
      return preg_replace($pattern, $replacement, $string);
    }

	
	function toUrl($dirty)
	{
		return preg_replace("/[^\w\d_\-\.\&\+\?\,\%\:\/]/", "", (string) $dirty);
	}
	
	// User messages should have a special exception made of them.
	function toMessage($dirty, $limit=null)
	{
		// Encode the quotes.
		$default_message_limit = 2000;
		// Encode single quotes.
		$dirty = substr(htmlentities($dirty), 0, ($limit? $limit : $default_message_limit));
		// Custom replacement of the apostrophe.
		$dirty = preg_replace("/[\']/", "&apos;", (string) $dirty);
		// Whitelist replace everything else that isn't in the character groups listed.
		$dirty = preg_replace("/[^\w\d_\-\+\.\&\;\s\!\?\,\=\*\%\(\)\:\@\/]/", "", (string) $dirty);
		// Replace urls with anchor hrefs.
		$dirty = $this->replace_urls($dirty);
		return $dirty;
	}
	
	function toEmail($dirty)
	{
		$result = NULL;
		$result = filter_var($dirty, FILTER_VALIDATE_EMAIL);
		return $result;
	}
	
	// Filter flags: http://phpro.org/tutorials/Filtering-Data-with-PHP.html#10

	function toHtml($dirty)
	{
		return htmlentities($dirty);
	}
	
	// Wraps toMessage method.
	function forChat($dirty)
	{
		return $this->toMessage($dirty);
	}
	
	// *** Wrapper function for user-originating mail.
	function forMail($dirty)
	{
		return $this->toMessage($dirty);
	}
	
	// Replaces occurances of http://whatever with links (in blank tab).
	function replace_urls($string){
	    $host = "([a-z\d][-a-z\d]*[a-z\d]\.)+[a-z][-a-z\d]*[a-z]";
	    $port = "(:\d{1,})?";
	    $path = "(\/[^?<>\#\"\s]+)?";
	    $query = "(\?[^<>\#\"\s]+)?";
	    return preg_replace("#((ht|f)tps?:\/\/{$host}{$port}{$path}{$query})#i", "<a target='_blank' href='$1'>$1</a>", $string);
	}
	
	
	/* EVENTUAL POTENTIAL FILTERS*/
	
	/*
	function cleanTags($source, $tags = null)
	{
	    function clean($matched)
	    {
	          $attribs = "javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|".
	                     "onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|".
	                     "onload|class|id|src|style";
	          $quot = "\"|\'|\`";
	          $stripAttrib = "' ($attribs)\s*=\s*($quot)(.*?)(\\2)'i";
	          $clean = stripslashes($matched[0]);
	          $clean = preg_replace($stripAttrib, '', $clean);
	          return $clean;
	    }      
 
	    $allowedTags='<a><br><b><i><br><li><ol><p><strong><u><ul>';
	    $clean = strip_tags($source, $allowedTags);
	    $clean = preg_replace_callback('#<(.*?)>#', "clean", $source);
	    return $source;
	}
	*/
	
	//IF NEEDED: function toIP
	
}

?>
