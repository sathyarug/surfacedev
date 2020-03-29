<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Process extends BaseValidator
{
    protected $table='app_process';
    protected $primaryKey='process_name';
    public $incrementing = false;
    public $timestamps = false;

    protected $rules = array(
        'process_name' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

}
