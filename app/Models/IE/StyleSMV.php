<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StyleSMV extends BaseValidator
{
 protected $table = 'ie_smv_update_component';
    protected $primaryKey = 'smv_comp_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['smv_comp_id', 'style_id', 'feature_id','service_type_id','smv_value'];
    protected $rules = array(
        'style_id' => 'required',
        'feature_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
