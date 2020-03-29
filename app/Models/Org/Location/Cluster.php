<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Cluster extends BaseValidator
{
		protected $table = 'org_group';
		protected $primaryKey = 'group_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['source_id','group_code','group_name'];

  	/*protected $rules = array(
      'source_id' => 'required',
      'group_code' => 'required',
      'group_name'  => 'required'
  	);*/

  	public function __construct() {
      parent::__construct();
  	}

		//Accessors & Mutators......................................................

    public function setGroupCodeAttribute($value) {
        $this->attributes['group_code'] = strtoupper($value);
    }

    public function setGroupNameAttribute($value) {
        $this->attributes['group_name'] = strtoupper($value);
    }

		//Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'group_code' => [
            'required',
            'unique:org_group,group_code,'.$data['group_id'].',group_id',
          ],
          'group_name' => 'required'
      ];
    }
}
