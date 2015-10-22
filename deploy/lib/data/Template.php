<?php
namespace app\data;
require_once(CORE.'data/database.php');

use Illuminate\Database\Eloquent\Model;

class Template extends Model{
	protected $primaryKey = 'template_id'; // When anything other than id
	//protected $table = 'TemplateCustom'; // Standard is automatically mapped to plural of classname
    //public $timestamps = false;
    // The overridden database column updated_at/created_at settings.
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    // Set fields that can be mass filled upon ::create()
    protected $fillable = ['identity', 'uname', 'operational', 'level', 'health', 'description', 'whatever'];
    //protected $guarded = ['template_id', 'created_date', 'updated_date'];
    // Excludes the date and primary key fields above

    /**
     * Special case method to get the id regardless of what it's actually called in the database
    **/
    public function id(){
    	return $this->template_id;
    }

    /**
     * Compare whether the entity is current set to active.
    **/
    public function isActive(){
    	return $this->active !== '0' && $this->operational !== '0';
    }

}