<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Department extends BaseValidator
{
    protected $table = 'org_departments';
    protected $primaryKey = 'dep_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['dep_id', 'dep_code','dep_name'];
    /*protected $rules = array(
        'dep_code' => 'required',
        'dep_name' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setDepCodeAttribute($value) {
        $this->attributes['dep_code'] = strtoupper($value);
    }

    public function setDepNameAttribute($value) {
        $this->attributes['dep_name'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'dep_code' => [
            'required',
            'unique:org_departments,dep_code,'.$data['dep_id'].',dep_id',
          ],
          'dep_name' => 'required',
          'unique:org_departments,dep_name,'.$data['dep_id'].',dep_id',
      ];
    }
}
