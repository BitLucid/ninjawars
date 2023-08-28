<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
// use Illuminate\Database\Eloquent\Model;
use NinjaWars\core\data\Player;
use Carbon\Carbon;
use PDOStatement;

/**
 * Acts as a mini model and query builder with an ActiveRecord pattern
 * (e.g. Message::find(id) $message->save(), whatever nw needs)
 */
abstract class NWQuery
{
    // Inheriting classes need to set primaryKey and table as:
    // protected $primaryKey;
    // protected $table = 'messages';


    public static function freshTimestamp()
    {
        // use Carbon::now() to get a current timestamp
        return Carbon::now();
    }

    public static function create($model)
    {
        $model_f = new static();
        foreach ($model as $key => $value) {
            $model_f->$key = $value;
        }
        return $model_f;
    }

    public static function mergeData($model, array $flat): array
    {
        foreach ($flat as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    /**
     * @return array of items
     */
    public static function query(array $builder): array
    {
        // Destructure the builder into query and parameters
        list($query, $params) = $builder;
        $datas = query_array($query, $params);
        // Meld the incoming data array of multiple entries with the current model
        $collected = array_map(function ($data) {
            $mod_t = new static();
            // 
            return static::mergeData($mod_t, $data);
        }, $datas);
        return $collected;
    }

    /**
     * @return \PDOStatement the raw statement for rowcount or whatever
     */
    public static function query_resultset(array $builder): array | \PDOStatement
    {
        // Destructure the builder into query and parameters
        list($query, $params) = $builder;
        $datas = query($query, $params);
        return $datas;
    }

    /**
     * @return object A single model object
     */
    public static function find(int|null|string $id)
    {
        $model = new static();
        $found_data = reset(self::query(['select * from messages where ' . $model->primaryKey . ' = :id', [':id' => $id]]));
        foreach ($found_data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    abstract protected function save();
    abstract protected function delete();
}
