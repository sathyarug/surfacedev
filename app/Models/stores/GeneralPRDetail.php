<?php

namespace App\Models\Stores;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class GeneralPRDetail extends BaseValidator
{

		protected $table = 'stores_gen_pr_detail';
		protected $primaryKey = 'id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['id','request_id','main_category','sub_category_code','req_qty','status','uom'];

    	 protected $rules = array(
        'main_category' => 'required',
        'sub_category_code'  => 'required'
    	); 

    	public function __construct()
    	{
        parent::__construct();    	
    	}
}
