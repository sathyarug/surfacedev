<?php

namespace App\Models\Org\Cancellation;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CancellationReason extends BaseValidator
{
    protected $table = 'org_cancellation_reason';
    protected $primaryKey = 'reason_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['reason_code', 'reason_description', 'reason_id' , 'reason_category'];
    /*protected $rules = array(
        'reason_code' => 'required',
        'reason_description' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
        /*$this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );*/
    }

    //Accessors & Mutators......................................................

    public function setReasonCodeAttribute($value) {
        $this->attributes['reason_code'] = strtoupper($value);
    }

    public function setReasonDescriptionAttribute($value) {
        $this->attributes['reason_description'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'reason_code' => [
            'required',
            'unique:org_cancellation_reason,reason_code,'.$data['reason_id'].',reason_id',
          ],
          'reason_description' => 'required'
      ];
    }
}
