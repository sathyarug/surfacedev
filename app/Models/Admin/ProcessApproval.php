<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProcessApproval extends BaseValidator
{
    protected $table='app_process_approval';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['process','level','approval_stage'];

    protected $rules = array(
        'process' => 'required',
        'level' => 'required',
        'approval_stage' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
    }

}
