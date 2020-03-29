<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class GarmentOperationMaster extends BaseValidator
{
 protected $table = 'ie_garment_operation_master';
    protected $primaryKey = 'garment_operation_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = [ 'garment_operation_name'];
    // protected $rules = array(
    //     'garment_operation_name' => 'required',

    // );


    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'garment_operation_name' => [
            'required',
            'unique:ie_garment_operation_master,garment_operation_name,'.$data['garment_operation_id'].',garment_operation_id',
          ],
      ];
    }

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
