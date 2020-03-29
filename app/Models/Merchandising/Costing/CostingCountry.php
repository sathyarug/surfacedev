<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingCountry extends BaseValidator {

    protected $table = 'costing_country';
    protected $primaryKey = 'costing_country_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['costing_country_id', 'costing_id', 'country_id', 'fob'];


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'costing_id' => 'required',
        'country_id' => 'required',
        'fob' => 'required'
      ];
    }


}
