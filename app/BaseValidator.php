<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseValidator extends Model
{
    protected $rules = array();
    protected $errors;
    protected $errors_str;


    public static function boot()
    {
        static::creating(function ($model) {
          //$user = auth()->user();
          try {
              $user = auth()->userOrFail();
              $payload = auth()->payload();

              $model->created_by = $user->user_id;
              $model->updated_by = $user->user_id;
              $model->user_loc_id = $payload['loc_id'];
          } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) { }
        });

        static::updating(function ($model) {
          try {
              $user = auth()->userOrFail();
              $model->updated_by = $user->user_id;
          } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) { }             
        });

        /*static::deleting(function ($model) {
            // bluh bluh
        });*/

        parent::boot();
    }



    public function validate($data)
    {
        // make a new validator object
        if($this->rules != null && $this->rules != false){
          $v = \Illuminate\Support\Facades\Validator::make($data, $this->rules);
        }
        else{
          $v = \Illuminate\Support\Facades\Validator::make($data, $this->getValidationRules($data));
        }

        // check for failure
        if ($v->fails()) {
            // set errors and return false
            $this->errors = $v->errors();
            $this->errors_str = implode(",",$v->messages()->all());
            return false;
        }
        // validation pass
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function errors_tostring(){
        return $this->errors_str;
    }
}
