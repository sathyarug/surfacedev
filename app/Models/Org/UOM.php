<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class UOM extends BaseValidator
{
    protected $table = 'org_uom';
    protected $primaryKey = 'uom_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['uom_code', 'uom_description','uom_factor','uom_base_unit','unit_type', 'uom_id'];
    /*protected $rules = array(
        'uom_code' => 'required',
        'uom_description' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    // public function setUomCodeAttribute($value) {
    //     $this->attributes['uom_code'] = strtoupper($value);
    // }

    // public function setUomDescriptionAttribute($value) {
    //     $this->attributes['uom_description'] = strtoupper($value);
    // }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'uom_code' => [
            'required',
            'unique:org_uom,uom_code,'.$data['uom_id'].',uom_id',
          ],
          'uom_description' => 'required'
      ];
    }
}
