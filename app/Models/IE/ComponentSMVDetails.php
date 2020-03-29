<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ComponentSMVDetails extends BaseValidator
{
 protected $table = 'ie_component_smv_details';
    protected $primaryKey = 'details_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    // protected $fillable = [ 'garment_operation_name'];
    // protected $rules = array(
    //     'garment_operation_name' => 'required',
    //
    // );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
