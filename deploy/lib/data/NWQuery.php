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

    private static $model;
    // Inheriting classes need to set primaryKey and table as:
    // protected $primaryKey;
    // protected $table = 'messages';


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

            self::$model = new static();
            self::$model->table = static::$table;
            self::$model->primaryKey = static::$primaryKey;
        }
        self::$model->date = self::freshTimestamp();
        return self::$model;
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

        $found_data = reset(self::query(['select * from ' . static::$table . ' where ' . static::$primaryKey . ' = :id', [':id' => $id]]));
        $model = new static();
        foreach ($found_data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
}
