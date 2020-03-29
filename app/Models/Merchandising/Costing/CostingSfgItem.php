<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingSfgItem extends BaseValidator {

    protected $table = 'costing_sfg_item';
    protected $primaryKey = 'costing_sfg_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['costing_sfg_id', 'costing_id', 'sfg_id', 'country_id', 'color_id'];


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'costing_id' => 'required',
        'sfg_id' => 'required',
        'country_id' => 'required',
        'color_id' => 'required'
      ];
    }


}
