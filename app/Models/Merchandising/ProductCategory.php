<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProductCategory extends BaseValidator {

    protected $table = 'prod_category';
    protected $primaryKey = 'prod_cat_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['prod_cat_description'];

    protected $rules = array(
        'prod_cat_description' => 'required'

    );
    
    

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

}
