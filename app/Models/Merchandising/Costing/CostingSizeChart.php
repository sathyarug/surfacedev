<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingSizeChart extends BaseValidator {

    protected $table = 'costing_size_chart';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'costing_id', 'size_chart_id', 'size_id', 'status'];


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'costing_id' => 'required',
        'size_chart_id' => 'required',
        'size_id' => 'required',
        'status' => 'required'
      ];
    }


}
