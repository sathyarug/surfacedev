<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Role extends BaseValidator
{
    protected $table='permission_role';
    protected $primaryKey='role_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['role_name','role_id'];

    // protected $rules = array(
    //     'role_name' => 'required'
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'role_name' => [
            'required',
            'unique:permission_role,role_name,'.$data['role_id'].',role_id',
          ]
      ];
    }

    public function __construct()
    {
        parent::__construct();
    }
}
