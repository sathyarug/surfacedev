<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ApprovalTemplateStage extends BaseValidator
{
    protected $table='app_approval_template_stages';
    protected $primaryKey='template_stage_id';
    public $timestamps = false;

    protected $rules = array(
        'template_id' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

}
