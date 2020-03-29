<?php

namespace App\Models\Stores;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

use App\Libraries\UniqueIdGenerator;

class GeneralPR extends BaseValidator
{

		protected $table = 'stores_gen_pr_header';
		protected $primaryKey = 'id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['id','Item_wanted_date','department','location','status','user_id'];

    	 protected $rules = array(
        'department' => 'required',
        'location'  => 'required'
    	); 

    	public function __construct()
    	{
        parent::__construct();    
		}
		
		public static function boot()
    {
        static::creating(function ($model) {
          $user = auth()->user();
          $code = UniqueIdGenerator::generateUniqueId('GEN_PR' , $user->location);
          $model->request_no = $code;
          //$model->updated_by = $user->user_id;
        });

        /*static::updating(function ($model) {
            $user = auth()->user();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }


    public function setItemWantedDateAttribute($value){
        $this->attributes['Item_wanted_date'] = date('Y-m-d H:i:s', strtotime($value));
    }
}
