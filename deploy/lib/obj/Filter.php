<?php
/*
 * Allows the filtering of different types.
 *
 * @package input
 * @subpackage filter
 */
require_once(OBJ_ROOT."Sanitize.php"); // *** Mainly a replacement for the Filter methods.
require_once(LIB_ROOT."common/lib_output.php"); // For the replace_urls master function.

class Filter
{
	// TODO: Username and password validation functions are in lib_auth.

	function toNumeric($dirty) {
		$result = NULL;

		if (is_numeric($dirty)) {
			$result = $dirty;
		}

		return $result;
	}

	function toID($dirty) {
		return filter_var($dirty, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1) ) );
	}

	function toInt($dirty, $max=NULL, $min=NULL) {
		$options = NULL;

		if ($max || $min) {
			$options = array("options" => array("min_range"=>$min, "max_range"=>$max));
		}

		return filter_var($dirty, FILTER_VALIDATE_INT, $options);
	}

	function toWord($dirty) { // Essentially allows numbers, words, and usernames, no spaces.
		return preg_replace("[^A-Za-z0-9_\-]", "", (string) $dirty);
	}

	// Can be used as the default solution for the in() function.
	function toDefault($dirty) {
	    return toText($dirty);
	}

	// This is the default non-user-message filtering.
	// Only needs to cover fewer eventualities: spaces, emails and usernames, digits,
	// and standard urls.
	function toText($dirty) {
	    // Allows words, digits, spaces, _, -, ., @, :, and slash for urls /
		return preg_replace("/[^\w\d\s_\-\.\@\:\/]/", "", (string) $dirty);
	}

	/**
	 * Relatively permissive username filter used during login procedure.
	 * Must allow: ^[A-Za-z0-9_-]+ alphanumerics, digits, underscores, and dashes.
	**/
	function toUsername($dirty) {
	    return preg_replace("/[^\w\d\s_\-]/", "", (string) $dirty);
	}

	/**
	 * Filters an array of ids each to the id filter
	**/
	function toIds($dirty) {
	    if (is_array($dirty)) {
	        foreach($dirty as $key => $dirty_val) {
	            $dirty[$key] = $this->toID($dirty_val);
	        }
	    }

	    return $dirty;
	}

	function toUrl($dirty) {
		return preg_replace("/[^\w\d_\-\.\&\+\?\,\%\:\/]/", "", (string) $dirty);
	}

	function toEmail($dirty) {
		$result = NULL;
		$result = filter_var($dirty, FILTER_VALIDATE_EMAIL);
		return $result;
	}

	// Filter flags: http://phpro.org/tutorials/Filtering-Data-with-PHP.html#10

	function toHtml($dirty) {
		return htmlentities($dirty);
	}

	// Wraps toMessage method.
	function forChat($dirty) {
		return $this->toMessage($dirty);
	}

	// *** Wrapper function for user-originating mail.
	function forMail($dirty) {
		return $this->toMessage($dirty);
	}

	// Replaces occurances of http://whatever with links (in blank tab).
	function replace_urls($string) {
	    return replace_urls($string); // Use the lib_output.php function.
	}
}
?>
