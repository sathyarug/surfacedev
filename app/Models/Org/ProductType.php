<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProductType extends BaseValidator
{
  protected $table = 'org_product_type';
    protected $primaryKey = 'product_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['product_code', 'product_description', 'product_id'];
    protected $rules = array(
        'product_code' => 'required',
        'product_description' => 'required'
    );
    
    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
