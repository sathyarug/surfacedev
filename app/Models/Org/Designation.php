<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Designation extends BaseValidator
{
    protected $table = 'org_designation';
    protected $primaryKey = 'des_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['des_id', 'des_code','des_name'];
    /*protected $rules = array(
        'des_code' => 'required',
        'des_name' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setDesCodeAttribute($value) {
        $this->attributes['des_code'] = strtoupper($value);
    }

    public function setDesNameAttribute($value) {
        $this->attributes['des_name'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'des_code' => [
            'required',
            'unique:org_designation,des_code,'.$data['des_id'].',des_id',
          ],
          'des_name' => 'required'
      ];
    }
}
