<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Product_Category extends BaseValidator
{
    protected $table = 'prod_category';
    protected $primaryKey = 'prod_cat_id';
    //protected $keyType = 'varchar';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    
    protected $fillable = ['prod_cat_description'];
    
    protected $rules = array(
        'prod_cat_description' => 'required'
        
    );
    
    
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
    
    
}
