<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ServiceType extends BaseValidator
{
    protected $table = 'ie_service_type';
    protected $primaryKey = 'service_type_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['service_type_code', 'service_type_description', 'service_type_id'];
    /*protected $rules = array(
        'service_type_code' => 'required',
        'service_type_description' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'service_type_code' => [
            'required',
            'unique:ie_service_type,service_type_code,'.$data['service_type_id'].',service_type_id',
          ],
          'service_type_description' => 'required'
      ];
    }
}
