<?php

namespace App\Models\Admin;

use App\BaseValidator;
use Illuminate\Database\Eloquent\Model;

class UsrProfile extends BaseValidator
{
    protected $table = 'usr_profile';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'loc_id', 'dept_id', 'cost_center_id', 'desig_id', 'nic_no', 'first_name', 'last_name', 'date_of_birth', 'gender', 'civil_status',
        'joined_date', 'mobile_no', 'email', 'emp_number', 'loc_id', 'dept_id', 'desig_id', 'cost_center_id', 'resign_date', 'reporting_level_1',
        'reporting_level_2'
    ];
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $dates = [
        'created_at',
        'updated_at',
        'joined_date',
        'resign_date',
        'date_of_birth'
    ];

  /*  protected $rules = array(
        'dept_id' => 'required',
        'cost_center_id' => 'required',
        'desig_id' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'emp_number' => 'required',
        'gender' => 'required'
    );*/

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'emp_number' => [
            'required',
            'unique:emp_number,emp_number,'.$data['user_id'].',user_id',
          ],
          'dept_id' => 'required',
          'cost_center_id' => 'required',
          'desig_id' => 'required',
          'first_name' => 'required',
          'last_name' => 'required',
          'emp_number' => 'required',
      ];
    }

    //Accesrs and mutators .....................................................

    public function setDateOfBirthAttribute($value){
        $this->attributes['date_of_birth'] = date('Y-m-d', strtotime($value));
    }

    public function setJoinedDateAttribute($value){
        $this->attributes['joined_date'] = date('Y-m-d', strtotime($value));
    }

    public function setResignDateAttribute($value){
        $this->attributes['resign_date'] = date('Y-m-d', strtotime($value));
    }

    //Relationships ............................................................

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\Role','user_roles','user_id','role_id')
        ->withPivot('loc_id');
    }

    public function locations()
    {
        return $this->belongsToMany('App\Models\Org\Location\Location','user_locations','user_id','loc_id');
        //->withPivot('id');
    }


}
