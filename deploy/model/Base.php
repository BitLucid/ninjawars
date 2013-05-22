<?php namespace model;

/**
 * Base model class
 *
 * @package NinjaWars
 * @category Model
 * @author Taufan Aditya<toopay@taufanaditya.com>
 */

class Base {

	/**
	 * @var bool init state
	 */
	protected static $init = false;

	/**
	 * Check init flag
	 *
	 * @return bool
	 */
	public static function isInitialized()
	{
		return self::$init;
	}

	/**
	 * Constructor
	 *
	 * Initialize the propel connection 
	 */
	public function __construct()
	{
		if ( ! self::$init) {
			// Setup propel
			\Propel::init(CONF_ROOT . 'connection.php');

			// flag init state
			self::$init = true;
		}
	}
	
}