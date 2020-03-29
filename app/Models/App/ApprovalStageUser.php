<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ApprovalStageUser extends BaseValidator
{
    protected $table = 'app_approval_stage_users';
    protected $primaryKey='id';
    public $timestamps = false;

    protected $rules = array(
        'stage_id' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

}
