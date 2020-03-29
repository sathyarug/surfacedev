<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ShipmentTerm extends BaseValidator
{
    protected $table = 'fin_shipment_term';
    protected $primaryKey = 'ship_term_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['ship_term_code','ship_term_description'];

    /*protected $rules = array(
        'ship_term_code' => 'required',
        'ship_term_description' => 'required'
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
          'ship_term_code' => [
            'required',
            'unique:fin_shipment_term,ship_term_code,'.$data['ship_term_id'].',ship_term_id',
          ],
          'ship_term_description' => 'required'
      ];
    }
}
