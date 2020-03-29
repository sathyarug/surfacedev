<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class GarmentOptions extends BaseValidator
{
    protected $table='org_garment_options';
    protected $primaryKey='garment_options_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['garment_options_description','garment_options_id'];

    // protected $rules=array(
    //     'garment_options_description'=>'required'
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'garment_options_description' => [
            'required',
            'unique:org_garment_options,garment_options_description,'.$data['garment_options_id'].',garment_options_id'],
      ];
    }

    public function __construct() {
        parent::__construct();
    }
}
