<?php
// Unit Test functions


// Tests against the lib_input

function test_input(){
	/*$start = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234 567890`~!@#$%^&*()-_=+[{]};:'\"<,>.?/|\\n\\r\\t";
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res != $start);
	var_dump('RESULT:');
	var_dump($res);

	$start = "Unallowed \'SQL\"''##--';";
	$TEST['test'] = $start;
	$res = sanitize_sql_string($start);
	assert($res != $start);
	var_dump('RESULT:');
	var_dump($res);*/

	// Example login username regex: $validLogin = ereg($postvar, '[[:alnum:] _-]{6,40}');

	// This may require running on the web url.
	$start = 'word';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == $start);

	$start = 'Sentence full of stuff, ya know?.';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toMessage', $TEST);
	assert($res == $start);

	$start = 'Unallowed Characters><>""';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res != $start);

	$start = 'Unallowed Characters><>""';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == 'Unallowed Characters');

	$start = "Unallowed HTML<a></a>";
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == 'Unallowed HTMLa/a');

	$start = "Unallowed \'SQL\"''##--';";
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == 'Unallowed SQL--');

	$start = 'Unallowed Characters>&<>""';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res != $start);

	$start = 'Unallowed Characters>&<>""';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='no filter', $TEST);
	assert($res == $start);

	$start = 'Unallowed Characters.>&<>""\'';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == 'Unallowed Characters.');

	$start = 'Unallowed <a>Characters.>&<>""\'';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == 'Unallowed aCharacters.');

	$start = 'Unallowed <a>Characters.>&<>""\'';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res == 'Unallowed aCharacters.');

	$start = 'All_allowed_characters?!.,  ';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toMessage', $TEST);
	assert($res == $start);

	$start = 'Non-messageWith Nonstandard_Characters should fail?!.,  ';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method=NULL, $TEST);
	assert($res != $start);

	$start = urlencode('http://www.ninjawars.net?val=5&pie=true');
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toUrl', $TEST);
	assert($res == $start);
/*
	$start = "Allowed characters.  This has apostrophes ' and quotes \" in it that should be encoded.";
	$with_apostrophes_encoded = "Allowed characters.  This has apostrophes &apos; and quotes &quot; in it that should be encoded.";
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toMessage', $TEST);
	assert($res == $with_apostrophes_encoded);
*/
	$start = NULL;
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toMessage', $TEST);
	assert($res == $start);
/*
	$start = "Greater Than > and less than < encoded.";
	$with_gtlt_encoded = "Greater Than &gt; and less than &lt; encoded.";
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toMessage', $TEST);
	assert($res == $with_gtlt_encoded);

	$start = "I like emails like tchalvakspam@gmail.com and urls like http://ninjawars.net in my messages.";
	$with_urls = "I like emails like tchalvakspam@gmail.com and urls like <a target='_blank' href='http://ninjawars.net'>http://ninjawars.net</a> in my messages.";
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toMessage', $TEST);
	assert($res == $with_urls);
	// Messages regex replace urls.
*/
	$start = '7';
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toInt', $TEST);
	assert($res == 7);

	$start = 7;
	$TEST['test'] = $start;
	$res = in('test', $default_val=null, $filter_method='toInt', $TEST);
	assert($res == 7);

	$start = '7';
	$TEST['test'] = $start;
	$res = in('index_that_is_not_set', $default_val='default', $filter_method='toInt', $TEST);
	assert($res == 'default');

	$start = 'alpha56';
	$TEST['test'] = $start;
	$res = in('test', $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == false); // ??

	$start = -10;
	$TEST['test'] = $start;
	$res = in('test', $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == false);

	$start = 0;
	$TEST['test'] = $start;
	$res = in('test', $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == false);

	$start = 10;
	$TEST['test'] = $start;
	$res = in('test', $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == $start);

	$start = '10';
	$TEST['test'] = $start;
	$res = in('test', $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == $start);

	$start = array('var', 'var2');
	$TEST = array('var'=>10, 'var2'=>20);
	$res = in($start, $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == $TEST);

	$start = array('var', 'var2');
	$TEST = array('var'=>10, 'var2'=>20);
	$res = in($start, $default_val=NULL, $filter_method='toText', $TEST);
	assert($res == $TEST);

	$start = array('var', 'var2');
	$TEST = array('var'=>10, 'var2'=>20);
	$res = in($start, $default_val=NULL, $filter_method='toText', $TEST);
	assert($res == $TEST);

	$start = array('var', 'var2');
	$TEST = array('var'=>10, 'var2'=>20);
	$EXPECTED = array('var'=>10, 'var2'=>20);
	$res = in($start, $default_val=NULL, $filter_method='toText', $TEST);
	assert($res == $EXPECTED);

	$start = array('var', 'var2', 'var3');
	$TEST = array('var'=>10, 'var2'=>20);
	$EXPECTED = array('var'=>10, 'var2'=>20, 'var3'=>NULL);
	$res = in($start, $default_val=NULL, $filter_method='toText', $TEST);
	assert($res == $EXPECTED);

	$start = array('var', 'var2');
	$TEST = array('var'=>10, 'var2'=>20, 'var3'=>'bad');
	$EXPECTED = array('var'=>10, 'var2'=>20);
	$res = in($start, $default_val=NULL, $filter_method='toText', $TEST);
	assert($res == $EXPECTED);

	$start = array('var', 'var2', 'var3');
	$TEST = array('var'=>10, 'var2'=>20, 'var3'=>'bad');
	$EXPECTED = array('var'=>10, 'var2'=>20, 'var3'=>NULL);
	$res = in($start, $default_val=NULL, $filter_method='toID', $TEST);
	assert($res == $EXPECTED);
}

function test_filter_methods(){

}



function test_filters(){
	/***
	 * Data Filtering Using PHP's Filter Functions - Part one
	 * Examples using PHP's Filter Functions
	 * http://devolio.com/blog/archives/413-Data-Filtering-Using-PHPs-Filter-Functions-Part-one.html
	 **/
	error_reporting(E_ALL);

	/* do a quick check to make sure that the filter list is available */
	if (function_exists('filter_list'))
	{
		/* filter list found */
	} else {
		die("Error: Filters not found.");
	}


	/* variables to test against */
	$int = 432;
	$bool = true;
	$float = 432.43;
	$reg = "/^([a-zA-Z0-9 ]){4,16}$/";
	$url = "http://devolio.com/blog";
	$email = 'joey@devolio.com';
	$ipaddr = '127.0.0.1';
	$ipres = "192.168.0.*";
	$ipv6addr = "2001:0db8:85a3:08d3:1319:8a2e:0370:7334";
	$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWX
	YZ1234	567890`~!@#$%^&*()-_=+[{]};:'\"<,>.?/\|\\n\\r\\t";

	/* grab all of the filters and show them */
	echo "<h2>Filter list</h2><pre>";
	echo "<ul>\n";
	$filters = filter_list();
	foreach ($filters as $filter)
	{
		echo "<li>".$filter."</li>\n";
	}
	echo "</ul></pre>\n";


	echo "<h2>FILTER_VALIDATE_*</h2>";


	/* check if an integer is valid */
	$valid_int = filter_var($int, FILTER_VALIDATE_INT);
	echo "<pre><b>FILTER_VALIDATE_INT</b><br>";
	if ($valid_int !== false)
	{
		echo "Valid integer.</pre>";
	} else {
		echo "Not a valid integer.</pre>";
	}


	/* check if a boolean is valid */
	$valid_bool = filter_var($bool, FILTER_VALIDATE_BOOLEAN);
	echo "<pre><b>FILTER_VALIDATE_BOOL</b><br>";
	if ($valid_bool !== false)
	{
		echo "Valid boolean.</pre>";
	} else {
		echo "Not a valid boolean.</pre>";
	}


	/* check if a float (int) is valid */
	$valid_float = filter_var($float, FILTER_VALIDATE_FLOAT);
	echo "<pre><b>FILTER_VALIDATE_FLOAT</b><br>";
	if ($valid_float !== false)
	{
		echo "Valid float.</pre>";
	} else {
		echo "Not a valid float.</pre>";
	}


	/* check if a regular expression is valid
	 * suppressed (bug?) in case regex not available
	 */
	$valid_reg = @filter_var($reg, FILTER_VALIDATE_REGEXP);
	echo "<pre><b>FILTER_VALIDATE_REGEXP</b><br>";
	if ($valid_reg !== false)
	{
		echo "Valid regular expression.</pre>";
	} else {
		echo "Not a valid regular expression.</pre>";
	}


	/* check if a URL is valid */
	$valid_url = filter_var($url, FILTER_VALIDATE_URL);
	echo "<pre><b>FILTER_VALIDATE_URL</b><br>";
	if ($valid_url !== false)
	{
		echo "Valid URL.</pre>";
	} else {
		echo "Not a valid URL.</pre>";
	}


	/* check if an e-mail address is valid */
	$valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
	echo "<pre><b>FILTER_VALIDATE_EMAIL</b><br>";
	if ($valid_email !== false)
	{
		echo "Valid e-mail address.</pre>";
	} else {
		echo "Not a valid e-mail address.</pre>";
	}


	/* check if an IP address is valid */
	$valid_ip = filter_var($ipaddr, FILTER_VALIDATE_IP);
	echo "<pre><b>FILTER_VALIDATE_IP</b><br>";
	if ($valid_ip !== false)
	{
		echo "Valid IP address.</pre>";
	} else {
		echo "Not a valid IP address.</pre>";
	}


	echo "<h2>FILTER_SANITIZE_*</h2>";


	/* sanitize filters */
	/* check if filter unsafe raw is unsafe. protip: YES */
	$raw = $string;
	$valid_raw = filter_var($raw, FILTER_UNSAFE_RAW);
	echo "<pre><b>FILTER_UNSAFE_RAW</b><br>".$valid_raw."</pre>";


	/* sanitize string */
	$san_string = filter_var($string, FILTER_SANITIZE_STRING);
	echo "<pre><b>FILTER_SANITIZE_STRING</b><br>".$san_string."</pre>";


	/* sanitize stripped */
	$san_stripped = filter_var($string, FILTER_SANITIZE_STRIPPED);
	echo "<pre><b>FILTER_SANITIZE_STRIPPED</b><br>".$san_stripped."</pre>";


	/* sanitize encoded */
	$san_enc = filter_var($string, FILTER_SANITIZE_ENCODED);
	echo "<pre><b>FILTER_SANITIZE_ENCODED</b><br>".$san_enc."</pre>";


	/* sanitize special chars */
	$san_spc = filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
	echo "<pre><b>FILTER_SANITIZE_SPECIAL_CHARS</b><br>".$san_spc."</pre>";


	/* sanitize email */
	$san_email = filter_var($string, FILTER_SANITIZE_EMAIL);
	echo "<pre><b>FILTER_SANITIZE_EMAIL</b><br>".$san_email."</pre>";


	/* sanitize url */
	$san_url = filter_var($string, FILTER_SANITIZE_URL);
	echo "<pre><b>FILTER_SANITIZE_URL</b><br>".$san_url."</pre>";


	/* sanitize int */
	$san_int = filter_var($string, FILTER_SANITIZE_NUMBER_INT);
	echo "<pre><b>FILTER_SANITIZE_NUMBER_INT</b><br>".$san_int."</pre>";

	echo "<h2>FILTER_FLAG_*</h2>";


	/* filter flags */

	/* strip low - strips ascii < 32 */
	$strip_low = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	echo "<pre><b>FILTER_FLAG_STRIP_LOW</b><br>".$strip_low."</pre>";


	/* strip high - strips ascii > 127 */
	$strip_high = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	echo "<pre><b>FILTER_FLAG_STRIP_HIGH</b><br>".$strip_high."</pre>";


	/* encode low - encodes ascii < 32 */
	$enc_low = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	echo "<pre><b>FILTER_FLAG_ENCODE_LOW</b><br>".$enc_low."</pre>";


	/* encode high - encodes ascii > 127 */
	$enc_high = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
	echo "<pre><b>FILTER_FLAG_ENCODE_HIGH</b><br>".$enc_high."</pre>";


	/* don't encode ' or " */
	$deq = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	echo "<pre><b>FILTER_FLAG_NO_ENCODE_QUOTES</b><br>".$deq."</pre>";

	var_dump('ORIGINAL STRING: ', $string);
	var_dump('ORIGINAL INT: ', $int);


}// End test_filter

?>
