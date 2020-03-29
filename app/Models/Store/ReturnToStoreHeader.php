<?php

namespace App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ReturnToStoreHeader extends BaseValidator
{
    protected $table='store_return_to_store_header';
    protected $primaryKey='return_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';
    
    protected $fillable=['return_no','issue_id','status'];

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data) {
      return [
          'return_no' => 'required',
          'issue_id' => 'required',
          'status' => 'required'
      ];
    }
    
    public function __construct() {
        parent::__construct();
    }

}
