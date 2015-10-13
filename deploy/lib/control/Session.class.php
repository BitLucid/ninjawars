<?php
// *** The session class from the comments in http://us3.php.net/session_start, providing static methods that abstract common session usage patterns. ***
class SESSION
{
	private static $session = null;
	public function __construct()
	{
		self::commence();
	}

	// *** Starts the session whenever a method is called for. ***
	public static function commence()
	{
		if(!isset(static::$session['ready']) || !static::$session['ready']){
			if (session_id() == ''){
				session_start();
				static::$session = $_SESSION;
				static::$session['ready'] = TRUE;
			}
		}
	}

	/**
	 * Inject a new session of arbitrary data, scary.
	**/
	public static function inject($session){
		static::$session = $session;
		static::$session['ready'] = TRUE;
	}

	public static function set($field, $val)
	{
		self::commence();
		static::$session[$field] = $val;
	}

	// *** Set a session id if it doesn't exist yet. ***
	public static function set_if_not_set($field, $val)
	{
		if (!self::is_set($field))
		{
			self::set($field, $val);
			return true;
		}

		return false;
	}

	public static function un_set($field)
	{
		self::commence();
		unset(static::$session[$field]);
	}

	public static function destroy()
	{
		self::commence();
		static::$session = null;
		if(session_id() === null){
			session_destroy();
		}
		return true;
	}

	public static function get($field)
	{
		self::commence();
		return (isset(static::$session[$field]) ? static::$session[$field] : null);
	}

	public static function has_values()
	{
		self::commence();
		return (count(static::$session) > 0);
	}

	public static function is_set($field)
	{
		self::commence();
		return isset(static::$session[$field]);
	}
}
