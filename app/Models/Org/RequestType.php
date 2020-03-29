<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class RequestType extends BaseValidator
{
    protected $table = 'org_request_type';
    protected $primaryKey = 'request_type_id';
    //protected $keyType = 'varchar';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['request_type'];

    /*protected $rules = array(
        'request_type' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setRequestTypeAttribute($value) {
        $this->attributes['request_type'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'request_type' => [
            'required',
            'unique:org_request_type,request_type,'.$data['request_type_id'].',request_type_id',
          ]
      ];
    }

}
