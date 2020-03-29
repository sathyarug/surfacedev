<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;


class ProcessApproval extends Model
{
    protected $table = 'app_process_approval';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $rules = array(
        'process' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
