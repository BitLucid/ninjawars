<?php

/*
 * Template Lite plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     mailto
 * Version:  1.2
 * Date:     May 21, 2002
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Credits:  Jason Sweat (added cc, bcc and subject functionality)
 * Purpose:  automate mailto address link creation, and optionally
 *           encode them.
 * Input:    address = e-mail address
 *           text = (optional) text to display, default is address
 *           encode = (optional) can be one of:
 *                 none : no encoding (default)
 *                 javascript : encode with javascript
 *                 hex : encode with hexidecimal (no javascript)
 *           cc = (optional) address(es) to carbon copy
 *           bcc = (optional) address(es) to blind carbon copy
 *           subject = (optional) e-mail subject
 *           newsgroups = (optional) newsgroup(s) to post to
 *           followupto = (optional) address(es) to follow up to
 *           extra = (optional) extra tags for the href link
 * 
 * Examples: {mailto address="me@domain.com"}
 *           {mailto address="me@domain.com" encode="javascript"}
 *           {mailto address="me@domain.com" encode="hex"}
 *           {mailto address="me@domain.com" subject="Hello to you!"}
 *           {mailto address="me@domain.com" cc="you@domain.com,they@domain.com"}
 *           {mailto address="me@domain.com" extra='class="mailto"'}
 * Taken from the original Smarty
 * http://smarty.php.net
 * -------------------------------------------------------------
 */
function tpl_function_mailto($params, &$template_object)
{
    extract($params);

    if (empty($address))
	{
        $template_object->trigger_error("mailto: missing 'address' parameter");
        return;
    }

    if (empty($text))
	{
		$text = $address;
    }

    if (empty($extra))
	{
		$extra = "";
    }

	// netscape and mozilla do not decode %40 (@) in BCC field (bug?)
	// so, don't encode it.

	$mail_parms = array();
	if (!empty($cc))
	{
		$mail_parms[] = 'cc='.str_replace('%40','@',rawurlencode($cc));
	}

	if (!empty($bcc))
	{
		$mail_parms[] = 'bcc='.str_replace('%40','@',rawurlencode($bcc));
	}

	if (!empty($subject))
	{
		$mail_parms[] = 'subject='.rawurlencode($subject);
	}

	if (!empty($newsgroups))
	{
		$mail_parms[] = 'newsgroups='.rawurlencode($newsgroups);
	}

	if (!empty($followupto))
	{
		$mail_parms[] = 'followupto='.str_replace('%40','@',rawurlencode($followupto));
	}

	$mail_parm_vals = "";
	for ($i=0; $i<count($mail_parms); $i++)
	{
		$mail_parm_vals .= (0==$i) ? '?' : '&';
		$mail_parm_vals .= $mail_parms[$i];
	}
	$address .= $mail_parm_vals;

	if (empty($encode))
	{
		$encode = 'none';
    }
	elseif (!in_array($encode,array('javascript','hex','none')) )
	{
        $template_object->trigger_error("mailto: 'encode' parameter must be none, javascript or hex");
        return;	
	}

	if ($encode == 'javascript' )
	{
		$string = 'document.write(\'<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>\');';
		for ($x=0; $x < strlen($string); $x++)
		{
			$js_encode .= '%' . bin2hex($string[$x]);
		}
		return '<script type="text/javascript" language="javascript">eval(unescape(\''.$js_encode.'\'))</script>';
	}
	elseif ($encode == 'hex')
	{
		preg_match('!^(.*)(\?.*)$!',$address,$match);
		if(!empty($match[2]))
		{
        	$template_object->trigger_error("mailto: hex encoding does not work with extra attributes. Try javascript.");
        	return;						
		}
		$address_encode = "";
		for ($x=0; $x < strlen($address); $x++)
		{
			if(preg_match('!\w!',$address[$x]))
			{
				$address_encode .= '%' . bin2hex($address[$x]);
			}
			else
			{
				$address_encode .= $address[$x];				
			}
		}
		$text_encode = "";
		for ($x=0; $x < strlen($text); $x++)
		{
			$text_encode .= '&#x' . bin2hex($text[$x]).';';
		}
		return '<a href="mailto:'.$address_encode.'" '.$extra.'>'.$text_encode.'</a>';
	}
	else
	{
		// no encoding		
		return '<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>';
	}
}

?>
