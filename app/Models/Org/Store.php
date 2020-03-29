<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Store extends BaseValidator
{

		protected $table = 'org_store';
		protected $primaryKey = 'store_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['store_id','loc_id','section','store_name','store_address','store_phone','store_fax','email','status'];

    	protected $rules = array(
        'store_id' => '',
        'loc_id' => 'required',
        'store_name' => 'required',
        'store_address'  => 'required',
        /*'store_phone' =>'required',
        'store_fax' =>'required',
        'store_email' =>'required',
        'status' =>'',*/
    	);

    	public function __construct()
    	{
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    	}
}
