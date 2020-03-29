<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingFngColor extends BaseValidator {

    protected $table = 'costing_fng_color';
    protected $primaryKey = 'fng_color_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['fng_color_id', 'costing_id', 'color_id'];

    //Relationships.............................................................

    public function color()
    {
        return $this->belongsTo('App\Models\Org\Color', 'color_id')->select(['color_code', 'color_name']);
    }


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'costing_id' => 'required',
        'color_id' => 'required'
      ];
    }


}
