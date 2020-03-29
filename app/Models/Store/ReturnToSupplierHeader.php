<?php

namespace App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ReturnToSupplierHeader extends BaseValidator
{
    protected $table='store_return_to_supplier_header';
    protected $primaryKey='return_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';
    
    protected $fillable=['grn_id','return_no','status'];

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data) {
      return [
          'grn_id' => 'required',
          'return_no' => 'required',
          'status' => 'required'
      ];
    }
    
    public function __construct() {
        parent::__construct();
    }

}
