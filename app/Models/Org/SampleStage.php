<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
class SampleStage extends BaseValidator
{
   protected $table = 'org_sample_stage';
    protected $primaryKey = 'sample_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['sample_code', 'sample_description', 'sample_id'];
    protected $rules = array(
        'sample_code' => 'required',
        'sample_description' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
