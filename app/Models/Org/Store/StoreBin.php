<?php

namespace App\Models\Org\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StoreBin extends BaseValidator
{
    protected $table='org_store_bin';
    protected $primaryKey='store_bin_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['store_id','store_bin_name','store_bin_id'];

    protected $rules=array(
        'store_bin_name'=>'required',
        'store_id'=>'required'
    );

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
          'store_bin_name' => [
            'required',
            'unique:org_store_bin,store_bin_name,'.$data['store_bin_id'].',store_bin_id',
          ]
      ];
    }

}
