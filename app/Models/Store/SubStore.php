<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SubStore extends BaseValidator
{
    protected $table='org_substore';
    protected $primaryKey='substore_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['substore_name','store_id'];

    /*protected $rules=array(
        'substore_name'=>'required'
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
          'substore_name' => [
            'required',
            'unique:org_substore,substore_name,'.$data['substore_id'].',substore_id',
          ]
      ];
    }
}
