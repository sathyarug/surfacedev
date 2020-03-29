<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Permission extends BaseValidator
{
    protected $table='permission';
    public $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey='code';
    public $timestamps = false;

    protected $fillable = ['code','description'];

    protected $rules = array(
        'code' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
    }
}
