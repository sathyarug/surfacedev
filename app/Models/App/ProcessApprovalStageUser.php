<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;

class ProcessApprovalStageUser extends Model
{
    protected $table = 'app_process_approval_stage_users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $rules = array(
        'approval_stage_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
