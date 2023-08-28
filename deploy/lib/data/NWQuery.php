<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
// use Illuminate\Database\Eloquent\Model;
use NinjaWars\core\data\Player;
use Carbon\Carbon;

/**
 * Acts as a mini model and query builder with an ActiveRecord pattern 
 * (e.g. Message::find(id) $message->save(), whatever nw needs)
 */
abstract class NWQuery
{

    protected static $model;
    // Inheriting classes need to set primaryKey and table as:
    static protected $primaryKey;
    static protected $table;


    public static function freshTimestamp()
    {
        // use Carbon::now() to get a current timestamp
        return Carbon::now();
    }

    public static function getTable()
    {
        return static::$table;
    }

    public static function getPrimaryKey()
    {
        return static::$primaryKey;
    }

    public static function creating($model)
    {
        // initialize the model if it hasn't been already
        if (!self::$model) {
            // initialize as a stdClass object

            static::$model = new static();

            static::$model->table = static::getTable();
            static::$model->primaryKey = static::getPrimaryKey();
        }
        static::$model->date = self::freshTimestamp();
        return static::$model;
    }

    public static function create($model)
    {
        $model_f = self::creating($model);
        $model_f->date = self::freshTimestamp();
        if (!$model_f->table) {
            throw new \Exception('Error: Model created does not have a table set.');
        }
        return $model_f;
    }

    /**
     * @return array of items
     */
    public static function query($builder)
    {
        // Destructure the builder into query and parameters
        list($query, $params) = $builder;
        $datas = query_array($query, $params);
        // Meld the incoming data array of multiple entries with the current model
        $collected = array_map(function ($data) {
            return (object) array_merge((array) self::$model, $data);
        }, $datas);
        return $collected;
    }

    /**
     * @return object A single model object
     */
    public static function find($id)
    {

        $found_data = reset(self::query(['select * from ' . static::getTable() . ' where ' . static::getPrimaryKey() . ' = :id', [':id' => $id]]));
        $model = new static();
        foreach ($found_data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
}
