<?php
// A cookie class derived from the SESSION class.
class C0OKIE
{
   public function __construct()
   {
		self :: commence();
   }

	// Starts the session whenever a method is called for.
   public function commence()
   {
   		// TODO: If this gets used, it should use the create_cookie function.
		if (!isset($_COOKIE['ready'])){
			$_COOKIE['ready'] = TRUE;
		}
   }

   public function set($field ,$val)
   {
		self :: commence();
		$_COOKIE[$field] = $val;
   }

   // Set a session id if it doesn't exist yet.
   public function set_if_not_set($field, $val)
   {
		if (!self :: is_set($field)){
			self::set($field, $val);
			return true;
		}
		return false;
   }

   public function un_set($field)
   {
		self :: commence ();
		unset($_COOKIE[$field]);
   }

   public function destroy()
   {
		self :: commence();
		unset ($_COOKIE);
		return true;
   }

   public function get($field)
   {
		self :: commence();
		return $_COOKIE[$field];
   }

   public function has_values(){
		self :: commence();
		return (count($_COOKIE)>0? true : false);
   }

   public function is_set($field)
   {
		self :: commence();
		return isset($_COOKIE[$field]);
   }
}
