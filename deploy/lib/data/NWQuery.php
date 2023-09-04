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
class NWQuery
{
    protected $date;

    public function __construct()
    {
        $this->date = static::freshTimestamp();
    }

    public static function freshTimestamp()
    {
        // use Carbon::now() to get a current timestamp
        return Carbon::now();
    }

    public static function create($model)
    {
        $model_f = new static();
        return $model_f;
    }

    public static function mergeData($model, $flat)
    {
        foreach ($flat as $key => $value) {
            $model->$key = $value;
        }
        return $model;
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
            $mod_t = new static();
            // 
            return static::mergeData($mod_t, $data);
        }, $datas);
        return $collected;
    }

    /**
     * @return object A single model object
     */
    public static function find($id)
    {

        $found_data = reset(self::query(['select * from messages where message_id = :id', [':id' => $id]]));
        $model = new static();
        foreach ($found_data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
}
