<?php

namespace App\Models\Admin;

use App\BaseValidator;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class User extends BaseValidator
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'usr_login';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'user_name', 'email', 'password', 'reset_status',
    ];
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

  //  public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /*protected $rules = array(
        'user_name' => 'required',
        'password' => 'required',
        'email' => 'required'
    );*/

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'user_name' => [
            'required',
            'unique:user_name,user_name,'.$data['user_id'].',user_id',
          ],
          'password' => 'required',
          'email' => 'required'
      ];
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
