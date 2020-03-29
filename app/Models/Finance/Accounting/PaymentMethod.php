<?php

namespace App\Models\Finance\Accounting;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;

class PaymentMethod extends BaseValidator
{
    protected $table = 'fin_payment_method';
    protected $primaryKey = 'payment_method_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['payment_method_code','payment_method_description','payment_method_id'];

    /*protected $rules = array(
        'payment_method_code' => 'required',
        'payment_method_description'  => 'required'
    );*/

    public function __construct()
    {
        parent::__construct();
    }

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'payment_method_code' => [
            'required',
            'unique:fin_payment_method,payment_method_code,'.$data['payment_method_id'].',payment_method_id',
          ],
          'payment_method_description' => 'required'
      ];
    }

}
