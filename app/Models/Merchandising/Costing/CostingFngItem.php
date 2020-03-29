<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingFngItem extends BaseValidator {

    protected $table = 'costing_fng_item';
    protected $primaryKey = 'costing_fng_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['costing_fng_id', 'costing_id', 'fng_id', 'country_id', 'color_id'];


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'costing_id' => 'required',
        'fng_id' => 'required',
        'country_id' => 'required',
        'color_id' => 'required'
      ];
    }


}
