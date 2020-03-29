<?php

namespace App\Models\Finance\General_item;

use Illuminate\Database\Eloquent\Model;

class gen_mat_main_category extends Model
{
    protected $table = 'gen_mat_main_category';
	protected $primaryKey = 'category_id';
		
    	public function __construct()
    	{
        parent::__construct();    
    	}
}
