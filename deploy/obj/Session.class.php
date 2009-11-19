<?php
// The session class from http://us3.php.net/session_start, referenced via static method calls.
class SESSION
{
   public function __construct()
   {
		self :: commence();
   }

	// Starts the session whenever a method is called for.
   public static function commence()
   {
		if (!isset($_SESSION ['ready'])){
			session_start();
			$_SESSION['ready'] = TRUE;
		}
   }

   public static function set($field, $val)
   {
		self :: commence();
		$_SESSION[$field] = $val;
   }

   // Set a session id if it doesn't exist yet.
   public static function set_if_not_set($field, $val)
   {
		if (!self :: is_set($field)){
			self::set($field, $val);
			return true;
		}
		return false;
   }

   public static function un_set($field)
   {
		self :: commence ();
		unset($_SESSION[$field]);
   }

   public static function destroy()
   {
		self :: commence();
		unset ($_SESSION);
		session_destroy();
		return true;
   }

   public static function get($field)
   {
		self :: commence();
		return (isset($_SESSION[$field])?$_SESSION[$field] : null);
   }

   public static function has_values(){
		self :: commence();
		return (count($_SESSION)>0? true : false);
   }

   public static function is_set($field)
   {
		self :: commence();
		return isset($_SESSION[$field]);
   }
}
?>
