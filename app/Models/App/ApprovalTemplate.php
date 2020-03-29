<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ApprovalTemplate extends BaseValidator
{
    protected $table='app_approval_template';
    protected $primaryKey='template_id';
    public $timestamps = false;

    protected $rules = array(
        'name' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

}
