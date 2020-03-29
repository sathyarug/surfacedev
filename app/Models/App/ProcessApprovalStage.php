<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;


class ProcessApprovalStage extends Model
{
    protected $table = 'app_process_approval_stages';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $rules = array(
        'approval_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
