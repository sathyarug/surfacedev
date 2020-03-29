<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends BaseValidator
{
 protected $table = 'org_uom';
    protected $primaryKey = 'uom_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['uom_code', 'uom_description','uom_factor','uom_base_unit','unit_type', 'uom_id'];
    protected $rules = array(
        'uom_code' => 'required',
        'uom_description' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
