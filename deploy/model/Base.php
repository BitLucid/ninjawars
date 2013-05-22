<?php 

/**
 * Base model class
 *
 * @package NinjaWars
 * @category Model
 * @author Taufan Aditya<toopay@taufanaditya.com>
 */

namespace model;

use \Propel;
use \BaseObject;
use \ModelCriteria;
use \PropelCollection as Collection;

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
	 * Factory method to manufacturing the ORM entities
	 *
	 * @param string entity name
	 * @return BaseObject 
	 */
	public static function create($entity = '')
	{
		$entityObject = '\\model\\orm\\'.$entity;

		return new $entityObject;
	}

	/**
	 * Factory method to manufacturing the entity model criteria
	 *
	 * @param string entity name
	 * @return ModelCriteria 
	 */
	public static function query($entity = '')
	{
		$modelCriteria = '\\model\\orm\\'.$entity.'Query';

		return new $modelCriteria;
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
			Propel::init(CONF_ROOT . 'connection.php');

			// flag init state
			self::$init = true;
		}
	}

	/**
	 * Validate base object
	 *
	 * @param mixed Something to assert
	 * @return bool true if instance of BaseObject
	 */
	public function isObject($object = null)
	{
		return $object instanceof BaseObject;
	}

	/**
	 * Validate collection
	 *
	 * @param mixed Something to assert
	 * @return bool true if instance of Collection
	 */
	public function isCollection($object = null)
	{
		return $object instanceof Collection;
	}

	/**
	 * Wrap object into PropelCollection
	 *
	 * @param mixed Something to add into collection
	 * @return PropelCollection
	 */
	public function collection($object = null)
	{
		if ($object instanceof Collection) return $object;

		$collectionData = is_array($object) ? $object : array($object);

		return new Collection($collectionData);
	}
}