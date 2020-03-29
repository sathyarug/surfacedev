<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Main_Cluster extends BaseValidator
{
	
		protected $table = 'org_group';
		protected $primaryKey = 'group_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';
   
		protected $fillable = ['group_name','source_id','group_code','group_id'];
    
    	protected $rules = array(
        'group_id' => 'required',
        'group_code' => 'required',
        'source_id' => 'required',
        'group_name'  => 'required'        
    	);
    
    	public function __construct()
    	{
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    	}
}
