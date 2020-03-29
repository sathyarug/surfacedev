<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Division extends BaseValidator
{
    protected $table='cust_division';
    protected $primaryKey='division_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['division_code','division_description','division_id'];

    /*protected $rules=array(
        'division_code'=>'required',
        'division_description'=>'required'
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
          'division_code' => [
            'required',
            'unique:cust_division,division_code,'.$data['division_id'].',division_id',
          ],
          'division_description' => 'required'
      ];
    }
}
