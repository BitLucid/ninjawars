<?php
namespace model;



/**
 * Base Model class that other models should inherit from
 */
abstract class BaseModel {
	/**
	 * Load any additional data to a model class
	 */
	abstract public function load();

	/**
	 * Save the model data to the database
	 */
    abstract public function save();
    
    /**
     * Find an entity by id
     */
    abstract public static function find(int $id);
}