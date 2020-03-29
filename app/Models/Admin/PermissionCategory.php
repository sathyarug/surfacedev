<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PermissionCategory extends BaseValidator
{
    protected $table='permission_category';
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

    public function permissions()
    {
        return $this->hasMany('App\Models\Admin\Permission','category');
    }

}
