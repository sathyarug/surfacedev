<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Transaction extends BaseValidator
{
    protected $table = 'fin_transaction';
    protected $primaryKey = 'trans_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['trans_description','trans_code'];

    /*protected $rules = array(
        'trans_description' => 'required',
        'trans_code'=>'required'
    );*/

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
          );
    }

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'trans_code' => [
            'required',
            'unique:fin_transaction,trans_code,'.$data['trans_id'].',trans_id',
          ],
          'trans_description' => 'required'
      ];
    }

}
