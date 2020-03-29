<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ResetPassword extends BaseValidator
{
    protected $table='usr_reset_pwd';
    protected $primaryKey='reset_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';
    
    protected $fillable=['user_id','token','status'];

    protected function getValidationRules($data) {
      return [
        'user_id' => 'required',
        'token' => 'required',
        'status' => 'required'
      ];
    }
    
    public function __construct() {
        parent::__construct();
    }

}
